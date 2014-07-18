<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notice extends MY_Controller {

	var $mainmenu = "推送管理";
	var $submenu = "推送信息列表";

	public function __construct()
	{
		parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->database();
		$this->load->library(array('baselib', "common", "session", "output"));
		$this->load->model(array('seo_model', 'noticelog_model'));
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
            $ret = $this->noticelog_model->delete_id($_REQUEST['del_ids']);
            if ($ret > 0)
                $data['error_msg'] = SUCCESS_MSG_DELETE_USERS;
            else
                $data['error_msg'] = ERROR_MSG_DELETE_USERS;
        }

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('notice/notice', $data, true);
		$data["inlinejs"] = $this->load->view('notice/notice_js', $data, true);
		$data["js_plugins"] = $this->load->view('notice/notice_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function retrievenoticelist()
    {
        $output = $this->noticelog_model->getList();

        $this->output->set_output($this->common->get_json_data( $output ));
    }
}