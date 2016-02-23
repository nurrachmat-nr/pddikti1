<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder File Module
 * 
 * @author 		Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @package     https://github.com/virbo/wsfeeder
 * 
*/

class Setup extends CI_Controller {

	//private $data;
	private $path_temps;
	private $xml_file;
	private $url_ws;

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->set();
	}

	public function set()
	{
		if ($this->input->post()) {
			$this->form_validation->set_rules('kode_pt','Kode Perguruan Tinggi','trim|required');
			$this->form_validation->set_rules('url_ws','URL Webservice Feeder','trim|required');
			if ($this->form_validation->run() == TRUE) {
				$kode_pt = $this->input->post('kode_pt', TRUE);
				$url_ws = $this->input->post('url_ws', TRUE);

				$data = $kode_pt.'#'.$url_ws;
				$temp = write_file('setting.ini',$data);
				if ($temp) {
					$this->session->set_flashdata('sukses','Setup WSClient berhasil dibuat');
					redirect('setup');
				}
			}
		}
		$data['site_title'] = 'Setup WSClient';
		$this->load->view('tpl/setup_view',$data);
	}
}
