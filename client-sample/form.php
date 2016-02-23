<html>
<?php
include('initsoap.php');
?>
<head>
	<title>WS - <?= $_REQUEST['act'] ?></title>
</head>
<body>
<h3>Lokasi web service: <a href="<?= $url ?>" target="_blank"><?= $url ?></a></h3>
<h1><?= $_REQUEST['act'] ?></h1>

<form method="get">
<input type="hidden" name="act" value="<?= $_REQUEST['act'] ?>" />
<input type="hidden" name="token" value="<?= $token ?>" />
<?php if ($_REQUEST['act'] == 'GetRecordset' || $_REQUEST['act'] == 'GetDeletedRecordset'): ?>
<table>
	<tr>
		<td>Table</td>
		<td><input type="text" name="table" size="30" value="<?= $_REQUEST['table'] ?>"></td>
	</tr>
	<tr>
		<td>Filter</td>
		<td><input type="text" name="filter" size="70" value="<?= $_REQUEST['filter'] ?>"></td>
	</tr>
	<tr>
		<td>Order</td>
		<td><input type="text" name="order" size="30" value="<?= $_REQUEST['order'] ?>"></td>
	</tr>
	<tr>
		<td>Limit</td>
		<td><input type="text" name="limit" value="<?= $_REQUEST['limit'] ?>"></td>
	</tr>
	<tr>
		<td>Offset</td>
		<td><input type="text" name="offset" value="<?= $_REQUEST['offset'] ?>"></td>
	</tr>
</table>

<?php endif; ?>

<?php if ($_REQUEST['act'] == 'GetRecord'): ?>
<table>
	<tr>
		<td>Table</td>
		<td><input type="text" name="table" size="30" value="<?= $_REQUEST['table'] ?>"></td>
	</tr>
	<tr>
		<td>Filter</td>
		<td><input type="text" name="filter" size="70" value="<?= $_REQUEST['filter'] ?>"></td>
	</tr>
</table>
<?php endif; ?>

<?php if ($_REQUEST['act'] == 'GetCountRecordset' || $_REQUEST['act'] == 'GetCountDeletedRecordset'): ?>
<table>
	<tr>
		<td>Table</td>
		<td><input type="text" name="table" size="30" value="<?= $_REQUEST['table'] ?>"></td>
	</tr>
</table>
<?php endif; ?>

<?php if ($_REQUEST['act'] == 'GetDictionary'): ?>
<table>
	<tr>
		<td>Table</td>
		<td><input type="text" name="table" size="30" value="<?= $_REQUEST['table'] ?>"></td>
	</tr>
</table>
<?php endif; ?>

<?php if ($_REQUEST['act'] == 'GetToken'): ?>
<table>
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" size="30" value="<?= $_REQUEST['username'] ?>"></td>
	</tr>
	<tr>
		<td>Password</td>
		<td><input type="password" name="password" size="30"></td>
	</tr>
</table>
<?php endif; ?>

<input type="submit" value="Go">
</form>

<?php

echo '<html>
    <head>
        <title>Web Service</title>
		<link rel="stylesheet" href="print.css" type="text/css" />
    </head>
    <body>';

$table = $_REQUEST['table'];
$filter = $_REQUEST['filter'];
$order = $_REQUEST['order'];
$limit = $_REQUEST['limit'];
$offset = $_REQUEST['offset'];

if ($_REQUEST['act'] == 'GetRecord' && $table)
    $result = $proxy->GetRecord($token, $table,$filter);
elseif ($_REQUEST['act'] == 'GetRecordset' && $table)
    $result = $proxy->GetRecordset($token, $table,$filter, $order, $limit, $offset);
elseif ($_REQUEST['act'] == 'GetDeletedRecordset' && $table)
    $result = $proxy->GetDeletedRecordset($token, $table,$filter, $order, $limit, $offset);
elseif ($_REQUEST['act'] == 'GetCountRecordset' && $table)
    $result = $proxy->GetCountRecordset($token, $table);
elseif ($_REQUEST['act'] == 'GetCountDeletedRecordset' && $table)
    $result = $proxy->GetCountDeletedRecordset($token, $table);
elseif ($_REQUEST['act'] == 'CheckDeveloperMode')
    $result = $proxy->CheckDeveloperMode($token);
elseif ($_REQUEST['act'] == 'ListTable')
    $result = $proxy->ListTable($token);
elseif ($_REQUEST['act'] == 'GetDictionary' && $table) {
    $result = $proxy->GetDictionary($token, $table);
}
elseif ($_REQUEST['act'] == 'GetToken' && $_REQUEST['username']) {
	$username = $_REQUEST['username'];
	$password = $_REQUEST['password'];
    $result = $proxy->GetToken($username, $password);
}
elseif ($_REQUEST['act'] == 'GetVersion') {
	$result = $proxy->GetVersion($token);
}
elseif ($_REQUEST['act'] == 'GetExpired') {
	$result = $proxy->GetExpired($token);
}
elseif ($_REQUEST['act'] == 'GetChangeLog') {
	$result = $proxy->GetChangeLog($token);
}

echo '<a href="index.php">Kembali</a><br/><br/>';
echo '<h2>Result</h2>';
if (is_array($result)) {
	if ($result['error_code'] != '0') {
		echo $result['error_desc'];
	}
	else {
		if (is_array($result['result'])) {
			if ($_REQUEST['act'] == 'GetDictionary') {
				echo '<table class="data_grid">';
				foreach ($result['result'] as $column) {
					if ($column['not_null'])
						$column['not_null'] = 'not null';

					echo '<tr>';
					echo '<td>' . $column['column_name'] . '</td>';
					echo '<td>';
					if ($column['pk'])
						echo 'primary key ';
					echo '</td>';
					echo '<td>';
						echo $column['type'];
					echo '</td>';
					echo '<td>';
						echo $column['not_null'];
					echo '</td>';
					echo '<td>';
						echo $column['default'];
					echo '</td>';
					echo '<td>';
						echo $column['desc'];
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';	
			}
			else {
				if ($_REQUEST['act'] == 'GetRecord' || $_REQUEST['act'] == 'InsertRecord' || $_REQUEST['act'] == 'UpdateRecord' || $_REQUEST['act'] == 'DeleteRecord') {
					$result['result'] = array($result['result']);
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
						echo $v;
						echo '&nbsp;</td>';
					}
					echo '</tr>';    
				}
				echo '</table>';
			}
		}
		else {
			echo nl2br($result['result']);
		}
	}
}
else {
	echo $result;
	if ($_REQUEST['act'] == 'GetToken') {
		$_SESSION['token'] = $result;
	}
}

echo '</body></table>';

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
