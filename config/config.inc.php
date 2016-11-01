<?php
/**
 * author 王杰
 * time 2016-11-01
 * version 3.0.1
 * 配置文件
 * config.inc.php
 */

defined('ACC')||exit('ACC Denied');

return array(

		'host' => '127.0.0.1',  //数据库地址

		'user' => 'root',       // 数据库帐号

		'pwd' =>'root',    		// 数据库密码

		'db' =>'hanke',			//数据库表名

		'char' => 'utf8',		// 数据库字符集

		'pref' => 't_',			// 数据库表前缀


		//短信接口相关配置
		'account'=>'jksc481',							//帐号

		'password'=>'ab123456',							//密码

		'contentleft'=>'尊敬的客户，您的验证码是：',	//内容  验证码左边

		'contentright'=>'【瀚客网】',					//内容  验证码右边

		'url'=>'http://sh2.ipyy.com/smsJson.aspx',		//验证码地址

		'selfurl'=>'http://www.firstsee.top',			//个人网站主页

	);