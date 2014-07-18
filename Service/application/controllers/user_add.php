<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_add extends MY_Controller {

	var $mainmenu = "系统设置";
	var $submenu = "用户管理";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('user_model'));
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
			$userid = $this->input->post("userid",true);
			$username = $this->input->post("username",true);
			$phonenum = $this->input->post("phonenum",true);
			$mailaddr = $this->input->post("mailaddr",true);
			$job = $this->input->post("job",true);
			$password = $this->input->post("password",true);
            $postaddr = $this->input->post("postaddr",true);

            log_message("info", "userid = " . $userid);
            log_message("info", "username = " . $username);
            log_message("info", "phonenum = " . $phonenum);
            log_message("info", "mailaddr = " . $mailaddr);
            log_message("info", "job = " . $job);
            log_message("info", "password = " . $password);
            log_message("info", "postaddr = " . $postaddr);


			if( $userid != "" && $username != "" && $phonenum != "" && $job != "" && $password != "") {
				$this->user_model->add_user($userid, $username, $phonenum, $mailaddr, $job, $password, $postaddr);
				redirect('user');
			}else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}
	/*
		$data['ID'] = $ID;
		if( $ID == 0 ) {
			//
		}else {
			$data['submenu'] = $submenu = "编辑产品";

			$info = $this->user_model->get_user( $ID );
            if ($info != 0)
            {
                $data["info"] = $info;
                $data['userid'] = $info['userid'];
                $data['username'] = $info['username'];
                $data['phonenum'] = $info['phonenum'];
                $data['mailaddr'] = $info['mailaddr'];
                $data['postaddr'] = $info['postaddr'];
                $data['job'] = $info['job'];
            }
		}
    */
		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('user/add', $data, true);
		$data["inlinejs"] = $this->load->view('user/add_js', $data, true);
		$data["js_plugins"] = $this->load->view('user/add_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}
}