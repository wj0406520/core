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

    public $models = NULL;             //模型对象
    public $modelsname = NULL;         //模型名称
    public $controlsname = NULL;       //控制器名称
    public $check = 1;                 //登录限制
    public $error = '';                //错误信息
    public $userId = '';               //用户Id

    /**
     * controls constructor.
     */
    public function __construct()
    {
        $this->controlsname = str_replace('Action', '',  get_class($this));
        $this->modelsname = str_replace(CONTROLS, MODELS, $this->controlsname);
        $this->models();
        $this->before();

        // $arr=array(
        //     array('名字','必须有商品名','require'),
        //     array('123','2222','number'),
        //     array('0','3333','in','0,1'),
        //     array('11111111111','4444','length','10,100')
        //     );
        // var_dump($this->validate($arr));
        // print_r($this->error);
        // exit;
    }


    /**
     * @param string $models
     * @param string $path
     */
    public function models($models = '0', $path = '0')
    {

        if ($models === '0') {
            $models = $this->modelsname;
        } else {
            if ($path === '0') {
                $models = substr(APP,0,-1).'\\'.MODELS.'\\'.$models;
            } else {
                $models = $path.'\\'.$models;
            }
        }
        if (is_file(str_replace('\\', '/',ROOT.$models.'.'.MODELS.'.php'))) {
          $this->models = new $models();
        } else {
          $this->models = new models();
        }
        return $this->models;
    }

    /**
     * @param string $name
     */
    public function display($name = '0', $arr = array())
    {
        if ($arr) {
          foreach ($arr as $key => $value) {
            $$key = $value;
          }
        }
        if ($name === '0') {
            $file = VIEWSDIR.URL_MODEL.'/'.$this->getFunction().'.html';
        } else {
            $file = VIEWSDIR.URL_MODEL.'/'.$name.'.html';
        }

        if (is_file($file)) {
            require($file);
        } else {
            if (DEBUG) {
                echo 'not view file '. $file;
            } else {
                getRoot();
            }
        }

    }


    // $this->fill(array(
    //     array('page',1,'int'),
    //     array('pagesize',6,'int'),
    //   ))
    public function fill($array)
    {
      $a = array();
      $arr = IS_POST ? $_POST : $_GET;
      foreach ($array as $v) {
          switch ($v[2]) {
            case 'int':
              $a[$v[0]] = isset($arr[$v[0]]) ? (intval($arr[$v[0]]) ? intval($arr[$v[0]]) : $v[1]) : $v[1];
              break;
            case 'double':
              $a[$v[0]] = isset($arr[$v[0]]) ? (floatval($arr[$v[0]]) ? floatval($arr[$v[0]]) : $v[1]) : $v[1];
              break;
            case 'string':
              $a[$v[0]] = isset($arr[$v[0]]) ? $arr[$v[0]] : $v[1];
              break;
            case 'time':
              $a[$v[0]] = time();
              break;

            default:
              # code...
              break;
          }
      }

      return $a;
    }

    public function check($array)
    {
      $a = array();
      $arr = IS_POST ? $_POST : $_GET;
      foreach ($array as $v) {
        $a[$v] = isset($arr[$v]) ? $arr[$v] : '';
      }
      return $a;
    }

    public function checkArrayId($data)
    {
      if (is_array($data)) {
        foreach ($data as $value) {
          if (!is_numeric($value)) {
            return false;
          }
        }
      }else{
        if (!is_numeric($data)) {
          return false;
        }
      }
      return true;
    }

    public function checkSearch($array, $num = 0){
      $a= $an = array();
      $arr = IS_POST ? $_POST : $_GET;
      foreach ($array as $k => $v) {
        $an[$k] = $a[$k] = isset($arr[$k]) ? (is_string($arr[$k]) ? trim($arr[$k]) : $arr[$k]) : '';
        $s = explode(',', $v);
        if (in_array($a[$k], $s) || $a[$k] == ''){
            unset($a[$k]);
            $an[$k] = $s['0'];
        }
      }
      if ($num == 0) {
        return $a;
      }else{
        return $an;
      }
    }


    /*
        require   //必须包含
        number    //必须是数字
        time      //必须是时间
        in        //只能是参数之间的
        between   //必须在参数（数字）之间
        length    //长度在参数之间
        phone     //必须是电话号码
        card      //必须是身份证帐号
        email     //必须是email


    array(
        'phone'=>array('phone','phone'),
        'password'=>array('password','length','6,16'),
      );

    */
    public function validate($data)
    {
        if (empty($data)) {
            return true;
        }

        $arr = array();

        $array = IS_POST ? $_POST : $_GET;

        foreach ($data as $k => $v) {

              $d = isset($array[$k]) ? $array[$k] : '';


              $v[2] = isset($v[2]) ? $v[2] : '';

              if (!$this->contrast($d, $v[1], $v[2])) {

                  $this->error = $v[0];

                  $this->errorMsg();

                  return false;
              }

              $arr[$k]=$d;
        }

        return $arr;

    }

    //输出错误
    public function errorMsg($data = '')
    {
      $data = $data ? $data : $this->error;
      $arr = $this->getError($data);
      $this->renderForAjax($arr);
    }

    //输出成功
    public function success($data = array())
    {
      if (is_array($data)) {
        foreach ($data as $key => $value) {
          $data[$key] = isset($value) ? $value : '';
        }
      }
      $arr = $this->getError('success');

      $arr['data'] = $data;

      $this->renderForAjax($arr);
    }

    public function getError($data)
    {
      $error = diyError($data);
      $arr['code'] = $error[0];
      $arr['msg'] = $error[1];
      return $arr;
    }

    //输出json
    protected function renderForAjax($arr)
    {
      echo json_encode($arr, JSON_UNESCAPED_UNICODE);
      exit;
    }

    //匹配验证数据
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
            default:
                return false;
        }
    }

    public function before()
    {

    }

    public function redirect($path, $arr = array())
    {
        $url = array();
        $str = '';
        if ($arr) {
          foreach ($arr as $key => $value) {
            $url[] = $key . '=' . $value;
          }
          $url = implode('&', $url);
          $str = '?' . $url;
        }
        getRoot($path . $str);
    }

    /**
     * @return mixed
     */
    protected function getFunction()
    {
       $trace = debug_backtrace();

       $arr = array_column($trace, 'function', 'class');

       $name = str_replace('Action', '', $arr[$this->controlsname]);
       return $name;
       // foreach ($trace as $key => $value) {
       //     if($value['class']==$this->controlsname){
       //          return $trace[$key]['function'];
       //     }
       // }
    }





}
