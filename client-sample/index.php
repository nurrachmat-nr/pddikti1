<?php 
error_reporting(0); 
include('initsoap.php');
?>
<html>
    <head>
        <title>Simulasi Web Service</title>
        <link rel="stylesheet" href="print.css" type="text/css" />
    </head>
    <body>
        <h1>Simulasi Web Service</h1>
        
		<h3>
		Lokasi web service: <br/><a href="<?= $url ?>" target="_blank"><?= $url ?></a>
		<br/>
		<?php
		$url_arr = explode('?', $url);
		$url2 = $url_arr[0];
		?>
		</h3>
		
        <table class="data_grid">
            <tr>
                <th colspan="3">
				Fungsi yang disediakan<br/>
				<? echo '<a href="'.$url2 .'" target="_blank">'.$url2 . '</a>'; ?>
				</th>
            </tr>
			<tr>
				<th>Fungsi</th>
				<th>Keterangan</th>
				<th>Parameter</th>
			</tr>
            <tr>
                <td><a href="form.php?act=GetToken" target="blank">Get Token</a></td>
				<td>Mendapatkan Token untuk dipakai sebagai parameter di fungsi web service lainnya</td>
				<td>
				1. username<br/>
				2. password
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=ListTable" target="blank">List Table</a></td>
				<td>Mendapatkan daftar tabel yang tersedia</td>
				<td>
				token
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetDictionary" target="blank">Get Dictionary</a></td>
				<td>Mendapatkan struktur data dari suatu tabel</td>
				<td>
				1. token<br/>
				2. tabel
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetRecord" target="blank">Get Record</a></td>
				<td>Mendapatkan 1 record dari sebuah tabel</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. filter
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetRecordset" target="blank">Get Recordset</a></td>
				<td>Mendapatkan recordset dari sebuah tabel</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. filter<br/>
				4. order<br/>
				5. limit<br/>
				6. offset
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetDeletedRecordset" target="blank">Get Deleted Recordset</a></td>
				<td>Mendapatkan recordset yang dihapus dari sebuah tabel</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. filter<br/>
				4. order<br/>
				5. limit<br/>
				6. offset
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetCountRecordset" target="blank">Get Count Recordset</a></td>
				<td>Mendapatkan jumlah data dari sebuah tabel</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetCountDeletedRecordset" target="blank">Get Count Deleted Recordset</a></td>
				<td>Mendapatkan jumlah data yang dihapus dari sebuah tabel</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				</td>
            </tr>
            <tr>
                <td><a href="add.php" target="blank">Insert Record</a></td>
				<td>Insert 1 record data</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="add.php" target="blank">Insert Recordset</a></td>
				<td>Insert recordset</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="update.php" target="blank">Update Record</a></td>
				<td>Update 1 record data</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="update.php" target="blank">Update Recordset</a></td>
				<td>Update recordset</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="delete.php" target="blank">Delete Record</a></td>
				<td>Menghapus 1 record data</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="delete.php" target="blank">Delete Recordset</a></td>
				<td>Menghapus recordset</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="restore.php" target="blank">Restore Record</a></td>
				<td>Mengembalikan data yang dihapus 1 record data</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>
            <tr>
                <td><a href="restore.php" target="blank">Restore Recordset</a></td>
				<td>Mengembalikan data yang dihapus recordset</td>
				<td>
				1. token<br/>
				2. tabel<br/>
				3. data
				</td>
            </tr>            
			<tr>
                <td><a href="form.php?act=CheckDeveloperMode" target="blank">Check Developer Mode</a></td>
				<td>Melihat apakah feeder dalam mode developer</td>
				<td>
				token
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetVersion" target="blank">Get Version</a></td>
				<td>Mendapatkan versi web service</td>
				<td>
				token
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetExpired" target="blank">Get Expired</a></td>
				<td>Mendapatkan tanggal expired</td>
				<td>
				token
				</td>
            </tr>
            <tr>
                <td><a href="form.php?act=GetChangeLog" target="blank">Get Change Log</a></td>
				<td>Mendapatkan log perubahan</td>
				<td>
				token
				</td>
            </tr>
        </table>
        <br/><br/>
    </body>
</html>
