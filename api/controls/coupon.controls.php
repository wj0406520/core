<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class coupon extends all
{

	public $check=1;

	//添加优惠券
	public function addAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$check['uid']=$this->userId;

		$a=$this->models->addCoupon($check);

		if(!$a){
			$this->errorMsg('coupon_add_error');
		}

		$this->success();

	}

	//获取优惠券列表
	public function listAction()
	{


		$a=$this->models->getlist($this->userId);


		$this->success($a);

	}


	//获取服务详情
	public function shopListAction()
	{


		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->shopList($check,$this->userId);


		if($a){
			$this->success($a);
		}else{
			if(is_array($a)){
				$this->success($a);
			}
			$this->errorMsg($this->models->error);
		}



	}


}
?>