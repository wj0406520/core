<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  获取错误信息   打印数据
// +----------------------------------------------------------------------
namespace core;

defined('ACC') || exit('ACC Denied');


class Error {
    /**
     * [diyError 获取错误信息]
     * @param  string $type  [错误名称]
     * @return [array]       [错误数据]
     */
    public static function diyError($type = ''){
        if (!file_exists(ERROR_FILE)) {
            debug('no found file ' . ERROR_FILE);
            return false;
        }
        $arr = include(ERROR_FILE);
        if ($type) {
            if (array_key_exists($type,$arr)) {
                return $arr[$type];
            } else {
                debug('no found type '.$type);
                return false;
            }
        }
        return $arr;
    }

    /**
     * [getError 获取错误信息]
     * @param  [string]  $data  [error文件中的键值]
     * @param  integer $type   [判断是不是错误信息]
     * @return [array]          [如果是错误直接输出，否则返回数组]
     */
    public static function getError($data, $type = 0){
        $error = self::diyError($data);
        $arr['code'] = $error[0];
        $arr['msg'] = $error[1];

        if ($type == 0) {
            return $arr;
        }else{
            self::renderForAjax($arr);
        }
    }

    /**
     * [renderForAjax 输出json数据]
     * @param  [array] $arr [输出的数据]
     */
    public static function renderForAjax($arr){
        header('Access-Control-Allow-Origin:*');
        echo jsonEncode($arr);
        exit;
    }
}
