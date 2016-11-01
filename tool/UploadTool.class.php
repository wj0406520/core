<?php

namespace tool;
/*
@@@ $name上传文件input名称
@@@ $uptype上传文件类型
@@@ $size上传文件不能超过多少大小
*/

defined('ACC')||exit('Acc Denied');

class UploadTool{
	public $url=null;
	public $array=array();
	static $error='';
	static private $ftype = array(
					'image'=>array('gif', 'jpg', 'jpeg', 'png', 'pneg', 'bmp'),
					'file'=>array('txt', 'doc', 'xls'),
					'flash'=>array('swf'),
					'music'=>array('mp3', 'wmv'),
					'video'=>array('mp4', 'avi', 'wmv', 'flv'),
					'zip_file'=>array('zip','rar'),
					'all'=>array('gif', 'jpg', 'jpeg', 'png', 'pneg', 'bmp','txt', 'doc', 'xls','swf','mp3', 'wmv','mp4', 'avi', 'wmv', 'flv','zip','rar')
				);
	 //构造函数， 实例化时直接调用



	static public function connect($name,$uptype='image',$size=2){

		//判断是不是有上传文件
		if(!isset($_FILES[$name]['tmp_name']) || !$_FILES[$name]['tmp_name'] || !is_uploaded_file($_FILES[$name]['tmp_name'])){
			return true;
		}
		//判断文件是否超出大小
		if($_FILES[$name]['size'] > $size * 1024 * 1024){
			self::$error='fileMax';
			return false;
		}

		//截取文件后缀名
		$ext = substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.')+1);
		$a=self::$ftype;
		//判断上传文件类型
		if(!in_array($ext, $a[$uptype])){
			self::$error='fileType';
			return false;

		}
		//上传文件夹
		$dir = '/'.$uptype.'/'.date('Ym/d').'/';
		// 上传路径
		$upload_dir = DATA.$dir;
		//上传文件名
		$newname = time(). mt_rand(100, 999). '.' .$ext;
		//判断文件夹是否存在
		if(!is_dir($upload_dir)){
			mkdir($upload_dir, 0777, true);
		}
		//移动临时文件到指定文件夹
		if(!move_uploaded_file($_FILES[$name]['tmp_name'], $upload_dir . $newname)){
			if(!copy($_FILES[$name]['tmp_name'], $upload_dir . $newname)){
				self::$error='fileFail';
				return false;
			}
		}
		//上传文件路径
		return $dir.$newname;


  }

}

class upload_files{
	public $url=null;
	private $ftype = array(
					'image'=>array('gif', 'jpg', 'jpeg', 'png', 'pneg', 'bmp'),
					'file'=>array('txt', 'doc', 'xls'),
					'flash'=>array('swf'),
					'music'=>array('mp3', 'wmv'),
					'video'=>array('mp4', 'avi', 'wmv', 'flv'),
					'zip_file'=>array('zip','rar')
				);
	 //构造函数， 实例化时直接调用


    public function __construct($name,$uptype,$size){
    	$this->connect($name,$uptype,$size);
    }

	public function connect($name,$uptype,$size){

		//判断是不是有上传文件
		foreach ($_FILES[$name]['tmp_name'] as $key => $value) {
			if(!$value || !is_uploaded_file($_FILES[$name]['tmp_name'][$key])){
				echo '没有找到您要上传的文件1';
				exit;
			}
		}

		//判断文件是否超出大小
		foreach ($_FILES[$name]['size'] as $key => $value) {
			if($value > $size * 1024){
				echo $value. $size * 1024;
				echo '文件超出大小限制2';
				exit;
			}
		}

		//截取文件后缀名
		foreach ($_FILES[$name]['name'] as $key => $value) {
			$ext = substr($value, strrpos($value, '.')+1);
			//判断上传文件类型
			if(!in_array($ext, $this->ftype[$uptype])){
				echo '文件类型不对3';
				exit;
			}
			//上传文件夹
			$upload_dir = ROOT_PATH_IMG .'upload/'.$uptype.'/'.date('Ym/d').'/';
			//上传文件名
			$newname = time(). mt_rand(100, 999). '.' .$ext;
			//判断文件夹是否存在
			if(!is_dir($upload_dir)){
				mkdir($upload_dir, 0777, true);
			}
			//移动临时文件到指定文件夹
			if(!move_uploaded_file($_FILES[$name]['tmp_name'][$key], $upload_dir . $newname)){
				if(!copy($_FILES[$name]['tmp_name'][$key], $upload_dir . $newname)){
					echo '上传文件失败';
					exit;
				}
			}
			//上传文件路径
			$this->array[]='upload/'.$uptype.'/'.date('Ym/d').'/'.$newname;
		}



  }
}
?>