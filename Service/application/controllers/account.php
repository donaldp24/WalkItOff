<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	var $mainmenu = "系统设置";
	var $submenu = "修改密码";
	var $_username = "";
	var $_password = "";
	var $_oldpass = "";
	var $_newpass = "";
	public function __construct()
	{
		parent::__construct();
        $this->load->helper(array('form', 'url'));
		$this->load->database();
		$this->load->library(array("form_validation"));
		$this->load->model(array('captcha_model', 'account_model'));
	}

	function index()
	{
		$data['rootUri'] = base_url();
        $_GET = $this->common->convertChn($_GET);
        $_POST = $this->common->convertChn($_POST);

        if ($this->account_model->logged_in() == true) {
			$this->mainpage(true);
		} else {

			$data['captchaImg'] = $this->loadCaptcha();
			$data['base_url'] = site_url('account');

			$this->form_validation->set_rules('username', '用户名', 'xss_clean|required'/*|callback_username_check*/);
			$this->form_validation->set_rules('password', '密码', 'required');//'xss_clean|required|min_length[6]|max_length[30]'/*|sha1|callbackup_password_check'*/);
			$this->form_validation->set_rules('seccode', '验证码', 'required');

			$this->_username = $this->input->post('username');
			$this->_password = sha1(AUTHSALT . $this->input->post('password'));
            //print_r($_POST);

			$this->form_validation->set_error_delimiters('<p style="color:#FF0000;font-size:12px;font-weight:normal;">', '</p>');
			$this->form_validation->set_message('required', '请输入%s。');
			$this->form_validation->set_message('min_length', '密码至少为6位。');
			$this->form_validation->set_message('max_length', '密码最大为30位。');
			if ($this->form_validation->run() == false)
			{
                log_message('info', '-----------------validation failed' . $this->form_validation->error_string());
				$this->load->view('login', $data);
			} else
            {
				if ($this->checkAuth() == true) {
					if ($this->account_model->login($this->_username, $this->_password) == false) {
                        log_message('info', '------------login failed');
						$data["errormsg"] = "此帐号不存在";
						redirect($data["base_url"], "location");
					}
                    log_message('info', '------------login success');
					redirect(base_url()."order", "location");
				} else {
                    log_message('info', '-------------checkAuth failed');
					$data["errormsg"] = "此帐号不存在";
					$this->load->view('login', $data);
				}
			}
		}
	}

    function mainpage($condition = false)
    {
        if ($condition == true or $this->account_model->logged_in() == TRUE)
        {
			redirect(base_url()."order", "location");
        }
        else
        {
			redirect(base_url()."order", "location");
        }
    }

	function loadCaptcha()
	{
		$rst = false;
		$verification_code = $this->baselib->get_validate_code(5);
		$rst = $this->captcha_model->generate_Captcha($verification_code);

		return $rst;
	}

	function reloadCaptcha()
	{
		$verification_code = $this->baselib->get_validate_code(5);
		$cap = $this->captcha_model->generate_Captcha($verification_code);

		echo $cap;
	}

	function checkAuth()
	{
		$isValid = $this->captcha_model->check_captcha($this->input->post('seccode'));
		if (!$isValid) {
            log_message('info', 'captcha check failed ');
			return false;
		} else {
			$isValidUser = $this->account_model->checkUser($this->_username, $this->_password);
			if ($isValidUser == true) {
				return true;
			}
            log_message('info', 'check user failed ');
		}
		return false;
	}

	public function username_check($str)
	{
		$isexist = $this->account_model->check_username($str);
		if ($isexist == true)
		{
			$this->form_validation->set_message('username_check', ' %s已存在');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

    function password_check()
    {
        $this->db->where('loginId', $this->_username);
        $query = $this->db->get('users');
        $result = $query->row_array();
        if ($result['password'] == $this->_password)
        {
            return true;
        }
        if ($query->num_rows() == 0)
        {
            $this->form_validation->set_message('password_check', 'There was an error!');
            return false;
        }
    }

    function logout()
    {
        $this->session->sess_destroy();
        //$this->load->view(base_url().'admin/account');
		redirect(base_url()."account", "location");
    }

	public function changepass()
	{
		$data['rootUri'] = base_url();
		$data['mainmenu'] = $this->mainmenu;
		$data['submenu'] = $this->submenu;

		if ($this->input->post()) {
			$this->_oldpass = sha1(AUTHSALT . $this->input->post('oldpassword'));
			$this->_newpass = sha1(AUTHSALT . $this->input->post('password'));

			$this->form_validation->set_rules('oldpassword', '旧代码', 'callback_check_oldpassword');

			$this->form_validation->set_error_delimiters('<p style="color:#FF0000;font-size:12px;font-weight:normal;">', '</p>');
			$this->form_validation->set_message('check_oldpassword', '旧密码不正确。');
			
			if ($this->form_validation->run() == true)
			{
				$this->account_model->set_user_pass($this->_newpass);
				$data['success'] = "操作成功, 您设置的密码修改好了";
			}
		}

		$data["leftmenu"] = $this->seo_model->buildmenu($this->mainmenu, $this->submenu);
		$data["contents"] = $this->load->view('user/password', $data, true);
		$data["inlinejs"] = $this->load->view('user/password_js', $data, true);
		$data["js_plugins"] = $this->load->view('user/password_js_plugins', $data, true);
		$this->load->view('mainlayout', $data);
	}

	public function check_oldpassword()
	{
		$valid = $this->account_model->check_old_pass($this->_oldpass);

		if ($valid) return true;

		return false;
	}

}