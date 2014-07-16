<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goodsstatistic extends MY_Controller {

	var $mainmenu = "统计报表";
	var $submenu = "产品统计";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goodsstatistic_model'));
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
		$data["contents"] = $this->load->view('statistic/goods', $data, true);
		$data["inlinecss"] = $this->load->view('statistic/goods_css', $data, true);
		$data["inlinejs"] = $this->load->view('statistic/goods_js', $data, true);
		$data["js_plugins"] = $this->load->view('statistic/goods_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function retrievestatisticlist()
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        $output = $this->goodsstatistic_model->getList();

        $this->output->set_output($this->common->get_json_data( $output ));
    }

    public function getchartdata()
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        //$output = $this->goodsstatistic_model->get_chart_data();
		$output = "";

        $this->output->set_output($this->common->get_json_data( $output ));
    }

    public function export()
    {

		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

        $output = $this->goodsstatistic_model->getexcellist();

		$filename ="sp_tongji.csv";
		$contents = $output;

		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);

		echo $contents;
    }

}