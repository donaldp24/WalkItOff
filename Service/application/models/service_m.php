<?php


class Service_m extends CI_Model {
	function __construct() {
        parent::__construct();
    }

    /**
     * user login
     */
    function loginUserWithEmail($email, $userPwd)
    {
		// query with email
        $this->db->select("*");
        $this->db->where("email", $email);
        $this->db->where("type", USERTYPE_NORMAL);
        $query=$this->db->get("tbl_user");
		
		// query verification
		if ($query == null)
			return SVCERR_USERLOGIN_EMAILINCORRECT;
		if ($query->num_rows() == 0)
			return SVCERR_USERLOGIN_EMAILINCORRECT;
        if($query->num_rows() > 1)
            return SVCERR_USERLOGIN_SEVERALEMAIL;

		// check password
        $row = $query->row();
        if($row->pwd != $userPwd)
            return SVCERR_USERLOGIN_PWDINCORRECT;   // Password is incorrect.

        return SVCERR_SUCCESS;
    }

    function loginUserWithFacebook($email, $name, $gender, $age, $token)
    {
        // query with email
        $this->db->select("*");
        $this->db->where("email", $email);
        $this->db->where("type", USERTYPE_FACEBOOK);
        $query=$this->db->get("tbl_user");

        // query verification
        if ($query == null)
            return SVCERR_USERLOGIN_EMAILINCORRECT;
        if($query->num_rows() > 1)
            return SVCERR_USERLOGIN_SEVERALEMAIL;
        if($query->num_rows() == 0)
        {
            // insert new record
            $newUser = array();
            $newUser['name'] = $name;
            $newUser['age'] = $age;
            $newUser['gender'] = $gender;
            $newUser['weight'] = 75;
            $newUser['height'] = 175;
            $newUser['email'] = $email;
            $newUser['pwd'] = '';
            $newUser['type'] = USERTYPE_FACEBOOK;
            $newUser['token'] = $token;
            $newUser['enabled'] = 1;

            $sql = $this->db->insert_string('tbl_user', $newUser);
            $ret = $this->db->query($sql);

            if ($ret == false)
                return SVCERR_DBERROR;
        }
        else
        {
            //
        }
        return SVCERR_SUCCESS;
    }
	
	function getUserWithEmail($email, $type)
	{
		// query with email
		$this->db->select("*");
		$this->db->where("email", $email);
        $this->db->where("type", $type);
		$query = $this->db->get("tbl_user");

		// query verification
		if ($query == null)
			return null;
		if ($query->num_rows() == 0)
			return null;
		$row = $query->row();
		return $row;
	}

	/**
     *
     */
    function logoutUser()
    {
    }

    function isForcedLogout($request)
    {
    }

    /**
     * register user
     */
    function registerUser($name, $age, $gender, $weight, $height, $email, $pwd, $type, $token)
    {
        $sql = "SELECT uid FROM tbl_user WHERE email='" . $email . "'";
        log_message('info', __METHOD__ . ' - sql = ' . $sql);
        $query = $this->db->query($sql);
        if ($query == null)
            return SVCERR_DBERROR;

        if ($query->num_rows() > 0)
            return SVCERR_REGISTERUSER_DUPLICATE;// email duplicated

        if ($gender != 0 && $gender != 1)
            $gender = 0;

        $newUser = array();
        $newUser['name'] = $name;
        $newUser['age'] = $age;
        $newUser['gender'] = $gender;
        $newUser['weight'] = $weight;
		$newUser['height'] = $height;
		$newUser['email'] = $email;
		$newUser['pwd'] = $pwd;
		$newUser['type'] = $type;
		$newUser['token'] = $token;
		$newUser['enabled'] = 1;

        $sql = $this->db->insert_string('tbl_user', $newUser);
        $ret = $this->db->query($sql);

        if ($ret == true)
            return SVCERR_SUCCESS;   // Reg Success
        else
            return SVCERR_DBERROR;  // Reg failed
    }

    /**
     * update user information
     */
    function updateUser($uid, $name, $age, $gender, $weight, $height, $email, $pwd, $type, $token)
    {
        $sql = "SELECT uid FROM tbl_user WHERE uid=" . $uid;
        log_message('info', __METHOD__ . ' - sql = ' . $sql);
        $query = $this->db->query($sql);
        if ($query == null)
            return SVCERR_DBERROR;

        if ($query->num_rows() == 0)
            return SVCERR_UPDATEUSER_NOTEXIST;// user is not exist

        if ($gender != 0 && $gender != 1)
            $gender = 0;

        $newUser = array();
        $newUser['name'] = $name;
        $newUser['age'] = $age;
        $newUser['gender'] = $gender;
        $newUser['weight'] = $weight;
        $newUser['height'] = $height;
        $newUser['email'] = $email;
        $newUser['pwd'] = $pwd;
        $newUser['type'] = $type;
        $newUser['token'] = $token;
        $newUser['enabled'] = 1;

        $where = "uid = " . $uid;
        $sql = $this->db->update_string('tbl_user', $newUser, $where);
        $ret = $this->db->query($sql);

        if ($ret == true)
            return SVCERR_SUCCESS;   // update Success
        else
            return SVCERR_DBERROR;  // update failed
    }

	/**
	 *
	 * get user information with email address
	 */
	function getUser($uid)
	{
		$this->db->where("uid", $uid);
		$query = $this->db->get("tbl_user");
		if ($query == null || $query->num_rows() <= 0)
			return null;
		return $query->row();
	}

    public function generateRandomPassword()
    {
        $pwdconf = "abcdefghijklmnopqrstuvwxyz1234567890";
        list($usec, $sec) = explode(' ', microtime());
        $sec_val = (float) $sec + ((float) $usec * 100000);
        srand($sec_val);
        $pwd = "";
        for($i = 0; $i<6; $i++)
        {
            $randval = rand() % strlen($pwdconf);
            $pwd .= substr($pwdconf, $randval,1);
        }
        return $pwd;
    }

    public function setRandomPwd($email)
    {
        $newPwd = $this->generateRandomPassword();
        $sql = "UPDATE tbl_user SET pwd='" . $newPwd . "' WHERE email = '" . $email . "' AND type = " . USERTYPE_NORMAL;
        $ret = $this->db->query($sql);
        if ($ret == false)
            return null;
        return $newPwd;
    }

    /**
     * get all foods
     */
    function getAllFoods($uid, $keyword)
    {
        if ($keyword == "")
            $sql = "SELECT * FROM tbl_food WHERE deleted = 0 AND (userid == 0 OR useruid = " . $uid . ")";
        else
            $sql = "SELECT * FROM tbl_food WHERE deleted = 0 AND (userid == 0 OR useruid = " . $uid . ") AND (name like '%".$keyword."%')";
        $query = $this->db->query($sql);
        if ($query == null)
            return null;
        return $query->result();
    }

    function getFoodsWithPage($uid, $keyword, $page, &$hasNext)
    {
        $recordsPerPage = 25;
        $start = $recordsPerPage * $page;
        $limit = "LIMIT " . $start . ", " . $recordsPerPage;
        if ($keyword == "")
        {
            $where = "WHERE deleted = 0 AND (useruid = 0 OR useruid = " . $uid . ")";
        }
        else
        {
            $where = "WHERE deleted = 0 AND (useruid = 0 OR useruid = " . $uid . ") AND (name like '%" . $keyword . "%')";
        }
        $sql = "SELECT * FROM tbl_food " . $where . " " . $limit;
        $countSql = "SELECT count(*) as numberofrecords FROM tbl_food " . $where;

        // get record count
        $query = $this->db->query($countSql);
        if ($query == null || $query == false)
            return null;
        if ($query->num_rows() <= 0)
            return null;

        $row = $query->row();
        $numberOfRecords = $row->numberofrecords;
        if ($page * $recordsPerPage + $recordsPerPage >= $numberOfRecords)
            $hasNext = false;
        else
            $hasNext = true;

        $query = $this->db->query($sql);
        if ($query == null)
            return null;
        return $query->result();
    }

    function addFoodToCurrent($fooduid, $useruid)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("uid", $fooduid);
        $query = $this->db->get("tbl_food");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("fooduid", $fooduid);
        $this->db->where("useruid", $useruid);
        $this->db->where("deleted", 0);
        $query = $this->db->get("tbl_currentfood");
        if ($query == null || $query->num_rows() != 0)
            return false;

        $currentFood = array();
        $currentFood['fooduid'] = $fooduid;
        $currentFood['useruid'] = $useruid;
        $currentFood['createdTime'] = date("Y-m-d H:i:s");
        $currentFood['consumedTime'] = date("Y-m-d H:i:s");
        $currentFood['isconsumed'] = 0;
        $currentFood['deleted'] = 0;
        $sql = $this->db->insert_string("tbl_currentfood", $currentFood);
        $result = $this->db->query($sql);
        return $result;
    }

    function removeFoodFromCurrent($fooduid, $useruid)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("fooduid", $fooduid);
        $this->db->where("useruid", $useruid);
        $this->db->where("deleted", 0);
        $result = $this->db->update("tbl_currentfood", array("deleted" => 1));
        return $result;
    }

    function getCurrentFoods($useruid, $isconsumed)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return null;

        $this->db->select("a.uid, a.fooduid, a.useruid, a.createdtime, a.consumedtime, a.isconsumed, b.name, b.calories, a.deleted");
        $this->db->where("a.useruid", $useruid);
        $this->db->where("a.deleted", 0);
        if ($isconsumed)
            $this->db->where("a.isconsumed", 1);
        else
            $this->db->where("a.isconsumed", 0);
        $this->db->from("tbl_currentfood a");
        $this->db->join("tbl_food b", "a.fooduid = b.uid", "inner");
        $query = $this->db->get();
        if ($query == null || $query->result() == null)
            return null;

        return $query->result();
    }

    function addFoodToFavorites($fooduid, $useruid)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("uid", $fooduid);
        $query = $this->db->get("tbl_food");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("useruid", $useruid);
        $this->db->where("fooduid", $fooduid);
        $this->db->where("deleted", 0);
        $query = $this->db->get("tbl_favoritefood");
        if ($query == null || $query->num_rows() != 0)
            return false;

        $currentFood = array();
        $currentFood['fooduid'] = $fooduid;
        $currentFood['useruid'] = $useruid;
        $currentFood['createdTime'] = date("Y-m-d H:i:s");
        $currentFood['deleted'] = 0;
        $sql = $this->db->insert_string("tbl_favoritefood", $currentFood);
        $result = $this->db->query($sql);
        return $result;
    }

    function removeFoodFromFavorites($fooduid, $useruid)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return false;

        $this->db->where("fooduid", $fooduid);
        $this->db->where("useruid", $useruid);
        $this->db->where("deleted", 0);
        $result = $this->db->update("tbl_favoritefood", array("deleted" => 1));
        return $result;
    }

    function getFavoritesFoods($useruid)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return null;

        $this->db->select("b.name, b.calories, a.uid, a.fooduid, a.useruid, a.createdtime, a.deleted");
        $this->db->where("a.useruid", $useruid);
        $this->db->where("a.deleted", 0);
        $this->db->from("tbl_favoritefood a");
        $this->db->join("tbl_food b", "a.fooduid = b.uid", "inner");
        $query = $this->db->get();
        if ($query == null || $query->result() == null)
            return null;

        return $query->result();
    }

    function getConsumedWithDate($useruid, $date)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return null;

        $this->db->select("*");
        $this->db->where("useruid", $useruid);
        $this->db->where("consumeddate", $date);
        $this->db->where("deleted", 0);
        $query = $this->db->get("tbl_consumed");
        if ($query == null || $query->result() == null)
            return null;
        return $query->result();
    }

    function consumedFoods($useruid, $date, $fooduids)
    {
        $this->db->where("uid", $useruid);
        $query = $this->db->get("tbl_user");
        if ($query == null || $query->num_rows() != 1)
            return false;

        foreach ($fooduids as $fooduid)
        {
            $this->db->where("useruid", $useruid);
            $this->db->where("isconsumed", 0);
            $this->db->where("deleted", 0);
            $this->db->where("fooduid", $fooduid);
            $ret = $this->db->update("tbl_currentfood", array("isconsumed" => 1, "consumedtime" => $date));
            if ($ret == false)
            {
                return false;
            }
        }
        return true;
    }


    function getEmail($user_id)
    {
        $this->db->select("mailaddr");
        $this->db->where("userid",$user_id);
        $query=$this->db->get("tbl_member");
                
        if($query->num_rows() > 0)        
        {               
            $email_addr = "";
            foreach($query->result() as $row)
            { 
                $email_addr = $row->mailaddr;  
            }
            return $email_addr;
        }else
        {
            return 2; // No UserID
        }
    }

    /**
     * @param $userId
     * @param $pwd
     * @return int
     */
    function checkPwd($userId, $pwd)
    {   
        $this->db->select("memberid, password");
        $this->db->where("memberid", $userId);
        $query=$this->db->get("tbl_member");
        if($query->num_rows() > 0)
        {
            $local_pwd = "";
            foreach($query->result() as $row)
            {
                $local_pwd = $row->password;
				break;
            }
            if(/*md5($pwd)*/ $pwd == $local_pwd)
            {
                return true;
            }
            else
                return false;
        }else
        {
            return false;
        }
    }

    /**
     * @param $userId
     * @param $pwd
     */
    function setPwd($userId, $pwd)
    {   
        $data = array();
        $data['password']   = /*md5($pwd)*/ $pwd;
        $this->db->update('tbl_member', $data, array('memberid' => $userId));
    }

    /**
     * 새상품목록을 얻는 함수
     * @param $month
     */
    function getNewGoodList($month)
    {
        $cur_year = date("Y");
        $cur_month = date("m");
        $cur_day = date("d");

		if ($month < 0)
		{
			$cur_year -= 1;
			$month = -$month;
		}
		if (strlen($month) == 1)
			$month = "0" . $month;
		$from_date = $cur_year . "-" . $month . "-01";
		$to_date = date("Y-m-t", strtotime($from_date));
		$to_date .= " 23:59:59";

        $select = "SELECT uid, name, imguri, reserveprice, price FROM tbl_goods";
        $where = "WHERE style = 0 AND createtime between '" . $from_date . "' and '" . $to_date . "' AND deleted = 0";
        $order = "ORDER BY showorder";
        $sql = $select . " " . $where . " " . $order;

        $query = $this->db->query($sql);
        log_message("info", "getNewGoodList sql = " . $query->num_rows() . " : ". $sql);
        return $query->result();
    }

    function getTest($sql)
    {
        $query = $this->db->query($sql); 
        return $query->result();
    }

    /**
     * 사용자주소를 하나 추가하는 함수
     * @param $userId
     * @param $userName
     * @param $userPhone
     * @param $userArea1
     * @param $userArea2
     * @param $userArea3
     * @param $userStreet
     * @param $userPost
     * @return string
     */
    function addUserAddress($userId, $userName, $userPhone, $userArea1, $userArea2, $userArea3, $userStreet, $userPost)
    {
		$sql = "SELECT uid, receiveraddrid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            $defaultAddrId = $row->receiveraddrid;
            break;
        }

        $addr = array();
        $addr['memberid'] = $memberUid;
        $addr['receivername'] = $userName;
        $addr['phonenum'] = $userPhone;
        $addr['addrprovince'] = $userArea1;
        $addr['addrcity'] = $userArea2;
        $addr['addrarea'] = $userArea3;
        $addr['addrstreet'] = $userStreet;
        $addr['postaddr'] = $userPost;
        $sql = $this->db->insert_string("tbl_member_pos", $addr);
		log_message('info', 'addadress sql = ' . $sql);
		$this->db->query($sql);
        if ($this->db->affected_rows() > 0)
        {
            $newId = $this->db->insert_id();
            if (trim($defaultAddrId) == '' || $defaultAddrId == 'NULL' || intval($defaultAddrId) == 0)
            {
                $data = array(
                    'receiveraddrid' => $newId
                );
                $this->db->where('uid', $memberUid);
                $this->db->update('tbl_member', $data);
            }
        }

        return SVCERR_SUCCESS;
    }

	/**
     * 사용자주소를 갱신하는 함수
	 * @param $addrId
     * @param $userId
     * @param $userName
     * @param $userPhone
     * @param $userArea1
     * @param $userArea2
     * @param $userArea3
     * @param $userStreet
     * @param $userPost
     * @return string
     */
    function updateUserAddress($addrId, $userId, $userName, $userPhone, $userArea1, $userArea2, $userArea3, $userStreet, $userPost)
    {
		$sql = "SELECT uid FROM tbl_member_pos WHERE uid=? AND deleted = 0";
        $query = $this->db->query($sql, array($addrId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_ADDRIDINCORRECT;

		$sql = "SELECT uid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }

        $addr = array();
        $addr['memberid'] = $memberUid;
        $addr['receivername'] = $userName;
        $addr['phonenum'] = $userPhone;
        $addr['addrprovince'] = $userArea1;
        $addr['addrcity'] = $userArea2;
        $addr['addrarea'] = $userArea3;
        $addr['addrstreet'] = $userStreet;
        $addr['postaddr'] = $userPost;
		$this->db->where('uid', $addrId);
		//$sql = $this->db->update_string("tbl_member_pos", $addr);
		//log_message('info', 'updateaddress sql = ' . $sql);
		$this->db->update("tbl_member_pos", $addr);

        return SVCERR_SUCCESS;
    }

    /**
     * 지적된 주소를 삭제한다.
     * @param $userId
     * @param $addrId
     */
    function deleteAddress($userId, $addrId)
    {
		$sql = "SELECT uid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }


        $sql = "SELECT receiveraddrid FROM tbl_member WHERE memberid=?";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_ADDRIDINCORRECT;

        $row = $query->row();
        $defaultAddrId = $row->receiveraddrid;

        $sql = "UPDATE tbl_member_pos SET deleted = 1 WHERE memberid = ? AND uid = ?";
        $this->db->query($sql, array($memberUid, $addrId));

        if ($addrId == $defaultAddrId)
        {
            $sql = "UPDATE tbl_member SET receiveraddrid = 0 WHERE uid=?";
            $this->db->query($sql, array($memberUid));
        }
		return SVCERR_SUCCESS;
    }

    /**
     * 주소목록을 얻는 함수
     * @param $userId
     * @return array
     */
    function getAddressList($userId)
    {
		$sql = "SELECT uid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }

        $sql = "SELECT uid, receivername, phonenum, addrprovince, addrcity, addrarea, addrstreet, postaddr FROM tbl_member_pos WHERE deleted = 0  AND memberid = ?";
        $query = $this->db->query($sql, array($memberUid));
        return $query->result();
    }

	/**
	 *
	 * 지적된 사용자의 기정주소를 얻는 함수
	 */
	function getDefaultAddressId($userId)
    {
		$sql = "SELECT receiveraddrid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

		$row = $query->row();
		if (empty($row->receiveraddrid))
			return -1;
		return intval($row->receiveraddrid);
    }

    /**
     * 지적된 주소를 기정주소로 만든다.
     * @param $user_id
     * @param $address_id
     */
    function setDefaultAddress($userId, $addrId)
    {
		$sql = "SELECT uid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_USERIDINCORRECT;

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }

		$sql = "SELECT uid FROM tbl_member_pos WHERE uid=? AND deleted = 0";
        $query = $this->db->query($sql, array($addrId));
        if ($query->num_rows() != 1)
            return SVCERR_ADDRESS_ADDRIDINCORRECT;

        $sql = "UPDATE tbl_member SET receiveraddrid = ? WHERE uid = ?";
        $this->db->query($sql, array($addrId, $memberUid));
        return;
    }

    /**
     * $levelId로 지적한 상품분류를 얻는다.
     * @param $categoryId
     * @return array|string
     */
    function getLevelList($levelId)
    {
        if ($levelId < 0)
        {
            $sql = "SELECT uid, name FROM tbl_goodslevel1 WHERE deleted = 0";
            $query = $this->db->query($sql);
            return $query->result();
        }
        $sql = "SELECT uid FROM tbl_goodslevel1 WHERE uid=".$levelId;
        $query = $this->db->query($sql);
        if ($query->num_rows() <= 0)
            return SVCERR_CATEGORY_CATEGORYINCORRECT;

        $sql = "SELECT uid, name FROM tbl_goodslevel2 WHERE levelid = ? AND deleted = 0";
        $query = $this->db->query($sql, array($levelId));
        return $query->result();
    }


    /**
     * 지적된 부류의 고전상품목록을 얻는다.
     * @param $levelId
     * @return array|string
     */
    function getGoodList( $levelId )
    {
        $sql = "SELECT uid FROM tbl_goodslevel2 WHERE uid = ?";
        $query = $this->db->query($sql, array($levelId));
        if ($query->num_rows() <= 0)
            return SVCERR_GOOD_LEVELIDINCORRECT;

        $select = "SELECT uid, name, imguri, reserveprice, price FROM tbl_goods";
        $where = "WHERE style = 1 AND deleted = 0 AND level2id = ?" ;
        $order = "ORDER BY showorder desc";
        $sql = $select . " " . $where . " " . $order;
        $query = $this->db->query($sql, array($levelId));

        $array = array();
        $i = 0;
        foreach($query->result() as $row)
        {
            $item = array();
            $item[SVCP_GOOD_GOODID] = $row->uid;
            $item[SVCP_GOOD_NAME] = $row->name;
            $item[SVCP_GOOD_IMAGEURL] = SVCC_BASEIMAGEURL . $row->imguri;
            $item[SVCP_GOOD_PRICE] = $row->price;
            $item[SVCP_GOOD_ORDERPRICE] = $row->reserveprice;
            $array[$i] = $item;
            $i++;
        }
        return $array;
    }

    /**
     * 상품상세정보를 얻는다.
     * @param $goodId
     * @return array
     */
    function getGood( $goodId )
    {
        $sql = "SELECT uid, name, imguri, price, reserveprice FROM tbl_goods WHERE uid=? AND deleted = 0";
        $query = $this->db->query($sql, array($goodId));
        if ($query->num_rows() != 1)
            return SVCERR_GOOD_GOODIDINCORRECT;

        foreach ($query->result() as $row)
            return $row;
    }

    /**
     * 지적된 상품의 련관상품목록을 얻는다.
     * @param $goodId
     * @return mixed
     */
    function getRelativeGoodList($goodId)
    {
        // relative goods
        $sql = "SELECT relid, imguri FROM tbl_goods_relatives AS a, tbl_goods AS b WHERE a.relid = b.uid";
        $sql .= " AND goodsid = ? AND a.deleted = 0";
        $rel_query = $this->db->query($sql, array($goodId));
        return $rel_query->result();
    }

    /**
     * 지적된 상품의 속성목록을 얻는다.
     * @param $goodId
     * @return mixed
     */
    function getGoodSizeList($goodId)
    {
        // sizes
        $sql = "SELECT size, color, remain FROM tbl_goods_property WHERE deleted = 0 AND goodsid = ?";
        $prop_query = $this->db->query($sql, array($goodId));
        return $prop_query->result();
    }

    /**
     * 지적된 상품의 전시그림목록(url)을 얻는다.
     * @param $goodId
     * @return mixed
     */
    function getGoodImageList($goodId)
    {
        // images;
        $sql = "SELECT imguri FROM tbl_goods_img WHERE deleted = 0 and kind = 0 AND goodsid = ?";
        $img_query = $this->db->query($sql, array($goodId));
        return $img_query->result();
    }

    /**
     * 지적된 상품의 리력그림목록(url)을 얻는다.
     * @param $goodId
     * @return mixed
     */
    function getGoodHistoryList($goodId)
    {
        // images;
        $sql = "SELECT imguri FROM tbl_goods_img WHERE deleted = 0 and kind = 1 AND goodsid = ?";
        $img_query = $this->db->query($sql, array($goodId));
        return $img_query->result();
    }

    /**
     * 파라메터로 지적된 예약을 등록한다.
     * @param $param
     * @return string
     */
    function reserveGoods($param)
    {
        // get member's uid
        $memberid = $param[SVCP_RESERVE_USERID];
        $sql = "SELECT uid, memberid FROM tbl_member WHERE memberid=? AND deleted = 0";
        $query = $this->db->query($sql, array($memberid));
        if ($query->num_rows() != 1)
        {
            log_message('info', 'reserveGoods - membercount : ' . $query->num_rows() ." " . $sql);
            return SVCERR_RESERVE_USERIDINCORRECT;
        }

        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }


        $posId = $param[SVCP_RESERVE_POSID];
        $sendTime = $param[SVCP_RESERVE_SENDTIME];

        // check pos
        $sql = "SELECT uid FROM tbl_member_pos WHERE memberid = ? AND uid = ?";
        $query = $this->db->query($sql, array($memberUid, $posId));
        if ($query->num_rows() != 1)
            return SVCERR_RESERVE_POSIDINCORRECT;


        $goods = $param[SVCP_RESERVE_GOODS];
        $count = count($goods);
        if ($count <= 0)
            return SVCERR_RESERVE_GOODSINCORRECT;

        $orderno = date("YmdHis");
        $orderno = sprintf("%s%04d", $orderno, $memberUid);

        $sql = "INSERT INTO tbl_order(orderno, memberid, ordertime, status, posid, sendtime) VALUES(?, ?, CURRENT_TIMESTAMP, 0, ?, ?)";
        $this->db->query($sql, array($orderno, $memberUid, $posId, $sendTime));
        $newId = $this->db->insert_id();
		log_message('info', 'tbl_order insert_id : ' . $newId);

        $i = 0;
        for($i = 0; $i < $count; $i++)
        {
            $good = $goods[$i];
            $goodId = $good[SVCP_GOOD_GOODID];
            $sizes = $good[SVCP_GOOD_SIZES];
			$reserveprice = $good[SVCP_GOOD_ORDERPRICE];

			$sql = "SELECT uid, price FROM tbl_goods WHERE uid = ".$goodId;
            $pricequery = $this->db->query($sql);
			if ($pricequery->num_rows() != 1)
				return SVCERR_RESERVE_GOODSINCORRECT;
			$price = $pricequery->row()->price;

            for ($j = 0; $j < count($sizes); $j++)
            {

                $size = $sizes[$j][SVCP_GOOD_SIZE];
                $color = $sizes[$j][SVCP_GOOD_COLOR];
                $sql = "SELECT uid, remain FROM tbl_goods_property WHERE goodsid = ? AND size=? AND color=? AND deleted = 0";
                $subquery = $this->db->query($sql, array($goodId, $size, $color));
                if ($subquery->num_rows() != 1)
                {
                    log_message('info', 'reserve goods-sql' . $sql);
                    return SVCERR_RESERVE_GOODSINCORRECT;
                }
                $row = $subquery->row();
                $propId = $row->uid;
                $remain = intval($row->remain);


                //$propId = $good[SVCP_GOOD_PROPID];
                $goodCount = intval($sizes[$j][SVCP_GOOD_COUNT]);
                if ($goodCount > $remain)
                    $goodCount = $remain;

                $sql = "INSERT INTO tbl_order_goods(orderid, goodsid, propertyid, quantity, reserveprice, price) VALUES(?, ?, ?, ?, ?, ?)";
                $this->db->query($sql, array($newId, $goodId, $propId, $goodCount, $reserveprice, $price));

                $data = array('remain' => $remain - $goodCount);
                $this->db->where('uid', $propId);
                $this->db->update('tbl_goods_property', $data);
            }
        }
        return SVCERR_SUCCESS;
    }

    function getReserveList( $userId, $firstMonth)
    {
        $fromDate = @date( 'Y-m-d', mktime(0,0,0, @date('m')-1, @date('d'), @date('Y')) );

        $sql = "SELECT uid FROM tbl_member WHERE memberid=?";
        $query = $this->db->query($sql, array($userId));
        if ($query->num_rows() != 1)
            return SVCERR_RESERVE_USERIDINCORRECT;
        foreach($query->result() as $row)
        {
            $memberUid = $row->uid;
            break;
        }

        $sql = "SELECT uid, orderno, CONVERT(VARCHAR, ordertime, 120) as ordertime, status, posid, sendtime FROM tbl_order WHERE deleted = 0 AND memberid = "
            . $memberUid;

        if( intval($firstMonth) == 1 )
            $sql .= " AND ordertime >= '" . $fromDate . "'";
        else if( intval($firstMonth) == 0 )
            $sql .= " AND ordertime < '" . $fromDate . "'";
		$sql .= " ORDER BY ordertime desc";

        log_message('info', 'reservelist sql = ' . $sql);
        $query = $this->db->query($sql);
        $orderArray = $query->result();
        $i = 0;
        $result = array();
        foreach ($orderArray as $orderData)
        {
            $item = array();
            $item['uid'] = $orderData->uid;
            $item['orderno'] = $orderData->orderno;
            $item['ordertime'] = $orderData->ordertime;
            $item['status'] = $orderData->status;
            $item['sendtime'] = $orderData->sendtime;

            //good list
            $sql = "SELECT a.uid as uid, a.goodsid as goodid, a.propertyid as propertyid, a.quantity as quantity, b.name as name, b.imguri as imguri, b.reserveprice as reserveprice, c.size as size, c.color as color FROM tbl_order_goods as a, tbl_goods as b, tbl_goods_property as c"
                . " WHERE a.deleted = 0 AND a.goodsid = b.uid AND a.propertyid = c.uid AND a.orderid = " . $orderData->uid;
            log_message("info", "order sql - " . $sql);
            $subQuery = $this->db->query($sql);
            $item['goods'] = $subQuery->result();

            $result[$i] = $item;
            $i++;
        }
        return $result;
    }


    /**
     * 분점목록을 얻는 함수
     */
    function getBranchList()
    {
        $sql = "SELECT uid, name, imguri, linkurl FROM tbl_online_shop WHERE deleted = 0 ORDER BY showorder";
        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * 통보문목록을 얻는 함수
     */
    function getMessageList($userId)
    {
        $sql = "SELECT uid, title, convert(varchar(19), createtime, 120) as createtime FROM tbl_message WHERE deleted = 0 ORDER BY createtime desc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * 통보문상세정보를 얻는 함수
     */
    function getMessageContent($msgId)
    {
        $sql = "SELECT uid, title, contents, convert(varchar(10), createtime, 120) as createtime FROM tbl_message WHERE deleted = 0 "
        . " AND uid = ?";
        $query = $this->db->query($sql, array($msgId));

        return $query->result();
    }

	function getCompanyInfo()
	{
		$sql = "SELECT imguri FROM tbl_comp_img WHERE deleted = 0";
		$query = $this->db->query($sql);
		return $query->result();
	}

    /**
     * @param $userId
     * @param $cardNum
     */
    function getPassword($userId, $cardNum)
    {
        $sql = "SELECT uid, phonenum, identifycard FROM tbl_member WHERE memberid=?";
        $query = $this->db->query($sql, array($userId));
        if ($query == null || $query->num_rows() != 1)
            return SVCERR_GETPWD_USERIDINCORRECT;

        $row = $query->row();
        if ($row == null)
            return SVCERR_GETPWD_USERIDINCORRECT;

        $memberUid = $row->uid;
        $realCardNum = $row->identifycard;
        if ($realCardNum != $cardNum)
            return SVCERR_GETPWD_CARDNUMINCORRECT;

        $phonenum = $row->phonenum;
        $pwd = $this->generateRandomPassword(6);
        $content = sprintf(SVCC_PWDMSG, $userId, $pwd);
        $content = $this->common->convertChn($content);
        $sendtime = date('Y-m-d H:i:s');

        $url = SMS_SERVER.
            '?act='.SMS_ACTION_SEND.
            '&unitid='.SMS_COMP_ID.
            '&username='.SMS_USERNAME.
            '&passwd='.SMS_PASSWORD.
            '&msg='.$content.
            '&phone='.$phonenum.
            '&port='.
            '&sendtime='.$sendtime;

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'method'  => 'GET'
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $results = explode(",", $result);

        if (count($results) > 2)
        {
            //$this->log_sms_history($receiver, $phonenum, $content, $sendtime, "");
            $data = array('password' => $pwd);
            $this->db->where('uid', $memberUid);
            $this->db->update('tbl_member', $data);
            return SVCERR_SUCCESS;
        }
        return SVCERR_GETPWD_SENDMSG;
    }
}