<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class distribut extends all
{


	public $check=1;

	//成为分销商
	public function becomeAction()
	{

		$check=$this->checkSearch(array(
				'yeji'=>'',
				'lvli'=>'',
			));

		$sfzm=\tool\UploadTool::connect('sfzm');

		$this->checkImg($sfzm);
		$sffm=\tool\UploadTool::connect('sffm');
		$this->checkImg($sffm);
		$scsf=\tool\UploadTool::connect('scsf');
		$this->checkImg($scsf);
		$yyzz=\tool\UploadTool::connect('yyzz');
		$check['sfzm']=$sfzm;
		$check['sffm']=$sffm;
		$check['scsf']=$scsf;
		$check['yyzz']=$yyzz;


		$check['createtime']=time();

		$check['userid']=$this->userId;

		$a=$this->models->become($check);

		if(!$a){
			$this->errorMsg($this->models->error);
		}
		$this->success();

	}

	//分销商列表
	public function shoplistAction()
	{
		$a=$this->models->shoplist();

		$this->success($a);
	}

	//分销商类型列表
	public function typelistAction()
	{
		$check=$this->fill(array(
				array('shopid',0,'int'),
			));
		$a=$this->models->typelist($check);

		$this->success($a);
	}

	//分销商列表
	public function classifyAction()
	{
		$check=$this->fill(array(
				array('pid',0,'int'),
				array('shopid',0,'int'),
				array('classify',0,'int')
			));
		$a=$this->models->classify($check);

		$this->success($a);
	}

	// 成为下级分销商
	public function lowerAction()
	{
		// $a=\tool\CardTool::checkIdCard('341124199404067217');
		// var_dump($a);
		// exit;
		$check=$this->fill(array(
				array('id',0,'int'),
				array('classify',0,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->lower($check);
		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 分销商  个人分销列表
	public function splistAction()
	{
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->splist($check);
		$this->success($a);
	}

	// 下级会员列表
	public function juniorAction()
	{
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->junior($check);
		$this->success($a);
	}

	// 分销订单列表
	public function orderAction()
	{
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->orderList($check);
		$this->success($a);
	}

	// 分销订单详情
	public function infoAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->orderInfo($check);
		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 下级会员详情
	public function userinfoAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->userinfo($check);
		if($a){
			$this->success($a);
		}else{
			$this->errorMsg($this->models->error);
		}
	}

	// 商家分类
	public function typeAction()
	{
		$check['userid']=$this->userId;
		$a=$this->models->type($check);

		$this->success($a);

	}

	// 我的佣金
	public function moneyAction()
	{
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->money($check);
		$this->success($a);
	}

	// 验证上传图片
	public function checkImg($img)
	{
		if($img===true){
			$this->errorMsg('fileFail');
		}elseif($img===false){
			$this->errorMsg(\tool\UploadTool::$error);
		}

	}


}
?>