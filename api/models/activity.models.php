<?php

/**
 * index模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class activity extends core\models
{

    public $table='huodong';

    public function getList($arr)
    {

        $re=$this
        ->field('id,hdbt,hdzs')
        ->page($arr['page'],$arr['pagesize'])
        ->where('sfyc=0')
        ->order('id desc')
        ->select();

        return $re;
    }

    public function getInfo($check)
    {

        $re=$this
        ->field('sfyc,hdzs,tjsj','no')
        ->where('sfyc=0')
        ->find($check['id']);

        // $re['jzbmsj']=date('Y-m-d H:i:s',$re['jzbmsj']);
        // $re['hdjssj']=date('Y-m-d H:i:s',$re['hdjssj']);

        return $re;
    }

    public function pay($arr)
    {


        $re=$this
        ->field('money,jzbmsj,num,nowNum')
        ->find($arr['hdid']);

        if(!$re){
            $this->error='huodongMiss';
            return false;
        }

        if($re['jzbmsj']<time()){
            $this->error='huodongJz';
            return false;
        }


        if(($re['nowNum']+$arr['bmrs'])>$re['num']){
            $this->error='huodongNum';
            return false;
        }
        $arr['status']=1;
        $arr['bmsj']=time();
        $arr['code']=$arr['userid'].'|'.$arr['hdid'].'|'.$arr['bmsj'].'|'.mt_rand(111111,999999);
        $arr['money']=$re['money']*$arr['bmrs'];

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
        ->table('huodong_bm')
        ->create($arr);

        $arr['typeid']=$this->insert_id();

        $this->setmoney($arr,3);

        $a=$this
        ->table('huodong')
        ->create(array('nowNum'=>'nowNum+'.$arr['bmrs'],'id'=>$arr['hdid']));
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
        ->where('spid='.$id)
        ->order('yykssj asc')
        ->select();
    }

    public function setmoney($arr,$type)
    {
    // [phone] => 13071985489
    // [hdid] => 1
    // [bmrs] => 1
    // [paytype] => 1
    // [userid] => 1
    // [status] => 1
    // [bmsj] => 1476780465
    // [code] => 1|1|1476780465|871156
    // [money] => 100
        $array=array(
                'userid'=>$arr['userid'],
                'paytype'=>$arr['paytype'],
                'typeid'=>$arr['typeid'],
                'changetype'=>1,
                'type'=>$type,
                'createtime'=>time(),
            );
         if($arr['paytype']!=4){

            $array['paymoney']=$arr['money'];
            $array['money']=$arr['money'];

            $this->table('money')->create($array);

            return true;
         }

         $user=new user();

         $a=$user->getInfo($arr['userid']);

         $array['money']=$arr['money'];

         $array['nowmoney']=$a['money'];

         $array['changemoney']=$a['money']-$arr['money'];

         $a['money']=$array['changemoney'];

         $this->table('money')->create($array);

         $this->table('user')->create($a);

         return true;
    }
}
?>