<?php

/**
 * 公益|资讯|投融资模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class other extends core\models
{

    public $table = 'other_news';

    public function news($arr)
    {

        $re=$this
        ->page($arr['page'],$arr['pagesize'])
        ->fetchSql(0)
        ->order('id desc')
        ->select();

        return $re;
    }

    public function free($arr)
    {
        $re=$this
        ->table('other_free')
        ->page($arr['page'],$arr['pagesize'])
        ->fetchSql(0)
        ->order('id desc')
        ->select();
        return $re;
    }


    public function fina($arr)
    {
        $where=array();
        if($arr['hyly']){
            $where['hyly']=$arr['hyly'];
        }
        if($arr['rzpc']){
            $where['rzpc']=$arr['rzpc'];
        }
        if($arr['szdq']){
            $where['szdq']=$arr['szdq'];
        }
        $re=$this
        ->table('other_finance')
        ->field('szdq,rzpc,hyly,creattime','no')
        ->page($arr['page'],$arr['pagesize'])
        ->fetchSql(0)
        ->where($where)
        ->order('id desc')
        ->select();
        return $re;
    }
}
?>

