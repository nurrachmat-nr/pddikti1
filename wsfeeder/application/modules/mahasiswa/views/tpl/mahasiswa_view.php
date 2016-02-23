<div class="container-fluid">
	<div class="page-header" style="margin-top: 50px;">
		<div class="row">
			<div class="col-lg-12">
				<h3><?php echo $title_page; ?></h3>
				<small><?php echo $ket_page;?></small>

			</div>
		</div>	
	</div>
	<!--a href="javascript:void();" class="modalButton" data-toggle="modal" data-src="ws_mahasiswa/view_nilai_pindah" data-target="#modalku">
		Test
	</a-->
	<div class="row">
		<?php
			if (($error_code == 0) && ($error_desc == '')) {
				echo "<div class=\"col-ld-12 header_aksi\">
						<form action=\"".base_url()."mahasiswa/uploadexcel\" class=\"frm_upload\" id=\"frmku\" enctype=\"multipart/form-data\">
							<div class=\"form-group col-xs-4\">
								<div class=\"input-group\">
									<span class=\"input-group-btn\">
										<span class=\"btn btn-success btn-file btn-sm\">
											Browse File... <input type=\"file\" name=\"userfile\" class=\"input-sm\">
										</span>
									</span>
									<input type=\"text\" class=\"form-control input-sm\" readonly>
									<span class=\"input-group-btn\">
										<button data-toggle=\"dropdown\" class=\"btn btn-sm btn-primary dropdown-toggle\">Mahasiswa <span class=\"caret\"></span></button>
										<ul class=\"dropdown-menu\">
											<li><input type=\"radio\" name=\"mode\" id=\"mhs\" value=\"0\" checked><label for=\"mhs\">Mahasiswa</label></li>
											<li><input type=\"radio\" name=\"mode\" id=\"lulus\" value=\"1\"><label for=\"lulus\">Mahasiswa Lulus/DO</label></li>
											<li><input type=\"radio\" name=\"mode\" id=\"nilai\" value=\"2\"><label for=\"nilai\">Nilai pindahan</label></li>
									    </ul>
										<button class=\"btn btn-primary btn-sm btn-upload ladda-button\" data-style=\"expand-right\">Upload</button>
									</span>
								</div>
			                </div>
		                </form>
						<a href=\"javascript:void();\" class=\"btn btn-info btn-sm btn-download ladda-button\" data-style=\"expand-right\">
						<!--a href=\"".base_url()."template/mhs_template.xlsx\" class=\"btn btn-info btn-sm\"-->
							<i class=\"fa fa-download\"></i> Generate Template
						</a>
						<!--span class=\"input-group-btn\">
							<button class=\"btn btn-download btn-sm btn-info ladda-button\" data-style=\"expand-right\"><i class=\"fa fa-download\"></i> Generate Template</button>
							<button data-toggle=\"dropdown\" class=\"btn btn-sm btn-info dropdown-toggle\">Kelas kuliah <span class=\"caret\"></span></button>
							<ul class=\"dropdown-menu\">
								<li><input type=\"radio\" name=\"mode\" id=\"kelas_down\" value=\"0\" checked><label for=\"kelas_down\">Kelas kuliah</label></li>
								<li><input type=\"radio\" name=\"mode\" id=\"krs_down\" value=\"1\"><label for=\"krs_down\">KRS Mhs</label></li>
								<li><input type=\"radio\" name=\"mode\" id=\"dosen_down\" value=\"2\"><label for=\"dosen_down\">Dosen MK</label></li>
							</ul>
						</span-->
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
									<th>NIM</th>
									<th>Mahasiswa</th>
									<th>Tgl Lahir</th>
									<th>Prog. Studi</th>
									<th>Status Masuk</th>
									<th>Angkt.</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th>#</th>
									<th>NIM</th>
									<th>Mahasiswa</th>
									<th>Tgl Lahir</th>
									<th>Prog. Studi</th>
									<th>Status Masuk</th>
									<th>Angkt.</th>
									<th>Status</th>
								</tr>
							</tfoot>
						</table>";
				}
			?>
		</div>
	</div>
</div>