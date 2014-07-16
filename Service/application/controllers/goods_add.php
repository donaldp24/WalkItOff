<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Goods_add extends MY_Controller {

	var $mainmenu = "产品管理";
	var $submenu = "添加产品";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(array("output"));
		$this->load->model(array('goods_model'));
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
		$data['specificmenu'] = base_url() . "goods";

		/* view data */
		if($this->input->post())
		{	
			$name = $this->input->post("name",true);
			$goodsno = $this->input->post("goodsno",true);
			$secondlevel = $this->input->post("secondlevel",true);
			$pattern = $this->input->post("pattern",true);
			$img1 = $this->input->post("img_name1",true);
			$img2 = $this->input->post("img_name2",true);
            $img3 = $this->input->post("img_name3",true);
			$order_price = $this->input->post("order_price",true);
			$price = $this->input->post("price",true);
			$kind = $this->input->post("kind",true);
			$showorder = $this->input->post("showorder",true);
			$sel_r_id = $this->input->post("sel_r_id",true);
			if( $name != "" && $goodsno != "" && $price != "" && $pattern != "" ) {
				$this->goods_model->add_goods($ID,$name,$goodsno,$secondlevel,$pattern,$showorder, $img1,$img2,$img3,$order_price,$price,$kind,$sel_r_id);
				redirect('goods');
			}else {
				$data['error_msg'] = ERROR_MSG_GOODS_ADD_GOODS;
			}
		}
		
		$data['ID'] = $ID;
		if( $ID == 0 ) {
			$data['first_level'] = $this->goods_model->getFirstLevel();
			$first_level_1st_id = 0;
			if( count($data['first_level']) )
				$first_level_1st_id = $data['first_level'][0]['uid'];
			$data['second_level_data'] = $this->goods_model->getSecondLevel( $first_level_1st_id );
		}else {
			$data['submenu'] = $submenu = "编辑产品";

			$info = $this->goods_model->getGoodsInfo( $ID );
			$data["info"] = $info;
			$data['name'] = $info['name'];
			$data['no'] = $info['goodsno'];
			$data['first_level'] = $this->goods_model->getFirstLevel();
			$data['second_level_data'] = $this->goods_model->getSecondLevel( $info['level1id'] );
			$data['SecondLevelID'] = $info['level2id'];
			$data['pattern'] = $info['style'];
			$data['img1'] = $info['imguri'];
			$data['img2'] = $info['img_exhib'];
            $data['img3'] = $info['img_detail'];
			$data['order_price'] = $info['reserveprice'];
			$data['price'] = $info['price'];
			$data['kind'] = $this->goods_model->getGoodsPropertyInfo( $ID );
			$data['relative_goods'] = $info['goods_rel'];
            $data['relative_goods_name'] = $this->goods_model->getRelativeGoodsName($data["relative_goods"]);
		}
        $data['max_showorder'] = $this->goods_model->get_max_showorder();

		/* load view */
		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('goods/add', $data, true);
		$data["inlinejs"] = $this->load->view('goods/add_js', $data, true);
		$data["js_plugins"] = $this->load->view('goods/add_js_plugins', $data, true);

		$this->load->view('mainlayout', $data);
	}

	public function getSecondLevel() {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
		//header("Content-Type: text/plain;charset=gb2312");
		//header("Content-Encoding: utf-8");
		$output = $this->goods_model->getSecondLevel( $_POST['id'] );

		echo $output;

		//$this->output->set_output(urldecode($this->common->get_json_data( $output )));
	}
	
	public function getRelativeGoodsSearchResult() {
		$_GET = $this->common->convertChn($_GET);
		$_POST = $this->common->convertChn($_POST);
     	$this->output->set_output($this->goods_model->getRelativeGoodsSearchResult( 
			$_POST['firstlevel'], 
			$_POST['secondlevel'], 
			$_POST['search_name']) 
		);
	}
	
	public function remove_photo1()
	{
		$f_name = $_POST['file_name'];
		$full_path = "www/images/uploads/products/image/".$f_name;
        unlink($full_path);
	}
	
	public function remove_photo2()
	{
		$f_name = $_POST['file_name'];
		$full_path = "www/images/uploads/products/picture/".$f_name;
        unlink($full_path);
	}
    public function remove_photo3()
    {
        $f_name = $_POST['file_name'];
        $full_path = "www/images/uploads/products/picture/".$f_name;
        unlink($full_path);
    }

}