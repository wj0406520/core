<?php

/**
 * 其他模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class all extends core\models
{


    public function checkToken($token)
    {
        $arr=array(
                'token'=>$token
            );
        $re=$this->table('user')->field('id')->where($arr)->fetchsql(0)->getOne();

        return $re['id'];
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