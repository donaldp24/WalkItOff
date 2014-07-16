<?php

class Account_model extends CI_Model
{

	var $tblname = "tbl_user";
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->library('common');
		$this->load->library('session');
    }

    public function create($data)
    {
        if($this->db->insert($tblname, $data))
        {
            return true;
        } else
        {
            return false;
        }
    }

    public function login($uname, $pass)
    {
		$sql = "SELECT * FROM " . $this->tblname .
			" WHERE userid = '" . $uname . "' AND password = '" . $pass . "'";

		$query = $this->db->query($sql);
        log_message('info', '----------- login sql = ' . $sql . " : " . $query->num_rows());
		if ($query != null && $query->num_rows() > 0) {
			$userinfo = $query->row();
		} else {
            log_message('info', '----------- return false');
			return false;
		}

        $data = array('username' => $uname, 
			'logged_in' => true,
			'userid' => $userinfo->userid,
			'realname' => $this->common->convertUTF8($userinfo->username),
//			'lastlogtime' => $userinfo->lastLogDate,
//			'lastip' => $userinfo->lastIP,
//			'posIdx' => $userinfo->posIdx,
			'uid' => $userinfo->uid
			);
        $this->session->set_userdata($data);

		return true;
    }

    public function logged_in()
    {
        if($this->session->userdata('logged_in') == true)
        {
            return true;
        }
        return false;
    }

	public function check_username($uname)
	{
		$sql = "SELECT * FROM " . $this->tblname . 
			" WHERE userid = '" . $uname . "' and deleted = 0";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return true;
		}

		return false;
	}

	public function checkUser($uname, $pass)
	{
		$sql = "SELECT * FROM " . $this->tblname .
			" WHERE userid = '" . $uname . "' AND password = '" . $pass . "'";

        log_message('info', '-------------check user sql = ' . $sql);
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			return true;
		}

		return false;
	}

	public function check_old_pass($oldpass)
	{
		$this->db->where("uid", $this->session->userdata['uid']);

		$query = $this->db->get($this->tblname);

		if ($query && $query->num_rows()) {
			if ($oldpass == $query->row()->password)
				return true;
		}

		return false;
	}

	public function set_user_pass($newpass)
	{
		$this->db->where("uid", $this->session->userdata['uid']);
		$data = array("password" => $newpass);
		$this->db->update($this->tblname, $data);

		return $this->db->affected_rows();
	}

}

?>