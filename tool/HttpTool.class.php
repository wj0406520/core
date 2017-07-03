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
     * @param  [string] $url    [路径]
     * @param  [array]  $arr    [请求参数]
     * @param  [array]  $header [请求头]
     * @return [string]         [请求结果]
     */
    public static function post($url,$arr,$header=[]){

       $re = self::http($url,$arr,$header,2);

       return $re;

    }

    /**
     * [postjson 发出post请求]
     * @param  [string] $url    [路径]
     * @param  [array]  $arr    [请求参数]
     * @param  [array]  $header [请求头]
     * @return [string]         [请求结果]
     */
    public static function postJson($url,$arr,$header=[]){

        $arr=json_encode($arr);

        $header[]='Content-Type:application/json';

       $re = self::http($url,$arr,$header,2);

       return $re;

    }
    /**
     * [put 发出put请求]
     * @param  [string] $url    [路径]
     * @param  [array]  $arr    [请求参数]
     * @param  [array]  $header [请求头]
     * @return [string]         [请求结果]
     */
    public static function put($url,$arr,$header=[]){

       $re = self::http($url,$arr,$header,1,'PUT');

       return $re;

    }
    /**
     * [delete 发出delete请求]
     * @param  [string] $url    [路径]
     * @param  [array]  $arr    [请求参数]
     * @param  [array]  $header [请求头]
     * @return [string]         [请求结果]
     */
    public static function delete($url,$arr,$header=[]){

       $re = self::http($url,$arr,$header,1,'DELETE');

       return $re;

    }

    /**
     * [get 发出get请求]
     * @param  [string] $url    [路径]
     * @param  [array]  $arr    [请求参数]
     * @param  [array]  $header [请求头]
     * @return [string]         [请求结果]
     */
    public static function get($url,$arr,$header=[]){

       $re = self::http($url,$arr,$header);

       return $re;
    }

    protected static function http($url,$arr,$header=[],$type=0,$method='')
    {
        self::check($url);

        $curl = curl_init();

        switch ($type) {
            case '0':
                    $str=http_build_query($arr);
                    $url=$str?$url.'?'.$str:$url;
                break;
            case '1':
                //设置发送方式：
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                //设置发送数据
                curl_setopt($curl, CURLOPT_POSTFIELDS, $arr);
                break;

            case '2':
                //设置发送方式：
                curl_setopt($curl, CURLOPT_POST, true);
                //设置发送数据
                curl_setopt($curl, CURLOPT_POSTFIELDS, $arr);
                break;

            default:
                # code...
                break;
        }
        if($header){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_URL,$url);
        //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($curl, CURLOPT_HEADER,0);
         // 获取数据返回
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //强制协议为1.0
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        //关闭ssl
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        //强制使用IPV4协议解析域名
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        //存取数据
        $file_contents = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);

        return $file_contents;
    }

    protected static function check($url)
    {
        if (!extension_loaded("curl")) {
            echo 'curl error';
            exit;
        }
        if(!$url){
            echo 'url error';
            exit;
        }
    }

}