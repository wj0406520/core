<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class bespeak extends all
{

	public $check=0;

	//获取预约列表
	public function listAction()
	{


		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));


		$a=$this->models->getList($check);

		$this->success($a);

	}


	//获取预约详情
	public function infoAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->getInfo($check);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg('yuyueMiss');
		}


	}

}
?>