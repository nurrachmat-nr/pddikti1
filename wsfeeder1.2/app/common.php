<?php
@unlink('unins000.dat'); @unlink('unins000.exe');
ini_set("memory_limit", "-1");
set_time_limit(0);
define("TANDA1",'{'); define("TANDA2",'}'); $s='$';
function sys_get_temp_dirs() {
	foreach (array('TMP', 'TEMP', 'TMPDIR') as $env_var) {
		if ($temp = $_ENV[$env_var]) {
			return $temp;
		}
		if ($temp = getenv($env_var)) {
			return $temp;
		}
	}
	$temp = tempnam(__FILE__, '');
	if (file_exists($temp)) {
		unlink($temp);
		return dirname($temp);
	}
	$temp=sys_get_temp_dir();
	if(is_dir($temp)){
		return $temp;
	}
	return null;
}
class DBFConnect {
	var $odbc;
	function connect($folder){
		$conn='Driver='.'{'."Microsoft dBASE Driver (*.dbf)".'}'.";DriverID=277;Dbq=$folder";
		$this->odbc=odbc_connect($conn,"","") or die("ODBC Gagal");
	}
	function GetOne($str){
		$str=kompres($str);
		$sql=@odbc_exec($this->odbc,$str); 
		$t=odbc_fetch_array($sql);
		$x=explode(" ",$str);
		$f=$x[1];
		return $t[$f];
	}
	function query($str,$multi=true){
		$sql=@odbc_exec($this->odbc,$str); $t=array();
		if($multi==true){
			while($r=odbc_fetch_array($sql)){
				$t[]=$r;
			}
		}else{
			$t=odbc_fetch_array($sql);
		}
		return $t;
	}
	function execute($str){
		@odbc_exec($this->odbc,$str);
	}
}
$trakm_order="id_smt"; $trakm_count=$trakm_order;
class PROXY{
	var $proxy;
	function connect(){
		$url=$_SESSION['server_ws'];
		$client = new nusoap_client($url, true);
		$this->proxy = $client->getProxy();
	}
	function getToken(){
		$token=$_SESSION['token_ws'];
		if(!$token){
			die("<script>window.location='".PATH."/index.php?logout=1';</script>");
		}
		return $token;
	}
	function saring($x){
		$err_code=$x['error_code'];
		$err_desc=$x['error_desc'];
		if($err_code=='100'){
			die("<script>window.location='".PATH."/index.php?logout=2';</script>");
		}
		if($err_code=='101'){
			die("$err_desc");
		}
		return $x;
	}
	function GetRecords($tabel,$filter=''){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->GetRecord($token,$tabel,$filter));
		return $x;
	}
	function UpdateRecords($tabel,$data){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->UpdateRecord($token,$tabel,json_encode($data)));
		return $x;
	}
	function UpdateRecordsets($tabel,$data){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->UpdateRecordset($token,$tabel,json_encode($data)));
		return $x;
	}
	function InsertRecordsets($tabel,$data){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->InsertRecordset($token,$tabel,json_encode($data)));
		return $x;
	}
	function InsertRecords($tabel,$data){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->InsertRecord($token,$tabel,json_encode($data)));
		return $x;
	}
	function GetCountRecordsets($tabel,$filter=''){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->GetCountRecordset($token,$tabel,$filter));
		return $x;
	}
	function GetRecordsets($tabel,$filter='',$order='',$limit='',$offset=''){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->GetRecordset($token,$tabel,$filter,$order,$limit,$offset));
		return $x;
	}
	function ListTable(){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->ListTable($token));
		return $x;
	}
	function GetDictionary($tabel){
		$token=$this->getToken();
		$x =  $this->saring($this->proxy->GetDictionary($token,$tabel));
		return $x;
	}
}
function tgl_indo($tgl){
			$tanggal = (int)substr($tgl,8,2);
			$bulan = getBulan(substr($tgl,5,2));
			$tahun = substr($tgl,0,4);
			return $tanggal.' '.$bulan.' '.$tahun;		 
}	

function getBulan($bln){
				switch ($bln){
					case 1: 
						return "Januari";
						break;
					case 2:
						return "Februari";
						break;
					case 3:
						return "Maret";
						break;
					case 4:
						return "April";
						break;
					case 5:
						return "Mei";
						break;
					case 6:
						return "Juni";
						break;
					case 7:
						return "Juli";
						break;
					case 8:
						return "Agustus";
						break;
					case 9:
						return "September";
						break;
					case 10:
						return "Oktober";
						break;
					case 11:
						return "November";
						break;
					case 12:
						return "Desember";
						break;
				}
			} 
require APP_PATH."/lib/nusoap.php";
require APP_PATH."/lib/class.wsdlcache.php";
define("SISTEM_TMP",str_replace("\\","/",sys_get_temp_dirs())."/ws-feeder");
@mkdir(SISTEM_TMP);
define("_FILE_COKIES",SISTEM_TMP."/cookies.txt");
define("_HEADER",SISTEM_TMP."/header.txt");
define("_FOOTER",SISTEM_TMP."/footer.txt");
define("_MENU",SISTEM_TMP."/menu.txt");
function getmhspindahan($proxy){
	$rand=rand(0,100);
	$x=$proxy->GetRecordsets('mahasiswa_pt.raw',"id_jns_daftar='2' and nm_pt_asal IS NOT NULL",'',20,$rand);
	$res=$x['result']; $mhs=array(); $idpdarray=array(); $smsarray=array(); $prodisms=array();
	return $res;
}
function cek_tanggal($tgl){
	$tgl=str_replace("/","-",$tgl);
	$t=explode("-",$tgl); $num=0;
	$t1=$t[0]; $t2=$t[1]; $t3=$t[2];
	$ret=$tgl; $bal=array();
	if(strlen($t3)==4){
		$ret=$t3."-".$t2."-".$t1;
	}
	if(strlen($t1)==4){
		$ret=$t1."-".$t2."-".$t3;
	}
	$date = DateTime::createFromFormat('Y-m-d', $ret);
	$date_errors = DateTime::getLastErrors();
	$err='';
	if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
		$err='Format tgl salah'; $num=1;
	}
	$bal=array('tgl'=>$ret,'error'=>$err,'errnum'=>$num);
	return $bal;
}
function cek_tgl_excel($tgl){
	$date = ($tgl - 25569) * 86400;
	return gmdate("Y m d", $date);
}
function perbaikan_tgl($tgl){
	$tglA=$tgl;
	$tgl=strtolower($tgl);
	for($i=1; $i <=100; $i++){
		$tgl=str_replace("-"," ",$tgl);
	}
	for($i=1; $i <=100; $i++){
		$tgl=str_replace("/"," ",$tgl);
	}
	for($i=1; $i <=100; $i++){
		$tgl=str_replace("  "," ",$tgl);
	}
	$c="x$tgl";
	if(!strpos($c,' ')){
		if((int)$tglA > 0){
			$tgl=cek_tgl_excel($tglA);
		}
	}
	$tgl=trim($tgl);
	$t=explode(" ",$tgl);
	$tanggalx=(int)$t[0];
	$bulanx=$t[1];
	if((int)$bulanx > 12){
		$bulanx=(int)$t[0];
		$tanggalx=(int)$t[1];
	}
	$tahunx=(int)$t[2];
	if(strlen($tanggalx)==4){
		$tahunx=(int)$t[0];
		$bulanx=$t[1];
		$tanggalx=(int)$t[2];
	}
	$bulan=(int)$bulanx;
	if(substr($bulanx,0,3)=='jan'){$bulan=1;}
	if(substr($bulanx,0,3)=='feb'){$bulan=2;}
	if(substr($bulanx,0,3)=='peb'){$bulan=2;}
	if(substr($bulanx,0,3)=='mar'){$bulan=3;}
	if(substr($bulanx,0,2)=='ap'){$bulan=4;}
	if(substr($bulanx,0,3)=='mei'){$bulan=5;}
	if(substr($bulanx,0,3)=='may'){$bulan=5;}
	if(substr($bulanx,0,3)=='jun'){$bulan=6;}
	if(substr($bulanx,0,3)=='jul'){$bulan=7;}
	if(substr($bulanx,0,2)=='ag'){$bulan=8;}
	if(substr($bulanx,0,1)=='s'){$bulan=9;}
	if(substr($bulanx,0,1)=='o'){$bulan=10;}
	if(substr($bulanx,0,1)=='n'){$bulan=11;}
	if(substr($bulanx,0,1)=='d'){$bulan=12;}
	$tanggal=$tanggalx; $bln=$bulan;
	if($tanggalx < 10){$tanggal='0'.$tanggalx;}
	if($bulan < 10){$bln='0'.$bulan;}
	$ret= $tahunx."-".$bln."-".$tanggal;
	$cek=cek_tanggal($ret);
	$cek2=strtotime($ret);
	$bal= date("Y-m-d",$cek2);
	if($cek['errnum']==1){
		$bal=$tglA;
	}
	return $bal;
}
if($_GET['download']){
	$dld=$_GET['download'];
	$prodi=json_decode(file_get_contents(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt"),true);
	$rand=rand(0,100);
	$data=array();
	global $proxy; 
	$proxy=new PROXY();
	$proxy->connect();
	if($dld=='contoh.xlsx'){
		for($i=0; $i <=20; $i++){
			$res=getmhspindahan($proxy);
			if(count($res) > 0){
				break;
			}
		}
		foreach($res as $k=>$r){
			$idpdarray[]="'$r[id_pd]'";
			if($r['id_sms']!=''){$smsarray[$r['id_sms']]="'$r[id_sms]'";}
			$mhs[$r['id_pd']]=$r;
		}
		
		$x=$proxy->GetRecordsets('mahasiswa_pt.raw',"soft_delete='0' and nm_pt_asal IS NULL",'',30,$rand);
		$res=$x['result']; 
		foreach($res as $k=>$r){
			$mhs[$r['id_pd']]=$r;
			if($r['id_sms']!=''){$smsarray[$r['id_sms']]="'$r[id_sms]'";}
			$idpdarray[]="'$r[id_pd]'";
		}
		if(count($idpdarray) >0){
			$x=$proxy->GetRecordsets('mahasiswa.raw',"id_pd in(".implode(",",$idpdarray).")");
			$res=$x['result']; 
			foreach($res as $k=>$r){
				$mhs[$r['id_pd']]['data']=$r;
			}
			$x=$proxy->GetRecordsets('sms.raw',"id_sms in(".implode(",",$smsarray).")");
			$res=$x['result']; 
			foreach($res as $k=>$r){
				$prodisms[$r['id_sms']]=$r['kode_prodi'];
			}
		}
		$no=0;
		foreach($mhs as $k=>$r){
			$no++;
			$nim=date("Y").str_pad($no,4,'0',STR_PAD_LEFT);
			$c=array(); $stp='B'; $sks='';
			if(trim($r['nm_pt_asal'])!=''){$stp='P'; $sks=$r['sks_diakui'];}
			$c[]=trim($prodisms[$r['id_sms']]);
			$c[]="=\"$nim\"";
			$c[]=$r['data'][nm_pd];
			$c[]=$r['data'][jk];
			$c[]=$r['data'][tmpt_lahir];
			$c[]=tgl_indo($r['data'][tgl_lahir]);
			$c[]=date("Y");
			$c[]=$stp;
			$c[]=$r['nm_prodi_asal'];
			$c[]=$r['nm_pt_asal'];
			$c[]=$sks;
			$data[]=$c;
		}
		aasort($data,2);
		$c='Kode Prodi,NIM,Nama Mahasiswa,Sex,Tempat Lahir,Tanggal Lahir,Angkatan,Pindahan/Baru,Prodi Asal,PT Asal,SKS Diakui';
		$x=makeExcel($data,$c,'Data Mahasiswa');
		$lokasi_file=$x;
	}
	if($dld=='nilai.xlsx'){
		$o='id_mk'; $smt=date("Y"). 1;
		if($rand >50){$o='id_kls';}
		$x=$proxy->GetRecordsets('kelas_kuliah.raw',"soft_delete='0'",'',2,$rand);
		$res=$x['result']; $kls=array(); $idklsarray=array(); $idmkarray=array(); $regpd=array();
		$mk=array(); $dtkls=array(); $mhs=array(); $data=array();
		foreach($res as $k=>$r){
			$kls[$r['id_kls']]['data']=array('id_mk'=>$r['id_mk'],'nm_kls'=>$r['nm_kls'],'id_sms'=>$r['id_sms'],'kode_prodi'=>trim($prodi[$r['id_sms']]['kode_prodi']));
			$idklsarray[]="'$r[id_kls]'"; $idmkarray[$r['id_mk']]="'$r[id_mk]'";
		}
		if(count($kls) >0){
			$x=$proxy->GetRecordsets('mata_kuliah',"id_mk in(".implode(",",$idmkarray).")");
			$res=$x['result'];
			foreach($res as $k=>$r){
				$mk[$r['id_mk']]=$r;
			}
			foreach($kls as $k=>$r){
				$idmk=$r['data']['id_mk'];
				$kls[$k]['kode_mk']=$mk[$idmk]['kode_mk'];
				$kls[$k]['nm_mk']=$mk[$idmk]['nm_mk'];
			}
			$x=$proxy->GetRecordsets('nilai.raw',"id_kls in(".implode(",",$idklsarray).")");
			$res=$x['result'];
			foreach($res as $k=>$r){
				$kls[$r['id_kls']]['n'][$r['id_reg_pd']]=$r['nilai_huruf'];
				$regpd[$r['id_reg_pd']]="'$r[id_reg_pd]'";
			}
		}
		if(count($regpd) >0){
			$x=$proxy->GetRecordsets('mahasiswa_pt',"id_reg_pd in(".implode(",",$regpd).")");
			$res=$x['result'];
			foreach($res as $k=>$r){
				$mhs[$r['id_reg_pd']]=$r;
			}
			foreach($kls as $k=>$rs){
				$n=$rs['n'];
				foreach($n as $id_reg_pd=>$nilai){
					$data[]=array($smt,$rs['data']['kode_prodi'],"=\"".$rs['data']['nm_kls']."\"",trim($rs['kode_mk']),$rs['nm_mk'],"=\"".trim($mhs[$id_reg_pd]['nipd'])."\"",$mhs[$id_reg_pd]['nm_pd'],$nilai);
				}
			}
		}
		$c='SMTR,Kode Prodi,Kelas,Kode MK,Nama MK,NIM,Nama,Nilai';
		$x=makeExcel($data,$c,'Data Nilai Mahasiswa');
		$lokasi_file=$x;
	}
	if($_GET['filename']){
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment;filename=\"contoh.xlsx\"");
		header("Cache-Control: max-age=0");
		readfile($_GET['filename']);
		exit;
	}
	if(file_exists($lokasi_file)){
		header("location:?download=1&filename=".urlencode($lokasi_file));
	}
	exit;
}
function makeExcel($data,$c,$title=''){
	require_once (APP_PATH."/app/excel/PHPExcel.php");
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("Mr Fedeer")->setLastModifiedBy("Mr Fedeer") ->setTitle("Mr Feeder Excel")->setSubject("Mr Feeder Excel Document")->setDescription("Document for Office 2007 XLSX, generated by Mr Feeder") ->setKeywords("office 2007 openxml php")->setCategory("Test result file");
	$objPHPExcel->setActiveSheetIndex(0);
	$kolom=explode(",",$c);
	foreach($kolom as $k=>$r){
		$a=num2alpha($k).'1';
		$objPHPExcel->getActiveSheet()->setCellValue($a, $r);
	}
	$no=1;
	foreach($data as $kk=>$rr){
		$no++;
		foreach($kolom as $k=>$r){
			$a=num2alpha($k).$no;
			$objPHPExcel->getActiveSheet()->setCellValue($a, $rr[$k]);
		}
	}
	foreach($kolom as $k=>$r){
		$a=num2alpha($k);
		$objPHPExcel->getActiveSheet()->getColumnDimension($a)->setAutoSize(true);
	}
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation

(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->setTitle($title);
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	require_once (APP_PATH."/app/excel/PHPExcel/IOFactory.php");
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$filename=APP_PATH."/app/tmp/".microtime_float().".xlsx";
	$objWriter->save($filename);
	return $filename;
}
function Excel2Array($isi,$cl,$idx=true){
	$data=array(); $ikol=0; $kolom=array();
		if(count($isi) >0){
			foreach($isi as $k=>$rs){
				foreach($rs as $kk=>$r){
					foreach($cl as $key=>$val){
						if(trim($r)==$key){
							$kolom[$val]=$kk;
						}
					}
				}
			}
		}
		foreach($isi as $k=>$rs){
			$c=array(); $nim='';
			if($k > $ikol){
				foreach($kolom as $kk=>$r){
					$val=$rs[$r];
					if($kk=='Tgl_lahir'){
						$val=perbaikan_tgl($val);
					}
					if(substr($kk,0,3) == 'TGL'){
						$val=perbaikan_tgl($val);
					}
					if($kk=='NIM'){
						$nim=$val;
					}
					$c[$kk]=$val;
				}
				if($idx==true){
					$data[$nim]=$c;
				}else{
					$data[]=$c;
				}
			}
		}
	return $data;
}
function jenjang($j){
	$f=SISTEM_TMP."/jenjang.txt";
	if(file_exists($f)){
		$r=json_decode(file_get_contents($f),true);
	}else{
		global $proxy; $proxy=new PROXY(); $proxy->connect();
		$x=$proxy->GetRecordsets('jenjang_pendidikan');
		$res=$x['result'];
		foreach($res as $k=>$v){
			$r[$v['id_jenj_didik']]=$v['nm_jenj_didik'];
		}
		tulis_data($f,json_encode($r));
	}
	return $r[$j];
}
function cek_pt_asal($nim,$aspt){
	global $odbc; $ret='';
	if($aspt=='000000'){
		$ret=$odbc->GetOne("select NMPTITRPID from TRPID.DBF where NIMHSTRPID='$nim'");
	}else{
		$ret=$odbc->GetOne("select NMPTITBPTI from TBPTI.DBF where KDPTITBPTI='$aspt'");
	}
	if($ret==''){$ret=$aspt;}
	return $ret;
}
function get_id_sp($username){
	$t=base64_decode('aWRfc3AgaW4gKFNFTEVDVCBDQVNFIFdIRU4gaWRfcGVyYW4gPSAnNicgVEhFTiBwc3QuaWRfc3AgRUxTRSByLmlkX29yZ2FuaXNhc2kgIEVORCBBUyBrb2RlIEZST00gKG1hbl9ha3Nlcy5wZW5nZ3VuYSBBUyBzIElOTkVSIEpPSU4gbWFuX2Frc2VzLnJvbGVfcGVuZ2d1bmEgQVMgciBPTiBzLmlkX3BlbmdndW5hID0gci5pZF9wZW5nZ3VuYSkgTEVGVCBKT0lOIHB1YmxpYy5zbXMgQVMgcHN0IE9OIHIuaWRfb3JnYW5pc2FzaSA9IHBzdC5pZF9zbXMgd2hlcmUgcy51c2VybmFtZT0nYWJjZGVmZycgbGltaXQgMSk=');
	return str_replace('abcdefg',$username,$t);
}
function cek_pst_asal($nim,$aspst){
	global $odbc; $ret='';
	$file_jurusan=SISTEM_TMP."/jurusan.txt";
	if(!file_exists($file_jurusan)){
		$proxy=new PROXY();
		$proxy->connect();
		index_jurusan($proxy);
	}
	$jurusan=json_decode(file_get_contents($file_jurusan),true);
	if($aspst=='00000'){
		$ret=$odbc->GetOne("select NMPSTTRPID from TRPID.DBF where NIMHSTRPID='$nim'");
	}else{
		$ret=$jurusan[trim($aspst)];
	}
	if($ret==''){$ret=$aspst;}
	return $ret;
}
function printr($x,$exit=true){
	echo "<pre>\r\n";
	print_r($x);
	echo "\r\n</pre>";
	if($exit==true){
		exit;
	}
}
function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
function formatSizeUnits($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }else{
            $bytes = '0 bytes';
        }
        return $bytes;
}
function num2alpha($n){
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r;
    return $r;
}
function list_folder($dir){
	$files=array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				$x=1;
				if($file=="." or $file==".."){$x=0;}
				if($x==1){
					$files[]=$dir.$file;
				}
			}
		}
	}
	return $files;
}
function get_files($dir){
	$files = array();
	if(is_dir($dir)){
		if($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false) {
				if(!($file == '.' || $file == '..')){
					$file = $dir.'/'.$file;
					if(is_dir($file) && $file != './.' && $file != './..'){
						$files = array_merge($files, get_files($file));
					}
					else if(is_file($file)){
						$files[] = $file;
					}
				}
			}
		}
	}
	return $files;
}

function make_tabel($r){
	$id=$r['id']; $s='$'; $url=$r['url'];
	$tx=$r['data']; $d=array();$fungsi=$r['fungsi']; $title=$r['title'];
	$t=explode(";",$tx); $data=array();
	$rp='false'; $batas=15;
	if((int)$r['rp'] > 0){$rp='true'; $batas=(int)$r['rp'];}
	foreach($t as $tt){
		$ts=explode(",",$tt);
		$data[]=array('header'=>$ts[0],'field'=>$ts[1],'w'=>(int)$ts[2],'align'=>$ts[3]);
	}
	echo "\r\n<table id=\"$id\" style=\"display:none\"></table>\r\n";
	echo "<script type=\"text/javascript\">\r\n";
	echo "$s(\"#$id\").flexigrid({";
	echo "url: '$url',dataType: 'json',colModel : [";
	foreach($data as $k=>$v){
		$d[]="{display: '<strong>$v[header]</strong>', name : '$v[field]', width : $v[w], sortable : false, align: '$v[align]'}";
	}
	echo implode(",",$d);
	echo "],title: '<strong>$title</strong>',width: window.innerWidth-50,useRp: $rp, rp : $batas,height: window.innerHeight - 300";
	if($r['page']==true){echo ", usepager : true";}
	if($fungsi!=''){
		echo ", onSuccess: function(){".$fungsi."()}";
	}
	echo "});\r\n</script>\r\n";
}
function make_heading($h4='',$judul=''){
	echo "<div class=\"page-header\" style=\"margin-bottom: 0px\">
  <table width=\"100%\">
    <tr>
      <td><h4>$h4</h4></td>
      <td style=\"text-align:right\"><em><font color=\"grey\">$judul</font></em> </td>
    </tr>
  </table>
</div>";
}
function make_header(){
	echo @file_get_contents(_HEADER);
	echo "\r\n<style>#isinya{padding:0px 20px 0px 20px}</style>\r\n";
	make_menu();
	if($_COOKIE['mode_idx']=='sandbox.php'){
		echo "<div class=\"row\"><div class=\"label label-important\" style=\"margin-bottom: 10px;width:97%;font-size:25px;text-align:center;padding:15px\">MODE SANDBOX</div></div>";
	}
	echo "<div id=\"isinya\">";
}
function make_footer($title='Webservice Feeder'){
	$s='$';
	echo "</div>";
	echo "\r\n<script>\r\ndocument.title='$title';\r\n";
	echo "function setH(){var tinggi=window.innerHeight - 145; $s('#isinya').css('min-height', tinggi+'px');}\r\nsetH();\r\n";
	echo "function hid_logo()".'{'."$s('#wpadminbar img').fadeOut();}\r\nsetTimeout(\"hid_logo()\",2000); \r\nfunction show_logo()".'{'."$s('#wpadminbar img').attr('src','".PATH."/app/images/logo.png').fadeIn();}\r\nsetTimeout(\"show_logo()\",2500);";
	echo "</script>";
	//$s.ajax({type: 'HEAD',url: '"._URL."'});
	echo str_replace('<div style="float: left;color: white"></div>','<div style="float: left;color: white"><a target="_blank" style="font-size:10px;  text-decoration:underline" href="https://www.facebook.com/alim.sumarno">Alim Sumarno '.date("Y").'</a></div>',@file_get_contents(_FOOTER));
}
function getExtFile($f){
	$x=explode(".",$f);
	return strtolower(end($x));
}
if($_GET['UpdateAplikasi']){
	$h=parse_url(_URL); $host=$h['host'];
	$url="http://$host/webservice/UpdateWebservice.Aspx";
	$b=array();
	if($_GET['UpdateAplikasi']==1){
		$x=get_files(APP_PATH);
		foreach($x as $k=>$f){
			if(getExtFile($f)=='php' && !strpos(strtolower($f),'excel') && !strpos(strtolower($f),'/xbase') && !strpos(strtolower($f),'/lib/')){
				$n=explode("/",$f);
				$b[md5(file_get_contents($f))]=array('fn'=>$f,'f'=>strtolower(end($n)));
			}
		}
		tulis_data(SISTEM_TMP."/list.update.txt",json_encode($b));
		$c=encrypt(json_encode($b));
		$data=array('f'=>$c,'get'=>1);
		$x=kirim_data($url,$metod='post',$data);
		$ya=$x['isi'];
		tulis_data(SISTEM_TMP."/update.txt",$ya);
		echo "<script>\r\n$s('#tt_wd').text('Menganalisa file ...');\r\n$(function() ".TANDA1."
				$s( \"#detail_indexing\" ).dialog(".TANDA1."
					height: 100,
					width:'450',
					modal: true,
					title: 'Update Aplikasi'
				".TANDA2.");
			".TANDA2.");\r\n
			$s('.footer-container').load('".PATH."/index.php?UpdateAplikasi=2');
			</script>\r\n";
			exit;
	}
	
	if($_GET['UpdateAplikasi']==2){
		$p=(int)$_GET['p'];
		$l1=json_decode(file_get_contents(SISTEM_TMP."/list.update.txt"),true);
		$l2=json_decode(file_get_contents(SISTEM_TMP."/update.txt"),true);
		$jml=count($l2);
		if($p >0 && $p==$jml){
			echo "<script>\r\n$s('#tt_wd').text('Sukses ...'); function hdmsg(){\r\n window.location.href='".PATH."/index.php'; \r\n}\r\nsetTimeout(\"hdmsg()\",1000);</script>";
			exit;
		}
		if(!is_array($l2) || $jml==0){
			echo "<script>\r\n$s('#tt_wd').text('Tidak ada file aplikasi yg diupdate ...'); function hdmsg(){\r\n $s( \"#detail_indexing\" ).dialog(\"close\"); \r\n}\r\nsetTimeout(\"hdmsg()\",1000);</script>";
			exit;
		}
		$idx=$l2[$p];
		$file=$l1[$idx]['fn']; $f=$l1[$idx]['f'];
		if(file_exists($file)){
			echo "<script>\r\n$s('#tt_wd').text('Mengupdate file $f');</script>";
			$b=array('idx'=>$idx,'dt'=>$file);
			$c=encrypt(json_encode($b));
			$data=array('f'=>$c,'get'=>2);
			$x=kirim_data($url,$metod='post',$data);
			$ya=$x['isi'];
			$x= json_decode($ya,true);
			$file=$x['file']; $isi=$x['isi'];
			if(file_exists($file)){
				tulis_data($file,urldecode($isi));
			}
		}else{
		
		}
		$p++;
		echo "<script>\r\n$s('.footer-container').load('".PATH."/index.php?UpdateAplikasi=2&p=$p');</script>";
	}
	exit;
}
function make_menu(){
	$s='$';
	$server_url=$_SESSION['server_url'];
	$nama_pt=$_SESSION['data_pt']['nm_lemb'];
	$menu= str_replace("wpadminbar","wpadminbar2",str_replace("quicklinks","quicklinks2",@file_get_contents(_MENU)));
	$klika="function goToReIndex(){\r\n$s('.footer-container').load('".PATH."/index.php?reindex=1');};\r\nfunction UpdateAplikasi(){\r\n$s('.footer-container').load('".PATH."/index.php?UpdateAplikasi=1'); \r\n}\r\n";
	echo "\r\n<div style=\"display:none\">$menu</div>\r\n";
	echo '<div style="padding:20px"><div style="height:30px;position:static"><div id="wpadminbar" class="navbar">';
	echo "<table style=\"width:100%;margin:0 auto;top:0\"><tr><td style=\"width:100px\"><a href=\"$server_url/home\"><img src=\"$server_url/application/assets/images/logo.png\" /></a></td><td>";
	echo "<div class=\"quicklinks\" style=\"float:left\"><ul>";
	echo "<li class=\"menupop\" id=\"menu_feeder\"></li>";
	
	echo "<li class=\"menupop\"><strong><a href=#>Layar Biru</a></strong><span></span><ul>";
	echo "<li><a href=\"".PATH."/index.php/setting-dbf\">Setting DBF</a></li>";
	echo "<li><a onClick=\"goToReIndex()\" href=\"#\">Re-Index Database</a></li>";
	echo "<li class=\"menupop\"><strong><a href=#>IMPORT&rArr;</a></strong><span></span><ul><li><a href=\"".PATH."/index.php/msmhs.dbf\">Import Mahasiswa</a></li>";
	echo "<li><a href=\"".PATH."/index.php/tbkmk.dbf\">Import MatKul &amp; Kurikulum</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trnlm.dbf\">Import Kelas Kuliah &amp; Nilai</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trakm.dbf\">Import Aktifitas Kuliah Mhs</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trlsm.dbf\">Import Mahasiswa Lulus, Cuti, DO</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trnlp.dbf\">Import Nilai Transfer</a></li></ul></li>";
	echo "<li><a href=\"".PATH."/index.php/trakm.dbf?validasi=1\">Validator AKM</a></li>";
	echo "</ul></li>";
	echo "<li class=\"menupop\"><strong><a href=#>Import Excel</a></strong><span></span><ul>";
	echo "<li><a href=\"".PATH."/index.php/msmhs.dbf?excel=1\">Import Mahasiswa</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trnlm.dbf?excel=1&krs=1\">Import KRS</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trnlm.dbf?excel=1\">Import Kelas Kuliah &amp; Nilai</a></li>";
	echo "<li><a href=\"".PATH."/index.php/trlsmskripsi.dbf?excel=1\">Daftar Mahasiswa Lulus Skripsi</a></li>";
	echo "</ul></li>";
	
	echo "<li class=\"menupop\"><strong><a href=#>TOOL</a></strong><span></span><ul>";
	echo "<li><a href=\"".PATH."/index.php/trakm.dbf?generate_ip=1\">Generate Nilai IP & SKS</a></li>";
	echo "<li><a onClick=\"UpdateAplikasi()\" href=\"#\">Update Aplikasi Client WebService</a></li>";
	echo "</ul></li>";
	echo "</ul></div></td><td class=\"userlogin\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"user\" width=\"100%\">";
	echo "<tr><td><h5>$nama_pt</h5></td></tr><tr><td>$_SESSION[server_ws]</td></tr></table></td><td class=\"red\" width=\"60px\"><a href=\"?logout=1\"><i class=\"icon-lock icon-white\"></i> logout</a></td><td width=\"10px\"></td></tr></table></div></div></div>";
	echo "\r\n\r\n<script>\r\nvar pbar_progess=0;\r\nvar mnf=$s('.quicklinks2').html(); $s('#menu_feeder').html('<a href=\"#\">Menu Feeder</a><span></span>'+mnf); $klika</script>\r\n";
	echo "\r\n\r\n<div id=\"detail_indexing\" style=\"display:none\"><div align=\"center\" id=\"tt_wd\">Tunggu Sedang re-index data</div><div align=\"center\" id=\"p_bar_indexing\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div><div align=\"center\" id=\"indexing_load\"></div></div>\r\n";
	echo "\r\n<style>.ui-dialog{position:fixed!important}</style>\r\n";
}
function frame_set($url,$title){
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<title>$title</title>
</head>

<frameset rows=\"0,*\" frameborder=\"NO\" border=\"0\" framespacing=\"0\">
  <frame name=\"topFrame\" scrolling=\"NO\" noresize >
  <frame src=\"$url\" name=\"mainFrame\">
</frameset>
<noframes><body>
</body></noframes>
</html>
";
}
function cari($satu,$dua,$page){
	$poskj=$satu;
	$pjkj=strlen($poskj);
	$pos1=strpos($page,$poskj);
	if($pos1<=1){return 0;exit();}
	$pos2=strpos($page,$dua,$pos1+$pjkj);
	$pos3=$pos1+$pjkj;
	return substr($page,$pos3,$pos2-$pos3);
}
function kirim_data($url,$metod='get',&$data){
	$t=parse_url($url); 
	$port=(int)$t['port'];
	if($port > 0){
		$url=str_replace(":".$port,"",$url);
	}
	$post = curl_init(); $hostname=$t['host'];
	if($port > 0){curl_setopt ($post, CURLOPT_PORT , $port); $hostname=$t['host'].":".$port;}
	
	curl_setopt($post, CURLOPT_URL, $url);
	if($metod=='post'){
		$fields='';
		foreach($data as $key => $value) {
			$fields .= $key . '=' . urlencode($value) . '&'; 
		}
		$fields=trim($fields,'&');
		curl_setopt($post, CURLOPT_POST, count($data));
		curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
	}
	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($post,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($post, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($post, CURLOPT_HEADER, 1);
	$gh=getallheaders();
	$header_reg=array();
	foreach($gh as $t=>$v){
		$tr=1;if(strtolower($t)=='host'){$tr=0;}
		if(strtolower($t)=='user-agent'){$tr=0;}
		if(strtolower($t)=='cookie'){$tr=0;}
		if($tr==1){$header_reg[]="$t: $v";}
	}
	$cukisx=json_decode(@file_get_contents(_FILE_COKIES),true); $cookies='';

	$cukis=$cukisx[$hostname];
	if(count($cukis)>0){
		foreach($cukis as $kukis =>$valkukis){
			$cookies.="$kukis=$valkukis; ";
		}
		$cookies=substr($cookies,0,strlen($cookies)-2);
		$header_reg[]="Cookie: $cookies";
	}
	
	curl_setopt($post,CURLOPT_HTTPHEADER, $header_reg);
	$result = curl_exec($post);
    curl_close($post);
	
	$delimiter = "\r\n\r\n";
	
	while ( preg_match('#^HTTP/[0-9\\.]+\s+100\s+Continue#i',$result) ) {
		$tmp = explode($delimiter,$result,2); 
		$result = $tmp[1];
	}
	$x=explode($delimiter , $result, 2);
	
	
	$get_header= baca_header($x[0]); $isi=$x[1];
	$cookies=$get_header['set-cookie'];
	if(count($cookies) > 0){
		set_coki($cookies,$cukisx,$hostname);
	}
	if (array_key_exists("location",$get_header)){
		$ew=array();
		$xx=kirim_data($get_header['location'],'',$ew);
		return $xx;
		exit();
	}
	if($get_header['content-encoding']=='gzip'){
		$isi=un_gzip($isi);
	}
	$xx['header']=$get_header; $xx['isi']=$isi;
	return $xx;
}
function un_gzip($isi){
	@mkdir(SISTEM_TMP."/tmp");
	$filename=SISTEM_TMP."/tmp/00-".microtime(true).".gz";
	$file=fopen($filename,"w"); fwrite($file,$isi); fclose($file);
	$zp = gzopen($filename, "r");
	$isi = gzread($zp, 900000);
	gzclose($zp);
	unlink($filename);
	return $isi;
}
function set_coki($cookies,$set_cokis,$hostname){
	foreach($cookies as $c){
		$r=explode("; ",$c);
		$x=array(); $n=0;
		foreach($r as $t){
			$n++;
			$u=explode("=",$t,2);
			if($n==1){
				$name=urldecode($u[0]);
				$val=urldecode($u[1]);
			}else{
				if($u[0]=='expires'){
					$tgl=$u[1];
					if(strpos($tgl,",")){
						$j=explode(", ",$tgl);
						$tgl=$j[1];
					}
					$expires=$tgl; $expired=strtotime($expires);
				}
				if($u[0]=='path'){$path=$u[1];}
				if($u[0]=='domain'){$domain=$u[1];}
			}
		}
		$set_cokis[$hostname][$name]=$val;
	}

	$x=fopen(_FILE_COKIES,"w"); fwrite($x,json_encode($set_cokis)); fclose($x);
}
function baca_header($x){
	$h=preg_split('/\r\n|[\r\n]/', $x);
	$header=array();
	$cookies=array();
	foreach ($h as $v) {
		$d=explode(": ",$v);
		$dp=strtolower($d[0]); $bl=$d[1];
		if($dp=='set-cookie'){
			$cookies[]=trim($bl);
		}else{
			$header[$dp]=trim($bl);
		}
	}
	$header['set-cookie']=$cookies;
	return $header;
}
function encrypt($string,$key='') {
  if($key==''){$key='abc';}
  $result = '';
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)+ord($keychar));
    $result.=$char;
  }
  return base64_encode($result);
}
function tulis_data($filename,$isi){
	$dir=dirname($filename);
	mkdirs($dir);
	$x=fopen($filename,"w"); fwrite($x,$isi); fclose($x);
}
function decrypt($string,$key='') {
  if($key==''){$key='abc';}
  $result = '';
  $string = base64_decode($string);
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }
  return $result;
}
function kompres($isi){
	$isi=str_replace("\r"," ",$isi);
	$isi=str_replace("\n"," ",$isi);
	$isi=str_replace("\t"," ",$isi);
	$isi=str_replace("\r\n"," ",$isi);
	for ($i = 1; $i <= 100; $i++) {
		$isi=str_replace(" <","<",$isi);
		$isi=str_replace("> ",">",$isi);
		$isi=str_replace("> <","><",$isi);
		$isi=str_replace("  "," ",$isi);
	}
	return $isi;
}
function mkdirs ($dir){
	if (! is_dir ($dir)){
		if (! mkdirs (dirname ($dir))) {
			return false;
		}
		if (! mkdir ($dir, 0777)) {
			return false;
		}
	}
	return true;
} 
function cek_dbf($filename,$kode_pt='abc'){
	$tmp=APP_PATH."/app/mhs.tmp";
	if(!file_exists($tmp)){
		die("<strong>Error : </strong> file $tmp not Exists");
	}
	$tmp2=SISTEM_TMP."/$kode_pt/MHS.DBF";
	mkdirs(dirname($tmp2));
	if(!file_exists($tmp2)){
		copy($tmp,$tmp2);
	}
	$tmp=APP_PATH."/app/dosen.tmp";
	if(!file_exists($tmp)){
		die("<strong>Error : </strong>file $tmp not Exists");
	}
	$tmp2=SISTEM_TMP."/$kode_pt/DOSEN.DBF";
	if(!file_exists($tmp2)){
		copy($tmp,$tmp2);
	}
	$tmp=APP_PATH."/app/matkul.tmp";
	if(!file_exists($tmp)){
		die("<strong>Error : </strong>file $tmp not Exists");
	}
	$tmp2=SISTEM_TMP."/$kode_pt/MATKUL.DBF";
	if(!file_exists($tmp2)){
		copy($tmp,$tmp2);
	}
	
	$f=SISTEM_TMP."/dbf_path.txt";
	if(!file_exists($f)){
		die("<strong>Error : </strong>file DBF not Exists");
	}
	$ff=file_get_contents($f);
	$files=list_folder($ff);
	$s=0;
	foreach($files as $f){
		$fi=explode("/",strtolower($f));
		$file=end($fi);
		if($file==strtolower($filename)){
			$fx=$f;
			$s=1;
		}
	}
	if($s==0){
		die("<strong>Error : </strong>file DBF not Exists");
	}
	$r=array();
	$r[]=str_replace("/","\\",$ff);
	$r[]=str_replace("/","\\",$fx);
	return $r;
}
$prodi=array();
if(file_exists(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt")){
	$prodi=json_decode(file_get_contents(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt"),true);
}
define("PRODI",serialize($prodi));
function list_prodi(){
	$t=json_decode(file_get_contents(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt"),true);
	foreach($t as $k=>$r){
		if(strlen($k)==5){
			$kode=trim($r['id_jur']);
			$nama=jenjang($r['id_jenj_didik'])." ". trim($r['nm_lemb']);
			$sl="";
			if($kode==$_GET['prodi']){$sl="selected";}
			echo "<option value=\"$kode\" $sl>$nama</option>";
		}	
	}
}
function list_smtr($smt){
	$t=json_decode(file_get_contents(SISTEM_TMP."/ref_smt.txt"),true);
	foreach($t as $k=>$r){
		$sl=''; if($r['id_smt']==$smt){$sl="selected";}
		echo "<option value=\"$r[id_smt]\" $sl>$r[nm_smt]</option>";
	}
}
$list_thn=array(); $list_smtr=array();
if(file_exists(SISTEM_TMP."/ref_smt.txt")){
	$t=json_decode(file_get_contents(SISTEM_TMP."/ref_smt.txt"),true);
	foreach($t as $k=>$r){
		$list_thn[]=$r['id_thn_ajaran'];
		$list_smtr[]=$r['id_smt'];
	}
}
define("LIST_THN",serialize($list_thn));
define("LIST_SMTR",serialize($list_smtr));
if($_POST['username']){
	@session_destroy();
	@unlink(_FILE_COKIES);
	@unlink(_HEADER);
	@unlink(_FOOTER);
	@unlink(_MENU);
	@unlink(SISTEM_TMP."/data-pt.txt");
	$param=array('u'=>$_POST['username'],'p'=>$_POST['pass'],'url'=>$_POST['url']);
	setcookie('host_idx', $_POST['host'], time() + (86400 * 30), "/");
	setcookie('mode_idx', $_POST['mode'], time() + (86400 * 30), "/");
	header("location:?connect_id=".urlencode(encrypt(serialize($param))));
	exit;
}
if($_GET['connect_id']){
	@unlink(_FILE_COKIES);
	$dt=array();
	$dt=unserialize(decrypt($_GET['connect_id']));
	$username=$dt['u']; $pass=$dt['p']; $url=$dt['url'];
	if(!$username){exit;}
	$client = new nusoap_client($url, true);
	$proxy = $client->getProxy();
	
	$x=$proxy->endpoint;
	if(!$x){
		header("location:?err=1&desc=".urlencode("url salah / tidak bisa koneksi dg server"));
	}
	$token = $proxy->GetToken($username, $pass);
	if(substr($token,0,5)=='ERROR'){
		header("location:?err=1&desc=".urlencode("user name/ password salah"));
		exit;
	}
	$_SESSION['token_ws']=$token;
	
	$x=$proxy->GetRecord($token,'satuan_pendidikan',get_id_sp($username),'','','');
	$res=$x['result'];
	$_SESSION['data_pt']=$res;
	$x=$proxy->GetRecordset($token,'semester',"a_periode_aktif='1'",'','','');
	$res=$x['result']; $smt=array();
	foreach($res as $k=>$v){
		$smt[]=$v['id_smt'];
	}
	

	ksort($smt);
	tulis_data(SISTEM_TMP."/ref_smt.txt",json_encode($res));
	$smtr=end($smt);
	$c=parse_url($url);
	$alamat=$c['scheme']."://".$c['host'];
	if((int)$c['port'] > 0){
		$alamat.=":".$c['port'];
	}
	$alamat1=$alamat."/login";
	$_SESSION['server_url']=$alamat;
	$_SESSION['server_ws']=$alamat."/ws/".$_COOKIE['mode_idx']."?wsdl";
	$xx=array();
	$data=array('act'=>'login','username'=>$username,'password'=>$pass,'id_smt'=>$smtr);
	$xx=kirim_data($alamat1,'post',$data);
	$isi=$xx['isi'];
	
	if(strpos($isi,'Silahkan Pilih Hak Akses')){
		$p1=cari('/loginas/setlogin/','"',$isi);
		$xx=kirim_data($alamat."/loginas/setlogin/$p1",'post',$data);
		$isi=$xx['isi'];
	}
	$pos=strpos($isi,'<body');
	$header=substr($isi,0,$pos);
	$rep="\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".PATH."/app/flexigrid.css\">\r\n<script type=\"text/javascript\" src=\"".PATH."/app/flexigrid.js\"></script>\r\n";
	$header.="\r\n<body>\r\n<div class=\"main-wrapper\" style=\"width:100%\">\r\n\r\n";
	$header=str_replace("<title>","$rep <title>",$header);
	$footer="\r\n\r\n</div>\r\n<div class=\"footer-container\"><div>\r\n".cari('<div class="footer-container">','</html>',$isi)."</html>";
	$menu='<div style="padding:20px"><div style="height:30px;position:static"><div id="wpadminbar" class="navbar">'.cari('<div id="wpadminbar" class="navbar">','<script type="text/javascript">',$isi)."</div>\r\n\r\n";
	
	tulis_data(_HEADER,$header);
	tulis_data(_FOOTER,$footer);
	tulis_data(_MENU,$menu);
	$_SESSION['id_sp_feeder']='1';
	header("location:".PATH."/index.php");
	
}
function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}
if($_GET['logout']){
	@session_destroy();
	if((int)$_GET['logout']==3){setcookie('modif', '1', time() + (86400 * 30), "/");}
	header("location:index.php");
	exit;
}
if((int)$_COOKIE['modif']==1){
	$_GET['err']=1;
	$_GET['desc']='Error, Aplikasi Feeder telah berubah dari setting semula ...';
	setcookie('modif', '', time() - (86400 * 30), "/");
}
if($_POST['dir']){
	$dir=urldecode($_POST['dir']);
	if( is_dir( $dir) ) {
		$files = scandir( $dir);
		natcasesort($files);
		if( count($files) > 2 ) {
			echo "<ul class=\"jqueryFileTree navigation treeview\" style=\"display: none;\">";
			foreach( $files as $file ) {
				if( file_exists( $dir . $file) && $file != '.' && $file != '..' && is_dir( $dir . $file) ) {
					$t="_".strtolower($file);
					if(!strpos($t,'recycle')){
						echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
					}
				}
			}
			echo "</ul>";	
		}
	}
	$dirx=str_replace("/","\\\\",$dir);
	$s='$';
	echo "<script>document.getElementById('ttitles').innerHTML='$dirx'; $s('#list_file').load('?getfiles=".urlencode($dir)."&f=1');</script>";
	exit;
}
function jumlah($file){
	$table = new XBaseTable($file);
	$table->open();
	return number_format($table->recordCount,0,'','.')." Record";
}
if($_GET['f']){
	require_once APP_PATH."/app/xbase/Column.class.php";
	require_once APP_PATH."/app/xbase/Record.class.php";
	require_once APP_PATH."/app/xbase/Table.class.php";

	$dir=$_GET['getfiles'];
	$files=list_folder($dir);
	echo '<table width="98%" class="table table-striped table-condensed" border="0" cellspacing="0" cellpadding="0"><thead><tr class="ab"><th colspan="2">Filename</th><th width="27%">Filesize</th><th width="21%">RecordNum</th></tr></thead>';
	foreach( $files as $file ) {
		if( file_exists($file) && $file != '.' && $file != '..' && is_dir( $file) ) {
			$t="_".strtolower($file);
			$c=explode("/",$file); $filex=end($c);
			if(!strpos($t,'recycle')){
				echo "<tr><td width=\"1%\"><div class=\"ikon directory\"></td><td width=\"51%\" style=\"cursor:pointer\" onDblClick=\"bukafolder('".urlencode($file."/")."')\">$filex</td> <td>&nbsp;</td><td>&nbsp;</td></tr>";
			}
		}
	}
	$f=array();
	foreach( $files as $file ) {
		if( file_exists($file) && $file != '.' && $file != '..' && !is_dir( $file) ) {
			$ext = strtolower(preg_replace('/^.*\./', '', $file));
			$c=explode("/",$file); $filex=end($c);
			$ukuran=formatSizeUnits(filesize($file));
			$jml='';
			if($ext=='dbf'){
				$jml=jumlah($file);
			}
			$f[]=array('ext'=>$ext,'filex'=>$filex,'ukuran'=>$ukuran,'jml'=>$jml,'lokasi'=>$file);
			
		}
	}
	aasort($f,'ext');
	foreach($f as $r){
		$ext=$r['ext']; $filex=$r['filex']; $ukuran=$r['ukuran']; $jml=$r['jml']; $lokasi=$r['lokasi'];
		$ff="<a href=\"".PATH."/index.php/setting-dbf?download=".str_replace("+","%20",urlencode($lokasi))."\">$filex</a>";
		if($ext!='cdx'){
			echo "<tr><td width=\"1%\"><div class=\"ikon file ext_$ext\"></td><td width=\"51%\">$ff</td><td style=\"text-align:right\">$ukuran</td><td style=\"text-align:right\">$jml</td></tr>";
		}
	}
	echo '</table>';
	$dirx=str_replace("/","\\\\",$dir);
	echo "<script>document.getElementById('ttitles').innerHTML='$dirx'; lokasi_dbf='".urlencode

($dir)."';</script>";
	exit;
}
if($_GET['download']){
	$file=$_GET['download'];
	if(file_exists($file)){
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
		readfile($file); 
		exit;
	}
	
}
if($_GET['savedbf']){
	$cek=array('msmhs.dbf','trakd.dbf','trnlm.dbf','trlsm.dbf','trakm.dbf','tbkmk.dbf','trpid.dbf','tbbnl.dbf','tbpti.dbf','tbpst.dbf','trnlp.dbf');
	$dir=$_GET['path'];
	$files=list_folder($dir);
	$s=0; $ff=array();
	foreach($files as $f){
		$fi=explode("/",strtolower($f));
		$file=end($fi);
		$ff[]=$file;
	}
	$b=array();
	foreach($cek as $f){
		if(!in_array($f,$ff)){
			$b[]=strtoupper($f);
		}	
	}
	if(count($b)==0){
		$file=fopen(SISTEM_TMP."/dbf_path.txt","w"); fwrite($file,$dir); fclose($file);
		echo "<script>window.location=String(window.location);</script>";
	}else{
		$al='';
		foreach($b as $f){
			$al.="- $f\\n";
		}
		echo "<script>alert('Tidak ada \\n$al di direktori $dir');</script>";
	}
	exit;
}

if($_GET['load_mhs'] or $_GET['load_mhs_xls'] or $_GET['ajax_msmhs'] or $_GET['read_tmp_excel']){
	include APP_PATH."/view/msmhs.dbf.php";
	exit;
}
if($_GET['load_mk']){
	include APP_PATH."/view/tbkmk.dbf.php";
	exit;
}
if($_GET['load_aktifitas'] or $_GET['ajax_akm'] or $_GET['perbaiki'] or $_GET['load_ipk']){
	include APP_PATH."/view/trakm.dbf.php";
	exit;
}
if($_GET['load_nilai'] or $_GET['indexing']){
	include APP_PATH."/view/trnlm.dbf.php";
	exit;
}
if($_GET['load_lsm']){
	include APP_PATH."/view/trlsm.dbf.php";
	exit;
}
if($_GET['load_lsm_skripsi']){
	include APP_PATH."/view/trlsmskripsi.dbf.php";
	exit;
}
if($_GET['load_transfer']){
	include APP_PATH."/view/trnlp.dbf.php";
	exit;
}

if($_GET['init']){
	$dt=json_decode(file_get_contents(SISTEM_TMP."/data-awal.txt"),true);
	$kode_pti='';
	foreach($dt as $k=>$v){
		if(trim($v['satu'])=='Kode PT'){
			$kode_pti=trim(str_replace(": ","",$v['dua']));
		}
	}
	if($kode_pti != ''){
		global $proxy;
		$proxy=new PROXY();
		$proxy->connect();
		$filter = "npsn = '".$kode_pti."'";
		$x = $proxy->getrecords('satuan_pendidikan',$filter);
		if((int)$x['error_code'] > 0){
			die($x['error_desc']);
			exit;
		 };
		if($x['result']['id_sp']==''){
			die("User Tidak Berhak");
		}
		$_SESSION['data_pt']=$x['result'];
		header("location:".PATH."/index.php");
		exit;
	}else{
		die("Error, server tidak bisa mengidentifikasi Kode PT");
	}
	exit;
}
if($_GET['reindex']){
	$s='$';
	if($_GET['reindex']=='1'){
		echo "<script>\r\n$s('#tt_wd').text('Tunggu Sedang re-index data');\r\n$(function() ".TANDA1."
			$s( \"#detail_indexing\" ).dialog(".TANDA1."
				height: 100,
				width:'450',
				modal: true,
				title: 'Re_index Database'
			".TANDA2.");
		".TANDA2.");\r\n
		$s('#indexing_load').load('".PATH."/index.php?reindex=2');
		</script>\r\n";
		exit;
	}
	if($_GET['reindex']=='2'){
		$kode_pt=trim($_SESSION['data_pt']['npsn']);
		$id_sp=$_SESSION['data_pt']['id_sp'];
		define("FOLDER_DATA",SISTEM_TMP."/$kode_pt");
		mkdirs(FOLDER_DATA);
		$folder=cek_dbf('MSMHS.DBF',$kode_pt);
		$folder_dbf=str_replace("/","\\",SISTEM_TMP."/$kode_pt/");
		$odbc=new DBFConnect();
		$odbc->connect($folder_dbf);
		global $proxy;
		$proxy=new PROXY();
		$proxy->connect();
		index_jurusan($proxy);
		$x=$proxy->GetCountRecordsets('dosen_pt');
		$jml=(int)$x['result'];
		$per_page=400;
		$max_pages = ceil($jml / $per_page);
		for ($i = 1; $i <= $max_pages; $i++) {
			$dt=$i * $per_page;
			$min=$dt-$per_page;
			$rs=$proxy->GetRecordsets('dosen_pt','','',$per_page,$min);
			$r=$rs['result'];
			foreach($r as $k=>$v){
				$id_ptk=$v['id_ptk'];
				$id_reg_ptk=$v['id_reg_ptk'];
				$smt=$v['id_thn_ajaran'];
				if($id_reg_ptk){
					if($smt){
						$sqlx="select * from DOSEN.DBF where ID_REG_PTK='$id_reg_ptk' and SMT='$smt'";
						$r=$odbc->query($sqlx,false);
						$jml=count($r);
						if($jml==1){
							$odbc->execute("insert into DOSEN.DBF(ID_PTK,ID_REG_PTK,SMT)values('$id_ptk','$id_reg_ptk','$smt')");
						}
					}
				}
			}
		}
		$x=$proxy->GetCountRecordsets('dosen');
		$jml=(int)$x['result'];
		$max_pages = ceil($jml / $per_page);
		for ($i = 1; $i <= $max_pages; $i++) {
			$dt=$i * $per_page;
			$min=$dt-$per_page;
			$rs=$proxy->GetRecordsets('dosen','','',$per_page,$min);
			$r=$rs['result'];
			foreach($r as $k=>$v){
				$id_ptk=$v['id_ptk'];
				$nidn=trim($v['nidn']);
				if($nidn){
					$odbc->execute("update DOSEN.DBF set NIDN='$nidn' where ID_PTK='$id_ptk'");
				}				
			}
		}
		$tbl_dbf="TBBNL.DBF";
		$folder=cek_dbf($tbl_dbf,$kode_pt);
		$folder_dbf=$folder[0];
		$odbc2=new DBFConnect();
		$odbc2->connect($folder_dbf);
		$sql=$odbc2->query("select * from TBBNL.DBF where KDPTITBBNL='$kode_pt'");
		$n=array(); $m=array();
		foreach($sql as $k=>$r){
			$kdpst=$r['KDPSTTBBNL'];
			$nilai=trim($r['NLAKHTBBNL']);
			$bobot=trim($r['BOBOTTBBNL']);
			$idx="$kdpst;$nilai";
			$n[$idx]=$bobot;
		}
		$prodi=unserialize(PRODI);
		$update=array(); $insert=array();
		foreach($prodi as $k=>$v){
			if(strlen($k)==5){
				$id_sms=$v['id_sms'];
				$kode_prodi=trim($v['kode_prodi']);
				$x = $proxy->GetRecordsets('bobot_nilai',"id_sms='$id_sms'");
				$res=$x['result'];
				foreach($res as $kk=>$vv){
					$nilai=trim($vv['nilai_huruf']);
					$bobot=trim($vv['nilai_indeks']);
					$kode_nilai=$vv['kode_bobot_nilai'];
					$tgl=trim($vv['tgl_mulai_efektif']);
					$th_mulai1=(int)substr($tgl,0,4);
					$th_mulai=(int)substr(trim($prodi[$id_sms]['smt_mulai']),0,4);
					$idx="$kode_prodi;$nilai";
					$m[$idx]=$bobot;
					if(!array_key_exists($idx,$n)){
						
					}else{
						$data=array(); $t=array();
						$data['key']=array('kode_bobot_nilai'=>$kode_nilai);
						$bobot2=$n[$idx];
						if($bobot!=$bobot2){
							$t['nilai_indeks']=$bobot2;
						}
						if($th_mulai < $th_mulai1){
							$t['tgl_mulai_efektif']=$th_mulai."-08-01";
						}
						$data['data']=$t;
						$update[]=$data;
					}
				}
			}
		}
		$x = $proxy->UpdateRecordsets('bobot_nilai',$update);
		foreach($n as $k=>$v){
			if(!array_key_exists($k,$m)){
				$l=explode(";",$k);
				$kode_prodi=$l[0]; $nilai=$l[1]; $bobot=trim($v);
				$th_mulai=(int)substr(trim($prodi[$kode_prodi]['smt_mulai']),0,4);
				$id_sms=$prodi[$kode_prodi]['id_sms'];
				$c=array();
				$c['id_sms']=$id_sms;
				$c['nilai_huruf']=$nilai;
				$c['bobot_nilai_min']=0;
				$c['bobot_nilai_maks']=100;
				$c['nilai_indeks']=$bobot;
				$c['tgl_mulai_efektif']=$th_mulai."-08-01";
				$insert[]=$c;
			}
		}
		$x = $proxy->InsertRecordsets('bobot_nilai',$insert);
		echo "<script>window.location='".PATH."/index.php';</script>";
	}
	
	exit;
}
function index_jurusan($proxy){
	$jenjang=array(); $jurusan=array();
	$x=$proxy->GetRecordsets('jenjang_pendidikan');
	$res=$x['result'];
	foreach($res as $k=>$v){
		$jenjang[trim($v['id_jenj_didik'])]=trim($v['nm_jenj_didik']);
	}
	tulis_data(SISTEM_TMP."/jenjang.txt",json_encode($jenjang));
	$x=$proxy->GetRecordsets('jurusan');
	$res=$x['result'];
	foreach($res as $k=>$v){
			$jurusan[trim($v['id_jur'])]=$jenjang[trim($v['id_jenj_didik'])]." ". trim($v['nm_jur']);
	}
	tulis_data(SISTEM_TMP."/jurusan.txt",json_encode($jurusan));
}
$f=array();$f=explode("/", $_SERVER['SCRIPT_NAME']);$e=end($f);$f=explode("?",$_SERVER['REQUEST_URI']);$lx=$f[0]; 

$f=explode("/",$lx); $l=end($f);
if($e=='index.php' && $_SESSION['id_sp_feeder']){
	$x=explode("index.php",$_SERVER['REQUEST_URI']);
	$b=explode("/",$x[1]);
	if($b[1]=='nilai'){
		$id_reg_pd=$b[2];
		global $proxy;
		$proxy=new PROXY();
		$proxy->connect();
		$x=$proxy->GetRecords("mahasiswa_pt.raw","id_reg_pd='$id_reg_pd'");
		$id_pd=$x['result']['id_pd'];
		if($id_pd){
			$alamat_server=$_SESSION['server_url'];
			frame_set("$alamat_server/nilaitransfer/lst/$id_pd","Nilai Transfer");
		}
		exit;
	}
	
	$title='WebService';
	make_header();
	$l=str_replace("-dbf",".dbf",$l);
	if(file_exists(APP_PATH."/view/$l.php")){
		include APP_PATH."/view/$l.php";
	}else{
		$f=explode("/", $_SERVER['SCRIPT_NAME']); $e=end($f);
		$f=explode("/", $_SERVER['REQUEST_URI']); $e1=end($f);
		if($e=='index.php'){
			if($e1=='index.php'){
				include APP_PATH."/view/index.php";
			}else{
				echo "not found";
			}
		}
	}

	make_footer($title);
}
function generate_ip(){
	global $proxy;
	$proxy=new PROXY();
	$proxy->connect(); 
	if($_POST['simpan']){
		$regpd=$_POST['id_reg_pd'];
		$sksema=$_POST['sksem'];
		$ipsa=$_POST['ips'];
		$ipka=$_POST['ipk'];
		$skstta=$_POST['skstt'];
		$nima=$_POST['nim'];
		$namaa=$_POST['nama'];
		$angka=$_POST['angk'];
		$insert=array(); $update=array();
		$tr=array(); $no=0;
		foreach($regpd as $id_reg_pd){
			$no++;
			$c=array(); $dt=array();
			$sksem=$a[$id_reg_pd];

			$ips=$ipsa[$id_reg_pd];
			$ipk=$ipka[$id_reg_pd];
			$skstt=$skstta[$id_reg_pd];
			$nim=$nima[$id_reg_pd];
			$nama=$namaa[$id_reg_pd];
			$angk=$angka[$id_reg_pd];
			$c['ips']=$ips;
			$c['sks_smt']=(int)$sksem;
			$c['ipk']=$ipk;
			$c['sks_total']=(int)$skstt;
			$c['id_stat_mhs']='A';
			$c['id_reg_pd']=$id_reg_pd;
			$c['id_smt']=$_GET['tahun'];
			$dt['key']=array('id_smt'=>$_GET['tahun'],'id_reg_pd'=>$id_reg_pd);
			$dt['data']=$c;
			$insert[]=$c;
			$update[]=$dt;
			$tr[]= BuatTR($id_reg_pd,$no,$sksem,$ips,$skstt,$ipk,$nama,$nim,$angk);
		}
		$x=$proxy->InsertRecordsets('kuliah_mahasiswa',$insert);
		$x=$proxy->UpdateRecordsets('kuliah_mahasiswa',$update);
		$t=implode("\r\n",$tr);
		buatTabelGen($t,'<strong>Perubahan telah disimpan di Feeder</strong></br>');
	}else{
		$prodi=unserialize(PRODI);
		$id_sms=$prodi[$_GET['prodi']]['id_sms'];
		$smt=$_GET['tahun'];
		$f="p.id_sms='$id_sms' and p.id_smt='$smt'";
		$x=$proxy->GetRecordsets("kelas_kuliah.raw",$f);
		$res=$x['result']; $idkls=array();
		$no=0; $i=0;
		foreach($res as $k=>$r){
			$no++; $id_kls=$r['id_kls'];
			if($no < 3){
				$idkls[$i][]=$id_kls;
			}else{
				$i++;
				$no=0;
				$g=$idkls[$i];
				if(!is_array($g)){
						$idkls[$i][]=$id_kls;
				}
			}
			$cek++;
		}
		$nimarray=array(); $reg_pd=array();
		foreach($idkls as $k=>$r){
			$idkls_array=array();
			foreach($r as $id_kls){
				$idkls_array[]="'$id_kls'";
			}
			$sqlx="select p.* from nilai_smt_mhs p where p.soft_delete=0  and (p.id_kls in(".implode(",",$idkls_array).")) ";
			$x=$proxy->GetRecordsets("nilai.raw","p.id_kls in(".implode(",",$idkls_array).")");
			$res=$x['result'];
			foreach($res as $kk=>$rr){
				$id_reg_pd=$rr['id_reg_pd'];
				$nimarray[$id_reg_pd]=$id_reg_pd;
			}
		}
		foreach($nimarray as $id_reg_pd=>$r){
			$reg_pd[]=$id_reg_pd;
		}
		$jml_mhs=count($reg_pd);
		if( $jml_mhs>0){
			tulis_data(SISTEM_TMP."/reg_pd_".$_GET[prodi].$_GET[tahun].".txt",json_encode($reg_pd));
			buatTabelGen();
			echo "<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\"><span id=\"dialog_txt\">Connect Server WS</span></div><div id=\"p_bar\"><div id=\"p_bar4\"><div id=\"p_bar5\">&nbsp;</div></div><div id=\"p_bar3\">..</div><div id=\"p_bar2\"><img src=\"".PATH."/app/images/vista.gif\"></div></div><div id=\"p_bara\"><div id=\"p_bar4a\"><div id=\"p_bar5a\">&nbsp;</div></div><div id=\"p_bar3a\">....</div><div id=\"p_bar2a\"><img src=\"".PATH."/app/images/vista.gif\"></div></div></div>";
			$s='$';
			echo "\r\n<script>\r\nvar mhstotal=$jml_mhs;"; if($jml_mhs > 0){echo "\r\n$s(function() {\r\n$s( \"#detail_mhs\" ).dialog({\r\nheight: 150,\r\nwidth:'50%',\r\nmodal: true,\r\ntitle: 'Wait'\r\n});\r\n}); ";} echo "\r\nvar ke=0; var tampil=true;\r\nfunction show_text(txt,t){\r\nsetTimeout(\"showext('\"+txt+\"')\",t);\r\n}\r\nfunction showext(tks){\r\nke=ke+1;\r\nvar xt=((ke+1) / mhstotal) * 100;\r\ndocument.getElementById('p_bar5a').style.width=xt+'%';\r\nif(tampil==true){\r\n$s('#p_bar3a').html('Proses NIM <strong>'+ tks +'</strong> ('+ke+')');\r\n}\r\n}\r\nfunction tutup_dialog(){\r\n$s( \"#detail_mhs\" ).dialog(\"close\");\r\n}\r\n</script>\r\n\r\n\r\n<script>\r\nif(mhstotal >0){\r\n$s('#balikan_nilai').load('".PATH."/index.php?load_ipk=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');}\r\n</script>";
		}else{
			echo "Tidak ada KRS mahasiswa dalam kelas di tahun ajaran $_GET[tahun]";
		}
	}
}
function BuatTR($id_reg_pd,$no,$sksem,$ips,$skstt,$ipk,$nama,$nim,$angk){
	global $alamat_server;
	$hidd="<input type=\"hidden\" name=\"sksem".'['.$id_reg_pd."]\" value=\"$sksem\">";
	$hidd.="<input type=\"hidden\" name=\"ips".'['.$id_reg_pd."]\" value=\"$ips\">";
	$hidd.="<input type=\"hidden\" name=\"skstt".'['.$id_reg_pd."]\" value=\"$skstt\">";
	$hidd.="<input type=\"hidden\" name=\"ipk".'['.$id_reg_pd."]\" value=\"$ipk\">";
	$hidd.="<input type=\"hidden\" name=\"nama".'['.$id_reg_pd."]\" value=\"$nama\">";
	$hidd.="<input type=\"hidden\" name=\"nim".'['.$id_reg_pd."]\" value=\"$nim\">";
	$hidd.="<input type=\"hidden\" name=\"angk".'['.$id_reg_pd."]\" value=\"$angk\">";
	$dtm="<tr><td>$no</td><td><a target=\"_blank\" href=\"$alamat_server/kuliahmhs/detail/id_reg_pd:$id_reg_pd"."___id_smt:$_GET[tahun]\">$nim</a></td><td><a target=\"_blank\" href=\"$alamat_server/kuliahmhs/detail/id_reg_pd:$id_reg_pd"."___id_smt:$_GET[tahun]\">$nama</a></td><td>$angk</td><td>$sksem</td><td>$ips</td><td>$skstt</td><td>$ipk</td><td><input type=\"checkbox\" name=\"".'id_reg_pd[]'."\" value=\"$id_reg_pd\" checked>$hidd</td></tr>";
	return $dtm;
}
function buatTabelGen($dt='',$ket=''){
	echo "\r\n<style>\r\n.pooter td{background-color:#999999!important; font-weight:bold!important;}\r\n#p_bar5{width:99%; background-color:#cccccc; right:0px ;position:absolute; height:22px}\r\n#p_bar4{width:97%; position:absolute; height:30px; text-align:right}\r\n#p_bar3{width:96%; position:absolute; text-align:center}\r\n#p_bar2{background-color:#ffffff; height:24px; width:100%}\r\n#p_bar{border:1px solid #cccccc; width:100%; height:25px}\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}\r\n</style>\r\n<style>#p_bar5a{width:0%; background-color:#cccccc; right:0px ;position:absolute; height:22px}#p_bar4a{width:97%; position:absolute; height:30px; text-align:right}#p_bar3a{width:96%; position:absolute; text-align:center}#p_bar2a{background-color:#ffffff; height:24px; width:100%}#p_bara{border:1px solid #cccccc; width:100%; height:25px}</style>\r\n$ket\r\nPenghitungan Nilai IPS &amp; IPK berdasarkan pada data yang telah disimpan di Feeder\r\n<form name=\"form1\" method=\"post\" action=\"\"><div style=\"width:700px\"><table width=\"100%\" class=\"table table-striped table-condensed\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><thead> <tr><th width=\"3%\">NO</th><th width=\"14%\">NIM</th><th width=\"38%\">NAMA</th><th width=\"16%\">Angkatan</th><th width=\"7%\">SKSSem</th><th width=\"7%\">IPS</th><th width=\"7%\">SKS TT </th><th width=\"8%\">IPK</th><th width=\"1%\"></th></tr></thead><tbody id=\"tbodi\">$dt</tbody></table><div align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"Simpan\"></div></div><input type=\"hidden\" name=\"simpan\" value=\"1\"></form>";
}
?>