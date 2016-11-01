<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class activity extends all
{

	public $check=0;

	//获取活动列表
	public function listAction()
	{

		// 验证输入的数据
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		// 运行当前模版getList
		$a=$this->models->getList($check);

		// 返回结果并输出
		$this->success($a);

	}


	//获取活动详情
	public function infoAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->getInfo($check);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg('huodongMiss');
		}


	}

}
?>