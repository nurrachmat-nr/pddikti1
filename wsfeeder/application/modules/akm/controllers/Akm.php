<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder AKM Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Akm extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $table;
	private $template;

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
			$this->table = 'kuliah_mahasiswa';
			$this->load->model('m_feeder','feeder');
			$this->load->helper('csv');
			$this->load->library('excel');
			$this->template = './template/akm_template.xlsx';

			$config['upload_path'] = $this->config->item('upload_path');
			$config['allowed_types'] = $this->config->item('upload_tipe');
			$config['max_size'] = $this->config->item('upload_max_size');
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload',$config);
		}
	}
	
	public function index()
	{
		$this->akm();
	}

	public function akm()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}

		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Aktifitas Kuliah Mahasiswa';
		$data['title_page'] = 'Aktifitas Kuliah Mahasiswa';
		$data['ket_page'] = 'Digunakan untuk mengelola status keaktifan ( Aktif, Cuti, Non Aktif dll) mahasiswa per periode';
		$data['assign_js'] = 'js/akm_dt.js';
		$data['assign_modal'] = '';
		tampil('akm_view',$data);
	}

	public function uploadexcel()
	{
		$this->benchmark->mark('mulai');
		if (!$this->upload->do_upload()) {
			echo "<div class=\"bs-callout bs-callout-danger\">".$this->upload->display_errors()."</div>";
		} else {
			$file_data = $this->upload->data();
			$file_path = $this->config->item('upload_path').$file_data['file_name'];
			$objPHPExcel = PHPExcel_IOFactory::load($file_path);
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
				$id_reg_pd = array();
				$sukses_count = 0;
				$sukses_msg = '';
				$error_count = 0;
				$error_msg = array();
				$error_nim = array();
				foreach ($arr_data as $value) {
					$npm = $value['B'];
					$smt = $value['D'];
					//$filter_npm = "nipd like '%".$npm."%'";
					$filter_npm = "nipd LIKE '%".$npm."%' AND p.id_sp='".$this->session->userdata('id_sp')."'";
					$temp_npm = $this->feeder->getrecord($this->session->userdata('token'),'mahasiswa_pt',$filter_npm);
					//var_dump($temp_npm);
					if ($temp_npm['result']) {
						$id_reg_pd = $temp_npm['result']['id_reg_pd'];
						$stat_reg = TRUE;
					}
					$temp_data[] = array('id_smt' => $value['D'],
									  'id_reg_pd' => $id_reg_pd,
									  		'ips' => $value['E'],
									  	'sks_smt' => $value['G'],
									  		'ipk' => $value['F'],
									  'sks_total' => $value['H'],
									 'id_stat_mhs' => $value['I']
									);
					//var_dump($temp_data);
					$temp_key = array('id_smt' => $value['D'],
									  'id_reg_pd' => $id_reg_pd
									);
					$temp_data2 = array('ips' => $value['E'],
									  	'sks_smt' => $value['G'],
									  		'ipk' => $value['F'],
									  'sks_total' => $value['H'],
									 'id_stat_mhs' => $value['I']
									);
					$array[] = array('key'=>$temp_key,'data'=>$temp_data2);
				}
				//updaterset($token,$table,$records)
				$mode = $this->input->post('mode');
				if ($mode==0) {
					$temp_result = $this->feeder->insertrset($this->session->userdata['token'], $this->table, $temp_data);
				} else {
					$temp_result = $this->feeder->updaterset($this->session->userdata['token'], $this->table, $array);
				}
				//$temp_result = $this->feeder->insertrset($this->session->userdata['token'], $this->table, $temp_data);
				$this->benchmark->mark('selesai');
				$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');
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

				if ((!$sukses_count==0) || (!$error_count==0)) {
					echo "<div class=\"alert alert-warning\" role=\"alert\">
							Waktu eksekusi ".$time_eks." detik<br />
							Results (total ".$i." baris data):<br /><font color=\"#3c763d\">".$sukses_count." data AKM berhasil ditambah/update</font><br />
							<font color=\"#ce4844\" >".$error_count." data error (tidak bisa ditambahkan) </font>";
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
		}
	}

	public function createexcel()
	{
		$this->benchmark->mark('mulai');
		if (!file_exists($this->template)) {
			echo "<div class=\"bs-callout bs-callout-danger\">
					<h4>Error</h4>File template tidak tersedia.
				</div>";
		} else {
			$objPHPExcel = PHPExcel_IOFactory::load($this->template);
			$objPHPExcel->setActiveSheetIndex(0);
			$data = array(array('nim' => '1234',
						'nama' => 'Mahasiswa 1',
						'semester' => '20142',
						'ips' => 3,
						'ipk' => '2,75',
						'sks_semester' => 25,
						'sks_total' => 50,
						'status_mhs' => 'A'
					),
				  array('nim' => '2345',
						'nama' => 'Mahasiswa 2',
						'semester' => '20142',
						'ips' => '2,75',
						'ipk' => '2,75',
						'sks_semester' => 20,
						'sks_total' => 50,
						'status_mhs' => 'A'
					),
				  array('nim' => '3456',
						'nama' => 'Mahasiswa 3',
						'semester' => '20141',
						'ips' => 2,
						'ipk' => '2,80',
						'sks_semester' => 18,
						'sks_total' => 80,
						'status_mhs' => 'L'
					)
				);
			$baseRow = 3;
			foreach($data as $r => $dataRow) {
				$row = $baseRow + $r;
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
									->setCellValue('B'.$row, $dataRow['nim'])
									->setCellValue('C'.$row, $dataRow['nama'])
									->setCellValue('D'.$row, $dataRow['semester'])
									->setCellValue('E'.$row, $dataRow['ips'])
									->setCellValue('F'.$row, $dataRow['ipk'])
									->setCellValue('G'.$row, $dataRow['sks_semester'])
									->setCellValue('H'.$row, $dataRow['sks_total'])
									->setCellValue('I'.$row, $dataRow['status_mhs']);
			}
			$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);

			$filename = time().'-template-akm.xlsx';

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			//$objWriter->save('php://output');
			$temp_tulis = $objWriter->save('temps/'.$filename);
			$this->benchmark->mark('selesai');
			$time_eks = $this->benchmark->elapsed_time('mulai', 'selesai');
			if ($temp_tulis==NULL) {
				echo "<div class=\"bs-callout bs-callout-success\">
						File berhasil digenerate dalam waktu <strong>".$time_eks." detik</strong>. <br />Klik <a href=\"".base_url()."temps/".$filename."\">disini</a> untuk download file
					</div>";    
			} else {
				echo "<div class=\"bs-callout bs-callout-danger\">
						<h4>Error</h4>File tidak bisa digenerate. Folder 'temps' tidak ada atau tidak bisa ditulisi.
					</div>";
			}
		}
	}

	public function jsonAKM()
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
		$temp_total = $this->feeder->count_all($this->session->userdata('token'),$this->table,$this->filter);
		$totalData = $temp_total['result'];
		$totalFiltered = $totalData;
		if (!empty($sSearch)) {
			$temp_filter = "id_smt like '%".$sSearch."%'";
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $temp_filter, 
												'id_smt DESC', $temp_limit,
												$temp_offset
								);
			$__total = $this->feeder->count_all($this->session->userdata('token'),$this->table,$temp_filter);
			$totalFiltered = $__total['result'];
		} else {
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $this->filter, 
												'id_smt DESC', $temp_limit,$temp_offset
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
				
				$filter_nim = "id_reg_pd='".$key['id_reg_pd']."'";
				$temp_nim = $this->feeder->getrecord($this->session->userdata('token'),'mahasiswa_pt',$filter_nim);

				$filter_sms = "id_sms='".$temp_nim['result']['id_sms']."'";
				$temp_sms = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_sms);
				$filter_jenjang = "id_jenj_didik='".$temp_sms['result']['id_jenj_didik']."'";
				$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);

				$filter_smt = "id_smt='".$key['id_smt']."'";
				$temp_smt = $this->feeder->getrecord($this->session->userdata('token'),'semester',$filter_smt);

				$temps[] = ++$i+$temp_offset;
				$temps[] = $temp_nim['result']['nipd'];
				$temps[] = $temp_nim['result']['nm_pd'];
				$temps[] = $temp_jenjang['result']['nm_jenj_didik'].' '.$temp_nim['result']['fk__sms'];
				$temps[] = $temp_smt['result']['nm_smt'];
				$temps[] = substr($temp_nim['result']['mulai_smt'],0,-1);
				$temps[] = $key['ips'];
				$temps[] = $key['ipk'];
				$temps[] = $key['sks_smt'];
				$temps[] = $key['sks_total'];
				$temps[] = $key['id_stat_mhs'];
				$temps[] = 'Lihat';
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
