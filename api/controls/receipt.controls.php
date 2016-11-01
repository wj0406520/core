<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class receipt extends all
{


	// 确认收货
	public function indexAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$check['statue']=4;

		$a=$this->models->post($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}


	}


}
?>