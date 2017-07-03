<?php
namespace api\models;

use core;

defined("ACC")||exit('ACC Denied');

class paytool extends all
{

	public $url = 'https://pay.swiftpass.cn/pay/gateway';


    public function checkDate($data)
    {
        if(!$data){
			$this->putError('noData');
        }
                // Log::write($__post);
        $arr = $this->getxml($data);
        $sign = $arr['sign'];
        $data = $this->buildQuery($arr);
        if($sign != $data){
			$this->putError('signError');
        }
        return $arr;
    }

    /**
     * [getxml xml转换成数组]
     * @param  [xml]   $fileContent [准备转换的xml]
     * @return [array]              [转换成功的array]
     */
    public function getxml($fileContent){
        $xml_parser = xml_parser_create();
        if(!xml_parse($xml_parser,$fileContent,true)){
           xml_parser_free($xml_parser);
           return false;
        }
        $xmlResult = simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
        //foreach循环遍历
        $xmlResult=$this->objectToArray($xmlResult);

        return $xmlResult;
    }
    /**
     * [objectToArray 对象转数组]
     * @param  [type] $e [对象]
     * @return [type]    [数组]
     */
    public function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }

    /**
     * [postxml 发送微信支付请求]
     * @param  [string] $str [请求的xml]
     * @return [string]      [响应数据]
     */
    public function postxml($str){

        if (!extension_loaded("curl")) {
            $this->error('curl error',2);
        }
        //构造xml
        $xmldata=$str;
        //初始一个curl会话
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL,$this->url);
        //设置发送方式：
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        //设置发送数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
        //抓取URL并把它不传递给浏览器
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //强制协议为1.0
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        //头部要送出'Expect: '
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:',''));
        //强制使用IPV4协议解析域名
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        //存取数据
        $file_contents = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);

        return $file_contents;

    }

    /**
     * [linkxml 数组转xml]
     * @param  [array] $data [要转换的数组]
     * @return [string]       [转换成功的xml]
     */
    public function linkxml($data)
    {
        $str='';
        $str.="<xml>\r\n\t";
        $arr=array();
        foreach ($data as $key => $value) {
			if( (empty($value) && $value!=0) || "" == $value){
                continue;
			}
            $arr[]='<'.$key.'><![CDATA['.$value.']]></'.$key.'>';
        }
        $str.=implode("\r\n\t", $arr);
        $str.="\r\n</xml>";
        return $str;
    }

    public function putError($data)
    {
      $arr = core\Error::getError($data, 0);
      $re['result_code'] = $arr['code'];
      $re['err_msg'] = $arr['msg'];
	  // print_r($re);
      $re = $this->linkxml($re);
      echo $re;
      exit;
    }

    /**
     * [rand16 生成随机数]
     * @return [type] [随机数]
     */
    public function rand16(){
        $str=rand('0','999999');
        $start=rand('0','16');
        return substr(md5($str),$start,16);
    }
    /**
     * [buildQuery 加密数据]
     * @param  [array] $query [加密的数组]
     * @return [string]       [加密后字符串]
     */
    public function buildQuery($query)
    {

        if (!$query) {
            return null;
        }

        //将要 参数 排序
        ksort( $query );


        //重新组装参数
        $params = [];

        foreach ($query as $key => $value) {

            if("sign" == $key || "" == $value || (empty($value) && $value!=0)){
                continue;
            }
            $params[] = $key .'='. $value;
        }

        $params[] = 'key=' . $this->key;

        $data = implode('&', $params);

        $sign = strtoupper(md5($data));

        // $out = $data .'&sign='.$sign;

        return $sign;
    }


}
?>
