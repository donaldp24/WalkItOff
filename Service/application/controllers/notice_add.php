<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notice_add extends MY_Controller {

	var $mainmenu = "推送管理";
	var $submenu = "添加推送信息";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('noticelog_model'));
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

	public function index( $ID = 0 )
	{
		/* initialization */
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;
        $data['ID'] = $ID;

		/* view data */
		if($this->input->post())
		{
            $title = $this->input->post("title", true);
			$contents = $this->input->post("contents", true);
			$receiver = $this->input->post("receiver", true);
            $starttime = '';//$this->input->post("starttime", true);
            $endtime = '';//$this->input->post("endtime", true);

            log_message("info", "title = " . $title);
            log_message("info", "contents = " . $contents);
            log_message("info", "receiver = " . $receiver);
            //log_message("info", "starttime = " . $starttime);
            //log_message("info", "endtime = " . $endtime);

			if( $title != "" && $contents != "" && $receiver != "" /*&& $starttime != "" && $endtime != ""*/) {
				$this->noticelog_model->addNotice($title, $contents, $receiver, $starttime, $endtime);
				redirect('notice');
			}else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}

		$data['ID'] = $ID;
		if( $ID == 0 ) {
            $data['submenu'] = $submenu = "添加推送信息";
		}else {
            /*
			$data['submenu'] = $submenu = "编辑推送信息";
			$info = $this->noticelog_model->getNotice( $ID );
            if ($info != null)
            {
                $data["info"] = $info[0];
                $data['title'] = $info[0]['title'];
                $data['contents'] = $info[0]['contents'];
                $data['receiver'] = $info[0]['receiver'];
                $data['starttime'] = $info[0]['starttime'];
                $data['endtime'] = $info[0]['endtime'];
            }
            */
		}

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('notice/add', $data, true);
		$data["inlinejs"] = $this->load->view('notice/add_js', $data, true);
        $data["inlinecss"] = $this->load->view('notice/add_css', $data, true);

		$data["js_plugins"] = $this->load->view('notice/add_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}
}