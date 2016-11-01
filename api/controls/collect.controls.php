<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class collect extends all
{


	public $check=1;

	// 加入收藏\删除收藏
	public function addAction()
	{

		$check=$this->fill(array(
				array('spid',0,'int'),
				array('is_delete',0,'int'),
			));
		$vali=$this->validate(
				array('type'=>array('typeError','in','1,2'))
			);
		$check=array_merge($vali,$check);
		$check['userid']=$this->userId;
		$a=$this->models->addCollect($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 获取收藏列表
	public function listAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		$check['userid']=$this->userId;

		$a=$this->models->getList($check);

		$this->success($a);

	}

}
?>