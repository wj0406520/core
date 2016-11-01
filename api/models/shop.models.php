<?php

/**
 * shop模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class shop extends core\models
{


    public function getList($arr)
    {

        $re=$this
        ->table('sp')
        ->field('id,publishuserid,name,larioc,jg')
        ->page($arr['page'],$arr['pagesize'])
        ->where(array('name'=>array('like'=>'%'.$arr['search'].'%')))
        ->order('id desc')
        ->fetchsql(0)
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
            ->where(array('spid'=>$id,'type'=>2,'userid'=>$uid,'is_delete'=>0))
            ->count();
        }

        $re=$this
        ->table('sp')
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
        ->where('type=2')
        ->count();

        if($re['pingjiaNum']){
            $arr=$this
            ->table('pingjia')
            ->alias('p')
            ->join('user','u')
            ->joinLink('p.plzid=u.id')
            ->joinField('p.pl,u.face,u.UserName')
            ->where('spid='.$id)
            ->where('type=2')
            ->order('createtime desc')
            ->getOne();
            $re=array_merge($re,$arr);
        }

        return $re;
    }

}
?>