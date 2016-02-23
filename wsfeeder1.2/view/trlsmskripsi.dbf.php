<?php
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/lsmskripsi");
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

if($_GET['excel']){
	$idx=microtime_float();
	make_heading('Impor Mhs Lulus Skripsi','Mengimpor status mahasiswa dari EXECL');
	echo "\r\n<style>\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.dlg{text-align:center;}</style>\r\n<div id=\"balikan_mhs\"></div>";
	echo "\r\n<script src=\"".PATH."/app/fn.js\"></script>\r\n";
	echo "<div id=\"balikan_mhs\"></div>\r\n";
	echo "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\" name=\"formexcel\">
  Pilih File
  <input id=\"filexls\" onchange=\"ValidateSingleInput(this);\" type=\"file\" name=\"file\">
  <button type=\"submit\" class=\"btn btn-small btn-primary\"><i class=\"icon-plus icon-white\"></i>Upload</button>
 <span style=\"float:right\"><a href=\"../app/tmp/skripsimahasiswa.xlsx\" class=\"btn btn-small btn-primary\">Download <strong>Contoh</strong> File Daftar Mahasiswa</a></span>
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
		$cl=array('Tahun'=>'THSMSTR','Kode PT'=>'KDPT','Kode PST'=>'KDPST','NIM'=>'NIM', 'Status'=>'STTS', 'TGL Lulus'=> 'TGLLS',
		'SKS'=>'SKS','IPK'=>'IPK','No SK'=>'NOSKR','TGLRE'=>'TGLRE', 'No Ijazah'=>'NOIJAZAH','Jalur Skripsi'=>'JLR', 'Judul'=>'JDL',
		'TGL SK Yudisium'=>'TGLSKYUD');
		$dt=Excel2Array($isi,$cl);
	}else{
		$file = FOLDER_DATA."/excel-trlsmskripsi.txt";
		if(file_exists($file)){
			echo "\r\n<style>\r\n.merah td,.merah span,.merah{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"?excel=1&\" id=\"form1\">
			  Program Studi
			  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
				<option></option>";
				list_prodi();
			echo " </select>
			 Semester
			  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" onChange=\"document.getElementById('form1').submit();\">
				<option></option>"; list_smtr($_GET['tahun']);
			echo " </select></form>\r\n";
		}
		
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
					});}\r\nfunction mulai(){"."$s('#balikan_mhs').load('".PATH."/index.php?load_skripsi_xls=1&id=$idx'); show_dialog_mhs();}</script>\r\n";
		echo FOLDER_DATA."/excel-trlsmskripsi.txt";
		tulis_data(FOLDER_DATA."/excel-trlsmskripsi.txt",json_encode($dt));
		
		echo "\r\n<style>\r\n.merah td,.merah span,.merah{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"?excel=1&\" id=\"form1\">
		  Program Studi
		  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
			<option></option>";
			list_prodi();
		echo " </select>
		 Semester
		  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" onChange=\"document.getElementById('form1').submit();\">
			<option></option>"; list_smtr($_GET['tahun']);
		echo " </select></form>\r\n";
		
		//$t="NO,no,20,right;PRODI,pst,150,left;NIM,nim,100,left;NAMA,nama,150,left;SEX,sex,30,center;ANGK,angk,60,center;TP LHR,tplhr,100,left;TGL LAHIR,tglhr,60,left;P/B,stpid,30,center; ASAL PINDAHAN,asal,200,left;KETERANGAN,ket,200,left";
		//$tabel=array('id'=>'tbl_mhs','url'=>PATH."/index.php/msmhs.dbf?read_tmp_excel=$idx",'fungsi'=>'mulai','data'=>$t);
		//make_tabel($tabel);
	}
	
	make_footer('WebService | Import Excel (mahasiswa)');
	exit;
}

if($_GET['load_lsm_skripsi']){
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
			$nim=$vv['NIM'];
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
		if($status=='L'):
		foreach($v as $kk=>$vv){
			$c=array(); $data=array(); $lulus=array(); $data_lulus=array();
			$nim=$vv['NIM'];
			$id_reg_pd=$idpdarray[$nim];
			
			if($id_reg_pd!=''){
				$x = $proxy->GetRecords('kuliah_mahasiswa',"id_smt='$_GET[tahun]' and id_reg_pd='$id_reg_pd'");
				$sts=trim($x['result']['id_stat_mhs']);
				$id_up=$x['result']['id_updater'];
				$c['id_stat_mhs']=$status;
				$c['id_reg_pd']=$id_reg_pd;
				$c['id_smt']=$_GET['tahun'];
				if($status=='L'){
					$c['ipk']=$vv['IPK'];
					$c['sks_total']=(int)$vv['SKS'];
					$lulus['id_jns_keluar']=1;
					$lulus['tgl_keluar']=$vv['TGLLS'];
					$lulus['sk_yudisium']=$vv['NOSKR'];
					//$lulus['tgl_sk_yudisium']=$vv['TGLRETRLSM'];
					$lulus['ipk']=$vv['IPK'];
					$lulus['no_seri_ijazah']=$vv['NOIJAZAH'];
					$lulus['tgl_sk_yudisium']=$vv['TGLSKYUD'];
					$lulus['jalur_skripsi']=$vv['JLR'];
					$lulus['judul_skripsi']=$vv['JDL'];
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
		endif;
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

//buka_data();
//function buka_data(){
	//$kode_pt=trim($_SESSION['data_pt']['npsn']);
	make_heading('Impor Mhs Lulus Skripsi','Mengimpor status mahasiswa dari TRLSMSKRIPSI.DBF');
	echo "\r\n<style>\r\n.merah td,.merah span,.merah{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"?excel=1&\" id=\"form1\">
	  Program Studi
	  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
		<option></option>";
		list_prodi();
	echo " </select>
	 Semester
	  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" onChange=\"document.getElementById('form1').submit();\">
		<option></option>"; list_smtr($_GET['tahun']);
	echo " </select></form>\r\n";
	echo "<div id=\"balikan_mhs\"></div>\r\n";
	if($_GET['prodi']){
		if($_GET['tahun']){
			//$rs=$odbc->query("select NIMHSTRLSM,STMHSTRLSM,TGLLSTRLSM,SKSTTTRLSM,NLIPKTRLSM,NOSKRTRLSM,TGLRETRLSM,NOIJATRLSM from TRLSMSKRIPSI.DBF where KDPTITRLSM='$kode_pt' and KDPSTTRLSM='$_GET[prodi]' and THSMSTRLSM='$_GET[tahun]'");
			$file = FOLDER_DATA."/excel-trlsmskripsi.txt";
			//if(file_exists($file)){
				$rs=json_decode(file_get_contents($file),true);
			//}
			echo $file."<br/>";
			
			$cl=array('Tahun'=>'THSMSTR','Kode PT'=>'KDPT','Kode PST'=>'KDPST','NIM'=>'NIM', 'Status'=>'STTS', 'TGL Lulus'=> 'TGLLS',
			'SKS'=>'SKS','IPK'=>'IPK','No SK'=>'NOSKR','TGLRE'=>'TGLRE', 'No Ijazah'=>'NOIJAZAH','Jalur SKripsi'=>'JLR', 'Judul'=>'JDL',
			'TGL SK Yudisium'=>'TGLSKYUD');
			$dataskrs =array();
			$mhs=array(); $statusr=array();
			foreach($rs as $k=>$r){
				//echo $k ." " .$r['KDPT']." " .$r['KDPST']." " .$r['THSMSTR'];
				if($r['KDPT'] == $kode_pt && $r['KDPST']==$_GET['prodi'] && $r['THSMSTR'] == $_GET['tahun']){
					//echo $k ." " .$r['KDPT']." " .$r['KDPST']." " .$r['THSMSTR']. " ".$r['NIM'];
					//echo "<br/>";
					
					$dataskrs=$r;
					$status=$r['STTS'];
					$nim=$r['NIM'];
					$nm=$odbc->query("select NMMHSMSMHS from MSMHS.DBF where NIMHSMSMHS='$nim'",false);
					$dataskrs['NAMA']=$nm['NMMHSMSMHS'];
					$mhs[$status][] = $dataskrs;
					$statusr[$status]=$status;
					//print_r($dataskrs);
				}
				//echo "<br/>";
			}
			
			//echo $_GET['prodi']." kdpt :".$kode_pt." <br/>";
			//echo FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt<br/>";
			tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($mhs));
			//sprint_r($mhs);
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
				if($sts=='L') :
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
						  <th width=\"125\">NO IJASAH</th>
						  <th width=\"50\">JALUR</th>
						  <th width=\"50\">JUDUL SKRIPSI</th>
						  <th width=\"50\">TGL YUDISIUM</th>";
					}
					
					echo "<th width=\"400\">KETERANGAN</th></tr>
						</thead>";
						$no=0;
							foreach($dt as $k=>$r){
								$no++;
								echo "\r\n<tr><td>$no</td><td>$r[NIM]</td><td>$r[NAMA]</td><td style=\"text-align:center\">$r[STTS]</td>";
								if($sts=='L'){
									echo "<td>$r[IPK]</td><td>$r[SKS]</td><td>$r[TGLLS]</td><td>$r[NOSKR]</td><td>$r[NOIJAZAH]</td>";
									echo "<td>$r[JLR]</td><td>$r[JDL]</td><td>$r[TGLSKYUD]</td>";
								}
								echo "<td><div id=\"sp_$r[NIM]\">...proses...</div></td></tr>\r\n";
							}
							echo "</table></div><hr>";
				endif;
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
			echo "\r\n<script>\r\n$s('#balikan_mhs').load('".PATH."/index.php?load_lsm_skripsi=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');\r\n</script>";
		}	
	}	
	//make_footer('WebService | Import Excel Data Skripsi');
//}
?>