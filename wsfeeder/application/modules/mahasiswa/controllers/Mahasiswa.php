<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder Mahasiswa Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Mahasiswa extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $table;
	private $table1;
	private $table2;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login')) {
			redirect('ws');
		} else {
			$this->limit = $this->config->item('limit');
			$this->filter = $this->config->item('filter');
			$this->order = $this->config->item('order');
			$this->offset = $this->config->item('offset');
			$this->table = 'mahasiswa';
			$this->table1 = 'mahasiswa_pt';
			$this->table2 = 'nilai_transfer';
			$this->load->model('m_feeder','feeder');
			$this->load->helper('csv');
			$this->load->library('excel');
			$this->template = './template/mhs_template.xlsx';

			$config['upload_path'] = $this->config->item('upload_path');
			$config['allowed_types'] = $this->config->item('upload_tipe');
			$config['max_size'] = $this->config->item('upload_max_size');
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload',$config);
		}
	}
	
	public function index()
	{
		$this->mhs();
	}

	public function mhs()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table1, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}
		
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Mahasiswa';
		$data['title_page'] = 'Daftar Mahasiswa';
		$data['ket_page'] = 'Menampilkan dan mengelola data mahasiswa';
		$data['assign_js'] = 'js/mahasiswa_dt.js';
		$data['assign_modal'] = 'layout/modal_big_tpl.php';
		tampil('mahasiswa_view',$data);
	}

	public function nilaipindah($id_reg_pd='')
	{
		if (!empty($id_reg_pd)) {
			$filter_nilai = "p.id_reg_pd='".$id_reg_pd."'";
			$temp_nilai = $this->feeder->getrset($this->session->userdata('token'), 
													$this->table2, $filter_nilai,
													$this->order, '',''
							);
			//var_dump($temp_nilai['result']);
			$temp_jml = count($temp_nilai['result']);
			$data['nilai_pindah'] = $temp_nilai['result'];
			$data['jml'] = $temp_jml;
			$this->load->view('tpl/__nilai_pindah_view',$data);
        } else {
            redirect('mahasiswa');
        }
	}

	public function uploadexcel()
	{
		$this->benchmark->mark('mulai');
		if (!$this->upload->do_upload()) {
			echo "<div class=\"bs-callout bs-callout-danger\">".$this->upload->display_errors()."</div>";
		} else {
			$mode = $this->input->post('mode');
			$file_data = $this->upload->data();
			$file_path = $this->config->item('upload_path').$file_data['file_name'];

			$objPHPExcel = PHPExcel_IOFactory::load($file_path);
			switch ($mode) {
				case 0:
					//echo "Import data mahasiswa";
					$objPHPExcel->setActiveSheetIndex(0);
					$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
					//var_dump($cell_collection);
					foreach ($cell_collection as $cell) {
						$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
						$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
						$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
						
						if ($row == 1) {
							$header[$row][$column] = $data_value;
						} else {
							$arr_data[$row][$column] = $data_value;
						}
					}
					if ($arr_data) {
						$temp_data = array();
						$sukses_count = 0;
						$sukses_msg = '';
						$error_count = 0;
						$error_msg = array();
						foreach ($arr_data as $key => $value) {
							$nim = $value['B'];
							$nm_mhs = $value['C'];
							$tgl_lahir = date('Y-m-d', strtotime($value['D']));
							$jk = trim($value['E']);
							$agama = trim($value['F']);
							$ds_kel = $value['G'];
							$wilayah = trim($value['H']);
							$nm_ibu = $value['I'];
							$kode_prodi = trim($value['J']);
							$tgl_masuk = date('Y-m-d', strtotime($value['K']));
							$smt_awal = trim($value['L']);
							$stat_mhs = trim($value['M']);
							$stat_awal = trim($value['N']);

							//$filter_sms = "id_sp='".$this->session->userdata('id_sp')."' AND kode_prodi='".$kode_prodi."'";
							$filter_sms = "kode_prodi='".$kode_prodi."' AND id_sp='".$this->session->userdata('id_sp')."'";
							$temp_sms = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_sms);
							$id_sms = $temp_sms['result']?$temp_sms['result']['id_sms']:'';
							if ($value['N']=='2') {
								$sks_diakui = $value['O'];
								$pt_asal = $value['P'];
								$prodi_asal = $value['Q'];
								$temp_data[] = array('nm_pd' => $nm_mhs, 
													'jk' => $jk,
													'tgl_lahir' => $tgl_lahir,
													'id_agama' => $agama,
													'id_kk' => 0,
													'id_sp' => $this->session->userdata('id_sp'),
													'ds_kel' => $ds_kel,
													'id_wil' => $wilayah,
													'a_terima_kps' => 0,
													'stat_pd' => $stat_mhs,
													'id_kebutuhan_khusus_ayah' => 0,
													'nm_ibu_kandung' => $nm_ibu,
													'id_kebutuhan_khusus_ibu' => 0,
													'kewarganegaraan' => 'ID',
													'regpd_id_sms' => $id_sms,
													'regpd_id_sp' => $this->session->userdata('id_sp'),
													'regpd_id_jns_daftar' => $stat_awal,
													'regpd_nipd' => $nim,
													'regpd_tgl_masuk_sp' => $tgl_masuk,
													'regpd_a_pernah_paud' => 0,
													'regpd_a_pernah_tk' => 0,
													'regpd_mulai_smt' => $smt_awal,
													'regpd_sks_diakui' => $sks_diakui,
													'regpd_nm_pt_asal' => $pt_asal,
													'regpd_nm_prodi_asal' => $prodi_asal
												);
							} else {
								$temp_data[] = array('nm_pd' => $nm_mhs, 
													'jk' => $jk,
													'tgl_lahir' => $tgl_lahir,
													'id_agama' => $agama,
													'id_kk' => 0,
													'id_sp' => $this->session->userdata('id_sp'),
													'ds_kel' => $ds_kel,
													'id_wil' => $wilayah,
													'a_terima_kps' => 0,
													'stat_pd' => $stat_mhs,
													'id_kebutuhan_khusus_ayah' => 0,
													'nm_ibu_kandung' => $nm_ibu,
													'id_kebutuhan_khusus_ibu' => 0,
													'kewarganegaraan' => 'ID',
													'regpd_id_sms' => $id_sms,
													'regpd_id_sp' => $this->session->userdata('id_sp'),
													'regpd_id_jns_daftar' => $stat_awal,
													'regpd_nipd' => $nim,
													'regpd_tgl_masuk_sp' => $tgl_masuk,
													'regpd_a_pernah_paud' => 0,
													'regpd_a_pernah_tk' => 0,
													'regpd_mulai_smt' => $smt_awal
												);
							}
						}
						//var_dump($temp_data);
						$temp_result = $this->feeder->insertrset($this->session->userdata['token'], $this->table, $temp_data);
						
						$i=0;
						if ($temp_result['result']) {
							foreach ($temp_result['result'] as $key) {
								++$i;
								if ($key['error_desc']==NULL) {
									++$sukses_count;
								} else {
									++$error_count;
									$error_msg[] = "<h4>Error</h4>".$key['error_desc']." (".$key['nm_pd']." / ".$key['tgl_lahir'].")";
									$stat_reg = FALSE;
								}
							}
						} else {
							echo "<div class=\"alert alert-danger\" role=\"alert\">
									<h4>Error</h4>";
									echo $temp_result['error_desc']."</div>";
						}
						$this->benchmark->mark('selesai');
						$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');

						if ((!$sukses_count==0) || (!$error_count==0)) {
							echo "<div class=\"alert alert-warning\" role=\"alert\">
									Waktu eksekusi ".$time_eks." detik<br />
									Results (total ".$i." baris data):<br /><font color=\"#3c763d\">".$sukses_count." data Kelas baru berhasil ditambah</font><br />
									<font color=\"#ce4844\" >".$error_count." data tidak bisa ditambahkan </font>";
									if (!$error_count==0) {
										echo "<a data-toggle=\"collapse\" href=\"#collapseExample\" aria-expanded=\"false\" aria-controls=\"collapseExample\">Detail error</a>";
									}
									//echo "<br />Total: ".$i." baris data";
									echo "<div class=\"collapse\" id=\"collapseExample\">";
											foreach ($error_msg as $pesan) {
													echo "<div class=\"bs-callout bs-callout-danger\">".$pesan."</div><br />";
												}
									echo "</div>
								</div>";
						}
					} else {
						echo "<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>Tidak dapat mengekstrak file.. Silahkan dicoba kembali</div>";
					}
					break;
				case 1:
					//echo "Mahasiswa Lulus/DO";
					$objPHPExcel->setActiveSheetIndex(1);
					$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
					//var_dump($cell_collection);
					foreach ($cell_collection as $cell) {
						$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
						$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
						$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
						
						if ($row == 1) {
							$header[$row][$column] = $data_value;
						} else {
							$arr_data[$row][$column] = $data_value;
						}
					}
					//var_dump($header[1]);
					//var_dump($arr_data);
					if ($arr_data) {
						$id_reg_pd = '';
						$temp_data = array();
						$sukses_count = 0;
						$sukses_msg = '';
						$error_count = 0;
						$error_msg = array();
						foreach ($arr_data as $key => $value) {
							$nim = $value['B'];
							$jenis_keluar = $value['D'];
							$tgl_keluar = $value['E'];
							$jalur_skripsi = $value['F'];
							$judul_skripsi = $value['G'];
							$bulan_awal_bimbingan = $value['H'];
							$bulan_akhir_bimbingan = $value['I'];
							$sk_yudisium = $value['J'];
							$tgl_yudisium = $value['K'];
							$ipk = $value['L'];
							$no_seri_ijazah = $value['M'];
							$keterangan = $value['N'];

							//$filter_regpd = "nipd like '%".$nim."%'";
							$filter_regpd = "nipd LIKE '%".$nim."%' AND p.id_sp='".$this->session->userdata('id_sp')."'";
							$temp_regpd = $this->feeder->getrecord($this->session->userdata('token'),$this->table1,$filter_regpd);
							if ($temp_regpd['result']) {
								$id_reg_pd = $temp_regpd['result']['id_reg_pd'];
							}
							$temp_key = array('id_reg_pd' => $id_reg_pd);
							$temp_data = array('id_jns_keluar' => $jenis_keluar,
												'tgl_keluar' => $tgl_keluar,
												'ket' => $keterangan,
												'jalur_skripsi' => $jalur_skripsi,
												'judul_skripsi' => $judul_skripsi,
												'bln_awal_bimbingan' => $bulan_awal_bimbingan,
												'bln_akhir_bimbingan' => $bulan_akhir_bimbingan,
												'sk_yudisium' => $sk_yudisium,
												'tgl_sk_yudisium' => $tgl_yudisium,
												'ipk' => $ipk,
												'no_seri_ijazah' => $no_seri_ijazah
											);
							$array[] = array('key'=>$temp_key,'data'=>$temp_data);
						}
						//var_dump($temp_data);
						//var_dump($array);
						$temp_result = $this->feeder->updaterset($this->session->userdata('token'),$this->table1,$array);
						//var_dump($temp_result['result']);
						$i=0;
						if ($temp_result['result']) {
							foreach ($temp_result['result'] as $key) {
								++$i;
								if ($key['error_desc']==NULL) {
									++$sukses_count;
								} else {
									++$error_count;
									$error_msg[] = "<h4>Error baris ".$i."</h4>".$key['error_desc'];
									$stat_reg = FALSE;
								}
							}
						} else {
							echo "<div class=\"alert alert-danger\" role=\"alert\">
									<h4>Error</h4>";
									echo $temp_result['error_desc']."</div>";
						}
						$this->benchmark->mark('selesai');
						$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');

						if ((!$sukses_count==0) || (!$error_count==0)) {
							echo "<div class=\"alert alert-warning\" role=\"alert\">
									Waktu eksekusi ".$time_eks." detik<br />
									Results (total ".$i." baris data):<br /><font color=\"#3c763d\">".$sukses_count." data Mahasiswa Lulus/DO berhasil diupdate</font><br />
									<font color=\"#ce4844\" >".$error_count." data tidak bisa ditambahkan </font>";
									if (!$error_count==0) {
										echo "<a data-toggle=\"collapse\" href=\"#collapseExample\" aria-expanded=\"false\" aria-controls=\"collapseExample\">Detail error</a>";
									}
									//echo "<br />Total: ".$i." baris data";
									echo "<div class=\"collapse\" id=\"collapseExample\">";
											foreach ($error_msg as $pesan) {
													echo "<div class=\"bs-callout bs-callout-danger\">".$pesan."</div><br />";
												}
									echo "</div>
								</div>";
						}
					}
					break;
				case 2:
					//echo "Nilai pindahan";
					$objPHPExcel->setActiveSheetIndex(2);
					$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
					//var_dump($cell_collection);
					foreach ($cell_collection as $cell) {
						$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
						$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
						$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
						
						if ($row == 1) {
							$header[$row][$column] = $data_value;
						} else {
							$arr_data[$row][$column] = $data_value;
						}
					}
					//var_dump($header[1]);
					//var_dump($arr_data);
					if ($arr_data) {
						$id_reg_pd = '';
						$id_mk = '';
						$id_sms = '';
						$temp_data = array();
						$sukses_count = 0;
						$sukses_msg = '';
						$error_count = 0;
						$error_msg = array();
						foreach ($arr_data as $key => $value) {
							$nim = $value['B'];
							$kode_mk_asal = $value['D'];
							$nm_mk_asal = $value['E'];
							$sks_asal = trim($value['F']);
							$nh_asal = trim($value['G']);
							$kode_mk_diakui = $value['H'];
							$nh_akui = trim($value['J']);
							$na_akui = trim($value['K']);
							$sks_akui = trim($value['L']);
							$kode_prodi = $value['M'];

							//$filter_regpd = "nipd='".$nim."'";
							$filter_regpd = "nipd LIKE '%".$nim."%' AND p.id_sp='".$this->session->userdata('id_sp')."'";
							$temp_regpd = $this->feeder->getrecord($this->session->userdata('token'),$this->table1,$filter_regpd);
							//var_dump($temp_regpd['result']);
							if ($temp_regpd['result']) {
								$id_reg_pd = $temp_regpd['result']['id_reg_pd'];
							}

							$filter_sms = "id_sp='".$this->session->userdata('id_sp')."' AND kode_prodi='".$kode_prodi."'";
							$temp_sms = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_sms);
							if ($temp_sms['result']) {
								$id_sms = $temp_sms['result']['id_sms'];
							}
							
							$filter_mk = "kode_mk='".$kode_mk_diakui."' AND id_sms='".$id_sms."'";
							//$filter_mk = "kode_mk='".$kode_mk_diakui."'";
							$temp_mk = $this->feeder->getrecord($this->session->userdata('token'),'mata_kuliah',$filter_mk);
							//var_dump($temp_regpd['result']);
							if ($temp_mk['result']) {
								$id_mk = $temp_mk['result']['id_mk'];
							}
							$temp_data[] = array('id_reg_pd' => $id_reg_pd,
												'id_mk' => $id_mk,
												'kode_mk_asal' => $kode_mk_asal,
												'nm_mk_asal' => $nm_mk_asal,
												'sks_asal' => $sks_asal,
												'sks_diakui' => $sks_akui,
												'nilai_huruf_asal' => $nh_asal,
												'nilai_huruf_diakui' => $nh_akui,
												'nilai_angka_diakui' => $na_akui
											);
						}
						$temp_result = $this->feeder->insertrset($this->session->userdata['token'], $this->table2, $temp_data);
						//var_dump($temp_result);
						$i=0;
						if ($temp_result['result']) {
							foreach ($temp_result['result'] as $key) {
								++$i;
								if ($key['error_desc']==NULL) {
									++$sukses_count;
								} else {
									++$error_count;
									$error_msg[] = "<h4>Error baris ".$i."</h4>".$key['error_desc'];
									$stat_reg = FALSE;
								}
							}
						} else {
							echo "<div class=\"alert alert-danger\" role=\"alert\">
									<h4>Error</h4>";
									echo $temp_result['error_desc']."</div>";
						}
						$this->benchmark->mark('selesai');
						$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');

						if ((!$sukses_count==0) || (!$error_count==0)) {
							echo "<div class=\"alert alert-warning\" role=\"alert\">
									Waktu eksekusi ".$time_eks." detik<br />
									Results (total ".$i." baris data):<br /><font color=\"#3c763d\">".$sukses_count." data Nilai Pindah baru berhasil ditambah</font><br />
									<font color=\"#ce4844\" >".$error_count." data tidak bisa ditambahkan </font>";
									if (!$error_count==0) {
										echo "<a data-toggle=\"collapse\" href=\"#collapseExample\" aria-expanded=\"false\" aria-controls=\"collapseExample\">Detail error</a>";
									}
									//echo "<br />Total: ".$i." baris data";
									echo "<div class=\"collapse\" id=\"collapseExample\">";
											foreach ($error_msg as $pesan) {
													echo "<div class=\"bs-callout bs-callout-danger\">".$pesan."</div><br />";
												}
									echo "</div>
								</div>";
						}
					} else {
						echo "<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>Tidak dapat mengekstrak file.. Silahkan dicoba kembali</div>";
					}
					break;
			}
		}
	}

	public function createexcel()
	{
		
		$this->benchmark->mark('mulai');
		if (!file_exists($this->template)) {
			echo "<div class=\"bs-callout bs-callout-danger\"><h4>Error</h4>File template tidak tersedia.</div>";
		} else {
			//Status Awal Masuk	SKS Diakui	PT Asal	PRODI Asal
			$data0 = array(array('nim' => '1234',
							'nama' => 'Mahasiswa 1',
							'tgl_lahir' => '1980-08-23',
							'jk' => 'L',
							'agama' => '1',
							'ds_kel' => 'Kel. Tano Bonunungan',
							'wilayah' => '999999',
							'nm_ibu' => 'Ibuku tercinta',
							'kode_prodi' => '14401',
							'tgl_masuk' => '2015-09-20',
							'smt_masuk' => '20151',
							'stat_mhs' => 'A',
							'stat_awal' => '1',
							'sks_diakui' => '',
							'pt_asal' => '',
							'prodi_asal' => ''),
						array('nim' => '2345',
							'nama' => 'Mahasiswa 2',
							'tgl_lahir' => '1982-11-23',
							'jk' => 'P',
							'agama' => '1',
							'ds_kel' => 'Pemalang',
							'wilayah' => '999999',
							'nm_ibu' => 'Ibuku tersayang',
							'kode_prodi' => '14401',
							'tgl_masuk' => '2014-09-20',
							'smt_masuk' => '20141',
							'stat_mhs' => 'A',
							'stat_awal' => '2',
							'sks_diakui' => '50',
							'pt_asal' => 'PT Suka Maju',
							'prodi_asal' => 'Keperawatan')
				   );
			$data1 = array(array('nim' => '1234',
							'nama' => 'Mahasiswa 1',
							'jenis_keluar' => 1,
							'tgl_keluar' => '2015-09-30',
							'jalur_skripsi' => 1,
							'judul_skripsi' => 'Judul Skripsi pertama',
							'bulan_awal_bimbingan' => '2015-01-01',
							'bulan_akhir_bimbingan' => '2015-09-01',
							'sk_yudisium' => '123/09/2015',
							'tgl_yudisium' => '2015-09-30',
							'ipk' => 3,
							'no_seri_ijazah' => '',
							'keterangan' => ''),
						array('nim' => '2345',
							'nama' => 'Mahasiswa 2',
							'jenis_keluar' => 1,
							'tgl_keluar' => '2015-09-30',
							'jalur_skripsi' => 1,
							'judul_skripsi' => 'Judul Skripsi kedua',
							'bulan_awal_bimbingan' => '2015-01-01',
							'bulan_akhir_bimbingan' => '2015-09-01',
							'sk_yudisium' => '456/09/2015',
							'tgl_yudisium' => '2015-09-30',
							'ipk' => 3.7,
							'no_seri_ijazah' => '',
							'keterangan' => ''),
					);
			$data2 = array(array('nim' => '2345',
							'nama' => 'Mahasiswa 2',
							'kode_mk_asal' => 'MKDU101',
							'nm_mk_asal' => 'Agama',
							'sks_asal' => 2,
							'nh_asal' => 'A',
							'kode_mk_diakui' => 'WAT101',
							'nm_mk_diakui' => 'Agama',
							'nh_akui' => 'A',
							'na_akui' => 4,
							'sks_akui' => 3),
						array('nim' => '2345',
							'nama' => 'Mahasiswa 2',
							'kode_mk_asal' => 'MKDU102',
							'nm_mk_asal' => 'Biologi',
							'sks_asal' => 3,
							'nh_asal' => 'C',
							'kode_mk_diakui' => 'WAT102',
							'nm_mk_diakui' => 'Biologi',
							'nh_akui' => 'A',
							'na_akui' => 4,
							'sks_akui' => 3),
					);
			$objPHPExcel = PHPExcel_IOFactory::load($this->template);

			//SET SHEET Mahasiswa
			$objPHPExcel->setActiveSheetIndex(0);
			$baseRow = 3;
			foreach($data0 as $r => $dataRow) {
				$row = $baseRow + $r;
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
									->setCellValue('B'.$row, $dataRow['nim'])
									->setCellValue('C'.$row, $dataRow['nama'])
									->setCellValue('D'.$row, $dataRow['tgl_lahir'])
									->setCellValue('E'.$row, $dataRow['jk'])
									->setCellValue('F'.$row, $dataRow['agama'])
									->setCellValue('G'.$row, $dataRow['ds_kel'])
									->setCellValue('H'.$row, $dataRow['wilayah'])
									->setCellValue('I'.$row, $dataRow['nm_ibu'])
									->setCellValue('J'.$row, $dataRow['kode_prodi'])
									->setCellValue('K'.$row, $dataRow['tgl_masuk'])
									->setCellValue('L'.$row, $dataRow['smt_masuk'])
									->setCellValue('M'.$row, $dataRow['stat_mhs'])
									->setCellValue('N'.$row, $dataRow['stat_awal'])
									->setCellValue('O'.$row, $dataRow['sks_diakui'])
									->setCellValue('P'.$row, $dataRow['pt_asal'])
									->setCellValue('Q'.$row, $dataRow['prodi_asal']);
			}
			$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

			//SET SHEET Mahasiswa Lulus/DO
			$objPHPExcel->setActiveSheetIndex(1);
			$baseRow1 = 3;
			foreach($data1 as $r => $dataRow) {
				$row = $baseRow1 + $r;
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
									->setCellValue('B'.$row, $dataRow['nim'])
									->setCellValue('C'.$row, $dataRow['nama'])
									->setCellValue('D'.$row, $dataRow['jenis_keluar'])
									->setCellValue('E'.$row, $dataRow['tgl_keluar'])
									->setCellValue('F'.$row, $dataRow['jalur_skripsi'])
									->setCellValue('G'.$row, $dataRow['judul_skripsi'])
									->setCellValue('H'.$row, $dataRow['bulan_awal_bimbingan'])
									->setCellValue('I'.$row, $dataRow['bulan_akhir_bimbingan'])
									->setCellValue('J'.$row, $dataRow['sk_yudisium'])
									->setCellValue('K'.$row, $dataRow['tgl_yudisium'])
									->setCellValue('L'.$row, $dataRow['ipk'])
									->setCellValue('M'.$row, $dataRow['no_seri_ijazah'])
									->setCellValue('N'.$row, $dataRow['keterangan']);
			}
			$objPHPExcel->getActiveSheet()->removeRow($baseRow1-1,1);

			//SET SHEET Nilai Pindahan
			$objPHPExcel->setActiveSheetIndex(2);
			$baseRow2 = 3;
			foreach($data2 as $r => $dataRow) {
				$row = $baseRow2 + $r;
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
									->setCellValue('B'.$row, $dataRow['nim'])
									->setCellValue('C'.$row, $dataRow['nama'])
									->setCellValue('D'.$row, $dataRow['kode_mk_asal'])
									->setCellValue('E'.$row, $dataRow['nm_mk_asal'])
									->setCellValue('F'.$row, $dataRow['sks_asal'])
									->setCellValue('G'.$row, $dataRow['nh_asal'])
									->setCellValue('H'.$row, $dataRow['kode_mk_diakui'])
									->setCellValue('I'.$row, $dataRow['nm_mk_diakui'])
									->setCellValue('J'.$row, $dataRow['nh_akui'])
									->setCellValue('K'.$row, $dataRow['na_akui'])
									->setCellValue('L'.$row, $dataRow['sks_akui']);
			}
			$objPHPExcel->getActiveSheet()->removeRow($baseRow2-1,1);

			$filename = time().'-template-mhs.xlsx';

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save('php://output');
			$temp_tulis = $objWriter->save('temps/'.$filename);
			$this->benchmark->mark('selesai');
			$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');
			if ($temp_tulis==NULL) {
				echo "<div class=\"bs-callout bs-callout-success\">
						File berhasil digenerate dalam waktu <strong>".$time_eks." detik</strong>. <br />Klik <a href=\"".base_url()."index.php/file/download/".$filename."\">disini</a> untuk download file
					</div>";
			} else {
				echo "<div class=\"bs-callout bs-callout-danger\">
						<h4>Error</h4>File tidak bisa digenerate. Folder 'temps' tidak ada atau tidak bisa ditulisi.
					</div>";
			}
		}
	}

	public function jsonMHS()
	{
		$search = $this->input->post('search');
		$sSearch = trim($search['value']);

		//$Data = $this->input->get('columns');
		$orders = $this->input->post('order');
		//$temp_order = 

		$iStart = $this->input->post('start');
		$iLength = $this->input->post('length');

		$temp_limit = $iLength;
		$temp_offset = $iStart?$iStart : 0;
		$temp_total = $this->feeder->count_all($this->session->userdata('token'),$this->table1,$this->filter);
		$totalData = $temp_total['result'];
		$totalFiltered = $totalData;

		if (!empty($sSearch)) {
			$temp_filter = "((nm_pd LIKE '%".$sSearch."%') OR (nipd LIKE '%".$sSearch."%') AND (p.id_sp='".$this->session->userdata('id_sp')."'))";
			//$temp_filter = "nm_pd like '%".$sSearch."%' OR nipd like '%".$sSearch."%'";
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table1, $temp_filter,
												'nipd DESC',$temp_limit,$temp_offset
						);
			//$totalFiltered = count($temp_rec['result']);
			$__total = $this->feeder->count_all($this->session->userdata('token'),$this->table1,$temp_filter);
			$totalFiltered = $__total['result'];
		} else {
			$temp_filter = "p.id_sp='".$this->session->userdata('id_sp')."'";
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table1, $temp_filter,
												'nipd DESC',$temp_limit,$temp_offset
						);
			//var_dump($temp_rec['result']);
		}
		$temp_error_code = $temp_rec['error_code'];
		$temp_error_desc = $temp_rec['error_desc'];

		if (($temp_error_code==0) && ($temp_error_desc=='')) {
			$temp_data = array();
			$i=0;
			foreach ($temp_rec['result'] as $key) {
				$temps = array();
				$temps[] = ++$i+$temp_offset;
				$temps[] = $key['nipd'];
				$temps[] = $key['nm_pd'];
				$temps[] = date('d-m-Y',strtotime($key['tgl_lahir']));
				//$temps[] = $key['fk__sms'];
				$filter_sms = "id_sms='".$key['id_sms']."'";
				$temp_sms = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_sms);
				//var_dump($temp_sms['result']);
				$filter_jenjang = "id_jenj_didik='".$temp_sms['result']['id_jenj_didik']."'";
				$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);
				//var_dump($temp_jenjang['result']);
				$link = $key['id_jns_daftar']==2?' <a href="javascript:void();" class="modalButton" data-toggle="modal" data-src="'.base_url().'mahasiswa/nilaipindah/'.$key['id_reg_pd'].'" data-target="#modalku"><i class="fa fa-external-link"></i></a>':'';
				$temps[] = $temp_jenjang['result']['nm_jenj_didik'].' '.$key['fk__sms'];
				//$temps[] = $key['fk__jns_daftar'].$link;
				$temps[] = $key['fk__jns_daftar'];
				$temps[] = substr($key['mulai_smt'], 0,4);

				$temp_label = strtoupper(substr($key['fk__jns_keluar'], 0,1));
				$label = $temp_label==''?'label-primary':'';
				$label .= $temp_label=='L'?'label-success':'';
				$label .= $temp_label=='M'?'label-danger':'';
				$label .= $temp_label=='D'?'label-warning':'';
				$label .= $temp_label=='N'?'label-default':'';
				$label .= $temp_label=='C'?'label-info':'';
				$label .= $temp_label=='G'?'label-primary':'';
				$label .= $temp_label=='X'?'label-default':'';
				$status = $key['fk__jns_keluar']==''?'Aktif':$key['fk__jns_keluar'];
				$temps[] = '<span class="label '.$label.'">'.$status.'</span>';
				$temps[] = '<a href="#"><i class="fa fa-search-plus"></i></a>';
				$temp_data[] = $temps;
			}
			$temp_output = array(
									'draw' => intval($this->input->get('draw')),
									'recordsTotal' => intval( $totalData ),
									'recordsFiltered' => intval( $totalFiltered ),
									'data' => $temp_data
				);
			echo json_encode($temp_output);
		}
	}
}
