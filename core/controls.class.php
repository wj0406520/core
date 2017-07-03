<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  数据库类 实例
// +----------------------------------------------------------------------
namespace core;

defined('ACC')||exit('ACC Denied');


class Controls {

    public $models = NULL;              //模型对象
    public $models_name = NULL;         //模型名称
    public $controls_name = NULL;       //控制器名称
    public $check = 1;                  //登录限制
    public $error = '';                 //错误信息
    public $userId = '';                //用户Id
    public $web = 0;                    //web为1是web页面
    public $layout = 'layout.html';     //layout是布局文件
    public $models_path = '';           //模版的位置
    protected $handle_array = [];       //返回数据
    protected $data_array = [];         //用户传的数据

    /**
     * 1.控制器实例化的时候，检测模版的未知
     * 2.加载模型
     * 3.执行before方法
     */
    public function __construct()
    {

        $this->controls_name = str_replace('/', '\\',  APP.CONTROLS.'/'.URL_CONTROL);
        // $this->models_name = str_replace(CONTROLS, MODELS, $this->controls_name);

        if($this->models_path){
            $a=str_replace(CONTROLS, MODELS, $this->controls_name);
            $b=explode('\\', $a);
            $b[0]=$this->models_path;
            $this->models_name=implode('\\', $b);
        }else{
            $this->models_name=str_replace(CONTROLS, MODELS, $this->controls_name);
        }

        $this->models();

        $this->before();

    }

    /**
     * [__destruct 程序结束后运行after方法]
     */
   public function __destruct()
   {
      $this->after();
   }

    /**
     * [models 加载模型]
     * @param  string $models [模型名称]
     * @param  string $path   [模型空间]
     */
    public function models($models = '0', $path = '0')
    {

        $str='\\'.MODELS.'\\';

        if ($models === '0') {
            $models = $this->models_name;
        } else {
            if ($path === '0') {
                if ($this->models_path) {
                  $models = $this->models_path.$str.$models;
                } else {
                  $models = substr(APP,0,-1).$str.$models;
                }
            } else {
                $models = $path.'\\'.$models;
            }
        }

        if (is_file(str_replace('\\', '/',ROOT.$models.'.'.MODELS.'.php'))) {
          $this->models = new $models();
        } else {
          $this->models = new Models();
        }
        $this->models->web = $this->web;
        return $this->models;
    }

    /**
     * [display 实例化界面]
     * @param  string $name [界面名称]
     * @param  array  $arr  [参数]
     *
     *
     * 1.载入数据
     * 2.判断界面位置
     * 3.判断布局文件位置
     * 4.加载布局文件
     * 5.从布局文件加载内容界面
     */
    public function display($name = '0', $arr = array())
    {
        $conf = Conf::getIns();
        define('IMG_URL', $conf->img_url);

        if ($arr) {
          foreach ($arr as $key => $value) {
            $$key = $value;
          }
        }

        if ($name === '0') {
            $file = VIEWS_DIR.URL_CONTROL.'/'.URL_MODEL.'.html';
        } else {
            $file = VIEWS_DIR.URL_CONTROL.'/'.$name.'.html';
        }

        if (!is_file($file)) {
            debug('not view file ' . $file);
        }
        $layout = LAYOUT_DIR.'/'.$this->layout;
        if (!is_file($layout)) {
            // debug('not view layout '. $layout);
            require($file);
        }else{
            require($layout);
        }
    }

    /**
     * [redirect 跳转界面]
     * @param  [type] $path [跳转路径]
     * @param  array  $arr  [跳转带参]
     */
    public function redirect($path, $arr = array())
    {

        $str = '';
        if ($arr) {
          $str=http_build_query($arr);
          $str = '?' . $str;
        }

        getRoot($path . $str);
    }

      // $arr = $this->handle([
      //     'password'=>['length','password','6,16'],
      //     'phone'=>['phone','phone'],
      //     'name'=>['search','true',''],
      //     'sex'=>['search','false',''],
      //     'age'=>['fill','int',8],
      //     'time'=>['fill','time'],
      //     'double'=>['fill','double',8.88],
      //     'string'=>['fill','string','asdfas'],
      //     'id'=>['arr','int'],
      //     'im'=>['arr','string'],
      // ]);

    /**
     * [handle 数据处理]
     * @param  [array] $array [多层处理]
     * @return [array]        [处理结果]
     */
    public function handle($array)
    {
        if (empty($array)) {
            return true;
        }
        foreach ($array as $key => $value) {
            if(is_string($value)){
              $array[$key] = $this->diyHandle($value);
            }
        }

        $this->data_array = IS_POST ? $_POST : $_GET;

        foreach ($array as $key => $value) {
            $arr = explode(',',$key);
            foreach ($arr as $val) {
              switch ($value[0]) {
                case 'search':
                  $this->searchData($val,$value[1],$value[2]);
                  break;
                case 'fill':
                  $v=isset($value[2])?$value[2]:'';
                  $this->fillData($val,$value[1],$v);
                  break;
                case 'arr':
                  $this->arrData($val,$value[1]);
                  break;

                default:
                  $v=isset($value[2])?$value[2]:'';
                  $this->valData($val,$value[0],$value[1],$v);
                  break;
              }
            }
        }

        return $this->handle_array;
    }

    /**
     * [diyHandle 优化handle]
     * @param  [type] $v [handle名称]
     * @return [type]    [handle内容]
     */
    public function diyHandle($v)
    {
      $arr = [
        'fill'=>['fill','int','0'],
        'page'=>['fill','int','1'],
        'pagesize'=>['fill','int','10'],
        'phone'=>['phone','phone'],
        'search'=>['search',true,''],
        'file'=>['file','fileNo',''],
      ];
      $key = array_keys($arr);
      if(!in_array($v,$key)){
         $this->errorMsg('handleError');
      }
      return $arr[$v];
    }


    /**
     * [valData 自动验证数据]
     * @param  [string] $name  [名称]
     * @param  [string] $rule  [规则]
     * @param  [string] $error [错误名称]
     * @param  [string] $parm  [参数]
     */
    protected function valData($name,$rule,$error,$parm)
    {

        $arr=$this->data_array;

        $a = isset($arr[$name]) ? $arr[$name] : '';

        if (!$this->contrast($a, $rule, $parm)) {

            $this->error = $error;

            $this->errorMsg();

            return false;
        }

        $this->handle_array[$name]=$a;

    }

    /**
     * [arrData 数组数据]
     * @param  [string] $name [参数]
     * @param  [string] $type [类型]
     */
    protected function arrData($name,$type)
    {

        $check = ($type=='int') ? 'is_numeric' : 'is_string';

        $data = isset($this->data_array[$name]) ? $this->data_array[$name] : '';
        if (is_array($data)) {
          foreach ($data as $value) {
            if (!$check($value)) {
              $this->errorMsg('paramError');
            }
          }
        }else{
          if (!$check($data)) {
              $this->errorMsg('paramError');
          }
        }
        $this->handle_array[$name]=$data;
    }


    /**
     * [fillData 填充fill数据]
     * @param  [string] $name [参数]
     * @param  [string] $type [类型]
     * @param  [string] $val  [填充数据]
     */
    protected function fillData($name,$type,$val)
    {

        $arr=$this->data_array;

        switch ($type) {
          case 'int':
            $a = isset($arr[$name]) ? (intval($arr[$name]) ? intval($arr[$name]) : $val) : $val;
            break;
          case 'double':
            $a = isset($arr[$name]) ? (floatval($arr[$name]) ? floatval($arr[$name]) : $val) : $val;
            break;
          case 'string':
            $a = isset($arr[$name]) ? trim($arr[$name]) : $val;
            break;
          case 'time':
            $a = isset($arr[$name]) ? (strtotime($arr[$name]) ? strtotime($arr[$name]) : '') : '';
            break;

          default:
            # code...
            break;
        }

        $this->handle_array[$name]=$a;
    }

    /**
     * [searchData 检索search数据]
     * @param  [string] $name [参数]
     * @param  [string] $exit [true存在 false不存在]
     * @param  [string] $val  [检测数据]
     */
    protected function searchData($name,$exit,$val)
    {

        $arr=$this->data_array;

        $a = isset($arr[$name]) ? (is_string($arr[$name]) ? trim($arr[$name]) : $arr[$name]) : '';

        if (in_array($a, explode(',', $val)) || $a == ''){
          if ($exit=='false') {
            return true;
          }
        }

        $this->handle_array[$name]=$a;
    }


    /**
     * [contrast 匹配数据]
     * @param  [type] $value [匹配内容]
     * @param  string $rule  [匹配规则]
     * @param  string $parm  [匹配带参]
     * @return [boolen]      [匹配结果]
     */
    protected function contrast($value, $rule = '', $parm = '')
    {
        switch ($rule) {
            case 'require':
                return !empty($value);
            case 'number':
                return is_numeric($value);
            case 'time':
                return strlen($value) >= 4 && strtotime($value);
            case 'in':
                if (!$parm) {
                  $this->errpr[] =' IN lose parm ';
                }
                $tmp = explode(',', $parm);
                return in_array($value, $tmp);
            case 'between':
                if (!$parm) {
                  $this->errpr[]=' BETWEEN  lose parm ';
                }
                list($min,$max) = explode(',', $parm);
                return $value >= $min && $value <= $max;
            case 'length':
                if (!$parm) {
                  $this->errpr[] =' LENGTH  lose parm ';
                }
                list($min,$max) = explode(',', $parm);
                $len = mb_strlen($value, "utf-8");

                return $len >= $min && $len <= $max;
            case 'phone':
                return preg_match("/^1[34578]{1}\d{9}$/", $value);
            case 'card':
              return \tool\CardTool::checkIdCard($value);
              break;
            case 'email':
                return (filter_var($value,FILTER_VALIDATE_EMAIL) !== false);
              break;
            case 'file':
                if($parm==$value){
                  return true;
                }
                return is_file(DATA.$value);
              break;
            default:
                return false;
        }
    }

    /**
     * [errorMsg 输出错误信息]
     * @param  string $data [错误带的参数]
     */
    public function errorMsg($data = '')
    {
      $data = $data ? $data : $this->error;
      if($this->web){
        $arr = Error::getError($data, 0);
        message($arr['msg']);
      }else{
        $arr = Error::getError($data, 1);
      }
    }

    //web输出成功
    public function webSuccess($data = 'success', $path = '')
    {
      $arr = Error::getError($data, 0);
      message($arr['msg'],$path);
    }

    //输出成功
    public function success($data = array())
    {

      if (is_array($data)) {
        foreach ($data as $key => $value) {
          $data[$key] = isset($value) ? $value : '';
        }
      }

      $arr = Error::getError('success');

      $arr['data'] = $data;

      Error::renderForAjax($arr);
    }

    //控制器执行之前
    public function before()
    {

    }
    //控制器执行之后
    public function after()
    {

    }


}
