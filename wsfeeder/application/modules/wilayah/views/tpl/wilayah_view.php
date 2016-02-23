<div class="container-fluid">
	<div class="page-header" style="margin-top: 50px;">
		<div class="row">
			<div class="col-lg-12">
				<h3><?php echo $title_page; ?></h3>
			</div>
		</div>	
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?php
				if (($error_code != 0) && ($error_desc != '')) {
					echo "<div class=\"bs-callout bs-callout-danger\">
							<h4>Error ".$error_code."</h4>
							<p>".$error_desc."</p>
						  </div>";
				} else {
					echo "<table class=\"table table-hover table-striped table-bordered\" id=\"dt_data\">
							<thead>
								<tr>
									<th width=\"10px;\">#</th>
									<th>ID Wilayah</th>
									<th>Nama Wilayah</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th width=\"10px;\">#</th>
									<th>ID Wilayah</th>
									<th>Nama Wilayah</th>
								</tr>
							</tfoot>
						</table>";
				}
			?>
		</div>
	</div>
</div>