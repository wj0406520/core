<?php
// 二维码
namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class qrcode extends all
{
	public $check = 0;
	public function indexAction(){
		$str = isset($_GET['str'])?$_GET['str']:'123456';
		include TOOL.'qrCodeTool.class.php';
		\QRcode::png($str,false,QR_ECLEVEL_Q,8,3);
	}

}
