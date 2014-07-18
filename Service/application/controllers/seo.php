<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seo extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('seo_model'));
	}

    function sitemap()
    {
        $data["urls"] = $this->seo_model->siteurls;//select urls from DB to Array
        $this->load->view("sitemap", $data);
    }
}