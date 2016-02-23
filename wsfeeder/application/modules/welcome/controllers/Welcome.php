<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder Welcome Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Welcome extends CI_Controller {

	//private $data;
	private $limit;
	private $filter;
	private $order;
	private $offset;
	private $table;
	private $url_ws;
	private $url_update;
	private $path_temps;
	//private $xml_file;
	private $temp_ws;
	private $temp_setting;
	private $npsn;
	private $dir_ws;

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
			$this->url_ws = $this->config->item('url_ws');
			$this->url_update = $this->config->item('url_update');
			$this->path_temps = $this->config->item('path_temps');
			$this->table = 'sms';
			$this->load->model('m_feeder','feeder');
			$this->load->library('unzip');
			$this->temp_ws = json_decode(read_file('wsclient.dat'));
			$temp_setting = read_file('setting.ini');
			$pecah = explode('#', $temp_setting);
			$this->npsn = $pecah[0];
			$this->dir_ws = $pecah[1];
		}
	}
	
	public function index()
	{
		$this->getProdi();
	}

	public function table()
	{
		$temp_rec = $this->feeder->listtable($this->session->userdata('token'));

		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Tabel Webservice';
		$data['title_page'] = 'Daftar Tabel Webservice';
		$data['assign_js'] = 'js/table_dt.js';
		$data['assign_modal'] = '';
		tampil('table_view',$data);	
	}

	public function setting()
	{
		echo "Kode SP: ".$this->session->userdata['id_sp'];
		if ($this->input->post()) {
			$this->form_validation->set_rules('kode_pt','Kode Perguruan Tinggi','trim|required');
			$this->form_validation->set_rules('url_ws','URL Webservice Feeder','trim|required');
			if ($this->form_validation->run() == TRUE) {
				$kode_pt = $this->input->post('kode_pt', TRUE);
				$url_ws = $this->input->post('url_ws', TRUE);
				$data = $kode_pt.'#'.$url_ws;
				$temp = write_file('setting.ini',$data);
				if ($temp) {
					$filter_sp = "npsn = '".$kode_pt."'";
					$temp_sp = $this->feeder->getrecord($this->session->userdata['token'],'satuan_pendidikan',$filter_sp);
					//var_dump($temp_sp);
					if ($temp_sp['result']) {
						$id_sp = $temp_sp['result']['id_sp'];
						$nm_lemb = $temp_sp['result']['nm_lemb'];
					} else {
						$id_sp = '0';
					}

					//$this->session->set_userdata('')
					$sessi = array('kode_pt' => $kode_pt,
									'id_sp' => $id_sp,
									'nm_lemb' => $nm_lemb
							);
					//$this->session->set_userdata('id_sp',$id_sp);
					$this->session->set_userdata($sessi);
					$this->session->set_flashdata('sukses','Settingan WSClient berhasil disimpan');
					redirect('welcome/setting');
				}
			}
		}
		$data['site_title'] = 'Setting WSClient';
		$data['title_page'] = 'Setting WSClient';
		$data['ket_page'] = 'Halaman ini digunakan untuk setting Aplikasi WSClient';
		$data['kode_pt'] = $this->npsn;
		$data['dir_ws'] = $this->dir_ws;
		$data['assign_js'] = '';
		$data['assign_modal'] = '';
		tampil('setting_view',$data);
	}

	public function getProdi()
	{
		$temp_sp = $this->session->userdata('id_sp');
		$filter_sms= "id_sp = '".$temp_sp."'";
		$temp_rec = $this->feeder->getrecord($this->session->userdata('token'), $this->table, $filter_sms);
		
		if (($temp_rec['error_desc']=='') && ($temp_sp=='') ){
			$this->session->set_flashdata('error','Kode PT Anda tidak ditemukan, silahkan masukkan kode PT anda dengan benar');
			redirect('welcome/setting');
		}

		//var_dump($temp_rec);
		$data['error_code'] = $temp_rec['error_code'];
		$data['error_desc'] = $temp_rec['error_desc'];
		$data['site_title'] = 'Daftar Program Studi';
		$data['title_page'] = 'Daftar Program Studi';
		$data['assign_js'] = 'js/welcome_dt.js';
		$data['assign_modal'] = '';
		tampil('prodi_view',$data);
	}
	
	public function getSMS()
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

		$temp_sp = $this->session->userdata('id_sp');
		$filter_sms= "id_sp = '".$temp_sp."'";
		$temp_total = $this->feeder->count_all($this->session->userdata('token'),$this->table,$filter_sms);
		$totalData = $temp_total['result'];
		$totalFiltered = $totalData;

		if (!empty($sSearch)) {
			$temp_filter = "((nm_lemb like '%".$sSearch."%') OR (kode_prodi LIKE '%".$sSearch."%') AND (id_sp='".$temp_sp."'))";
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $temp_filter,'',
												$temp_limit,$temp_offset
						);
			$__total = $this->feeder->count_all($this->session->userdata('token'),$this->table,$temp_filter);
			$totalFiltered = $__total['result'];
		} else {
			$temp_rec = $this->feeder->getrset($this->session->userdata('token'),
												$this->table, $filter_sms,'',
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
				$filter_jenjang = "id_jenj_didik = ".$key['id_jenj_didik'];
				$temp_jenjang = $this->feeder->getrecord($this->session->userdata('token'),'jenjang_pendidikan',$filter_jenjang);
				$temps[] = ++$i;
				$temps[] = $key['nm_lemb'];
				$temps[] = $temp_jenjang['result']['nm_jenj_didik'];
				$temps[] = $key['kode_prodi'];
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

	public function jsonTable()
	{
		$temp_table = $this->feeder->listtable($this->session->userdata('token'));

		$temp_error_code = $temp_table['error_code'];
		$temp_error_desc = $temp_table['error_desc'];

		if (($temp_error_code==0) && ($temp_error_desc=='')) {
			$temp_data = array();
			$i=0;
			foreach ($temp_table['result'] as $key) {
				$temp_data[] = array('no' => ++$i,
									'nama_table' => $key['table'],
									'jenis' => $key['jenis'],
									'keterangan' => $key['keterangan'],
									'aksi' => '<a href="#"><i class="fa fa-bars"></i> Struktur</a> | <a href="#"><i class="fa fa-search"></i> View</a>'
					);
			}
			$temp_output = array(
								//'draw' => 1,
								//'recordsTotal' => 42,
								//'recordsFiltered' => 42,
								'data' => $temp_data
						);
			echo json_encode($temp_output);
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('ws');
	}

	public function token($uri='')
	{
		if (!empty($uri)) {
			$temp_uri = explode('-', $uri);
			$new_uri = $temp_uri[0].'/'.$temp_uri[1];

			$temp_token = $this->feeder->new_token($this->session->userdata('username'),$this->session->userdata('password'));
			$this->session->set_userdata('token',$temp_token);
			redirect(base_url().'index.php/'.$new_uri);
		}
	}

	public function update()
	{
		$versi = $this->temp_ws->version;
		$ping = ping($this->url_ws,80);
		if ($ping) {
			$temps_get = read_file($this->url_update.'fileku/'.$this->session->userdata('header'));
			$temps_decode = json_decode($temps_get);
			$data['jml'] = $temps_decode->jml;
			$data["data"] = $temps_decode->data;
			$data['is_connect'] = TRUE;
		} else {
			$data['is_connect'] = FALSE;
		}
		$data['site_title'] = 'Update WS Client';
		$data['title_page'] = 'Update WS Client';
		$data['versi'] = $versi;
		$data['assign_js'] = 'js/update.js';
		$data['assign_modal'] = '';
		tampil('update_view',$data);
	}

	public function update_core($id)
	{
		$error = '';
		$ping = ping($this->url_ws,80);
		if ($ping) {
			$temp_check = $this->url_update.'update/'.$this->session->userdata('header')."/".$id.".zip";
			//$temp_check = 'http://localhost/project/site_ws/file/update/'.$this->session->userdata('header')."/".$id.".zip";
			$temp_get = $this->getUrlContents($temp_check);	
			if (($temp_get==337) || ($temp_get==338)){
				$error = "<div class=\"bs-callout bs-callout-danger\">
							<h4>Error ".$temp_get."</h4>
							<p>Terjadi kesalahan dalam koneksi ke server. Pastikan koneksi internet Anda lancar jaya</p>
						  </div>";
			} elseif (($temp_get==286) || ($temp_get==603)) {
				$error = "<div class=\"bs-callout bs-callout-danger\">
							<h4>Error ".$temp_get."</h4>
							<p>Terjadi kesalahan dalam membaca path update di server.</p>
						  </div>";
			}
		} else {
			$error = "<div class=\"bs-callout bs-callout-danger\">
							<h4>Error</h4>
							<p>Terjadi kesalahan dalam koneksi ke server. Silahkan check koneksi internet Anda.</p>
						  </div>";
		}
		
		if ($error == '') {
			$test_unzip = $this->unzip->extract($this->path_temps.$id.".zip","./");
			if ($test_unzip) {
				@unlink($this->path_temps.$id.".zip");
				$this->session->sess_destroy();
				echo "<div class=\"bs-callout bs-callout-success\">
							<h4>Sukses</h4>
							<p>Aplikasi berhasil diupdate. Silahkan refresh aplikasi anda</p>
						  </div>";
			} else {
				@unlink($this->path_temps.$id.".zip");
				echo "<div class=\"bs-callout bs-callout-danger\">
							<h4>Error</h4>
							<p>Terjadi kesalahan saat ekstrak file.</p>
						  </div>";
			}
		} else {
			echo $error;
		}
	}

	function getUrlContents($url)
	{
		$url_parsed = parse_url($url);
		$host = $url_parsed["host"];
		//var_dump($url_parsed);
		if ($url == '' || $host == '') {
			return false;
		}
		$port = 80;
		$path = (empty($url_parsed["path"]) ? '/' : $url_parsed["path"]);
		//$path = base_url()."temps/";
		$path.= (!empty($url_parsed["query"]) ? '?'.$url_parsed["query"] : '');
		$out = "GET $path HTTP/1.0\r\nHost: $host\r\nConnection: Close\r\n\r\n";
		//echo $path;
		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		fwrite($fp, $out);
		$headers = '';
		$content = '';
		$buf = '';
		$isBody = false;
		while (!feof($fp) and !$isBody) {
			$buf = fgets($fp, 1024);
			if ($buf == "\r\n" ) {$isBody = true;}
			else{$headers .= $buf;}
		}
		//$file1 = fopen(basename("temps/".$url_parsed["path"]), 'w');
		$temp_ = basename($url_parsed["path"]);
		//echo($temp_);
		$file1 = fopen($this->path_temps.$temp_, 'w');
		$bytes=stream_copy_to_stream($fp,$file1);
		fclose($fp);
		return $bytes;
		
	}
}
