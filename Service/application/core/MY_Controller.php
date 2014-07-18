<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library(array('baselib', "common", "session"));

		if (!$this->is_logged_in()) {
			redirect(base_url()."account");
			return;
		}
	}

	public function is_logged_in() 
	{
        if($this->session->userdata('logged_in') == true)
        {
            return true;
        }
        return false;

	}

}
?>