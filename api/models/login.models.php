<?php

/**
 * 登录模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class login extends core\models
{

    public $table = 'user';

    public function checkPsw($phone,$psw)
    {
        $arr=array(
                'pass'=>md5($psw),
                'telphone'=>$phone
            );
        $re=$this->field('id,is_fenxiao')->where($arr)->fetchsql(0)->getOne();
        return $re;
    }

    public function checkUser($phone)
    {
        $arr=array(
                'telphone'=>$phone
            );
        $re=$this->field('id')->where($arr)->fetchsql(0)->getOne();
        return $re['id'];
    }

}
?>