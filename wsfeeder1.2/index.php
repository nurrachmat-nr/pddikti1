<?php
@session_start();
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
define("APP_PATH",str_replace("\\","/",dirname(__FILE__)));
define("PATH","http://".$_SERVER['HTTP_HOST'].dirname( $_SERVER['SCRIPT_NAME']));
require_once(APP_PATH."/app/common.php");
if((int)$_SESSION['id_sp_feeder']!=1){
	include APP_PATH."/view/login.php";
	exit;
}

?>