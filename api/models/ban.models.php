<?php

/**
 * banner模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class ban extends core\models
{

    public $table = 'banner';

    public function getList($arr)
    {

        $re=$this->field('id','no')->limit(5)->fetchsql(0)->where($arr)->select();

        return $re;
    }

}
?>