<?php
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/mahasiswa");
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
if($_GET['ajax_msmhs']){
	header("Content-type: application/json");
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);;
	$jsonData = array('page'=>$page,'total'=>$total,'rows'=>array());
	$no=0;
	foreach($dt as $k=>$rs){
		$r=array(); $r=$rs;
		$nim=$r['nim'];
		$r['ket']="<div id=\"sp_$nim\"></div>";
		$no++; $r['no']=$no.".";
		$r['nim']="<a name=\"a_$nim\"></a><span id=\"td_nim_$nim\">$nim</span>";
		$r['nama']="<span id=\"td_nama_$nim\">$rs[nama]</span>";
		$entry=array('id'=>$nim,'cell'=>$r);
		$jsonData['rows'][] = $entry;
	}
	echo json_encode($jsonData);
	exit;
}

if($_GET['read_tmp_excel']){
	$idx=$_GET['read_tmp_excel'];
	$no=0; $simpan=array();
	$jsonData = array('page'=>$page,'total'=>$jml_data,'rows'=>array());
	$dt=json_decode(file_get_contents(FOLDER_DATA."/excel-$idx.txt"),true);
	@unlink(FOLDER_DATA."/excel-$idx.txt");
		foreach($dt as $k=>$r){
				$rs=array();
				$nim=$k; $no++;
				$rs=$r; $lanjut=1;
				/*$pind=strtoupper(substr($r['Pindahan'],0,1));
				$sex=strtoupper(substr($r['Sex'],0,1));
				if($sex=='W'){$sex='P';}
				$rs['Sex']=$sex;
				$rs['Pindahan']=$pind;
				$tg=cek_tanggal($r['Tgl_lahir']);
				$tanggal=$tg['tgl']; $err=$tg['error'];
				$rs['Tgl_lahir']=$tanggal;
				$pst=jenjang(trim($prodi[$r['Kode_Prodi']]['id_jenj_didik']))." ".trim($prodi[$r['Kode_Prodi']]['nm_lemb']);
				$id_sms=$prodi[$r['Kode_Prodi']]['id_sms'];
				$rs['id_sms']=$id_sms;
				$ket="<span id=\"sp_$nim\"></span>"; 
				$angk=$r['Angkatan'];
				if(trim($id_sms)==''){$lanjut=0; $pst="<span class=\"abang\">$r[Kode_Prodi]</span><br>"; $ket.='<span class="abang">Kode Prodi tdk terdaftar</span><br>';}
				if($err!=''){ $lanjut=0; $ket.='<span class="abang">Format Tanggal Lahir salah</span><br>'; $tanggal="<span class=\"abang\">$tanggal</span><br>";}
				if($sex!='L'){if($sex!='P'){$lanjut=0; $ket.='<span class="abang">Format Jenis Kel salah</span><br>';}}
				if((int)$angk < 1997){$lanjut=0; $ket.='<span class="abang">Tahun angkatan salah</span><br>';}
				if(trim($r['Tp_Lhr'])==''){$lanjut=0; $ket.='<span class="abang">Tempat Lahir tdk boleh kosong</span><br>';}
				if($pind!='B'){if($pind!='P'){$lanjut=0; $ket.='<span class="abang">Status Pindahan tidak jelas (B/P)</span><br>';}}
				$asal="$r[Prodi_Asal] ; $r[PT_Asal] ; $r[SKS] SKS";
				if($pind=='P'){
					if(trim($r['Prodi_Asal'])==''){$lanjut=0; $ket.='<span class="abang">Prodi asal tidak boleh kosong</span><br>';}
					if(trim($r['PT_Asal'])==''){$lanjut=0; $ket.='<span class="abang">P.T asal tidak boleh kosong</span><br>';}
					if((int)$r['SKS']==0){$lanjut=0; $ket.='<span class="abang">SKS Tidak boleh 0</span><br>';}
				}else{
					$asal='';
				}
				if($lanjut==1){
					$simpan[]=$rs;
				}*/
				$ket="<span id=\"sp_$nim\"></span>";
				if($lanjut==1){
					$simpan[]=$rs;
				}
				$nama="<span id=\"td_nama_$nim\">$r[Nama]</span>";
				$data=array('no'=>$no,'nim'=>"<span id=\"td_nim_$nim\">$nim</span>",'nama'=>$nama,'ibu'=>$r['Ibu'],'ket'=>$ket);
				$entry=array('id'=>"cell_".$nim,'cell'=>$data);
				$jsonData['rows'][] = $entry;
		}
		tulis_data(FOLDER_DATA."/$idx.txt",json_encode($simpan));
		echo json_encode($jsonData);
	exit;
}

if($_GET['excel']){
	$idx=microtime_float();
	make_heading('Mahasiswa','Mengimpor data dari EXECL');
	echo "\r\n<style>\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}</style>\r\n<div id=\"balikan_mhs\"></div>";
	echo "\r\n<script src=\"".PATH."/app/fn.js\"></script>\r\n";
	echo "<div id=\"balikan_mhs\"></div>\r\n";
	echo "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\" name=\"formexcel\">
  Pilih File
  <input id=\"filexls\" onchange=\"ValidateSingleInput(this);\" type=\"file\" name=\"file\">
  <button type=\"submit\" class=\"btn btn-small btn-primary\"><i class=\"icon-plus icon-white\"></i>Upload</button>
 <span style=\"float:right\"><a href=\"http://192.168.35.254:8082/wsfeeder1.2/app/excel/update_mahasiswa.xlsx\" class=\"btn btn-small btn-primary\">Download <strong>Contoh</strong> File Daftar Mahasiswa</a></span>
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
		$cl=array('NIM'=>'NIM','Nama'=>'Nama','Agama'=>'Agama','Nama Ibu'=>'Ibu',
            'Nama Ayah' => 'Ayah', 'Jalan'=> 'Jalan', 'Kode Pos' =>'KodePos', 'Telepon' =>'Telp', 'HP' =>'hp');
		$dt=Excel2Array($isi,$cl);
	}else{
		echo "<a href=\"http://192.168.35.254:8082/wsfeeder1.2/app/excel/update_mahasiswa.xlsx\"><img src=\"".PATH."/app/images/DataMahasiswa.jpg\"></a>";
	}
	$simpan=array();
	if(count($dt) >0){
		$s='$';
		echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n";
		echo "<script type=\"text/javascript\">\r\nfunction show_dialog_mhs(){"."$s( \"#detail_mhs\" ).dialog({
						height: 100,
						width:450,
						modal: true,
						title: 'Wait'
					});}\r\nfunction mulai(){"."$s('#balikan_mhs').load('".PATH."/index.php?load_mhs_xls=1&id=$idx'); show_dialog_mhs();}</script>\r\n";
		tulis_data(FOLDER_DATA."/excel-$idx.txt",json_encode($dt));
		$t="NO,no,20,right;NIM,nim,100,left;NAMA,nama,150,left;NAMA IBU,ibu,200,left;KETERANGAN,ket,200,left";
		$tabel=array('id'=>'tbl_mhs','url'=>PATH."/index.php/msmhs.dbf?read_tmp_excel=$idx",'fungsi'=>'mulai','data'=>$t);
		make_tabel($tabel);
	}
	make_footer('WebService | Import Excel (mahasiswa)');
	exit;
}

if($_GET['load_mhs_xls']){
	$idx=$_GET['id'];
	if(!file_exists(FOLDER_DATA."/$idx.txt")){
		echo "\r\n<script>\r\n$s( \"#detail_mhs\" ).dialog( \"close\" ); \r\n</script>\r\n";
		exit;
	}
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$idx.txt"),true);
	
	if(count($dt)==0){
		echo "\r\n<script>\r\n$s( \"#detail_mhs\" ).dialog( \"close\" ); \r\n</script>\r\n";
		@unlink(FOLDER_DATA."/$idx.txt");
		exit;
	}
	$data=array();
	foreach($dt as $k=>$rr){
		$r=array();
		$r['nama']=$rr['Nama'];
		$r['nim']=$rr['NIM'];
		$r['ibu']=$rr['Ibu'];
		$r['agama']=$rr['Agama'];
		$r['ayah']=$rr['Ayah'];
		$r['jln']=$rr['Jalan'];
		$r['kodepos']=($rr['KodePos'] === '') ? '00000' : $rr['KodePos'];
		$r['telp']=$rr['Telp'];
		$r['hp']=$rr['hp'];

		$data[]=$r;
	}
	proses_data_excel($data);
	exit;
}


if(!$_SERVER['QUERY_STRING']){
	$sqlx="select DISTINCT TAHUNMSMHS from $tbl_dbf where KDPTIMSMHS='$kode_pt'";
	$rs=$odbc->query($sqlx);
	//print_r($rs);
	//echo "sql Query";
	//var_dump($rs);
	//echo $sqlx;
	foreach($rs as $k=>$r){
		$angkatan[$r['TAHUNMSMHS']]=$r['TAHUNMSMHS'];
		//echo $r['TAHUNMSMHS'];
	}
	ksort($angkatan);
	tulis_data(FOLDER_DATA."/tahun.txt",json_encode($angkatan));
}
$angkatan=json_decode(file_get_contents(FOLDER_DATA."/tahun.txt"),true);


function make_array($r,$id_sms,$id_sp,$insert=true){
	$rc=array(); $rs=array(); 
	$rc['nm_pd']= strtoupper($r['nama']);
	$rc['jk']= $r['sex'];
	$rc['tmpt_lahir']= $r['tplhr'];
	$rc['tgl_lahir']= $r['tglhr'];
	$id_jns_daftar=1;
	if($r['stpid']!='B'){
		$id_jns_daftar=2;
		$rc['regpd_sks_diakui']=(int)$r['sks'];
		$rc['regpd_nm_pt_asal']=$r['aspt'];
		$rc['regpd_nm_prodi_asal']=$r['aspst'];
	}
	$rc['regpd_id_jns_daftar']= $id_jns_daftar;
	$rc['regpd_mulai_smt']=$r['smawl'];
	if($insert==true){
		$rc['regpd_nipd']= $r['nim'];
		$rc['nisn']= '';
		$rc['nik']= '';
		$rc['id_agama']= '98';
		$rc['id_kk']= '0';
		$rc['id_sp']= $id_sp;
		$rc['jln']= 'jln';
		$rc['rt']= '0';
		$rc['rw']= '0';
		$rc['nm_dsn']= '';
		$rc['ds_kel']= '';
		$rc['id_wil']= '999999';
		$rc['kode_pos']= '00000';
		$rc['id_jns_tinggal']= '1';
		$rc['id_alat_transport']= '1';
		$rc['telepon_rumah']= '';
		$rc['telepon_seluler']= '';
		$rc['email']= '';
		$rc['a_terima_kps']= '0';
		$rc['stat_pd']= 'A';
		$rc['id_kebutuhan_khusus_ayah']= '0';
		$rc['nm_ibu_kandung']= ' - ';
		$rc['id_kebutuhan_khusus_ibu']= '0'; 
		$rc['kewarganegaraan']= 'ID';
		$rc['regpd_id_sms']= $id_sms; 
		if($r['id_sms']!=''){
			$rc['regpd_id_sms']= $r['id_sms']; 
		}
		$rc['regpd_id_sp']= $id_sp;
		$rc['regpd_tgl_masuk_sp']= $r['tglmsk'];
		return $rc;
	}else{
		$x['key']=array('id_pd'=>$r['id_pd']);
		$x['data']=$rc;
		return $x;
	}
}
function make_array_Update_peserta_didik($r){
	$rc=array(); $data=array();
	$rc['nm_pd']= strtoupper($r['nama']);
	$rc['jk']= $r['sex'];
	$rc['tmpt_lahir']= $r['tplhr'];
	$rc['tgl_lahir']= $r['tglhr'];
	$data['key']=array('id_pd'=>$r['id_pd']);
	$data['data']=$rc;
	return $data;
}

function make_array_Update_peserta_didik_excel($r){
	$rc=array(); $data=array();
	//$rc['nm_pd']= strtoupper($r['nama']);
    if($r['agama'] == 2){
        $agama = 3;
    }elseif($r['agama'] == 3){
        $agama=2;
    }elseif($r['agama'] == 4){
        $agama=5;
    }elseif($r['agama'] == 5){
        $agama=4;
    }else{
        $agama = 98;
    }


	$rc['id_agama']= $agama;
	//$rc['nm_ibu_kandung']= $r['ibu'];
	$rc['nm_ayah']= $r['ayah'];
	$rc['jln']= $r['jln'];
	$rc['kode_pos']= ($r['kodepos'] === '' || $r['kodepos'] == '0' || $r['kodepos'] == NULL) ? '00000' : substr($r['kodepos'],0,5);
	$rc['nm_dsn']= '-';
	$rc['ds_kel']= '-';
	$rc['id_wil']= '999999';
	$rc['telepon_rumah']= str_replace(' ', '',$r['telp']);
	$rc['telepon_seluler']= $r['hp'];
	$data['key']=array('id_pd'=>$r['id_pd']);
	$data['data']=$rc;



    $host        = "host=192.168.35.254";
    $port        = "port=54321";
    $dbname      = "dbname=pddikti";
    $credentials = "user=s3kar4ng password=m3n4ng";

    $db = pg_connect( "$host $port $dbname $credentials"  );
    if(!$db){
       echo "Error : Unable to open database\n";
    }
    $sql =<<<EOF
      UPDATE peserta_didik set nm_ibu_kandung = '$r[ibu]' where id_pd='$r[id_pd]';
EOF;
    $ret = pg_query($db, $sql);
    if(!$ret){
       echo pg_last_error($db);
       exit;
    } else {
       echo "Update Nama Ibu ".$r['nama']." Sukses ".substr($r['kodepos'],0,5). "<br/> ";
    }

    pg_close($db);

	return $data;
}
function make_Update_array_reg_pd($r){
	$id_jns_daftar=1; $rc=array(); $data=array();
	if($r['stpid']!='B'){
		$id_jns_daftar=2;
		$rc['sks_diakui']=(int)$r['sks'];
		$rc['nm_pt_asal']=$r['aspt'];
		$rc['nm_prodi_asal']=$r['aspst'];
	}
	$rc['id_jns_daftar']= $id_jns_daftar;
	$rc['mulai_smt']=$r['smawl'];
	$rc['tgl_masuk_sp']= $r['tglmsk'];
	$data['key']=array('id_reg_pd'=>$r['id_reg_pd']);
	$data['data']=$rc;
	return $data;
}
function insert_data($rs,$id_sms,$id_sp,$update_start){
	$kode_pt=trim($_SESSION['data_pt']['npsn']);
	global $proxy; $js=''; $s='$'; global $odbc2;
	$alamat_server=$_SESSION['server_url'];
	$update=array(); $update_index=array();
	foreach($rs as $k=>$r){
		$update[]=make_array($r,$id_sms,$id_sp,true);
		$update_index[]=$r['nim'];
	}
	
	$x=$proxy->InsertRecordsets('mahasiswa',$update);
	$res=$x['result'];
	$total=count($res);
	$update_time= microtime_float();
	$time = $update_time - $update_start;
	if(time!=0){
		if($total!=0){
			$time=$time / $total;
		}
	}
	$time=substr($time,0,5);
	$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$_GET[prodi].txt";
	$idpdarray=array();
	if(file_exists($file_id_pd)){
		$idpdarray=json_decode(file_get_contents($file_id_pd),true);
	}
	foreach($res as $k=>$v){
		$nim=$update_index[$k];
		$nama=str_replace("'","\\'",$v['nm_pd']);
		$id_pd=$v['id_pd'];
		$id_reg_pd=$v['id_reg_pd'];
		$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
		if($err!=''){
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<div class=\"abang\">$err</div>'); window.location='#a_$nim';\r\n";
		}else{
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+' <strong>->Berhasil</strong> ($time detik)');\r\n";
			$js.="$s('#td_nim_$nim').html('<a target=\"_blank\" href=\"$alamat_server/regpd/lst/$id_pd\">$nim</a>'); \r\n$s('#td_nama_$nim').html('<a target=\"_blank\" href=\"$alamat_server/pesertadidik/detail/$id_pd\">$nama</a>');\r\n";
		}
		$idpdarray[$nim]=$id_reg_pd;
	}
	tulis_data($file_id_pd,json_encode($idpdarray));
	echo "\r\n<script>\r\n $js</script>";
}
function update_data_reg_pd($rs,$update_start){
	global $proxy; $js=''; $s='$';
	$update=array(); $update_index=array();
	foreach($rs as $k=>$r){
		$update[]=make_Update_array_reg_pd($r);
		$update_index[]=$r['nim'];
	}
	$x=$proxy->UpdateRecordsets('mahasiswa_pt',$update);
	$res=$x['result'];
	
	$total=count($res);
	$update_time= microtime_float();
	$time = $update_time - $update_start;
	if(time!=0){
		if($total!=0){
			$time=$time / $total;
		}
	}
	$time=substr($time,0,5);
	
	foreach($res as $k=>$v){
		$nim=$update_index[$k];
		
		$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
		if($err!=''){
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<div class=\"abang\">$err</div>'); window.location='#a_$nim';\r\n";
		}else{
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+' <br><span style=\"background-color:#33FF00\">TBL <strong>reg_pd</strong> : Berhasil diupdate ($time detik)</span>');\r\n";
		}
	}
	echo "\r\n<script>\r\n $js</script>";
	
}
function update_data_peserta_didik($rs,$update_start){
	global $proxy; $js=''; $s='$';
	$update=array(); $update_index=array();
	foreach($rs as $k=>$r){
		$update[]=make_array_Update_peserta_didik($r);
		$update_index[]=$r['nim'];
	}
	$x=$proxy->UpdateRecordsets('mahasiswa',$update);
	$res=$x['result'];
	$total=count($res);
	$update_time= microtime_float();
	$time = $update_time - $update_start;
	if(time!=0){
		if($total!=0){
			$time=$time / $total;
		}
	}
	$time=substr($time,0,5);
	foreach($res as $k=>$v){
		$nim=$update_index[$k];
		$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
		if($err!=''){
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<div class=\"abang\">".$err ." </div>'); window.location='#a_$nim';\r\n";
		}else{
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+' <br><span style=\"background-color:#33FF00\">TBL <strong>peserta_didik</strong> : Berhasil diupdate ($time detik)</span>');\r\n";
		}
	}
	echo "\r\n<script>\r\n $js</script>";
}

function update_data_peserta_didik_excel($rs,$update_start){
	global $proxy; $js=''; $s='$';
	$update=array(); $update_index=array();
	foreach($rs as $k=>$r){
		$update[]=make_array_Update_peserta_didik_excel($r);
		$update_index[]=$r['nim'];
	}
	$x=$proxy->UpdateRecordsets('mahasiswa',$update);
	$res=$x['result'];
	$total=count($res);
	$update_time= microtime_float();
	$time = $update_time - $update_start;
	if(time!=0){
		if($total!=0){
			$time=$time / $total;
		}
	}
	$time=substr($time,0,5);
	foreach($res as $k=>$v){
		$nim=$update_index[$k];
		$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
		if($err!=''){
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<div class=\"abang\">".$err."</div>'); window.location='#a_$nim';\r\n";
		}else{
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+' <br><span style=\"background-color:#33FF00\">TBL <strong>peserta_didik</strong> : Berhasil diupdate ($time detik)</span>');\r\n";
		}
	}
	echo "\r\n<script>\r\n $js</script>";
}

function cek_peserta_didik($r,$rs){
	$sama=1;
	if(bandingkan_data($r['nama'],$rs['nm_pd'])==0){$sama=0;}
	if(bandingkan_data($r['tglhr'],$rs['tgl_lahir'])==0){$sama=0;}
	if(bandingkan_data($r['tplhr'],$rs['tmpt_lahir'])==0){$sama=0;}
	if(bandingkan_data($r['sex'],$rs['jk'])==0){$sama=0;}
	return $sama;
}
function cek_peserta_didik_excel($r,$rs){
	$sama=0;
	if(bandingkan_data($r['nama'],$rs['nm_pd'])==0){$sama=0;}
	if(bandingkan_data($r['agama'],$rs['id_agama'])==0){$sama=0;}
	if(bandingkan_data($r['ibu'],$rs['nm_ibu_kandung'])==0){$sama=0;}
	if(bandingkan_data($r['ayah'],$rs['nm_ayah'])==0){$sama=0;}
	return $sama;
}

function cek_reg_pd($r,$rs){
	$pid=1; $sama=1;
	if($r['stpid']!='B'){$pid=2;}
	if(bandingkan_data($r['tglmsk'],$rs['tgl_masuk_sp'])==0){$sama=0;}
	if(bandingkan_data((int)$r['sks'],(int)$rs['sks_diakui'])==0){$sama=0;}
	if(bandingkan_data($r['aspt'],$rs['nm_pt_asal'])==0){$sama=0;}
	if(bandingkan_data($r['aspst'],$rs['nm_prodi_asal'])==0){$sama=0;}
	if(bandingkan_data($pid,(int)$rs['id_jns_daftar'])==0){$sama=0;}
	if($r['id_sms']!=''){
		if(bandingkan_data($r['id_sms'],$rs['id_sms'])==0){$sama=0;}
	}
	return $sama;
}
function bandingkan_data($d1,$d2){
	$ret=0;
	if(trim(strtoupper($d1))== trim(strtoupper($d2))){
		$ret=1;
	}
	return $ret;
}
function proses_data($dt){
	$prodi=unserialize(PRODI);
	$id_sms=$prodi[$_GET['prodi']]['id_sms'];
	$id_sp=$_SESSION['data_pt']['id_sp'];
	global $odbc2;
	$mulai=microtime_float();
	$js=''; $s='$'; $insert=array(); $update_peserta_didik=array(); $update_reg_pd=array();
	$id_pd_array=array();
	$alamat_server=$_SESSION['server_url'];
	global $proxy; 
	$proxy=new PROXY();
	$proxy->connect();
	
	$nim_arr=array();
	foreach($dt as $k=>$r){
		$nim=trim($r['nim']);
		$nim_arr[]="'".$nim."'";
	}
	$filter="trim(nipd) in(".implode(",",$nim_arr).")";
	$x=$proxy->GetRecordsets("mahasiswa_pt.raw",$filter);
	
	$res=$x['result'];
	$data_reg_pd=array(); $data_pd=array();
	foreach($res as $k=>$r){
		$nim=trim($r['nipd']);
		$id_pd=$r['id_pd'];
		$data_pd[$id_pd]=$r;
		$id_pd_array[]="'$id_pd'";
	}
	$filter="id_pd in(".implode(",",$id_pd_array).")";
	$x=$proxy->GetRecordsets("mahasiswa.raw",$filter);
	
	$res=$x['result']; 
	foreach($res as $k=>$r){
		$id_pd=$r['id_pd'];
		$nim=trim($data_pd[$id_pd]['nipd']);
		$data_reg_pd[$nim]=array_merge($data_pd[$id_pd],$r);
	}	
	$dt2=array();
	foreach($dt as $k=>$r){
		$nim=trim($r['nim']);
		if(isset($data_reg_pd[$nim])){
			$dt2[$nim]=$r;
		}else{
			$insert[]=$r;
			$js.="$s('#sp_$nim').html('<span style=\"background-color:#00FFFF\">Mencoba Insert Record baru ..</span>');";
		}
	}
	$kode_pt=trim($_SESSION['data_pt']['npsn']);
	$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$_GET[prodi].txt";
	$idpdarray=array();
	if(file_exists($file_id_pd)){
		$idpdarray=json_decode(file_get_contents($file_id_pd),true);
	}
	foreach($dt2 as $nim=>$rr){
		$r=array();
		$r=$rr;
		if($_GET['prodi']){
			$r['id_sms']=$id_sms;
		}
		$t=cek_peserta_didik($r,$data_reg_pd[$nim]);
		$nama=str_replace("'","\\'",$r['nama']);
		$tt=cek_reg_pd($r,$data_reg_pd[$nim]);
		$id_pd=$data_reg_pd[$nim]['id_pd'];
		$id_reg_pd=$data_reg_pd[$nim]['id_reg_pd'];
		if($t==1){
			$js.="$s('#sp_$nim').html('TBL <strong>peserta_didik</strong> : Tidak ada perubahan data');";
		}else{
			$js.="$s('#sp_$nim').html('<span style=\"background-color:#CCFF00\"> TBL <strong>peserta_didik</strong> : Ada Perbedaan data, mencoba update</span>');";
			$update_peserta_didik[]=$r;
		}
		if($tt==1){
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<br>TBL <strong>reg_pd</strong> : Tidak ada perubahan data');";
		}else{
			$js.="$s('#sp_$nim').html($s('#sp_$nim').html()+'<br><span style=\"background-color:#CCFF00\">TBL <strong>reg_pd</strong> : Ada Perbedaan data, mencoba update</span>');";
			$update_reg_pd[]=$r;
		}
		$js.="$s('#td_nim_$nim').html('<a target=\"_blank\" href=\"$alamat_server/regpd/lst/$id_pd\">$nim</a>'); \r\n$s('#td_nama_$nim').html('<a target=\"_blank\" href=\"$alamat_server/pesertadidik/detail/$id_pd\">$nama</a>');\r\n";
		$idpdarray[$nim]=$id_reg_pd;
	}
	tulis_data($file_id_pd,json_encode($idpdarray));
	
	echo "\r\n<script>\r\n $js</script>";
	
	if(count($update_peserta_didik) > 0){
		$update_start= microtime_float();
		update_data_peserta_didik($update_peserta_didik,$update_start);
	}
	if(count($update_reg_pd) > 0){
		$update_start= microtime_float();
		update_data_reg_pd($update_reg_pd,$update_start);
	}
	
	if(count($insert) > 0){
		$update_start= microtime_float();
		insert_data($insert,$id_sms,$id_sp,$update_start);
	}
	$selesai=microtime_float();
	$waktu=substr(($selesai -$mulai),0,5);
	echo "\r\n<script>\r\n$s( \"#detail_mhs\" ).dialog( \"close\" ); $s('#info_proses').text('Time $waktu detik'); \r\n</script>\r\n";
	exit;
	
}

function proses_data_excel($dt){
	$prodi=unserialize(PRODI);
	$id_sms=$prodi[$_GET['prodi']]['id_sms'];
	$id_sp=$_SESSION['data_pt']['id_sp'];
	global $odbc2;
	$mulai=microtime_float();
	$js=''; $s='$'; $insert=array(); $update_peserta_didik=array(); $update_reg_pd=array();
	$id_pd_array=array();
	$alamat_server=$_SESSION['server_url'];
	global $proxy;
	$proxy=new PROXY();
	$proxy->connect();

	$nim_arr=array();
	foreach($dt as $k=>$r){
		$nim=trim($r['nim']);
		$nim_arr[]="'".$nim."'";
	}
	$filter="trim(nipd) in(".implode(",",$nim_arr).")";
	$x=$proxy->GetRecordsets("mahasiswa_pt.raw",$filter);

	$res=$x['result'];
	$data_reg_pd=array(); $data_pd=array();
	foreach($res as $k=>$r){
		$nim=trim($r['nipd']);
		$id_pd=$r['id_pd'];
		$data_pd[$id_pd]=$r;
		$id_pd_array[]="'$id_pd'";
	}
	$filter="id_pd in(".implode(",",$id_pd_array).")";
	$x=$proxy->GetRecordsets("mahasiswa.raw",$filter);

	$res=$x['result'];
	foreach($res as $k=>$r){
		$id_pd=$r['id_pd'];
		$nim=trim($data_pd[$id_pd]['nipd']);
		$data_reg_pd[$nim]=array_merge($data_pd[$id_pd],$r);
	}
	$dt2=array();
	foreach($dt as $k=>$r){
		$nim=trim($r['nim']);
		if(isset($data_reg_pd[$nim])){
			$dt2[$nim]=$r;
		}else{
			$insert[]=$r;
			$js.="$s('#sp_$nim').html('<span style=\"background-color:#00FFFF\">Mencoba Insert Record baru ..</span>');";
		}
	}
	$kode_pt=trim($_SESSION['data_pt']['npsn']);
	$file_id_pd=SISTEM_TMP."/$kode_pt/id_pd_$_GET[prodi].txt";
	$idpdarray=array();
	if(file_exists($file_id_pd)){
		$idpdarray=json_decode(file_get_contents($file_id_pd),true);
	}
	foreach($dt2 as $nim=>$rr){
		$r=array();
		$r=$rr;
		if($_GET['prodi']){
			$r['id_sms']=$id_sms;
		}
		$t=cek_peserta_didik_excel($r,$data_reg_pd[$nim]);
		$nama=str_replace("'","\\'",$r['nama']);

		$id_pd=$data_reg_pd[$nim]['id_pd'];
		$id_reg_pd=$data_reg_pd[$nim]['id_reg_pd'];
		if($t==1){
			$js.="$s('#sp_$nim').html('TBL <strong>peserta_didik</strong> : Tidak ada perubahan data');";
		}else{
			$js.="$s('#sp_$nim').html('<span style=\"background-color:#CCFF00\"> TBL <strong>peserta_didik</strong> : Ada Perbedaan data, mencoba update</span>');";
			$r['id_pd'] = $data_reg_pd[$nim]['id_pd'];
			$update_peserta_didik[]=$r;

		}

		$js.="$s('#td_nim_$nim').html('<a target=\"_blank\" href=\"$alamat_server/regpd/lst/$id_pd\">$nim</a>'); \r\n$s('#td_nama_$nim').html('<a target=\"_blank\" href=\"$alamat_server/pesertadidik/detail/$id_pd\">$nama</a>');\r\n";
		$idpdarray[$nim]=$id_reg_pd;
	}
	tulis_data($file_id_pd,json_encode($idpdarray));

	echo "\r\n<script>\r\n $js</script>";

	if(count($update_peserta_didik) > 0){
		$update_start= microtime_float();
		update_data_peserta_didik_excel($update_peserta_didik,$update_start);
	}

	if(count($insert) > 0){
		$update_start= microtime_float();
		insert_data($insert,$id_sms,$id_sp,$update_start);
	}
	$selesai=microtime_float();
	$waktu=substr(($selesai -$mulai),0,5);
	echo "\r\n<script>\r\n$s( \"#detail_mhs\" ).dialog( \"close\" ); $s('#info_proses').text('Time $waktu detik'); \r\n</script>\r\n";
	exit;

}

$id_sms=$prodi[$_GET['prodi']]['id_sms'];
if($_GET['load_mhs']){
	$dt=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);
	proses_data($dt);
	exit;
}
make_heading('Mahasiswa','Mengimpor data dari MSMHS.DBF');
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

if($_GET['prodi']){
	if($_GET['tahun']){
		
echo "<div id=\"balikan_mhs\"></div>";

$dtmhsx=$odbc->query("select NIMHSMSMHS as nim,NMMHSMSMHS as nama,TPLHRMSMHS as tplhr,TGLHRMSMHS as tglhr,KDJEKMSMHS as sex,TGMSKMSMHS as tglmsk,STPIDMSMHS as stpid,SKSDIMSMHS as sks,ASNIMMSMHS as asnim,ASPTIMSMHS as aspt,ASPSTMSMHS as aspst,SMAWLMSMHS as smawl from $tbl_dbf where KDPTIMSMHS='$kode_pt' and KDPSTMSMHS='$_GET[prodi]' and TAHUNMSMHS='$_GET[tahun]'");	
$d=array(); $dtmhs=array();
foreach($dtmhsx as $k=>$rs){
	$d[$rs['nim']]=$rs;
}
foreach($d as $k=>$rs){
	$dtmhs[]=$rs;
}
$no=0; $new=array(); $up=0; $update=array(); $server_url=$_SESSION['server_url'];
$rx=array();

if(count($dtmhs) >0){
	foreach($dtmhs as $k=>$rs){
		$no++; $r=array(); $r=$rs;
		$r['sks']=(int)$rs['sks'];
		$nim=$r['nim'];
		$asal='';
		if($r[stpid]!='B'){
			$aspt=cek_pt_asal($nim,$r['aspt']);
			$aspst=cek_pst_asal($nim,$r['aspst']);
			$asal="$r[asnim] ; $aspst ; <br> $aspt ;  $r[sks] SKS";
			$r['aspt']=$aspt;
			$r['aspst']=$aspst;
			
		}
		$r['asal']=$asal;
		$rx[]=$r;
	}
}
$s='$';
echo "<script type=\"text/javascript\">\r\nfunction mulai(){"."$s('#balikan_mhs').load('".PATH."/index.php?load_mhs=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');}</script>\r\n";

$t="NO,no,20,right;NIM,nim,100,left;NAMA,nama,200,left;SEX,sex,20,center,TP LHR,tplhr,130,left;TGL LAHIR,tglhr,80,left;P/B,stpid,20,center;ASAL PINDAHAN,asal,280,left;KETERANGAN,ket,280,left";
$tabel=array('id'=>'tbl_mhs','url'=>PATH."/index.php/msmhs.dbf?prodi=$_GET[prodi]&tahun=$_GET[tahun]&ajax_msmhs=1",'fungsi'=>'mulai','data'=>$t);
make_tabel($tabel); 

echo "<div id=\"info_proses\"></div></div>\r\n";
tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($rx));
	if($no >0){
		echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n";
		echo "\r\n\r\n</script>\r\n<script>\r\nvar itg=0;\r\n$(function() {
			$s( \"#detail_mhs\" ).dialog({
				height: 100,
				width:'450',
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
		
		</script>\r\n";
	}
}}
?>
