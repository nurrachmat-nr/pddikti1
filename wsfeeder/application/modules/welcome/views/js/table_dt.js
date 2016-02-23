<script type="text/javascript">
	$(document).ready(function() {
		var t = $('#dt_data').DataTable({
			//"dom": '<"toolbar">frtip',
			//"dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
			//"dom": '<"top"fp>rt<"bottom"l><"clear">',
			//"dom": '<"col-lg-6 top"i<"col-lg-6 top"f>>rt',
			"aaSorting": [],
			"lengthMenu": [[10, 50, 100], [10, 50, 100]],
			"iDisplayLength": 50,
			//"sPaginationType": "full_numbers",
			//"processing": true,
			//"serverSide": true,
			"ajax": "<?php echo base_url(); ?>index.php/welcome/jsonTable",
			"columns": [
							//{ "data": 'test',"searchable": false, "orderable": false, "targets": 0 },
							{ "data": "no","searchable": false, "orderable": false, "targets": 0 },
							{ "data": "nama_table" },
							{ "data": "jenis" },
							{ "data": "keterangan","searchable": false, "orderable": false, "targets": 0 },
							{ "data": "aksi","searchable": false, "orderable": false, "targets": 0 }
						],
	        //"order": [[ 1, 'asc' ]],
			initComplete: function () {
				this.api().columns([2]).every(function () {
					var column = this;
					//var title = $('#dynamic-table thead th').eq( $(this).index() ).text();
					var select = $('<select><option value="">All</option></select>')
					.appendTo( $(column.footer()).empty() )
					.on('change', function() {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);
						column
							.search( val?'^'+val+'$':'',true,false )
							.draw();
					});
					column.data().unique().sort().each(function (d,j) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					});
				});
			}
		});

		/*t.on('order.dt search.dt', function() {
			t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
				cell.innerHTML = i+1;
			});
		}).draw();*/
	});
</script>