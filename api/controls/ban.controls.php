<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class ban extends all
{

	public $check=0;

	//获取图片列表
	public function listAction()
	{
		$check=$this->fill(array(
				array('type',1,'int'),
			));

		$a=$this->models->getList($check);

		$this->success($a);

	}



}
?>