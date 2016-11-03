<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  数据库底层类
// +----------------------------------------------------------------------
namespace core;

defined('ACC') || exit('ACC Denied');

class mysql extends db {

    //自身类
    private static $ins = NULL;

    //mysqli类
    private $mysqli = NULL;

    //主键
    public $main_key = NULL;

    //配置参数
    private $conf = array();

    //拦截因子
    public $num = 0;

    /**
     * [__construct 实例化]
     */
    protected function __construct()
    {
        //获取配置参数
        $this->conf = conf::getIns();
        //连接数据库
        $this->connect();
        //设置字符集
        $this->setChar();
    }

    //实例化自身
    public static function getIns()
    {
        if (!(self::$ins instanceof self)) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    //连接数据库
    public function connect()
    {
        $this->mysqli = new \mysqli($this->conf->host, $this->conf->user, $this->conf->pwd, $this->conf->db);
        if ($this->mysqli->connect_error) {
            printf("Connect failed: %s\n", $this->mysqli->connect_error);
            exit();
        }
    }
    /**
     * [setChar 设置字符集]
     */
    protected function setChar()
    {
        $sql = 'set names ' . $this->conf->char;
        return $this->query($sql);
    }

    /**
     * [query 发送sql语句到mysqli]
     * @param  [string]   $sql [sql语句]
     * @return [resource]      [资源类型]
     */
    public function query($sql)
    {
        //如果拦截因子存在输出sql语句
        if ($this->num) {
           echo $sql;
           echo '<br/>';
        }
        //发送sql语句
        $rs = $this->mysqli->query($sql);
        //如果sql失败  写入log文件
        if(!$rs){
            debug($sql);
        }

        return $rs;
    }

    //获取数据库前缀
    public function getPref()
    {
        return $this->conf->pref;
    }

    //显示当前数据库下的表
    public function showTables()
    {
        $sql = 'show tables';
        $arr = $this->getAll($sql);
        $array = array();
        foreach ($arr as $value) {
            $array[] = $value['Tables_in_' . $this->conf->db];
        }
        return $array;
    }

    /**
     * [descTables 显示当前表中所有字段，并获取主见名称]
     * @param  [string] $table [表名]
     * @return [array]         [所有字段]
     */
    public function descTables($table)
    {
        $sql = 'desc ' . $table;
        $arr = $this->getAll($sql);
        $array = array();
        foreach ($arr as $value)
        {
            if ($value['Key'] == 'PRI') {
                $this->main_key = $value['Field'];
            }
            $array[] = $value['Field'];
        }
        return $array;
    }

    /**
     * [getAll 获取所有数据]
     * @param  [string] $sql [sql语句]
     * @return [array]       [sql之后的所有数据]
     */
    public function getAll($sql)
    {
        $rs = $this->query($sql);

        $list = array();

        while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {
            $list[] = $row;
        }

        return $list;
    }

    /**
     * [getOne 获取第一条数据]
     * @param  [string] $sql [sql语句]
     * @return [array]       [sql之后的第一条数据]
     */
    public function getOne($sql)
    {
        $rs = $this->query($sql);
        $row = $rs->fetch_assoc();
        return $row;
    }

    // 返回影响行数的函数
    public function affectedRows()
    {
        return $this->mysqli->affectedRows;
    }

    // 返回最新的auto_increment列的自增长的值
    public function insertId()
    {
        return $this->mysqli->insertId;
    }

    /**
     * [autoCommit 开启事务]
     * @param  boolean $bool [真假值 真为开启自动提交 假为关闭自动提交]
     */
    public function autoCommit($bool = false){
        $this->mysqli->autoCommit($bool);
    }

    /**
     * [commit 提交事务]
     * @param  [boolean] $boolean [真则提交  假则回滚]
     */
    public function commit($boolean){
        if($boolean){
            $this->mysqli->commit();
        }else{
            $this->mysqli->rollback();
        }
    }


}
