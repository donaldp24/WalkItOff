<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/* add by CSC */
define('AUTHSALT',		'12345678987654321');

/*style constants*/

/*error message constants*/
define('ERROR_MSG_GOODS_ADD_GOODS', '“*”项目的数据不正确，请再输入.');						//产品管理->添加产品
define('ERROR_MSG_GOODS_REG_FIRSTGOODS', '请输入一级分类名称和选择一级分类图片.');			//产品管理->添加一级分类
define('ERROR_MSG_GOODS_REG_SECONDGOODS', '请输入二级分类名称和选择二级分类图片.');			//产品管理->添加二级分类
define('ERROR_MSG_GOODS_MOD_GOODS', '请输入分类名称和选择分类图片.');						//产品管理->修改分类
define('ERROR_MSG_CHANGE_PASSWORD', '密码修改成功了.');                        //产品管理->修改分类


/*messages*/
define('SUCCESS_MSG_DELETE_USERS', '删除成功了.');                        //用户管理->删除
define('ERROR_MSG_DELETE_USERS', '删除失败了，请选择正确用户');                        //用户管理->删除

define('SUCCESS_MSG_DELETE_MEMBERS', '删除成功了.');                        //用户管理->删除
define('ERROR_MSG_DELETE_MEMBERS', '删除失败了，请选择正确用户');                        //用户管理->删除

define('SUCCESS_MSG_INVITE_CODE', '成功生成邀请码.');                        //用户管理->删除
define('ERROR_MSG_INVITE_CODE', '删除失败了，请选择正确用户');                        //用户管理->删除

/* db constants */
define('GOODS_IMAGE_EXHIBITION', 0);
define('GOODS_IMAGE_DETAIL', 1);

define('ORDER_STATUS_WAIT_DELIVER', 0);
define('ORDER_STATUS_ALREADY_DELIVER', 1);
define('ORDER_STATUS_ALREADY_RECEIVE', 2);
define('ORDER_STATUS_ALREADY_CANCEL', 3);

define('MEMBER_LEVEL0', 0);
define('MEMBER_LEVEL1', 1);
define('MEMBER_LEVEL2', 2);
define('MEMBER_LEVEL3', 3);
define('MEMBER_LEVEL4', 4);
define('MEMBER_LEVEL5', 5);


/* sms service information */
define('SMS_SERVER', "http://61.143.160.150:8080/smshttp");
define('SMS_COMP_ID', "326");
define('SMS_USERNAME', "pomp");
define('SMS_PASSWORD', "3fc6");

define('SMS_ACTION_SEND', "sendmsg");


/* image manipulation */
define('GOODS_THUMB_PREFIX',	"goods_thumb_");
define('GOODS_THUMBNAIL_SIZE_X',	300);
define('GOODS_THUMBNAIL_SIZE_Y',	300);

define('GOODS_EXHIBITION_PREFIX',	"goods_exhib_");
define('GOODS_EXHIBITION_SIZE_X',	640);
define('GOODS_EXHIBITION_SIZE_Y',	640);

define('GOODS_DETAIL_PREFIX',	"goods_detail_");
define('GOODS_DETAIL_SIZE_X',	640);
define('GOODS_DETAIL_SIZE_Y',	738);

define('COMPANY_IMAGE_PREFIX',	"comp_img_");
define('COMPANY_IMAGE_SIZE_X',	640);
define('COMPANY_IMAGE_SIZE_Y',	914);

define('BANNER_IMAGE_PREFIX',	"banner_img_");
define('BANNER_IMAGE_SIZE_X',	640);
define('BANNER_IMAGE_SIZE_Y',	260);

define('ONLINESHOP_IMAGE_PREFIX',	"onlineshop_img_");
define('ONLINESHOP_IMAGE_SIZE_X',	140);
define('ONLINESHOP_IMAGE_SIZE_Y',	140);

