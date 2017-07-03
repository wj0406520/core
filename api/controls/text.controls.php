<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class text extends all
{

	public $check=0;

	public function logAction(){

		header('Content-Type:text/html; charset=UTF-8');
		set_time_limit(0);
		$buffer = ini_get('output_buffering');
		echo str_repeat(' ',$buffer+1);     //防止浏览器缓存
		ob_end_flush();     //关闭缓存


		$a = true;
		$id = 1;
		$this->models->str = 'select * from j_log_admin where session_data =\'{"id":"42","type":"1","name":"服装电商3"}\'';
		$re = $this->models->diySelect();
		// $this->models->str = 'select * from j_log_admin where session_data =\'{"id":"68","type":"1","name":"CP17"}\'';
		// $re2 = $this->models->diySelect();
		// $re = array_merge($re,$re2);
		echo '<table>';
		echo '<tr><td>id</td><td>url</td><td>time</td><td>time2</td><td>post</td><td>get</td></tr>';
		foreach ($re as $key => $value) {

			$t = date('Y-m-d H:i:s',$value['create_time']);
			echo '<tr>';
			echo "<td>{$value['id']}</td>";
			echo "<td>{$value['url']}</td>";
			echo "<td>{$t}</td>";
			echo "<td>{$value['create_time']}</td>";
			echo "<td>{$value['post_data']}</td>";
			echo "<td>{$value['get_data']}</td>";
			echo '</tr>';
    		flush(); //刷新缓存（直接发送到浏览器）

		}
		echo '</table>';
		echo '结束';

	}


	public function moAction()
	{

		exit;
		$t=20170330;
		for ($i=1; $i < 25; $i++) {
			$time = date('Ymd',strtotime('+'.$i.' days',strtotime($t)));

			$stime = strtotime($time);

			$etime = $stime+3600*24;
			// p($time);
			// p($stime);
			// p($etime);
			// exit;
			$re = $this->models
			->table('pay_log')
			->joinField('proports_type,sum(money-proports_money) as money,count(id) as num,user_id')
			->where('pay_type=2 and is_buckle=0')
			->where('pay_time>='.$stime.' and pay_time<'.$etime)
			->group('proports_type,user_id')->fetchSql(0)->select();

			if(!$re){
				return false;
			}
			$arr = [];
			foreach ($re as $value) {
				$type = $value['proports_type'];
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

				$arr[$value['user_id']][$str] = $value['money'];
				$arr[$value['user_id']]['all_money'] = isset($arr[$value['user_id']]['all_money'])?$arr[$value['user_id']]['all_money']+$value['money']:$value['money'];
				$arr[$value['user_id']]['num'] = isset($arr[$value['user_id']]['num'])?$arr[$value['user_id']]['num']+$value['num']:$value['num'];
			}
			// $arr['create_data'] = $time;

			foreach ($arr as $key => $value) {
				// $a = $value;
				// $a['user_id']=$key;
				// $a['create_data'] = $time;

				$this->models->table('day_log')->where([
						'user_id'=>$key,
						'create_data'=>$time
					])->fetchSql(1)->save($value);
			}


		}

		// print_r($re);
		// print_r($arr);
	}

	// 没用
	public function trAction()
	{
		$re = $this->models->table('bank')->field('id,shop_key,shop_id')->select();

		foreach ($re as $key => $value) {
			$this->models->create([
					'id'=>$value['id'],
					'shop_key'=>trim($value['shop_key']),
					'shop_id'=>trim($value['shop_id']),
				]);
		}
	}


	// 批量导入excel
	public function excelAction()
	{

		exit;

		/** PHPExcel_IOFactory */

		require_once ROOT.'tool/PHPExcel.php';

		// Check prerequisites
		$file=ROOT."excel.xls";
		if (!file_exists($file)) {
			exit("not found $file.\n");
		}

		$reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
		$PHPExcel = $reader->load($file); // 载入excel文件
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		$highestColumm= \PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27

		/** 循环读取每个单元格的数据 */
		for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
			$arr=[];
		    for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
		        $columnName = \PHPExcel_Cell::stringFromColumnIndex($column);
		        // echo $columnName.$row.":".$sheet->getCellByColumnAndRow($column, $row)->getValue()."<br />";
		        $arr[]=$sheet->getCellByColumnAndRow($column, $row)->getValue();
		    }

				$cr = [];
				$cr['shop_key'] = trim($arr[3]);
				$cr['shop_id'] = trim($arr[1]);
				$cr['user_name'] = trim($arr[4]);
				$cr['last_number'] = trim($arr[5]);

				$cr['user_id'] = 1;
				$cr['accounts_type'] = 2;
				$cr['create_time'] = TIME;
				$cr['proports'] = '1,5';
				$cr['money'] = 89000;
				$cr['appid'] = 0;
				$cr['secret'] = 0;
				$cr['bank_type'] = 3;
				$cr['is_delete'] = 0;
				$cr['is_normal'] = 0;
				$cr['all_money'] = 0;
				$cr['last_time'] = 0;
				$cr['info'] = '';
				$cr['is_used'] = 0;

				$a = $this->models->table('bank')->where('shop_id='.$cr['shop_id'])->getOne();
				if(!$a){
					$this->models->create($cr);
					echo '成功'.$cr['shop_id']."\r\n";
				}


		  //       [0] => 2016/08/17 05:28 PM CST
    // [1] => 89860616090041861087
    // [2] => 无
    // [3] => 460068039095838
		  //   print_r($arr);
		  //   exit;


		}
	}

	public function oneBillAction(){

		exit;
		$data = '20170419';
		$mch_id = '103520006740';
		$key = '698bd7e5027b59595bb87e912832073b';
		set_time_limit(0);
		$buffer = ini_get('output_buffering');
		echo str_repeat(' ',$buffer+1);     //防止浏览器缓存
		ob_end_flush();     //关闭缓存
		$paytool = new \api\models\paytool();

		$arr['service'] = 'pay.bill.merchant';
		$arr['bill_date'] = $data;
		$arr['bill_type'] = 'SUCCESS';
		$arr['mch_id'] = $mch_id;
		$arr['nonce_str'] = 'dcd4065473150ed3';

		$paytool->key = $key;
		$paytool->url = 'https://download.swiftpass.cn/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);

		$re = $paytool->postxml($re);


		// print_r($re);
		$order_no=[];
		preg_match_all('/M\w{21}/', $re, $order_no);
		$money=0;
		foreach ($order_no['0'] as $key => $value) {

			$re = $this->models->table('pay_log')->field('id,pay_type,money')->where(['order_no'=>$value])->getOne();

			echo $re['id'].'----'.$value.'----'.$re['pay_type'].'----'.$re['money'].'<br/>';
			$money+=$re['money'];
		}
		echo $money;
	}

	// 对账系统
	public function billAction(){
		set_time_limit(0);
		$buffer = ini_get('output_buffering');
		echo str_repeat(' ',$buffer+1);     //防止浏览器缓存
		ob_end_flush();     //关闭缓存

		isset($_GET['data']) || exit;
		$data = $_GET['data'];

		$t = strtotime($data)+3600*24;
		if($t>TIME){
			echo '还没有对账单';
			exit;
		}
		$a = $this->models
		->table('pay_log')
		->joinField('shop_id,shop_key,bank_id,sum(money) as money,count(id) as count')
		->where('pay_time>='.strtotime($data).' and pay_time<'.$t)
		->where('pay_type=2')
		->group('bank_id,shop_id,shop_key')
		->select();

		if(!$a){
			echo '还没有对账单';
			exit;
		}


		$paytool = new \api\models\paytool();
            // [shop_id] => 103540004672
            // [shop_key] => a9d49a2d390d716672d123765f243ca9
            // [bank_id] => 194
            // [money] => 18156.00
            // [count] => 299
			echo '<table>';
			echo '<tr><td>帐号</td><td>总交易额</td><td>总交易数</td><td>手续费</td><td>到账费用</td><td>-----</td><td>总交易额</td><td>总交易数</td><td>-----</td><td>单数差</td><td>交易差</td></tr>';
		foreach ($a as $key => $value) {
			# code...

			$arr['service'] = 'pay.bill.merchant';
			$arr['bill_date'] = $data;
			$arr['bill_type'] = 'SUCCESS';
			$arr['mch_id'] = $value['shop_id'];
			$arr['nonce_str'] = 'dcd4065473150ed3';

			$paytool->key = $value['shop_key'];
			$paytool->url = 'https://download.swiftpass.cn/gateway';
			$arr['sign'] = $paytool->buildQuery($arr);
			$re = $paytool->linkxml($arr);

			$re = $paytool->postxml($re);
			$num = strpos($re,'总交易单数,总交易额,总退款金额');
			$str = substr($re,$num);
			// echo $num;
			$a = explode('`',$str);

			$amoney = str_replace(',','',$a['6']);
			$smoney = str_replace(',','',$a['5']);
			$jmoney = $amoney-$smoney;
			$count = str_replace(',','',$a['1']);


			echo '<tr>';
			echo "<td>{$value['shop_id']}</td>";
			echo "<td>{$amoney}</td>";
			echo "<td>{$count}</td>";
			echo "<td>{$smoney}</td>";
			echo "<td>{$jmoney}</td>";
			echo "<td></td>";
			echo "<td>{$value['money']}</td>";
			echo "<td>{$value['count']}</td>";
			echo "<td></td>";
			echo "<td>".($value['count']-$count)."</td>";
			echo "<td>".($value['money']-$amoney)."</td>";
			echo '</tr>';
		    flush(); //刷新缓存（直接发送到浏览器）

		}
			echo '</table>';


		// print_r($user);
		exit;
		// print_r($a);

	}

	// 批量修改商户信息
	public function wjAction(){
		exit;
		set_time_limit(0);
		$buffer = ini_get('output_buffering');
		echo str_repeat(' ',$buffer+1);     //防止浏览器缓存
		ob_end_flush();     //关闭缓存

		$user = $this->models->table('user')->field('id,bank_money,money,all_money')->select();


			echo '<table>';
		foreach ($user as $key => $value) {

				// $this->models->table('user')->create([
				// 		'all_money'=>$value['bank_money']+$value['money'],
				// 		'id'=>$value['id']
				// 	]);
				// echo '成功'.$value['id'].'<br/>';
			$money = $this->models->table('pay_log')->where([
					'user_id'=>$value['id'],
					'pay_type'=>2,
					'is_buckle'=>0
				])->joinField('sum(money-proports_money) as money,count(id) as num')->getOne();

			if($money['num']!=0){
				// $this->models->table('user')->create([
				// 		'all_money'=>$value['bank_money']+$value['money'],
				// 	]);
				echo '<tr>';
				echo "<td>{$value['all_money']}</td>";
				echo "<td>{$money['money']}</td>";
				echo "<td>".($money['money']-$value['bank_money'])."</td>";
				echo "<td>{$value['money']}</td>";
				echo "<td>{$value['id']}</td>";
				echo '</tr>';
			}
		    flush(); //刷新缓存（直接发送到浏览器）
		}
			echo '</table>';
		// print_r($user);
		exit;
		for( $i=1; $i<=10; $i++ ){
		    echo '第 '.$i.' 次输出.'."<br />\n";
		    flush(); //刷新缓存（直接发送到浏览器）
		    sleep(1);
		}

	}

	// 批量添加银行数据
	public function textAction()
	{
		exit;

		$a = file_get_contents("php://input");

		$arr = explode("\r\n",$a);
		// $arr= array_unique($arr);


		for ($i=0; $i < 800; $i++) {

			if(isset($arr[$i]) && strlen($arr[$i])>20){
				$cr = [];
				$cr['shop_key'] = $arr[$i];
				$cr['shop_id'] = $arr[$i-2];
				$cr['user_name'] = $arr[$i-4];
				$cr['last_number'] = $arr[$i-3];

				$cr['user_id'] = 1;
				$cr['accounts_type'] = 1;
				$cr['create_time'] = TIME;
				$cr['proports'] = '1,5';
				$cr['money'] = 80000;
				$cr['appid'] = 0;
				$cr['secret'] = 0;
				$cr['bank_type'] = 3;
				$cr['is_delete'] = 0;
				$cr['is_normal'] = 0;
				$cr['all_money'] = 0;
				$cr['last_time'] = 0;
				$cr['info'] = '';


				$a = $this->models->table('bank')->where('shop_id='.$cr['shop_id'])->getOne();
				if(!$a){
					$this->models->create($cr);
					echo '成功'.$cr['shop_id']."\r\n";
				}
			}

		}


		exit;
		$str = 'https://statecheck.swiftpass.cn/pay/wappay?token_id=74d8c9f4edcca69dc7f8ec8868414090&service=pay.weixin.wappayv3';


		$this->textWeixinNative();
		exit;
		$paytool = new \api\models\paytool();
		$arr['service'] = 'pay.weixin.jspay';
		$arr['sign_type'] = 'MD5';
		$arr['mch_id'] = '2017033000261568296';
		$arr['out_trade_no'] = '65651111111165';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '1';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';
		$arr['sub_openid'] = 'oywgtuDfAIcchQbw3YRgE8feT-Aw';
		$paytool->key = 'KPiEctqq6n4pVfrjGAxaCQ4DC3';
		$paytool->url = 'http://www.text.com/pay/api/pay/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);

		echo $paytool->postxml($re);

	}

	public function addAction(){
		exit;
		$id = 15;
		$num = 3;
		$money = 50000;
		$re = $this->models->table('agent')->where('id='.$id)->getOne();

		$a = $this->models->table('user_bank')->where('is_show=1')->count();

		if($a>=$num){
			return false;
		}
		// $this->models->table('user_bank')
		$re['agent_id'] = $re['id'];
		$re['state'] = 1;
		$re['money'] = $money;
		$re['create_time'] = TIME;
		$re['is_show'] = 1;
		$re['counter'] = 5;
		unset($re['id']);
		unset($re['is_used']);

		$this->models->table('user_bank')->create($re);

		$this->models->table('user')->create([
				'id'=>$re['user_id'],
				'all_money'=>'all_money+'.$re['money'],
			]);

	}

	public function reduceAction(){

		exit;
		$user_id = 63;
		$money = 50000;
		$this->models->table('user')->create([
				'id'=>$user_id,
				'all_money'=>'all_money-'.$money,
				'bank_money'=>'bank_money-'.$money
			]);
	}

	public function delAction(){
		exit;
		$id = 1289;
		$re = $this->models->table('user_bank')->where([
				'id'=>$id,
				'is_show'=>1
			])->getOne();
		if($re){
			$re = $this->models->table('user_bank')->delete($re['id']);
		}
		print_r($re);

	}

	public function dellogAction(){
		exit;
		$id = 624596;
		$re = $this->models->table('log_admin')->where('id>'.$id)->delete();
		print_r($re);
	}

	public function reAction(){
		exit;
		$re = $this->models->table('j_pay_log')->field('id,money,proports_type,user_id,pay_time')->where('is_buckle=1 and pay_time>1492704000 and pay_time<1492963200')->select();
		$re_money = 100000;
		$num = 7;
		set_time_limit(0);
		$buffer = ini_get('output_buffering');
		echo str_repeat(' ',$buffer+1);     //防止浏览器缓存
		ob_end_flush();     //关闭缓存
		$money=0;

		foreach ($re as $key => $value) {
			if($money>$re_money){
				echo '结束';
				exit;
			}
			$money += $value['money'];

			if(isset($re[$key*$num]['id'])){
				$this->models->create([
						'id'=>$re[$key*$num]['id'],
						'is_show'=>1,
						'is_buckle'=>0
					]);
				$this->dayLog($value['user_id'],$value['user_id'],$value['proports_type'],$value['pay_time']);
				echo $re[$key*$num]['id'];
				echo '<br/>';
		    	flush(); //刷新缓存（直接发送到浏览器）
			}

		}
		echo $money;

	}


	public function dayLog($user_id,$money,$type,$date){

		// $date = date('Ymd',TIME);
		$date = date('Ymd',$date);

		$log = $this->models->table('day_log')->where([
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
			$arr[$str] = $log[$str]+$money;
			$arr['all_money'] = $log['all_money']+$money;
			$arr['num'] = $log['num']+1;
		}else{
			$arr[$str] = $money;
			$arr['create_time'] = TIME;
			$arr['create_data'] = $date;
			$arr['user_id'] = $user_id;
			$arr['all_money'] = $money;
			$arr['num'] = 1;
		}

		$this->models->create($arr);

		return $log;

	}

	public function textAlipayJspay()
	{
		$paytool = new \api\models\paytool();
		$arr['service'] = 'pay.alipay.njspay';
		$arr['mch_id'] = '2017033000261568296';
		$arr['out_trade_no'] = '656511111111651';
		$arr['sign_type'] = 'MD5';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '1';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';
		$paytool->key = 'KK3QSrLyH4caHkD6WvWi5iuj7T';
		$paytool->url = URL_PATH.'/pay/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);

		$re = $paytool->linkxml($arr);
		$xml = $paytool->postxml($re);
		$arr = $paytool->getxml($xml);
		exit;
		$this->redirect($arr['code_url']);
	}

	public function textAlipayNative()
	{
		$paytool = new \api\models\paytool();
		$arr['service'] = 'pay.alipay.native';
		$arr['mch_id'] = '2017040120452945682';
		$arr['out_trade_no'] = '65651111111165';
		$arr['sign_type'] = 'MD5';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '10000';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';
		$paytool->key = 'K7JG7VXBGYiPhQpFkyw4PLneew';
		$paytool->url = URL_PATH.'/pay/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);

		$re = $paytool->linkxml($arr);
		$xml = $paytool->postxml($re);
		$arr = $paytool->getxml($xml);
		print_r($arr['code_img_url']);
		exit;
		// $this->redirect($arr);

	}

	public function textWeixinNative()
	{
		$paytool = new \api\models\paytool();
		$arr['service'] = 'pay.weixin.native';
		$arr['sign_type'] = 'MD5';
		$arr['mch_id'] = '2017032922191423581';
		$arr['out_trade_no'] = '65651111111165';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '1';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';
		// $arr['sign'] = 'D00B593A92A9FBE206033DDFF9F50127';
		$paytool->key = 'KJ9CuXHQuJBmDsQpL76EfanmTy';
		$paytool->url = URL_PATH.'/pay/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);
		// print_r($re);
		echo $paytool->postxml($re);
	}

	public function textWeixinWappay()
	{
		$paytool = new \api\models\paytool();
		$arr['service'] = 'pay.weixin.wappay';
		$arr['sign_type'] = 'MD5';
		$arr['mch_id'] = '2017031920431360256';
		$arr['out_trade_no'] = '65651111111165';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '1';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';
		// $arr['sign'] = 'D00B593A92A9FBE206033DDFF9F50127';
		$paytool->key = 'KPiEctqq6n4pVfrjGAxaCQ4DC3';
		$paytool->url = URL_PATH.'/pay/gateway';
		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);
		// print_r($re);
		$xml = $paytool->postxml($re);

		$arr = $paytool->getxml($xml);

		if(isset($arr['pay_info']) && $arr['pay_info']){
			$this->redirect($arr['pay_info']);
		}
		echo $xml;
	}
	public function textWeixinJs()
	{

		$arr['service'] = 'pay.weixin.jspay';
		$arr['sign_type'] = 'MD5';
		$arr['mch_id'] = '2017031920431360256';
		$arr['out_trade_no'] = '6565111111111165';
		$arr['body'] = 'miaoshi';
		$arr['total_fee'] = '1';
		$arr['mch_create_ip'] = '120.18.25.124';
		$arr['notify_url'] = 'http://www.shouqiana.cn/a.php/';
		$arr['nonce_str'] = 'dcd4065473150ed3';

		$check=$this->handle([
				'code'=>['fill','string',''],
			]);

		if(!$check['code']){
			$this->redirect('/wx/code',$arr);
		}

		$paytool = new \api\models\paytool();
		$wx = new \api\models\wx();
		$arr['sub_openid'] = $wx->getOpenId($check);

		// $arr['sign'] = 'D00B593A92A9FBE206033DDFF9F50127';
		$paytool->key = 'KPiEctqq6n4pVfrjGAxaCQ4DC3';
		$paytool->url = URL_PATH.'/pay/gateway';

		$arr['sign'] = $paytool->buildQuery($arr);
		$re = $paytool->linkxml($arr);
		// print_r($re);

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


}
?>