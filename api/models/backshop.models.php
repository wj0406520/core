<?php

/**
 * 退货模版
 *
 * 退货成功
 * 退钱   退优惠券（不同优惠券处理方式不同）   减掉商户不可用的钱
 *
 *定时任务每天0点执行   支付14天之后的未退订单 把商户不可用的钱给减去   把商户可提现的钱加上
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class backshop extends core\models
{

    public $table = 'money';

    public function backshop($arr)
    {
    	$this->array=$arr;
    	$this->tuihuo=array(
        		'userid'=>$arr['userid'],
        		'typeid'=>$arr['typeid'],
        		'type'=>$arr['type'],
        		'createtime'=>time(),
    		);
    	$a=true;
    	switch ($arr['type']) {
    		case 1:
    			$a=$this->backorder();
    			break;
    		case 2:
    			$a=$this->backhdyy(1);
    			break;
    		case 3:
    			$a=$this->backhdyy();
    			break;

    		default:
    			$this->error='typeError';
    			return false;
    			break;
    	}

        return $a;
    }

    public function backorder()
    {
    	$arr=$this->array;
    	$a=true;

        $re=$this
        ->table('order')
        ->field('relainfo,statue,zhifutime')
        ->find($arr['typeid']);
        if(!$re){
            $this->error='orderMiss';
            return false;
        }
        if(time()>(60*60*24*7)*$re['zhifutime']){
            $this->error='backOrderTimeError';
            return false;
        }
        if(!($re['statue']==4 || $re['statue']==5 || $re['statue']==1)){
            $this->error='backOrderError';
            return false;
        }

		$a=$this->backordersun($re);
        return $a;

    	// print_r($arr);

     //    $money=array_column($re,'money');

     //    $tuihuo=array(
     //            'backmoney'=>$money
     //        );
     //    $this->tuihuo=array_merge($tuihuo,$this->tuihuo);


     //    $this->createmoney();
    	// return $a;

    }
    public function backordersun($re)
    {
    	$arr=$this->array;
    	$a=true;

    	$re=json_decode($re['relainfo'],true);
        $shopid=array_column($re,'shopid');
        // $type=array_column($re,'type');
        // $money=array_column($re,'money');
        // $num=array_column($re,'num');
        // $sjid=array_column($re,'sjid');

        $data=array();
        // foreach ($arr['data'] as $key => $value) {
        //     $sign=0;
	       //  foreach ($shopid as $k => $v) {
	       //      if($v==$value['id'] && $type[$k]==$value['type']){
	       //          $sign=1;
	       //          $data[$key]['type']=$value['type'];
	       //          $data[$key]['id']=$value['id'];
	       //          $data[$key]['money']=$money[$key];
	       //      }
	       //  }
        // }

        // $money=array_sum(array_column($data,'money'));

        // $this
        // ->table('order')
        // ->create(array('id'=>$arr['typeid'],'statue'=>2));



        foreach ($shopid as $k => $v) {
            if($v==$arr['shopid'] && $re[$k]['type']==$arr['shoptype']){
                $data['type']=$arr['shoptype'];
                $data['id']=$arr['shopid'];
                $data['money']=$re[$k]['money']*$re[$k]['num'];
                $data['sjid']=$re[$k]['sjid'];
                if($re[$k]['state']!=1){
                    $this->error='backOrderError';
                    return false;
                }
                $re[$k]['state']=2;
            }
        }
        $state=array_column($re,'state');

        $statue=2;
        foreach ($state as $key => $value) {
            if($value!=2){
                $statue=1;
            }
        }

        if(!$data){
            $this->error='backOrderError';
            return false;
        }

        $this
        ->table('order')
        ->create(array('id'=>$arr['typeid'],'statue'=>$statue,'relainfo'=>json_encode($re,JSON_UNESCAPED_UNICODE)));


        $money=$data['money'];

        $tuihuo=array(
                'backstate'=>1,
                'changetype'=>2,
        		'relainfo'=>json_encode($data),
        		'backmoney'=>$money,
                'money'=>$money,
        	);
        $this->tuihuo=array_merge($tuihuo,$this->tuihuo);

        $this->createmoney();

        $array['moneyid']=$this->insert_id();
        $array['reason']=$arr['reason'];
        $array['backex']=$arr['explain'];
        $array['img1']=$arr['img1'];
        $array['img2']=$arr['img2'];
        $array['img3']=$arr['img3'];

        $this->table('tuihuo')->create($array);

    	return $a;
    }
    public function backhdyy($type=0)
    {
        $arr=$this->array;

        if($type==0){
            $a=$this
            ->table('huodong_bm')
            ->find($arr['typeid']);

            if(!$a){
                $this->error='huodongMiss';
                return false;
            }
            $time=$this
            ->table('huodong')
            ->field('jzbmsj,nowNum')
            ->find($a['hdid']);

            if($time['jzbmsj']<time() || $a['status']!=1){
                $this->error='huodongError';
                return false;
            }
            $this->table('huodong')->create(array(
                    'nowNum'=>$time['nowNum']-$a['bmrs'],
                    'id'=>$a['hdid']
                ));

            $this->table('huodong_bm')->create(array(
                'id'=>$arr['typeid'],
                'status'=>'3')
            );
        }else{
            $arr=$this->array;
            $a=$this
            ->table('yuyue')
            ->find($arr['typeid']);
            if(!$a){
                $this->error='yuyueMiss';
                return false;
            }
            if($a['yykssj']<time() || $a['status']!=1){
                $this->error='yuyueError';
                return false;
            }
            $this->table('yuyue')->create(array(
                'id'=>$arr['typeid'],
                'status'=>'3')
            );
        }


        $money=$this->table('user')->field('money')->find($arr['userid']);

        $this->table('user')->create(array(
            'id'=>$arr['userid'],
            'money'=>$money['money']+$a['money']
            ));

        $tuihuo=array(
                'backstate'=>2,
                'backmoney'=>$a['money'],
                'changetype'=>2,
                'backtime'=>time(),
                'nowmoney'=>$money['money'],
                'changemoney'=>$money['money']+$a['money'],
                'money'=>$a['money'],
            );

        $this->tuihuo=array_merge($tuihuo,$this->tuihuo);


        $this->createmoney();

    	return 1;
    }
    //废弃
    public function backyuyue()
    {
        // print_r($this->array);
        $arr=$this->array;
        $a=$this
        ->table('yuyue')
        ->find($arr['typeid']);
        if($a['yykssj']<time() || $a['status']!=1){
            $this->error='yuyueError';
            return false;
        }


        $this->table('yuyue')->create(array('id'=>$arr['typeid'],'status'=>'3'));

        $money=$this->table('user')->field('money')->find($arr['userid']);

        $this->table('user')->create(array('id'=>$arr['userid'],'money'=>$money['money']+$a['money']));

        $tuihuo=array(
                'backstate'=>2,
                'backmoney'=>$a['money'],
                'changetype'=>2,
                'backtime'=>time(),
                'nowmoney'=>$money['money'],
                'changemoney'=>$money['money']+$a['money'],
                'money'=>$a['money'],
            );

        $this->tuihuo=array_merge($tuihuo,$this->tuihuo);

        $this->createmoney();

    	return 1;
    }

    public function createmoney()
    {
    	$this
    	->table('money')
    	->create($this->tuihuo);
    }

    public function backcancel($arr)
    {


        $order=$this
        ->table('order')
        ->find($arr['typeid']);

        if(!$order){
            $this->error='orderMiss';
            return false;
        }
        $re=json_decode($order['relainfo'],true);

        $shopid=array_column($re,'shopid');

        $sign=0;

        foreach ($shopid as $k => $v) {
            if($v==$arr['shopid'] && $re[$k]['type']==$arr['shoptype']){
                if($re[$k]['state']!=2){
                    $this->error='backOrderCancelError';
                    return false;
                }else{
                    $sign=0;
                    $re[$k]['state']=7;
                    break;
                }
            }else{
                $sign=1;
            }
        }
        if($sign){
            $this->error='backError';
            return false;
        }

        $state=array_column($re,'state');

        $statue=7;
        foreach ($state as $key => $value) {
            if($value!=7){
                $statue=1;
            }
        }

        $order['statue']=$statue;
        $order['relainfo']=json_encode($re,JSON_UNESCAPED_UNICODE);

        $this->table('order')->create($order);

        $money=$this
        ->table('money')
        ->where(array('changetype'=>2,'type'=>1,'typeid'=>$arr['typeid']))
        ->select();

        foreach ($money as $key => $value) {
            $re=json_decode($value['relainfo'],true);
            if($re['type']==$arr['shoptype'] && $re['id']==$arr['shopid']){
                $value['backstate']=4;
            }
            $this->table('money')->create($value);
        }

        return true;
    }

    public function backsucc($arr)
    {
        $order=$this
        ->table('order')
        ->find($arr['typeid']);

        if(!$order){
            $this->error='orderMiss';
            return false;
        }
        print_r($order);
        exit;
    }
}
?>