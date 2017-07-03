<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class paytext extends all
{

	public $check=0;



	public function indexAction()
	{
		// channelName	渠道名称	是		由我司运营人员或业务人员提供
		// channelNo	渠道编码	是		由我司运营人员或业务人员提供
		// merchantName	商户名称	是		
		// merchantBillName	签购单显示名称	是		

		// installProvince	安装归属省	是		
		// installCity	安装归属市	是		
		// installCounty	安装归属县（区）	是		
		// operateAddress	经营地址	是		

		// merchantType	商户类型	是		ENTERPRISE -企业商户
		// INSTITUTION -事业单位商户
		// INDIVIDUALBISS -个体工商户
		// PERSON -个人商户
		// businessLicense	营业执照号码	否		非个人商户时必填
		// legalPersonName	法人代表姓名	是		个人用户请填写身份证姓名
		// legalPersonID	法人代表身份证号	是		个人用户请填写身份证号
		// merchantPersonName	商户联系人姓名	是		
		// merchantPersonPhone	商户联系人电话	是		
		// merchantPersonEmail	商户联系人邮箱	否		可不传，若传，全局唯一
		// wxType	微信经营类目	是		开通时必填
		// wxT1Fee	微信商户手T1续费	是		开通时必填(例子：0.0038)
		// wxT0Fee	微信商户手T0续费	是		开通时必填(例子：0.0038)
		// alipayType	支付宝经营类目	是		开通时必填
		// alipayT1Fee	支付宝商户手T1续费	是		开通时必填(例子：0.0038)
		// alipayT0Fee	支付宝商户手T0续费	是		开通时必填(例子：0.0038)
		// bankType	结算账户性质	是		对公-TOPUBLIC
		// 对私-TOPRIVATE
		// accountName	开户名称	是		张三
		// accountNo	开户账号	是		3DES加密
		// bankName	开户银行名（大行全称）	是		上海浦东发展银行
		// bankProv	开户行省	是		
		// bankCity	开户行市	是		
		// bankBranch	开户银行名称（精确到支行）	是		上海浦东发展银行哈尔滨分行营业部
		// bankCode	联行号	是		
		// creditCardNo	结算人信用卡	否		个人商户填写验证通过则提高日限额(3DES加密)
		// remarks	备注	否		该域原样返回
		// expand1	备用1	否		
		// expand2	备用2	否		
		// sign	签名	是		MD5全文签名
		$array['channelName'] = '典韵科技';
		$array['channelNo'] = 'C2534348891';
		$array['merchantName'] = '天韵贸易';
		$array['merchantBillName'] = '天韵贸易';

		$array['installProvince'] = '安徽省';
		$array['installCity'] = '合肥市';
		$array['installCounty'] = '蜀山区';
		$array['operateAddress'] = '合肥市蜀山区长江西路';

		$array['merchantType'] = 'PERSON';
		// $array['businessLicense'] = '340100001036142'; //营业执照号码
		$array['legalPersonName'] = '王志龙';
		$array['legalPersonID'] = '340822199109261118';

		$array['merchantPersonName'] = '王志龙';
		$array['merchantPersonPhone'] = '15375256050';
		// $array['merchantPersonEmail'] = '2411001@qq.com';

		$array['wxType'] = '204';
		$array['wxT1Fee'] = '0.0038';
		$array['wxT0Fee'] = '0.0038';

		$array['alipayType'] = '2016062900190191';
		$array['alipayT1Fee'] = '0.0038';
		$array['alipayT0Fee'] = '0.0038';

		$array['bankType'] = 'TOPRIVATE';

		$array['accountName'] = '王志龙';
		$array['accountNo'] = '6225223380213854';
		$array['bankName'] = '上海浦东发展银行';
		$array['bankProv'] = '安徽省';
		$array['bankCity'] = '合肥市';
		$array['bankBranch'] = '上海浦东发展银行股份有限公司合肥新站区支行';

		$array['bankCode'] = '310361000038';
		// $array['creditCardNo'] = '上海浦东发展银行哈尔滨分行营业部';
		// $array['remarks'] = '310361000038';

		header('Content-Type:text/html;charset=utf-8');
		ksort($array);
		$tool = $this->models('tool');
		$array['accountNo'] = $tool->encrypt($array['accountNo']);
		// $array['creditCardNo'] = $tool->encrypt($array['creditCardNo']);
		$array['sign'] = $tool->signMerchant($array);
		$re = $tool->post($array);

		// print_r(jsonEncode($array));
		print_r($array);
		print_r($re);

	}

	public function selectAction()
	{
		// 1	channelName	渠道名称	是		由我司运营人员或业务人员提供	1
		// 2	channelNo	渠道编码	是		由我司运营人员或业务人员提供	2
		// 3	merchantNo	商户编号	是			3
		// 4	sign	MD5签名	是		与入件一样的签名方法	4

		$array['channelName'] = '典韵科技';
		$array['channelNo'] = 'C2534348891';
		$array['merchantNo'] = 'B101263831';
		ksort($array);
		$tool = $this->models('tool');
		$array['sign'] = $tool->signMerchant($array);
		$re = $tool->spost($array);

		header('Content-Type:text/html;charset=utf-8');
		print_r($array);
		print_r($re);
	}

	public function wxAction()
	{
		// 1	trxType	接口类型	是	固定值	WX_SCANCODE微信扫码
		// WX_SCANCODE_JSAPI微信公众号
		// WX_PASSIVE微信终端号支付
		// QQPAY_SCANCODE  QQ钱包扫码
		// QQPAY_SCANCODE_JSAPI
		// QQ钱包公众号
		// QQPAY_PASSIVE   QQ钱包条形码	1
		// 2	merchantNo	商户编号	是	10位	B1000001	2
		// 3	orderNum	商户订单号	是	1-50位	R_12345	3
		// 4	amount	金额	是	元为单位	100.00	4
		// 5	goodsName	订单描述	是	<150	一条裤子	5
		// 6	timeOut	订单失效时间	否	yyyyMMddHHmmss	20160906231010	6
		// 7	callbackUrl	页面回调地址	否	<300	http://c.a.com/page	7
		// 8	serverCallbackUrl	服务器回调地址	否	<300	http://c.a.com/server	8
		// 9	orderIp	用户支付IP	是	Ip地址	1.1.1.1	9
		// 10	toibkn	收款行联行号	否	长度0-12		10
		// 11	cardNo	入账卡号	否		3des加密后，使用base64，utf8编码做加密。	11
		// 12	idCardNo	入帐卡对应身份证号	否		同上	12
		// 13	payerName	入帐卡对应姓名	否		同上	13
		// 14	encrypt	T0/T1标识，若此项为T0，对应的10,11,12,13必填	是		T0	14
		// 15	authCode
		// 	授权码(终端号支付时必填)	否			15
		// 16	sign	签名	是	=32		16

		$array['trxType'] = 'WX_SCANCODE';
		$array['merchantNo'] = 'B101263831';
		$array['orderNum'] = '123456';
		$array['amount'] = '0.01';
		$array['goodsName'] = '测试商品';
		$array['serverCallbackUrl'] = 'http://120.55.171.181:9527/pay/api/text/wback';
		$array['orderIp'] = '114.16.253.21';
		$array['toibkn'] = '310361000038';
		$array['cardNo'] = '6225223380213854';
		$array['idCardNo'] = '340822199109261118';
		$array['payerName'] = '王志龙';
		$array['encrypt'] = 'T0';
		header('Content-Type:text/html;charset=utf-8');
		// ksort($array);
		$tool = $this->models('tool');
		$tool->desKey = 'RPhnpUZt7ym9HxIJ1x1pU43w';
		$tool->signkey = 'kqymsFAdmYWs8WSydfhMF7ozf5pSX29R';
		$array['cardNo'] = $tool->encrypt($array['cardNo']);
		$array['payerName'] = $tool->encrypt($array['payerName']);
		$array['idCardNo'] = $tool->encrypt($array['idCardNo']);
		$array['sign'] = $tool->sign($array);
		$re = $tool->wpost($array);
		print_r($array);
		print_r($re);
	}

	public function aliAction()
	{
		// 1	trxType	接口类型	是	固定值	Alipay_SCANCODE 支付宝扫码
		// Alipay_SCANCODE_JSAPI 支付宝服务窗
		// Alipay_PASSIVE支付宝终端号支付	1
		// 2	merchantNo	商户编号	是	10位	B1000001	2
		// 3	orderNum	商户订单号	是	1-50位	R_12345	3
		// 4	amount	金额	是	元为单位	100.00	4
		// 5	goodsName	订单描述	是	<150	一条裤子	5
		// 6	timeOut	订单失效时间	否	yyyyMMddHHmmss	20160906231010	6
		// 7	callbackUrl	页面回调地址	否	<300	http://c.a.com/page	7
		// 8	serverCallbackUrl	服务器回调地址	否	<300	http://c.a.com/server	8
		// 9	orderIp	用户支付IP	是	Ip地址	1.1.1.1	9
		// 10	toibkn	收款行联行号	否	长度0-12		10
		// 11	cardNo	入账卡号	否		3des加密后，使用base64，utf8编码做加密。	11
		// 12	idCardNo	入帐卡对应身份证号	否		同上	12
		// 13	payerName	入帐卡对应姓名	否		同上	13
		// 14	encrypt	T0/T1标识，若此项为T0，对应的10,11,12,13必填	是		T0	14
		// 15	authCode	授权码(终端号支付时必填)	否			15
		// 16	sign	签名	是	=32		16
		$array['trxType'] = 'Alipay_SCANCODE';
		$array['merchantNo'] = 'B101263831';
		$array['orderNum'] = '111111';
		$array['amount'] = '0.01';
		$array['goodsName'] = '测试商品';
		$array['serverCallbackUrl'] = 'http://120.55.171.181:9527/pay/api/text/wback';
		$array['orderIp'] = '114.16.253.21';
		$array['toibkn'] = '310361000038';
		$array['cardNo'] = '6225223380213854';
		$array['idCardNo'] = '340822199109261118';
		$array['payerName'] = '王志龙';
		$array['encrypt'] = 'T0';
		header('Content-Type:text/html;charset=utf-8');
		// ksort($array);
		$tool = $this->models('tool');
		$tool->desKey = 'RPhnpUZt7ym9HxIJ1x1pU43w';
		$tool->signkey = 'kqymsFAdmYWs8WSydfhMF7ozf5pSX29R';
		$array['cardNo'] = $tool->encrypt($array['cardNo']);
		$array['payerName'] = $tool->encrypt($array['payerName']);
		$array['idCardNo'] = $tool->encrypt($array['idCardNo']);
		$array['sign'] = $tool->sign($array);
		$re = $tool->apost($array);
		print_r($array);
		print_r($re);
	}
	public function abackAction()
	{
		// 支付宝
		// 1	trxType	接口类型	是	固定值	OnlineQuery
		// 2	retCode	处理结果码	是		详见附录
		// 3	retMsg	处理结果描述	否		
		// 4	r1_merchantNo	商户编号	是		B100001
		// 5	r2_orderNumber	商户订单号	是		原下单订单号
		// 6	r3_amount	金额	是		100.00
		// 7	r4_bankId	银行编码	是	固定值	Alipay
		// 8	r5_business	业务	是	固定值	Alipay
		// 9	r6_createDate	系统创建订单时间	是		
		// 10	r7_completeDate	系统订单完成时间	是		
		// 11	r8_orderStatus	订单状态	是		
		// 12	r9_withdrawStatus	平台序列号	是		
		// 13	sign	签名	是	=32	
		// {"sign":"1de24af200c416ccd7d9f28a549b9539","r1_merchantNo":"B101263831","r3_amount":"0.01","retCode":"0000","r6_createDate":"2017-05-24 18:09:24","r7_completeDate":"2017-05-24 18:10:35","r5_business":"Alipay","r2_orderNumber":"11111","trxType":"OnlineQuery","r9_withdrawStatus":"WAITING","r4_bankId":"Alipay","r8_orderStatus":"SUCCESS"}

		$arr = '{"sign":"1de24af200c416ccd7d9f28a549b9539","r1_merchantNo":"B101263831","r3_amount":"0.01","retCode":"0000","r6_createDate":"2017-05-24 18:09:24","r7_completeDate":"2017-05-24 18:10:35","r5_business":"Alipay","r2_orderNumber":"11111","trxType":"OnlineQuery","r9_withdrawStatus":"WAITING","r4_bankId":"Alipay","r8_orderStatus":"SUCCESS"}';

		$arr = json_decode($arr,true);

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


		$tool = $this->models('tool');
		$tool->signkey = '3xfrqbZMgXb1aMbI6EFZk1sVmX11vl0G';
		$sign = $tool->sign($array);
		// print_r($arr);
		// p($sign);
		// p($arr['sign']);
		if(strtoupper($arr['sign'])!=$sign){
			return false;
		}
	}

	public function wback()
	{

		// 微信
		// 1	trxType	接口类型	是	固定值	WX_SCANCODE
		// 2	retCode	处理结果码	是		详见附录
		// 3	retMsg	处理结果描述	否		
		// 4	r1_merchantNo	商户编号	是		B100001
		// 5	r2_orderNumber	商户订单号	是		原下单订单号
		// 6	r3_amount	金额	是		100.00
		// 7	r4_bankId	银行编码	是	固定值	WX
		// 8	r5_business	业务	是	固定值	WX
		// 9	r6_timestamp	系统创建订单时间	是		
		// 10	r7_completeDate	系统订单完成时间	是		
		// 11	r8_orderStatus	订单状态	是		
		// 12	r9_serialNumber	平台序列号	是		
		// 13	r10_t0PayResult	T0，T1标识	是		
		// 14	sign	签名	是	=32	

		$arr = json_decode($arr,true);

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

		$tool = $this->models('tool');
		$tool->signkey = '3xfrqbZMgXb1aMbI6EFZk1sVmX11vl0G';
		$sign = $tool->sign($array);
		if(strtoupper($arr['sign'])!=$sign){
			return false;
		}
	}
}
?>