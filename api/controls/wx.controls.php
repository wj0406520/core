<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class wx extends all
{

	public $check=0;



	// 获取微信code
	public function codeAction()
	{


		// $re = file_get_contents('php://input');
		$paytool = $this->paytool = new \api\models\paytool();

		$a=$this->models->getCode($_GET);

		$this->redirect($a);
	}
	public function textAction()
	{
		//echo file_get_contents('weixin://wxpay/bizpayurl?pr=pCZQdXe');
		$this->redirect('weixin://wxpay/bizpayurl?pr=pCZQdXe');
	}
	// 获取微信openid
	public function openidAction()
	{

		$check=$this->handle([
				'code'=>['fill','string',''],
			]);
		if(!$check['code']){
			return false;
		}

		$a=$this->models->getOpenId($check);
		print_r($a);
		// $this->redirect($a);
	}


}
?>