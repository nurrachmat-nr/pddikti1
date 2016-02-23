<?php

function getConn() {
	global $g_conn;
	
	$conn_str = "host=localhost port=5432 dbname=unesa user=postgres password=xxx";

	if (!is_resource($g_conn))
		$g_conn = pg_connect($conn_str);

	return $g_conn;
}

function getColumns($schema, $table) {
    $conn = getConn();

	$sql = "select * from information_schema.columns where table_name='$table' and table_schema='$schema' order by ordinal_position";
	$rows = dbGetRows($sql);
	$columns = array();
	$exception = array('create_date', 'last_update', 'expired_date', 'last_sync');
	foreach ($rows as $row) {
		if (in_array($row['column_name'], $exception))
			continue;
		$columns[] = $row['column_name'];
	}
	return implode(',', $columns);
}

function insertSQL($table, $fields) {
	$sql = "insert into $table (";
	$fieldnames = "";
	$fieldvals = "";
	
	$i = 0;
	$binds = array();
	
	reset($fields);
	while(list($f, $v) = each($fields)) {
		$fieldnames .= $f . ", ";
		$fieldvals .= "'" . pg_escape_string($v) . "', ";
	}
	$fieldnames = substr($fieldnames, 0, -2);
	$fieldvals = substr($fieldvals, 0, -2);

	$sql .= $fieldnames . ") values (" .$fieldvals . ")";
	return $sql;
}

function updateSQL($table, $fields, $where) {
	$sql = "update $table set ";
	$vars = "";
	
	reset($fields);
	while(list($f, $v) = each($fields)) {
		$vars .= "$f = '" . pg_escape_string($v) . "', ";
	}
	$sql .= substr($vars, 0, -2);
	$sql .= " where $where";
	
	return $sql;
}

function dbGetOne($sql) {
	$conn = getConn();
	
	$result = pg_query($conn, $sql);
    while ($row = pg_fetch_row($result)) {
		return $row[0];
    }
}

function dbGetRows($sql) {
	$conn = getConn();
	
	$result = pg_query($conn, $sql);
	
    $i=0;
	$rows = array();
    while ($row = pg_fetch_assoc($result)) {
		$rows[] = $row;
    }
	return $rows;
}

function dbGetRow($sql) {
	$conn = getConn();
	
	$result = pg_query($conn, $sql);
	
    $i=0;
    while ($row = pg_fetch_assoc($result)) {
		return $row;
    }
	return false;
}

function dbQuery($sql) {
	$conn = getConn();
	
	return pg_query($conn, $sql);
}

function dbLastError() {
	$conn = getConn();
	return pg_last_error($conn);
}

function isValidDate($str) {
	if (preg_match('/-/',$str)) {
		$date = explode("-", $str);
		$month = (int)$date[1];
		$day = (int)$date[2];
		$year = (int)$date[0];
		return checkdate($month,$day,$year);
	}
	return false;
}

function getIDUpdater() {
	$sql = "select id_pengguna from man_akses.pengguna order by id_pengguna limit 1";
	return dbGetOne($sql);
}

function getUUID() {
    $sql = 'SELECT uuid_in(md5(now()::text)::cstring) as guid';
    return dbGetOne($sql);
}
