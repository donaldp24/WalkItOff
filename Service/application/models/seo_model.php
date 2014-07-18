<?php

class Seo_model extends CI_Model
{
	public $siteurls = array(
//		"仪表板|icon-dashboard" => "dashboard",
		"订单管理|icon-book" => "order",
		"产品管理|icon-gift" => array(
			"添加产品|icon-plus" => "goods_add",
			"产品列表|icon-eye-open" => "goods",
			"产品分类|icon-double-angle-right" => "goodslevel",
			"添加首页产品|icon-plus" => "goodsbanner_add",
			"首页产品列表|icon-eye-open" => "goodsbanner"
		),
		"会员管理|icon-user" => "member",
		"信息管理|icon-edit" => array(
			"公司简介|icon-double-angle-right" => "company",
			"在线店铺|icon-double-angle-right" => "onlineshop",
			"站内文章列表|icon-double-angle-right" => "message"
		),
		"推送管理|icon-list-alt" => array(
			"添加推送信息|icon-double-angle-right" => "notice_add",
			"推送信息列表|icon-double-angle-right" => "notice",
			"短信列表|icon-double-angle-right" => "smslist",
			"邀请码|icon-double-angle-right" => "invitecode"
		),
		"统计报表|icon-bar-chart" =>
            array(
			"产品统计|icon-double-angle-right" => "goodsstatistic",
//			"区域统计|icon-double-angle-right" => "statistic/area",
			"订单统计|icon-double-angle-right" => "orderstatistic"
		)
        ,"系统设置|icon-gears" => array(
			"修改密码|icon-double-angle-right" => "account/changepass",
			"用户管理|icon-user" => "user"
		)
	);

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->library('common');
		$this->load->library('session');
    }

	public function buildmenu($mainactive, $subactive)
	{
        $data["urls"] = $this->siteurls;//select urls from DB to Array
		$data["mainactive"] = $mainactive;
		$data["subactive"] = $subactive;
        return $this->load->view("sitemap", $data, true);
	}
}

?>