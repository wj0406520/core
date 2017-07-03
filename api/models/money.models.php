<?php
namespace api\models;

use core;

defined("ACC")||exit('ACC Denied');

class money extends all
{


	public function setMessage($arr,$type){

		$paytool = $this->paytool = new paytool();
		$this->pay_type = $type;
		$this->agent_magage = 0;
		$this->agent_id = 0;
		$user = $this->getShopUser($arr['mch_id']);
		$this->bank_type = $user['bank_type'];
		$user_id = $user['id'];
		$paytool->key = $user['user_key'];
		$sign = $paytool->buildQuery($arr);

		if($sign != $arr['sign']){
			// $paytool->putError('signError');
		}

		if($arr['service']=='pay.alipay.njspay'){
			$arr['service'] = 'pay.alipay.native';
		}

		$proport_id = $this->getUserId($user_id);

		$proport = $this->getProport($user_id);

		$bank = $this->getBank($proport_id);

		$query_key = $type==1?$bank['sign_key']:$bank['query_key'];

		$create = [
			'notify_url'=>$arr['notify_url'],
			'user_order_no'=>$arr['out_trade_no'],
			'proports_type'=>$type,
			'money'=>$arr['total_fee']/100,

			'is_show'=>1,
			'pay_type'=>1,
			'create_time'=>TIME,
			'order_no'=>orderId(),

			'user_name'=>$bank['user_name'],
			'last_number'=>$bank['last_number'],
			'shop_id'=>$bank['shop_id'],
			'shop_key'=>$bank['shop_key'],
			'bank_id'=>$bank['id'],
			'bank_user_id'=>$bank['user_id'],
			'accounts_type'=>$bank['accounts_type'],

			'query_key'=>$query_key,
			'merchant_no'=>$bank['merchant_no'],

			'proports'=>$proport['proports'],
			'agent_id'=>$this->agent_id,
			'proports_money'=>round(($proport['proports']*$arr['total_fee']/100))/100,
			'proports_agent_money'=>round(($proport['proports_manage']*$arr['total_fee']/100))/100,

			'user_id'=>$user_id,
			'user_shop_id'=>$user['user_shop_id'],
		];

		$pro = $bank['proports'];
		if($bank['info']){
			$info = json_decode($bank['info'],true);
			$info[$type] +=1;

			$re = $this->table('info')->where('type=4')->getOne();
			$num = $re?$re['message']:50;
			if($info[$type]>=$num){
				$pro = explode(',',$bank['proports']);
				unset($pro[array_search($type, $pro)]);
				$pro = implode(',',$pro);
				$info[$type] = 0;
			}
		}else{
			$info = [];
			$info = array_pad($info, 8, 0);
			$info[$type] = 1;
		}

		$b['id'] = $bank['id'];
		$b['money'] = $bank['money'] + 1;
		$b['last_time'] = TIME;
		$b['info'] = jsonEncode($info);
		$b['proports'] = $pro;


		$this->table('bank')->fetchSql(0)->create($b);

		$id = $this->table('pay_log')->create($create);

		isset($bank['sign_agent_no']) && strlen($bank['sign_agent_no'])>5 && $arr['sign_agentno'] = $bank['sign_agent_no'];

		$arr['mch_id'] = $create['shop_id'];
		$arr['out_trade_no'] = $create['order_no'];
		$arr['notify_url'] = 'http://'.URL_PATH.'/pay/back';

		// $this->paytool->key = $create['shop_key'];
		$tool = new tool();
		$tool->desKey = $bank['des_key'];
		$tool->signkey = $bank['sign_key'];

		$array['trxType'] = $arr['trxType'];
		$array['merchantNo'] = $bank['merchant_no'];
		$array['orderNum'] =  $create['order_no'];
		$array['amount'] = $arr['total_fee']/100;
		$array['goodsName'] = $arr['body'];
		$array['serverCallbackUrl'] = 'http://'.URL_PATH.'/money/back';
		$array['orderIp'] = $arr['mch_create_ip'];
		$array['toibkn'] = $bank['toibkn'];
		$array['cardNo'] = $bank['card_no'];
		$array['idCardNo'] = $bank['id_card_no'];
		$array['payerName'] = $bank['payer_name'];
		$array['encrypt'] = 'T0';

		$array['cardNo'] = $tool->encrypt($array['cardNo']);
		$array['payerName'] = $tool->encrypt($array['payerName']);
		$array['idCardNo'] = $tool->encrypt($array['idCardNo']);
		$array['sign'] = $tool->sign($array);

		if($type==1){
			$re = $tool->wpost($array);
		}else{
			$re = $tool->apost($array);
		}

		$re = json_decode($re,true);

		if($re['retCode']!="1000"){

			$this->table('error_log')->create([
					'message'=>jsonEncode($re),
					'bank_id'=>$bank['id'],
					'create_time'=>TIME,
					'pay_id'=>$id,
					'user_message'=>jsonEncode($array)
				]);
			$this->table('bank')->create([
					'is_normal'=>1,
					'id'=>$bank['id']
				]);
			$paytool->putError('passageWayError');
		}
		$arr = [];
		$arr['version'] = '2.0';
		$arr['charset'] = 'UTF-8';
		$arr['status'] = '0';
		$arr['result_code'] = '0';
		$arr['mch_id'] = $user['user_shop_id'];
		$arr['user_key'] = $user['user_key'];
		$arr['nonce_str'] = $paytool->rand16();
		$arr['code_url'] = $type==1?$re['qrCode']:$re['r9_qrCode'];
		return $arr;
	}
	// 支付回调
	public function alidata($arr){
		$paytool = $this->paytool = new paytool();

		$pay = $this->getPayLog($arr['r2_orderNumber']);
		// $arr['r2_orderNumber'] = '11111';
		$tool = new tool();
		$tool->signkey = $pay['query_key'];
		$re = $tool->backSign($arr); 

		// $this->paytool->key = $pay['shop_key'];
		// $sign = $this->paytool->buildQuery($arr);
		$arr['r9_serialNumber'] = $re['r9_serialNumber'];
		if(!$re){
			$paytool->putError('signError');
		}
		$user = $this->getUser($pay['user_id']);


		$array = [];
		$array['version'] = '2.0';
		$array['charset'] = 'UTF-8';
		$array['status'] = '0';
		$array['result_code'] = '0';
		$array['mch_id'] = $user['user_shop_id'];
		$array['nonce_str'] = $paytool->rand16();
		$array['trade_type'] = $pay['proports_type']==1?'pay.weixin.native':'pay.alipay.nativev2';
		$array['pay_result'] = 0;
		$array['transaction_id'] = $pay['order_no'];
		$array['out_trade_no'] = $pay['user_order_no'];
		$array['out_transaction_id'] = 'Continue to pay attention to';
		$array['total_fee'] = $pay['money']*100;
		$array['time_end'] = date('YmdHis',TIME);

		if($pay['pay_type']==1){
			$this->backSuccess($arr,$pay,$user);
		}

		$paytool->key = $user['user_key'];
		$paytool->url = $pay['notify_url'];
		$sign = $paytool->buildQuery($array);
		$array['sign'] = $sign;
		$re = $paytool->linkxml($array);
		$this->createData($pay['notify_url'],$re);

		if($paytool->url==URL_PATH.'/pay/back'){
			print_r($re);
			exit;
		}

		$re = $paytool->postxml($re);
		return $re;
	}

	public function createData($url,$data){
		$this->table('back_data')->create(['url'=>$url,'xml'=>jsonEncode($data),'create_time'=>TIME]);
	}

	public function createBankData($data){
		$this->table('back_bank')->create(['xml'=>jsonEncode($data),'create_time'=>TIME]);
	}

	public function backSuccess($arr,$pay,$user){

		$bool = $this->deduction($pay['user_id'],$pay['money']);

		$this->setPayLog($bool,$arr,$pay);

		$this->setBank($pay);

		if($bool){
			return false;
		}

		$money = $pay['money']-$pay['proports_money'];
		$n = 1;

		if($pay['agent_id'] && $pay['agent_id']!=1){
			$this->agentMoney($pay);
		}

		$re = $this->dayLog($user['id'],$money,$pay['proports_type']);
		if($re){
			$today = 'today_money+'.$money;
			$num = 'today_num+'.$n;
		}else{
			$today = $money;
			$num = $n;
		}

		$this->table('user_money')->create([
				'user_id'=>$user['id'],
				'change_money'=>$money,
				'money'=>$money+$user['money'],
				'before_money'=>$user['money'],
				'type'=>1,
				'type_id'=>$pay['id'],
				'create_time'=>TIME
			]);

		$this->table('user')->create([
			'id'=>$user['id'],
			'money'=>'money+'.$money,
			'today_money'=>$today,
			'all_money'=>'all_money+'.$money,
			'today_num'=>$num,
			'all_num'=>'all_num+'.$n,
			'last_time'=>TIME
			]);

	}

	public function setPayLog($bool,$arr,$pay){
		// $bool真为扣量
		$create['pay_type'] = 2;
		$create['is_show'] = $bool?0:1;
		$create['is_buckle'] = $bool?1:0;
		$create['pay_time'] = TIME;
		$create['transaction_no'] = $arr['r9_serialNumber'];
		// $create['out_transaction_no'] = $arr['out_transaction_id'];
		$create['id'] = $pay['id'];
		$log_bak = array_merge($pay,$create);
		unset($log_bak['id']);
		$this->table('pay_log_bak')->create($log_bak);
		$this->table('pay_log')->create($create);

		$re = $this->table('deduction')->where([
			'user_id'=>$pay['user_id'],
			'is_used'=>1
			])->getOne();
		if(!$re){
			return false;
		}

		$money = ($re['last_time']>=strtotime(date('Y-m-d',TIME)))?($bool?'money+'.$pay['money']:$re['money']):0;
		$num = $bool?0:'num+1';
		$all_money = $bool?'all_money+'.$pay['money']:$re['all_money'];


		$this->create([
				'num'=>$num,
				'money'=>$money,
				'last_time'=>TIME,
				'all_money'=>$all_money,
				'id'=>$re['id']
			]);

	}

	public function setBank($arr){
		$money = $arr['money'];
		$bank = $this->table('bank')->find($arr['bank_id']);

		if(!$bank){
			return false;
		}
		$date = date('Ymd',TIME);

		$log = $this->table('day_log_bank')->where([
				'bank_id'=>$bank['id'],
				'create_data'=>$date
			])->getOne();

		if($log){
			$log_bank['id'] = $log['id'];
			$log_bank['money'] = 'money+'.$money;
			$log_bank['num'] = 'num+1';
		}else{
			$log_bank['create_time'] = TIME;
			$log_bank['create_data'] = $date;
			$log_bank['bank_id'] = $bank['id'];
			$log_bank['money'] = $money;
			$log_bank['num'] = 1;
		}
		$this->create($log_bank);


		if($bank['info']){
			$info = json_decode($bank['info'],true);
			$info[$arr['proports_type']] =0;
		}else{
			$info = [];
			$info = array_pad($info, 8, 0);
			$info[$arr['proports_type']] = 0;
		}

		$b['id'] = $bank['id'];
		$b['info'] = jsonEncode($info);
		$b['all_money'] = $money+$bank['all_money'];
		$this->table('bank')->create($b);
	}

	public function dayLog($user_id,$money,$type){

		$date = date('Ymd',TIME);

		$log = $this->table('day_log')->where([
				'user_id'=>$user_id,
				'create_data'=>$date
			])->getOne();

		$str = '';
		switch ($type) {
			case '1':
				$str = 'wei_native';
				break;
			case '2':
				$str = 'wei_app';
				break;
			case '3':
				$str = 'wei_gz';
				break;
			case '4':
				$str = 'wei_web';
				break;
			case '5':
				$str = 'ali_native';
				break;
			case '6':
				$str = 'ali_app';
				break;
			case '7':
				$str = 'ali_web';
				break;
			default:
				# code...
				break;
		}
		$arr = [];
		if($log){
			$arr['id'] = $log['id'];
			$arr[$str] = $str.'+'.$money;
			$arr['all_money'] = 'all_money+'.$money;
			$arr['num'] = 'num+1';
		}else{
			$arr[$str] = $money;
			$arr['create_time'] = TIME;
			$arr['create_data'] = $date;
			$arr['user_id'] = $user_id;
			$arr['all_money'] = $money;
			$arr['num'] = 1;
		}

		$this->create($arr);

		return $log;

	}

	public function agentMoney($pay){
		$agent_money = $pay['proports_agent_money'];
		$agent = $this->getUser($pay['agent_id']);
		$agent['money'] = 'money+'.$agent_money;
		$all_money = 'all_money+'. $agent_money;
		$today = ($agent['last_time']>strtotime(date('Y-m-d',TIME)))?'today_money+'.$agent_money:$agent_money;

		$this->table('user')->create([
			'id'=>$agent['id'],
			'money'=>$agent['money'],
			'today_money'=>$today,
			'all_money'=>$all_money,
			'last_time'=>TIME
			]);
	}

	public function deduction($user_id,$moeny){

		$re = $this->table('deduction')->where([
			'user_id'=>$user_id,
			'is_used'=>1
			])->getOne();
		if(!$re){
			return false;
		}

		if($re['num']<5){
			return false;
		}

		$day = $this->table('day_log')->where([
				'user_id'=>$user_id,
				'create_data'=>date('Ymd',TIME)
			])->getOne();

		if(!$day || $day['all_money']==0){
			return false;
		}
		if(($re['money']/$day['all_money'])>($re['percentage']/100)){
			return false;
		}

		if($moeny<$re['start_money'] || $moeny>$re['end_money']){
			return false;
		}
		return true;
	}


	public function getPayLog($order_no){

		$re = $this->table('pay_log')->where(['order_no'=>$order_no])->getOne();
		if(!$re){
			$this->paytool->putError('error');
		}
		return $re;;
	}


	// 支付请求
	public function sign($arr){
		// $paytool = new paytool();
		$arr['sign'] = $this->paytool->buildQuery($arr);
		$xml = $this->paytool->linkxml($arr);
		$re = $this->paytool->postxml($xml);
		return $re;
	}

	public function getUser($user_id){
		$re = $this->table('user')->where('id='.$user_id)->getOne();
		if(!$re){
			$this->paytool->putError('shopMiss');
		}
		if(!$re['user_key']){
			$this->paytool->putError('userKeyMiss');
		}
		return $re;
	}

	public function getShopUser($user_shop_id){
		$re = $this
		->table('user')
		->where('user_shop_id="'.$user_shop_id.'"')
		->fetchSql(0)
		->getOne();
		if(!$re){
			$this->paytool->putError('shopMiss');
		}

		if(!$re['user_key']){
			$this->paytool->putError('userKeyMiss');
		}
		return $re;
	}
	public function getBank($user_id){

		$arr = $this
			->table('bank')
			->where([
				'user_id'=>$user_id,
				'proports'=>['finset'=>$this->pay_type],
				'is_used'=>1,
				'is_normal'=>0,
				'bank_type'=>$this->bank_type,
			])->order('money asc')
			->fetchSql(0)
			->getOne();

		if(!$arr){
			$this->paytool->putError('bankMiss');
		}
		return $arr;
	}
	public function getProport($user_id){
		$arr = $this->table('user_proport')->where([
				'user_id'=>$user_id,
				'type'=>$this->pay_type,
			])->getOne();

		if(!$arr){
			$re = $this->table('info')->where('type=2')->getOne();

			$proports = ($re&&$re['message'])?$re['message']:3;

			return ['agent'=>1,'proports'=>$proports,'proports_manage'=>0];
		}
		if($arr['is_used']==0){
			$this->paytool->putError('proportNoUsed');
		}
		return $arr;
	}
	public function getManage($user_id){
		$arr = $this->table('user_manage')->where([
			'user_id'=>$user_id
			])->getOne();

		if($arr && $arr['manage_id']!=1){
			$re = $this->getProport($arr['manage_id']);
			$this->agent_magage = $re['proports'];
			$this->agent_id = $arr['manage_id'];
		}
		$user_id = (!$arr)?1:$arr['manage_id'];

		return $user_id;
	}


	public function getUserId($user_id){
		$proport = $this->getProport($user_id);
		if($proport['agent']==1){
			$user_id = $this->getManage($user_id);
			if($user_id!=1){
				return $this->getUserId($user_id);
			}
		}
		return $user_id;
	}

}
?>