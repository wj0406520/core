<?php

/**
 * 微信模版
 */


namespace api\models;

use core;
use tool\HttpTool;

defined('ACC')||exit('ACC Denied');


class tool extends core\Models
{

    public $desKey = 'Cu7926a5L1o15cB2f78b5op4';
    public $signkey = '854z6LT4m5L71Qj9q0V020k057BUm4Mf'; 
    public $channel_name = '典韵科技';
    public $channel_no = 'C2534348891';
    public $merchant_type = 'PERSON';
    public $bank_type = 'TOPRIVATE';
    public $url = 'http://real.izhongyin.com/middlepayportal/merchant/in'; //申请代理商接口
    public $surl = 'http://real.izhongyin.com/middlepayportal/merchant/query';//查询商户
    public $wurl = 'http://real.izhongyin.com/middlepaytrx/wx/scanCode'; //微信扫码地址
    public $aurl = 'http://real.izhongyin.com/middlepaytrx/alipay/scanCode'; //支付宝扫码地址

    public function encrypt($strinfo){//数据加密
        $desKey = $this->desKey;
        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
        $strinfo = $this->pkcs5Pad($strinfo, $size);
        $key = str_pad($desKey,24,'0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $strinfo);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //    $data = base64_encode($this->PaddingPKCS7($data));
        $data = base64_encode($data);
        $data = str_replace('\\', '', $data);
        return $data;
    }

    public function pkcs5Pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public function sign($arr){
        $str = implode('#', $arr);
        $str .='#'.$this->signkey;
        $str = '#'.$str;
        $str = str_replace('\\', '', $str);
        // print_r($str);
        $str = strtoupper(md5($str));
        return $str;
    }

    public function signMerchant($arr){
        $str = jsonEncode($arr);
        $str .= $this->signkey; 
        $str = str_replace('\\', '', $str);
        // print_r($str);
        $str = strtoupper(md5($str));
        return $str;
    }

    public function post($arr){
        $re = HttpTool::postJson($this->url,$arr);
        return $re;
    }

    public function spost($arr){
        $re = HttpTool::postJson($this->surl,$arr);
        return $re;
    }

    public function wpost($arr){
        $re = HttpTool::get($this->wurl,$arr);
        return $re;
    }

    public function apost($arr){
        $re = HttpTool::get($this->aurl,$arr);
        return $re;
    }

    public function backSign($arr){
        if($arr['trxType']=='WX_SCANCODE'){
            return $this->wback($arr);
        }else if($arr['trxType']=='OnlineQuery'){
            return $this->aback($arr);
        }else{
            return false;
        }
    }

    public function aback($arr){

        if(!isset($arr['retCode']) && $arr['retCode']!=="0000"){
            return false;
        }
        $array['trxType'] = $arr['trxType'];
        $array['retCode'] = $arr['retCode'];
        $array['r1_merchantNo'] = $arr['r1_merchantNo'];
        $array['r2_orderNumber'] = $arr['r2_orderNumber'];
        $array['r3_amount'] = $arr['r3_amount'];
        $array['r4_bankId'] = $arr['r4_bankId'];
        $array['r5_business'] = $arr['r5_business'];
        $array['r6_createDate'] = $arr['r6_createDate'];
        $array['r7_completeDate'] = $arr['r7_completeDate'];
        $array['r8_orderStatus'] = $arr['r8_orderStatus'];
        $array['r9_withdrawStatus'] = $arr['r9_withdrawStatus'];

        // $this->signkey = $signkey;
        $sign = $this->sign($array);
        // print_r($arr);
        // p($sign);
        // p($arr['sign']);
        if(strtoupper($arr['sign'])!=$sign){
            return false;
        }
        $array['r9_serialNumber'] = $arr['r9_withdrawStatus'];
        return $array;
    }

    public function wback($arr){

        if(!isset($arr['retCode']) && $arr['retCode']!=="0000"){
            return false;
        }
        $array['trxType'] = $arr['trxType'];
        $array['retCode'] = $arr['retCode'];
        $array['r1_merchantNo'] = $arr['r1_merchantNo'];
        $array['r2_orderNumber'] = $arr['r2_orderNumber'];
        $array['r3_amount'] = $arr['r3_amount'];
        $array['r4_bankId'] = $arr['r4_bankId'];
        $array['r5_business'] = $arr['r5_business'];
        $array['r6_timestamp'] = $arr['r6_timestamp'];
        $array['r7_completeDate'] = $arr['r7_completeDate'];
        $array['r8_orderStatus'] = $arr['r8_orderStatus'];
        $array['r9_serialNumber'] = $arr['r9_serialNumber'];
        $array['r10_t0PayResult'] = $arr['r10_t0PayResult'];

        // $this->signkey = $signkey;
        $sign = $this->sign($array);
        if(strtoupper($arr['sign'])!=$sign){
            return false;
        }
        return $array;
    }
}
?>