<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class address extends all
{


	//地址列表
	public function listAction()
	{


		$a=$this->models->where(array('userid'=>$this->userId))->select();

		if($a){
			$this->success($a);
		}else{
			$this->success();
		}

	}

	// 新增 修改 地址
	public function createAction()
	{

        $arr=array(
              'name'=>array('username','length','2,6'),
              'phone'=>array('phone','phone'),
            );
		$check=$this->checkSearch(array(
				'sheng'=>'',
				'shi'=>'',
				'xian'=>'',
				'isdefault'=>'',
				'id'=>'',
				'xiangxidizhi'=>'',
			));

        $arr=$this->validate($arr);

		$change=array_merge($arr,$check);
		$change['userid']=$this->userId;

		$num=$this->models->where(array('userid'=>$this->userId))->count();

		if(!$num || (isset($change['isdefault']) && $change['isdefault']==1)){
			$change['isdefault']=1;
		}

		$a=$this->models->fetchsql(0)->create($change);

		$id=isset($change['id'])?$change['id']:$this->models->insert_id();

		if(isset($change['isdefault']) && $change['isdefault']==1){
			$this->models->setDefault($change['userid'],$id);
		}

		$this->success();

	}

	// 删除地址
	public function deleteAction()
	{

		$check=$this->fill(array(
				array('id',0,'int'),
			));

		$a=$this->models->where(array('userid'=>$this->userId))->delete($check['id']);


		$this->success();

	}


}
?>