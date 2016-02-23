<?php

class Sqldebug extends CI_Controller
{

    public function __construct() {
		parent::__construct();
	}

    public function showDebug() {
        $ci = &get_instance();
		if ($ci->config->item('debug_db')) {
			foreach ($ci->load->list_sql as $sql) {
				echo '<div style="border-bottom:1px solid black;padding:3px">' . $sql . '</div>';
			}
		}
    }
    
}