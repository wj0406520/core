<?php

/**
检测配置和环境
*/
$str=array();

/**
检测mysqli数据库
*/
$my=function_exists('mysqli_connect');
if($my){
	$str[]='have "mysqli"';
}else{
	$str[]='no found "mysqli"';
}

/**
检测上传文件夹是否有权限
*/
$write=is_writeable('../data');
if($write){
	$str[]='"data" dir is write';
}else{
	$str[]='"data" dir don`t write';
}


/**
检测相关文件夹
*/
$dirname=array(
		'../config',
		'../core',
		'../data',
		'../data/log'
	);
foreach ($dirname as $value) {
	$dir=is_dir($value);
	if($dir){
		$str[]='exist "'.$value.'" dir';
	}else{
		$str[]='no found "'.$value.'" dir';
	}
}

/**
检测重要的文件是否存在
*/
$filename=array(
		'config.inc.php',
		'../core/conf.class.php',
		'../core/controls.class.php',
		'../core/db.class.php',
		'../core/function.php',
		'../core/init.php',
		'../core/linksql.class.php',
		'../core/log.class.php',
		'../core/models.class.php',
		'../core/mysql.class.php',
		'../core/prourl.class.php',
	);
foreach ($filename as $value) {
	$file=is_file($value);
	if($file){
		$str[]='exist "'.$value.'" file';
	}else{
		$str[]='no found "'.$value.'" file';
	}
}


$id=isset($_GET['id'])?$_GET['id']:0;

/**
生成一个例子
*/
$filename='../example.php';
$string=<<<EXT
<?php

	//定义文件访问
	define('ACC',111);

	//定义项目入口
	define('APP', 'example/');

	//是否开启报错
	// define('DEBUG',false);
	define('DEBUG',true);

	//引入核心文件
	require('./core/init.php');
EXT;
if($id==1){
	file_put_contents($filename, $string);
}elseif($id==2){
	if(file_exists($filename)){
		unlink($filename);
	}
}

$filepath='../example';
if($id==1){
	if (!is_dir($filepath)) {
		mkdir($filepath,0777);
	}
}

$filepath='../example/controls';
$filename='../example/controls/index.controls.php';
$string=<<<EXT
<?php

namespace example\controls;

use core;

defined('ACC')||exit('ACC Denied');

class index extends core\controls{
	public function index()
	{
		\$this->models->index();
		\$this->display();
	}
}
?>
EXT;
if($id==1){
	if (!is_dir($filepath)) {
		mkdir($filepath,0777);
	}
	file_put_contents($filename, $string);
}elseif($id==2){
	if(file_exists($filename)){
		unlink($filename);
	}
	if (is_dir($filepath)) {
		rmdir($filepath);
	}
}


$filepath='../example/models';
$filename='../example/models/index.models.php';
$string=<<<EXT
<?php

namespace example\models;

use core;

defined('ACC')||exit('ACC Denied');

class index extends core\models{

	public \$table='cc';

	public function index()
	{

		print_r(\$this->select());

	}

}
?>
EXT;
if($id==1){
	if (!is_dir($filepath)) {
		mkdir($filepath,0777);
	}
	file_put_contents($filename, $string);
}elseif($id==2){
	if(file_exists($filename)){
		unlink($filename);
	}
	if (is_dir($filepath)) {
		rmdir($filepath);
	}
}

$filepath='../example/views';
$filename='../example/views/index.html';
$string=<<<EXT
<?php echo 111;?>
xxx
?>
EXT;
if($id==1){
	if (!is_dir($filepath)) {
		mkdir($filepath,0777);
	}
	file_put_contents($filename, $string);
}elseif($id==2){
	if(file_exists($filename)){
		unlink($filename);
	}
	if (is_dir($filepath)) {
		rmdir($filepath);
	}
}
$filepath='../example';
if($id==2){
	if (is_dir($filepath)) {
		rmdir($filepath);
	}
}
if($id==1){
	$str[]='new example';
}elseif ($id==2) {
	$str[]='delect example';
}
$string=implode('<br /><br />', $str);
echo $string;
// var_dump($write);
// print_r($str);

?>