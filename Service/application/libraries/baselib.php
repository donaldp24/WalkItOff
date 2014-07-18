<?php

class BaseLib {

	function realip() {
		$ip=false;
		if(!empty($_SERVER["HTTP_CLIENT_IP"])) $ip = $_SERVER["HTTP_CLIENT_IP"];

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
			for ($i = 0; $i < count($ips); $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}

	/**
	 * @description
	 *  截取字符串长度，一个中文字符为两个单位长度
	 *  如果$start开始的字是大于160的，就回溯到直到最近出现小于160的字符或字符头的位置
	 *  如果经过的字符是奇数，说明当前$start的位置为半个字符的位置
	 *
	 *  如果$len为奇数,实际截取字符串长度为$len+1
	 *  如果$len为偶数,实际截取字符串长度为$len
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $str    被截取字符串
	 *  $len    截取长度
	 *  $start  截取开始位置
	 * @return
	 *  (string)截取后的字符串
	 */
	function zh_substr($str, $len, $start=0, $postfix=0) {
		for($i=0;$i<$len;$i++) {
			$temp_str = substr($str,0,1);
			if(ord($temp_str) > 127){
				$i++;
				if($i<$len) {
					$new_str[] = substr($str,0,3);
					$str = substr($str,3);
				}
			} else {
				$new_str[] = substr($str,0,1);
				$str = substr($str,1);
			}
		}
		return join($new_str);
	}

	/**
	 * @description
	 *  计算字符串长度，一个中文字符为两个单位长度
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $str    被计算字符串
	 * @return
	 *  (int)字符串长度
	 */
	function zh_strlen($str, $start=0) {
	   for ($i = $start,$zh = 0,$asc = 0; $i < strlen($str); $i++)
		   (ord($str[$i]) > 160) ? $zh++ : $asc++;
	   return $zh + $asc;
	}



	/**
	 * @description
	 *  计算utf8字符串长度，utf8是3个字符表示一个非asc字符，
	 *  按照原来的习惯，我们还是转换成2个字符计算一个汉字。
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $str    被计算字符串
	 * @return
	 *  (string)被截取字符串
	 */
	function utf8_substr($str,$len,$start=0) {
		for($i=0;$i<$len;$i++) {
			$temp_str = substr($str,0,1);
			if(ord($temp_str) > 127){
				$i++;
				if($i<$len) {
					$new_str[] = substr($str,0,3);
					$str = substr($str,3);
				}
			} else {
				$new_str[] = substr($str,0,1);
				$str = substr($str,1);
			}
		}
		return join($new_str);
	}

	/**
	 * @description
	 *  计算utf8字符串长度，utf8是3个字符表示一个非asc字符，
	 *  按照原来的习惯，我们还是转换成2个字符计算一个汉字。
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $str    被计算字符串
	 * @return
	 *  (int)字符串长度
	 */
	function utf8_strlen($str, $start=0) {
		$len = strlen($str);
		for($i=0,$asc=0,$other=0;$i<$len;$i++) {
			(ord($str[$i]) > 127) ? $other++ : $asc++;
		}
		return ceil($other/3*2) + $asc;
	}


	/**
	 * function : get_max_filesize()
	 * param    : null
	 * return   : (int) filesize;
	 *
	 * memo     : 得到当前系统和应用共同决定的最大文件上传大小
	 */
	function get_max_filesize() {
		if(!defined('UPFILE_SIZE')) define('UPFILE_SIZE', 5242880);
		$post_max_size      = get_cfg_var('post_max_size');
		$post_max_size      = $post_max_size * 1024 * 1024;
		$upload_max_filesize= get_cfg_var('upload_max_filesize');
		$upload_max_filesize= $upload_max_filesize * 1024 * 1024;
		return min(array($post_max_size, $upload_max_filesize, UPFILE_SIZE));
	}

	function get_upfile_url($file, $nopic) {
		global $G_web_upload, $G_abs_upload;
		$abs    = $G_abs_upload . '/' . $file;
		if(file_exists($abs) && is_file($abs)) {
			return $G_web_upload . $file;
		} else {
			return $nopic;
		}
	}

	/**
	 * @description
	 *  处理sql字符串中的特殊字符
	 *  特别是用LIKE作为WHERE条件的时候不允许输入sql通配符
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $str        sql字符串
	 *  $like       是否使用like
	 * @return
	 *  (string)处理后的sql
	 */
	function parse_sql_str($str, $like=0) {
		if(!get_magic_quotes_gpc()) {
			$str    = str_replace("\0", "\\0", $str);
			$str    = str_replace("\t", "\\t", $str);
			$str    = str_replace("\r", "\\r", $str);
			$str    = str_replace("\b", "\\b", $str);
			$str    = str_replace("\\", "\\\\",$str);   //\
			$str    = str_replace("'", "\\'", $str);    //'
			$str    = str_replace("\"", "\\\"", $str);  //"
			$str    = str_replace("\n", "\\n", $str);   //\n
		}
		//使用Like时才屏蔽通配付
		if($like) {
			$str    = str_replace("%", "\\%", $str);
			$str    = str_replace("_", "\\_", $str);
		}
		return $str;
	}

	function InitGP($keys,$method='GP',$to='GLOBALS',$htmcv=0){
		!is_array($keys) && $keys = array($keys);
		$to = @(string)$to;

		$tmp    = array();
		foreach($keys as $val){
			$tmp[$val] = NULL;
			if($method!='P' && isset($_GET[$val])){
				$tmp[$val] = $_GET[$val];
			} elseif($method!='G' && isset($_POST[$val])){
				$tmp[$val] = $_POST[$val];
			}
			$htmcv && $$to[$val] = Char_cv($$to[$val]);
		}
		if($to != 'GLOBALS') {
			// 把变量解到指定的数组中去
			$GLOBALS[$to]   = $tmp;
		} else {
			// 把变量解到全局变量中去
			foreach($tmp as $k=>$v) $GLOBALS[$k] = $v;
		}
	}
	function GetGP($key,$method='GP'){
		if($method=='G' || $method!='P' && isset($_GET[$key])){
			return @$_GET[$key];
		}
		return @$_POST[$key];
	}


	/**
	 * @description
	 *  重定向函数
	 * @author
	 *  hotgun@163.com
	 *
	 * @param $location 定向地址
	 * @return  null
	 */
	function redirect($location, $force=0) {
		//if(DEBUG_FLAG && $force==0){
		if(0){
			debug("This page is about to redirect to <B><u>{$location}</u></B>. <a href=\"$location\">Hit me to go to</a>");
		} else {
			if(headers_sent()) {
				echo "<script language=\"JavaScript\">\n".
				"<!--\n".
				"window.location.href = '$location';\n".
				"//-->\n".
				"</script>";
				exit();
			}
			header("Location: $location");
			exit();
		}
		exit();
	}


	/**
	 * @description
	 *  给变量的引号转义
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $arr need addslashes array
	 * @return 已转义变量
	 */
	function & exe_addslashes(&$var, $force=0) {
		if(!get_magic_quotes_gpc() || $force) {
			if(is_array($var)) {
				foreach($var as $key => $val)
					$var[$key] = exe_addslashes($val);
			} else {
				$var = addslashes($var);
			}
		}
		return $var;
	}

	/**
	 * 将转义的字符转换回来
	 *
	 * @param $var
	 * @return 原始状态
	 */
	function & exe_stripslashes(&$var, $force=0) {
		if(!get_magic_quotes_gpc() || $force) {
			if(is_array($var)) {
				foreach($var as $key => $val)
					$var[$key] = exe_stripslashes($val);
			} else {
				$var = stripslashes($var);
			}
		}
		return $var;
	}
	/**
	 * @description
	 *  类似 htmlspecialchars
	 *  函数明文返回转义后的字符串，其实不需要通过如下方式复制
	 *  $mystring   = exe_htmlspecialchars($mystring);
	 *  只需如此即可  exe_htmlspecialchars($mystring);
	 *  这里的返回一个值是为了某些时候方便才这样做的
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  &$string    需要转换的字符串的引用，这里这样做尽量减少系统开销
	 * @return  (string)转换后的字符串
	 */
	function & exe_htmlspecialchars(&$string) {
		if(is_array($string)) {
			foreach($string as $key => $val)
				$string[$key] = exe_htmlspecialchars($val);
		} else {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', 
						'&\\1',
						str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
		}
		return $string;
	}


	/**
	 * 输入框预处理，
	 * 因为我们数据库中存放的都是正常的引号
	 * 
	 *
	 */
	function & string_pre_process(&$var, $include = array(), $except = array(), $from = array('&', "\"", "'", '<', '>'), $to = array('&amp;', '&#34;', '&#39;', '&lt;', '&gt;')) {
		if(is_array($var)) {
			foreach($var as $key => $val) {
				if(!empty($include) && is_array($include)) {
					// 如果没有制定$include
					$tmp    = $include;
					$find1  = true;
					$find2  = false;
				} else {
					$tmp    = $except;
					$find1  = false;
					$find2  = true;
				}

				$find   = $find1;
				foreach($tmp as $v) {
					if($key == $v) {
						$find   = $find2;
						break;
					}
				}
				if(!$find){ 
					$var[$key] = string_pre_process($val, $include, $except, $from, $to);
				}
			}
		} else {
			$var = str_replace($from, $to, $var);
		}
		return $var;
	}




	/**
	 * @description
	 *  生成分页列表字符串
	 * @author
	 *  hotgun@163.com
	 *
	 * @param:
	 *  $file           链接的文件名
	 *  $page_total     总共分多少页
	 *  $page_list_tpl  模板数组，为false就用类 page.class.php 中默认的模板
	 *  $param          出了当前url以外的附加参数
	 *  $page_var       当前页的参数名，默认为“p”
	 * @return
	 *  (string)分页的页数列表
	 */
	function get_page_nav($page_total, $file='_self', $page_list_tpl=false, $param=array(), $page_var='page') {
		global $G_abs_includes;
		require_once('page.class.php');
		$split_page = new show_page;
		if($file == '_self') $file = $_SERVER['PHP_SELF'];
		$split_page->file=$file;
		$split_page->pvar=$page_var;



		if(is_string($page_list_tpl)) {
			$split_page->set_tpl($split_page->$page_list_tpl);
		} elseif(is_array($page_list_tpl)) {
			$split_page->set_tpl($page_list_tpl);
		} elseif($page_list_tpl == false) {
			// 如果没有显式设置模板，这里放默认模板
			$split_page->set_tpl($split_page->page_list_tpl);
		}

		$split_page->setvar($param);
		$split_page->set($page_total);
		$dsp_PageNumberList = $split_page->output(1);
		return $dsp_PageNumberList;
	}

	/**
	 * @description
	 * 读取文件内容
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  (string)$file       目标文件位置
	 * @return
	 *  (string)目标文件内容
	 */ 
	function read_from_file($file) {
		if(!is_readable($file)) @chmod($file, 755);
		$fp     = @fopen($file, 'r');
		$content= @fread($fp, filesize($file));
		@fclose($fp);
		return $content;
	}
	/**
	 * @description
	 * 写内容到文件
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  (string)$file       目标文件位置
	 *  (string)$content    需要写的内容
	 * @return
	 *  (int)写入的字节数，为0表示该函数执行错误
	 */
	function write_to_file($file ,$content) {
		//$content = stripslashes($content);
		if(!is_writable($file)) @chmod($file, 755);
		$fp     = @fopen($file, 'w');
		$num    = @fwrite($fp, $content);
		@fclose($fp);
		return $num;
	}
	/**
	 * 删除目录树
	 * 万分危险，特别是在windows主机上请小心使用该函数！
	 *
	 */
	function deltree($file) {
	}

	// form 基本操作

	/**
	 * @description
	 *  将数组内的值用逗号隔开
	 *  该函数的一般用途是，将数组内的值生成用逗号隔开的字符串，供sql中的IN()调用
	 *  类似于php函数中的 join(',',$arr)，比起join array_join_comma能够判断数组项的类型
	 *  下述函数也可以用array_walk + join 实现
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $arr        需要处理的数组
	 *  $value_type (0整数/1字符串)需要处理的是整数还是字符串，字符串将增加单引号“'”
	 *  $join_str   用于分割的字符，默认为逗号“,”
	 * @return
	 *  (string)处理后的字符串
	 */
	function array_join_comma(&$arr, $value_type = 0, $join_str = ','){
		$str    = "";
		foreach($arr as $v) {
			if($value_type) {
				$v  = trim($v);
				$str .= '\'' . $v . $join_str . '\'';
			} else {
				$v  = @(int)$v;
				$str .= $v . $join_str;
			}
		}
		if($str != '') {
			$str = substr($str, 0, strlen($str) - strlen($join_str));
		}
		return $str;
	}

	// 将 22,3,,33,4, 转换成用在sql中用IN的字符串
	function conv_valid_instr($str, $div = ',') {
		$str = str_replace(array("'", "\""), array('', ''), $str);
		$arr = explode($div, $str);
		$instr = '';
		foreach($arr as $v) {
			$v = trim(@(string)$v);
			if($v != '') $instr .= "'$v',";
		}
		if($instr != '') $instr = substr($instr, 0, strlen($instr)-1);
		return $instr;
	}

	function reload_parentframe($msg='') {
		echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>\n";  
		echo "<SCRIPT LANGUAGE=\"JavaScript\" charset=\"utf-8\" type=\"text/javascript\">\n" .
			 "<!--\n";
		if($msg != '') echo "alert('$msg');\n";
		echo "if(parent.frames.length != 0) {parent.frames['main'].document.location.reload();}\n";
		echo "//-->\n" . 
			"</SCRIPT>";
	}

	/**
	 * 适用普通数组
	 * array(
	 *  1 => '数据名称1',
	 *  2 => '数据名称2',
	 * )
	 *
	 *
	 */
	function get_option_str($sid=0, $arr=array(), $opt=1) {
		$str    = '';
		if($opt == 1) {
			foreach($arr as $k => $v) {
				$selected   = $sid == $k ? ' selected':'';
				$str       .= "<option value=\"$k\"$selected>$v</option>";
			}
		} else {
			$str    = $arr[$sid];
		}
		return $str;
	}

	/**
	 * 适用于此类数组
	 * array(
	 *  array(
	 *     id => 1,
	 *     name => '数据名称1'
	 *   ),
	 *  array(
	 *     id => 2,
	 *     name => '数据名称2'
	 *   ),
	 * )
	 */
	function get_option_str1($arr=array(), $id=0, $key = 'id', $value = 'name') {
		$str    = '';
		foreach($arr as $k => $v) {
			$selected = $v[$key] == $id ? ' selected' : '';
			$str   .= "<option value=\"" . $v[$key] . "\" $selected>" . $v[$value] . "</option>";
		}
		return $str;
	}

	function get_radio_str($sid=0, $arr=array(), $input_name, $opt=1) {
		$str    = '';
		if($opt == 1) {
			foreach($arr as $k => $v) {
				$selected   = $sid == $k ? ' checked':'';
				$str       .= "<INPUT TYPE=\"radio\" NAME=\"$input_name\" ID=\"{$input_name}_$k\" VALUE=\"$k\"$selected> <label for=\"{$input_name}_$k\">$v</label> ";
			}
		} else {
			$str    = $arr[$sid];
		}
		return $str;
	}
	function get_checkbox_str($sid=0, $arr=array(), $input_name, $opt=1) {
		$str    = '';
		if($opt == 1) {
			foreach($arr as $k => $v) {
				$k  = @(int)$k;
				$selected   = ($sid&$k)==$k ? ' checked':'';
				$str       .= "<INPUT TYPE=\"checkbox\" NAME=\"{$input_name}[]\" ID=\"{$input_name}_$k\" VALUE=\"$k\"$selected> <label for=\"{$input_name}_$k\">$v</label> ";
			}
		} else {
			$str    = $arr[$sid];
		}
		return $str;
	}



	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
	// cookie 操作
	function Cookie($ck_Var,$ck_Value,$ck_Time='F',$p=true,$ck_Httponly=true){
		global $db_ckdomain,$timestamp;
		if (!$_SERVER['REQUEST_URI'] || ($https = @parse_url($_SERVER['REQUEST_URI']))===false) {
			$https = array();
		}
		if ((empty($https['scheme']) && 
				(
					array_key_exists('HTTP_SCHEME', $_SERVER) && 
					$_SERVER['HTTP_SCHEME']=='https' 
				|| 
					array_key_exists('HTTPS', $_SERVER) && 
					$_SERVER['HTTPS'] && 
					strtolower($_SERVER['HTTPS'])!='off'
				)
			) 
		  || 
			array_key_exists('scheme', $https) && 
			$https['scheme']=='https') {
			$ck_Secure = true;
		} else {
			$ck_Secure = false;
		}

		if (0 && 'P_W'!='admincp') {
			$ckpath = !$db_ckpath ? '/' : $db_ckpath;
			$ckdomain = $db_ckdomain;
		} else {
			$ckpath = '/';
			$ckdomain = '';
		}
		$p && $ck_Var = CookiePre().'_'.$ck_Var;
		if ($ck_Time=='F') {
			$ck_Time = $timestamp+31536000;
		} elseif ($ck_Value=='' && $ck_Time==0) {
			return setcookie($ck_Var,'',$timestamp-31536000,$ckpath,$ckdomain,$ck_Secure);
		}
		if (PHP_VERSION>='5.2.0') {
			return setcookie($ck_Var,$ck_Value,$ck_Time,$ckpath,$ckdomain,$ck_Secure,$ck_Httponly);
		} else {
			return setcookie($ck_Var,$ck_Value,$ck_Time,$ckpath.($ck_Httponly ? '; HttpOnly' : ''),$ckdomain,$ck_Secure);
		}
	}

	/**
	 * @description
	 *  给Cookie字符串生成加密前缀
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  null
	 * @return
	 *  (string)加密后的字符串
	 */
	function CookiePre(){
		global $G_hash;
		return substr(md5($G_hash),0,10);
	}

	/**
	 * @description
	 *  在Cookie中取得相关变量的值
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $var    变量名
	 * @return
	 *  (string)
	 */
	function GetCookie($var){
		return $_COOKIE[CookiePre().'_'.$var];
	}


	/**
	 * @description
	 * 一个简单的换位加/解密函数
	 *
	 * @author
	 *  hotgun@163.com
	 * 
	 * 
	 */
	function shift_code($str){
		//ABCDEFGHIJKLM
		//NOPQRSTUVWXYZ

		$newString = "";

		for ($i = 0; $i < strlen($str); $i++) {

			$temp = substr($str, $i, 1);
			$asc = ord($temp);

			$newChar = $temp;

			if (($asc >= 65) && ($asc <= 90)) {
				if ($asc <= 65 + 12) $newChar = chr($asc + 13);
				if ($asc >= 65 + 13) $newChar = chr($asc - 13);
			}
			
			if (($asc >= 97) && ($asc <= 122)) {
				if ($asc <= 97 + 12) $newChar = chr($asc + 13);
				if ($asc >= 97 + 13) $newChar = chr($asc - 13);
			}

			$newString = $newString . $newChar;
		}

		return $newString;
	}

	/**
	 * @description
	 *  字符串加密/解密函数
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $string 需要加密/解密的函数
	 * @return
	 *  (string)
	 */
	function strcode($string,$action='ENCODE'){
		global $G_hash;
		//if(isset($G_hash)) 
		$key    = substr(md5($_SERVER["HTTP_USER_AGENT"] . $G_hash ),8,18);
		$action == 'DECODE' && $string = base64_decode($string);
		$len    = strlen($key); $code = '';
		for ($i=0; $i<strlen($string); $i++) {
			$k      = $i % $len;
			$code  .= $string[$i] ^ $key[$k];
		}
		$action == 'ENCODE' && $code = base64_encode($code);
		return $code;
	}

	/**
	 * @description
	 *  产生一个指定长度的随机字符串,并返回
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $len    产生字符串的位数
	 *  $use_sp 是否使用特殊字符
	 * @return
	 *  (string)随机字符串
	 */
	function randstr($len=6, $use_sp=1) {
		/**
		 * \/:*?"<>| 不产生这些字符 不能作为文件名
		 * & 不能作为url参数
		 * ' 也不作为可选的字符
		 */
		$chars      ='0123456789';
		$sp_chars   ='`~!@#$%^&()_+-=,;,.{}[]';

		if($use_sp) $chars     .=$sp_chars;
		
		// characters to build the password from
		// seed the random number generater (must be done)
//		mt_srand((double)microtime()*1000000*getmypid());
		$password='';
		while(strlen($password)<$len) {
			$password.=substr($chars,(mt_rand()%strlen($chars)),1);
		}
		return $password;
	}

	/**
	 * @description
	 * 得到验证码的图片url
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $width(int)     宽
	 *  $height(int)    高
	 *  $length(int)    字符长度
	 * @return
	 *  (string)验证码图片的url
	 */
	function get_validate_code($length=6) {
	//    global $G_web_root, $G_web_common, $G_admin_session_key, $G_front_session_key;
//		$G_admin_session_key = "admin";
		$code = $this->randstr($length, 0);
//		@session_start();
		

//		if(defined('IN_ADMIN') && IN_ADMIN) {
//			$_SESSION[$G_admin_session_key]['validate_code'] = $code;
//		} else {
///			$_SESSION[$G_front_session_key]['validate_code'] = $code;
//		}
		//$code = $this->strcode($code . '|' . $width . '|' . $height );
		// 好像加密后的等号没用
		// 删除
//		while(0 != strlen($code)) {
//			if(substr($code, -1) == '=') $code = substr($code, 0, strlen($code)-1);
//			else break;
//		}

//		return 'code_img/' . $code;
		return $code;
	}

	/**
	 * @description
	 * 得到当前页面的 url
	 * @author
	 *  hotgun@163.com
	 *
	 * @param
	 *  $except(array)  生成的url字符串中，url参数将除去数组中$except的值
	 * @return
	 *  (string)当前页面的url
	 */
	function get_current_url($except = null) {
		if($_SERVER['QUERY_STRING'] == '') {
			return $_SERVER['PHP_SELF'];
		} else {
			if(!$except) $except = array();
			// 分割$_SERVER['PHP_SELF']，去掉“$except”中排除的变量
			$query_param    = explode('&', $_SERVER['QUERY_STRING']);
			$query  = '';
			foreach($query_param as $v) {
				$value = explode('=', $v);
				$exist = false;
				foreach($except as $vv) {
					if($value[0]==$vv) {
						$exist = true;
						break;
					}
				}
				if(!$exist) {
					$query .= ($query=='')?"{$v}":"&{$v}";
				}
			}
		}
		return $_SERVER['PHP_SELF'] . '?' . $query;
	}

	function getcolor($color)
	{
		 global $image;
		 $color = eregi_replace ("^#","",$color);
		 $r = $color[0].$color[1];
		 $r = hexdec ($r);
		 $b = $color[2].$color[3];
		 $b = hexdec ($b);
		 $g = $color[4].$color[5];
		 $g = hexdec ($g);
		 $color = imagecolorallocate ($image, $r, $b, $g); 
		 return $color;	
	}
	function setnoise()
	{
		global $image, $w, $h, $back, $noisenum;

		for ($i=0; $i<$noisenum; $i++)
		{
			$randColor = imageColorAllocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
			imageSetPixel($image, rand(0, $w), rand(0, $h), $randColor);
		} 
	}

	function setline()
	{
		global $image, $w, $h, $back, $linenum;
		global $text_left_padding, $text_top_padding;

		$x_rang = $w + $text_left_padding;
		$y_rang = $h + $text_top_padding;

		for ($i=0; $i<$linenum; $i++)
		{
			$x1 = rand(0, $x_rang);
			$y1 = rand(0, $y_rang);
			$x2 = rand(0, $x_rang);
			$y2 = rand(0, $y_rang);

			$line_color = imagecolorallocate($image, rand(80, 120), rand(80, 120), rand(80, 120));

			imageline($image, $x1, $y1, $x2, $y2, $line_color);
			//imageline($image, $x1, $y1+1, $x2, $y2+1, $line_color);
		}
	}
}

?>
