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
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<div class="panel panel-danger">
						<div class="panel-heading">
							<h3 class="panel-title">Parameter WSClient</h3>
						</div>
						<div class="panel-body">
							<!--form action=\"".base_url()."welcome/setting\" class=\"frm_setting\"-->
							<?php
								$attributes = array('role'=>'form');
								echo form_open('welcome/setting',$attributes);
								if(validation_errors()) {
									echo "<div class=\"bs-callout bs-callout-danger\">
											<h4>Error</h4>
											<p>".validation_errors()."</p>
										</div>";
								}
								$error = $this->session->flashdata('error');
								if ($error != '') {
									echo "<div class=\"bs-callout bs-callout-danger\">
											<h4>Error</h4>
											<p>".$error."</p>
										</div>";
								}
								$sukses = $this->session->flashdata('sukses'); 
								if($sukses != '') {
									echo "<div class=\"bs-callout bs-callout-success\">
											<h4>Sukses</h4>
											<p>".$sukses."</p>
										</div>";
								}
							?>
								<!--div class=\"loading\"></div>
								<div class=\"isi\"></div-->
								<div class="form-group">
									<label for="kodept">Kode PT</label>
									<input type="text" class="form-control" id="kodept" name="kode_pt" value="<?php echo $kode_pt; ?>">
								</div>
								<div class="form-group">
									<label for="urlws">URL Webservice</label>
									<input type="text" class="form-control" id="urlws" name="url_ws" value="<?php echo $dir_ws;?>">
								</div>
								<button type="submit" class="btn btn-danger btn-sm btn-setting">Simpan</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>