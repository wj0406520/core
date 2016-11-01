<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class shop extends all
{

	public $check=0;

	//获取服务列表
	public function listAction()
	{


		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		$get=$this->check(array('search'));

		$check=array_merge($get,$check);

		$a=$this->models->getList($check);

		$this->success($a);

	}

	//获取服务类型
	public function typeAction()
	{


		$check=$this->fill(array(
				array('type',0,'int'),
			));

		$a=$this->models->getType($check);

		$this->success($a);

	}


	//获取服务详情
	public function infoAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
				array('token','','string')
			));


		$a=$this->models('all')->checkToken($check['token']);
		$this->models();

		if($check['token'] && !$a){
			$this->errorMsg('tokenMiss');
		}

		$check['uid']=$a;


		$a=$this->models->getInfo($check);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg('shopMiss');
		}


	}


}
?>