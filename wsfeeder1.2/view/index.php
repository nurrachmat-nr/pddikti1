<?php
$id_sp=$_SESSION['data_pt']['id_sp'];
$filter_sms= "id_sp = '$id_sp' and stat_prodi='A' and id_jns_sms='3'";
global $proxy;
$proxy=new PROXY();
$proxy->connect();

$x = $proxy->GetRecordsets('sms',$filter_sms,'','','');

$y=$x['result'];
$prodi=array();
if(count($y)){
	foreach($y as $k=>$r){
		$id_sms=$r['id_sms'];
		$kode=trim($r['id_jur']);
		$prodi[$id_sms]=$r;
		$prodi[$kode]=$r;
	}
}
tulis_data(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt",json_encode($prodi));
if(file_exists(SISTEM_TMP."/data-pt.txt")){
	$dt=file_get_contents(SISTEM_TMP."/data-pt.txt");
}else{
	$alamat=$_SESSION['server_url']."/profil";
	$data=array();
	$xx=kirim_data($alamat,'get',$data);
	$dt=kompres($xx['isi']);
	tulis_data(SISTEM_TMP."/data-pt.txt",$dt);
}
$rf='<div class="row-fluid">'.cari('<div class="row-fluid">','<div id="tabs">',$dt);
if(!$_SESSION['data_pt']){
	require_once(APP_PATH."/lib/class.table_ex.php");
	$tabel=cari('<table class="table form-inline">','</table>',$rf);
	if($tabel!='0'){
		$d=array();
		$tabel="<table><tr><td>satu</td><td>dua</td></tr>".$tabel."</table>";
		$tbl = new tableExtractor; 
		$tbl->source = $tabel;
		$tbl->anchor ='';
		$tbl->anchorWithin = true;
		$d = $tbl->extractTable();
		tulis_data(SISTEM_TMP."/data-awal.txt",json_encode($d));
		echo "<div align=\"center\"><img src=\"".PATH."/app/images/ajax-loading.gif\"><br>Tunggu sedang mengambil data feeder ....</div>";
		echo "<script>\r\nfunction bukaw()".'{'."window.location='?init=1';}setTimeout(\"bukaw()\",2000);\r\n</script>";
		exit;
	}else{
		die("Halaman Web Feeder telah diubah, tidak dapat mengidentifikasi Kode PT");
	}
}
$tabs=cari('<div id="tabs">','<script type="text/javascript">',$dt);
if($tabs=='0'){
	echo "<script type=\"text/javascript\">window.location='".PATH."/index.php?logout=3';</script>";
	exit;
}
$tabs='<div id="tabs">'.$tabs;
echo "<div align=\"center\"><img style=\"display:none\" id=\"dos2feeder\" src=\"".PATH."/app/images/layar_biru.png\"></div>";
echo $rf;
echo $tabs;
$s='$';
echo "<script type=\"text/javascript\">\r\n$s(document).ready(function() ".'{'." $(\"#tabs\").tabs(); });\r\nfunction show_imgs()".'{'."$s('#dos2feeder').fadeIn();}\r\nsetTimeout(\"show_imgs()\",2500);\r\n</script>";
?>