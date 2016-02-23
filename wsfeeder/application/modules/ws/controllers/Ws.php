<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WS Client Feeder Welcome Module
 * 
 * @author      Yusuf Ayuba
 * @copyright   2015
 * @link        http://jago.link
 * @github      https://github.com/virbo/wsfeeder
 * 
*/

class Ws extends CI_Controller {

	//private $data;
	private $dir_ws;
	private $npsn;
	private $temp_ws;

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login')) {
			redirect('welcome');
		} else {
			if (file_exists('setting.ini')) {
				$temp_setting = read_file('setting.ini');
				$pecah = explode('#', $temp_setting);
				$this->npsn = $pecah[0];
				$this->dir_ws = $pecah[1];
				$this->temp_ws = json_decode(read_file('wsclient.dat'));
			} else {
				redirect('setup');
			}
		}
	}

	public function index()
	{
		$this->login();
	}
	
	public function login()
	{
		if ($this->input->post()) {
			$this->form_validation->set_rules('username','Username Feeder','trim|required');
			$this->form_validation->set_rules('password','Password Feeder','required');

			if ($this->form_validation->run() == TRUE) {
				$username = $this->input->post('username',TRUE);
				$password = $this->input->post('password',TRUE);
				$temp_db = $this->input->post('db_ws',TRUE);

				$ws = $temp_db=='on'?$this->dir_ws.'live.php?wsdl':$this->dir_ws.'sandbox.php?wsdl';
				//echo $ws;
				$ws_client = new nusoap_client($ws, true);
				$temp_proxy = $ws_client->getProxy();
				$temp_error = $ws_client->getError();
				if ($temp_proxy==NULL) {
					$this->session->set_flashdata('error','Gagal melakukan koneksi ke Webservice Feeder.<br /><pre>'.$temp_error.'</pre>');
					redirect(base_url());
				} else {
					$temp_token = $temp_proxy->GetToken($username, $password);
					if ($temp_token=='ERROR: username/password salah') {
						$this->session->set_flashdata('error',$temp_token);
						redirect(base_url());
					} else {
						$filter_sp = "npsn = '".$this->npsn."'";
						$temp_sp = $temp_proxy->getrecord($temp_token,'satuan_pendidikan',$filter_sp);
						$id_sp = $temp_sp['result']?$temp_sp['result']['id_sp']:'';
						$nm_lemb = $temp_sp['result']['nm_lemb'];
						$header = headers($this->temp_ws->date,$this->temp_ws->md5,$this->npsn,$username,$temp_sp['result']['nm_lemb']);
						//var_dump($header);
						$sessi = array('login' => TRUE,
										  'ws' => $ws,
									   'token' => $temp_token,
									'username' => $username,
									'password' => $password,
									     'url' => base_url(),
									 'kode_pt' => $this->npsn,
									   'id_sp' => $id_sp,
									  'header' => $header,
									   'nm_lemb' => $nm_lemb
							);
						//var_dump($sessi);
						$this->session->sess_expiration = '900'; //session timeout 15 minute
						//$this->session->set_tempdata($sessi, NULL, 60); //session timeout 15 minute
						$this->session->set_userdata($sessi);
						redirect('welcome');
					}
				}
			}
		}
		$data['site_title'] = 'Please Login';
		$this->load->view('tpl/login_view',$data);
	}
}
