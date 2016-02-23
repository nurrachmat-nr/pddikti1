<?php
make_heading('Setting DBF','Mengatur lokasi folder Epsbed layar biru');
echo "<link href=\"".PATH."/app/tree.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<script type=\"text/javascript\" src=\"".PATH."/app/easing.js\"></script>
<script type=\"text/javascript\" src=\"".PATH."/app/filetree.js\"></script>
<span style=\"float:right; padding-right:10px; padding-bottom:5px\">
<button type=\"submit\" class=\"btn btn-small btn-primary\" onclick=\"goSaveDBF()\"><i class=\"icon-ok icon-white\"></i> Simpan</button>
</span>
<table width=\"98%\"  border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr valign=\"top\">
    <td width=\"27%\"><table class=\"table table-striped table-condensed\">
        <thead>
          <tr>
            <th>Folder</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><div role=\"tablist\" class=\"ui-accordion ui-widget ui-helper-reset\" id=\"div_filtertree\">
                <h3 tabindex=\"0\" aria-selected=\"true\" aria-controls=\"ui-accordion-div_filtertree-panel-0\" id=\"ui-accordion-div_filtertree-header-0\" role=\"tab\" class=\"ui-accordion-header ui-helper-reset ui-state-default ui-accordion-header-active ui-state-active ui-corner-top ui-accordion-icons\"><span class=\"ui-accordion-header-icon ui-icon ui-icon-triangle-1-s\"></span>
                  <select name=\"select\" onChange=\"list_folder(this.value)\">";
			  $cek=''; $main_drv='';
			  if(file_exists(SISTEM_TMP."/dbf_path.txt")){
			  	$l=file_get_contents(SISTEM_TMP."/dbf_path.txt");
				if(is_dir($l)){
					$cek=$l;
					$main_drv=substr($l,0,1);
				}
			  }
			  $x=0;
			  for ($i = 0; $i <= 25; $i++) {
			  	$driv=num2alpha($i);
				if(is_dir($driv.":/")){
					$x++;
					if($x==1 and $cek==''){$cek=$driv.":/";}
					$sl=''; if($driv==$main_drv){$sl='selected';}
					echo "<option value=\"$driv:/\" $sl>Drive $driv:\\</option>\r\n";
				}
			  }
			  echo " </select>
                </h3>
                <div id=\"folder_list\" style=\"display: block; height:200px; overflow: auto;\" class=\"ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active\"></div>
              </div></td>
          </tr>
        </tbody>
      </table></td>
    <td width=\"73%\"><table class=\"table table-striped table-condensed\" style=\"margin-left:20px\">
    <thead>
      <tr>
        <th id=\"ttitles\">Folder</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><div id=\"list_file\" style=\"height:<? echo $h-280;?>px; overflow: auto; padding:5px 5px 5px 5px\" class=\"ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active\"></div></td>
      </tr>
    </tbody>
  </table></td>
  </tr>
</table>
<div id=\"x_hidden\"></div>";
echo "\r\n\r\n<div id=\"detail_mhs\" style=\"display:none\"><div class=\"dlg\">Wait ...proses update data..</div></div>\r\n";
echo "<script language=\"javascript\">
function list_folder(d){
	$('#folder_list').fileTree({ root: d, script: '".PATH."/index.php?nofile=1', folderEvent: 'click', expandSpeed: 750, collapseSpeed: 750, expandEasing: 'easeOutBounce', collapseEasing: 'easeOutBounce', loadMessage: 'Un momento...' }, function(file) {  
	});
}
$(document).ready( function() {
	list_folder('$cek');
});
var lokasi_dbf='".urlencode($cek)."';
function goSaveDBF(){
	$('#x_hidden').load('".PATH."/index.php?savedbf=1&path='+lokasi_dbf);
}
function bukafolder(f){
	$('#list_file').load('?getfiles='+f+'&f=1');
}
</script>";
?>