<?php
$time_start = microtime_float();
global $kode_pt;
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/nilai");
mkdirs(FOLDER_DATA);
$filedosen=dirname(FOLDER_DATA)."/idptk.txt";
define("FILE_DOSEN",$filedosen);
$file_mhs=FOLDER_DATA."/".$_GET['prodi']."/".$_GET['tahun'];
define("FILE_MHS",$file_mhs);
$tbl_dbf="TRNLM.DBF";
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

if($_GET['excel']){
	$prodi=json_decode(file_get_contents(SISTEM_TMP."/".trim($_SESSION['data_pt']['npsn'])."/prodi.txt"),true);
	$idx=microtime_float();
	$tit='Upload Kelas Kuliah &amp; Nilai';
	if((int)$_GET['krs']>0){
		$tit='Upload KRS';
	}
	make_heading($tit,'Mengimpor data dari EXECL');
	echo "\r\n<style>\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}</style>\r\n<div id=\"balikan_mhs\"></div>";
	echo "\r\n<script src=\"".PATH."/app/fn.js\"></script>\r\n";
	echo "<div id=\"balikan_mhs\"></div>\r\n";
	echo "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\" name=\"formexcel\">
	  Pilih File
	  <input id=\"filexls\" onchange=\"ValidateSingleInput(this);\" type=\"file\" name=\"file\">
	  <button type=\"submit\" class=\"btn btn-small btn-primary\"><i class=\"icon-plus icon-white\"></i>Upload</button>
	 <span style=\"float:right\"><a href=\"?download=nilai.xlsx\" class=\"btn btn-small btn-primary\">Download <strong>Contoh</strong> File Nilai</a></span>
	 <input type=\"hidden\" name=\"uploadfile\" value=\"mhs\">
	</form>";
	$file_upload=$_FILES['file'];
	$file_name=$file_upload['name'];
	$file_tmp=$file_upload['tmp_name'];
	$dt=array();
	if($file_tmp){
		require_once (APP_PATH."/app/excel/PHPExcel/IOFactory.php");
		try {
			$inputFileType = PHPExcel_IOFactory::identify($file_tmp);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($file_tmp);
		} catch(Exception $e) {
			die('Error loading file "'.$file_name.'": '.$e->getMessage());
		}
		$isi=array();
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();
		for ($row = 1; $row <= $highestRow; $row++){ 
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
			$isi[]=$rowData[0];
		}
		$c='SMTR,Kode Prodi,Kelas,Kode MK,Nama MK,NIM,Nama,Nilai';
		$x=explode(",",$c); $cl=array();
		foreach($x as $k=>$r){
			$cl[$r]=$r;
		}
		$dt=Excel2Array($isi,$cl,false); $data=array(); $nilai_array=array(); $bobot=array();
		$kodemkarray=array(); $mk=array(); $idmkarray=array(); $smtrarray=array(); $klsarray=array();
		$id_smsarray=array(); $error=array();
		foreach($dt as $k=>$r){
			$kodemk=trim($r['Kode MK']);
			$smtr=trim($r['SMTR']); $kelas=trim($r['Kelas']);
			$idx=$smtr.$kelas.$r['Kode MK'];
			$id_sms=$prodi[$r['Kode Prodi']]['id_sms'];
			$data[$id_sms]['idx'][$idx]=$idx;
			$nm_pst=$prodi[$r['Kode Prodi']]['nm_lemb'];
			$data[$id_sms]['nama_pst']=$nm_pst;
			$data[$id_sms]['kdpst']=$r['Kode Prodi'];
			$data[$id_sms][$idx]['Kelas']=$r['Kelas'];
			$data[$id_sms][$idx]['Kode MK']=$kodemk;
			$data[$id_sms][$idx]['smt']=$smtr;
			$data[$id_sms][$idx]['data'][trim($r['NIM'])]=array('nama'=>$r['Nama'],'n'=>$r['Nilai']);
			$nilai_array[trim($r['Nilai'])]="'".trim($r['Nilai'])."'";
			if($kodemk!=''){$kodemkarray[$kodemk]="'$kodemk'";}
			if($smtr!=''){$smtrarray[$smtr]="'$smtr'";} if($kelas!=''){$klsarray[$kelas]="'$kelas'";}
			if($id_sms!=''){$id_smsarray[$id_sms]="'$id_sms'";}
		}
		if(count($kodemkarray) >0){
			$x = $proxy->GetRecordsets('mata_kuliah',"trim(kode_mk) in(".implode(",",$kodemkarray).")");
			$res=$x['result'];
			foreach($res as $k=>$r){
				$mk[$r['id_sms']][trim($r['kode_mk'])]=$r['id_mk'];
				$mk[$r['id_sms']]['sks'][trim($r['kode_mk'])]=array('mk'=>$r['sks_mk'],'tm'=>$r['sks_tm']);
				$mk[$r['id_sms']][trim($r['id_mk'])]=$r['kode_mk'];
				$mk[$r['id_sms']]['namamk'][$r['id_mk']]=$r['nm_mk'];
				$idmkarray[$r['id_mk']]="'$r[id_mk]'";
			}
		}
		if(count($idmkarray) >0){
			$filter="p.id_sms in(".implode(",",$id_smsarray).") and p.id_mk in(".implode(",",$idmkarray).") and p.nm_kls in(".implode(",",$klsarray).") and p.id_smt in(".implode(",",$smtrarray).")";
			$x = $proxy->GetRecordsets('kelas_kuliah.raw',$filter);
			$res=$x['result'];
			foreach($res as $k=>$r){
				$idx=trim($r[id_smt]).trim($r[nm_kls]).$mk[$r['id_sms']][trim($r['id_mk'])];
				$data[$id_sms][$idx]['id_kls']=$r['id_kls'];
			}
		}
		
		if(count($nilai_array) >0){
			$x = $proxy->GetRecordsets('bobot_nilai',"trim(nilai_huruf) in(".implode(",",$nilai_array).")");
			$res=$x['result'];
			foreach($res as $k=>$r){
				$bobot[trim($r['nilai_huruf'])]=$r['nilai_indeks'];
			}
		}
		foreach($data as $id_sms=>$rs){
			$idpdarray=array();
			$update=array(); $insertkls=array(); $cek_nim=array();
			$idxarray=$rs['idx'];
			$kdpst=$rs['kdpst'];
			$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$kdpst.txt";
			if(file_exists($file_id_pd)){
				$idpdarray=json_decode(file_get_contents($file_id_pd),true);
			}
			foreach($idxarray as $idx){
				$arr=$rs[$idx];
				$dt=$arr['data'];
				$kelas=$arr['Kelas'];
				$kodemk=$arr['Kode MK'];
				$id_mk=$mk[$id_sms][trim($kodemk)];
				$smt=$data[$id_sms][$idx]['smt'];
				$sks_mk=$mk[$id_sms]['sks'][trim($kodemk)]['mk'];
				$sks_tm=$mk[$id_sms]['sks'][trim($kodemk)]['tm'];
				
				$id_kls=$rs[$idx]['id_kls'];
				if($id_kls==''){
					$c=array();
					$c['id_sms']=$id_sms;
					$c['id_smt']=$smt;
					$c['id_mk']=$id_mk;
					$c['nm_kls']=$kelas;
					$c['sks_mk']=$sks_mk;
					$c['sks_tm']=$sks_tm;
					$c['a_selenggara_pditt']=0;
					$c['kuota_pditt']=0;
					$c['a_pengguna_pditt']=0;
					$insertkls[$idx]=$c;
				}
				foreach($dt as $nim=>$n){
					$id_reg_pd=$idpdarray[$nim];
					if($id_reg_pd==''){
						$cek_nim[$nim]="'$nim'";
					}
				}
			}
			if(count($insertkls) >0){
				$insert=array(); $IndexKls=array();
				foreach($insertkls as $idx=>$r){
					$IndexKls[]=$idx;
					$insert[]=$r;
				}
				$x = $proxy->InsertRecordsets('kelas_kuliah',$insert);
				$res=$x['result'];
				foreach($res as $k=>$r){
					$id_kls=$r['id_kls'];
					$idx=$IndexKls[$k];
					$data[$id_sms][$idx]['id_kls']=$r['id_kls'];
				}
			}
			if(count($cek_nim)>0){
				$f="trim(nipd) in(".implode(",",$cek_nim).")";
				$x = $proxy->GetRecordsets('mahasiswa_pt',$f);
				$res=$x['result'];
				if(count($res) >0){
					foreach($res as $k=>$r){
						$idpdarray[trim($r['nipd'])]=$r['id_reg_pd'];
					}
				}
			}
			$nim_index=array();
			tulis_data($file_id_pd,json_encode($idpdarray));
			$insert=array(); $update=array();
			foreach($idxarray as $idx){
				$dt=$rs[$idx]['data'];
				$kodemk=trim($rs[$idx]['Kode MK']);
				$id_kls=$rs[$idx]['id_kls'];
				$data[$id_sms][$idx]['id_mk']=$mk[$id_sms][$kodemk];
				foreach($dt as $nim=>$n){
					$id_reg_pd=$idpdarray[$nim];
					$bobotn=$bobot[trim($n['n'])];
					$data[$id_sms][$idx]['data'][$nim]['id_reg_pd']=$id_reg_pd;
					$data[$id_sms][$idx]['data'][$nim]['bobot']=$bobotn;
					if($id_kls!='' && $id_reg_pd!=''){
						$c=array(); $cdata=array();
						$cdata['key']=array('id_kls'=>$id_kls,'id_reg_pd'=>$id_reg_pd);
						$c['id_kls']=$id_kls;
						$c['id_reg_pd']=$id_reg_pd;
						$c['asal_data']='9';
						if((int)$_GET['krs']==0){
							$c['nilai_huruf']=trim($data[$id_sms][$idx]['data'][$nim]['n']);
							$c['nilai_indeks']=$data[$id_sms][$idx]['data'][$nim]['bobot'];
						}
						$cdata['data']=$c;
						$insert[]=$c;
						$update[]=$cdata;
					}
					if($id_kls==''){
						$data[$id_sms][$idx]['data'][$nim]['error'][]='Kode MK Tidak terdaftar';
					}
					if($id_reg_pd==''){
						$data[$id_sms][$idx]['data'][$nim]['error'][]='NIM Tidak terdaftar';
					}
				}
				if(count($insert) >0){
					$x = $proxy->InsertRecordsets('nilai',$insert);
					$res=$x['result'];
					foreach($res as $k=>$r){
						$err_code=(int)$r['error_code'];
						$error_desc=$r['error_desc'];
						$id_pd=$r['id_reg_pd'];
						$id_kls=$r['id_kls'];
						if($err_code >0 && $id_pd!=''){
							$error[$id_kls][$id_pd]=$error_desc;
						}
					}
					if((int)$_GET['krs']==0){
						$x = $proxy->UpdateRecordsets('nilai',$update);
						$res=$x['result'];
						foreach($res as $k=>$r){
							$err_code=(int)$r['error_code'];
							$error_desc=$r['error_desc'];
							$id_pd=$r['id_reg_pd'];
							$id_kls=$r['id_kls'];
							if($err_code >0 && $id_pd!=''){
								$error[$id_kls][$id_pd]=$error_desc;
							}
						}
					}
				}
			}
		}
		$echo="";
		$server_url=$_SESSION['server_url'];
		foreach($data as $id_sms=>$rs){
			$idxarray=$rs['idx'];
			$nmpst=$prodi[$id_sms]['nm_lemb'];
			if($nmpst==''){$nmpst='????????';}
			$echo.= "<ul><li><strong>Prodi : $nmpst</strong><ol>";
			foreach($idxarray as $idx){
				$dt=$rs[$idx];
				$rx=$dt['data'];
				$namamk=$mk[$id_sms]['namamk'][$dt['id_mk']];
				$namamk="<a target=\"_blank\" href=\"$server_url/matakuliah/detail/$dt[id_mk]\">$namamk</a>";
				if($dt['id_mk']==''){
					$namamk="<span class=\"merah\"><span>Kode MK Tidak terdaftar</span></span>";
				}
				$kelas=$dt['Kelas']; $id_kls=$dt['id_kls'];
				if($id_kls!=''){
					$kelas="<a target=\"_blank\" href=\"$server_url/inputnilai/detail/$id_kls\">$kelas</a>";
				}
				$echo.= "<li>";
				$echo.= "<table><tr><td>Kelas</td><td>:</td><td>$kelas</td></tr>";
				$echo.= "<tr><td>Kode MK</td><td>:</td><td>".$dt['Kode MK']."</td></tr>";
				$echo.= "<tr><td>Nama MK</td><td>:</td><td>$namamk</td></tr></table>";
				$echo.= "<table class=\"table table-striped table-condensed\"><thead><tr>";
				$echo.="<th width=\"2%\">No</th><th width=\"17%\">NIM</th><th width=\"32%\">Nama</th>";
				if((int)$_GET['krs']==0){
					$echo.= "<th width=\"7%\">Nilai</th><th width=\"6%\">Bobot</th>";
				}
				$echo.="<th width=\"36%\">Keterangan</th></tr></thead>";
				$no=0;
				foreach($rx as $nim =>$r){
					$err=$r['error']; $id_reg_pd=$r['id_reg_pd'];
					$err2=$error[$id_kls][$id_reg_pd];
					$no++; $ket='Sukses'; $class='';
					if(is_array($err)){
						$ket=implode(",",$err);
						$class=' class="merah" ';
					}
					if($err2!=''){
						$ket=$err2;
						$class=' class="merah" ';
					}
					$echo.="<tr $class><td>$no</td><td>$nim</td><td>$r[nama]</td>";
					if((int)$_GET['krs']==0){
						$echo.="<td>$r[n]</td><td>$r[bobot]</td>";
					}
					$echo.="<td>$ket</td></tr>";
				}
				
				$echo.= "</table><hr></li>";
			}
			$echo.= "</ol></li></ul>";
		}
		$nmfil='nilai_last.txt';
		if($_GET['krs']){
			$nmfil='krs_last.txt';
		}
		tulis_data(FOLDER_DATA."/$nmfil",$echo);
		echo $echo;
	}else{
		$img="excel-nilai";
		if($_GET['krs']){
			$img="excel-krs";
		}
		echo "<a href=\"?download=nilai.xlsx\"><img src=\"".PATH."/app/images/$img.jpg\"></a>";
	}
	make_footer('WebService | Import Excel (Nilai)');
	exit;
}

if($_GET['error']){
	$t=explode("*",decrypt($_GET['error']));
	
	if(count($t)==3){
		$pst=$t[0]; $smt=$t[1]; $idx=$t[2];
		$lokasi=FOLDER_DATA."/error/$pst/$smt/$idx.txt";
		$lokasi2=FOLDER_DATA."/$pst/$smt/$idx.txt";
		
		$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$pst.txt";
		$idpdarray=array();
		if(file_exists($file_id_pd)){
			$idpdarray=json_decode(file_get_contents($file_id_pd),true);
		}
		$nimhs=array();
		if(count($idpdarray)>0){
			foreach($idpdarray as $k=>$r){
				$nimhs[$r]=trim($k);
			}
		}
		if(file_exists($lokasi)){
			if(file_exists($lokasi2)){
				echo "<table width=\"989\" class=\"table table-striped table-condensed\"><thead><tr>";
				echo "<th style=\"width:120px\">NIM</th><th style=\"width:40px\">NILAI</th><th style=\"width:40px\">BOBOT</th><th>ERROR</th></tr></thead>";
				$dt=json_decode(file_get_contents($lokasi),true);
				$dt2=json_decode(file_get_contents($lokasi2),true);
				foreach($dt as $id_reg_pd =>$r){
					$nim=$nimhs[$id_reg_pd];
					$data=$dt2[$nim];
					$nilai=$data['nilai']; $bobot=$data['bobot']; $error='';
					foreach($r as $k=>$v){
						$error.="Proses: <strong>$v[proses]</strong> | err_kode : <strong>$v[code]</strong> : $v[desc]<br>";
					}
					echo "<tr><td>$nim</td><td>$nilai</td><td style=\"text-align:right\">$bobot</td><td>$error</td></tr>";
				}
				echo "</table>";
			}
		}
	}
	make_footer("ERROR Data");
	exit;
}


$prodi=unserialize(PRODI);

$id_sms=$prodi[$_GET['prodi']]['id_sms'];
$nama_prodi=trim(str_replace("'","",$prodi[$_GET['prodi']]['nm_lemb']));
$id_jenj=$prodi[$_GET['prodi']]['id_jenj_didik'];
function jml_dosen($mk,$kls){
	global $odbc; global $kode_pt; $dosen=array(); $dos=array();
	$idx="$mk-$kls";
	$file_dosen=FOLDER_DATA."/dosen-$_GET[prodi]-$_GET[tahun].txt";
	if(file_exists($file_dosen)){
		$dosen=json_decode(file_get_contents($file_dosen),true);
	}
	$sqlx="select NODOSTRAKD from TRAKD.DBF where KDPTITRAKD='$kode_pt' and KDPSTTRAKD='$_GET[prodi]' and THSMSTRAKD='$_GET[tahun]' and KDKMKTRAKD='$mk' and KELASTRAKD='$kls'";
	$sql=$odbc->query($sqlx);
	$no=0; 
	foreach($sql as $k=>$r){
		$dos[]=trim($r['NODOSTRAKD']);
	}
	$dosen[$idx]=$dos;
	tulis_data($file_dosen,json_encode($dosen));
	return $dos;
}
if($_GET['load_nilai']){
	$s='$'; $js="$s('#dialog_txt').text('Wait proses upload data');"; $keterangan=array();
	$pos=(int)$_GET['pos']; $mm=array(); $mn=array();
	
	$mm=json_decode(file_get_contents(FOLDER_DATA."/".$_GET['prodi']."-".$_GET['tahun']."-1.txt"),true);
	$mn=json_decode(file_get_contents(FOLDER_DATA."/".$_GET['prodi']."-".$_GET['tahun']."-2.txt"),true);
	$jml=count($mn);
	$pp=$pos+1;
	if(($pp) > $jml){
		echo "<script>\r\ntampil=false; \r\n$s('#p_bar6').html('&nbsp;');\r\n$s('#p_bar3').html('&nbsp;');\r\n$s('#dialog_txt').text('Close server connection');\r\nsetTimeout(\"tutup_dialog()\",2000);\r\n</script>";
		exit;
	}
	$idx=$mn[$pos];
	$r=$mm[$idx];
	$kodemk=$r['mk'];
	$nama_mk=str_replace("'","\\'",$r['nm']);
	$id_mk=$r['id_mk'];
	$kelas=trim($r['kls']);
	$sks=(int)$r['sks'];
	$filter="p.id_sms='$id_sms' and p.id_mk='$id_mk' and trim(p.nm_kls)='$kelas' and p.id_smt='$_GET[tahun]'";
	$x = $proxy->GetRecords('kelas_kuliah.raw',$filter);
	$res=$x['result'];
	$id_kls=$res['id_kls'];
	if($id_kls==''){
		$c=array();
		$c['id_sms']=$id_sms;
		$c['id_smt']=$_GET['tahun'];
		$c['id_mk']=$id_mk;
		$c['nm_kls']=$kelas;
		$c['sks_mk']=$sks;
		$c['sks_tm']=$sks;
		$c['a_selenggara_pditt']=0;
		$c['kuota_pditt']=0;
		$c['a_pengguna_pditt']=0;
		$x = $proxy->InsertRecords('kelas_kuliah',$c);
		$res=$x['result'];
		$id_kls=$res['id_kls'];
	}
	$server_url=$_SESSION['server_url'];
	$link="<a target=\"_blank\" href=\"$server_url/inputnilai/detail/$id_kls\">";
	$js.="var jml=$s('#jmhs_$idx').text();\r\n $s('#jmhs_$idx').html('$link'+jml+'</a>');\r\n";
	$link="<a target=\"_blank\" href=\"$server_url/kelaskuliah/detail/$id_kls\">$nama_mk</a>";
	$js.="$s('#nm_$idx').html('$link');\r\n";
	$link="<a target=\"_blank\" href=\"$server_url/kelaskuliah/detail/$id_kls\">$kodemk</a>";
	$js.="$s('#kode_$idx').html('$link');\r\n";
	$idpdarray=array();
	$mh=json_decode(file_get_contents(FILE_MHS."/$idx.txt"),true);
	$f=dirname(dirname(dirname(FILE_MHS)));
	$file_id_pd="$f/id_pd_$_GET[prodi].txt";
	if(file_exists($file_id_pd)){
		$idpdarray=json_decode(file_get_contents($file_id_pd),true);
	}
	$cek_nim=array();
	foreach($mh as $nim=>$v){
		$id_reg_pd=$idpdarray[$nim];
		if($id_reg_pd==''){
			$cek_nim[$nim]="'$nim'";
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
	$mhs=array();
	$insert=array(); $update=array();
	$tjs=''; $tt=0;
	foreach($mh as $k=>$v){
		$tt=$tt+40;
		$nim=trim($v['nim']);
		$tjs.="show_text('$nim',$tt);\r\n";
		$nilai=$v['nilai'];
		$bobot=$v['bobot'];
		$id_reg_pd=$idpdarray[$nim];
		if($id_reg_pd!=''){
			$c=array(); $data=array();
			$data['key']=array('id_kls'=>$id_kls,'id_reg_pd'=>$id_reg_pd);
			$c['id_kls']=$id_kls;
			$c['id_reg_pd']=$id_reg_pd;
			$c['asal_data']='9';
			if(trim($nilai)!=''){$c['nilai_huruf']=$nilai;}
			if((int)$bobot > 0){$c['nilai_indeks']=$bobot;}
			$data['data']=$c;
			$insert[]=$c;
			$update[]=$data;
		}else{
			$keterangan[]="<strong>NIM</strong> $nim tidak terdaftar";
		}
	}
	
	$x = $proxy->InsertRecordsets('nilai',$insert);
	$res=$x['result']; $jml_error=0; $errr=array();
	foreach($res as $k=>$v){
		$err_code=(int)$v['error_code'];
		$id_reg_pd=$insert[$k]['id_reg_pd'];
		$err_desc=trim(kompres($v['error_desc']));
		if($err_code > 0){
			$errr[$id_reg_pd][]=array('code'=>$err_code,'desc'=>$err_desc,'proses'=>'Insert Record');
		}
	}
	
	
	$x = $proxy->UpdateRecordsets('nilai',$update);
	$res=$x['result']; $errx=array();
	foreach($res as $k=>$v){
		$err_code=(int)$v['error_code'];
		$id_reg_pd=$update[$k]['key']['id_reg_pd'];
		$err_desc=trim(kompres($v['error_desc']));
		if($err_code > 0){
			$errr[$id_reg_pd][]=array('code'=>$err_code,'desc'=>$err_desc,'proses'=>'Update Record');
		}
	}
	$jml_error=count($errr);
	if($jml_error > 0){
		$folder=FOLDER_DATA."/error/".$_GET['prodi']."/".$_GET['tahun'];
		mkdirs($folder); $lok=urlencode(encrypt("$_GET[prodi]*$_GET[tahun]*$idx"));
		$link="<a target=\"_blank\" href=\"".PATH."/index.php/trnlm.dbf?error=$lok\">$jml_error</a>";
		$js.="$s('#err_$idx').html('$link');\r\n";
		tulis_data("$folder/$idx.txt",json_encode($errr));
	}
	
	$dosen=array();
	$file_dosen=FOLDER_DATA."/dosen-$_GET[prodi]-$_GET[tahun].txt";
	if(file_exists($file_dosen)){
		$dosen=json_decode(file_get_contents($file_dosen),true);
	}
	$dos=$dosen[$idx];
	$thn_ajar=substr($_GET['tahun'],0,4);
	$insert=array();
	$dosen_array=json_decode(file_get_contents(FILE_DOSEN),true);
	foreach($dos as $nidn){
		$id_reg_ptk=$dosen_array[$nidn][$thn_ajar];
		if($id_reg_ptk!=''){
			$c=array();
			$c['id_reg_ptk']=$id_reg_ptk;
			$c['id_kls']=$id_kls;
			$c['sks_subst_tot']=$sks;
			$c['sks_tm_subst']=$sks;
			$c['sks_prak_subst']=0;
			$c['sks_prak_lap_subst']=0;
			$c['sks_sim_subst']=0;
			$c['jml_tm_renc']=16;
			$c['jml_tm_real']=16;
			$c['id_jns_eval']=1;
			$insert[]=$c;
		}else{
			$keterangan[]="<strong>NIDN</strong> $nidn tidak terdaftar";
		}
	}
	$x = $proxy->InsertRecordsets('ajar_dosen',$insert);
	$pj=($pp / $jml) * 100;
	$pjx=100 - $pj;
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	$time=substr($time,0,5);
	$keter='';
	if(count($keterangan)>0){
		$keter="<div class=\"abang\"><strong>Warning</strong><br>".implode("<br>",$keterangan)."</div>";
	}
	$js.="$s('#ket_$idx').html('Time Execute : ($time detik) $keter');\r\n";
	echo "<script>$s('#p_bar3').html('Import <strong>$nama_mk</strong> KLS : <strong>$kelas</strong>'); $js; \r\n $tjs \r\n</script>\r\n";
	echo "<script>document.getElementById('p_bar5').style.width='$pjx%';</script>\r\n";
	echo "\r\n\r\n<script>\r\n$s('#balikan_nilai').load('".PATH."/index.php?load_nilai=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]&pos=$pp');\r\n</script>";
	
	exit;
}
make_heading('Kelas &amp; Nilai','Mengimpor data dari TRNLM.DBF &amp; TRAKD.DBF');
echo "\r\n<style>\r\n.pooter td{background-color:#999999!important; font-weight:bold!important;}\r\n#p_bar5{width:99%; background-color:#cccccc; right:0px ;position:absolute; height:22px}\r\n#p_bar4{width:97%; position:absolute; height:30px; text-align:right}\r\n#p_bar3{width:96%; position:absolute; text-align:center}\r\n#p_bar2{background-color:#ffffff; height:24px; width:100%}\r\n#p_bar{border:1px solid #cccccc; width:100%; height:25px}\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}\r\n</style>\r\n";
echo "<style>#p_bar5a{width:0%; background-color:#cccccc; right:0px ;position:absolute; height:22px}
#p_bar4a{width:97%; position:absolute; height:30px; text-align:right}
#p_bar3a{width:96%; position:absolute; text-align:center}
#p_bar2a{background-color:#ffffff; height:24px; width:100%}
#p_bara{border:1px solid #cccccc; width:100%; height:25px}</style>";
echo "<form name=\"form1\" method=\"get\" action=\"\" id=\"form1\">
Program Studi
  <select name=\"prodi\" id=\"prodi\" >
    <option></option>";
  	list_prodi();
echo "</select>
  Semester
  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" >
    <option></option>";
	list_smtr($_GET['tahun']);
echo "  </select> <button type=\"submit\" class=\"btn btn-small btn-primary\"><i class=\"icon-plus icon-white\"></i> Import</button>
</form><div id=\"balikan_nilai\"></div>";
if($_GET['prodi']){
	if($_GET['tahun']){
		$mk_feeder=array();
		$x = $proxy->GetRecordsets('mata_kuliah',"id_sms='$id_sms'");
		$res=$x['result'];
		foreach($res as $k=>$r){
			$mk_feeder[$r['kode_mk']]=array('id_mk'=>$r['id_mk'],'sks_mk'=>$r['sks_mk'],'nm_mk'=>$r['nm_mk']);
		}
		$smt=$_GET['tahun']; $pos=substr($smt,4,1); $thn=(int)substr($smt,0,4);
		$tglawal=$thn+1 . "-01-01"; $tglakhir=$thn+1 . "-06-30";
		if($pos=='1'){
			$tglawal=$thn . "-07-01"; $tglakhir=$thn . "-12-30";
		}
		$sql=$odbc->query("select * from TRNLM.DBF where KDPTITRNLM='$kode_pt' and KDPSTTRNLM='$_GET[prodi]' and THSMSTRNLM='$_GET[tahun]'");
		$n=array(); $m=array(); $y=array();
		foreach($sql as $k=>$r){
			$idx=$r['KDKMKTRNLM']."-".$r['KELASTRNLM'];
			$n[$idx]=array('mk'=>$r['KDKMKTRNLM'],'kls'=>$r['KELASTRNLM']);
			$y[$idx][$r['NIMHSTRNLM']]=array('nim'=>$r['NIMHSTRNLM'],'nilai'=>$r['NLAKHTRNLM'],'bobot'=>$r['BOBOTTRNLM']);
		}
		
		$folder=FOLDER_DATA."/".$_GET['prodi']."/".$_GET['tahun'];
		mkdirs($folder);
		foreach($y as $k=>$v){
			$yy=$y[$k];
			tulis_data("$folder/$k.txt",json_encode($yy));
		}
		$m=array(); $mm=array(); $mn=array();
		foreach($n as $k =>$v){
			$kodemk=$v['mk'];
			$id_mk=$mk_feeder[$kodemk]['id_mk'];
			$sks=$mk_feeder[$kodemk]['sks_mk'];
			$namamk=$mk_feeder[$kodemk]['nm_mk'];
			$jml=1; if($id_mk==''){$jml=0;}
			$nm_kelas=$v['kls'];
			$m[$k]=array('jml'=>$jml,'mk'=>$kodemk,'kls'=>$nm_kelas,'id_mk'=>$id_mk,'sks'=>$sks,'nm'=>$namamk,'id_kls'=>$id_kls);
			if($jml==1){
				$mm[$k]=array('jml'=>$jml,'mk'=>$kodemk,'kls'=>$nm_kelas,'id_mk'=>$id_mk,'sks'=>$sks,'nm'=>$namamk,'id_kls'=>$id_kls);
				$mn[]=$k;
			}
		}
		tulis_data(FOLDER_DATA."/".$_GET['prodi']."-".$_GET['tahun']."-1.txt",json_encode($mm));
		tulis_data(FOLDER_DATA."/".$_GET['prodi']."-".$_GET['tahun']."-2.txt",json_encode($mn));
echo "<div style=\"width:98%\">
  <table width=\"989\" class=\"table table-striped table-condensed\">
    <thead>
      <tr>
        <th width=\"26\" style=\"width:20px\">No.</th>
        <th width=\"119\" class=\"filtertable_header\" style=\"text-align:center\">Kode MK </th>
        <th width=\"325\" class=\"filtertable_header\" style=\"text-align:center\">Nama MK </th>
        <th width=\"82\" class=\"filtertable_header\" style=\"text-align:center\">Nama Kelas </th>
        <th width=\"52\" class=\"filtertable_header\" style=\"text-align:center\">SKS </th>
        <th width=\"77\" class=\"filtertable_header\" style=\"text-align:center\">Peserta Kelas </th>
        <th width=\"86\" class=\"filtertable_header\" style=\"text-align:center\">Dosen Mengajar </th>
		<th width=\"186\" style=\"text-align:center\">Keterangan</th>
		<th width=\"50\" style=\"text-align:center\">Jml Error</th>
      </tr>
    </thead>
    <tbody>";

$no=0; $jml_dosen=0; $jml_mhs=0; $nidn_array=array();
foreach($m as $k=>$r){
	$jmlmhs=(int)count($y[$k]);
	$jml_mhs=$jml_mhs+$jmlmhs;
	$no++;
	$jml=(int)$r['jml'];
	$klass="";
	if($jml==0){$klass='merah';}
	echo "\r\n<tr class=\"$klass\">
        <td>$no</td>
        <td style=\"text-align:center\"><span id=\"kode_$k\">$r[mk]</span></td>
        <td><span id=\"nm_$k\">$r[nm]</span></td>
        <td style=\"text-align:center\">$r[kls]</td>
        <td style=\"text-align:right\">$r[sks]</td>
        <td style=\"text-align:right\"><span id=\"jmhs_$k\">$jmlmhs</span></td>
        <td style=\"text-align:right\">";
		
		$jd= jml_dosen($r['mk'],$r['kls']);
		$jmdos=count($jd);
		if($jmdos>0){
			foreach($jd as $kk=>$nidn){
				$nidn_array[$nidn]=$nidn;
			}
		}
		
		$jml_dosen=$jml_dosen+$jmdos;
		echo $jmdos;
		echo "</td><td><span id=\"ket_$k\">";
		if($jml==0){
			echo "Kode MK Tidak Terdaftar";
		}
		echo "</span></td><td style=\"text-align:center\"><span id=\"err_$k\"></span></td>\r\n</tr>\r\n";
}
$dosen_array=array(); $id_ptk_array=array(); $nidn_array2=array();
if(file_exists(FILE_DOSEN)){
	$dosen_array=json_decode(file_get_contents(FILE_DOSEN),true);
}
$smtr=substr($_GET['tahun'],0,4);
if(count($nidn_array) >0){
	foreach($nidn_array as $nidn=>$r){
		$id_reg_ptk=$dosen_array[$nidn][$smtr];
		if($id_reg_ptk==''){
			$nidn_array2[]="'$nidn'";
		}
	}
}
if(count($nidn_array2) >0){
	$x = $proxy->GetRecordsets('dosen.raw',"trim(nidn) in(".implode(",",$nidn_array2).")");
	$res=$x['result'];
	if(count($res)>0){
		foreach($res as $k=>$r){
			$nidn=trim($r['nidn']);
			$id_ptk=$r['id_ptk'];
			$dosen_array[$nidn]['id_ptk']=$id_ptk;
			$dosen_array[$id_ptk]=$nidn;
			$id_ptk_array[$id_ptk]="'$id_ptk'";
		}
	}
	if(count($id_ptk_array)>0){
		$x = $proxy->GetRecordsets('dosen_pt.raw',"id_ptk in(".implode(",",$id_ptk_array).")");
		$res=$x['result'];
		if(count($res)>0){
			foreach($res as $k=>$r){
				$id_ptk=$r['id_ptk'];
				$nidn=$dosen_array[$id_ptk];
				$id_reg_ptk=$r['id_reg_ptk'];
				$smt=$r['id_thn_ajaran'];
				$dosen_array[$nidn][$smt]=$id_reg_ptk;
			}
		}
	}
}
tulis_data(FILE_DOSEN,json_encode($dosen_array));
echo "<tr class=\"pooter\">
  <td colspan=\"5\">&nbsp;</td>
  <td style=\"text-align:right\">$jml_mhs</td>
  <td style=\"text-align:right\">$jml_dosen</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr></tbody>
  </table><br><br>
</div>
<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\"><span id=\"dialog_txt\">Connect Server WS</span></div><div id=\"p_bar\"><div id=\"p_bar4\"><div id=\"p_bar5\">&nbsp;</div></div><div id=\"p_bar3\">....</div><div id=\"p_bar2\"><img src=\"".PATH."/app/images/vista.gif\"></div></div><div id=\"p_bara\"><div id=\"p_bar4a\"><div id=\"p_bar5a\">&nbsp;</div></div><div id=\"p_bar3a\">....</div><div id=\"p_bar2a\"><img src=\"".PATH."/app/images/vista.gif\"></div></div></div>";
$s='$';
echo "\r\n<script>\r\nvar mhstotal=$jml_mhs;"; if($jml_mhs > 0){echo "\r\n$s(function() {\r\n$s( \"#detail_mhs\" ).dialog({\r\nheight: 150,\r\nwidth:'50%',\r\nmodal: true,\r\ntitle: 'Wait'\r\n});\r\n}); ";} echo "\r\nvar ke=0; var tampil=true;\r\nfunction show_text(txt,t){\r\nsetTimeout(\"showext('\"+txt+\"')\",t);\r\n}\r\nfunction showext(tks){\r\nke=ke+1;\r\nvar xt=((ke+1) / mhstotal) * 100;\r\ndocument.getElementById('p_bar5a').style.width=xt+'%';\r\nif(tampil==true){\r\n$s('#p_bar3a').html('Proses NIM <strong>'+ tks +'</strong> ('+ke+')');\r\n}\r\n}\r\nfunction tutup_dialog(){\r\n$s( \"#detail_mhs\" ).dialog(\"close\");\r\n}\r\n</script>\r\n\r\n\r\n<script>\r\nif(mhstotal >0){\r\n$s('#balikan_nilai').load('".PATH."/index.php?load_nilai=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');}\r\n</script>";
	}
}
?>
