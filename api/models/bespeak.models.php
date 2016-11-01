<?php

/**
 * index模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class bespeak extends core\models
{

    public $table='yuyue_sp';

    public function getList($arr)
    {

        $re=$this
        ->field('id,spbt,spzs')
        ->page($arr['page'],$arr['pagesize'])
        ->where('sfyc=0')
        ->order('id desc')
        ->select();

        return $re;
    }

    public function getInfo($check)
    {

        $re=$this
        ->field('jzsj,sfyc,spzs,tjsj','no')
        ->where('sfyc=0')
        ->find($check['id']);


        $timearr=$this->gettime($check['id']);

        if($timearr){
            $arr=array();
            foreach ($timearr as $key => $value) {
                $arr[$key]['yykssj']=date('Y-m-d H:i',$value['yykssj']);
                $arr[$key]['yyjssj']=date('Y-m-d H:i',$value['yyjssj']);
            }
            $re['time']=$arr;

        }

        return $re;
    }

    public function pay($arr)
    {
        $yykssj=strtotime($arr['yykssj']);
        $yyjssj=strtotime($arr['yyjssj']);
        if( $yykssj>afterDayTime('+3') ||
            $yykssj<afterDayTime('+1') ||
            $yyjssj>afterDayTime('+3') ||
            $yyjssj<afterDayTime('+1') ||
            $yyjssj<$yykssj
            ){
            $this->error='timeError';
            return false;
        }

        $re=$this
        ->field('money')
        ->find($arr['spid']);

        if(!$re){
            $this->error='yuyueMiss';
            return false;
        }

        $timearr=$this->gettime($arr['spid']);

        if($timearr){
            foreach ($timearr as $value) {

                if( ($yykssj>=$value['yykssj'] && $yykssj<$value['yyjssj']) ||
                    ($yyjssj>=$value['yykssj'] && $yyjssj<$value['yyjssj'])
                    ){
                    $this->error='timeSelect';
                    return false;
                }
            }
        }

        $arr['status']=1;
        $arr['xdsj']=time();
        $arr['yykssj']=$yykssj;
        $arr['yyjssj']=$yyjssj;
        $arr['yysj']=intval(($yyjssj-$yykssj)/60/60);
        if($arr['yysj']==0){
            $this->error='timeSelect';
            return false;
        }
        $arr['money']=$re['money']*$arr['yysj'];
        $arr['code']=$arr['userid'].'|'.$arr['spid'].'|'.$yykssj.'|'.mt_rand(111111,999999);

        if($arr['paytype']==4){
            $usermoney=$this
            ->table('user')
            ->field('money')
            ->find($arr['userid']);
            $usermoney['money']-=$arr['money'];
            if($usermoney['money']<0){
                $this->error='lackMoney';
                return false;
            }
        }
        $a=$this
        ->table('yuyue')
        ->create($arr);

        $arr['typeid']=$this->insert_id();

        $money=new activity();

        $money->setmoney($arr,2);

        $array['money']=$arr['money'];
        $array['code']=$arr['code'];

        return $array;

    }


    public function gettime($id)
    {
        $tomorrow=afterDayTime('+1');
        $three=afterDayTime('+3');

        return $this
        ->table('yuyue')
        ->field('yykssj,yyjssj')
        ->where(array('yykssj'=>array('gt'=>$tomorrow),'yyjssj'=>array('lt'=>$three)))
        ->where('spid='.$id.' and is_delete=0 and status!=3')
        ->order('yykssj asc')
        ->select();
    }
}
?>