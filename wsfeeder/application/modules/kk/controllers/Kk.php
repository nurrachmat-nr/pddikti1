<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder Kebutuhan Khusus Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Kk extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $table;
	private $tabel2;

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
			$this->table = 'kebutuhan_khusus';
			$this->load->model('m_feeder','feeder');
		}
	}
	
	public function index()
	{
		$this->k_khusus();
		//$this->jggrid();
	}

	public function jggrid()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table, $this->filter);
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Kebutuhan Khusus';
		$data['title_page'] = 'Daftar Kebutuhan Khusus';
		$data['assign_js'] = 'js/jggrid.js';
		$data['assign_modal'] = '';
		tampil('jggrid_view',$data);
	}

	public function jsonJG()
	{
		/*$page = $_GET['page']; // get the requested page 
		$limit = $_GET['rows']; // get how many rows we want to have into the grid 
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
		$sord = $_GET['sord']; // get the direction*/
		$page = $this->input->get('page');
		$limit = $this->input->get('rows');
		//$limit = isset($_GET['rows'])?$_GET['rows']:10;
		$sidx = $this->input->get('sidx');
		$sord = $this->input->get('sord');
		/*$page = isset($_GET['page'])?$_GET['page']:1;
		$limit = isset($_GET['rows'])?$_GET['rows']:50;
		$sidx = isset($_GET['sidx'])?$_GET['sidx']:'nm_kk';
		$order = isset($_GET['sord'])?$_GET['sord']:'id_kk';*/
		if(!$sidx) $sidx =1;

		$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $this->filter,
												$sidx. ' '.$sord,$limit,$page
						);
		$temp_totals = $this->feeder->count_all($this->session->userdata('token'),$this->table,$this->filter);
		$temp_total = $temp_totals['result'];
		//var_dump($temp_total['result']);
		if ($temp_total > 0) {
			$total_pages = ceil($temp_total/$limit);
		} else {
			$total_pages = 0;
		}

		if ($page > $total_pages) $page=$total_pages;
			$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		if($start < 0) $start = 0;
		if(!$sidx) $sidx =1;
		/*
		$data->records = $temp_total;
		$data->page = $page;
		$data->total = $total_pages;
		$i=0;
		foreach ($temp_rec['result'] as $key) {
			$data->key[$i]['id_kk'] = $key['id_kk'];
			$data->key[$i]['nm_kk'] = $key['nm_kk'];
			++$i;
		}
		echo json_encode($data);*/
		$i=0;
		$test = array();
		foreach ($temp_rec['result'] as $key) {
			/*$data->key[$i]['id_kk'] = $key['id_kk'];
			$data->key[$i]['nm_kk'] = $key['nm_kk'];
			++$i;*/
			$test[$i]['id_kk'] = $key['id_kk'];
			$test[$i]['nm_kk'] = $key['nm_kk'];
			++$i;
		}
		//var_dump($test);
		//echo json_encode($test);
		
		$output = array('records' => $temp_total,
						'page' => $page,
						'total' => $total_pages,
						'rows' => $test
			);
		echo json_encode($output);
		//var_dump($output);
	}

	public function k_khusus()
	{
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table, $this->filter);
		$temp_sp = $this->session->userdata('id_sp');
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Kebutuhan Khusus';
		$data['title_page'] = 'Daftar Kebutuhan Khusus';
		$data['assign_js'] = 'js/kebutuhan_dt.js';
		$data['assign_modal'] = '';
		tampil('kebutuhan_view',$data);
	}

	public function test_dt()
	{
		$this->load->library('dt_feeder');
		$this->dt_feeder->select('id_kk, nm_kk')
						->from($this->table);
		echo $this->dt_feeder->generate();
	}

	public function jsonKk()
	{
		$temp_colum = array(0 => 'id_kk',1 => 'nm_kk');
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
			$temp_filter = "nm_kk like '%".$sSearch."%'";
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
				$temps[] = $key['id_kk'];
				$temps[] = $key['nm_kk'];
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
