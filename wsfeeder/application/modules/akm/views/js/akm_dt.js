<script type="text/javascript">
	$(document).ready(function() {
		var t = $('#dt_data').DataTable({
			"aaSorting": [],
			"lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
			"iDisplayLength": 10,
			//"sPaginationType": "full_numbers",
			"processing": true,
			"language": {
				"processing": "<i class=\"fa fa-spinner fa-spin\"></i> Loading data, please wait..." //add a loading image,simply putting <img src="loader.gif" /> tag.
			},
			"serverSide": true,
			"ajax": {
				"url": "<?php echo base_url(); ?>index.php/akm/jsonAKM",
				"type": "POST"
			},
			"columns": [
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 }
						],
		});
		$.fn.dataTable.ext.errMode = 'throw';
		/*t.on( 'order.dt search.dt', function () {
			t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				cell.innerHTML = i+1;
			} );
		}).draw();*/
	});

	$(".btn-download").click(function(e){
		e.preventDefault();
		var url = top_url+'akm/createexcel/';
		var l = Ladda.create(this);
		$(".isi").hide();
		l.start();
		$.get(url, function(returnData) {
			$(".isi").show();
			if (!returnData) {
				$('.isi').html('<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>No respond from server.</div>');
			} else {
				$('.isi').html(returnData);
			};
		})
		.always(function() { l.stop(); });
		return false;
	});

	$(".btn-upload").click(function(e){
		e.preventDefault();
		var l = Ladda.create(this);
		l.start();
		uploadexcel();
		l.stop();
		return false;
	});

	function uploadexcel() {
		var urls = $(".frm_upload").attr("action");
		$.ajax({
			url: urls,
			type: "POST",
			data: new FormData($('.frm_upload')[0]),
			mimeType:"multipart/form-data",
			contentType: false,
			cache: false,
			processData:false,
			beforeSend:function()
			{
				$(".isi").hide();
			},
			complete:function()
			{
				$(".isi").show();
			},
			error: function()
			{
				$('.isi').html('<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>No respond from server.</div>');
			},
			success: function(data)
			{
				$('#dt_data').DataTable().ajax.reload();
				$(".isi").html(data);
			}
		})
	}

	Ladda.bind('.btn-download', { timeout: 2000 } );
	Ladda.bind('.btn-download', {
		callback: function( instance ) {
			var progress = 0;
			var interval = setInterval( function() {
				progress = Math.min( progress + Math.random() * 0.1, 1 );
				instance.setProgress( progress );
				if( progress === 1 ) {
					instance.stop();
					clearInterval( interval );
				}
			}, 200 );
		}
	});

	Ladda.bind('.btn-upload', { timeout: 2000 } );
	Ladda.bind('.btn-upload', {
		callback: function( instance ) {
			var progress = 0;
			var interval = setInterval( function() {
				progress = Math.min( progress + Math.random() * 0.1, 1 );
				instance.setProgress( progress );
				if( progress === 1 ) {
					instance.stop();
					clearInterval( interval );
				}
			}, 200 );
		}
	});

	/*$(".btn-download").click(function(){
		var url = '<?php echo base_url();?>index.php/akm/createexcel/';
		$('.loading').show();
		$(".loading").html("<i class=\"fa fa-spinner fa-spin\"></i> Loading data, please wait...");
		//alert(url);
		$.get(url, function(returnData) {
			if (!returnData) {
				$('.isi').html('Error, unkonwn error');
				$('.loading').hide();
			} else {
				$('.isi').html(returnData);
				$('.loading').hide();
			};
		})
		return false;
	});

	$(".frm_upload").on('submit',(function(e) {
		e.preventDefault();
		var urls = $(this).attr("action");
		var btn = $(this).attr(".btn_upload");
		//alert(urls);
		$.ajax({
			url: urls,
			type: "POST",
			data: new FormData(this),
			mimeType:"multipart/form-data",
			contentType: false,
			cache: false,
			processData:false,
			beforeSend:function()
			{
				$(".isi").hide();
				$(".loading").html('<i class=\"fa fa-spinner fa-spin\"></i> Extract file...Please wait...');
				//$("#loading").show();
			},
			complete:function()
			{
				//$("#loading").hide();
				$(".loading").empty();
				$(".isi").show();
			},
			error: function()
			{
				$('.isi').html('<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>No respond from server.</div>');
			},
			success: function(data)
			{
				$('#dt_data').DataTable().ajax.reload();
				$(".isi").html(data);
			}
		});
	}));*/
</script>