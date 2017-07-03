<?php

/**
 * 微信模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class wx extends core\Models
{

    // public $key='041uW7xE0ukCfg2VzHyE0OHSwE0uW7xa';
    // public $notify_url='http://hankebank.com/wx/backurl';
    // public $mch_id='1308749501';
    // public $trade_type='JSAPI';
    public $url='https://api.weixin.qq.com/sns/oauth2/access_token';
    public $appid='wxfaf2a5bebbea7f33';
    public $secret = '898bce8f58e8aee1db1ce53b373abe1b';


    public function getCode($arr)
    {


        $this->getAppid($arr);

        $param = [];
        foreach ($arr as $key => $value) {
            $param[] = $key.'='.$value;
        }
        $str = implode('&',$param);

        $array['appid']=$this->appid;
        $array['redirect_uri'] = 'http://'.URL_PATH.'/pay/weijs?'.$str;
        $array['response_type']='code';
        $array['scope']='snsapi_base';

        $str=http_build_query($array);

        $url='https://open.weixin.qq.com/connect/oauth2/authorize?'.$str.'#wechat_redirect';
        return $url;
    }

    public function getAppid($arr)
    {

        $pay = new pay();
        $pay->paytool = new paytool();

        $type = $arr['service']=='pay.weixin.jspay'?3:$pay->paytool->putError('serviceError');

        $user = $pay->getShopUser($arr['mch_id']);
        $pay->bank_type = $user['bank_type'];
        $pay->pay_type = $type;
        $user_id = $user['id'];

        $proport_id = $pay->getUserId($user_id);
        $bank = $pay->getBank($proport_id);

        $this->appid = $bank['appid'];
        $this->secret = $bank['secret'];
    }

    public function getOpenId($array)
    {
		$this->getAppid($array);
        $arr['appid']=$this->appid;
        $arr['secret']=$this->secret;
        $arr['code']=$array['code'];
        $arr['grant_type']='authorization_code';
        $url='https://api.weixin.qq.com/sns/oauth2/access_token';

        $a=$this->httpGET($arr);
        $a=json_decode($a,true);

		if(!$a['openid']){
			$pay = new paytool();
			$pay->putError('error');
		}
        return $a['openid'];
    }


    public function httpGET($str){

        if (!extension_loaded("curl")) {
            $this->error('curl error',2);
        }
        //构造xml
        $str=http_build_query($str);
        $url = $this->url;
        $url=$str?$url.'?'.$str:$url;

        //初始一个curl会话
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL,$url);
        //抓取URL并把它不传递给浏览器
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //关闭ssl
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        //存取数据
        $file_contents = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);

        return $file_contents;

    }

}
?>