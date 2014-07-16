<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invitecode extends MY_Controller {

	var $mainmenu = "推送管理";
	var $submenu = "邀请码";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('invitecode_model'));
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

        if ($this->input->post())
        {
            $ret = $this->invitecode_model->insert_data();
            if ($ret > 0)
                $data['error_msg'] = SUCCESS_MSG_INVITE_CODE;
            else
                $data['error_msg'] = ERROR_MSG_INVITE_CODE;
        }

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('invitecode/codelist', $data, true);
		$data["inlinejs"] = $this->load->view('invitecode/codelist_js', $data, true);
		$data["js_plugins"] = $this->load->view('invitecode/codelist_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function retrievecodelist()
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        $output = $this->invitecode_model->getList();

        $this->output->set_output($this->common->get_json_data( $output ));
    }

    public function export($id)
    {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
        $output = $this->invitecode_model->getcodelist($id);

		$filename =$id.".xls";
		//$contents = "时间 \t 邀请码 \t \n";
//		$contents = '<html><head><meta http-equiv="Content-Type" content="application/vnd.ms-excel;charset=euc-cn"></head>';
//		$contents .= "邀请码 \t \n";
		$contents = $output;
//		$contents .= "</html>"
		header('Content-type: application/vnd.ms-excel; charset=gb2312');
		header('Content-Disposition: attachment; filename='.$filename);
//		header( "Content-Description: PHP4 Generated Data" ); 
		echo $contents;
    }
}