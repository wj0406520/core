<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class user extends all
{


	//获取用户信息
	public function infoAction()
	{

		$a=$this->models->getInfo($this->userId);

		$this->success($a);

	}

	//更新用户信息
	public function updateAction()
	{


		$check=$this->checkSearch(array(
				'gxqm'=>'',
				'grsm'=>'',
				'wx'=>'',
				'face'=>'',
				'xingbie'=>'',
				'email'=>'',
				'gzdw'=>'',
				'lc'=>'',
				'fjh'=>'',
				'ygh'=>'',
				'UserName'=>'',
			));
		$post=array();
		if(isset($check['UserName'])){
			$post=$this->validate(array(
					'UserName'=>array('username','length','2,6'),
				));
		}
		$change=array_merge($post,$check);

		$face=\tool\UploadTool::connect('face');
		if($face===true){
			$face='';
		}elseif($face===false){
			$this->errorMsg(\tool\UploadTool::$error);
		}else{
			$change['face']=$face;
		}
		$change['id']=$this->userId;

		$this->models->create($change);

		$this->success();
	}

	//意见反馈
	public function feedbackAction()
	{

		$check=$this->checkSearch(array(
				'msg'=>'',
				'contact'=>''
			));
		$check['userid']=$this->userId;
		$check['createtime']=time();
		$this->models()->table('yjfk')->create($check);
		$this->success();
	}
	//用户预约列表
	public function bespeakAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));

		$check['userid']=$this->userId;
		$a=$this->models->bespeak($check);
		$this->success($a);
	}
	//用户活动列表
	public function huodongAction()
	{

		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
		$a=$this->models->huodong($check);
		$this->success($a);
	}

	//用户活动详情
	public function hdinfoAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$check['userid']=$this->userId;

		$a=$this->models->hdinfo($check);
		$this->success($a);
	}
	//用户预约详情
	public function bsinfoAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$check['userid']=$this->userId;

		$a=$this->models->bsinfo($check);
		$this->success($a);
	}

	//身份验证
	public function cardAction()
	{
			$post=$this->validate(array(
					'name'=>array('username','length','2,6'),
					'code'=>array('card','card')
				));

		$check['userid']=$this->userId;

		$change=array_merge($post,$check);

		$face=\tool\UploadTool::connect('img');
		if($face===true){
			$this->errorMsg('fileNo');
		}elseif($face===false){
			$this->errorMsg(\tool\UploadTool::$error);
		}else{
			$change['img']=$face;
		}
		$a=$this->models->card($change);
		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}
}
?>