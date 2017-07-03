<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class money extends all
{

	public $check = 0;
	public $paytool = '';

	public function payAction(){
		// $this->text();

		$paytool = $this->paytool = new \api\models\paytool();

		$re = file_get_contents('php://input');
		if(!$re){
			$paytool->putError('noData');
		}
		$arr = $paytool->getxml($re);
		if(!isset($arr['service'])){
			$paytool->putError('serviceMiss');
		}
		if(!isset($arr['mch_id'])){
			$paytool->putError('mchIdMiss');
		}
		if(!isset($arr['body'])){
			$paytool->putError('bodyMiss');
		}
		if(!isset($arr['total_fee'])){
			$paytool->putError('totalFeeMiss');
		}
		if($arr['total_fee']<=0){
			$paytool->putError('totalFeeError');
		}
		// if($arr['total_fee']>=50000){
		// 	$paytool->putError('totalFeeError');
		// }
		if(floor($arr['total_fee'])!=$arr['total_fee']){
			$paytool->putError('totalFeeInt');
		}
		$arr['total_fee'] = floor($arr['total_fee']);
		if(!isset($arr['out_trade_no'])){
			$paytool->putError('outTradeNoMiss');
		}
		if(!isset($arr['notify_url'])){
			$paytool->putError('notifyUrlMiss');
		}
		if(!isset($arr['sign'])){
			$paytool->putError('signMiss');
		}
		if(!isset($arr['mch_create_ip'])){
			$paytool->putError('mchIpMiss');
		}
		if(strlen($arr['mch_create_ip'])>16){
			$paytool->putError('mchIpError');
		}

		$this->post_date = $arr;
		$re = '';

		switch ($arr['service']) {
			case 'pay.weixin.native':
				$re = $this->weixinNative();
				break;
			case 'pay.weixin.jspay':
				$re = $this->weixinJspay();
				break;
			case 'pay.alipay.native':
				$re = $this->alipayNative();
				break;
			case 'pay.alipay.jspay':
				$re = $this->alipayJspay();
				break;
			case 'pay.alipay.njspay':
				$re = $this->alipaynJspay();
				break;
			case 'pay.weixin.wappay':
				$re = $this->weixinWappay();
				break;
			default:
				$paytool->putError('serviceError');
				break;
		}
		// print_r($re);
		echo $re;
		exit;
	}
	public function jspayAction()
	{

		$check=$this->handle([
				'token_id'=>['fill','string',''],
			]);
		if(!$check['token_id']){
			$this->errorMsg('error');
		}

		$url = 'https://pay.swiftpass.cn/pay/jspay?token_id='.$check['token_id'].'&showwxpaytitle=1';
		$this->redirect($url);
	}

	public function weppayAction()
	{
		$check=$this->handle([
				'token_id'=>['fill','string',''],
			]);
		if(!$check['token_id']){
			$this->errorMsg('error');
		}
		$url = 'https://statecheck.swiftpass.cn/pay/wappay?token_id='.$check['token_id'].'&service=pay.weixin.wappayv3';
		$this->redirect($url);
	}

	public function weijsAction()
	{

		$check=$this->handle([
				'code'=>['fill','string',''],
			]);
		$paytool = new \api\models\paytool();
		$user = $this->models->getShopUser($_GET['mch_id']);
		$paytool->key = $user['user_key'];
		if(!$check['code']){
			$callback_url = $_GET['callback_url'];
			unset($_GET['callback_url']);
			$sign = $paytool->buildQuery($_GET);
			if($sign != $_GET['sign']){
				$paytool->putError('signError');
			}
			$_GET['callback_url'] = $callback_url;

			$this->redirect('/wx/code',$_GET);
		}

		$wx = new \api\models\wx();

		$arr = $_GET;
		$arr['sub_openid'] = $wx->getOpenId($arr);
		unset($arr['code']);
		unset($arr['state']);
		// $arr['sign'] = 'D00B593A92A9FBE206033DDFF9F50127';

		$paytool->url = URL_PATH.'/pay/gateway';
		$arr['is_raw'] = 1;
		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);

		$xml = $paytool->postxml($re);

		$arr = $paytool->getxml($xml);

		//print_r($arr['token_id']);
		//exit;

		if(isset($arr['token_id']) && $arr['token_id']){
			$url = 'https://pay.swiftpass.cn/pay/jspay?token_id='.$arr['token_id'].'&showwxpaytitle=1';
			//$str = file_get_contents($url);
			$this->redirect($url);
		}
		echo $xml;
	}



	public function backAction()
	{

// 		 $re = '<xml><buyer_logon_id><![CDATA[may***@qq.com]]></buyer_logon_id>
// <buyer_user_id><![CDATA[2088521209011179]]></buyer_user_id>
// <charset><![CDATA[UTF-8]]></charset>
// <fee_type><![CDATA[CNY]]></fee_type>
// <mch_id><![CDATA[101510023890]]></mch_id>
// <nonce_str><![CDATA[1490607258105]]></nonce_str>
// <openid><![CDATA[2088521209011179]]></openid>
// <out_trade_no><![CDATA[MA80321490606891492334]]></out_trade_no>
// <out_transaction_id><![CDATA[2017032721001004170243227596]]></out_transaction_id>
// <pay_result><![CDATA[0]]></pay_result>
// <result_code><![CDATA[0]]></result_code>
// <sign><![CDATA[9C3013A2266FD1B0E6FF0E3660431BE5]]></sign>
// <sign_type><![CDATA[MD5]]></sign_type>
// <status><![CDATA[0]]></status>
// <time_end><![CDATA[20170327172823]]></time_end>
// <total_fee><![CDATA[1]]></total_fee>
// <trade_type><![CDATA[pay.alipay.native]]></trade_type>
// <transaction_id><![CDATA[101510023890201703273136699274]]></transaction_id>
// <version><![CDATA[2.0]]></version>
// </xml>';
		// $re = file_get_contents('php://input');
		$re = $_POST;
		// $arr = '{"sign":"0f2e87e37cd05d8a85f8062d98673ea5","r1_merchantNo":"B101263831","r3_amount":"0.01","retCode":"0000","r6_createDate":"2017-05-26 10:15:19","r7_completeDate":"2017-05-26 10:19:32","r5_business":"Alipay","r2_orderNumber":"M6AAFF1495764918842397","trxType":"OnlineQuery","r9_withdrawStatus":"WAITING","r4_bankId":"Alipay","r8_orderStatus":"SUCCESS"}';

		// $re = json_decode($arr,true);
		$paytool = $this->paytool = new \api\models\paytool();
		if(!$re){
			$paytool->putError('noData');
		}
		$this->models->createBankData($re);
		// $arr = $paytool->getxml($re);
		$arr = $re;

		$re = $this->models->alidata($arr);
		echo $re;
		exit;

	}


	public function weixinNative()
	{
		$type = 1;
		$this->post_date['trxType'] = 'WX_SCANCODE';
		$arr = $this->setMessage($type);
				// URL_PATH.'/qrcode?str='.
		if($arr['status']==0 && $arr['result_code']==0){
			$arr['code_img_url'] = 'http://'.URL_PATH.'/qrcode/index?str='.$arr['code_url'];
		}
		$re = $this->signParame($arr);
		return $re;

	}
	public function weixinJspay()
	{
		$paytool->putError('proportNoUsed');
		if(isset($arr['sub_openid'])){
			$paytool->putError('subOpenidMiss');
		}
		$type = 3;
		$arr = $this->setMessage($type);
		$re = $this->signParame($arr);
		return $re;

	}
	public function weixinWappay()
	{
		$paytool->putError('proportNoUsed');
		$type = 4;
		$arr = $this->setMessage($type);
				// URL_PATH.'/qrcode?str='.
		if($arr['status']==0 && $arr['result_code']==0){
			$str = parse_url($arr['pay_info']);
			parse_str($str['query'], $re);
			$arr['pay_info'] = 'http://'.URL_PATH.'/pay/weppay?token_id='.$re['token_id'];
		}

		$re = $this->signParame($arr);
		return $re;

	}

	public function alipayNative()
	{
		$type = 5;
		$this->post_date['trxType'] = 'Alipay_SCANCODE';
		$arr = $this->setMessage($type);
		
		if($arr['status']==0 && $arr['result_code']==0){
			$arr['code_img_url'] = 'http://'.URL_PATH.'/qrcode/index?str='.$arr['code_url'];
		}
		$re = $this->signParame($arr);
		return $re;
	}
	public function alipaynJspay()
	{
		$paytool->putError('proportNoUsed');
		$type = 6;
		$arr = $this->setMessage($type);


		if($arr['status']==0 && $arr['result_code']==0){
			$arr['code_img_url'] = 'http://'.URL_PATH.'/qrcode/index?str='.$arr['code_url'];
		}
		$re = $this->signParame($arr);
		return $re;
	}

	public function alipayJspay()
	{
		$paytool->putError('proportNoUsed');
		$type = 6;
		$arr = $this->setMessage($type);

		$re = $this->signParame($arr);
		return $re;
	}

	public function signParame($arr){
		$this->paytool->key = $arr['user_key'];
		unset($arr['user_key']);
		$arr['sign'] = $this->paytool->buildQuery($arr);
		$re = $this->paytool->linkxml($arr);
		return $re;
	}

	public function setMessage($type){

		$arr = $this->models->setMessage($this->post_date,$type);
		return $arr;
	}


}
?>