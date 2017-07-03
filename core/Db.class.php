<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  数据库接口
// +----------------------------------------------------------------------
namespace core;

defined('ACC') || exit('ACC Denied');


abstract class Db {

    /*
    连接服务器
    parms $h 服务器地址
    parms $u 用户名
    parms $p 密码
    return bool
    */
    public abstract function connect();


    /*
    发送查询
    parms $sql 发送的sql语句
    return mixed bool/resource
    */
    public abstract function query($sql);

    /*
    查询多行数据
    parms $sql select型语句
    return array/bool
    */
    public abstract function getAll($sql);


    /*
    查询单个数据
    parms $sql select型语句
    return array/bool
    */
    public abstract function getOne($sql);


}
