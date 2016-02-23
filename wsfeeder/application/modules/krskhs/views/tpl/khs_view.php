<div class="container-fluid">
	<div class="page-header" style="margin-top: 50px;">
		<div class="row">
			<div class="col-md-12">
				<h3><?php echo $title_page; ?></h3>
				<small><?php echo $ket_page;?></small>
			</div>
		</div>	
	</div>
	<div class="row">
		<div class="col-md-6 header_aksi">

		</div>
		<div class="col-md-6" align="right">
			
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
					echo "<div class=\"row\">
								<div class=\"col-md-4 col-md-offset-4\">
									<div class=\"panel panel-primary\">
										<div class=\"panel-heading\">
											<h3 class=\"panel-title\">Parameter KHS</h3>
										</div>
										<div class=\"panel-body\">
											<form action=\"".base_url()."krskhs/createkhs\" class=\"frm_krskhs\">
												<div class=\"loading\"></div>
												<div class=\"isi\"></div>
												<div class=\"form-group\">
													<label for=\"mhs\">Mahasiswa</label>
													<select class=\"form-control select2\" id=\"mhs\" name=\"mhs\"></select>
												</div>
												<div class=\"form-group\">
													<label for=\"prodi\">Program Studi</label>
													<select class=\"form-control select2\" id=\"prodi\" name=\"prodi\"></select>
												</div>
												<div class=\"form-group\">
													<label for=\"periode\">Periode</label>
													<select class=\"form-control select2\" id=\"periode\" name=\"periode\"></select>
												</div>
												<button type=\"submit\" class=\"btn btn-primary btn-sm btn-generate\">Generate KHS</button>
											</form>
										</div>
									</div>
								</div>
						</div>";
				}
			?>	
		</div>
	</div>
</div>