<?php

/**
 * 用户模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class user extends core\models
{

    public $table = 'user';

    public function getInfo($id)
    {

        $re=$this->field('pass,token,verificat,shopName,money_no,firshop,firtype','no')->fetchsql(0)->find($id);

        return $re;
    }

    public function bespeak($arr)
    {
    	$a=$this
		->table('yuyue')
        ->alias('y')
        ->join('yuyue_sp','ys')
        ->joinLink('y.spid=ys.id')
        ->joinField('ys.spzs,ys.spbt,y.id,y.money')
        ->page($arr['page'],$arr['pagesize'])
		->where('y.userid='.$arr['userid'].' and is_delete=0')
        ->order('xdsj desc')
		->select();
        return $a;
    }

    public function huodong($arr)
    {
        $a=$this
        ->table('huodong_bm')
        ->alias('hb')
        ->join('huodong','h')
        ->joinLink('hb.hdid=h.id')
        ->joinField('h.hdzs,h.hdbt,hb.id,hb.money')
        ->page($arr['page'],$arr['pagesize'])
        ->where('hb.userid='.$arr['userid'].' and is_delete=0')
        ->order('bmsj desc')
        ->select();
        return $a;
    }

    public function hdinfo($arr)
    {
        $a=$this
        ->table('huodong_bm')
        ->alias('hb')
        ->join('huodong','h')
        ->joinLink('hb.hdid=h.id')
        ->joinField('h.hdxq,hb.bmrs,hb.bmsj,h.jzbmsj,h.hdjssj,h.address,h.hdbt,hb.id,hb.money,hb.status')
        ->where('hb.userid='.$arr['userid'].' and is_delete=0 and hb.id='.$arr['id'])
        ->getOne();
        if(!$a){
            $a=array();
        }

        return $a;
    }

    public function bsinfo($arr)
    {
        $a=$this
        ->table('yuyue')
        ->alias('y')
        ->join('yuyue_sp','ys')
        ->joinLink('y.spid=ys.id')
        ->joinField('ys.spxq,ys.spxq,ys.spbt,y.id,y.money,y.yykssj,y.yyjssj,y.yysj,y.xdsj,y.code,y.status')
        ->where('y.userid='.$arr['userid'].' and is_delete=0 and y.id='.$arr['id'])
        ->getOne();
        if(!$a){
            $a=array();
        }
        return $a;
    }

    public function card($arr)
    {
        $arr['creattime']=time();
        $a=$this->table('renzhen')->where(array('code'=>$arr['code']))->count();
        if($a){
            $this->error='cardHad';
            return false;
        }

        $this->table('renzhen')->create($arr);

        $this->table('user')->create(array('id'=>$arr['userid'],'is_renzhen'=>1));

        return true;

    }

}
?>