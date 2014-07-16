<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends MY_Controller {

	var $mainmenu = "订单管理";
	var $submenu = "";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model(array('order_model'));
	}

	public function index($start = 0)
	{
		/* initialization */
        $_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('order/order', $data, true);
		$data["inlinecss"] = $this->load->view('order/order_css', $data, true);
		$data["inlinejs"] = $this->load->view('order/order_js', $data, true);
		$data["js_plugins"] = $this->load->view('order/order_js_plugins', $data, true);
		$this->load->view('mainlayout', $data);
	}

	public function retrieveorderlist()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->order_model->getorderlist();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function sendsms()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->order_model->sendsms();
		$this->output->set_output($this->common->get_json_data( $output ));
	}
}