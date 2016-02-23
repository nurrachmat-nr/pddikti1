<?php error_reporting(0); ?>
<html>
    <head>
        <title>WS - Delete Record</title>
		<link rel="stylesheet" href="print.css" type="text/css" />
    </head>
<?php

include('initsoap.php');
include('function.php');

?>

<h1>Simulasi Delete Record</h1>

<table class="data_grid">
	<tr>
		<th>Entitas</th>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=mahasiswa" >Mahasiswa</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=mahasiswa_pt" >Mahasiswa PT</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=dosen" >Dosen</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=dosen_pt" >Dosen PT</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=mata_kuliah" >Mata Kuliah</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=substansi_kuliah" >Substansi Kuliah</a></td>
	</tr>
	<tr>
		<td><a href="delete.php?entitas=kurikulum" >Kurikulum</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=kelas_kuliah">Kelas Kuliah</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=kuliah_mahasiswa">Kuliah Mahasiswa</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=mata_kuliah_kurikulum">Mata Kuliah Kurikulum</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=ajar_dosen">Ajar Dosen</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=bobot_nilai">Bobot Nilai</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=daya_tampung">Daya Tampung</a></td>
	</tr>	
	<tr>
		<td><a href="delete.php?entitas=nilai">Nilai</a></td>
	</tr>	
</table>

<br/>

<a href="index.php">Kembali</a><br/><br/>

<form method="get" id="form1">
<input type="hidden" name="entitas" value="<?= $_REQUEST['entitas'] ?>" />

<?php

if ($_REQUEST['entitas'] == 'mahasiswa') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_pd = 'Si Joni' or nm_pd = 'Si Doel'", 'nm_pd asc', 10);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_pd'=>$row['id_pd']);
	}
	echo '</table>';
	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'mahasiswa_pt') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_pd = 'Si Joni' or nm_pd = 'Si Doel'", 'nm_pd asc', 10);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_reg_pd'=>$row['id_reg_pd']);
	}
	echo '</table>';
	submitAct($proxy, $records, $token);
}

elseif ($_REQUEST['entitas'] == 'dosen') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_ptk ilike '%budi%'", 'nm_ptk asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_ptk'=>$row['id_ptk']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'dosen_pt') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_ptk ilike '%budi%'", 'nm_ptk asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_reg_ptk'=>$row['id_reg_ptk']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'mata_kuliah') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_mk ilike '%berhitung%' or nm_mk ilike '%menulis%'", 'nm_mk asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_mk'=>$row['id_mk']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'substansi_kuliah') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_subst ilike '%sub%'", 'nm_subst asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_subst'=>$row['id_subst']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'kurikulum') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_kurikulum_sp ilike '%kurikulum%'", 'nm_kurikulum_sp asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_kurikulum_sp'=>$row['id_kurikulum_sp']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}

elseif ($_REQUEST['entitas'] == 'kelas_kuliah') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "nm_kls ilike '%kls%'", 'nm_kls asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_kls'=>$row['id_kls']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'kuliah_mahasiswa') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "id_smt ='20141'", 'id_smt asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_reg_pd'=>$row['id_reg_pd'], 'id_smt'=>$row['id_smt']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'mata_kuliah_kurikulum') {
	$temp = $proxy->GetRecord($token, 'kurikulum', "nm_kurikulum_sp ilike '%kurikulum a%'");
	$id_kurikulum_sp1 =  $temp['result']['id_kurikulum_sp'];

	$temp = $proxy->GetRecord($token, 'kurikulum', "nm_kurikulum_sp ilike '%kurikulum b%'");
	$id_kurikulum_sp2 =  $temp['result']['id_kurikulum_sp'];

	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "p.id_kurikulum_sp ='$id_kurikulum_sp1' or p.id_kurikulum_sp ='$id_kurikulum_sp2'", 'id_mk asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_kurikulum_sp'=>$row['id_kurikulum_sp'], 'id_mk'=>$row['id_mk']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'ajar_dosen') {
	$temp = $proxy->GetRecord($token, 'dosen_pt', "nm_ptk ilike '%budisetyo%'");
	$id_reg_ptk1 = $temp['result']['id_reg_ptk'];

	$temp = $proxy->GetRecord($token, 'dosen_pt', "nm_ptk ilike '%agustinus%'");
	$id_reg_ptk2 = $temp['result']['id_reg_ptk'];

	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "p.id_reg_ptk ='$id_reg_ptk1' or p.id_reg_ptk ='$id_reg_ptk2'", 'id_reg_ptk asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_ajar'=>$row['id_ajar']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'bobot_nilai') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "", 'last_update desc', 2);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('kode_bobot_nilai'=>$row['kode_bobot_nilai']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'daya_tampung') {
	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$sms = $proxy->GetRecord($token, 'sms', "nm_lemb ilike '%{$nama_prodi}%'");
	$id_sms = $sms['result']['id_sms'];
	
	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "id_sms='$id_sms'", '', 2);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);
		$records[] = array('id_smt'=>$row['id_smt'], 'id_sms'=>$row['id_sms']);
	}
	echo '</table>';

	submitAct($proxy, $records, $token);
}
elseif ($_REQUEST['entitas'] == 'nilai') {
	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd ilike '%si joni%'");
	$id_reg_pd1 = $temp['result']['id_reg_pd'];

	$temp = $proxy->GetRecord($token, 'mahasiswa_pt', "nm_pd ilike '%si doel%'");
	$id_reg_pd2 = $temp['result']['id_reg_pd'];

	$records = array();
	
	echo '<br/><br/>';
	echo "<h2>Data dari GetRecordset {$_REQUEST['entitas']}</h2>";

	$result = $proxy->GetRecordset($token, $_REQUEST['entitas'], "p.id_reg_pd='$id_reg_pd1' or p.id_reg_pd='$id_reg_pd2'", 'id_reg_pd asc', 5);

	echo '<table class="data_grid">';
	foreach ($result['result'] as $row) {
		echoDataUpdate($row, $i);

		$records[] = array('id_reg_pd'=>$row['id_reg_pd'], 'id_kls'=>$row['id_kls']);
	}
	echo '</table>';
	submitAct($proxy, $records, $token);
}

function echoDataUpdate($row, &$i) {
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

	echo "<td $style >$i.</td>";
	foreach($row as $k=>$v){
		echo "<td $style>";
		echo $v;
		echo '&nbsp;</td>';
	}
	echo '</tr>';
}

function echoDataMasuk($records) {
	echo '<br/><br/>';
	echo '<h2>Data yg akan dihapus (Key)</h2>';
	$i = 0;
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
	<input type="button" value="DeleteRecord" onclick="document.getElementById(\'act\').value=\'delete_record\';document.getElementById(\'form1\').submit();"> <input type="button" value="DeleteRecordset" onclick="document.getElementById(\'act\').value=\'delete_recordset\';document.getElementById(\'form1\').submit();">
	</form>';	
	
}

function echoDeleteRecord($result) {
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

function echoDeleteRecordset($result) {
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

	if ($_REQUEST['act'] == 'delete_record') {
		$i=0;
		foreach ($records as $record) {
			$result = $proxy->DeleteRecord($token, $_REQUEST['entitas'], json_encode($record));
			echoDeleteRecord($result);
		}
	}	
	elseif ($_REQUEST['act'] == 'delete_recordset') {
		$result = $proxy->DeleteRecordset($token, $_REQUEST['entitas'], json_encode($records));
		echoDeleteRecordset($result);
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

