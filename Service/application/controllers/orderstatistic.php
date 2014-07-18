<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orderstatistic extends MY_Controller {

	var $mainmenu = "统计报表";
	var $submenu = "订单统计";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('orderstatistic_model', 'order_model'));
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

	public function index()
	{
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('statistic/order', $data, true);
		$data["inlinecss"] = $this->load->view('statistic/order_css', $data, true);
		$data["inlinejs"] = $this->load->view('statistic/order_js', $data, true);
		$data["js_plugins"] = $this->load->view('statistic/order_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function retrievestatisticlist()
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        $output = $this->orderstatistic_model->getList();

        $this->output->set_output($this->common->get_json_data( $output ));
    }
}
