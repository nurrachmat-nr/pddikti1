// 1 detik = 10000
var refresh_loginuser = setInterval(
    function ()	{loadUsers();}, 10000
);

$(document).ready(function() {
    loadUsers();
});

function loadUsers() {
    ajaxurl = "<?php echo site_url('chatajax/getloginusers') ?>";
    $.ajax({
        type: "POST",
        url: ajaxurl,
        success: function(ret){
            parseAjaxVal(ret);
        }
    });            
}
