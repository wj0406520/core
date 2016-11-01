<?php

/**
 * server模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class server extends core\models
{


    public function getList($arr)
    {
        $where=array();

        if($arr['cwsh']){
            $where['cwsh']=$arr['cwsh'];
        }
        if($arr['cwdb']){
            $where['cwdb']=$arr['cwdb'];
        }
        if($arr['dljz']){
            $where['dljz']=$arr['dljz'];
        }


        $re=$this
        ->table('service')
        ->field('id,name,larioc,publishuserid,jg')
        ->page($arr['page'],$arr['pagesize'])
        ->order('id desc')
        ->fetchsql(0)
        ->where($where)
        ->select();

        return $re;
    }

    public function getType($arr)
    {
        $re=$this
        ->table('servicetype')
        ->field('id,serviceName')
        ->where('paterid='.$arr['type'])
        ->select();
        return $re;
    }

    public function getInfo($arr)
    {

        $id=$arr['id'];
        $uid=$arr['uid'];
        $collect=0;
        if($uid){
            $collect=$this
            ->table('collect')
            ->where(array('spid'=>$id,'type'=>1,'userid'=>$uid,'is_delete'=>0))
            ->count();
        }


        $re=$this
        ->table('service')
        ->field('id,name,js,jg,cs,headeImg,headeImg1,headeImg2,headeImg3,server_address,publishuserid,psf')
        ->where('id='.$id)
        ->where('sfyc=0')
        ->getOne();


        if(!$re){
            return false;
        }
        $re['shopName']='';
        $re['qq']='';
        $re['add']='';
        if($re['publishuserid']){
            $arr=$this
            ->table('user_shop')
            ->field('shopName,qq')
            ->where('id='.$re['publishuserid'])
            ->getOne();
            $re=array_merge($re,$arr);
        }
        if($re['server_address']){
            $add=explode(',', $re['server_address']);
            $arr=array();
            foreach ($add as $v) {
                if($v){
                    $arr[]='district_code='.$v;
                }
            }
            $add=implode(' or ' ,$arr);
            $arr=$this
            ->table('district')
            ->joinField('district_name as name,district_code as code')
            ->where($add)
            ->select();
            $re['add']=$arr;
            // $re=array_merge($re,$arr);
        }
        $re['collect']=$collect;
        $re['pingjiaNum']=$this
        ->table('pingjia')
        ->where('spid='.$id)
        ->where('type=1')
        ->count();

        if($re['pingjiaNum']){
            $arr=$this
            ->table('pingjia')
            ->alias('p')
            ->join('user','u')
            ->joinLink('p.plzid=u.id')
            ->joinField('p.pl,u.face,u.UserName')
            ->where('spid='.$id)
            ->where('type=1')
            ->order('createtime desc')
            ->getOne();
            $re=array_merge($re,$arr);
        }


        return $re;
    }

}
?>