<?php error_reporting(0); ?>
<html>
    <head>
        <title>Simulasi Web Service (UNESA)</title>
		<link rel="stylesheet" href="print.css" type="text/css" />
    </head>
<?php

include('initsoap.php');
include('function.php');
include('mapping.php');

$sql = "select * from ms_mahasiswa where tgllahir is not null and alamat is not null
and namaibu is not null order by npm limit 5 offset 0";
$list_mhs = dbGetRows($sql);
$mappings = array();
$records = array();
foreach ($list_mhs as $row) {
	$mapping = array();
	$nama = strtolower($row['nama']);
	$tgllahir = strtolower($row['tgllahir']);

	$result_unesa = $proxy->GetRecord($token, 'mahasiswa_history', "lower(nm_pd) = '$nama' and tgl_lahir='$tgllahir'");

	$mapping['nim'] = $row['npm'];
	if (count($result_unesa['result']) != 0) {
		$mapping['id_pd'] = $result_unesa['result']['id_pd'];
		$mapping['id_reg_pd'] = $result_unesa['result']['id_reg_pd'];
	}else{
		foreach ($list_mhs as $mhs) {
			$record = array();
			
			foreach ($mhs as $k=>$v) {
				$key = array_search($k, $map_mhs);
				if ($key) {
					$record[$key] = $v;
				}
			}

			# field berikut diisi dari tabel referensi yg didapat dari pemanggilan web service
			# untuk contoh simulasi sementara diisi manual
			
			$record['id_kk'] = 0;
			$record['id_sp'] = 'd378f2a8-b572-46c0-9638-d4e13d68c836';
			$record['ds_kel'] = '-';
			$record['id_wil'] = '999999';
			$record['id_agama'] = '1';
			$record['kewarganegaraan'] = 'ID';
			$record['a_terima_kps'] = '0';
			$record['id_kebutuhan_khusus_ayah'] = 0;
			$record['id_kebutuhan_khusus_ibu'] = 0;
			$record['regpd_id_sp'] = 'd378f2a8-b572-46c0-9638-d4e13d68c836';
			$record['regpd_id_sms'] = 'cd816eb9-349e-4cbe-bd70-b3ee05fa7d51';
			$record['regpd_id_jns_daftar'] = '1';
			$record['regpd_tgl_masuk_sp'] = '2010-10-09';

			$result_feeder = $proxy->InsertRecord($token, 'mahasiswa', json_encode($record));
			if ($result_feeder['result']['id_pd']) {
				$id_pd = $result_feeder['result']['id_pd'];
				$id_reg_pd = $result_feeder['result']['id_reg_pd'];
				$mapping['id_pd'] = $id_pd;
				$mapping['id_reg_pd'] = $id_reg_pd;
			}
		}
	}
	$mappings[] = $mapping;
}

echo "<table class='data_grid'>";
foreach ($mappings as $mapping) {
	echo "<tr>";
	echo "<td>{$mapping['nim']}</td>";
	echo "<td>{$mapping['id_pd']}</td>";
	echo "<td>{$mapping['id_reg_pd']}</td>";
	echo "</tr>";
	$sql = insertSQL('map_mahasiswa', $mapping);
	echo $sql."<br>";
	echo dbQuery($sql);
}
echo "</table>";

?>