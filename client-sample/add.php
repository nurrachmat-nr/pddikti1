<?php error_reporting(0); ?>
<html>
    <head>
        <title>WS - Insert Record</title>
		<link rel="stylesheet" href="print.css" type="text/css" />
    </head>
<?php

include('initsoap.php');
include('function.php');

?>

<h1>Simulasi Insert Record</h1>

<table class="data_grid">
	<tr>
		<th>Entitas</th>
	</tr>
	<tr>
		<td><a href="add.php?entitas=mahasiswa">Mahasiswa</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=mahasiswa_pt">Mahasiswa PT</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=dosen">Dosen</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=dosen_pt">Dosen PT</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=mata_kuliah">Mata Kuliah</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=substansi_kuliah">Substansi Kuliah</a></td>
	</tr>
	<tr>
		<td><a href="add.php?entitas=kurikulum">Kurikulum</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=kelas_kuliah">Kelas Kuliah</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=kuliah_mahasiswa">Kuliah Mahasiswa</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=mata_kuliah_kurikulum">Mata Kuliah Kurikulum</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=ajar_dosen">Ajar Dosen</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=bobot_nilai">Bobot Nilai</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=daya_tampung">Daya Tampung</a></td>
	</tr>	
	<tr>
		<td><a href="add.php?entitas=nilai">Nilai</a></td>
	</tr>	
</table>

<br/>

<a href="index.php">Kembali</a><br/><br/>

<form method="get" id="form1">
<input type="hidden" name="entitas" value="<?= $_REQUEST['entitas'] ?>" />

<?php

if ($_REQUEST['entitas'] == 'mahasiswa') {
	$rows = array(
		array('nm_pd'=>'Si Joni', 'tgl_lahir'=>'1990-01-15', 'jk'=>'L', 'stat_pd'=>'A', 'nm_ibu_kandung'=>'Ibunya Joni'),
		array('nm_pd'=>'Si Doel', 'tgl_lahir'=>'1990-01-16', 'jk'=>'L', 'stat_pd'=>'A', 'nm_ibu_kandung'=>'Ibunya Doel'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sp = $proxy->GetRecord($token, 'satuan_pendidikan', "nm_lemb ilike '%{$nama_pt}%'");
		
		$record['id_kk'] = 0;
		$record['id_sp'] = $sp['result']['id_sp'];
		$record['ds_kel'] = '-';
		$record['id_wil'] = '999999';
		$record['id_agama'] = rand(1, 5);
		$record['kewarganegaraan'] = 'ID';
		$record['a_terima_kps'] = '0';
		$record['id_kebutuhan_khusus_ayah'] = 0;
		$record['id_kebutuhan_khusus_ibu'] = 0;
		
		$records[] = $record;
	}
		
	echoDataMasuk($records);

	if ($_REQUEST['act'] == 'insert_record') {
		$i=0;
		foreach ($records as $record) {
			$result = $proxy->InsertRecord($token, $_REQUEST['entitas'], json_encode($record));
			echoInsertRecord($result);
		}
	}	
	elseif ($_REQUEST['act'] == 'insert_recordset') {
		$result = $proxy->InsertRecordset($token, $_REQUEST['entitas'], json_encode($records));
		echoInsertRecordset($result);
	}	
}
elseif ($_REQUEST['entitas'] == 'mahasiswa_pt') {
	$result = $proxy->GetRecordset($token, 'mahasiswa', "nm_pd = 'Si Joni' or nm_pd = 'Si Doel'", 'nm_pd asc', 10);

	$records = array();
	echo '<table class="data_grid">';
	$i = 0;
	foreach ($result['result'] as $row) {
		$i++;
		$record = array();
		
		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sp = $proxy->GetRecord($token, 'satuan_pendidikan', "nm_lemb ilike '%{$nama_pt}%'");
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		
		$record['nipd'] = 'nipd_'.$i;
		$record['id_pd'] = $row['id_pd'];
		$record['id_sp'] = $sp['result']['id_sp'];
		$record['id_sms'] = $sms['result']['id_sms'];
		$record['id_jns_daftar'] = 1;
		$record['tgl_masuk_sp'] = '2014-09-01';
		$record['a_pernah_paud'] = 1;
		$record['a_pernah_tk'] = 1;
		
		$records[] = $record;
	}
		
	submitAct($proxy, $records, $token);

}

elseif ($_REQUEST['entitas'] == 'dosen') {
	$rows = array(
		array('nm_ptk'=>'Si Dumang', 'tgl_lahir'=>'1990-01-15', 'jk'=>'L', 'nm_ibu_kandung'=>'Ibunya Dumang'),
		array('nm_pd'=>'Si Dodol', 'tgl_lahir'=>'1990-01-16', 'jk'=>'L', 'nm_ibu_kandung'=>'Ibunya Dodol'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sp = $proxy->GetRecord($token, 'satuan_pendidikan', "nm_lemb ilike '%{$nama_pt}%'");
		
		$record['id_sp'] = $sp['result']['id_sp'];
		$record['ds_kel'] = '-';
		$record['id_wil'] = '999999';
		$record['id_agama'] = rand(1, 5);
		$record['kewarganegaraan'] = 'ID';
		$record['a_terima_kps'] = '0';
		$record['id_kebutuhan_khusus_ayah'] = 0;
		$record['id_kebutuhan_khusus_ibu'] = 0;
		
		$records[] = $record;
	}
		
	submitAct($proxy, $records, $token);

}
elseif ($_REQUEST['entitas'] == 'dosen_pt') {
	$result = $proxy->GetRecordset($token, 'dosen_pt', "nm_ptk ilike '%budi%'", 'nm_ptk asc', 5);

	$records = array();
	echo '<table class="data_grid">';
	$i = 0;
	foreach ($result['result'] as $row) {
		$i++;
		$record = array();
		
		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sp = $proxy->GetRecord($token, 'satuan_pendidikan', "nm_lemb ilike '%{$nama_pt}%'");
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		
		$record['id_ptk'] = $row['id_ptk'];
		$record['id_sp'] = $sp['result']['id_sp'];
		$record['id_sms'] = $sms['result']['id_sms'];
		
		$records[] = $record;
	}
		
	submitAct($proxy, $records, $token);

}

elseif ($_REQUEST['entitas'] == 'mata_kuliah') {
	$rows = array(
		array('kode_mk'=>'hitung1', 'nm_mk'=>'Berhitung mudah', 'id_jenj_didik'=>'30'),
		array('kode_mk'=>'tulis1', 'nm_mk'=>'Menulis mudah', 'id_jenj_didik'=>'30'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);

}
elseif ($_REQUEST['entitas'] == 'substansi_kuliah') {
	$rows = array(
		array('nm_subst'=>'Substansi A', 'id_jns_subst'=>'1'),
		array('nm_subst'=>'Substansi B', 'id_jns_subst'=>'1'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'kurikulum') {
	$rows = array(
		array('nm_kurikulum_sp'=>'Kurikulum A', 'id_jenj_didik'=>'30'),
		array('nm_kurikulum_sp'=>'Kurikulum B', 'id_jenj_didik'=>'30'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		$record['jml_sem_normal'] = rand(1,5);
		$record['jml_sks_lulus'] = rand(1,5);
		$record['jml_sks_wajib'] = rand(1,5);
		$record['jml_sks_pilihan'] = rand(1,5);
		$record['id_smt_berlaku'] = '20141';
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'kelas_kuliah') {
	$temp = $proxy->GetRecord($token, 'mata_kuliah', "nm_mk ilike '%hitung%'");
	$id_mk1 = $temp['result']['id_mk'];

	$temp = $proxy->GetRecord($token, 'mata_kuliah', "nm_mk ilike '%nulis%'");
	$id_mk2 = $temp['result']['id_mk'];

	$rows = array(
		array('id_mk'=>$id_mk1, 'nm_kls'=>'KLSA', 'id_smt'=>'20141'),
		array('id_mk'=>$id_mk2, 'nm_kls'=>'KLSB', 'id_smt'=>'20141'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		$record['sks_mk'] = rand(1,5);
		$record['sks_tm'] = rand(1,5);
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'kuliah_mahasiswa') {
	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd = 'Si Joni'");
	$id_reg_pd1 =  $temp['result']['id_reg_pd'];

	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd = 'Si Doel'");
	$id_reg_pd2 =  $temp['result']['id_reg_pd'];
	
	$rows = array(
		array('id_reg_pd'=>$id_reg_pd1, 'id_smt'=>'20141'),
		array('id_reg_pd'=>$id_reg_pd2, 'id_smt'=>'20141'),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
	
		$record['sks_smt'] = rand(1,5);
		$record['ipk'] = rand(1,5);
		$record['id_stat_mhs'] = 'A';
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'mata_kuliah_kurikulum') {
	$temp = $proxy->GetRecord($token, 'kurikulum', "nm_kurikulum_sp ilike '%kurikulum a%'");
	$id_kurikulum_sp1 =  $temp['result']['id_kurikulum_sp'];

	$temp = $proxy->GetRecord($token, 'kurikulum', "nm_kurikulum_sp ilike '%kurikulum b%'");
	$id_kurikulum_sp2 =  $temp['result']['id_kurikulum_sp'];

	$temp = $proxy->GetRecord($token, 'mata_kuliah', "nm_mk ilike '%hitung%'");
	$id_mk1 = $temp['result']['id_mk'];

	$temp = $proxy->GetRecord($token, 'mata_kuliah', "nm_mk ilike '%nulis%'");
	$id_mk2 = $temp['result']['id_mk'];
	
	$rows = array(
		array('id_kurikulum_sp'=>$id_kurikulum_sp1, 'id_mk'=>$id_mk1),
		array('id_kurikulum_sp'=>$id_kurikulum_sp2, 'id_mk'=>$id_mk2),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
	
	
		$record['smt'] = rand(1,2);
		$record['sks_mk'] = rand(1,5);
		$record['sks_tm'] = rand(1,5);		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}

elseif ($_REQUEST['entitas'] == 'ajar_dosen') {
	$temp = $proxy->GetRecord($token, 'dosen_pt', "nm_ptk ilike '%budisetyo%'");
	$id_reg_ptk1 = $temp['result']['id_reg_ptk'];

	$temp = $proxy->GetRecord($token, 'dosen_pt', "nm_ptk ilike '%agustinus%'");
	$id_reg_ptk2 = $temp['result']['id_reg_ptk'];
	
	$temp = $proxy->GetRecord($token, 'kelas_kuliah', "nm_kls ilike '%klsa%'");
	$id_kls1 = $temp['result']['id_kls'];

	$temp = $proxy->GetRecord($token, 'kelas_kuliah', "nm_kls ilike '%klsb%'");
	$id_kls2 = $temp['result']['id_kls'];

	$rows = array(
		array('id_reg_ptk'=>$id_reg_ptk1, 'id_kls'=>$id_kls1),
		array('id_reg_ptk'=>$id_reg_ptk2, 'id_kls'=>$id_kls2),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$record['sks_subst_tot'] = rand(1,5);
		$record['sks_tm_subst'] = rand(1,5);
		$record['jml_tm_renc'] = rand(1,10);
		$record['id_jns_eval'] = rand(1,10);
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'bobot_nilai') {
	$rows = array(
	1,2
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		$record['nilai_huruf'] = 'A';
		$record['bobot_nilai_min'] = rand(20,90);
		$record['bobot_nilai_maks'] = 100;
		$record['tgl_mulai_efektif'] = date('Y-m-d');
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);

}
elseif ($_REQUEST['entitas'] == 'daya_tampung') {
	$rows = array(
		array('id_smt'=>'20141'),
		array('id_smt'=>'20142'),
	);
	$records = array();
	foreach ($rows as $row) {
		$record = array();
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}
		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
		$record['id_sms'] = $sms['result']['id_sms'];
		$record['target_mhs_baru'] = rand(20,90);
		$record['calon_ikut_seleksi'] = rand(20,90);
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);

}
elseif ($_REQUEST['entitas'] == 'nilai') {
	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd ilike '%si joni%'");
	$id_reg_pd1 = $temp['result']['id_reg_pd'];

	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd ilike '%si doel%'");
	$id_reg_pd2 = $temp['result']['id_reg_pd'];
	
	$temp = $proxy->GetRecord($token, 'kelas_kuliah', "nm_kls ilike '%klsa%'");
	$id_kls1 = $temp['result']['id_kls'];

	$temp = $proxy->GetRecord($token, 'kelas_kuliah', "nm_kls ilike '%klsb%'");
	$id_kls2 = $temp['result']['id_kls'];

	$rows = array(
		array('id_reg_pd'=>$id_reg_pd1, 'id_kls'=>$id_kls1),
		array('id_reg_pd'=>$id_reg_pd2, 'id_kls'=>$id_kls2),
	);

	$records = array();
	foreach ($rows as $row) {
		$record = array();
		
		foreach ($row as $k=>$v) {
			$record[$k] = $v;
		}

		# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
		# untuk contoh simulasi beberapa sementara diisi manual
		
		$record['nilai_angka'] = rand(10,100);
		$record['nilai_huruf'] = 'C';
		$record['asal_data'] = 9;
		
		$records[] = $record;
	}

	submitAct($proxy, $records, $token);
}


function echoDataMasuk($records) {
	echo '<br/><br/>';
	echo "<h2>Data {$_REQUEST['entitas']} yang akan dimasukkan</h2>";

	echo '<table class="data_grid">';
	foreach ($records as $row) {
		if (!$i) {
			echo '<tr>';
			echo '<th>No</th>';
			foreach(array_keys($row) as $k=>$v){
				echo '<th>';
				echo $v;
				echo '</th>';
			}
			echo '</tr>';
		}
		echo '<tr>';
		$i++;

		$style='';
		foreach($row as $k=>$v){
			if (strtolower($k) == 'soft_delete' && $v == '1') {
				$style='style="text-decoration:line-through"';
			}
		}

		echo "<td $style >$i.</td>";
		foreach($row as $k=>$v){
			echo "<td $style>";
			echo $v;
			echo '&nbsp;</td>';
		}
		echo '</tr>';    
	}
	echo '</table>';

	echo '<br/>
	<input type="hidden" name="act" id="act">
	<input type="button" value="InsertRecord" onclick="document.getElementById(\'act\').value=\'insert_record\';document.getElementById(\'form1\').submit();"> <input type="button" value="InsertRecordset" onclick="document.getElementById(\'act\').value=\'insert_recordset\';document.getElementById(\'form1\').submit();">
	</form>';	
	
}

function echoInsertRecord($result) {
	if ($result['error_desc']) {
		echo $result['error_desc'] . '<br/>';
		return false;
	}
	
	$result['result'] = array($result['result']);
		
	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {		
		echo '<tr>';
		echo '<th width="30">No</th>';
		foreach(array_keys($row) as $k=>$v){
			echo '<th width="200">';
			echo $v;
			echo '</th>';
		}
		echo '</tr>';

		echo '<tr>';
		$i++;

		$style='';
		foreach($row as $k=>$v){
			if (strtolower($k) == 'soft_delete' && $v == '1') {
				$style='style="text-decoration:line-through"';
			}
		}

		echo "<td $style >$i.</td>";
		foreach($row as $k=>$v){
			echo "<td $style>";
			if ($k == 'data') {
				foreach ($v as $k2=>$v2) {
					echo $k2 . ': ' . $v2 . '<br/>';
				}
			}
			else
				echo $v;
			echo '&nbsp;</td>';
		}
		echo '</tr>';    
	}
	echo '</table>';
}

function echoInsertRecordset($result) {
	if ($result['error_desc']) {
		echo $result['error_desc'] . '<br/>';
		return false;
	}

	$i=0;
	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		if (!$i) {
			echo '<tr>';
			echo '<th>No</th>';
			foreach(array_keys($row) as $k=>$v){
				echo '<th>';
				echo $v;
				echo '</th>';
			}
			echo '</tr>';
		}
		echo '<tr>';
		$i++;

		$style='';
		foreach($row as $k=>$v){
			if (strtolower($k) == 'soft_delete' && $v == '1') {
				$style='style="text-decoration:line-through"';
			}
		}

		echo "<td $style >$i.</td>";
		foreach($row as $k=>$v){
			echo "<td $style>";
			if ($k == 'data') {
				foreach ($v as $k2=>$v2) {
					echo $k2 . ': ' . $v2 . '<br/>';
				}
			}
			else
				echo $v;
			echo '&nbsp;</td>';
		}
		echo '</tr>';    
	}
	echo '</table>';
}

function submitAct($proxy, $records, $token) {
	echoDataMasuk($records);

	if ($_REQUEST['act'] == 'insert_record') {
		$i=0;
		foreach ($records as $record) {
			$result = $proxy->InsertRecord($token, $_REQUEST['entitas'], json_encode($record));
			echoInsertRecord($result);
		}
	}	
	elseif ($_REQUEST['act'] == 'insert_recordset') {
		$result = $proxy->InsertRecordset($token, $_REQUEST['entitas'], json_encode($records));
		echoInsertRecordset($result);
	}	
}

if (!$proxy->request)
	exit;

echo '<br/><br/><br/><br/>';
echo '<h2>Web Service Raw</h2>';
echo '<div style="font-family:courier new">';
echo '<h2>Request</h2>';
echo nl2br(htmlentities(html_entity_decode($proxy->request), ENT_NOQUOTES));

echo '<br/><br/>';

echo '<h2>Response</h2>';
echo nl2br(htmlentities(html_entity_decode($proxy->response), ENT_NOQUOTES));
