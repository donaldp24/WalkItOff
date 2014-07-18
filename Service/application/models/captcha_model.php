<?php

class Captcha_model extends CI_Model {

    var $cutLength = 40;
	var $tblname = "tbl_captcha";

	var $img_path = 'www/images/captcha/';
	var $font_path = 'www/font/fontawesome-webfontf77b.ttf/';
	
	var $width = 140;
	var $height = 30 ;
	var $expiration = 7200;

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->library('common');
		$this->load->helper('captcha');
    }
    
	public function generate_Captcha($word)
	{
		$captVals = array(
			'word' => $word,
			'word_length' => 6,
			'img_path' => $this->img_path,
			'img_url' => base_url() . $this->img_path,
			'font_path' => base_url() . $this->font_path,
			'img_width' => $this->width,
			'img_height' => $this->height,
			'expiration' => $this->expiration
			);
		$cap = create_captcha( $captVals );
		if ($cap) {
			$data = array(
				"captchatime" => $cap["time"],
				"ipaddress" => $this->input->ip_address(),
				"word" => $cap["word"] ,
			);
			$query = $this->db->insert_string($this->tblname, $data);
			$this->db->query( $query );
		} else {
			return false;
		}
		return $cap['image'];
	}

	public function check_captcha($captcha)
	{
		// Delete old data ( 2hours)
		$expiration = time()-$this->expiration;
		$sql = "DELETE FROM " . $this->tblname . " WHERE captchatime < ?";
		$binds = array($expiration);
		$query = $this->db->query($sql, $binds);

		//checking input
		$sql = "SELECT COUNT(*) AS count FROM " . $this->tblname . " WHERE word = ? AND ipaddress = ? AND captchatime > ?";
		$binds = array($captcha, $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);

		if ($query->num_rows())
		{
			$row = $query->row();
			if ($row->count > 0 ) {
				return true;
			}
		}

		return false;
	}

}

?>