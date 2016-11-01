<?php

/**
 * 优惠券模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class coupon extends core\models
{


    public function addCoupon($arr)
    {

        $re=$this
        ->table('youhuiquan')
        ->field('num,limit_time')
        ->where(array('id'=>$arr['id']))
        ->getOne();
        if(!$re){
            return false;
        }

        if(time()>$re['limit_time']){
            return false;
        }
        $num=$re['num'];
        $re=$this
        ->table('yhq_user')
        ->where(array('userid'=>$arr['uid'],'yhqid'=>$arr['id']))
        ->count();

        if($re>=$num){
            return false;
        }
        $arr=array(
                'userid'=>$arr['uid'],
                'yhqid'=>$arr['id'],
                'createtime'=>time()
            );

        $re=$this
        ->create($arr);

        return true;
    }

    public function getlist($id)
    {

        $re=$this
        ->table('yhq_user')
        ->alias('yu')
        ->join('youhuiquan','y')
        ->joinLink('y.id=yu.yhqid')
        ->joinField('yu.id,y.limit_time,y.limit_money,y.yhq_type,y.yhq_other,y.limit_type,y.type_id,yu.state')
        ->where('yu.userid='.$id)
        ->fetchsql(0)
        ->select();

        return $re;
    }

    public function getInfo($id)
    {

        $re=$this
        ->table('yhq_user')
        ->alias('yu')
        ->join('youhuiquan','y')
        ->joinLink('y.id=yu.yhqid')
        ->joinField('yu.id,y.limit_time,y.limit_money,y.yhq_type,y.yhq_other,y.limit_type,y.type_id,yu.state')
        ->where('yu.id='.$id)
        ->fetchsql(0)
        ->getOne();

        return $re;
    }

    public function shopList($arr,$id)
    {
        $re=$this->getlist($id);
        if(!$re){
            return array();
        }
        $info=$this
        ->table('order')
        ->field('relainfo')
        ->where('statue=0')
        ->find($arr['id']);

        if(!$info){
            $this->error='orderCouSh';
            return false;
        }

        $arr=json_decode($info['relainfo'],true);

        $array=array();
        $money=array_column($arr,'money');
        $num=array_column($arr,'num');
        $shopid=array_column($arr,'shopid');
        $type=array_column($arr,'type');

// array_search
        foreach ($re as $k=> $value) {
            if($value['limit_time']<time()){
                continue;
            }

            if(in_array($value['limit_type'],$type)){
                foreach ($type as $key => $v) {

                    if( $value['state']==0 &&
                        $v==$value['limit_type'] &&
                        in_array($shopid[$key],explode(',', $value['type_id'])) &&
                        ($value['limit_money']<$money[$key]*$num[$key])
                        ){
                        $array[]=$re[$k];
                    }
                }

            }
        }


        return $array;

    }

    public function checkCoupon($arr)
    {
        $id=$arr['id'];
        if (!is_numeric($id)) {
            return false;
        }

        $a=$this
        ->table('youhuiquan')
        ->find($id);
        if(!$a){
            return false;
        }
        $this->addCoupon($arr);
        // return true;
    }

    public function useCoupon($orderid,$id,$type,$code)
    {
        if($type==2){
            $arr['yjq_code']=$code;
        }
        $arr['id']=$id;
        $arr['state']=1;
        $arr['usetime']=time();
        $arr['order_id']=$orderid;

        $this
        ->table('yhq_user')
        ->create($arr);
    }
}
?>