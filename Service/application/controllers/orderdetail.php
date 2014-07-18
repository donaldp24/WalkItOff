<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orderdetail extends MY_Controller {

	var $mainmenu = "订单管理";
	var $submenu = "订单详情";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model(array('order_model', 'orderdetail_model', 'member_model'));
	}

	public function _remap($method)
	{
		$param_offset = 2;

		// Default to index
		if ( ! method_exists($this, $method))
		{
		// We need one more param
		$param_offset = 1;
		$method = 'index';
		}

		// Since all we get is $method, load up everything else in the URI
		$params = array_slice($this->uri->rsegment_array(), $param_offset);

		// Call the determined method with all params
		call_user_func_array(array($this, $method), $params);
	}

	public function index($id = 0)
	{
		/* initialization */
        $_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;
		$data['specificmenu'] = base_url() . "order";


		/* model data */
		$info = $this->orderdetail_model->getorderinfo($id);

		if ($info == null) return;
		$data["info"] = $info;
		$data["memberpos"] = $this->orderdetail_model->getmemberpos($info["posid"]);
		$data["ordergoods"] = $this->orderdetail_model->getordergoods($id);
		$data["status"] = $this->order_model->get_status_message($info["status"]);
		$data["memberlevel"] = $this->order_model->get_memberlevel_value($info["memberlevel"]);
		$data["provinces"] = $this->member_model->getprovinces();
		$data["cities"] = $this->member_model->getcities($this->common->convertChn($info["addrprovince"]));
		$data["areas"] = $this->member_model->getareas($this->common->convertChn($info["addrcity"]));


		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('order/orderdetail', $data, true);
		$data["inlinecss"] = $this->load->view('order/orderdetail_css', $data, true);
		$data["inlinejs"] = $this->load->view('order/orderdetail_js', $data, true);
		$data["js_plugins"] = $this->load->view('order/orderdetail_js_plugins', $data, true);
		$this->load->view('mainlayout', $data);
	}

	public function updatesendingno()
	{
		if (!$this->input->is_ajax_request()) return false;

		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->orderdetail_model->update_sendingno();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function completeorder()
	{
		if (!$this->input->is_ajax_request()) return false;

		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->orderdetail_model->completeorder();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function cancelorder()
	{
		if (!$this->input->is_ajax_request()) return false;

		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->orderdetail_model->cancelorder();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function changereceiver()
	{
		if (!$this->input->is_ajax_request()) return false;

		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->orderdetail_model->changereceiver();

		$this->output->set_output($this->common->get_json_data( $output ));
	}
}