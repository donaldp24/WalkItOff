<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goodslevel extends MY_Controller {

	var $mainmenu = "产品管理";
	var $submenu = "产品分类";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goodslevel_model'));
	}

	public function index()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('goods/goodslevel', $data, true);
		$data["inlinecss"] = $this->load->view('goods/goodslevel_css', $data, true);
		$data["inlinejs"] = $this->load->view('goods/goodslevel_js', $data, true);
		$data["js_plugins"] = $this->load->view('goods/goodslevel_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	/* processing level1 data */
	public function retrievegoodslevellist()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$output = $this->goodslevel_model->getgoodslevellist();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function editlevel()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$this->goodslevel_model->manipulatelevel();
	}

	/* processing level2 data */
	public function retrievegoodslevel2list()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$output = $this->goodslevel_model->getgoodslevel2list();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function editlevel2()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$this->goodslevel_model->manipulatelevel2();
	}

}