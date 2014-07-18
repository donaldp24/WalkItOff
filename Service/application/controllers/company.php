<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends MY_Controller {

	var $mainmenu = "信息管理";
	var $submenu = "公司简介";

	public function __construct()
	{
		parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->database();
		$this->load->library(array('baselib', "common", "session", "output"));
		$this->load->model(array('seo_model', 'company_model'));
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

	public function index($ID=0)
	{
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

        $data['ID'] = $ID;

        if (isset($_POST['imguri']))
        {
            $arrayOfuri = explode(",", $_POST['imguri']);
            for ($i = 0; $i < 5; $i++)
            {
                $ret = $this->company_model->updatePhoto($arrayOfuri[$i], $i + 1);
            }
        }

        $ret = $this->company_model->getPhotos();
        $uris = "";
        foreach ($ret as $imguri)
        {
            $uris .= $imguri . ",";
        }
        for ($i = count($ret); $i < 5; $i++)
        {
            $uris .= ",";
        }
        $data["imguri"] = $uris;

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('company/company', $data, true);
		$data["inlinejs"] = $this->load->view('company/company_js', $data, true);
		$data["js_plugins"] = $this->load->view('company/company_js_plugins', $data, true);


		$this->load->view('mainlayout', $data);
	}

    public function removephoto()
    {
        $f_name = $_POST['file_name'];
        $full_path = "www/images/uploads/company/image/" . $f_name;
        unlink($full_path);
    }

}