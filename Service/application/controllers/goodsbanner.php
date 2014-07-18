<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goodsbanner extends MY_Controller {

	var $mainmenu = "产品管理";
	var $submenu = "首页产品列表";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goodsbanner_model'));
	}

	public function index()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		if (isset($_GET["succ"]) && isset($_GET["action"])) {
			$succ = $this->input->get("succ");
			$data['notice'] = "";
			$action = $this->input->get("action");
			if ($action == "del") {
				$data['notice'] = "您选择的项目都被删除。";
			}
			$data['succ'] = $succ;
		}

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('goods/goodsbanner', $data, true);
		$data["inlinecss"] = $this->load->view('goods/goodsbanner_css', $data, true);
		$data["inlinejs"] = $this->load->view('goods/goodsbanner_js', $data, true);
		$data["js_plugins"] = $this->load->view('goods/goodsbanner_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	public function retrievegoodsbannerlist()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->goodsbanner_model->getgoodsbannerlist();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

    public function deleteitems()
    {
        if (isset($_REQUEST['del_ids']))
        {
            $this->goodsbanner_model->delete_id($this->input->post('del_ids'));
        }
		
		redirect(base_url() . "goodsbanner?succ=success&action=del", "location");
    }

}