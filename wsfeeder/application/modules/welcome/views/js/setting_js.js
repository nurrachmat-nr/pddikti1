<script type="text/javascript">
	$(".frm_setting").on('submit',(function(e) {
		e.preventDefault();
		var urls = $(".frm_setting").attr("action");
		$.ajax({
			url: urls,
			type: "POST",
			data: new FormData(this),
			//mimeType:"multipart/form-data",
			contentType: false,
			cache: false,
			processData:false,
			beforeSend:function()
			{
				$(".isi").hide();
				$(".loading").html('<i class=\"fa fa-spinner fa-spin\"></i> Proses penyimpanan setting... Please wait...');
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
				$(".isi").html(data);
				//window.location=data;
			}
		});
	}));
	
</script>