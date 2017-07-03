<?php

/**
 * 公共的信息处理，对请求统一处理
 */
namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class all extends core\Models
{

    /**
     * [checkToken 检测token]
     * @param  [type] $token [唯一标识符]
     * @return [type]        [用户id]
     *
     * 1.获取token
     * 2.获取用户表中的token相同的用户的id
     * 3.返回用户id，如果不存在返回false
     */
    public function checkToken($token)
    {
        $arr = [
        'token' => $token
        ];
        $re = $this->table('user')->field('id')->where($arr)->fetchsql(0)->getOne();

        return $re['id'];
    }

    /**
     * [checkUser 通过电话号码检测用户]
     * @param  [type] $phone [电话号码]
     * @return [type]        [用户id]
     */
    public function checkUser($phone)
    {
        $arr = [
        'telphone' => $phone
        ];
        $re = $this->table('user')->field('id')->where($arr)->fetchsql(0)->getOne();
        return $re['id'];
    }

    /**
     * [setLog 设置请求log信息]
     *
     * 1.获取信息
     * 2.存入数据库
     */
    public function setLog()
    {
        $__post = file_get_contents("php://input");
        $arr['post_data'] = jsonEncode($_POST);
        $arr['get_data'] = jsonEncode($_GET);
        $arr['file_data'] = jsonEncode($_FILES);
        $a = getallheaders();
        $arr['header_data'] = jsonEncode($a);
        $arr['php_input'] = file_get_contents('php://input');
        // $arr['file_data'] = $__post;
        $arr['create_time'] = TIME;
        $arr['url'] = URL_CONTROL.'/'.URL_MODEL;

        $this->table('log_api')->create($arr);
    }

    /**
     * [checkSafePass 检测用户安全密码]
     * @param  [type] $pay_password [安全密码]
     *
     * 1.获取用户信息
     * 2.比对md5后的值如果错误则报错
     */
    public function checkSafePass($pay_password)
    {
        $pass = $this
        ->table('user')
        ->field('pay_password')
        ->find(self::$user_id);

        if(md5($pay_password)!=$pass['pay_password']){
            $this->errorMsg('safePassError');
        }
    }

}