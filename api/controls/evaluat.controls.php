<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class evaluat extends all
{


	//获取评价列表
	public function listAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->getList($check,$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}

	}


	//评价
	public function postAction()
	{

		if(isset($_POST['data']) && !is_array($_POST['data'])){
			$this->errorMsg('evaluatError');
		};

		$check=$this->fill(array(
				array('orderid',0,'int'),
			));


		foreach ($_POST['data'] as $value) {
			if(!isset($value['id']) || !isset($value['type']) || empty($value['id']) || empty($value['type'])){
				$this->errorMsg('evaluatError');
			}
		}

		$a=$this->models->evaluat($_POST['data'],$check,$this->userId);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}


	}

	// 获取全部评价
	public function shoplistAction()
	{

		$check=$this->fill(array(
				array('type',0,'int'),
				array('id',0,'int'),
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		$a=$this->models->getEvaluat($check);

		$this->success($a);
	}

}
?>