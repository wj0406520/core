<?php

/**
 * 商家首页模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class shopindex extends core\models
{

    public $table = 'user_shop';

    public function getList($id)
    {

        $re=$this->field('id,UserName,face,shopindeximg')->find($id);
		$sp=$this
        ->table('sp')
        ->joinfield('id,name,larioc,jg,"2" as type')
        ->where(' publishuserid='.$id)
        ->select();
		$fw=$this
        ->table('service')
        ->joinfield('id,name,larioc,jg,"1" as type')
        ->where(' publishuserid='.$id)
        ->select();
        $arr=array_merge($fw,$sp);
        $re['num']=count($arr);
        $re['data']=$arr;

        return $re;
    }

    public function getFxList($arr)
    {

        $a=$this
        ->table('fenxiao_sp')
        ->alias('fs')
        ->join('sp','s')
        ->joinLink('s.id=fs.spid')
        ->joinField('s.id,s.name,s.jg,s.larioc')
        ->where('fs.userid='.$arr['userid'])
        ->where(' is_show=1 and is_delete=1')
        ->page($arr['page'],$arr['pagesize'])
        ->order('id desc')
        ->fetchSql(0)
        ->select();

        return $a;
    }

}



?>
