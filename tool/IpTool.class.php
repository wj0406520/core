<?php

namespace tool;
/**
 * 根据ip获取地址信息
*/

defined('ACC')||exit('Acc Denied');

class IpTool{

    public static function getAddress($ip)
    {
        $url = 'http://ip.taobao.com/service/getIpInfo.php';
        $arr =[
            'ip'=>$ip
        ];
        $re = HttpTool::get($url,$arr);

        $arr = json_decode($re,true);

        if($arr['code']==0){
            return $arr['data'];
        }
        return false;
    }
}