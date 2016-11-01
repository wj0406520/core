<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class money extends all
{

	public $check=1;

	//查询余额
	public function indexAction()
	{

		$uid=$this->userId;

		$a=$this->models('user')->field('money')->find($uid);

		$this->success($a);

	}

	//添加银行卡
	public function addbankAction()
	{
        $check=$this->validate(array(
              'bankcard'=>array('bankCardError','length','10,100'),
              'bankphone'=>array('phone','phone'),
              'bankusername'=>array('name','length','2,6'),
              'bankname'=>array('bankCardError','length','2,20'),
              'code'=>array('code','length','6,6')
            ));
		$img=$this->checkSearch(array(
				'bankimg'=>'',
			));

		$check=array_merge($img,$check);
        // $phone=new \api\models\index();
        $phone=$this->models('index');
        $a=$phone->checkphone($check['bankphone'],$check['code']);
        $this->models();

        // $bankname=$this->models->get_card_name($check['bankcard']);
        // if(!$bankname){
        // 	$this->errorMsg('bankCardError');
        // }
        // $check['bankname']=$bankname['0']['bank_name'];

        $check['userid']=$this->userId;

		$a=$this->models->addbank($check);
		if($a){
			$this->success();
		}else{
        	$this->errorMsg($this->models->error);
		}


	}
	//搜索银行
	public function searchbankAction()
	{

        $check=$this->validate(array(
              'card'=>array('bankCardError','length','10,100')
            ));

		$a=$this->models->get_card_name($check['card']);

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}

	}


	//提现接口
	public function takenAction()
	{

		$check=$this->fill(array(
				array('bankid',0,'int'),
				array('money',0,'double'),
			));
		if($check['money']<100){
			$this->errorMsg('takenmoenyerror');
		}

        $check['userid']=$this->userId;

        $a=$this->models->tokenmoney($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg($this->models->error);
		}
	}


	//银行列表
	public function listbankAction()
	{

        $check['userid']=$this->userId;
        $a=$this->models->getList($check);
        $this->success($a);
	}

	// 删除银行卡
	public function deleteAction()
	{
		$check=$this->fill(array(
				array('id',0,'int'),
			));
		$check['userid']=$this->userId;

		$a=$this->models->deleteBank($check);

		if($a){
			$this->success();
		}else{
			$this->errorMsg('deleteBankError');
		}
	}

	// 账单明细
	public function detailAction()
	{
		$check=$this->fill(array(
				array('page',1,'int'),
				array('pagesize',6,'int'),
			));
		$check['userid']=$this->userId;
        $a=$this->models->detail($check);
        $this->success($a);
	}
}
?>