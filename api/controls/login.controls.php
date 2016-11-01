<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class login extends all
{

	public $check=0;

	//登录接口
	public function postAction()
	{

		$post=$this->validate(array(
				'phone'=>array('phone','phone'),
              	'password'=>array('password','length','6,16'),
			));

		$a=$this->models->checkPsw($post['phone'],$post['password']);

		if(!$a){
			$this->errorMsg('login');
		}

		$token=$a['id'].'|'.token();

		$this->models->create(array('token'=>$token,'id'=>$a['id']));

		$arr['token']=$token;
		$arr['is_fenxiao']=$a['is_fenxiao'];

		$this->success($arr);

	}

	//注册接口
	public function registerAction()
	{

		$post=$this->validate(array(
				'phone'=>array('phone','phone'),
              	'password'=>array('password','length','6,16'),
              	'code'=>array('code','length','6,6')
			));

		$a=$this->models->checkUser($post['phone']);

		if($a){
			$this->errorMsg('havuse');
		}

		$phone=$this->models('index');
        $a=$phone->checkphone($post['phone'],$post['code']);
        $this->models();


		$this->models->create(array('telphone'=>$post['phone'],'pass'=>md5($post['password'])));

		$a=$this->models->insert_id();

		$token=$a.'|'.token();

		$this->models->create(array('token'=>$token,'id'=>$a));

		$arr['token']=$token;

		$this->success($arr);
	}

	//忘记密码
	public function forgetAction()
	{

		$post=$this->validate(array(
				'phone'=>array('phone','phone'),
              	'password'=>array('password','length','6,16'),
              	'code'=>array('code','length','6,6')
			));

		$a=$this->models->checkUser($post['phone']);

		$uid=$a;

		if(!$a){
			$this->errorMsg('usermiss');
		}

		$phone=$this->models('index');
        $a=$phone->checkphone($post['phone'],$post['code']);
        $this->models();


		$this->models->create(array('id'=>$uid,'telphone'=>$post['phone'],'pass'=>md5($post['password'])));


		$this->success();
	}


}
?>