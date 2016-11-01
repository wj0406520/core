<?php

/**
 * money模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class money extends core\models
{

    public $table = 'bank_user';

    public function addbank($arr)
    {

        unset($arr['code']);

        $a=$this
        ->table('bank_user')
        ->where('bankcard='.$arr['bankcard'].' and is_delete=0')
        ->count();
        if($a){
            $this->error='cardhad';
            return false;
        }
        $arr['createtime']=time();

    	$a=$this
        ->table('bank_user')
    	->create($arr);

        return true;
    }

    public function getList($arr)
    {
        $a=$this
        ->table('bank_user')
        ->field('shopid,is_delete,userid','no')
        ->where('userid='.$arr['userid'].' and is_delete=0')
        ->select();
        return $a;
    }

    public function deleteBank($arr)
    {
        $a=$this
        ->table('bank_user')
        ->where($arr)
        ->save(array('is_delete'=>1));
        return $a;
    }


    public function tokenmoney($arr)
    {
        // print_r($arr);
        $a=$this
        ->table('user')
        ->field('money')
        ->where(array('id'=>$arr['userid']))
        ->getOne();
        if(isset($a['money']) && $a['money']<$arr['money']){
            $this->error='moneyLack';
            return false;
        }
        $money=$a['money'];
        $a=$this
        ->table('bank_user')
        ->where(array('userid'=>$arr['userid'],'id'=>$arr['bankid']))
        ->count();
        if(!$a){
            $this->error='bankCardMiss';
            return false;
        }
        $arr['changemoney']=$array['money']=$money-$arr['money'];
        $array['id']=$arr['userid'];
        $arr['createtime']=time();

        $a=$this
        ->table('user')
        ->create($array);

        $a=$this
        ->table('bank_money')
        ->fetchSql(0)
        ->create($arr);

        return true;
    }

    public function get_card_name($card)
    {
        if(!is_numeric($card)){
            return false;
        }


        $data=$this->getArr($card);

        return $data;
    }

    public function getArr($card,$num=3)
    {

        $str=substr($card, 0,$num).'%';


        $data = $this
        ->table('bank_info')
        ->field('card_bin,bank_name,bank_img')
        ->where('card_bin like "'.$str.'"')
        ->select();

        if(count($data)==0){
            return false;
        }
        if(count($data)==1){
            return $data['0'];
        }

        $num++;

        return $this->getArr($card,$num);
        // echo 1;
    }

    public function detail($arr)
    {

        // $weekarray=array("周日","周一","周二","周三","周四","周五","周六");
        // $weekarray[date("w")];
        //1提现   2订单   3预约(状态1付款2退款)   4活动(状态1付款2退款)  5退款(订单)
        // $a=$this
        // ->table('bank_money')
        // ->joinfield('id,money,statue,"1" type,createtime')
        // ->where('userid='.$arr['userid'].' and statue=1')
        // ->order('id desc')
        // ->fetchSql(1)
        // ->select();

        // $a=$this
        // ->table('money')
        // ->joinfield('id,money,changetype as statue,"2" type,createtime')
        // ->where('userid='.$arr['userid'].' and type=1 and changetype=1')
        // ->order('id desc')
        // ->fetchSql(1)
        // ->select();
        // $a=$this
        // ->table('money')
        // ->joinfield('id,money,changetype as statue,"2" type,createtime')
        // ->where('userid='.$arr['userid'].' and type=1 and changetype=2 and backstate=2')
        // ->order('id desc')
        // ->fetchSql(1)
        // ->select();
        // $a=$this
        // ->table('money')
        // ->joinfield('id,money,changetype as statue,"3" type,createtime')
        // ->where('userid='.$arr['userid'].' and type=2')
        // ->order('id desc')
        // ->fetchSql(1)
        // ->select();
        // $a=$this
        // ->table('money')
        // ->joinfield('id,money,changetype as statue,"4" type,createtime')
        // ->where('userid='.$arr['userid'].' and type=3')
        // ->order('id desc')
        // ->fetchSql(1)
        // ->select();
        // ->fetchSql(1)
        // print_r($a);

        $arr['page']=$arr['page']>1?$arr['page']:1;
        $this->str='SELECT  id,money,statue,"1" type,"" orderno,createtime  FROM t_bank_money WHERE (userid='.$arr['userid'].' and statue=1)
              union
              SELECT  m.id,money,changetype as statue,"2" type,o.orderno ,m.createtime FROM t_money as m join t_order as o on  m.typeid =o.id WHERE (userid='.$arr['userid'].' and type=1 and changetype=1)
              union
              SELECT  m.id,m.money,changetype as statue,"3" type,y.code orderno,m.createtime FROM t_money as m left join t_yuyue as y on y.id=m.typeid WHERE (m.userid='.$arr['userid'].' and m.type=2)
              union
              SELECT  m.id,m.money,changetype as statue,"4" type,h.code orderno,m.createtime FROM t_money as m left join t_huodong_bm as h on h.id=m.typeid WHERE (m.userid='.$arr['userid'].' and m.type=3)
              union
              SELECT  m.id,money,changetype as statue,"2" type,o.orderno ,m.createtime FROM t_money as m join t_order as o on  m.typeid =o.id WHERE (userid='.$arr['userid'].' and type=1 and changetype=2 and backstate=2)
              ORDER BY id desc
              limit '.($arr['page']-1)*$arr['pagesize'].','.$arr['pagesize'].'
        ';
        $a=$this->diySelect();

        return $a;

    }
}
?>