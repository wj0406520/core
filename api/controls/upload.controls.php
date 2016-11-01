<?php

namespace api\controls;

use core;

defined('ACC')||exit('ACC Denied');


class upload extends all
{


	public $check=0;

	// 上传图片接口
	public function indexAction()
	{


		$face=\tool\UploadTool::connect('img');

		$arr['url']=$face===true?'':$face;

		$this->success($arr);

	}

	// 上传图片base64
	public function strtoimgAction()
	{

		$check=$this->fill(array(
				array('img','','string'),
			));
		$img=\tool\Imgbase::strToImg($check['img']);

		if(!$img){
			$this->errorMsg(\tool\Imgbase::$error);
		}
		$arr['url']=$img;

		$this->success($arr);
	}

}
?>