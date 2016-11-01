<?php

/**
 * 收藏模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class collect extends core\models
{

    public $table = 'collect';

    public function addCollect($arr)
    {


    	if($arr['type']==1){
    		$table='service';
    	}else{
    		$table='sp';
    	}

    	$a=$this
    	->table($table)
    	->field('id')
    	->find($arr['spid']);
    	if(!$a){
    		$this->error=($arr['type']==1)?'serverMiss':'shopMiss';
    		return false;
    	}

    	$a=$this
    	->table('collect')
    	->where($arr)
    	->getOne();

    	if($a && $arr['is_delete']!=1){
    		$this->error='collected';
    		return false;
    	}
    	if($arr['is_delete']==1){
    		unset($arr['is_delete']);
	    	$re=$this
	    	->where($arr)
	    	->data(array('is_delete'=>1))
	    	->save();
    	}else{
    		$arr['createtime']=time();
    		$re=$this
	    	->create($arr);
    	}

        return $re;
    }

    public function getList($arr)
    {
    	$arr=$this
    	->where('is_delete=0 and userid='.$arr['userid'])
        ->page($arr['page'],$arr['pagesize'])
    	->select();
    	if(!$arr){
    		return array();
    	}
    	$array=array();
    	foreach ($arr as $key => $value) {
    		if($value['type']==1){
    			$array[$key]=$this->getService($value['spid']);
    		}else{
    			$array[]=$this->getSp($value['spid']);
    		}
            $array[$key]['type']=$value['type'];
            $array[$key]['collectid']=$value['id'];
    	}
    	return $array;
    }

    public function getService($id)
    {
        $re=$this
    	->table('service')
    	->field('id,name,larioc,jg')
    	->find($id);
    	return $re;

    }
    public function getSp($id)
    {
        $re=$this
        ->table('sp')
        ->field('id,name,larioc,jg')
    	->find($id);
    	return $re;
    }

}
?>