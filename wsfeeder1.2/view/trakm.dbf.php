<?php
$time_start = microtime_float();
global $kode_pt;
$kode_pt=trim($_SESSION['data_pt']['npsn']);
$id_sp=$_SESSION['data_pt']['id_sp'];
define("FOLDER_DATA",SISTEM_TMP."/$kode_pt/akm");
mkdirs(FOLDER_DATA);
$file_mhs=dirname(FOLDER_DATA)."/mahasiswa-$_GET[prodi].txt";
define("FILE_MHS",$file_mhs);
$tbl_dbf="TRAKM.DBF";
$folder=cek_dbf($tbl_dbf,$kode_pt);
$folder_dbf=$folder[0]; global $odbc; global $odbc2;
$folder_dbf2=str_replace("/","\\",SISTEM_TMP."/$kode_pt/");
$alamat_server=$_SESSION['server_url'];
$odbc=new DBFConnect();
$odbc->connect($folder_dbf);
$odbc2=new DBFConnect();
$odbc2->connect($folder_dbf2);
global $proxy;
$proxy=new PROXY();
$proxy->connect();

$prodi=unserialize(PRODI);
$id_sms=$prodi[$_GET['prodi']]['id_sms'];
$nama_prodi=trim(str_replace("'","",$prodi[$_GET['prodi']]['nm_lemb']));
$id_jenj=$prodi[$_GET['prodi']]['id_jenj_didik'];
$batas=10;

if($_GET['load_ipk']){
	$js=''; $s='$';
	$p=(int)$_GET['p'];
	$reg_pd=json_decode(file_get_contents(SISTEM_TMP."/reg_pd_".$_GET[prodi].$_GET[tahun].".txt"),true);
	$jml=count($reg_pd); 
	$id_reg_pd=$reg_pd[$p];
	
	$p++; 
	$no=$jml-$p;
	$pj=($p / $jml) * 100;
	$pjx=100 - $pj;
	if($p==$jml){
		echo "<script>tutup_dialog();</script>";
		exit;
	}
	echo "Proses : $p / $jml";
	$x=$proxy->GetRecords('mahasiswa_pt',"id_reg_pd='$id_reg_pd'");
	$r=$x['result'];
	$nim=trim($r['nipd']); $nama=str_replace("'","\\'",$r['nm_pd']); $angk=substr($r[mulai_smt],0,4);
	$kls=array();
	$x=$proxy->GetRecordsets('nilai.raw',"id_reg_pd='$id_reg_pd'");
	$res=$x['result']; $idkls=array();
	foreach($res as $k=>$r){
		$kls[$r['id_kls']]['n']=floatval($r['nilai_indeks']);
		$idkls[$r['id_kls']]="'$r[id_kls]'";
	}
	if(count($idkls) >0){
		$x=$proxy->GetRecordsets("kelas_kuliah.raw","p.id_kls in(".implode(",",$idkls).")");
		$res=$x['result']; 
		foreach($res as $k=>$r){
			$kls[$r['id_kls']]['smt']=$r['id_smt'];
			$kls[$r['id_kls']]['sks']=$r['sks_mk'];
		}
	}
	$sksem=0; $ips=0; $skstt=0; $ipk=0; $ns=0; $nt=0;
	if(count($kls) >0){
		foreach($kls as $k=>$r){
			$smt=(int)$r['smt'];
			$n=floatval($r['n']);
			$sks=(int)$r['sks'];
			if($smt <= (int)$_GET['tahun']){
				$skstt=$skstt+$sks;
				$ns=$ns+$n;
			}
			if($smt==(int)$_GET['tahun']){
				$sksem=$sksem + $sks;
				$nt=$nt+$n;
			}
		}
	}
	if($nt >0){
		$ips=$ns/$sksem;
		$ipk=$nt/$skstt;
	}
	$ips=substr($ips,0,4); $ipk=substr($ipk,0,4);
	$dtm=BuatTR($id_reg_pd,$no,$sksem,$ips,$skstt,$ipk,$nama,$nim,$angk);
	$js.="$('#tbodi').prepend('$dtm');\r\n $s('#p_bar3').html('Proses NIM <strong>$nim</strong>'); document.getElementById('p_bar5').style.width='$pjx%';";
	$js.="$s('#balikan_nilai').load('".PATH."/index.php?load_ipk=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]&p=$p');";
	
	echo "<script>$js;</script>";
	
	exit;
}

if((int)$_POST['rp'] > 0){$batas=(int)$_POST['rp'];}
if($_GET['perbaiki']){
	$r=explode(";",$_GET['perbaiki']); $js=''; $s='$';
	$nim=$r[0]; $smt=$r[1]; $pst=$r[2]; $id_reg_pd=$r[3];
	$sqlx="select * from TRAKM.DBF where NIMHSTRAKM='$nim' and KDPSTTRAKM='$pst' and THSMSTRAKM='$smt'";
	$rs=$odbc->query($sqlx,false);
	$ipk=floatval($rs['NLIPKTRAKM']); $ips=floatval($rs['NLIPSTRAKM']); $sks=(int)$rs['SKSEMTRAKM'];
	$c['ips']=$ips; $c['sks_smt']=$sks; $c['ipk']=$ipk;
	$data['key']=array('id_smt'=>$smt,'id_reg_pd'=>$id_reg_pd);
	$data['data']=$c;
	if(count($rs)==1){
		$js.="$s('#ket_$nim').html('<div class=\"abang\">Data tidak ada di tabel TRAKM.DBF</div>');\r\n";
	}else{
		$start=microtime_float();
		$x=$proxy->UpdateRecords('kuliah_mahasiswa',$data);
		$code=(int)$x['result']['error_code']; $err=trim(kompres(str_replace("'","\\'",$x['result']['error_desc'])));
		$end=microtime_float();
		$waktu=substr(($end -$start),0,5);
		if($code > 0){
			$js.="$s('#ket_$nim').html('<div class=\"abang\">$err</div>');\r\n";
		}else{
			$js.="$s('#img_$nim, #btn_$nim').fadeOut();\r\n$s('#ket_$nim').html('Sukses ($waktu detik)');\r\n";
			$js.="$s('#sks_$nim').text('$sks'); $s('#ips_$nim').text('$ips'); $s('#ipk_$nim').text('$ipk');";
		}
	}
	echo "<script>$js</script>";
	exit;
}
if($_GET['ajax_akm']){
	$page=(int)$_POST['page'];
	$th=(int)date("Y"); $smt=array();
	for ($i = 0; $i <= 6; $i++) {
		$tahun=$th -$i;
		$smt[]="'".$tahun.'2'."'";
		$smt[]="'".$tahun.'1'."'";
	}
	$semester=implode(",",$smt);
	$filter="id_smt in($semester)  and ( (sks_smt>30) or (ips>4) or (ipk>4) or (ipk=0 and ips>0))";
	$trakm_order=str_replace("ID_SP",$id_sp,str_replace('REF_SMT',$semester,$trakm_order));
	$trakm_count=str_replace("ID_SP",$id_sp,str_replace('REF_SMT',$semester,$trakm_count));
	$x=$proxy->GetRecordsets("kuliah_mahasiswa",$filter,$trakm_count);
	$jml_data=(int)$x['result'][0]['count'];
	$p=($page -1) * $batas;
	$x=$proxy->GetRecordsets("kuliah_mahasiswa",$filter,$trakm_order,$batas,$p);
	$mhs=$x['result']; $idregpd=array();
	$jsonData = array('page'=>$page,'total'=>$jml_data,'rows'=>array());
	$no=$p;
	foreach($mhs as $k=>$r){
		$no++;
		$smt=$r[id_smt];
		$ips=$r[ips];
		$id_reg_pd=$r['id_reg_pd'];
		$ipk=$r[ipk];
		$skstt=$r['sks_total'];
		$sksem=$r['sks_smt'];
		$nim=trim($r['nipd']);
		$nama=$r['nm_pd'];
		$id_sms=$r['id_sms'];
		$krs=(int)$r['krs'];
		$angk=substr($r['mulai_smt'],0,4);
		$nmpst=$prodi[$id_sms]['nm_lemb'];
		$kode_pst=$prodi[$id_sms]['kode_prodi'];
		$ket="<img id=\"img_$nim\" class=\"ket\" src=\"".$_SESSION['server_url']."/application/assets/images/error.png\">";
		$aksi="<button id=\"btn_$nim\" onClick=\"perbaiki('".urlencode("$nim;$smt;$kode_pst;$id_reg_pd")."');\" type=\"button\" class=\"kecil btn btn-small btn-primary\">Perbaiki</button>";
		$res="<span id=\"ket_$nim\"></span>";
		$rs=array('no'=>$no,'nim'=>$nim,'nama'=>$nama,'smt'=>$smt,'prodi'=>$nmpst,'sks'=>"<span id=\"sks_$nim\">$sks</span>",'ips'=>"<span id=\"ips_$nim\">$ips</span>",'ipk'=>"<span id=\"ipk_$nim\">$ipk</span>",'angk'=>$angk,'ket'=>$ket,'aksi'=>$aksi,'res'=>$res,'krs'=>$krs);
		$entry=array('id'=>"cell_".$nim,'cell'=>$rs);
		$jsonData['rows'][] = $entry;
	}
	echo json_encode($jsonData);
	exit;
}

if($_GET['validasi']){
	echo "\r\n<div id=\"balikan\"></div>\r\n<style>\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n.kecil{height:20px!important; padding-top:0px!important}\r\n.ket{width:20px}\r\n.pcontrol input{width:20px!important;}\r\n.pGroup select{width:50px!important;}</style>\r\n";
	$t="NO,no,20,right;NIM,nim,100,left;NAMA,nama,150,left;ANGK,angk,30,left;PRODI,prodi,150,left;SMTR,smt,30,left;";
	$t.="SKS > <br>30sks,sks,50,right;IPS <br>Lebih 4,ips,50,right; IPK <br>Lebih 4,ipk,50,right;KRS,krs,50,right;KET,ket,50,center;AKSI,aksi,100,center;RESULT,res,200,left";
	$tabel=array('id'=>'tbl_mhs','url'=>PATH."/index.php/trakm.dbf?ajax_akm=1",'fungsi'=>'','data'=>$t,'title'=>'','page'=>true,'rp'=>$batas);
	make_heading('Cek Validasi AKM di FEEDER','');
	make_tabel($tabel); $s='$';
	echo "<script>function perbaiki(id){"."$s('#balikan').load('?perbaiki='+id);}</script>";
	make_footer('Validator AKM');
	exit;
}

function cek_data($r,$feeder){
	$sama=1; $bal=array(); $s='$';
	$nim=$r['NIMHSTRAKM']; $js='';
	if(floatval($r['NLIPSTRAKM'])!=floatval($feeder['ips'])){$sama=0; $js.="$('#ips_$nim, #ips2_$nim').addClass('ijo');\r\n";}
	if((int)$r['SKSEMTRAKM']!=(int)$feeder['sks_smt']){$sama=0; $js.="$('#sksem_$nim, #sksem2_$nim').addClass('kuning');\r\n";}
	if(floatval($r['NLIPKTRAKM'])!=floatval($feeder['ipk'])){$sama=0; $js.="$('#ipk_$nim, #ipk2_$nim').addClass('oranye');\r\n";}
	if((int)$r['SKSTTTRAKM']!=(int)$feeder['sks_total']){$sama=0; $js.="$('#skstt_$nim, #skstt2_$nim').addClass('biru2');\r\n";}
	$bal=array('sama'=>$sama,'js'=>$js);
	return $bal;
}
if($_GET['load_aktifitas']){
	$dtx=array();
	$dta=json_decode(file_get_contents(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt"),true);
	foreach($dta as $k=>$r){
		$nim=$r['NIMHSTRAKM'];
		$dtx[$nim]=$r;
	}
	foreach($dtx as $k=>$r){
		$data[]=$r;
	}
	$mhs=array(); $reg_pd_array=array(); $akm=array(); $mhs2=array();
	foreach($data as $k=>$r){
		$mhs[$r['NIMHSTRAKM']]="'".trim($r['NIMHSTRAKM'])."'";
	}
	$filter="trim(nipd) in(".implode(",",$mhs).")";
	unset($mhs);
	$s='$'; $js='';
	$x=$proxy->GetRecordsets("mahasiswa_pt.raw",$filter);
	$res=$x['result'];
	foreach($res as $k=>$r){
		$mhs[trim($r['nipd'])]=$r['id_reg_pd'];
		$reg_pd_array[]="'".$r['id_reg_pd']."'";
		$mhs[$r['id_reg_pd']]=trim($r['nipd']);
	}
	$filter="id_smt='$_GET[tahun]' and id_reg_pd in(".implode(",",$reg_pd_array).")";
	$x=$proxy->GetRecordsets("kuliah_mahasiswa",$filter);
	$res=$x['result'];
	foreach($res as $k=>$r){
		$id_reg_pd=$r['id_reg_pd'];
		$akm[$id_reg_pd]=$r;
		$nim=$mhs[$id_reg_pd];
		$ips=$r['ips']; $sksem=$r['sks_smt'];
		$ipk=$r['ipk']; $skstt=$r['sks_total'];
		$js.="$s('#ipk_$nim').text('$ipk');\r\n";
		$js.="$s('#ips_$nim').text('$ips');\r\n";
		$js.="$s('#sksem_$nim').text('$sksem');\r\n";
		$js.="$s('#skstt_$nim').text('$skstt');\r\n";
	}	
	$insert=array(); $update=array(); $index_update=array();
	$index_insert=array();
	
	foreach($data as $k=>$r){
		$nim=trim($r['NIMHSTRAKM']);
		$id_reg_pd=$mhs[$nim];
		if($id_reg_pd!=''){
			$c=array(); $dt=array(); $cc=array();
			$feeder=array();
			$feeder=$akm[$id_reg_pd];
			$c['ips']=$r['NLIPSTRAKM'];
			$c['sks_smt']=(int)$r['SKSEMTRAKM'];
			$c['ipk']=$r['NLIPKTRAKM'];
			$c['sks_total']=(int)$r['SKSTTTRAKM'];
			$c['id_stat_mhs']='A';
			
			$link="<a target=\"_blank\" href=\"$alamat_server/kuliahmhs/detail/id_reg_pd:$id_reg_pd"."___id_smt:$_GET[tahun]\">$nim</a>";
			$js.="$s('#sp_$nim').html('$link');\r\n";
			if(count($feeder) > 0){
				$cek=cek_data($r,$feeder);
				$js.=$cek['js'];
				if($cek['sama']==0){
					$dt['key']=array('id_smt'=>$_GET['tahun'],'id_reg_pd'=>$id_reg_pd);
					$dt['data']=$c;
					$index_update[]=$nim;
					$update[]=$dt;
				}else{
					$js.="$s('#ket_$nim').text('Tidak ada perubahan data');\r\n";
				}
			}else{
				$c['id_reg_pd']=$id_reg_pd;
				$c['id_smt']=$_GET['tahun'];
				$insert[]=$c;
				$index_insert[]=$nim;
			}
		}else{
			$js.="$s('#ket_$nim').text('NIM Tidak terdaftar di feeder');\r\n";
		}
	}
	if(count($insert)>0){
		$x=$proxy->InsertRecordsets('kuliah_mahasiswa',$insert);
		$res=$x['result'];
		foreach($res as $k=>$v){
			$nim=$index_insert[$k];
			$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
			$js.="$s('#ket_$nim').html($s('#ket_$nim').html()+' <span style=\"background-color:#00FFFF\">Mencoba Insert ...</span>'); ";
			if($err!=''){
				$js.="$s('#ket_$nim').html($s('#ket_$nim').text()+'<div class=\"abang\">$err</div>'); window.location='#a_$nim';\r\n";
			}else{
				$js.="$s('#ket_$nim').html($s('#ket_$nim').html()+'<br>Sukses diInsert'); ";
			}
		}
	}
	if(count($update) >0){
		$x=$proxy->UpdateRecordsets('kuliah_mahasiswa',$update);
		$res=$x['result'];
		foreach($res as $k=>$v){
			$nim=$index_update[$k];
			$err=trim(kompres(str_replace("'","\\'",$v['error_desc'])));
			$js.="$s('#ket_$nim').html($s('#ket_$nim').html()+' <span style=\"background-color:#CCFF00\">Mencoba Update ...</span>'); ";
			if($err!=''){
				$js.="$s('#ket_$nim').html($s('#ket_$nim').html()+'<div class=\"abang\">$err</div>'); window.location='#a_$nim';\r\n";
			}else{
				$js.="$s('#ket_$nim').html($s('#ket_$nim').html()+'<br>Sukses diupdate'); ";
			}
		}
	}
	echo "<script>\r\n$js</script>";
	echo "<script>$s( \"#detail_mhs\" ).dialog(\"close\");</script>";
	exit;
}

make_heading('Aktifitas Kuliah Mahasiswa','Mengimpor data dari TRAKM.DBF');
echo "\r\n<style>\r\n.satu{width:1px!important}\r\n.biru2{background-color:#00FFFF!important}\r\n.oranye{background-color:#FF9900!important}\r\n.kuning{background-color:#FFFF00!important}\r\n.ijo{background-color:#66FF00!important}\r\n.abang{background-color:#FF0000!important; color:#FFFFFF!important; padding:0px 5px 0px 5px!important}\r\n\r\n#p_bar{width:100%; height:25px}\r\n.merah td,.merah span{background-color:#FF0000!important; color:#FFFFFF!important}\r\n.dlg{text-align:center;}</style>\r\n<form name=\"form1\" method=\"get\" action=\"\" id=\"form1\">
  Program Studi
  <select name=\"prodi\" id=\"prodi\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>";
  	list_prodi();
echo "</select>
<input type=\"hidden\" name=\"generate_ip\" value=\"$_GET[generate_ip]\">
  Semester
  <select name=\"tahun\" id=\"tahun\" style=\"width:170px\" onChange=\"document.getElementById('form1').submit();\">
    <option></option>"; list_smtr($_GET['tahun']);
echo "  </select>
</form><div id=\"balikan_nilai\"></div>";
if($_GET['prodi']){
	if($_GET['tahun']){
		if($_GET['generate_ip']){
			generate_ip();
		}else{
echo "<div style=\"width:1000px\">
<table class=\"table table-striped table-condensed\">
  <thead>
  <tr>
      <th rowspan=\"2\" style=\"width:20px\">No.</th>
      <th rowspan=\"2\" style=\"text-align:center\" >NIM </th>
      <th rowspan=\"2\" style=\"text-align:center\" >Nama Mahasiswa </th>
      <th rowspan=\"2\" style=\"text-align:center\" >Angkatan </th>
      <th rowspan=\"2\" style=\"text-align:center\" >Status </th>
	  <th class=\"satu\" rowspan=\"2\" style=\"background-color:#1a69b6\">&nbsp;</th>
      <th colspan=\"4\" style=\"text-align:center\" >DBF</th>
      <th class=\"satu\" style=\"background-color:#1a69b6\">&nbsp;</th>
      <th colspan=\"4\" style=\"text-align:center\" >FEEDER</th>
	   <th colspan=\"2\" rowspan=\"2\" style=\"text-align:center\">KETERANGAN</th>
    </tr>
    <tr>
      <th style=\"text-align:center\" >IPS </th>
      <th style=\"text-align:center\" >IPK </th>
      <th style=\"text-align:center\" >SKSSEM</th>
      <th style=\"text-align:center\" >SKSTT</th>
	   <th class=\"satu\" style=\"background-color:#1a69b6\">&nbsp;</th>
	   <th style=\"text-align:center\" >IPS </th>
      <th style=\"text-align:center\" >IPK </th>
      <th style=\"text-align:center\" >SKSSEM</th>
      <th style=\"text-align:center\" >SKSTT</th>
    </tr>
  </thead>
  <tbody>";

$no=0; $mhs=array();
$sqlx="select a.NIMHSTRAKM,a.SKSEMTRAKM,a.NLIPSTRAKM,a.SKSTTTRAKM,a.NLIPKTRAKM,b.NMMHSMSMHS,b.TAHUNMSMHS from (TRAKM.DBF a INNER JOIN MSMHS.DBF b ON(b.NIMHSMSMHS=a.NIMHSTRAKM)) where KDPTITRAKM='$kode_pt' and KDPSTTRAKM='$_GET[prodi]' and THSMSTRAKM='$_GET[tahun]'";
$sql=$odbc->query($sqlx);
$sq=array();
if(is_array($sql) && count($sql)>0){
	foreach($sql as $k=>$rs){
		$nim=$rs['NIMHSTRAKM'];
		$sq[$nim]=$rs;
	}
	unset($sql);
	foreach($sq as $k=>$rs){
		$sql[]=$rs;
	}
}
if(count($sql) >0){aasort($sql,'NIMHSTRAKM');}
foreach($sql as $k=>$rs){
  	$no++;
	$r=array(); $r=$rs;
	$nim=$r['NIMHSTRAKM'];
	$r['NLIPSTRAKM']=floatval($rs['NLIPSTRAKM']);
	$r['NLIPKTRAKM']=floatval($rs['NLIPKTRAKM']);
	$r['SKSEMTRAKM']=floatval($rs['SKSEMTRAKM']);
	$r['SKSTTTRAKM']=floatval($rs['SKSTTTRAKM']);
	$mhs[]=$r;
	echo "<tr>
      <td><a name=\"a_$nim\"></a>$no</td>
      <td style=\"text-align:center\"><span id=\"sp_$nim\">$nim</span></td>
      <td style=\"white-space: nowrap;\">$r[NMMHSMSMHS]</td>
      <td style=\"text-align:center\">$r[TAHUNMSMHS]</td>
      <td style=\"text-align:center\">AKTIF </td>
	  <td class=\"satu\" style=\"background-color:#1a69b6\">&nbsp;</td>
      <td style=\"text-align:center\" id=\"ips2_$nim\">$r[NLIPSTRAKM]</td>
      <td style=\"text-align:center\" id=\"ipk2_$nim\">$r[NLIPKTRAKM]</td>
      <td style=\"text-align:right\" id=\"sksem2_$nim\">$r[SKSEMTRAKM]</td>
      <td style=\"text-align:right\" id=\"skstt2_$nim\">$r[SKSTTTRAKM]</td>
	  <td class=\"satu\" style=\"background-color:#1a69b6\">&nbsp;</td>
	  <td style=\"text-align:center\" id=\"ips_$nim\">&nbsp;</td>
	  <td style=\"text-align:center\" id=\"ipk_$nim\">&nbsp;</td>
	  <td style=\"text-align:right\" id=\"sksem_$nim\">&nbsp;</td>
	  <td style=\"text-align:right\" id=\"skstt_$nim\">&nbsp;</td>
	  <td class=\"satu\" style=\"background-color:#1a69b6\">&nbsp;</td>
	  <td><div style=\"width:300px\" id=\"ket_$nim\"></div></td>
    </tr>";
 } 
tulis_data(FOLDER_DATA."/$_GET[prodi]-$_GET[tahun].txt",json_encode($mhs));
echo " </tbody>
</table>
</div>";
echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div><div align=\"center\" id=\"p_bar\"><img src=\"".PATH."/app/images/ajax-loader.gif\"></div></div>\r\n";
$s='$';
if($no > 0){
	echo "\r\n<script>\r\nvar itg=0;\r\n$s(function() {
			$s( \"#detail_mhs\" ).dialog({
				height: 100,
				width:'450',
				modal: true,
				title: 'Wait'
			});
		});
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
	echo "\r\n\r\n<script>\r\n$s('#balikan_nilai').load('".PATH."/index.php?load_aktifitas=1&prodi=$_GET[prodi]&tahun=$_GET[tahun]');\r\n</script>";
}}
	}
}

?>
