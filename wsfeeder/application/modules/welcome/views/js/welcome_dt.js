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
				"url": top_url+'welcome/getSMS',
				"type": "POST"
            },
			"columns": [
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 }
						],
		});
		$.fn.dataTable.ext.errMode = 'throw';
	});

	
</script>