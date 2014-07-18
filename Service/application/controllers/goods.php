<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goods extends MY_Controller {

	var $mainmenu = "产品管理";
	var $submenu = "产品列表";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goods_model'));
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
				$data['notice'] = "您选择的商品都被删除。";
			}
			$data['succ'] = $succ;
		}

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('goods/goods', $data, true);
		$data["inlinejs"] = $this->load->view('goods/goods_js', $data, true);
		$data["js_plugins"] = $this->load->view('goods/goods_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	public function retrievegoodslist()
	{
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		$output = $this->goods_model->getgoodslist();

		$this->output->set_output($this->common->get_json_data( $output ));
	}

    public function deleteitems()
    {
        if (isset($_REQUEST['del_ids']))
        {
            $this->goods_model->delete_id($this->input->post('del_ids'));
        }
		
		redirect(base_url() . "goods?succ=success&action=del", "location");
    }

}