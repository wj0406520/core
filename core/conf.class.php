<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  配置文件读写类
// +----------------------------------------------------------------------
namespace core;

defined('ACC') || exit('ACC Denied');

class Conf {

    //自己实例化
    protected static $ins = null;

    protected $data = [];

    final protected function __construct() {

        // 一次性把配置文件信息,读过来,赋给data属性,
        // 这样以后就不再管配置文件了.
        // 再要配置的值是,直接从 data属性找

        $this->data = include(CONFIG);
    }

    final protected function __clone() {
    }


    /**
     * [getIns 实现单例模式，实例化一些参数]
     * @return [type] [返回自己]
     */
    public static function getIns() {
        if (self::$ins instanceof self) {
            return self::$ins;
        } else {
            self::$ins = new self();
            return self::$ins;
        }
    }


    // 用魔术方法,读取data内的信息
    public function __get($key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            return null;
        }
    }


    // 用魔术方法,在运行期,动态增加或改变配置选项
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
}


// $conf = conf::getIns();


/****
// 已经能把配置文件的信息,读取到自身的 data属性中存储起来
// print_r($conf);


// 读取选项
echo $conf->host,'<br />';
echo $conf->user,'<br />';
var_dump($conf->template_dir);

// 动态的追加选项
$conf->template_dir = 'D:/www/smarty';

echo $conf->template_dir;
****/