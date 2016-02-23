<script type="text/javascript">
	$('#mhs').select2({
		placeholder: "NIM / Nama Mahasiswa...",
		//minimumInputLength: 1,
		ajax: {
			url: top_url+'krskhs/mhslist',
			type: "POST",
			dataType: 'json',
			delay: 20,
			data: function (cari) {
				return {
					q: cari.term,
					page: 10
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data, function(obj) {
						return {
							id: obj.id_reg_pd,
							text: obj.nipd+' -'+obj.nm_pd+' ('+obj.id_stat+')'
						};
					})
				};
			},
			cache: true
		}
	});

	$('#prodi').select2({
		placeholder: "Kode Prodi / Nama Program Studi...",
		//minimumInputLength: 1,
		ajax: {
			url: top_url+'krskhs/prodilist',
			type: "POST",
			dataType: 'json',
			delay: 20,
			data: function (cari) {
				return {
					q: cari.term,
					page: 10
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data, function(obj) {
						return {
							id: obj.id_sms,
							text: obj.kode_prodi+' '+obj.prodi+' ('+obj.jenjang+')'
						};
					})
				};
			},
			cache: true
		}
	});

	$('#periode').select2({
		placeholder: "Periode KRS...",
		//minimumInputLength: 1,
		ajax: {
			url: top_url+'krskhs/periodelist',
			type: "POST",
			dataType: 'json',
			delay: 20,
			data: function (cari) {
				return {
					q: cari.term,
					page: 10
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data, function(obj) {
						return {
							id: obj.id_smt,
							text: obj.nm_smt
						};
					})
				};
			},
			cache: true
		}
	});

	$(".frm_krskhs").on('submit',(function(e) {
		e.preventDefault();
		var urls = $(".frm_krskhs").attr("action");
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
				$(".loading").html('<i class=\"fa fa-spinner fa-spin\"></i> Generate KRS...Please wait...');
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