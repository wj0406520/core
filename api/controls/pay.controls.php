<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class pay extends all
{

	//生成订单
	public function indexAction()
	{

		$_POST['id'] || $this->errorMsg('cartIdMiss');

		$a=$this->checkArrayId($_POST['id']);

        $a || $this->errorMsg('cartIdError');


		$a=$this->models->generateOrder($_POST['id'],$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg('addCartError');
		}

	}

	// 获取订单详情
	public function infoAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
				array('addressid',0,'int'),
			));

		$a=$this->models->getlist($check,$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 支付完成
	public function payAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
				array('addressid',0,'int'),
				array('paytype',1,'int'),
				array('couponid',0,'int'),
			));

		$a=$this->checkPay($this->userId);

		if(!$a){
			$this->errorMsg('nopay');
		}

		$a=$this->models->pay($check,$this->userId);

		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 微信支付
	public function paywxAction()
	{
		$check=$this->fill(array(
				array('shopid',0,'int'),//商品id
				array('userid',0,'int'),//分销商id
				array('num',0,'int'),//购买数量
				array('addressid',0,'int'),//地址id
				array('paytype',0,'int'),//支付类型
			));

		$a=$this->checkPay($this->userId);

		if(!$a){
			$this->errorMsg('nopay');
		}

		$check['userid']=$this->userId;
		$a=$this->models->paywx($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 预约支付
	public function bespeakAction()
	{

		$post=$this->validate(array(
				'yykssj'=>array('timeError','time'),
				'yyjssj'=>array('timeError','time'),
				'spid'=>array('yuyueMiss','number'),
				'paytype'=>array('paytype','in','1,2,3,4'),
			));

		$a=$this->checkPay($this->userId);

		if(!$a){
			$this->errorMsg('nopay');
		}
		$post['userid']=$this->userId;
		$a=$this->models('bespeak');
		$re=$a->pay($post);
		if($re){
			$this->success($re);
		}else{
			$this->errorMsg($a->error);
		}

	}

	// 活动支付
	public function activityAction()
	{

		$post=$this->validate(array(
				'phone'=>array('phone','phone'),
				'hdid'=>array('huodongMiss','number'),
				'bmrs'=>array('rscw','number'),
				'paytype'=>array('paytype','in','1,2,3,4'),
			));
		$a=$this->checkPay($this->userId);

		if(!$a){
			$this->errorMsg('nopay');
		}
		$post['userid']=$this->userId;
		$a=$this->models('activity');
		$re=$a->pay($post);
		if($re){
			$this->success($re);
		}else{
			$this->errorMsg($a->error);
		}

	}

}
?>