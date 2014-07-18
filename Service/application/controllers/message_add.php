<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Message_add extends MY_Controller {

	var $mainmenu = "信息管理";
	var $submenu = "添加文章";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('message_model'));
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
		if (isset($_POST["contents"])) {
			$_POST["contents"] = $this->common->ms_escape_string(htmlspecialchars($this->input->post("contents")));
            //$_POST["contents"] = $this->common->ms_escape_string($this->input->post("contents"));
		}
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);

		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;
        $data['ID'] = $ID;

		/* view data */
		if($this->input->post())
		{
			$rst = $this->message_model->addMessage($ID);
			if ($rst > 0) {
				redirect('message');
			} else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}


		$data['ID'] = $ID;
		if( $ID == 0 ) {
            $data['submenu'] = $submenu = "添加文章";
		}else {
			$data['submenu'] = $submenu = "编辑文章";

			$info = $this->message_model->getMessage( $ID );
			if ($info != null)
			{
				$data['title'] = $info["title"];
				$data['contents'] = htmlspecialchars_decode($info["contents"]);
				$data['allowread'] = $info["allowread"];
			}
		}

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('message/add', $data, true);
		$data["inlinejs"] = $this->load->view('message/add_js', $data, true);
		$data["js_plugins"] = $this->load->view('message/add_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function remove_photo()
    {
        $f_name = $_POST['file_name'];
        $full_path = "www/images/uploads/products/image/".$f_name;
        unlink($full_path);
    }
}