<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class cart extends all
{

	//加入购物车
	public function indexAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
				array('type',0,'int'),
				array('num',1,'int'),
			));


		$a=$this->models->addCart($check,$this->userId);

		if($a){
			$this->success();
		}else{
			$this->errorMsg('addCartError');
		}

	}

	//删除购物车
	public function deleteAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->create(array('id'=>$check['id'],'state'=>0));
		if($a){
			$this->success();
		}else{
			$this->errorMsg('deleteCartError');
		}
	}

	// 购物车列表
	public function listAction()
	{

		$a=$this->models->getlist($this->userId);

		$this->success($a);

	}
}
?>