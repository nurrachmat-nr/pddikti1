<script type="text/javascript">
	$('a.btnUpdate').click(function(){
		var url = $('a.btnUpdate').attr('data-src');
		$('#loading').show();
		$('#isi').hide();
		update_core(url);
		return false;
	});

	function update_core(url) {
		$('#loading').html('<i class="fa fa-spinner fa-spin"></i> Update core, please wait...');
		$.get(url, function(returnData) {
			if (!returnData) {
				$('#isi').html('Error, unknown');
				$('#loading').hide();
			} else {
				$('#isi').show();
				$('#isi').html(returnData);
				$('#loading').hide();
			};
		});
	};

	$('a.modalButton').click(function(){
		var src = $(this).attr('data-src');
		//alert(src);
		$('#isi').html('Loading, please wait...');
		$('#isi').load(src);
	});
</script>