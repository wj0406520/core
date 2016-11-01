<?php

/**
 * 用户模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class cart extends core\models
{

    public $table='gouwuche';

    public function addCart($arr,$uid)
    {


    	if($arr['type']==1){
	    	$re=$this
		        ->table('service')
		        ->field('id,name,sico,jg,publishuserid')
		        ->where('id='.$arr['id'])
		        ->getOne();
    	}elseif($arr['type']==2){
	    	$re=$this
		        ->table('sp')
		        ->field('id,name,sico,jg,publishuserid,psf')
		        ->where('id='.$arr['id'])
		        ->getOne();
    	}else{
    		return false;
    	}
        $array['psf']=isset($re['psf'])?$re['psf']:0;
    	$array['shopid']=$re['id'];
    	$array['type']=$arr['type'];
    	$array['title']=$re['name'];
    	$array['money']=$re['jg'];
    	$array['headerpic']=$re['sico'];
    	$array['userid']=$uid;
    	$array['num']=$arr['num'];
        $array['sjid']=$re['publishuserid'];
        $array['state']=1;

        $re=$this
		    ->table('user_shop')
	        ->field('shopName')
	        ->fetchsql(0)
	        ->find($re['publishuserid']);

    	$array['shopName']=$re['shopName'];

    	$array['createtime']=time();

        $re=$this
        ->table('gouwuche')
        ->field('num,id')
        ->where(array(
                'userid'=>$uid,
                'shopid'=>$arr['id'],
                'state'=>1,
                'type'=>$arr['type']
            ))
        ->getOne();

        if($re['id']){
            $array['id']=$re['id'];
        }

        $re=$this
        ->table('gouwuche')
        ->create($array);

        return $re;
    }

    public function getlist($uid,$type=1)
    {

        if($type==1){
            $re=$this
            ->where(array(
                    'state'=>1,
                    'userid'=>$uid,
                ))
            ->order('id desc')
            ->select();
        }else{
            $re=$this
            ->where(' id='.$uid)
            ->order('id desc')
            ->select();
        }

        if(!$re){
            return array();
        }
        $arr=$array=array();

        foreach ($re as $value) {
            $arr[$value['sjid']]['shopName']=$value['shopName'];
            $arr[$value['sjid']]['data'][]=array(
                    'id'=>$value['id'],
                    'headerpic'=>$value['headerpic'],
                    'type'=>$value['type'],
                    'shopid'=>$value['shopid'],
                    'money'=>$value['money'],
                    'title'=>$value['title'],
                    'num'=>$value['num'],
                    'psf'=>$value['psf'],
                );
        };
        foreach ($arr as $value) {
            $array[]=$value;
        }

        return $array;
    }
}
?>