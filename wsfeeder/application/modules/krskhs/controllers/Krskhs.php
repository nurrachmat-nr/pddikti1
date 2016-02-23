<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client KRS/KHS Mahasiswa Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Krskhs extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $tbl_mhs;
	private $tbl_mhspt;
	private $tbl_nilai;

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
			$this->tbl_mhs = 'mahasiswa';
			$this->tbl_mhspt = 'mahasiswa_pt';
			$this->tbl_nilai = 'nilai';
			$this->load->model('m_feeder','feeder');
			$this->load->helper('csv');
			$this->load->library('excel');
			$this->template = './template/krs_khs_template.xlsx';

			$config['upload_path'] = $this->config->item('upload_path');
			$config['allowed_types'] = $this->config->item('upload_tipe');
			$config['max_size'] = $this->config->item('upload_max_size');
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload',$config);
		}
	}
	
	public function index()
	{
		$this->krs();
	}

	public function khs()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->tbl_mhs, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Export Data KHS';
		$data['title_page'] = 'Export Data KHS';
		$data['ket_page'] = 'Halaman ini digunakan untuk eksport data KHS mahasiswa';
		$data['assign_js'] = 'js/krskhs.js';
		$data['assign_modal'] = '';
		tampil('khs_view',$data);
	}

	public function krs()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->tbl_mhs, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Export Data KRS';
		$data['title_page'] = 'Export Data KRS';
		$data['ket_page'] = 'Halaman ini digunakan untuk eksport data KRS mahasiswa';
		$data['assign_js'] = 'js/krskhs.js';
		$data['assign_modal'] = '';
		tampil('krs_view',$data);
	}

	public function createkhs()
	{
		$this->benchmark->mark('mulai');
		$sms = '';
		$id_jenjang = '';
		$nm_jenj_didik = '';
		$nm_smt = '';
		$nim = '';
		$nm_mhs = '';
		$id_kls = '';
		$krs = '';
		$temp_data = array();
		$id_reg_pd = $this->input->post('mhs',TRUE);
		$id_sms = $this->input->post('prodi',TRUE);
		$id_smt = $this->input->post('periode', TRUE);

		if (($id_reg_pd!='') && ($id_sms!='') && ($id_smt!='')) {
			$filter_prodi = "id_sms='".$id_sms."'";
			$temp_prodi = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_prodi);
			if ($temp_prodi['result']) {
				$sms = $temp_prodi['result']['nm_lemb'];
				$id_jenjang = $temp_prodi['result']['id_jenj_didik'];
			}
			$filter_jenjang = "id_jenj_didik='".$id_jenjang."'";
			$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);
			if ($temp_jenjang['result']) {
				$nm_jenj_didik = $temp_jenjang['result']['nm_jenj_didik'];
			}

			$filter_nilai = "p.id_reg_pd='".$id_reg_pd."'";
			$temp_nilai = $this->feeder->getrset($this->session->userdata('token'),$this->tbl_nilai,$filter_nilai,pos_($id_reg_pd,$id_smt),'','');

			$filter_smt = "id_smt='".$id_smt."' AND a_periode_aktif=1";
			$temp_smt = $this->feeder->getrecord($this->session->userdata('token'),'semester',$filter_smt);
			if ($temp_smt['result']) {
				$nm_smt = $temp_smt['result']['nm_smt'];
			}

			$filter_mhs = "id_reg_pd='".$id_reg_pd."'";
			$temp_mhs = $this->feeder->getrecord($this->session->userdata('token'),$this->tbl_mhspt,$filter_mhs);
			if ($temp_mhs['result']) {
				$nim = $temp_mhs['result']['nipd'];
				$nm_mhs = $temp_mhs['result']['nm_pd'];
			}
			//var_dump($temp_nilai['result']);

			if ($temp_nilai['result']) {
				foreach ($temp_nilai['result'] as $key => $value) {
					$content = array('kode_mk' => $value['kode_mk'],
										'nm_mk' => $value['nm_mk'],
										'sks' => $value['sks_mk'],
										'nilai_huruf' => $value['nilai_huruf'],
										'nilai_indeks' => $value['nilai_indeks']
								);
					$temp_data[] = $content;
				}
				//var_dump($temp_data);
				$objPHPExcel = PHPExcel_IOFactory::load($this->template);

				//SET SHEET KHS
				$objPHPExcel->setActiveSheetIndex(1);
				$objPHPExcel->getActiveSheet()->setCellValue('E7', $nm_smt);
				$objPHPExcel->getActiveSheet()->setCellValue('E8', $nm_jenj_didik.' '.$sms);
				$objPHPExcel->getActiveSheet()->setCellValue('E9', $nim);
				$objPHPExcel->getActiveSheet()->setCellValue('E10', $nm_mhs);
				$baseRow = 15;
				foreach($temp_data as $r => $dataRow) {
					$row = $baseRow + $r;
					$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
										->setCellValue('B'.$row, $dataRow['kode_mk'])
										->setCellValue('C'.$row, $dataRow['nm_mk'])
										->setCellValue('F'.$row, $dataRow['sks'])
										->setCellValue('G'.$row, $dataRow['nilai_huruf'])
										->setCellValue('H'.$row, $dataRow['nilai_indeks'])
										->setCellValue('I'.$row, '=F'.$row.'*H'.$row);
					$temp_row = 6+$row;
				}
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$temp_row, date('d F Y'));
				$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
				$filename = time().'-template-khs.xlsx';

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
			} else {
				echo "<div class=\"bs-callout bs-callout-success\">
							KRS Kosong
						</div>";
			}
		}
	}

	public function createkrs()
	{
		$this->benchmark->mark('mulai');
		$sms = '';
		$id_jenjang = '';
		$nm_jenj_didik = '';
		$nm_smt = '';
		$nim = '';
		$nm_mhs = '';
		$id_kls = '';
		$krs = '';
		$temp_data = array();
		$id_reg_pd = $this->input->post('mhs',TRUE);
		$id_sms = $this->input->post('prodi',TRUE);
		$id_smt = $this->input->post('periode', TRUE);

		if (($id_reg_pd!='') && ($id_sms!='') && ($id_smt!='')) {
			$filter_prodi = "id_sms='".$id_sms."'";
			$temp_prodi = $this->feeder->getrecord($this->session->userdata('token'),'sms',$filter_prodi);
			if ($temp_prodi['result']) {
				$sms = $temp_prodi['result']['nm_lemb'];
				$id_jenjang = $temp_prodi['result']['id_jenj_didik'];
			}
			$filter_jenjang = "id_jenj_didik='".$id_jenjang."'";
			$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);
			if ($temp_jenjang['result']) {
				$nm_jenj_didik = $temp_jenjang['result']['nm_jenj_didik'];
			}

			$filter_nilai = "p.id_reg_pd='".$id_reg_pd."'";
			$temp_nilai = $this->feeder->getrset($this->session->userdata('token'),$this->tbl_nilai,$filter_nilai,str($id_reg_pd,$id_smt),'','');

			$filter_smt = "id_smt='".$id_smt."' AND a_periode_aktif=1";
			$temp_smt = $this->feeder->getrecord($this->session->userdata('token'),'semester',$filter_smt);
			if ($temp_smt['result']) {
				$nm_smt = $temp_smt['result']['nm_smt'];
			}

			$filter_mhs = "id_reg_pd='".$id_reg_pd."'";
			$temp_mhs = $this->feeder->getrecord($this->session->userdata('token'),$this->tbl_mhspt,$filter_mhs);
			if ($temp_mhs['result']) {
				$nim = $temp_mhs['result']['nipd'];
				$nm_mhs = $temp_mhs['result']['nm_pd'];
			}

			if ($temp_nilai['result']) {
				foreach ($temp_nilai['result'] as $key => $value) {
					$content = array('kode_mk' => $value['kode_mk'],
										'nm_mk' => $value['nm_mk'],
										'sks' => $value['sks_mk']
								);
					$temp_data[] = $content;
				}
				//var_dump($temp_data);
				$objPHPExcel = PHPExcel_IOFactory::load($this->template);

				//SET SHEET KRS
				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->setCellValue('E7', $nm_smt);
				$objPHPExcel->getActiveSheet()->setCellValue('E8', $nm_jenj_didik.' '.$sms);
				$objPHPExcel->getActiveSheet()->setCellValue('E9', $nim);
				$objPHPExcel->getActiveSheet()->setCellValue('E10', $nm_mhs);
				$baseRow = 15;
				foreach($temp_data as $r => $dataRow) {
					$row = $baseRow + $r;
					$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $r+1)
										->setCellValue('B'.$row, $dataRow['kode_mk'])
										->setCellValue('C'.$row, $dataRow['nm_mk'])
										->setCellValue('J'.$row, $dataRow['sks']);
					$temp_row = 4+$row;
				}
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$temp_row, date('d F Y'));
				$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
				$filename = time().'-template-krs.xlsx';

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
			} else {
				echo "<div class=\"bs-callout bs-callout-success\">
							KRS Kosong
						</div>";
			}
		}
	}

	public function mhslist()
	{
		$cari = $this->input->post('q');
		$temp_cari = $cari==''?'':$cari;
		$page = $this->input->post('page');
		$filter = "((nm_pd LIKE '%".$temp_cari."%') OR (nipd LIKE '%".$temp_cari."%')) AND p.id_sp='".$this->session->userdata('id_sp')."'";
		$temp_rec = $this->feeder->getrset($this->session->userdata('token'),$this->tbl_mhspt,$filter,'nipd DESC',$page,'');
		//var_dump($temp_rec['result']);
		$temp = array();
		if ($temp_rec['result']) {
			foreach ($temp_rec['result'] as $key => $value) {
				$status = $value['fk__jns_keluar']==''?'Aktif':$value['fk__jns_keluar'];
				$temp[] = array('id_reg_pd' => $value['id_reg_pd'],
								'nipd' => $value['nipd'],
								'nm_pd' => $value['nm_pd'],
								'id_stat' => $status
							);
			}
		}
		echo json_encode($temp);
	}

	public function prodilist()
	{
		$cari = $this->input->post('q');
		$temp_cari = $cari==''?'':$cari;
		$page = $this->input->post('page');

		$filter = "((kode_prodi LIKE '%".$temp_cari."%') OR (nm_lemb LIKE '%".$temp_cari."%')) AND id_sp='".$this->session->userdata('id_sp')."'";
		$temp_rec = $this->feeder->getrset($this->session->userdata('token'),'sms',$filter,'',$page,'');
		$temp = array();
		if ($temp_rec['result']) {
			$id_jenj_didik = '';
			foreach ($temp_rec['result'] as $key => $value) {
				$filter_jenjang = "id_jenj_didik='".$value['id_jenj_didik']."'";
				$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);
				if ($temp_jenjang['result']) {
					$id_jenj_didik = $temp_jenjang['result']['nm_jenj_didik'];
				}
				$temp[] = array('kode_prodi' => $value['kode_prodi'],
								'prodi' => $value['nm_lemb'],
								'jenjang' => $id_jenj_didik,
								'id_sms' => $value['id_sms']
							);
			}
		}
		echo json_encode($temp);
	}

	public function periodelist()
	{
		$cari = $this->input->post('q');
		$temp_cari = $cari==''?'':$cari;
		$page = $this->input->post('page');

		$filter = "((id_smt LIKE '%".$temp_cari."%') OR (nm_smt LIKE '%".$temp_cari."%')) AND a_periode_aktif='1'";
		//$filter = "nm_smt LIKE '%".$temp_cari."%' AND a_periode_aktif=1";
		$temp_rec = $this->feeder->getrset($this->session->userdata('token'),'semester',$filter,'id_smt DESC',$page,'');
		//var_dump($temp_rec['result']);

		$temp = array();
		if ($temp_rec['result']) {
			foreach ($temp_rec['result'] as $key => $value) {
				$temp[] = array('id_smt' => $value['id_smt'],
								'nm_smt' => $value['nm_smt']
							);
			}
		}
		echo json_encode($temp);
	}
}
