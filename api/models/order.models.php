<?php

/**
 * 用户模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class order extends core\models
{

    public $table = 'order';

    public function getList($type,$uid)
    {

    	$arr=array(
    		'uid'=>$uid,
            'sfyc'=>0
    		);
    	if($type!=-1){
            $statue=$arr;
    		$arr['statue']=$type;
            if($arr['statue']==4){
                $statue['statue']=5;
            }
    	}

        $array=array();
        if(isset($arr['statue']) && $arr['statue']==4){
            $array=$this->getOrder($statue);
        }

        $re=$this->getOrder($arr);

        $re=array_merge($re,$array);

        if(empty($re)){
    		return false;
    	}

    	foreach ($re as $k => $v) {
    		$arr=array();
    		$a=json_decode($v['relainfo'],true);
    		$re[$k]['relainfo']=$a;
    		foreach ($a as $key => $value) {
    			# code...
    			#           [shopid] => 4 [shopid] => 4 [type] => 1 [shopName] => u5546u5bb6u8d22u52a1
    			unset($re[$k]['relainfo'][$key]['shopid']);
    			unset($re[$k]['relainfo'][$key]['type']);
    			unset($re[$k]['relainfo'][$key]['shopName']);
    			unset($re[$k]['relainfo'][$key]['id']);
    		}
    	}

        return $re;
    }

    public function getOrder($arr)
    {
        $re=$this
        ->field('orderno,id,totalprice,relainfo,statue')
        ->where($arr)
        ->fetchSql(0)
        ->select();
        return $re;
    }


    public function getinfo($arr,$id)
    {

    	$re=$this
    	->field('useraddid,id,relainfo')
    	->where($arr)
    	->getOne();

        if(!$re){
            $this->error='orderMiss';
            return false;
        }

    	$array['addressid']=$re['useraddid'];
    	$array['id']=$re['id'];

        $af=json_decode($re['relainfo'],true);


        $arr=$data=array();

        foreach ($af as $value) {
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
                    'state'=>$value['state'],
                );
        };
        foreach ($arr as $value) {
            $data[]=$value;
        }

        $re=new pay();
        $a=$re->getlist($array,$id);
        if(!$a){
        	$this->error=$re->error;
        	return false;
        }
        $a['order']=$data;

        // print_r($a);
        // print_r($data);
        // exit;

        return $a;
    }

    public function delOder($arr,$id)
    {
        $re=$this
        ->field('id')
        ->where($arr)
        ->getOne();
        if(!$re){
            $this->error='orderMiss';
            return false;
        }
        $arr['sfyc']=1;
        $re=$this
        ->where(array('uid'=>$id))
        ->create($arr);
        if(!$re){
            $this->error='deleteFail';
            return false;
        }
        return true;
    }
}
?>

