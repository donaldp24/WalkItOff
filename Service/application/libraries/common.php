<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require ('json.inc.php');
require ('json.class.php');

class Common {

	public function han_cut($val,$cut_len)
	{ 
		$i = 0;
		$byteLen = strlen($val);
		$strLen = 0;

		while ($i < $byteLen && $strLen < $cut_len)
		{
			if (ord($val[$i]) > 0xE0)
				$i += 3;
			else
				$i ++;

			$strLen ++;
		}

		$retStr = substr($val, 0, $i);

		if ($i < $byteLen && $strLen >= $cut_len)
			$retStr .= "...";

		return $retStr;
	}

	public function get_json_data($data)
	{
		$json = new JSON();
		return $json->encode($data);
	}

	public function php_debug($obj) {
		$php_log_file = "c:/debug.log";

		$fp = fopen($php_log_file, "a+");
		fputs($fp, print_r($obj, true)."\n");
		fclose($fp);
	}

	/* Convert chinese to utf8 encoding */
	public function convertUTF8($target)
	{
		if (is_array($target)) {
			foreach($target as $key => $item) {
				if (is_string($item)) {
//					if($key == "name") {
//						print_r(mb_detect_encoding($item, "UTF-8, gb2312", "UTF-8") . " $item\r\n");
//						print_r(@iconv('EUC-CN', 'utf-8//IGNORE', $item));
//					}
					if(mb_detect_encoding($item, "UTF-8, gb2312", "UTF-8") != 'UTF-8'){
						$target[$key] = @iconv('EUC-CN', 'utf-8//IGNORE', $item);
					}
				} else if (is_array($item)) {
					$target[$key] = $this->convertUTF8($item);
				}
			}
		} else if (is_string($target)) {
			if(mb_detect_encoding($target, "UTF-8, gb2312", "UTF-8") != 'UTF-8'){
				$target = @iconv('EUC-CN', 'utf-8//IGNORE', $target);
			}
		}

		return $target;
	}

	/* Convert utf8 to chinese encoding */
	public function convertChn($target)
	{
		if (is_array($target)) {
			foreach($target as $key => $item) {
				if (is_string($item)) {
					$target[$key] = iconv('utf-8', 'gb2312//IGNORE', $item);
				} else if (is_array($item)) {
					$target[$key] = $this->convertChn($item);
				}
			}
		} else if (is_string($target)) {
			$target = iconv('utf-8', 'gb2312', $target);
		}

		return $target;
	}

	function getLocaleDateTime($type)
	{
		$retStr = "";

		date_default_timezone_set("Asia/Shanghai");

		switch ($type)
		{
			case 0: // 2012
				$retStr = strftime("%Y");
				break;
			case 1: // 2012-04
				$retStr = strftime("%Y-%m");
				break;
			case 2: // 2012-04-08
				$retStr = strftime("%Y-%m-%d");
				break;
			case 3: // 2012-04-08 23
				$retStr = strftime("%Y-%m-%d %H");
				break;
			case 4: // 2012-04-08 23:41
				$retStr = strftime("%Y-%m-%d %H:%M");
				break;
			case 5: // 2012-04-08 23:41:10
				$retStr = strftime("%Y-%m-%d %H:%M:%S");
				break;
			case 6: // 04
				$retStr = strftime("%m");
				break;
			case 7: // 08
				$retStr = strftime("%d");
				break;
			case 8:
				$retStr = strftime("%Y.%m.%d(%Hh-%Mm-%Ss)");				
				break;
			case 9:
				$retStr = strftime("%H:%M:%S");				
				break;
			case 10:
				$retStr = strftime("%Y%m%d%H%M%S");				
				break;
		}

		return $retStr;
	}

	function ms_escape_string($data) {

        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
//        $data = str_replace('"', '""', $data );
        return $data;
    }
}

/* End of file Someclass.php */
?>