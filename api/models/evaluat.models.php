<?php

/**
 * banner模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class evaluat extends core\models
{

	public $table='order';

    public function getList($check,$userId)
    {


    	$a=$this
    	->field('relainfo')
    	->where($check)
    	->getOne();

    	if(!$a){
    		$this->error='orderMiss';
    		return false;
    	}

    	$arr=json_decode($a['relainfo'],JSON_UNESCAPED_UNICODE);

    	$array=array();

    	foreach ($arr as $key => $value) {
    		$array[$key]['shopid']=$value['shopid'];
    		$array[$key]['type']=$value['type'];
    	}

        return $array;
    }


    public function evaluat($data,$check,$uid)
    {

    	$a=$this
    	->table('order')
        ->field('statue,relainfo')
    	->where('id='.$check['orderid'])
    	->getOne();

    	if(!$a){
            $this->error='orderMiss';
    		return false;
    	}
        if($a['statue']!=4){
            $this->error='evaluatNo';
            return false;
        }

        $array=json_decode($a['relainfo'],true);

        $shopid=array_column($array,'shopid');
        $type=array_column($array,'type');


        // print_r($array);


        $signa=1;
    	foreach ($data as $value) {

            $sign=0;
            foreach ($shopid as $k => $v) {
                if($v==$value['id'] && $type[$k]==$value['type']){
                    $sign=1;
                }
            }
            if(!$sign){
                continue;
            }

    		$start=isset($value['start'])?$value['start']:5;
    		$msg=isset($value['msg'])?$value['msg']:'';
            $img1=isset($value['img1'])?$value['img1']:'';
            $img2=isset($value['img2'])?$value['img2']:'';
            $img3=isset($value['img3'])?$value['img3']:'';
    		$arr['spid']=$value['id'];
    		$arr['type']=$value['type'];
            $arr['img1']=$img1;
            $arr['img2']=$img2;
            $arr['img3']=$img3;
    		$arr['plzid']=$uid;
    		$arr['orderid']=$check['orderid'];

    		$a=$this->table('pingjia')->field('id')->where($arr)->getOne();
    		if($a){
    			$arr['id']=$a['id'];
    		}
    		$arr['xj']=$start;
    		$arr['pl']=$msg;
    		$arr['createtime']=time();
    		$a=$this->table('pingjia')->create($arr);
            if(!$a){
                $signa=0;
            }
    	}



        if(!$signa){
            $this->error='evaluatError';
            return false;
        }

        $a=$this
        ->table('order')
        ->create(array('statue'=>5,'id'=>$check['orderid']));

    	return true;
    }

    public function getEvaluat($arr)
    {

        $re=$this
        ->table('pingjia')
        ->alias('p')
        ->join('user','u')
        ->joinLink('p.plzid=u.id')
        ->joinField('p.pl,p.xj,p.img1,p.img2,p.img3,p.createtime,u.face,u.UserName')
        ->where('spid='.$arr['id'])
        ->where('type='.$arr['type'])
        ->page($arr['page'],$arr['pagesize'])
        ->order('createtime desc')
        ->select();

        return $re;
    }
}
?>