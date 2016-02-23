<?php
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/transfer");
mkdirs(FOLDER_DATA);
$tbl_dbf="MSMHS.DBF";
$folder=cek_dbf($tbl_dbf,$kode_pt);
$folder_dbf=$folder[0];
$folder_dbf2=str_replace("/","\\",SISTEM_TMP."/$kode_pt/");
$angkatan=array(); global $odbc; global $odbc2;
$odbc=new DBFConnect();
$odbc->connect($folder_dbf);
$odbc2=new DBFConnect();
$odbc2->connect($folder_dbf2);
$prodi=unserialize(PRODI);
$id_sms=$prodi[$_GET['prodi']]['id_sms'];
if(!$_SERVER['QUERY_STRING']){
	if($_POST['simpannilai']){
		$n=array();
		foreach($_POST as $k=>$v){
			if($k!='simpannilai'){
				$vv=trim(str_replace(",",".",$v));
				$n[$k]=$vv;
			}
		}
		tulis_data(FOLDER_DATA."/nilai_indeks.txt",json_encode($n));
	}
	$sqlx="select DISTINCT TAHUNMSMHS from $tbl_dbf where KDPTIMSMHS='$kode_pt' and STPIDMSMHS='P'";
	$rs=$odbc->query($sqlx);
	foreach($rs as $k=>$r){
		$angkatan[$r['TAHUNMSMHS']]=$r['TAHUNMSMHS'];
	}
	$rs=$odbc->query("select DISTINCT NLAKHTRNLP from TRNLP.DBF where KDPTITRNLP='$kode_pt'");
	$n=array(); $nl=array();
	if(file_exists(FOLDER_DATA."/nilai_indeks.txt")){
		$nl=json_decode(file_get_contents(FOLDER_DATA."/nilai_indeks.txt"),true);
	}
	foreach($rs as $k=>$r){
		$nilai=$r['NLAKHTRNLP'];
		$angka=$nl[$nilai];
		if($angka==''){$angka=0;}
		$n[$nilai]=$angka;
	}
	ksort($angkatan); ksort($n);
	tulis_data(FOLDER_DATA."/tahun.txt",json_encode($angkatan));
	tulis_data(FOLDER_DATA."/nilai_indeks.txt",json_encode($n));
}
if($_GET['load_transfer']){
	$js=''; $s='$'; $insert=array(); $update=array();
	$bobot_nilai=array();
	$bobot_nilai_file=FOLDER_DATA."/nilai_indeks.txt";
	if(file_exists($bobot_nilai_file)){
		$bobot_nilai=json_decode(file_get_contents($bobot_nilai_file),true);
	}
	$alamat_server=$_SESSION['server_url'];
	global $proxy; 
	$proxy=new PROXY();
	$proxy->connect();
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);
	$nilai=array(); $mk=array();
	
	
	$x = $proxy->GetRecordsets('mata_kuliah',"id_sms='$id_sms'");
	$res=$x['result'];
	foreach($res as $k=>$r){
		$mk[$r['kode_mk']]=array('id_mk'=>$r['id_mk'],'sks_mk'=>$r['sks_mk'],'nm_mk'=>$r['nm_mk']);
	}
	$nimarray=array(); $mhs=array();
	foreach($dt as $k=>$rx){
		$nimarray[$rx['nim']]="'$rx[nim]'";
	}
	if(count($nimarray)>0){
		$f="trim(nipd) in (".implode(",",$nimarray).")";
		$x = $proxy->GetRecordsets('mahasiswa_pt.raw',$f);
		$res=$x['result'];
		if(count($res)>0){
			foreach($res as $k=>$r){
				$mhs[trim($r['nipd'])]=$r['id_reg_pd'];
			}
		}
	}
	foreach($dt as $k=>$rx){
		$c=array();
		$nim=$rx['nim'];
		$c['nim']=$nim;
		$id_reg_pd=$mhs[$nim];
		
		if($id_reg_pd!=''){
			$rs=$odbc->query("select KDKMKTRNLP as kodemk,NLAKHTRNLP as nilai,BOBOTTRNLP as bobot from TRNLP.DBF where KDPSTTRNLP='$_GET[prodi]' and NIMHSTRNLP='$nim'");
			if(count($rs) >0){
				$c['id_reg_pd']=$id_reg_pd;
				$c['data']=$rs;
				$nilai[]=$c;
			}else{
				$js.="$s('#sp_$nim').html('Tidak ada Nilai di TRNLP.DBF');\r\ndocument.getElementById('tr_$nim').className='merah';\r\n";
			}
			$js.="$s('#td_nim_$nim').html('<a target=\"_blank\" href=\"".PATH."/index.php/nilai/$id_reg_pd\">$nim</a>');\r\n";
		}else{
			$js.="$s('#sp_$nim').html('NIM tidak terdaftar di Feeder');\r\n";
		}
		
	}
	$nilai2=array(); $nimwarning=array();
	foreach($nilai as $k=>$r){
		$rs=array(); $rx=array();
		$rx=$r;
		$nim=$r['nim'];
		$id_reg_pd=$r['id_reg_pd'];
		$rs=$r['data'];
		foreach($rs as $kk=>$v){
			$kodemk=$v['kodemk'];
			$nilai=$v['nilai'];
			$bobot=$v['bobot'];
			$id_mk=$mk[$kodemk]['id_mk'];
			$sks=$mk[$kodemk]['sks'];
			$nama_mk=$mk[$kodemk]['nama'];
			if($id_mk==''){
				$x = $proxy->GetRecords('mata_kuliah',"kode_mk='$kodemk' and id_sms='$id_sms'");
				$f=$x['result'];
				$idmk=$f['id_mk'];
				$sks=$f['sks_mk'];
				$nama_mk=$f['nm_mk'];
			}
			if($id_mk!=''){
				$rx['data'][$kk]['id_mk']=$id_mk;
				$rx['data'][$kk]['sks']=$sks;
				$rx['data'][$kk]['nm_mk']=$nama_mk;
			}else{
				$nimwarning[$nim][]="Kode MK $kodemk Tidak terdaftar di forlap";
			}
		}
		$nilai2[]=$rx;
	}
	$insert=array(); $update=array(); $index=array();
	foreach($nilai2 as $k=>$r){
		$nim=$r['nim'];
		$id_reg_pd=$r['id_reg_pd'];
		$rs=$r['data'];
		$skstt=0; $skscek=0; $rc=array();
		foreach($rs as $kk=>$v){
			$c=array(); $cc=array(); $data=array();
			$kodemk=$v['kodemk'];
			$nilai=$v['nilai'];
			$bobot=$v['bobot'];
			$id_mk=$v['id_mk'];
			$sks=(int)$v['sks'];
			$nm_mk=$v['nm_mk'];
			$n_angka=$bobot_nilai[$nilai];
			if($id_mk!=''){
				$skscek=$skscek + $sks;
				if($skscek < 100){
					$skstt=$skstt+$sks;
					$c['id_reg_pd']=$id_reg_pd;
					$c['id_mk']=$id_mk;
					$c['kode_mk_asal']=$kodemk; $cc['kode_mk_asal']=$kodemk;
					$c['nm_mk_asal']=$nm_mk; $cc['nm_mk_asal']=$nm_mk;
					$c['sks_asal']=$sks; $cc['sks_asal']=$sks;
					$c['sks_diakui']=$sks; $cc['sks_diakui']=$sks;
					$c['nilai_huruf_asal']=$nilai; $cc['nilai_huruf_asal']=$nilai;
					$c['nilai_huruf_diakui']=$nilai; $cc['nilai_huruf_diakui']=$nilai;
					$c['nilai_angka_diakui']=$n_angka; $cc['nilai_angka_diakui']=$n_angka;
					$data['key']=array('id_reg_pd'=>$id_reg_pd,'id_mk'=>$id_mk);
					$data['data']=$cc;
					$update[]=$data; $insert[]=$c;
				}
			}
		}
		$js.="$s('#sp_$nim').html('Sukses mengupdate $skstt SKS');\r\n";
		$js.="$s('#sks_$nim').text('$skstt');\r\n";
	}
	if(count($insert) >0){
		$x = $proxy->InsertRecordsets('nilai_transfer',$insert);
	}
	if(count($update) >0){
		$x = $proxy->UpdateRecordsets('nilai_transfer',$update);
	}
	if(count($nimwarning) >0){
		foreach($nimwarning as $k=>$v){
			$nim=$k;
			$war=implode("<br>",$v);
			$js.="$s('#sp_$nim').html('<div class=\"abang\">$war</div>');\r\n";
		}
	}
	echo "\r\n<script>\r\n$s( \"#detail_mhs\" ).dialog( \"close\" ); \r\n</script>\r\n";
	echo "\r\n<script>\r\n$js</script>\r\n";
	exit;
}
$angkatan=json_decode(file_get_contents(FOLDER_DATA."/tahun.txt"),true);
make_heading('Nilai Transfer','Mengimpor data dari TRNLP.DBF');
echo "\r\n<style>\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"\" id=\"form1\">
  Program Studi
  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
	list_prodi();
echo " </select>
  Angkatan
  <select name=\"tahun\" id=\"tahun\" style=\"width:70px\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
foreach($angkatan as $tahun){
  	$sl=''; if($_GET['tahun']==$tahun){$sl='selected';}
  	echo "<option value=\"$tahun\" $sl>$tahun</option>\r\n";
}
echo " </select></form>";
if(!$_SERVER['QUERY_STRING']){
	echo "Konversi Nilai Huruf ke Nilai<div style=\"width:100px\" align=\"center\"><form name=\"form1\" method=\"post\" action=\"\"><input name=\"simpannilai\" type=\"hidden\" value=\"1\"><table><tr>";
	$n=array(); $n=json_decode(file_get_contents(FOLDER_DATA."/nilai_indeks.txt"),true); $no=0; $tt=1; $xx=0;
	foreach($n as $k=>$v){
		$xx++;
		$no++;
		if($no==1){
			$tt=$tt+1;
			echo "<td style=\"background-color:#1B58B4\"></td><td style=\"vertical-align:top\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"table table-striped table-condensed\"><thead><tr><th width=\"188\">Nilai Huruf </th><th width=\"212\">Nilai Angka </th></tr></thead>";
		}
		if($no > 0){echo "<tr><td>$k</td><td><input name=\"$k\" type=\"text\" value=\"$v\"></td></tr>";}
		if($no==5){
			echo "</table></td>";
			$no=0;
		}
	}
	$tt=$tt+2;
	if($xx >0){
		if($no==0){
			echo "<td style=\"background-color:#1B58B4\"></td>";
		}else{
			echo "<td style=\"background-color:#1B58B4\"></td></table></td>";
		}
	}
	echo "</tr><tr><td colspan=\"$tt\" style=\"background-color:#1B58B4; text-align:center\"><button type=\"submit\" class=\"btn_start btn btn-primary\">Simpan</button></td></tr></table></form></div>";

}
if($_GET['prodi']){
	if($_GET['tahun']){
		
echo "<div id=\"balikan_mhs\"></div><div style=\"width:1200px\">
  <table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"table table-striped table-condensed\">
  <thead>
    <tr>
      <th width=\"38\">No</th>
      <th width=\"113\">NIM</th>
      <th width=\"291\">NAMA</th>
      <th width=\"30\">SEX</th>
      <th width=\"120\">TP LHR </th>
      <th width=\"125\">TGL LHR </th>
	  <th width=\"30\">P/B</th>
	  <th width=\"475\">ASAL PINDAHAN</th>
      <th width=\"475\">KETERANGAN</th>
    </tr>
	</thead>";
$dtmhs=$odbc->query("select NIMHSMSMHS as nim,NMMHSMSMHS as nama,TPLHRMSMHS as tplhr,TGLHRMSMHS as tglhr,KDJEKMSMHS as sex,TGMSKMSMHS as tglmsk,STPIDMSMHS as stpid,SKSDIMSMHS as sks,ASNIMMSMHS as asnim,ASPTIMSMHS as aspt,ASPSTMSMHS as aspst,SMAWLMSMHS as smawl from $tbl_dbf where KDPTIMSMHS='$kode_pt' and KDPSTMSMHS='$_GET[prodi]' and TAHUNMSMHS='$_GET[tahun]' and STPIDMSMHS='P'");	
$no=0; $new=array(); $up=0; $update=array(); $server_url=$_SESSION['server_url'];
$rx=array();
foreach($dtmhs as $k=>$rs){
  	$no++; $r=$rs;
	$r['sks']=(int)$rs['sks'];
	$nim=$r['nim'];
	$asal='';
	if($r[stpid]!='B'){
		$aspt=cek_pt_asal($nim,$r['aspt']);
		$aspst=cek_pst_asal($nim,$r['aspst']);
		$asal="$r[asnim] ; $aspst ; $aspt ;  <span id=\"sks_$nim\">$r[sks]</span> SKS";
		$r['aspt']=$aspt;
		$r['aspst']=$aspst;
	}
	$rx[]=$r;
	echo "<tr id=\"tr_$nim\"><td><a name=\"a_$nim\"></a>$no</td><td><span id=\"td_nim_$nim\">$nim</span></td><td><span id=\"td_nama_$nim\">$r[nama]</span></td><td>$r[sex]</td><td>$r[tplhr]</td><td>$r[tglhr]</td>";
	echo "<td>$r[stpid]</td><td>$asal</td>";
	echo "<td><div id=\"sp_$nim\"></div></td></tr>";
}
echo "</table></div>\r\n";
$s='$';
tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($rx));
	if($no >0){
		echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n";
		echo "\r\n\r\n<script>\r\n$s('#balikan_mhs').load('".PATH."/index.php?load_transfer=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');\r\n</script>\r\n<script>\r\nvar itg=0;\r\n$(function() {
			$s( \"#detail_mhs\" ).dialog({
				height: 100,
				width:450,
				modal: true,
				title: 'Wait'
			});
		});\r\n
		function pbar(){
			itg=itg+1;
			var x=$s('#p_bar').html();
			$s('#p_bar').html(x+'.');
			if(itg==1){
				$s('#p_bar').html('.');
			}
			setTimeout(\"pbar()\",500);
			if(itg > 50){
				itg=0;
			}
		}
		\r\n/* setTimeout(\"pbar()\",500); */\r\n
		</script>\r\n";
	}
}}
?>
