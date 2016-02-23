<?php
   require "lib/nusoap.php";
   require "lib/class.wsdlcache.php";
   require "header.php";
   require "setting.php";
?>
<body>
    <div class="page-header">
      <h1>Service GetRecordset (Table <?php echo isset($_GET['table'])?$table=$_GET['table']:$table='mahasiswa';?>) - <small><a href="index.php">kembali</a></small></h1>
    </div>
  <div>
    <?php
       require 'recordset.php';
       require 'footer.php';
    ?>
