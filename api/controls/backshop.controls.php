<?php

/*
退货
 */

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class backshop extends all
{

	public $check=1;

	//退货
	public function indexAction()
	{


		$check=$this->fill(array(
				array('typeid',0,'int'),
				array('shoptype',0,'int'),
				array('shopid',0,'int'),
				array('reason','','string'),
				array('explain','','string'),
				array('img1','','string'),
				array('img2','','string'),
				array('img3','','string'),
			));

// id
// type
// data[0][id]
// data[0][type]
		// $check['sun']=0;
		$check['userid']=$this->userId;
		$check['type']=1;


		// $check=array_merge($check,$_POST);
		// if($check['type']==1){
		// 	if(isset($_POST['data'])){
		// 		$check['sun']=1;
		// 		if(!is_array($_POST['data'])){
		// 			$this->errorMsg('backshopError');
		// 		};
		// 		foreach ($_POST['data'] as $value) {
		// 			if(!isset($value['id']) || !isset($value['type']) || empty($value['id']) || empty($value['type'])){
		// 				$this->errorMsg('backshopError');
		// 			}
		// 		}
		// 	}
		// }
		// data[0][type]
		// data[0][id]
		$a=$this->models->backshop($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}

	}

	//活动和预约退款
	public function hdyyAction()
	{

		$check=$this->fill(array(
				array('type',0,'int'),
				array('typeid',0,'int'),
			));
		$check['userid']=$this->userId;

		if($check['type']==1){
			$this->errorMsg('typeError');
			return false;
		}
		$a=$this->models->backshop($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	//退货成功
	public function backsuccAction()
	{
		$check=$this->fill(array(
				array('typeid',0,'int'),
				array('shoptype',0,'int'),
				array('shopid',0,'int'),
			));
		$check['userid']=$this->userId;
		$check['type']=1;
		$this->models->backsucc($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	//取消退货
	public function backcancelAction()
	{

		$check=$this->fill(array(
				array('typeid',0,'int'),
				array('shoptype',0,'int'),
				array('shopid',0,'int'),
			));
		$check['type']=1;

		$a=$this->models->backcancel($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

}
?>