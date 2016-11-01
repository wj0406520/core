<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class other extends all
{

	public $check=0;
	//获取订单信息
	public function listnewsAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));


		$a=$this->models->news($check);

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}


	}

	// 公益列表
	public function listfreeAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		$a=$this->models->free($check);

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}

	}

	// 投资列表
	public function listfinaAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
				array('hyly',0,'int'),
				array('rzpc',0,'int'),
				array('szdq',0,'int'),
			));

		$a=$this->models->fina($check);

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}

	}


}
?>