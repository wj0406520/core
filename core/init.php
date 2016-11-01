<?php
/**
 * author 王杰
 * time 2016-11-01
 * version 3.0.1
 * 框架初始化
 * init.php
 */

// 初始化当前的绝对路径
// 换成正斜线是因为 win/linux都支持正斜线,而linux不支持反斜线

// 开启session
session_start();
//设置时区
date_default_timezone_set('Asia/Shanghai');

//判断是不是可以访问
defined('ACC')		   || exit('ACC Denied');

//判断有没有入口
defined('APP')		   || exit('APP Denied');

//定义访问类型
defined('IS_POST') 	   || define('IS_POST', $_SERVER['REQUEST_METHOD'] == 'POST');

//设置根目录
defined('ROOT') 	   || define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');

//设置模型名称
defined('MODELS') 	   || define('MODELS','models');
//设置控制器名称
defined('CONTROLS')    || define('CONTROLS','controls');
//设置视图名称
defined('VIEWS') 	   || define('VIEWS','views');
//设置静态文件名称
defined('PACK') 	   || define('PACK','pack');

//设置模型目录
defined('MODELSDIR')   || define('MODELSDIR',ROOT.APP.MODELS.'/');
//设置控制器目录
defined('CONTROLSDIR') || define('CONTROLSDIR',ROOT.APP.CONTROLS.'/');
//设置视图目录
defined('VIEWSDIR')    || define('VIEWSDIR',ROOT.APP.VIEWS.'/');

//设置核心文件路径
defined('CORE') 	   || define('CORE',ROOT.'core/');

//设置访问后缀
defined('ACTION') 	   || define('ACTION','Action');

//设置工具类文件路径
defined('TOOL') 	   || define('TOOL',ROOT.'tool/');

//视图文件路径
defined('PATH') 	   || define('PATH', str_replace($_SERVER['DOCUMENT_ROOT'],'',ROOT.APP).PACK.'/');

//访问时间
defined('TIME') 	   || define('TIME', $_SERVER['SCRIPT_NAME']);

//访问路径前缀
defined('URL') 		   || define('URL', $_SERVER['SCRIPT_NAME']);

//配置文件
defined('CONFIG') 	   || define('CONFIG',ROOT . 'config/config.inc.php');

//类型文件
defined('TYPE') 	   || define('TYPE',ROOT . 'config/type.php');

//错误信息文件
defined('ERRORFILE')   || define('ERRORFILE',ROOT . 'config/error.php');

//数据目录
defined('DATA') 	   || define('DATA',ROOT . 'data/');

//数据目录
defined('LOGDIR') 	   || define('LOGDIR',DATA . 'log/');

//引入函数文件
require(CORE . 'function.php');

//动态加载类
spl_autoload_register('autoload');

// 过滤参数,用递归的方式过滤$_GET,$_POST,$_COOKIE
$_GET = _addslashes($_GET);
$_POST = _addslashes($_POST);
$_COOKIE = _addslashes($_COOKIE);

// 设置报错级别
if(DEBUG) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

//获取参数
core\Prourl::parseUrl();

//访问控制器
accessController();


?>