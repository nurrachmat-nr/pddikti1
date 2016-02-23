<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>LOGIN PAGE</title>
<link rel="stylesheet" href="<?php echo PATH;?>/app/jquery-ui.css">
<script src="<?php echo PATH;?>/app/jquery-1.10.2.js"></script>
<script src="<?php echo PATH;?>/app/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo PATH;?>/app/style.css">
<style>
		body { font-size: 62.5%; }
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:0px; }
		h1 { font-size: 1.2em; margin: .6em 0; }
		div#users-contain { width: 350px; margin: 20px 0; }
		div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
		div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips { border: 1px solid transparent; padding: 0.3em; }
	</style>
</head>
<body>
<div id="dialog-form" title="Login Page">
  <form id="form1" name="form1" method="post" action="">
    <fieldset>
    <label for="host"><strong>URL WS FEEDER</strong> </label>
    <table>
      <tr>
        <td style="vertical-align:middle">http://</td>
        <td><input style="width:90px" type="text" name="host" id="host" value="<?php if($_COOKIE['host_idx']){echo $_COOKIE['host_idx'];}else{echo '192.168.35.254:8082';} ;?>" class=""></td>
		<td>/ws/</td>
		<td><select name="mode" id="modes">
		  <option value="live.php" <?php if($_COOKIE['mode_idx']=='live.php'){echo 'selected';}?>>live.php</option>
		  <option value="sandbox.php" <?php if($_COOKIE['mode_idx']=='sandbox.php'){echo 'selected';}?>>sandbox.php</option>
	    </select></td>
		<td>?wsdl</td>
      </tr>
      <tr>
        <td style="vertical-align:middle">Ex:</td>
        <td colspan="4">192.168.0.100:8082</td>
      </tr>
    </table>
    <label for="user"><strong>User ID</strong></label>
    <input name="username" type="text" id="username" value="" class="text ui-widget-content ui-corner-all">
    <label for="password"><strong>Password</strong></label>
    <input name="pass" type="password" id="password" class="text ui-widget-content ui-corner-all">
	<input name="url" type="hidden" id="url">
    </fieldset><div style="text-align:center; color:#FF0000"><?php
	if($_GET['err']){
		echo $_GET['desc'];
	}
	?></div>
  </form>
</div>
<script language="javascript">
function LoginPage() {
	var url="http://"+$('#host').val()+'/ws/live.php?wsdl';
	$('#url').val(url);
	document.getElementById('form1').submit();
}
$(function() {
	var dialog;
	  dialog = $( "#dialog-form" ).dialog({
      autoOpen: true,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Login": LoginPage
      }
    });
});
</script>
</body>
</html>
