<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_add extends MY_Controller {

	var $mainmenu = "会员管理";
	var $submenu = "编辑会员";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('member_model', 'order_model'));
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
			$rst = $this->member_model->savemember();
			if ($rst == "") {
				redirect('member');
			} else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}

		$data["provinces"] = $this->member_model->getprovinces();
		$data["levels"] = $this->member_model->getmemberlevels();
		$data['ID'] = $ID;
		if( $ID == 0 ) {
            //$data['submenu'] = $submenu = "添加";
		}else {
			//$data['submenu'] = $submenu = "编辑";

			$info = $this->member_model->getmemberinfo( $ID );
            if ($info != null)
            {
                $data["info"] = $info;
				$data["cities"] = $this->member_model->getcities($this->common->convertChn($info["addrprovince"]));
				$data["areas"] = $this->member_model->getareas($this->common->convertChn($info["addrcity"]));
            }
		}

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('member/memberadd', $data, true);
		$data["inlinecss"] = $this->load->view('member/memberadd_css', $data, true);
		$data["inlinejs"] = $this->load->view('member/memberadd_js', $data, true);
		$data["js_plugins"] = $this->load->view('member/memberadd_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	public function getcities() {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$output = $this->member_model->getcities( $_POST['province'] );

		$this->output->set_output($this->common->get_json_data( $output ));
	}

	public function getareas() {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$output = $this->member_model->getareas( $_POST['city'] );

		$this->output->set_output($this->common->get_json_data( $output ));
	}

}