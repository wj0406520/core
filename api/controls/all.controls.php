<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class all extends core\controls
{

	// 用户登录判断
    public function before()
    {

    	if($this->check==1){

			$post=$this->validate(array(
				'token'=>array('token','length','32,50'),
			));

			$a=$this->models('all')->checkToken($post['token']);
			$this->models();
			if($a){
				$this->userId=$a;
			}else{
				$this->errorMsg('tokenMiss');
			}
    	}
    }
}
?>