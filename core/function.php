<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  公共函数库
// +----------------------------------------------------------------------

defined('ACC') || exit('ACC Denied');

// 递归转义数组
function _addslashes($data) {
     if (get_magic_quotes_gpc() == false) {
      if (is_array($data)){
       foreach ($data as $k => $v){
        $data[$k] = _addslashes($v);
       }
      }else{
        $data = addslashes($data);
      }
     }
     return $data;
}

//自动加载函数
function autoload($class) {

    $class = str_replace('\\', '/', $class);

    $arr = explode('/',$class);

    if (count($arr) == 3) {
        if((strpos($class,CONTROLS) !== false)){
            $file = ROOT . $class . '.' . CONTROLS . '.php';
        } elseif ((strpos($class,MODELS) !== false)) {
            $file = ROOT . $class . '.' . MODELS . '.php';
        }
    } else {
        $file = ROOT . $class . '.class.php';
    }

    if (is_file($file)) {
        require($file);
    } else {
        debug('not found class ' . $class);
    }
}

/**
 * [diyDate 自定义时间]
 * @param  [integer]  $data [时间戳]
 * @param  integer $type    [0为短时间 1为长时间]
 * @return [string]         [返回时间字符]
 */
function diyDate($data, $type = 0){
    $str = '';
    if ($type == 0) {
       $str = date('Y-m-d', $data);
    } else {
       $str = date('Y-m-d H:i:s', $data);
    }
    return $str;
}

/**
 * [diyType 获取类型]
 * @param  string $type  [类型名称]
 * @return [array]       [类型数据]
 */
function diyType($type = ''){
    if (!file_exists(TYPE)) {
        debug('no found file ' . TYPE);
        return false;
    }
    $arr = include(TYPE);
    if ($type) {
        if (!in_array($type,$arr)) {
            return $arr[$type];
        } else {
            debug('no found type '.$type);
        }
    }
    return $arr;
}

/**
 * [diyError 获取错误信息]
 * @param  string $type  [错误名称]
 * @return [array]       [错误数据]
 */
function diyError($type = ''){
    if (!file_exists(TYPE)) {
        debug('no found file ' . TYPE);
        return false;
    }
    $arr = include(ERRORFILE);
    if ($type) {
        if (!in_array($type,$arr)) {
            return $arr[$type];
        } else {
            debug('no found type '.$type);
        }
    }
    return $arr;
}
/**
 * [debug 调试工具]
 * @param  string $str [要输出的字符]
 */
function debug($str = ''){
    if (DEBUG) {
        echo $str;
        exit;
    } else {
        Log::write($str);
        getRoot();
    }
}
//访问控制器
function accessController(){

    $name = str_replace('/', '\\', APP . CONTROLS . '\\');
    $m = $name . $_GET['m'];
    $a = $_GET['a'] . ACTION;
    define('URL_MODEL',$_GET['m']);
    unset($_GET['m']);
    unset($_GET['a']);


    $ontrol = new $m();
    $arr = get_class_methods($m);
    if (in_array($a, $arr)) {
        $ontrol->$a();
    } else {
        debug('not found function '. $a);
    }
    // call_user_func($name .$m.'->'.$a);
    // call_user_func(array($name.$m, $a));
}



//跳转到根目录
function getRoot($url = ''){

    $str = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

    header('location:' . $str . $url);
}

function p(){
    $args = func_get_args();  //获取多个参数
    //多个参数循环输出
    foreach ($args as $arg) {
        if (is_array($arg)) {
            print_r($arg);
            echo '<br>';
            echo "\r\n";
        }elseif (is_string($arg)) {
            echo $arg.'<br>';
            echo "\r\n";
        } else {
            var_dump($arg);
            echo '<br>';
            echo "\r\n";
        }
    }
}

//跳转到message界面
function message($msg, $link = ''){
    $msg = urlencode($msg);
    getRoot("/message/index?msg=$msg&link=$link");
}

// function inclu_page(){
//     $array=explode('/',$_SERVER['SCRIPT_NAME']);
//     $array=array_pop($array);
//     $aa= substr($array,0,-4).'.html';
//     return 'templates/'.$aa;
// }

// function include_page(){
//     $array=explode('/',$_SERVER['SCRIPT_NAME']);
//     $array=array_pop($array);
//     $aa= substr($array,0,-4).'.html';
//     return 'view/'.$aa;
// }

// 退出销毁session
function quit($str = ''){
    if ($str) {
        session_destroy();
        getRoot("/login.php");
        exit;
    }
}

/**
 * [delete_file 删除文件]
 * @param  [file] $file [文件名称]
 * @return [boolean]    [是否删除成功]
 */
function delete_file($file){
    if (is_file(ROOT . $file)) {
        unlink(ROOT . $file);
        return true;
    } else {
        return false;
    }
}

/**
 * [page 翻页]
 * @param  [int] $pagesize       [一页多少个]
 * @param  [int] $page           [当前页]
 * @param  [int,array] $arr      [数据总数]
 * @return [array]               [翻页相关数组]
 */
function page($pagesize, $page, $arr){
        $array = array();

        $array['pagesize'] = $pagesize;

        $array['page'] =$page;

        if (is_array($arr)) {
            $array['rsnum'] = count($arr);
        } else {
            $array['rsnum'] = $arr;
        }

        $array['pcount'] = $array['rsnum'] % $array['pagesize'] > 0 ? intval($array['rsnum'] / $array['pagesize'])+1 : $array['rsnum'] / $array['pagesize'];

        $array['page'] = $array['page'] > $array['pcount'] ? $array['pcount'] : $array['page'];

        $array['page'] = $array['page'] < 1 ? 1 : $array['page'];

        $array['next'] = ($array['page'] + 1 > $array['pcount']) ? $array['pcount'] : $array['page'] + 1;

        $array['previous'] = ($array['page'] - 1< 1 ) ? 1 : $array['page'] - 1;

        $array['sql'] = " limit " . ($array['page'] - 1) * $array['pagesize'] . "," . $array['pagesize'];

        return $array;

}

//获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的
//部分多音字分辨不出来   需要手动分辨
function getfirstchar($s0){
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z") ) return strtoupper($s0{0});
    $s1 = @iconv("UTF-8","gb2312", $s0);
    $s2 = @iconv("gb2312","UTF-8", $s1);
    if ($s2 == $s0) {$s = $s1;} else {$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284) return "A";
    if ($asc >= -20283 and $asc <= -19776) return "B";
    if ($asc >= -19775 and $asc <= -19219) return "C";
    if ($asc >= -19218 and $asc <= -18711) return "D";
    if ($asc >= -18710 and $asc <= -18527) return "E";
    if ($asc >= -18526 and $asc <= -18240) return "F";
    if ($asc >= -18239 and $asc <= -17923) return "G";
    if ($asc >= -17922 and $asc <= -17418) return "H";
    if ($asc >= -17922 and $asc <= -17418) return "I";
    if ($asc >= -17417 and $asc <= -16475) return "J";
    if ($asc >= -16474 and $asc <= -16213) return "K";
    if ($asc >= -16212 and $asc <= -15641) return "L";
    if ($asc >= -15640 and $asc <= -15166) return "M";
    if ($asc >= -15165 and $asc <= -14923) return "N";
    if ($asc >= -14922 and $asc <= -14915) return "O";
    if ($asc >= -14914 and $asc <= -14631) return "P";
    if ($asc >= -14630 and $asc <= -14150) return "Q";
    if ($asc >= -14149 and $asc <= -14091) return "R";
    if ($asc >= -14090 and $asc <= -13319) return "S";
    if ($asc >= -13318 and $asc <= -12839) return "T";
    if ($asc >= -12838 and $asc <= -12557) return "W";
    if ($asc >= -12556 and $asc <= -11848) return "X";
    if ($asc >= -11847 and $asc <= -11056) return "Y";
    if ($asc >= -11055 and $asc <= -10247) return "Z";
    return NULL;
    //return $s0;
}
//获取整条字符串汉字拼音首字母
function pinyinLong($zh){
    $ret = "";
    $s1 = iconv("UTF-8", "gb2312", $zh);
    $s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $zh) {$zh = $s1;}
    for ($i = 0; $i < strlen($zh); $i++) {
        $s1 = substr($zh, $i, 1);
        $p = ord($s1);
        if ($p > 160) {
            $s2 = substr($zh, $i++,2);
            $ret .= getfirstchar($s2);
        } else {
            $ret .= $s1;
        }
    }
    return $ret;
}


// function is_login(){
//     if(ACC===1 && !isset($_SESSION['user_id']) && strpos($_SERVER['PHP_SELF'],'login.php')===false){
//         //跳转到login页面
//         Redirect("login.php");
//         exit;
//     }
// }
// function index_login(){
//     if(ACC===true && !isset($_SESSION['uid']) && strpos($_SERVER['PHP_SELF'],'login.php')===false){
//         //跳转到login页面
//         if(isset($_GET['id']) && isset($_GET['bsh_bid'])){
//             Redirect("login.php?id=".$_GET['id'].'&bsh_bid='.$_GET['bsh_bid']);
//             exit;
//         }
//         Redirect("login.php");
//         exit;
//     }

//     if(!isset($_GET['tuichu'])){
//         if(!isset($_GET['id']) && isset($_SESSION['uid']) && strpos($_SERVER['PHP_SELF'],'index.php')!==false){
//             Redirect("index.php?id=".$_SESSION['uid']);
//             exit;
//         }
//     }
// }

//获取一个月从几号到几号
function getTime($date, $ye = '0'){
    if (!$ye) {
        $time = time();
        $y = date('Y', $time);
    }else{
        $y = $ye;
    }
    // echo $y.$date.'01';
    $ss = strtotime($y . $date . '01');
    $sss = strtotime('-1 day', $ss);
    $ssss = strtotime('-1 month', $ss);
    // date('Y/m/d H:i:s',$ss);
    $arr = array('end' => date('Y年m月d日', $sss), 'stat'=>date('Y年m月d日', $ssss));
    return $arr;
}

//obj转成array
function objectToArray($obj){
    $arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_array($arr)) {
        return array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

//array转成obj
function arrayToObject($arr){
    if (is_array($arr)) {
        return (object) array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}

function token(){
    return md5(TIME . mt_rand(111111,999999));
}

function orderId(){
    return 'M' . strtoupper(substr(md5(TIME), mt_rand(0, 9), 5)) . TIME . mt_rand(111111, 999999);
}


/**
 * [mrand 随机一段数组出来]
 * @param  [int]  $start  [开头长度]
 * @param  [int]  $num    [随机多少个]
 * @param  [int]  $length [随机长度]
 * @param  integer $mleng [随机密码长度]
 * @param  array   $noarr [不在这个数组中]
 * @return [array]        [随机一段数组]
 */
function mrand($start, $num, $length, $mleng = 8, $noarr = array()){
  $arr = array();
  $length = $length - strlen($start);

  for ($j = 0; count($arr) != $num; $j++) {
    $ss = $start;
    for ($i = 0; $i < $length; $i++) {
     $ss .= mt_rand(0, 9);
    }
    if (!in_array($ss, $arr) && !in_array($ss, $noarr)) {
      $mm = '';
      for ($i = 0; $i < $mleng; $i++) {
       $mm .= mt_rand(0, 9);
      }
      $arr[$ss] = $mm;
    }
  }
  return $arr;
}


/**
 * [timeToDay 当前时间戳之后的时间戳]
 * @param  [int] $now [当前时间戳]
 * @param  [int] $num [天数]
 * @return [int]      [之后的时间戳]
 */
function timeToDay($now, $num){
    $now += $num * 24 * 60 * 60;
    return $now;
}


/**
 * [timeArray 获取时间戳直接相差的 天时分秒]
 * @param  [int] $now   [当前时间戳]
 * @param  [int] $old   [以前时间戳]
 * @return [array]      [返回所有记录]
 */
function timeArray($now, $old){

    //时间戳差值
    $array['cha'] = ($now - $old);

    //相差的秒数
    $array['miao'] = $array['cha'] % 60;

    //相差的分数
    $array['fen']= ($array['cha'] - $array['miao']) % (60 * 60) / 60;

    //相差的时数
    $array['shi'] = ($array['cha'] - $array['miao'] - $array['fen'] * 60) / (60 * 60) % 24;

    //相差的天数
    $array['tian'] = ($array['cha'] - $array['miao'] - $array['fen'] * 60 - $array['shi'] * (60 * 60)) / (60 * 60 * 24);

    return $array;
}

/**
 * [afterDayTime 获取今天的几天后或者几天前]
 * @param  [number] $day [数值正负都可以]
 * @return [time]        [时间类型 年月日]
 */
function afterDayTime($day){
    return strtotime(date('Y-m-d', strtotime($day . ' days')));
}