<?php

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);

session_start();

require_once('nusoap/nusoap.php');
require_once('nusoap/class.wsdlcache.php');

$url = 'http://localhost:8082/ws/live.php?wsdl';
//$url = 'http://localhost:8082/ws/sandbox.php?wsdl';
$client = new nusoap_client($url, true);

$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    die();
}

$proxy = $client->getProxy();

$token = $_SESSION['token'];

$nama_pt = 'petra';
$nama_prodi = 'informatika';

