<script type="text/javascript">
	$(document).ready(function() {
		var t = $('#dt_data').DataTable({
			//"dom": '<"toolbar">frtip',
			//"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
			"aaSorting": [],
			"lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
			"iDisplayLength": 10,
			//"sPaginationType": "full_numbers",
			"processing": true,
			"language": {
				"processing": "Loading data, please wait..." //add a loading image,simply putting <img src="loader.gif" /> tag.
			},
			"serverSide": true,
			//"ajax": "<?php echo base_url(); ?>index.php/kk/jsonKk",
			"ajax": {
				"url": "<?php echo base_url(); ?>index.php/kk/jsonKk",
				"type": "POST"
            },
            "columns": [
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 },
							{ "searchable": false, "orderable": false, "targets": 0 }
						],
		});
		$.fn.dataTable.ext.errMode = 'throw';
	});
</script>