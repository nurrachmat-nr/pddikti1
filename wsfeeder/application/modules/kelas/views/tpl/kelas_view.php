<div class="container-fluid">
	<div class="page-header" style="margin-top: 50px;">
		<div class="row">
			<div class="col-lg-12">
				<h3><?php echo $title_page; ?></h3>
				<small><?php echo $ket_page;?></small>
			</div>
		</div>	
	</div>
	<div class="row">
		<?php
			if (($error_code == 0) && ($error_desc == '')) {
				echo "<div class=\"col-ld-12 header_aksi\">
						<form action=\"".base_url()."kelas/uploadexcel\" class=\"frm_upload\" id=\"frmku\" enctype=\"multipart/form-data\">
							<div class=\"form-group col-xs-4\">
								<div class=\"input-group\">
									<span class=\"input-group-btn\">
										<span class=\"btn btn-success btn-file btn-sm\">
											Browse File... <input type=\"file\" name=\"userfile\" class=\"input-sm\">
										</span>
									</span>
									<input type=\"text\" class=\"form-control input-sm\" readonly>
									<span class=\"input-group-btn\">
										<button data-toggle=\"dropdown\" class=\"btn btn-sm btn-primary dropdown-toggle\">Kelas kuliah <span class=\"caret\"></span></button>
										<ul class=\"dropdown-menu\">
											<li><input type=\"radio\" name=\"mode\" id=\"kelas\" value=\"0\" checked><label for=\"kelas\">Kelas kuliah</label></li>
											<li><input type=\"radio\" name=\"mode\" id=\"krs\" value=\"1\"><label for=\"krs\">KRS Mhs</label></li>
											<li><input type=\"radio\" name=\"mode\" id=\"dosen\" value=\"2\"><label for=\"dosen\">Dosen MK</label></li>
											<li><input type=\"radio\" name=\"mode\" id=\"nilai_mhs\" value=\"3\"><label for=\"nilai_mhs\">Nilai Mhs</label></li>
									    </ul>
										<button class=\"btn btn-primary btn-sm btn-upload ladda-button\" data-style=\"expand-right\">Upload</button>
									</span>
								</div>
			                </div>
		                </form>
						<a href=\"javascript:void();\" class=\"btn btn-info btn-sm btn-download ladda-button\" data-style=\"expand-right\">
							<i class=\"fa fa-download\"></i> Generate Template
						</a>
					</div>";
			}
		?>	
	</div>
	<div class="loading"></div>
	<div class="isi"></div>
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
									<th>Program Studi</th>
									<th>Semester</th>
									<th>Kode MK</th>
									<th>Mata Kuliah</th>
									<th>Kelas</th>
									<th>SKS</th>
									<th>MHS KRS</th>
									<th>Dosen</th>
									<!--th></th-->
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th width=\"10px;\">#</th>
									<th>Program Studi</th>
									<th>Semester</th>
									<th>Kode MK</th>
									<th>Mata Kuliah</th>
									<th>Kelas</th>
									<th>SKS</th>
									<th>MHS KRS</th>
									<th>Dosen</th>
									<!--th></th-->
								</tr>
							</tfoot>
						</table>";
				}
			?>
		</div>
	</div>
</div>