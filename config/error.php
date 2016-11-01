<?php
/**
 * author 王杰
 * time 2016-11-01
 * version 3.0.1
 * 错误信息
 * error.php
 */

defined('ACC')||exit('ACC Denied');
return array(

		'token'=>array(200,'token丢失'),

		'success'=>array(100,'操作成功'),

		'phone'=>array(10001,'请输入正确的手机号码'),
		'password'=>array(10002,'密码不能少于6位，多于16位'),
		'login'=>array(10003,'登录失败，账户或者密码错误'),
		'havuse'=>array(10004,'当前账户已注册'),
		'username'=>array(10005,'用户名不能少于2位，大于6位'),
		'name'=>array(10006,'姓名不能少于2位，大于6位'),
		'code'=>array(10007,'验证码错误'),
		'cardhad'=>array(10008,'银行卡已存在，请勿重复提交'),
		'usermiss'=>array(10009,'帐号不存在'),
		'card'=>array(10010,'身份证验证错误'),
		'cardHad'=>array(10011,'当前身份已认证'),
		'nopay'=>array(10012,'当前用户未身份认证，无法购买'),

		'tokenMiss'=>array(20001,'token不存在'),
		'msgapierror'=>array(20002,'短信接口返回失败'),
		'msgerror'=>array(20003,'短信未验证，30分钟有效'),
		'phonecodeerror'=>array(20004,'短信验证失败'),

		'fileMax'=>array(30001,'上传文件超出大小限制'),
		'fileType'=>array(30002,'上传文件类型不对'),
		'fileFail'=>array(30003,'上传文件失败'),
		'fileNo'=>array(30004,'没找到上传文件'),

		'fxMiss'=>array(30010,'分销商不存在'),
		'fxError'=>array(30011,'分销商错误'),
		'fxNo'=>array(30012,'已经分销此分类，不能再次分销了'),
		'fxLevel'=>array(30013,'当前分销商此分类不能在分销了'),
		'fxUserNo'=>array(30014,'当前用户还不是分销商'),
		'fxUserYes'=>array(30014,'当前用户已经是分销商'),

		'classifyError'=>array(30100,'分销id错误'),
		'manOver'=>array(30101,'当前人数已满'),

		'serverMiss'=>array(40001,'没有该服务'),
		'addCartError'=>array(40002,'添加购物车失败'),
		'deleteCartError'=>array(40003,'删除购物车失败'),
		'cartIdMiss'=>array(40005,'购物车id不存在'),
		'cartIdError'=>array(40006,'购物车id错误'),
		'shopMiss'=>array(40101,'没有该商品'),

		'orderMiss'=>array(50001,'订单id丢失'),
		'addressMiss'=>array(50002,'用户地址丢失'),
		'payError'=>array(50003,'支付失败'),
		'orderPayd'=>array(50004,'此单已经支付，请勿重复支付'),
		'deleteFail'=>array(50006,'删除失败'),

		'evaluatError'=>array(60001,'评价失败'),
		'evaluatNo'=>array(60002,'此单不能评价'),
		'typeError'=>array(60003,'无法识别的类型'),
		'collected'=>array(60004,'已经收藏，请勿重复操作'),

		'timeError'=>array(70001,'时间参数错误'),
		'timeSelect'=>array(70002,'预约时间已经被选择'),
		'yuyueMiss'=>array(70003,'预约商品丢失'),

		'huodongMiss'=>array(80001,'活动丢失'),
		'rscw'=>array(80002,'报名人数错误'),
		'huodongJz'=>array(80003,'活动报名时间已经截至'),
		'huodongNum'=>array(80004,'活动剩余人数不足报名人数'),

		'orderSh'=>array(90001,'当前订单不能收货'),
		'orderCouSh'=>array(90002,'当前订单没有优惠券'),

		'coupon_add_error'=>array(11001,'添加优惠券失败'),

		'bankCardError'=>array(11002,'银行卡错误'),
		'bankCardMiss'=>array(11003,'银行卡丢失'),
		'deleteBankError'=>array(11004,'删除银行卡失败'),
		'takenmoenyerror'=>array(11005,'提现金额不能小于100'),
		'moneyLack'=>array(11006,'提现金额不足'),
		'lackMoney'=>array(11007,'瀚客币不足，不能支付'),
		'backshopError'=>array(11008,'退货参数错误'),
		'backOrderError'=>array(11009,'当前订单不能退货'),
		'paytype'=>array(11010,'支付类型错误'),
		'yuyueError'=>array(11011,'当前预约不能退款'),
		'huodongError'=>array(11012,'当前活动不能退款'),
		'backOrderCancelError'=>array(11013,'当前订单不能取消'),
		'backOrderTimeError'=>array(11014,'当前订单不能退货，7天内可退款'),
		'backError'=>array(11015,'退单错误'),

	);