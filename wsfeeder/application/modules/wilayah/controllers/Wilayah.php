<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder Wilayah Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Wilayah extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $table;

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
			$this->table = 'wilayah';
			$this->load->model('m_feeder','feeder');
		}
	}
	
	public function index()
	{
		$this->wilayah();
	}

	public function wilayah()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Wilayah';
		$data['title_page'] = 'Daftar Wilayah';
		$data['assign_js'] = 'js/wilayah_dt.js';
		$data['assign_modal'] = '';
		tampil('wilayah_view',$data);
	}

	public function jsonWil()
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
			$temp_filter = "nm_wil like '%".$sSearch."%'";
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $temp_filter,'',
												$temp_limit,$temp_offset
						);
			//$totalFiltered = count($temp_rec['result']);
			$__total = $this->feeder->count_all($this->session->userdata('token'),$this->table,$temp_filter);
			$totalFiltered = $__total['result'];
		} else {
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $this->filter,'',
												$temp_limit,$temp_offset
						);
		}
		$temp_error_code = $temp_rec['error_code'];
		$temp_error_desc = $temp_rec['error_desc'];

		if (($temp_error_code==0) && ($temp_error_desc=='')) {
			$temp_data = array();
			$i=0;
			foreach ($temp_rec['result'] as $key) {
				$temps = array();
				//$temps[] = '<a href="oke">Test</a>';
				$temps[] = ++$i+$temp_offset;
				$temps[] = $key['id_wil'];
				$temps[] = $key['nm_wil'];
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
