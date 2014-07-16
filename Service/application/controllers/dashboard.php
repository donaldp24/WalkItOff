<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	var $mainmenu = "仪表板";
	var $submenu = "";

	public function __construct()
	{
		parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->database();
		$this->load->library(array('baselib', "common", "session"));
		$this->load->model(array('seo_model'));
	}

	public function index()
	{
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('dashboard', $data, true);
		$data["inlinejs"] = $this->load->view('dashboard_js', $data, true);
		$data["js_plugins"] = $this->load->view('dashboard_js_plugins', $data, true);
		$this->load->view('mainlayout', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */