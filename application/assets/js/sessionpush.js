var intervalPromo;
window.setInterval(notifyFunction, 15000);
function notifyFunction() {
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('ajax/notification'); ?>",
        data: "name=home",
        success: function(ret){
            $.pnotify({
                pnotify_title: 'Periodic Notification',
                pnotify_text: ret
            });
        }
    });
}
