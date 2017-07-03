<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  路径重写类
// +----------------------------------------------------------------------
namespace core;

defined('ACC') || exit('ACC Denied');


class Prourl {
		/**
		 * URL路由,转为PATHINFO的格式
		 */
		public static function parseUrl(){
			if (isset($_SERVER['PATH_INFO'])) {
					$name=str_replace('/index.php','',$_SERVER["PATH_INFO"]);

					$url=str_replace($name,'',$_SERVER["REQUEST_URI"]);

      			 	//获取 pathinfo
					$pathinfo = explode('/', trim($url, "/"));

       				// 获取 control
       				$m = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');

       				array_shift($pathinfo); //将数组开头的单元移出数组

			       	// 获取 action
       				$a = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');

					array_shift($pathinfo); //再将将数组开头的单元移出数组

					for ($i=0; $i<count($pathinfo); $i+=2) {

						if (!isset($_GET[$pathinfo[$i]])) {

							$_GET[$pathinfo[$i]] = isset($pathinfo[$i+1]) ? $pathinfo[$i+1] : '';

						}

					}
			}else{

				if (isset($_SERVER['REQUEST_URI'])) {
					$name=URL;

					$url=str_replace($name,'',$_SERVER["REQUEST_URI"]);

					$pathinfo = explode('/', trim($url, "/"));

					$m = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');

	       			array_shift($pathinfo); //将数组开头的单元移出数组

	       			if (isset($pathinfo[0])) {

		       			$pathinfo = explode('?', trim($pathinfo[0], "/"));

		       			$a = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');

	       			}
				}

				$m = (!empty($m) ? $m : 'index');    //默认是index模块

				$a = (!empty($a) ? $a : 'index');   //默认是index动作

				// if($_SERVER["QUERY_STRING"]){
				// 	$m=$_GET["m"];
				// 	unset($_GET["m"]);  //去除数组中的m
				// 	$a=$_GET["a"];
				// 	unset($_GET["a"]);  //去除数组中的a
				// 	$query=http_build_query($_GET);   //形成0=foo&1=bar&2=baz&3=boom&cow=milk格式
				// 	//组成新的URL
				// 	$url=$_SERVER["SCRIPT_NAME"]."/{$m}/{$a}/".str_replace(array("&","="), "/", $query);
				// 	header("Location:".$url);
				// }
			}
		    //控制器中的方法名
		    define('URL_MODEL',$a);
		    //控制器名称
		    define('URL_CONTROL',$m);
		}
	}
