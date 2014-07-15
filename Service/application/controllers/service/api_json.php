<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

////////////////////////////////////////////////////////////////////////////////////////
define('SVCC_DATA', 'data');
define('SVCC_ERROR', 'error');
define('SVCC_MSG', 'msg');
define('SVCC_TOKEN', 'token');

//////////////////////////////////////////////////////////////////////////
define('SVCERR_SUCCESS', '0');

// user login
define('SVCERR_USERLOGIN_EMAILNOTSET', '10');
define('SVCERR_USERLOGIN_EMAILINCORRECT', '11');
define('SVCERR_USERLOGIN_SEVERALEMAIL', '12');
define('SVCERR_USERLOGIN_PWDINCORRECT', '13');

// register User
define('SVCERR_REGISTERUSER_PARAMNOTSET', '20');
define('SVCERR_REGISTERUSER_DUPLICATE', '21');

// update user
define('SVCERR_UPDATEUSER_PARAMNOTSET', '30');
define('SVCERR_UPDATEUSER_NOTEXIST', '31');

// get user
define('SVCERR_GETUSER_PARAMNOTSET', '40');
define('SVCERR_GETUSER_EMAILINCORRECT', '41');

// forgot pwd
define('SVCERR_FORGOTPWD_EMAILNOTSET', '50');

// get all foods

// get foods of page
define('SVCERR_GETFOODS_PARAMINCORRECT', '70');

// add current food
define('SVCERR_CURRENTFOOD_FOODIDINCORRECT', '80');
define('SVCERR_CURRENTFOOD_USERIDNOTSET', '81');
define('SVCERR_CURRENTFOOD_USERIDINCORRECT', '82');

// retrieve current foods

// add favorites food
define('SVCERR_FAVORITESFOOD_FOODIDINCORRECT', '100');
define('SVCERR_FAVORITESFOOD_USERIDNOTSET', '101');
define('SVCERR_FAVORITESFOOD_USERIDINCORRECT', '102');

// get consumed with date
define('SVCERR_CONSUMED_USERIDNOTSET', '110');
define('SVCERR_CONSUMED_USERIDINCORRECT', '111');
define('SVCERR_CONSUMED_DATENOTSET', '112');


define('SVCERR_DBERROR', '1001');



define('USERTYPE_NORMAL', '0');
define('USERTYPE_NONEAUTH', '1');
define('USERTYPE_FACEBOOK', '2');
define('USERTYPE_TWITTER', '3');

class Api_json extends CI_Controller {
	
	var $ERROR_CODES = array(
			SVCERR_SUCCESS => array('code' => SVCERR_SUCCESS, 'msg' => 'success'),
			SVCERR_USERLOGIN_EMAILNOTSET => array('code' => SVCERR_USERLOGIN_EMAILNOTSET, 'msg' => 'Email is not set'),
			SVCERR_USERLOGIN_EMAILINCORRECT => array('code' => SVCERR_USERLOGIN_EMAILINCORRECT, 'msg' => 'Email address is incorrect'),
			SVCERR_USERLOGIN_SEVERALEMAIL => array('code' => SVCERR_USERLOGIN_SEVERALEMAIL, 'msg' => 'Several email addresses'),
			SVCERR_USERLOGIN_PWDINCORRECT => array('code' => SVCERR_USERLOGIN_PWDINCORRECT, 'msg' => 'Password is incorrect'),
			
			SVCERR_REGISTERUSER_PARAMNOTSET => array('code' => SVCERR_REGISTERUSER_PARAMNOTSET, 'msg' => 'Parameter is not set'),
			SVCERR_REGISTERUSER_DUPLICATE => array('code' => SVCERR_REGISTERUSER_DUPLICATE, 'msg' => 'Email address is duplicated'),

            SVCERR_UPDATEUSER_PARAMNOTSET => array('code' => SVCERR_UPDATEUSER_PARAMNOTSET, 'msg' => 'Parameter is not set'),
            SVCERR_UPDATEUSER_NOTEXIST => array('code' => SVCERR_UPDATEUSER_NOTEXIST, 'msg' => 'User is unknown'),

            SVCERR_FORGOTPWD_EMAILNOTSET => array('code' => SVCERR_FORGOTPWD_EMAILNOTSET, 'msg' => 'Email is not set'),

            SVCERR_GETUSER_PARAMNOTSET => array('code' => SVCERR_GETUSER_PARAMNOTSET, 'msg' => 'Parameter is not set'),
            SVCERR_GETUSER_EMAILINCORRECT => array('code' => SVCERR_GETUSER_EMAILINCORRECT, 'msg' => 'Email is incorrect'),

            SVCERR_GETFOODS_PARAMINCORRECT => array('code' => SVCERR_GETFOODS_PARAMINCORRECT, 'msg' => 'Page is incorrect'),

            SVCERR_CURRENTFOOD_FOODIDINCORRECT => array('code' => SVCERR_CURRENTFOOD_FOODIDINCORRECT, 'msg' => 'Food Id is incorrect'),
        SVCERR_CURRENTFOOD_USERIDINCORRECT => array('code' => SVCERR_CURRENTFOOD_USERIDINCORRECT, 'msg' => 'User Id is incorrect'),
        SVCERR_CURRENTFOOD_USERIDNOTSET => array('code' => SVCERR_CURRENTFOOD_USERIDNOTSET, 'msg' => 'User Id is not set'),

        SVCERR_FAVORITESFOOD_FOODIDINCORRECT => array('code' => SVCERR_FAVORITESFOOD_FOODIDINCORRECT, 'msg' => 'Food Id is incorrect'),
        SVCERR_FAVORITESFOOD_USERIDINCORRECT => array('code' => SVCERR_FAVORITESFOOD_USERIDINCORRECT, 'msg' => 'User Id is incorrect'),
        SVCERR_FAVORITESFOOD_USERIDNOTSET => array('code' => SVCERR_FAVORITESFOOD_USERIDNOTSET, 'msg' => 'User Id is not set'),

        SVCERR_CONSUMED_USERIDNOTSET => array('code' => SVCERR_CONSUMED_USERIDNOTSET, 'msg' => 'User Id is is not set'),
        SVCERR_CONSUMED_USERIDINCORRECT => array('code' => SVCERR_CONSUMED_USERIDINCORRECT, 'msg' => 'User Id is incorrect'),
        SVCERR_CONSUMED_DATENOTSET => array('code' => SVCERR_CONSUMED_DATENOTSET, 'msg' => 'Date is not set'),

            SVCERR_DBERROR => array('code' => SVCERR_DBERROR, 'msg' => 'Database error')
		);
	
    public function __construct() {
        parent::__construct(); 
        $this->load->library( 
            array(
                'parser'
                )
        );      
        $this->load->model('service_m');
        $this->load->library(array('baselib.php', "common", "form_validation"));
    }
    
    private function index()
    {
    }
    
    private function token(){
        return sha1($this->session->userdata('log_user_id'));
    }

    /**
     * user login
     */
    public function loginUserWithEmail()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
		$msg = "";

        // parameters
        log_message('info', "email:" . $_POST["email"]);
        log_message('info', "pwd:" . $_POST["pwd"]);

        if(!isset($_POST["email"]))
        {
            $err = SVCERR_USERLOGIN_EMAILNOTSET;
            goto bail;
        }
        $email = $_POST["email"];

        if(isset($_POST["pwd"]))
        {
            $userPwd   = $_POST["pwd"];
        }
        else
        {
            $userPwd = "";
        }
		
        $err = $this->service_m->loginUserWithEmail($email, $userPwd);

        if ($err == SVCERR_SUCCESS)
		{
			// get user data
			$user = $this->service_m->getUserWithEmail($email, USERTYPE_NORMAL);
			if ($user != null)
				$this->datas[SVCC_DATA] = $user;
			else
				$err = SVCERR_USERLOGIN_EMAILINCORRECT;
		}

    bail:
		$this->datas[SVCC_ERROR] = $err;
		$this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];
		
		log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);
		
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function loginUserWithFacebook()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        // parameters
        log_message('info', "email:" . $_POST["email"]);

        if(!isset($_POST["email"]))
        {
            $err = SVCERR_USERLOGIN_EMAILNOTSET;
            goto bail;
        }
        $email = $_POST["email"];
        $name = $_POST["name"];
        $gender = $_POST["gender"];
        $age = $_POST["age"];
        $token = $_POST["token"];

        $err = $this->service_m->loginUserWithFacebook($email, $name, $gender, $age, $token);

        if ($err == SVCERR_SUCCESS)
        {
            // get user data
            $user = $this->service_m->getUserWithEmail($email, USERTYPE_FACEBOOK);
            if ($user != null)
                $this->datas[SVCC_DATA] = $user;
            else
                $err = SVCERR_USERLOGIN_EMAILINCORRECT;
        }

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }


	/**
     * logoutUser
     */
    public function logoutUser()
    {
        //
    }

    /**
     * register user
     */
    public function registerUser()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
		$msg = "";

        // parameters
		log_message('info', "name:" . $_POST["name"]);
		log_message('info', "age:" . $_POST["age"]);
		log_message('info', "gender:" . $_POST["gender"]);
		log_message('info', "weight:" . $_POST["weight"]);
		log_message('info', "height:" . $_POST["height"]);
        log_message('info', "email:" . $_POST["email"]);
        log_message('info', "pwd:" . $_POST["pwd"]);
		log_message('info', "type:" . $_POST["type"]);
		log_message('info', "token:" . $_POST["token"]);

        if(!isset($_POST["name"]) ||
			!isset($_POST["age"]) ||
			!isset($_POST["gender"]) ||
			!isset($_POST["weight"]) ||
			!isset($_POST["height"]) ||
			!isset($_POST["email"]) ||
			!isset($_POST["pwd"]) ||
			!isset($_POST["type"]) ||
			!isset($_POST["token"]))
        {
            $err = SVCERR_REGISTERUSER_PARAMNOTSET;
            goto bail;
        }
		
		$name = $_POST["name"];
		$age = $_POST["age"];
		$gender = $_POST["gender"];
		$weight = $_POST["weight"];
		$height = $_POST["height"];
        $email = $_POST["email"];
		$pwd = $_POST["pwd"];
		$type = $_POST["type"];
		$token = $_POST["token"];

        $err = $this->service_m->registerUser($name, $age, $gender, $weight, $height, $email, $pwd, $type, $token);

        if ($err == SVCERR_SUCCESS)
		{
            // get user data
            $user = $this->service_m->getUserWithEmail($email, $type);
            if ($user != null)
                $this->datas[SVCC_DATA] = $user;
            else
                $err = SVCERR_USERLOGIN_EMAILINCORRECT;
		}

    bail:
		$this->datas[SVCC_ERROR] = $err;
		$this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];
		
		log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);
		
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * update user information
     */
    public function updateUser()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        // parameters
        log_message('info', "name:" . $_POST["name"]);
        log_message('info', "age:" . $_POST["age"]);
        log_message('info', "gender:" . $_POST["gender"]);
        log_message('info', "weight:" . $_POST["weight"]);
        log_message('info', "height:" . $_POST["height"]);
        log_message('info', "email:" . $_POST["email"]);
        log_message('info', "pwd:" . $_POST["pwd"]);
        log_message('info', "type:" . $_POST["type"]);
        log_message('info', "token:" . $_POST["token"]);

        if(!isset($_POST["uid"]) ||
            !isset($_POST["name"]) ||
            !isset($_POST["age"]) ||
            !isset($_POST["gender"]) ||
            !isset($_POST["weight"]) ||
            !isset($_POST["height"]) ||
            !isset($_POST["email"]) ||
            !isset($_POST["pwd"]) ||
            !isset($_POST["type"]) ||
            !isset($_POST["token"]))
        {
            $err = SVCERR_UPDATEUSER_PARAMNOTSET;
            goto bail;
        }

        $uid = $_POST["uid"];
        $name = $_POST["name"];
        $age = $_POST["age"];
        $gender = $_POST["gender"];
        $weight = $_POST["weight"];
        $height = $_POST["height"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];
        $type = $_POST["type"];
        $token = $_POST["token"];

        $err = $this->service_m->updateUser($uid, $name, $age, $gender, $weight, $height, $email, $pwd, $type, $token);

        if ($err == SVCERR_SUCCESS)
        {
            // get user data
            $user = $this->service_m->getUser($uid);
            if ($user != null)
                $this->datas[SVCC_DATA] = $user;
            else
                $err = SVCERR_USERLOGIN_EMAILINCORRECT;
        }

    bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

	/**
     * get user information
     */
    public function getUser()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        // parameters
        log_message('info', "name:" . $_POST["email"]);


        if (!isset($_POST["email"]))
        {
            $err = SVCERR_GETUSER_PARAMNOTSET;
            goto bail;
        }

        $email = $_POST["email"];
        $data = $this->service_m->getUserWithEmail($email, USERTYPE_NORMAL);

        if ($data != null)
        {
            $this->datas[SVCC_DATA] = $data;
        }
        else
            $err = SVCERR_GETUSER_EMAILINCORRECT;


    bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function forgotPassword()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        if(!isset($_POST['email']))
        {
            $err = SVCERR_FORGOTPWD_EMAILNOTSET;
            goto bail;
        }

        $email = $_POST['email'];
        $ret = $this->service_m->setRandomPwd($email);
        if ($ret == null)
        {
            $err = SVCERR_DBERROR;
            goto bail;
        }

        // send email
        $admin_email = "admin@sin.com";
        $this->load->library('email');
        $this->email->from($admin_email, 'Admin');
        $this->email->to($email);
        $this->email->cc($admin_email);
        $this->email->bcc($admin_email);

        $this->email->subject('Password Reset');
        $this->email->message('New Password is '.$ret);

        $this->email->send();

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * get foods for specified page
     */
    public function getFoodsWithPage()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $uid = 0;
        if (isset($_POST['uid']))
        {
            $uid = $_POST['uid'];
        }

        $keyword = "";
        if (isset($_POST['keyword']))
        {
            $keyword = $_POST['keyword'];
        }

        if (!isset($_POST['page']))
        {
            $err = SVCERR_GETFOODS_PARAMINCORRECT;
            goto bail;
        }
        $page = $_POST['page'];
        if ($page < 0)
        {
            $err = SVCERR_GETFOODS_PARAMINCORRECT;
            goto bail;
        }

        $hasNext = false;
        $data = $this->service_m->getFoodsWithPage($uid, $keyword, $page, $hasNext);

        if ($data != null)
        {
            $array = array();
            $i = 0;
            foreach($data as $row)
            {
                $item = array();
                $item["uid"] = $row->uid;
                $item["brand"] = $row->brand;
                $item["name"] = $row->name;
                $item["servingsize"] = $row->servingsize;
                $item["calories"] = $row->calories;
                $item["protein"] = $row->protein;
                $item["carbs"] = $row->carbs;
                $item["fat"] = $row->fat;
                $item["image"] = $row->image;
                $item["useruid"] = $row->useruid;
                $array[$i] = $item;
                $i++;
            }
            $resultData = array();
            $resultData["hasNext"] = ($hasNext == true)?"true":"false";
            $resultData["array"] = $array;
            $this->datas[SVCC_DATA] = $resultData;
        }
        else
            $err = SVCERR_DBERROR;


        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function addFoodToCurrent()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $fooduid = 0;
        if (!isset($_POST['fooduid']))
        {
            $err = SVCERR_CURRENTFOOD_FOODIDINCORRECT;
            goto bail;
        }
        $fooduid = $_POST['fooduid'];

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }

        $ret = $this->service_m->addFoodToCurrent($fooduid, $useruid);

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function removeFoodFromCurrent()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $fooduid = 0;
        if (!isset($_POST['fooduid']))
        {
            $err = SVCERR_CURRENTFOOD_FOODIDINCORRECT;
            goto bail;
        }
        $fooduid = $_POST['fooduid'];

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }

        $ret = $this->service_m->removeFoodFromCurrent($fooduid, $useruid);

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function getCurrentFoods()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }
        $useruid = $_POST['useruid'];
        $data = $this->service_m->getCurrentFoods($useruid);

        if ($data != null)
        {
            $array = array();
            $i = 0;
            foreach($data as $row)
            {
                $item = array();
                $item["uid"] = $row->uid;
                $item["fooduid"] = $row->fooduid;
                $item["useruid"] = $row->useruid;
                $item["name"] = $row->name;
                $item["calories"] = $row->calories;
                $item["createdtime"] = $row->createdtime;
                $item["consumedtime"] = $row->consumedtime;
                $item["isconsumed"] = $row->isconsumed;
                $item["deleted"] = $row->deleted;
                $array[$i] = $item;
                $i++;
            }
            $resultData = array();
            $this->datas[SVCC_DATA] = $resultData;
        }
        else
            $err = SVCERR_DBERROR;


        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function addFoodToFavorites()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $fooduid = 0;
        if (!isset($_POST['fooduid']))
        {
            $err = SVCERR_CURRENTFOOD_FOODIDINCORRECT;
            goto bail;
        }
        $fooduid = $_POST['fooduid'];

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }

        $ret = $this->service_m->addFoodToFavorites($fooduid, $useruid);

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function removeFoodFromFavorites()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $fooduid = 0;
        if (!isset($_POST['fooduid']))
        {
            $err = SVCERR_CURRENTFOOD_FOODIDINCORRECT;
            goto bail;
        }
        $fooduid = $_POST['fooduid'];

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }

        $ret = $this->service_m->removeFoodFromFavorites($fooduid, $useruid);

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function getFavoritesFoods()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CURRENTFOOD_USERIDNOTSET;
            goto bail;
        }
        $useruid = $_POST['useruid'];
        $data = $this->service_m->getCurrentFoods($useruid);

        if ($data != null)
        {
            $array = array();
            $i = 0;
            foreach($data as $row)
            {
                $item = array();
                $item["uid"] = $row->uid;
                $item["fooduid"] = $row->fooduid;
                $item["useruid"] = $row->useruid;
                $item["name"] = $row->name;
                $item["calories"] = $row->calories;
                $item["createdtime"] = $row->createdtime;
                $item["deleted"] = $row->deleted;
                $array[$i] = $item;
                $i++;
            }
            $resultData = array();
            $this->datas[SVCC_DATA] = $resultData;
        }
        else
            $err = SVCERR_DBERROR;


        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function getConsumedWithDate()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CONSUMED_USERIDNOTSET;
            goto bail;
        }
        $useruid = $_POST['useruid'];

        $date = null;
        if (!isset($_POST['date']))
        {
            $err = SVCERR_CONSUMED_DATENOTSET;
            goto bail;
        }
        $date = $_POST['date'];

        $data = $this->service_m->getConsumedWithDate($useruid, $date);

        if ($data != null)
        {
            if (count($data) == 0)
            {
                //
            }
            else
            {
                $array = array();
                $i = 0;
                foreach($data as $row)
                {
                    $item = array();
                    $item["uid"] = $row->uid;
                    $item["useruid"] = $row->useruid;
                    $item["consumeddate"] = $row->consumeddate;
                    $item["createdtime"] = $row->createdtime;
                    $item["stepstaken"] = $row->stepstaken;
                    $item["caloriesconsumed"] = $row->caloriesconsumed;
                    $item["mileswalked"] = $row->mileswalked;
                    $item["deleted"] = $row->deleted;
                    $array[$i] = $item;
                    $i++;
                    break;
                }
                $resultData = array();
                $this->datas[SVCC_DATA] = $resultData;
            }
        }
        else
            $err = SVCERR_DBERROR;


        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function consumedFoods()
    {
        log_message("info", "-----------------" . __METHOD__ . "----------------------------------------------");
        $err = SVCERR_SUCCESS;
        $msg = "";

        $useruid = 0;
        if (!isset($_POST['useruid']))
        {
            $err = SVCERR_CONSUMED_USERIDNOTSET;
            goto bail;
        }
        $useruid = $_POST['useruid'];

        $date = null;
        if (!isset($_POST['date']))
        {
            $err = SVCERR_CONSUMED_DATENOTSET;
            goto bail;
        }

        // consumed date
        $date = $_POST['date'];

        $fooduids = $_POST['fooduids'];
        $fooduids = json_decode($fooduids, true);
        $ret = $this->service_m->consumedFoods($useruid, $date, $fooduids);

        if ($ret == false)
        {
            $err = SVCERR_DBERROR;
        }

        bail:
        $this->datas[SVCC_ERROR] = $err;
        $this->datas[SVCC_MSG] = $this->ERROR_CODES[$err]['msg'];

        log_message('info', __METHOD__ . " - error = " . $err . " : " . $this->ERROR_CODES[$err]['msg']);

        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;

    }

    /**
     *  사용자암호를 수정하는 API
     */
    public function changePwd()
    {
		log_message("info", "-changePwd----------------------------------------------");
        $_POST = $this->common->convertChn($_POST);
        $err = SVCERR_SUCCESS;

        log_message('info', SVCP_CHPWD_USERID . ":" . $_POST[SVCP_CHPWD_USERID]);
        log_message('info', SVCP_CHPWD_OLDPWD . ":" . $_POST[SVCP_CHPWD_OLDPWD]);
        log_message('info', SVCP_CHPWD_NEWPWD . ":" . $_POST[SVCP_CHPWD_NEWPWD]);


        if ($this->service_m->isForcedLogout($_POST) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        // check userId
        if(!isset($_POST[SVCP_CHPWD_USERID])){
            $err = SVCERR_CHPWD_USERIDINCORRECT;
			log_message('info', "error : userid not set");
            goto bail;
        }
        else
            $userId = $_POST[SVCP_CHPWD_USERID];

        // Check Password
        if(!isset($_POST[SVCP_CHPWD_OLDPWD])){
            $err = SVCERR_CHPWD_OLDPWDINCORRECT;
			log_message('info', "error : old password not set");
            goto bail;
        }
        else
            $oldPwd = $_POST[SVCP_CHPWD_OLDPWD];

        // check old password
        $result = $this->service_m->checkPwd($userId, $oldPwd);
        if ($result == false)
        {
            $err = SVCERR_CHPWD_OLDPWDINCORRECT;
			log_message('info', "error : old password incorrect : " . $oldPwd);
            goto bail;
        }

        // check new password
        if(!isset($_POST[SVCP_CHPWD_NEWPWD])){
            $err = SVCERR_CHPWD_NEWPWDINCORRECT;
			log_message('info', "error : new password not set: ");
            goto bail;
        }
        else
            $newPwd = $_POST[SVCP_CHPWD_NEWPWD];

        $this->service_m->setPwd($userId, $newPwd);
        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 사용자주소를 추가하는 API
     */
    public function addAddress()
    {
        log_message("info", "----------addAddress----------------------------------------------");
        $_POST = $this->common->convertChn($_POST);
        $err = SVCERR_SUCCESS;

        log_message('info', SVCP_ADDRESS_USERID . ":" . $_POST[SVCP_ADDRESS_USERID]);
        log_message('info', SVCP_ADDRESS_USERNAME . ":" . $_POST[SVCP_ADDRESS_USERNAME]);
        log_message('info', SVCP_ADDRESS_USERPHONE . ":" . $_POST[SVCP_ADDRESS_USERPHONE]);
        log_message('info', SVCP_ADDRESS_USERAREA1 . ":" . $_POST[SVCP_ADDRESS_USERAREA1]);
        log_message('info', SVCP_ADDRESS_USERAREA2 . ":" . $_POST[SVCP_ADDRESS_USERAREA2]);
        log_message('info', SVCP_ADDRESS_USERAREA3 . ":" . $_POST[SVCP_ADDRESS_USERAREA3]);
		log_message('info', SVCP_ADDRESS_USERSTREET . ":" . $_POST[SVCP_ADDRESS_USERSTREET]);
        log_message('info', SVCP_ADDRESS_USERPOST . ":" . $_POST[SVCP_ADDRESS_USERPOST]);


        if ($this->service_m->isForcedLogout($_POST) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        //userId, userName, userPhone, userAreaCode1, userAreaCode2, userAreaCode3, userStreet
        if(!isset($_POST[SVCP_ADDRESS_USERID]))
        {
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
        $userId = $_POST[SVCP_ADDRESS_USERID];

        if(!isset($_POST[SVCP_ADDRESS_USERNAME])){
            $userName = "";
        }else{
            $userName= $_POST[SVCP_ADDRESS_USERNAME];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERPHONE])){
            $userPhone = "";
        }else{
            $userPhone = $_POST[SVCP_ADDRESS_USERPHONE];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA1])){
            $userArea1 = "";
        }else{
            $userArea1 = $_POST[SVCP_ADDRESS_USERAREA1];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA2])){
            $userArea2 = "";
        }else{
            $userArea2 = $_POST[SVCP_ADDRESS_USERAREA2];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA3])){
            $userArea3 = "";
        }else{
            $userArea3 = $_POST[SVCP_ADDRESS_USERAREA3];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERSTREET])){
            $userStreet = "";
        }else{
            $userStreet = $_POST[SVCP_ADDRESS_USERSTREET];
        }
		if(!isset($_POST[SVCP_ADDRESS_USERSTREET])){
            $userPost = "";
        }else{
            $userPost = $_POST[SVCP_ADDRESS_USERSTREET];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERPOST])){
            $userPost = "";
        }else{
            $userPost = $_POST[SVCP_ADDRESS_USERPOST];
        }
        $result = $this->service_m->addUserAddress($userId, $userName, $userPhone, $userArea1, $userArea2, $userArea3, $userStreet, $userPost);
		if ($result == SVCERR_ADDRESS_USERIDINCORRECT)
		{
			$err = SVCERR_ADDRESS_USERIDINCORRECT;
			log_message('info', "error : " . SVCP_ADDRESS_USERID);
			goto bail;
		}

        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

	/**
     * 사용자주소를 갱신하는 API
     */
    public function updateAddress()
    {
        log_message("info", "----------updateAddress----------------------------------------------");
        $_POST = $this->common->convertChn($_POST);
        $err = SVCERR_SUCCESS;

		log_message('info', SVCP_ADDRESS_ADDRID . ":" . $_POST[SVCP_ADDRESS_ADDRID]);
        log_message('info', SVCP_ADDRESS_USERID . ":" . $_POST[SVCP_ADDRESS_USERID]);
        log_message('info', SVCP_ADDRESS_USERNAME . ":" . $_POST[SVCP_ADDRESS_USERNAME]);
        log_message('info', SVCP_ADDRESS_USERPHONE . ":" . $_POST[SVCP_ADDRESS_USERPHONE]);
        log_message('info', SVCP_ADDRESS_USERAREA1 . ":" . $_POST[SVCP_ADDRESS_USERAREA1]);
        log_message('info', SVCP_ADDRESS_USERAREA2 . ":" . $_POST[SVCP_ADDRESS_USERAREA2]);
        log_message('info', SVCP_ADDRESS_USERAREA3 . ":" . $_POST[SVCP_ADDRESS_USERAREA3]);
		log_message('info', SVCP_ADDRESS_USERSTREET . ":" . $_POST[SVCP_ADDRESS_USERSTREET]);
        log_message('info', SVCP_ADDRESS_USERPOST . ":" . $_POST[SVCP_ADDRESS_USERPOST]);

        if ($this->service_m->isForcedLogout($_POST) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }


        //addrId, userName, userPhone, userAreaCode1, userAreaCode2, userAreaCode3, userStreet
		if(!isset($_POST[SVCP_ADDRESS_ADDRID]))
        {
            $err = SVCERR_ADDRESS_ADDRIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
            goto bail;
        }
        $addrId = $_POST[SVCP_ADDRESS_ADDRID];

        if(!isset($_POST[SVCP_ADDRESS_USERID]))
        {
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
        $userId = $_POST[SVCP_ADDRESS_USERID];

        if(!isset($_POST[SVCP_ADDRESS_USERNAME])){
            $userName = "";
        }else{
            $userName= $_POST[SVCP_ADDRESS_USERNAME];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERPHONE])){
            $userPhone = "";
        }else{
            $userPhone = $_POST[SVCP_ADDRESS_USERPHONE];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA1])){
            $userArea1 = "";
        }else{
            $userArea1 = $_POST[SVCP_ADDRESS_USERAREA1];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA2])){
            $userArea2 = "";
        }else{
            $userArea2 = $_POST[SVCP_ADDRESS_USERAREA2];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERAREA3])){
            $userArea3 = "";
        }else{
            $userArea3 = $_POST[SVCP_ADDRESS_USERAREA3];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERSTREET])){
            $userStreet = "";
        }else{
            $userStreet = $_POST[SVCP_ADDRESS_USERSTREET];
        }
		if(!isset($_POST[SVCP_ADDRESS_USERSTREET])){
            $userPost = "";
        }else{
            $userPost = $_POST[SVCP_ADDRESS_USERSTREET];
        }
        if(!isset($_POST[SVCP_ADDRESS_USERPOST])){
            $userPost = "";
        }else{
            $userPost = $_POST[SVCP_ADDRESS_USERPOST];
        }
        $result = $this->service_m->updateUserAddress($addrId, $userId, $userName, $userPhone, $userArea1, $userArea2, $userArea3, $userStreet, $userPost);
		if ($result == SVCERR_ADDRESS_USERIDINCORRECT)
		{
			$err = SVCERR_ADDRESS_USERIDINCORRECT;
			log_message('info', "error : " . SVCP_ADDRESS_USERID);
			goto bail;
		}
		else if ($result == SVCERR_ADDRESS_ADDRIDINCORRECT)
		{
			$err = SVCERR_ADDRESS_ADDRIDINCORRECT;
			log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
			goto bail;
		}

        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function deleteAddress()
    {
        log_message("info", "----------deleteAddress----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        // parameters
        log_message('info', SVCP_ADDRESS_USERID . ":" . $_GET[SVCP_ADDRESS_USERID]);
        log_message('info', SVCP_ADDRESS_ADDRID . ":" . $_GET[SVCP_ADDRESS_ADDRID]);

        if(!isset($_GET[SVCP_ADDRESS_USERID]))
        {
            $err = SVCERR_ADDRESS_USERIDNOTSET;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }else{
            $userId = $_GET[SVCP_ADDRESS_USERID];
        }
        if(!isset($_GET[SVCP_ADDRESS_ADDRID]))
        {
            $err = SVCERR_ADDRESS_ADDRIDNOTSET;
            log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
            goto bail;
        }else{
            $addrId = $_GET[SVCP_ADDRESS_ADDRID];
        }

        $ret = $this->service_m->deleteAddress($userId, $addrId);
        if ($ret == SVCERR_ADDRESS_USERIDINCORRECT)
        {
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
        if ($ret == SVCERR_ADDRESS_ADDRIDINCORRECT)
        {
            $err = SVCERR_ADDRESS_ADDRIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
            goto bail;
        }

        log_message('info', "sucess");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 주소목록을 얻는 API
     */
    public function getAddressList()
    {
        log_message("info", "----------deleteAddress----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_ADDRESS_USERID . ":" . $_GET[SVCP_ADDRESS_USERID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if(!isset($_GET[SVCP_ADDRESS_USERID])){
            $err = SVCERR_ADDRESS_USERIDNOTSET;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
        $userId = $_GET[SVCP_ADDRESS_USERID];
        $ret = $this->service_m->getAddressList($userId);

		if($ret == SVCERR_ADDRESS_USERIDINCORRECT){
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }

		$defaultAddrId = $this->service_m->getDefaultAddressId($userId);
		if($ret == SVCERR_ADDRESS_USERIDINCORRECT){
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error1 : " . SVCP_ADDRESS_USERID);
            goto bail;
        }

        $i = 0;
        $array = array();
        foreach($ret as $row)
        {
            $item = array();
            $item[SVCP_ADDRESS_ADDRID] = $row->uid;
            $item[SVCP_ADDRESS_USERNAME] = $row->receivername;
            $item[SVCP_ADDRESS_USERPHONE] = $row->phonenum;
            $item[SVCP_ADDRESS_USERAREA1] = $row->addrprovince;
            $item[SVCP_ADDRESS_USERAREA2] = $row->addrcity;
            $item[SVCP_ADDRESS_USERAREA3] = $row->addrarea;
            $item[SVCP_ADDRESS_USERSTREET] = $row->addrstreet;
            $item[SVCP_ADDRESS_USERPOST] = $row->postaddr;
			if ($defaultAddrId == $row->uid)
				$item[SVCP_ADDRESS_ISDEFAULT] = 1;
			else
				$item[SVCP_ADDRESS_ISDEFAULT] = 0;
            $array[$i] = $item;
            $i++;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }


    /**
     * 기정주소를 설정하는 API
     */
    public function setDefaultAddress()
    {
        log_message("info", "----------setDefaultAddress----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_ADDRESS_USERID . ":" . $_GET[SVCP_ADDRESS_USERID]);
        log_message('info', SVCP_ADDRESS_ADDRID . ":" . $_GET[SVCP_ADDRESS_ADDRID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if(!isset($_GET[SVCP_ADDRESS_USERID])){
            $err = SVCERR_ADDRESS_USERIDNOTSET;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
        $userId = $_GET[SVCP_ADDRESS_USERID];
        if(!isset($_GET[SVCP_ADDRESS_ADDRID])){
            $err = SVCERR_ADDRESS_ADDRIDNOTSET;
            log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
            goto bail;
        }
        $addrId = $_GET[SVCP_ADDRESS_ADDRID];
        $ret = $this->service_m->setDefaultAddress($userId, $addrId);
		if($ret == SVCERR_ADDRESS_USERIDINCORRECT){
            $err = SVCERR_ADDRESS_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_USERID);
            goto bail;
        }
		if($ret == SVCERR_ADDRESS_ADDRIDINCORRECT){
            $err = SVCERR_ADDRESS_ADDRIDINCORRECT;
            log_message('info', "error : " . SVCP_ADDRESS_ADDRID);
            goto bail;
        }

        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 새상품목록을 얻는 API
     */
    public function getNewGoodList()
    {
        log_message("info", "----------getNewGoodList----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_GOOD_MONTH . ":" . $_GET[SVCP_GOOD_MONTH]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if (!isset($_GET[SVCP_GOOD_MONTH]))
            $month = "";
        else
            $month = $_GET[SVCP_GOOD_MONTH];

        $err = SVCERR_SUCCESS;
        $ret = $this->service_m->getNewGoodList($month);

        $array = array();
        $i = 0;
        foreach($ret as $row)
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
        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 상품분류목록을 얻는 API
     */
    public function getLevelList()
    {
        log_message("info", "----------getLevelList----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_LEVEL_LEVELID . ":" . $_GET[SVCP_LEVEL_LEVELID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if (!isset($_GET[SVCP_LEVEL_LEVELID]))
            $levelId = -1;
        else
            $levelId = $_GET[SVCP_LEVEL_LEVELID];

        $ret = $this->service_m->getLevelList($levelId);
        if ($ret == SVCERR_LEVEL_LEVELIDINCORRECT)
        {
            $err = SVCERR_LEVEL_LEVELIDINCORRECT;
            log_message('info', "error : " . SVCP_LEVEL_LEVELID);
            goto bail;
        }

        $i = 0;
        $array = array();
        foreach ($ret as $row)
        {
            $item = array();
            $item[SVCP_LEVEL_LEVELID] = $row->uid;
            $item[SVCP_LEVEL_NAME] = $row->name;
            $array[$i] = $item;
            $i++;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 고전상품목록을 얻는 API
     */
    public function getGoodList()
    {
        log_message("info", "----------getGoodList----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_GOOD_LEVELID . ":" . $_GET[SVCP_GOOD_LEVELID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if( !isset($_GET[SVCP_GOOD_LEVELID]))
        {
            $err = SVCERR_GOOD_LEVELIDNOTSET;
            goto bail;
        }
        $categoryId = $_GET[SVCP_GOOD_LEVELID];
        $ret = $this->service_m->getGoodList($categoryId);
        if ($ret == SVCERR_GOOD_LEVELIDINCORRECT)
        {
            $err = SVCERR_GOOD_LEVELIDINCORRECT;
            log_message('info', "error : " . SVCP_GOOD_LEVELID);
            goto bail;
        }

        $this->datas[SVCC_DATA] = $ret;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 상품의 상세정보를 얻는 API
     */
    public function getGoodDetail()
    {
        log_message("info", "----------getGoodDetail----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_GOOD_GOODID . ":" . $_GET[SVCP_GOOD_GOODID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if( !isset($_GET[SVCP_GOOD_GOODID]) )
        {
            $err = SVCERR_GOOD_GOODIDNOTSET;
            log_message('info', "error : " . SVCP_GOOD_GOODID);
            goto bail;
        }

        $goodId = $_GET[SVCP_GOOD_GOODID];
        $goodObj = $this->service_m->getGood($goodId);
        if ($goodObj == SVCERR_GOOD_GOODIDINCORRECT	)
        {
            $err = SVCERR_GOOD_GOODIDINCORRECT;
            log_message('info', "error : " . SVCP_GOOD_GOODID);
            goto bail;
        }

        $array = array();
        $array[SVCP_GOOD_GOODID] = $goodObj->uid;
        $array[SVCP_GOOD_NAME] = $goodObj->name;
        $array[SVCP_GOOD_IMAGEURL] = SVCC_BASEIMAGEURL . $goodObj->imguri;
        $array[SVCP_GOOD_PRICE] = $goodObj->price;
        $array[SVCP_GOOD_ORDERPRICE] = $goodObj->reserveprice;

        // relative goods
        $relObj = $this->service_m->getRelativeGoodList($goodId);
        $rel_goods = array();
        $i = 0;
        foreach ($relObj as $rel_row)
        {
            $item = array();
            $item[SVCP_GOOD_GOODID] = $rel_row->relid;
            $item[SVCP_GOOD_IMAGEURL] = SVCC_BASEIMAGEURL . $rel_row->imguri;
            $rel_goods[$i] = $item;
            $i++;
        }
        $array[SVCP_GOOD_RELATIVEGOODS] = $rel_goods;

        // sizes
        $sizeObj = $this->service_m->getGoodSizeList($goodId);
        $i = 0;
        $prop_array = array();
        foreach ($sizeObj as $prop_row)
        {
            $item = array();
            $item[SVCP_GOOD_SIZE] = $prop_row->size;
            $item[SVCP_GOOD_COLOR] = $prop_row->color;
            $item[SVCP_GOOD_COUNT] = $prop_row->remain;
            $prop_array[$i] = $item;
            $i++;
        }
        $array[SVCP_GOOD_SIZES] = $prop_array;

        // images;
        $imgObj = $this->service_m->getGoodImageList($goodId);
        $i = 0;
        $img_array = array();
        foreach ($imgObj as $img_row)
        {
            $item = array();
            $item[SVCP_GOOD_SMALLIMAGE] = SVCC_BASEIMAGEURL . $img_row->imguri;
            $item[SVCP_GOOD_LARGEIMAGE] = SVCC_BASEIMAGEURL . $img_row->imguri;
            $img_array[$i] = $item;
            $i++;
        }
        $array[SVCP_GOOD_IMAGES] = $img_array;

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success " . count($img_array));

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 상품의 리력정보를 얻는 API
     */
    public function getGoodHistory()
    {
        log_message("info", "----------getGoodHistory----------------------------------------------");

        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_GOOD_GOODID . ":" . $_GET[SVCP_GOOD_GOODID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if( !isset($_GET[SVCP_GOOD_GOODID]) )
        {
            $err = SVCERR_GOOD_GOODIDNOTSET;
            log_message('info', "error : " . SVCP_GOOD_GOODID);
            goto bail;
        }

        $goodId = $_GET[SVCP_GOOD_GOODID];
        $goodObj = $this->service_m->getGoodHistoryList($goodId);
        if ($goodObj == SVCERR_GOOD_GOODIDINCORRECT	)
        {
            $err = SVCERR_GOOD_GOODIDINCORRECT;
            log_message('info', "error : " . SVCP_GOOD_GOODID);
            goto bail;
        }

        $array = array();
        $i = 0;
        foreach($goodObj as $row)
        {
            $item = array();
            $item[SVCP_GOOD_IMAGEURL] = SVCC_BASEIMAGEURL . $row->imguri;
            $array[$i] = $item;
            $i++;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 상품에 대한 예약을 진행하는 API
     */
    public function reserveGoods()
    {
        log_message("info", "----------reserveGoods----------------------------------------------");
        //$_POST = $this->common->convertChn($_POST);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_RESERVE_PARAM . ":" . $_POST[SVCP_RESERVE_PARAM]);

        if ($this->service_m->isForcedLogout($_POST) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        $param = $_POST[SVCP_RESERVE_PARAM];
		log_message('info', 'reserveGoods - param : ' . $param);
        $param = json_decode($param, true);
        $param = $this->common->convertChn($param);
        log_message('info', 'param - ' . print_r($param, true));
        $ret = $this->service_m->reserveGoods($param);

		if ($ret == SVCERR_SUCCESS)
	        log_message('info', "success");
		else
			log_message('info', "error : " . $ret);
        $err = $ret;

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 지적된 user에 대한 예약기록을 얻는 API
     */
    public function getReserveList()
    {
        log_message("info", "----------getReserveList----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_RESERVE_USERID . ":" . $_GET[SVCP_RESERVE_USERID]);
        log_message('info', SVCP_RESERVE_FIRSTMONTH . ":" . $_GET[SVCP_RESERVE_FIRSTMONTH]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        // userId
        if(!isset($_GET[SVCP_RESERVE_USERID]))
        {
            $err = SVCERR_RESERVE_USERIDNOTSET;
            goto bail;
        }
        $userId = $_GET[SVCP_RESERVE_USERID];

        // firstMonth
        if(!isset($_GET[SVCP_RESERVE_FIRSTMONTH]))
        {
            $err = SVCERR_RESERVE_FIRSTMONTHNOTSET;
            log_message('info', "error : " . SVCP_RESERVE_FIRSTMONTH);
            goto bail;
        }
        $firstMonth = $_GET[SVCP_RESERVE_FIRSTMONTH];
		if (intval($firstMonth) != 0 && intval($firstMonth) != 1)
		{
			$err = SVCERR_RESERVE_FIRSTMONTHINCORRECT;
            log_message('info', "error : " . SVCP_RESERVE_FIRSTMONTH);
            goto bail;
		}
        $ret = $this->service_m->getReserveList($userId, $firstMonth);


        if ($ret == SVCERR_RESERVE_USERIDINCORRECT)
        {
            $err = SVCERR_RESERVE_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_RESERVE_USERID);
            goto bail;
        }

        $i = 0;
        $array = array();
        foreach ($ret as $orderData)
        {
            $item = array();
            $item[SVCP_RESERVE_RESERVEID] = $orderData['uid'];
            $item[SVCP_RESERVE_ORDERNO] = $orderData['orderno'];
            $item[SVCP_RESERVE_ORDERTIME] = $orderData['ordertime'];
            $item[SVCP_RESERVE_STATUS] = $orderData['status'];
            $item[SVCP_RESERVE_SENDTIME] = $orderData['sendtime'];

            $j = 0;
            $goods = array();
            foreach ($orderData['goods'] as $good)
            {
                $same = -1;
                for ($k = 0; $k < $j; $k++)
                {
                    if (!isset($goods[$k]))
                        continue;
                    if ($goods[$k][SVCP_GOOD_GOODID] == $good->goodid)
                    {
                        $same = $k;
                        break;
                    }
                }
                if ($same >= 0)
                {
                    //add kind

                    $subitem = $goods[$same];
                    $sizes = $subitem[SVCP_GOOD_SIZES];
                    $lastIndex = count($sizes);
                    $sizes[$lastIndex][SVCP_GOOD_SIZE] = $good->size;
                    $sizes[$lastIndex][SVCP_GOOD_COLOR] = $good->color;
                    $sizes[$lastIndex][SVCP_GOOD_COUNT] = $good->quantity;
                    $subitem[SVCP_GOOD_SIZES] = $sizes;
                    $goods[$same] = $subitem;
                }
                else
                {
                    // create new item and create a kind
                    $subitem = array();
                    $subitem[SVCP_GOOD_GOODID] = $good->goodid;
                    $subitem[SVCP_GOOD_NAME] = $good->name;
                    $subitem[SVCP_GOOD_IMAGEURL] = SVCC_BASEIMAGEURL . $good->imguri;
                    $subitem[SVCP_GOOD_ORDERPRICE] = $good->reserveprice;

                    $sizes = array();
                    $sizeItem = array();
                    $sizeItem[SVCP_GOOD_SIZE] = $good->size;
                    $sizeItem[SVCP_GOOD_COLOR] = $good->color;
                    $sizeItem[SVCP_GOOD_COUNT] = $good->quantity;
                    $sizes[0] = $sizeItem;

                    $subitem[SVCP_GOOD_SIZES] = $sizes;
                    $goods[$j] = $subitem;
                    $j++;
                }
            }

            $item[SVCP_RESERVE_GOODS] = $goods;

            $array[$i] = $item;
            $i++;
        }
        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }


    /**
     * 분점목록을 얻는 API
     */
    public function getBranchList()
    {
        log_message("info", "----------getBranchList----------------------------------------------");
        $err = SVCERR_SUCCESS;

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        $ret = $this->service_m->getBranchList();
        $i = 0;
        $array = array();
        foreach ($ret as $row)
        {
            $item = array();
            $item[SVCP_BRANCH_BRANCHID] = $row->uid;
            $item[SVCP_BRANCH_NAME] = $row->name;
            $item[SVCP_BRANCH_IMAGEURL] = SVCC_BASEIMAGEURL . $row->imguri;
            $item[SVCP_BRANCH_LINKURL] = $row->linkurl;
            $array[$i] = $item;
            $i++;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', 'success');

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 통보문목록을 얻는 API
     */
    public function getMessageList()
    {
        log_message("info", "----------getMessageList----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_MESSAGE_USERID . ":" . $_GET[SVCP_MESSAGE_USERID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        // userId
        if(!isset($_GET[SVCP_MESSAGE_USERID]))
        {
            $err = SVCERR_MESSAGE_USERIDNOTSET;
            goto bail;
        }
        $userId = $_GET[SVCP_MESSAGE_USERID];

        $ret = $this->service_m->getMessageList($userId);


        if ($ret == SVCERR_MESSAGE_USERIDINCORRECT)
        {
            $err = SVCERR_MESSAGE_USERIDINCORRECT;
            log_message('info', "error : " . SVCP_MESSAGE_USERID);
            goto bail;
        }

        $i = 0;
        $array = array();
        foreach ($ret as $row)
        {
            $item = array();
            $item[SVCP_MESSAGE_MSGID] = $row->uid;
            $item[SVCP_MESSAGE_TITLE] = $row->title;
            $item[SVCP_MESSAGE_SENDTIME] = $row->createtime;
            $array[$i] = $item;
            $i++;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");

    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    /**
     * 통보문상세정보를 얻는 API
     */
    public function getMessageContent()
    {
        log_message("info", "----------getMessageContent----------------------------------------------");
        $_GET = $this->common->convertChn($_GET);
        $err = SVCERR_SUCCESS;

        // parameters
        log_message('info', SVCP_MESSAGE_MSGID . ":" . $_GET[SVCP_MESSAGE_MSGID]);

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

        if (!isset($_GET[SVCP_MESSAGE_MSGID]))
        {
            $err = SVCERR_MESSAGE_MSGIDNOTSET;
            log_message('info', "error : " . SVCP_MESSAGE_MSGID);
            goto bail;
        }

        $msgId = $_GET[SVCP_MESSAGE_MSGID];
        $ret = $this->service_m->getMessageContent($msgId);
        $i = 0;
        $array = array();
        foreach ($ret as $row)
        {
            $array[SVCP_MESSAGE_MSGID] = $row->uid;
            $array[SVCP_MESSAGE_TITLE] = $row->title;
            $array[SVCP_MESSAGE_CONTENTS] = htmlspecialchars_decode($row->contents);
            $array[SVCP_MESSAGE_SENDTIME] = $row->createtime;
			break;
        }

        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

	public function getCompanyInfo()
	{
		log_message("info", "----------getCompanyInfo----------------------------------------------");
        $err = SVCERR_SUCCESS;

        if ($this->service_m->isForcedLogout($_GET) == true)
        {
            $err = SVCERR_FORCELOGOUT;
            goto bail;
        }

		$ret = $this->service_m->getCompanyInfo();
		$i = 0;
		$array = array();
		foreach ($ret as $row)
		{
			$item = array();
			$item[SVCP_COMPANY_IMAGEURL] = SVCC_BASEIMAGEURL . $row->imguri;
			$array[$i] = $item;
			$i++;
		}
        $this->datas[SVCC_DATA] = $array;
        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
	}

    public function getPassword()
    {
        log_message("info", "----------getPassword----------------------------------------------");
        $err = SVCERR_SUCCESS;

        $_POST = $this->common->convertChn($_POST);

        log_message('info', SVCP_GETPWD_USERID . ":" . $_POST[SVCP_GETPWD_USERID]);
        log_message('info', SVCP_GETPWD_CARDNUM . ":" . $_POST[SVCP_GETPWD_CARDNUM]);

        $userId = $_POST[SVCP_GETPWD_USERID];
        $cardNum = $_POST[SVCP_GETPWD_CARDNUM];

        if (empty($userId))
        {
            $err = SVCERR_GETPWD_USERIDNOTSET;
            log_message('info', 'error : userid not set');
            goto bail;
        }

        if (empty($cardNum))
        {
            $err = SVCERR_GETPWD_CARDNUMNOTSET;
            log_message('info', 'error : cardNum not set');
            goto bail;
        }

        $ret = $this->service_m->getPassword($userId, $cardNum);
        switch($ret)
        {
            case SVCERR_GETPWD_USERIDINCORRECT:
                log_message('info', 'error : userId incorrect');
                $err = SVCERR_GETPWD_USERIDINCORRECT;
                goto bail;
                break;
            case SVCERR_GETPWD_CARDNUMINCORRECT:
                log_message('info', 'error : cardNum incorrect');
                $err = SVCERR_GETPWD_CARDNUMINCORRECT;
                goto bail;
                break;
            case SVCERR_GETPWD_SENDMSG:
                log_message('info', 'error : send sms failed');
                $err = SVCERR_GETPWD_SENDMSG;
                goto bail;
                break;
            case SVCERR_SUCCESS:
                break;
            default:
                log_message('info', 'error : unkown error occurred in getPassword');
                goto bail;
                break;
        }

        log_message('info', "success");
    bail:
        $this->datas[SVCC_RET] = $err;
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
        return;
    }

    public function logPos()
    {


        $userId = $_POST['userid'];
        $x = $_POST['Long'];
        $y = $_POST['Lat'];
        $message = '(' . $x . ', ' . $y . ')' . "\r\n";

        if ( ! $fp = @fopen("C:/log.txt", FOPEN_WRITE_CREATE))
        {
            return FALSE;
        }

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);


        $this->datas[SVCC_RET] = "1";
        $this->parser->parse_string(json_encode($this->common->convertUTF8($this->datas)),array());
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */