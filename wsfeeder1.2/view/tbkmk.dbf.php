<?php
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/mk");
mkdirs(FOLDER_DATA);
$tbl_dbf="TBKMK.DBF";
$folder=cek_dbf($tbl_dbf,$kode_pt);
$folder_dbf=$folder[0];
$odbc=new DBFConnect();
$odbc->connect($folder_dbf);
$tahun=array();
if(!$_SERVER['QUERY_STRING']){
	$list_thn=unserialize(LIST_THN);
	$sqlx="select DISTINCT THSMSTBKMK from $tbl_dbf where KDPTITBKMK='$kode_pt'";
	$rs=$odbc->query($sqlx);
	foreach($rs as $k=>$r){
		$th=substr($r['THSMSTBKMK'],0,4);
		if(in_array($th,$list_thn)){$tahun[$th]=$th;}
	}
	ksort($tahun);
	tulis_data(FOLDER_DATA."/tahun.txt",json_encode($tahun));
}
function bandingkan_data($d1,$d2){
	$ret=1;
	if(trim(strtoupper($d1))== trim(strtoupper($d2))){
		$ret=0;
	}
	return $ret;
}
function bandingkan($r,$rs){
	$ret=0;
	if(bandingkan_data($r['NAKMKTBKMK'],$rs['nm_mk'])==1){$ret=1;}
	if(bandingkan_data((int)$r['SKSMKTBKMK'],(int)$rs['sks_mk'])==1){$ret=1;}
	if(bandingkan_data((int)$r['SKSTMTBKMK'],(int)$rs['sks_tm'])==1){$ret=1;}
	if(bandingkan_data((int)$r['SKSPRTBKMK'],(int)$rs['sks_prak'])==1){$ret=1;}
	if(bandingkan_data((int)$r['SKSLPTBKMK'],(int)$rs['sks_prak_lap'])==1){$ret=1;}
	if(bandingkan_data((int)mapping_mk($r['SAPPPTBKMK']),(int)$rs['a_sap'])==1){$ret=1;}
	if(bandingkan_data((int)mapping_mk($r['SLBUSTBKMK']),(int)$rs['a_silabus'])==1){$ret=1;}
	if(bandingkan_data((int)mapping_mk($r['BHNAJTBKMK']),(int)$rs['a_bahan_ajar'])==1){$ret=1;}
	if(bandingkan_data((int)mapping_mk($r['DIKTTTBKMK']),(int)$rs['a_diktat'])==1){$ret=1;}
	return $ret;
}
$tahun=json_decode(file_get_contents(FOLDER_DATA."/tahun.txt"),true);
$prodi=unserialize(PRODI);
$id_sms=$prodi[$_GET['prodi']]['id_sms'];
$nama_prodi=trim(str_replace("'","",$prodi[$_GET['prodi']]['nm_lemb']));
$id_jenj=$prodi[$_GET['prodi']]['id_jenj_didik'];
$token=$_SESSION['token_ws'];
if($_GET['prodi']){
	if($_GET['tahun']){
		global $proxy;
		$proxy=new PROXY();
		$proxy->connect();
	}
}
function mapping_mk($s){
	$ret=0;
	if(trim(strtoupper($s))=='Y'){
		$ret=1;
	}
	return $ret;
}
function update_mk($rs){
	global $proxy;
	$d=array();
	foreach($rs as $k=>$r){
		$c=array(); $data=array();
		$c['nm_mk']=$r['NAKMKTBKMK'];
		$c['sks_mk']=(int)$r['SKSMKTBKMK'];
		$c['sks_mk']=(int)$r['SKSMKTBKMK'];
		$c['sks_tm']=(int)$r['SKSTMTBKMK'];
		$c['sks_prak']=(int)$r['SKSPRTBKMK'];
		$c['sks_prak_lap']=(int)$r['SKSLPTBKMK'];
		$c['a_sap']=mapping_mk($r['SAPPPTBKMK']);
		$c['a_silabus']=mapping_mk($r['SLBUSTBKMK']);
		$c['a_bahan_ajar']=mapping_mk($r['BHNAJTBKMK']);
		$c['a_diktat']=mapping_mk($r['DIKTTTBKMK']);
		$data['key']=array('id_mk'=>$r['id_mk']);
		$data['data']=$c;
		$d[]=$data;
	}
	$result = $proxy->UpdateRecordsets('mata_kuliah', $d);
	
}
function insert_mk($r,$id_sms,$id_jenj,$id_sp){
	global $proxy;
	$c=array(); $data=array();
	$c['id_sms']=$id_sms;
	$c['id_jenj_didik']=$id_jenj;
	$c['kode_mk']=$r['KDKMKTBKMK'];
	$c['nm_mk']=$r['NAKMKTBKMK'];
	$c['sks_mk']=(int)$r['SKSMKTBKMK'];
	$c['sks_tm']=(int)$r['SKSTMTBKMK'];
	$c['sks_prak']=(int)$r['SKSPRTBKMK'];
	$c['sks_prak_lap']=(int)$r['SKSLPTBKMK'];
	$c['a_sap']=mapping_mk($r['SAPPPTBKMK']);
	$c['a_silabus']=mapping_mk($r['SLBUSTBKMK']);
	$c['a_bahan_ajar']=mapping_mk($r['BHNAJTBKMK']);
	$c['a_diktat']=mapping_mk($r['DIKTTTBKMK']);
	$result = $proxy->InsertRecords('mata_kuliah', $c);
	$id_mk=$result['result']['id_mk'];
	return $id_mk;
}
$server_url=$_SESSION['server_url'];
if($_GET['load_mk']){
	global $proxy;
	$js=''; $s='$'; 
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);
	$id_kurikulum_sp=$dt['id_kurikulum_sp'];
	$update=$dt['update'];
	$insert=$dt['insert'];
	$normal=$dt['normal'];
	update_mk($update);
	$data=array();
	foreach($update as $k=>$r){
		$data[]=$r;
	}
	foreach($normal as $k=>$r){
		$data[]=$r;
	}
	foreach($insert as $k=>$r){
		$id_mk=insert_mk($r,$id_sms,$id_jenj,$id_sp);
		$r['id_mk']=$id_mk;
		$data[]=$r;
	}
	$mkarray=array(); $datax=array();
	foreach($data as $k=>$r){
		$idmk=$r['id_mk'];
		$mkarray[]="'$idmk'";
		$datax[$idmk]=$r;
	}
	$filter="id_mk in(".implode(",",$mkarray).")";
	$filter="id_kurikulum_sp='$id_kurikulum_sp'";
	$x=$proxy->GetRecordsets('mata_kuliah_kurikulum.raw',$filter,"id_mk");
	$res=$x['result']; $datakur=array();
	foreach($res as $k=>$r){
		$idmk=$r['id_mk'];
		$datakur[$idmk]=$r;
	}
	$datas=array();
	foreach($datax as $k=>$r){
		$kodemk=$r['KDKMKTBKMK'];
		$namamk=str_replace("'","\\'",$r['NAKMKTBKMK']);
		$idmk=$r['id_mk'];
		$jml=(int)array_key_exists($idmk,$datakur);
		if($jml==0){
			$x=(int)$r['SEMESTBKMK'];
			$y=$x/2;
			$smt=2;
			if(strlen($y) > 1){
				$smt=1;
			}
			$c=array();
			$c['id_kurikulum_sp']=$id_kurikulum_sp;
			$c['id_mk']=$idmk;
			$c['smt']=$smt;
			$c['sks_mk']=(int)$r['SKSMKTBKMK'];
			$c['sks_tm']=(int)$r['SKSTMTBKMK'];
			$c['sks_prak']=(int)$r['SKSPRTBKMK'];
			$c['sks_prak_lap']=(int)$r['SKSLPTBKMK'];
			$c['a_wajib']=1;
			$datas[]=$c;
		}else{
			$js.="$s('#sp_$kodemk').html('Tidak ada update data');\r\n";
			$js.="$s('#mk_$kodemk').html('<a target=\"_blank\" href=\"$server_url/matakuliah/detail/$idmk\">$kodemk</a>');\r\n";
			$js.="$s('#nk_$kodemk').html('<a target=\"_blank\" href=\"$server_url/matakuliah/detail/$idmk\">$namamk</a>');\r\n";
		}
	}
	if(count($datas) > 0){
		$result = $proxy->InsertRecordsets('mata_kuliah_kurikulum',$datas);
		$res=$result['result'];
		foreach($res as $k=>$v){
			$error=trim(str_replace("'","\\'",kompres($v['error_desc'])));
			$id_mk=$v['id_mk'];
			$kodemk=$datax[$id_mk]['KDKMKTBKMK'];
			$namamk=str_replace("'","\\'",$datax[$id_mk]['NAKMKTBKMK']);
			if(trim($id_mk)!=''){
				$js.="$s('#mk_$kodemk').html('<a target=\"_blank\" href=\"$server_url/matakuliah/detail/$id_mk\">$kodemk</a>');\r\n";
				$js.="$s('#nk_$kodemk').html('<a target=\"_blank\" href=\"$server_url/matakuliah/detail/$id_mk\">$namamk</a>');\r\n";
			}
			if($error==''){
				$js.="$s('#sp_$kodemk').text('Sukses diupdate');\r\n";
			}else{
				$js.="$s('#sp_$kodemk').html('<div class=\"abang\">$error</div>');\r\n";
			}
		}
	}
	echo "<script>\r\n$js\r\n</script>\r\n";
	echo "\r\n<script>$s( \"#detail_mk\" ).dialog( \"close\" );</script>\r\n";
	exit;
}
make_heading('Impor Kurikulum','Mengimpor kurikulum &amp; matakuliah dari TBKMK.DBF');
echo "\r\n<style>\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n</style>\r\n<form name=\"form1\" method=\"get\" action=\"\" id=\"form1\">
  Program Studi
  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
	list_prodi();
echo " </select>
  Kurikulum Tahun
  <select name=\"tahun\" id=\"tahun\" style=\"width:70px\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
foreach($tahun as $th){
  	$sl=''; if($_GET['tahun']==$th){$sl='selected';}
  	echo "<option value=\"$th\" $sl>$th</option>\r\n";
}
echo " </select><span id=\"klik_kur\"></span></form>";

if($_GET['prodi']){
	if($_GET['tahun']){
		$insert=array(); $update=array(); $normal=array();
		$sql=$odbc->query("select * from TBKMK.DBF where KDPTITBKMK='$kode_pt' and KDPSTTBKMK='$_GET[prodi]' and STKMKTBKMK='A' and THSMSTBKMK like '$_GET[tahun]%'");
		$mk=array();
		$insert=array(); $kmk=array();
		foreach($sql as $k=>$r){
			$kodemk=$r['KDKMKTBKMK'];
			$mk[$kodemk]=$r;
			$kmk[]="'$kodemk'";
		}
		$skstt=0; $smtr=array(); $r=array();
		$x = $proxy->GetRecordsets('mata_kuliah',"id_sms='$id_sms' and kode_mk in(".implode(",",$kmk).")");
		$res=$x['result']; $mkfeeder=array();
		foreach($res as $k=>$r){
			$kode_mk=trim($r['kode_mk']);
			$mkfeeder[$kode_mk]=$r;
		}
		foreach($mk as $kode=>$v){
			$r=$v;
			$sks=(int)$v['SKSMKTBKMK'];
			$idmk=$mkfeeder[$kode]['id_mk'];
			if($idmk==''){
				$insert[]=$r;
			}else{
				$r['id_mk']=$idmk;
				$rs=$mkfeeder[$kode];
				$c=bandingkan($r,$rs);
				if($c==1){
					$update[]=$r;
				}else{
					$normal[]=$r;
				}				
			}
			$skstt=$skstt+$sks;
			$smtv=$v['SEMESTBKMK'];
			$smtr[$smtv]=$smtv;
		}
		
		ksort($smtr);
		$semester=(int)end($smtr);
		$namakur="$nama_prodi $_GET[tahun]"; $smt=$_GET['tahun']."1";
		$x = $proxy->GetRecords('kurikulum',"id_sms='$id_sms' and id_smt_berlaku='$smt'");		
		$rs=$x['result'];
		$id_kurikulum_sp=$rs['id_kurikulum_sp'];
		
		if($id_kurikulum_sp==''){
			$p=array();
			$p['nm_kurikulum_sp']=$namakur;
			$p['jml_sem_normal']=$semester;
			$p['jml_sks_lulus']=$skstt;
			$p['jml_sks_wajib']=$skstt;
			$p['jml_sks_pilihan']=0;
			$p['id_sms']=$id_sms;
			$p['id_jenj_didik']=$id_jenj;
			$p['id_smt_berlaku']=$smt;
			$result = $proxy->InsertRecords('kurikulum',$p);
			$id_kurikulum_sp=$result['result']['id_kurikulum_sp'];
		}else{
			$namakur2=$rs['nm_kurikulum_sp'];
			if(trim(strtolower($namakur))!=trim(strtolower($namakur2))){
				$key["id_kurikulum_sp"]=$id_kurikulum_sp;
				$dtupdate['nm_kurikulum_sp']=$namakur;
				$updatekur=array('key'=>$key,'data'=>$dtupdate);
				$x=$proxy->UpdateRecords('kurikulum',$updatekur);
			}
		}
		
		$data=array();
		$data['nm_kur']=$namakur;
		$data['id_kurikulum_sp']=$id_kurikulum_sp;
		$data['smt']=$smt;
		$data['sks']=$skstt;
		$data['semester']=$semester;
		$data['update']=$update;
		$data['insert']=$insert;
		$data['normal']=$normal;
		$s='$';echo "\r\n<script>$s('#klik_kur').html('&nbsp;&nbsp;&nbsp;&nbsp;Nama Kurikulum : <a target=\"_blank\" href=\"$server_url/kurikulumsp/detail/$id_kurikulum_sp\"><strong><u>$namakur</u></strong></a>');</script>\r\n";
		tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($data));
echo "<div id=\"balikan_mk\"></div><div style=\"width:1200px\">
<table class=\"table table-condensed\">
  <tr>
    <th width=\"4%\" rowspan=\"2\" style=\"text-align:center\">No</th>
    <th rowspan=\"2\" style=\"text-align:center\" width=\"11%\">Kode Matakuliah</th>
    <th width=\"27%\" rowspan=\"2\" style=\"text-align:center\">Nama Matakuliah</th>
    <th colspan=\"2\" style=\"text-align:center\">SKS</th>
    <th rowspan=\"2\" style=\"text-align:center\" width=\"8%\">Semester</th>
    <th width=\"25%\" rowspan=\"2\">Keterangan</th>
  </tr>
  <tr>
    <th width=\"6%\" style=\"text-align:center\">Mata Kuliah</th>
    <th width=\"6%\" style=\"text-align:center\">Tatap Muka</th>
  </tr>";
  $no=0; $skstt=0;
foreach($mk as $k=>$r){	
	$ket="-";
	$sks=(int)$r['SKSMKTBKMK'];
	$skstt=$skstt + $sks;
	$no++;
	echo "<tr>
    <td style=\"text-align:center\">$no</td>
    <td style=\"text-align:center\"><span id=\"mk_$r[KDKMKTBKMK]\">$r[KDKMKTBKMK]</span></td>
    <td style=\"text-align:left\"><span id=\"nk_$r[KDKMKTBKMK]\">$r[NAKMKTBKMK]</span></td>
    <td style=\"text-align:center\">$r[SKSMKTBKMK]</td>
    <td style=\"text-align:center\">$r[SKSMKTBKMK]</td>
    <td style=\"text-align:center\">$r[SEMESTBKMK]</td>
    <td><span id=\"sp_$r[KDKMKTBKMK]\"></span></td>
  </tr>";
} 
echo "<tr>
    <th colspan=\"3\" style=\"text-align:right\">Jumlah SKS</th>
    <th style=\"text-align:center\">$skstt</th>
    <th style=\"text-align:center\">$skstt</th>
    <th colspan=\"2\"></th>
  </tr>
</table>
</div>";
echo "\r\n\r\n<div id=\"detail_mk\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n"; $up=1;
	if($up >0){
		$s='$';
		echo "\r\n\r\n<script>\r\n$s('#balikan_mk').load('".PATH."/index.php?load_mk=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');\r\n</script>\r\n<script>\r\n$s(function() {
		$s( \"#detail_mk\" ).dialog({
			height: 100,
			width:'450',
			modal: true,
			title: 'Wait'
		});
	});\r\n
	function pbar(){
		var x=$s('#p_bar').text();
		$s('#p_bar').text(x+'.');
		setTimeout(\"pbar()\",500);
	}
	\r\n/* setTimeout(\"pbar()\",500); */\r\n
	</script>\r\n";
	}
}}
?>
