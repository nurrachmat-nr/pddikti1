<div class="container-fluid">
	<div class="page-header" style="margin-top: 50px;">
		<div class="row">
			<div class="col-md-12">
				<h3><?php echo $title_page; ?></h3>
				<small>
					Versi yang digunakan <?php echo $versi;?> 
					<!--a href="#" class="modalButton" data-toggle="modal" data-src="<?php echo base_url();?>welcome/update_view" data-target="#modalku">
						#Change log
					</a-->
				</small>
			</div>
		</div>	
	</div>
	<div class="row">
		<div class="col-md-6 header_aksi">
			<?php
				if ($is_connect) {
					$temp_status = "<span class=\"label label-success\">connected</span>";
				} else {
					$temp_status = "<span class=\"label label-primary\">disconnected</span>";
				}
			?>
			Status koneksi dengan server <?php echo $temp_status;?>
		</div>
		<div class="col-md-6" align="right">
			<?php
				/*if ($is_connect) {
					echo "<a href=\"#\" class=\"modalButton btn btn-primary btn-sm\">
						<i class=\"fa fa-check-square-o\"></i>  Check Update
					</a>";
				}*/
			?>
		</div>	
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="loading" id="loading"></div>
			<div class="isi" id="isi"></div>
			<?php
				if ($is_connect) {
					echo "<table class=\"table table-hover table-striped table-bordered\" id=\"dt_data\">
								<thead>
									<tr>
										<th width=\"10px;\">#</th>
										<th>Versi Update</th>
										<th>Tanggal Update</th>
										<th>Tipe</th>
										<th>Change Logs</th>
										<th width=\"100px\">Aksi</th>
									</tr>
								</thead>
								<tbody>";
									$i=0;
									if ($jml==0) {
										echo "<tr>
												<td colspan=\"5\">Tidak ada update-an terbaru</td>
											</tr>";
									} else {
										foreach ($data as $row) {
											$temp = explode(', ', $row->changes);
											echo "<tr>
													<td>".++$i."</td>
													<td>".$row->version."</td>
													<td width=\"200px\">".date('d-m-Y',strtotime($row->date))."</td>
													<td>";
														switch ($row->ext) {
															case 1:
																echo "Update WSClient ".$row->version;
																break;
															case 2:
																echo "Dokumentasi WSClient ".$row->version;
																break;
															default:
																echo "Update WSClient ".$row->version;
																break;
														}
											  echo "</td>
													<td>
														<ul>";
															foreach ($temp as $value) {
																echo "<li>".$value."</li>";
															}
												  echo "</ul>
													</td>
													<td>
														<a href=\"javascript:void();\" class=\"btn btn-info btn-sm btnUpdate\" id=\"btn_update\" data-src=\"".base_url()."index.php/welcome/update_core/".$row->md5."/\">
															<i class=\"fa fa-refresh\"></i>  Update Online
														</a>
														<!--a href=\"#\" class=\"btn btn-default btn-sm\">
															<i class=\"fa fa-download\"></i>  Download ".$row->version."
														</a-->
													</td>
												</tr>";
										}
									}
								echo "</tbody>
							</table>";
				}
			?>
		</div>
	</div>
</div>