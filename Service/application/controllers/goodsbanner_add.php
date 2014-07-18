<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goodsbanner_add extends MY_Controller {

	var $mainmenu = "产品管理";
	var $submenu = "添加首页产品";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goodsbanner_model'));
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

		/* view data */
		if($this->input->post())
		{	
			$goodsid = $this->input->post("goodsid",true);
			$imguri = $this->input->post("img_name1",true);
			$showorder = $this->input->post("showorder",true);
			if( $goodsid != "" && $imguri != "" && $showorder != "") {
				$this->goodsbanner_model->add_goodsbanner($ID,$goodsid,$imguri,$showorder);
				redirect('goodsbanner');
			}else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}
		
		$data['ID'] = $ID;
		if( $ID == 0 ) {
			$data['goodslist'] = $this->goodsbanner_model->getGoods();
		}else {
			$data['submenu'] = $submenu = "编辑首页产品";

			$info = $this->goodsbanner_model->getGoodsBannerInfo( $ID );
			$data["info"] = $info;
			$data['goodslist'] = $this->goodsbanner_model->getGoods();
		}

        $data['max_showorder'] = $this->goodsbanner_model->get_max_showorder();

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('goods/addbanner', $data, true);
		$data["inlinecss"] = $this->load->view('goods/addbanner_css', $data, true);
		$data["inlinejs"] = $this->load->view('goods/addbanner_js', $data, true);
		$data["js_plugins"] = $this->load->view('goods/addbanner_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	public function remove_photo1()
	{
		$f_name = $_POST['file_name'];
		$full_path = "www/images/uploads/products/image/".$f_name;
        unlink($full_path);
	}
}