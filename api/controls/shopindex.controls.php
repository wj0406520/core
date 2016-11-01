<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class shopindex extends all
{

	public $check=0;

	// 商家主页
	public function indexAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$a=$this->models->getList($check['id']);

		$this->success($a);

	}

	// 分销商主页
	public function fxAction()
	{

		$check=$this->fill(array(
				array('userid',0,'int'),
				array('page',0,'int'),
				array('pagesize',6,'int'),
			));
		$a=$this->models->getFxList($check);

		$this->success($a);

	}


}
?>