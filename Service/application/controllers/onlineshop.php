<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OnlineShop extends MY_Controller {

	var $mainmenu = "信息管理";
	var $submenu = "在线店铺";

	public function __construct()
	{
		parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->database();
		$this->load->library(array('baselib', "common", "session", "output"));
		$this->load->model(array('seo_model', 'shop_model'));
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

        if (isset($_REQUEST['del_ids']))
        {
            $ret = $this->shop_model->delete_id($_REQUEST['del_ids']);
            if ($ret > 0)
                $data['error_msg'] = SUCCESS_MSG_DELETE_USERS;
            else
                $data['error_msg'] = ERROR_MSG_DELETE_USERS;
        }

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('onlineshop/onlineshop', $data, true);
		$data["inlinejs"] = $this->load->view('onlineshop/onlineshop_js', $data, true);
		$data["js_plugins"] = $this->load->view('onlineshop/onlineshop_js_plugins', $data, true);



		$this->load->view('mainlayout', $data);
	}

    public function retrieveshoplist()
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        $output = $this->shop_model->getList();

        $this->output->set_output($this->common->get_json_data( $output ));
    }
}