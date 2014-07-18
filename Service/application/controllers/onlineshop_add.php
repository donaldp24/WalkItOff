<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OnlineShop_add extends MY_Controller {

	var $mainmenu = "信息管理";
	var $submenu = "在线店铺";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('shop_model'));
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
            $name = $this->input->post("name", true);
			$imguri = $this->input->post("imguri", true);
			$linkurl = $this->input->post("linkurl", true);
            $showorder = $this->input->post("showorder", true);

            log_message("info", "name = " . $name);
            log_message("info", "imguri = " . $imguri);
            log_message("info", "linkurl = " . $linkurl);
            log_message("info", "showorder = " . $showorder);

			if( $name != "" && $imguri != "" && $linkurl != "") {
				$this->shop_model->addShop($ID, $name, $linkurl, $imguri, $showorder);
				redirect('onlineshop');
			}else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}

		$data['ID'] = $ID;
		if( $ID == 0 ) {
            $data['submenu'] = $submenu = "添加在线店铺";
		}else {
			$data['submenu'] = $submenu = "编辑在线店铺";

			$info = $this->shop_model->getShop( $ID );
            if ($info != null)
            {
                $data["info"] = $info[0];
                $data['name'] = $info[0]['name'];
                $data['linkurl'] = $info[0]['linkurl'];
                $data['imguri'] = $info[0]['imguri'];
                $data['showorder'] = $info[0]['showorder'];
            }
		}

        $data['max_showorder'] = $this->shop_model->get_max_showorder();

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('onlineshop/add', $data, true);
		$data["inlinejs"] = $this->load->view('onlineshop/add_js', $data, true);
		$data["js_plugins"] = $this->load->view('onlineshop/add_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

    public function remove_photo()
    {
        $f_name = $_POST['file_name'];
        $full_path = "www/images/uploads/products/image/".$f_name;
        unlink($full_path);
    }
}