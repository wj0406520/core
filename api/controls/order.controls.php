<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class order extends all
{


	//获取订单信息
	public function listAction()
	{

		$type=isset($_POST['type'])?(is_numeric($_POST['type'])?$_POST['type']:-1):-1;

		$a=$this->models->getList($type,$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}


	}

	// 订单详情
	public function infoAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));

		if(!$check['id']){
			$this->errorMsg('orderMiss');
		}

		$a=$this->models->getinfo($check,$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 删除订单
	public function deleteAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$a=$this->models->delOder($check,$this->userId);
		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

}
?>