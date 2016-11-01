<?php

namespace tool;
/*
@@@ $name上传文件input名称
@@@ $uptype上传文件类型
@@@ $size上传文件不能超过多少大小
*/

defined('ACC')||exit('Acc Denied');

class HttpTool{


    /**
     * [post 发出post请求]
     * @param  [string] $url [路径]
     * @param  [array]  $arr [请求参数]
     * @return [string]      [请求结果]
     */
    static public function post($url,$arr){

        if (!extension_loaded("curl")) {
            $this->error('curl error',2);
        }

        //构造xml
        $xmldata=$arr;
        //初始一个curl会话
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置发送方式：
        curl_setopt($curl, CURLOPT_POST, true);
        //设置发送数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
        //抓取URL并把它不传递给浏览器
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //存取数据
        $file_contents = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);

        return $file_contents;

    }

    /**
     * [get 发出get请求]
     * @param  [string] $url [路径]
     * @param  [array]  $arr [请求参数]
     * @return [string]      [请求结果]
     */
    static public function get($url,$arr){

        if (!extension_loaded("curl")) {
            $this->error('curl error',2);
        }

        $str=http_build_query($arr);
        $url=$str?$url.'?'.$str:$url;
        //初始一个curl会话
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL,$url);
        //抓取URL并把它不传递给浏览器
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //存取数据
        $file_contents = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);

        return $file_contents;

    }
}
?>