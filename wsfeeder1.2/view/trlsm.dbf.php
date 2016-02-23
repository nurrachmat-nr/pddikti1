<?php
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/lsm");
mkdirs(FOLDER_DATA);
$tbl_dbf="TRLSM.DBF";
$folder=cek_dbf($tbl_dbf,$kode_pt);
$folder_dbf=$folder[0]; global $odbc; global $odbc2;
$folder_dbf2=str_replace("/","\\",SISTEM_TMP."/$kode_pt/");
$odbc=new DBFConnect();
$odbc->connect($folder_dbf);
$odbc2=new DBFConnect();
$odbc2->connect($folder_dbf2);

global $proxy;
$proxy=new PROXY();
$proxy->connect();
$prodi=unserialize(PRODI);
$id_sms=$prodi[$_GET['prodi']]['id_sms'];


if($_GET['load_lsm']){
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);
	$js='';
	$s='$';
	$insert=array(); $update=array();$L_update=array();$c_index_update=array(); $L_index_update=array();
	$c_index_insert=array();$idpdarray=array(); $cek_nim=array();
	$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$_GET[prodi].txt";
	if(file_exists($file_id_pd)){
		$idpdarray=json_decode(file_get_contents($file_id_pd),true);
	}
	foreach($dt as $k=>$v){
		foreach($v as $kk=>$vv){
			$nim=$vv['NIMHSTRLSM'];
			$id_reg_pd=$idpdarray[$nim];
			if($id_reg_pd==''){
				$cek_nim[$nim]="'$nim'";
			}
		}
	}
	if(count($cek_nim)>0){
		$x = $proxy->GetRecordsets('mahasiswa_pt.raw',"trim(nipd) in(".implode(",",$cek_nim).") and id_sms='$id_sms' and id_sp='$id_sp'");
		$res=$x['result'];
		if(count($res) >0){
			foreach($res as $k=>$r){
				$idpdarray[trim($r['nipd'])]=$r['id_reg_pd'];
			}
		}
	}
	tulis_data($file_id_pd,json_encode($idpdarray));
	foreach($dt as $status=>$v){
		foreach($v as $kk=>$vv){
			$c=array(); $data=array(); $lulus=array(); $data_lulus=array();
			$nim=$vv['NIMHSTRLSM'];
			$id_reg_pd=$idpdarray[$nim];
			
			if($id_reg_pd!=''){
				$x = $proxy->GetRecords('kuliah_mahasiswa',"id_smt='$_GET[tahun]' and id_reg_pd='$id_reg_pd'");
				$sts=trim($x['result']['id_stat_mhs']);
				$id_up=$x['result']['id_updater'];
				$c['id_stat_mhs']=$status;
				$c['id_reg_pd']=$id_reg_pd;
				$c['id_smt']=$_GET['tahun'];
				if($status=='L'){
					$c['ipk']=$vv['NLIPKTRLSM'];
					$c['sks_total']=(int)$vv['SKSTTTRLSM'];
					$lulus['id_jns_keluar']=1;
					$lulus['tgl_keluar']=$vv['TGLLSTRLSM'];
					$lulus['sk_yudisium']=$vv['NOSKRTRLSM'];
					$lulus['tgl_sk_yudisium']=$vv['TGLRETRLSM'];
					$lulus['ipk']=$vv['NLIPKTRLSM'];
					$lulus['no_seri_ijazah']=$vv['NOIJATRLSM'];
					$data_lulus['key']=array('id_reg_pd'=>$id_reg_pd);
					$data_lulus['data']=$lulus;
					$L_update[]=$data_lulus;
					$L_index_update[]=$nim;
				}
				$data['key']=array('id_smt'=>$_GET['tahun'],'id_reg_pd'=>$id_reg_pd);
				$data['data']=$c;
				if($sts!=$status){
					if($id_up){
						$update[]=$data;
						$c_index_update[]=$nim;
						
					}else{
						$insert[]=$c;
						$c_index_insert[]=$nim;
					}
				}else{
					$js.="$s('#sp_$nim').html('<strong>TBL kuliah_mahasiswa : </strong>No Change');\r\n";
				}
			}else{
				$js.="$s('#sp_$nim').text('NIM Tidak terdaftar');\r\n";
			}
		}	
	}
	if(count($insert) > 0){
		$x = $proxy->InsertRecordsets('kuliah_mahasiswa',$insert);
		$res=$x['result'];
		foreach($res as $k=>$v){
			$nim=$c_index_insert[$k];
			$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
			if($err=='ERROR SQL'){$err='';}
			if(trim($err)==''){
				$js.="$s('#sp_$nim').html('<strong>TBL kuliah_mahasiswa : </strong>Data telah di-Insert');\r\n";
			}else{
				$js.="$s('#sp_$nim').html('<div class=\"merah\"><strong>TBL kuliah_mahasiswa : </strong>$err</div>');\r\n";
			}
		}
	}
	
	if(count($update) >0){
		$x = $proxy->UpdateRecordsets('kuliah_mahasiswa',$update);
		$res=$x['result'];
		foreach($res as $k=>$v){
			$nim=$c_index_update[$k];
			$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
			if($err=='ERROR SQL'){$err='';}
			if(trim($err)==''){
				$js.="$s('#sp_$nim').html('<strong>TBL kuliah_mahasiswa : </strong>Data telah di-Update');\r\n";
			}else{
				$js.="$s('#sp_$nim').html('<div class=\"merah\"><strong>TBL kuliah_mahasiswa : </strong>$err</div>');\r\n";
			}
		}
	}
	if(count($L_update) > 0){
		$x = $proxy->UpdateRecordsets('mahasiswa_pt',$L_update);
		$res=$x['result'];
		foreach($res as $k=>$v){
			$nim=$L_index_update[$k];
			$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
			if($err=='ERROR SQL'){$err='';}
			if(trim($err)==''){
				$js.="$s('#sp_$nim').html($s('#sp_$nim').text()+'<br><strong>TBL mahasiswa_pt : </strong> telah diUpdate');\r\n";
			}else{
				$js.="$s('#sp_$nim').html($s('#sp_$nim').text()+'<div class=\"merah\"><strong>TBL mahasiswa_pt : </strong>$err</div>');\r\n";
			}
		}
	}
	echo "\r\n<script>$s( \"#detail_mhs\" ).dialog( \"close\" );</script>\r\n";
	echo "\r\n<script>\r\n$js</script>\r\n";
	exit;
}
make_heading('Impor Mhs Lulus, DO, Cuti','Mengimpor status mahasiswa dari TRLSM.DBF');
echo "\r\n<style>\r\n.merah td,.merah span,.merah{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"\" id=\"form1\">
  Program Studi
  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
	list_prodi();
echo " </select>
 Semester
  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>"; list_smtr($_GET['tahun']);
echo " </select></form>\r\n<div id=\"balikan_mhs\"></div>\r\n";
if($_GET['prodi']){
	if($_GET['tahun']){
		$rs=$odbc->query("select NIMHSTRLSM,STMHSTRLSM,TGLLSTRLSM,SKSTTTRLSM,NLIPKTRLSM,NOSKRTRLSM,TGLRETRLSM,NOIJATRLSM from TRLSM.DBF where KDPTITRLSM='$kode_pt' and KDPSTTRLSM='$_GET[prodi]' and THSMSTRLSM='$_GET[tahun]'");
		$mhs=array(); $statusr=array();
		foreach($rs as $k=>$r){
			$rs=$r;
			$status=$r['STMHSTRLSM'];
			$nim=$r['NIMHSTRLSM'];
			$nm=$odbc->query("select NMMHSMSMHS from MSMHS.DBF where NIMHSMSMHS='$nim'",false);
			$rs['NAMA']=$nm['NMMHSMSMHS'];
			$mhs[$status][]=$rs;
			$statusr[$status]=$status;
		}
		tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($mhs));
		$x=$proxy->GetRecordsets('status_mahasiswa');
		$res=$x['result'];
		$status_array=array();
		foreach($res as $k=>$v){
			$status_array[trim($v['id_stat_mhs'])]=$v['nm_stat_mhs'];
		}
		foreach($statusr as $sts){
			$w=600;
			$st=$status_array[$sts];
			if($st==''){$st='Tidak Jelas';}
			if($sts=='L'){$w=1100;}
			$dt=$mhs[$sts]; $jml=count($dt);
echo "\r\n<div class=\"form-container alert alert-info\"><strong>Status $st : $jml</strong></div>\r\n";
echo "<div style=\"width:$w"."px\">\r\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"table table-striped table-condensed\">
  <thead>
    <tr>
      <th width=\"38\">No</th>
      <th width=\"113\">NIM</th>
      <th width=\"291\">NAMA</th>
      <th width=\"30\">STATUS</th>";
if($sts=='L'){
	echo "
	  <th width=\"25\">IPK</th>
	  <th width=\"25\">SKS</th>
      <th width=\"120\">TGL LULUS</th>
      <th width=\"300\">NO SK</th>
	  <th width=\"125\">NO IJASAH</th>";
}
echo "<th width=\"400\">KETERANGAN</th></tr>
	</thead>";
	$no=0;
		foreach($dt as $k=>$r){
			$no++;
			echo "\r\n<tr><td>$no</td><td>$r[NIMHSTRLSM]</td><td>$r[NAMA]</td><td style=\"text-align:center\">$r[STMHSTRLSM]</td>";
			if($sts=='L'){
				echo "<td>$r[NLIPKTRLSM]</td><td>$r[SKSTTTRLSM]</td><td>$r[TGLLSTRLSM]</td><td>$r[NOSKRTRLSM]</td><td>$r[NOIJATRLSM]</td>";
			}
			echo "<td><div id=\"sp_$r[NIMHSTRLSM]\">...proses...</div></td></tr>\r\n";
		}
		echo "</table></div><hr>";
		}
$s='$';
echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n";
echo "<script>\r\nvar itg=0;\r\n$(function() {
		$s( \"#detail_mhs\" ).dialog({
			height: 100,
			width:450,
			modal: true,
			title: 'Wait'
		});
	});\r\n
	function pbar(){
		itg=itg+1;
		var x=$s('#p_bar').text();
		$s('#p_bar').text(x+'.');
		if(itg==1){
			$s('#p_bar').text('.');
		}
		setTimeout(\"pbar()\",500);
		if(itg > 50){
			itg=0;
		}
	}
	\r\n/* setTimeout(\"pbar()\",500); */\r\n
	</script>";
	echo "\r\n<script>\r\n$s('#balikan_mhs').load('".PATH."/index.php?load_lsm=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');\r\n</script>";
	}	
}
?>