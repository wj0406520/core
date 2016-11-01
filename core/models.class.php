<?php
/**
 * author 王杰
 * time 2016-11-01
 * version 3.0.1
 * 数据库连贯操作类 实例
 * models.class.php
 */


//模型类
namespace core;

defined('ACC')||exit('ACC Denied');

class models extends linksql {

    public $str='';//数据库语句
    protected $table_array = array();     // 所有数据表
    public $table = NULL;                 // 是model所控制的表名
    protected $db = NULL;                 // 是引入的mysql对象
    protected $pref = NULL;               // 是表前缀

    protected $errno = NULL;              // 是错误码

    protected $where = NULL;              // 是where 操作的字段
    protected $field_array = NULL;        // 是当前表中所有字段
    protected $field = '*';               // 是field 操作的字段
    protected $mainkey = NULL;            // 是field 操作的字段
    protected $order = NULL;              // 是order 操作的字段
    protected $alias = NULL;              // 是alias 操作的字段
    protected $limit = NULL;              // 是page limit 操作的字段
    protected $group = NULL;              // 是 group 操作的字段
    protected $data = array();            // 是data 操作的字段
    protected $join = NULL;               // 是join 操作的字段
    protected $having = NULL;             // 是having 操作的字段
    protected $comment = NULL;            // 是comment 操作的字段
    protected $fetchSql = 0;              // 是fetchSql 操作的字段默认为0  运行为1


    //初始化函数
    public function __construct($table='')
    {
        //引入mysql对象    进行操作
        $this->db = mysql::getIns();
        $this->pref = $this->db->get_pref();
        $this->table_array=$this->db->show_tables();
        if($table){
            $this->table=$table;
            $this->check_table();
        }
        if($this->table){
            $this->check_table();
        }
    }


    /*
    判断table是否正确
    */
    protected function check_table()
    {
        if(strpos($this->table, $this->pref)===false){
            $this->table=$this->pref.$this->table;
        }
        if(!in_array($this->table, $this->table_array)){
            $this->errno='10001';
            $this->error($this->table);
        }
        $this->field_array=$this->db->desc_tables($this->table);
        $this->mainkey=$this->db->mainkey;
    }


    /*
    判断join table是否正确
    */
    protected function check_join_table($table)
    {

        if(!in_array($table, $this->table_array)){
            $this->errno='10001';
            $this->error($table);
        }
    }

    /*
    判断field是否正确
    */
    protected function check_field($key,$errno)
    {
        if(DEBUG==false){
            return true;
        }
        if(!in_array($key, $this->field_array)){
            $this->errno=$errno;
            $this->error($key);
        }

    }


    /*
    错误信息
    */
    public function error($msg='')
    {
        $str='';
        switch ($this->errno) {
            case '10001':
                $str='table error ' .$msg;
                break;
            case '10002':
                $str='where function field error '.$msg;
                break;

            case '10003':
                $str='field function field error '.$msg;
                break;

            case '10004':
                $str='data function field error '.$msg;
                break;

            case '10005':
                $str='order function field error '.$msg;
                break;

            case '10006':
                $str='group function field error '.$msg;
                break;

            case '10008':
                $str='create function field error '.$msg;
                break;

            case '20001':
                $str='limit function parms error ';
                break;

            case '20002':
                $str='page function parms error ';
                break;

            case '30001':
                $str='create function parms error ';
                break;
            case '30002':
                $str='save function parms error ';
                break;
            case '30003':
                $str='delete function parms error ';
                break;
            case '30004':
                $str='select function parms error ';
                break;

            case '40001':
                $str='save no data';
                break;

            default:
                $str='Unknown error';
                break;
        }
        exit($str);
    }
    /*
    错误码
    */
    public function errno()
    {
        return $this->errno;
    }

    /*
    条件筛选

    多次的数组条件表达式会最终合并，但字符串条件则只支持一次。

    parms $m 字符串 直接拼接 $this->where('status=1')

    parms $m 1级数组 连续拼接$this->where(array('b'=>1))

    parms $m 2级数组 条件拼接$this->where(array('a'=>array('b',1)))
                        EQ  等于（=）
                        NEQ 不等于（<>）
                        GT  大于（>）
                        EGT 大于等于（>=）
                        LT  小于（<）
                        ELT 小于等于（<=）
                        LIKE    模糊查询
                        [NOT] IN    （不在）IN 查询
                        in    IN 查询
                        finset   find_in_set 查询
    return bool
    */
    public function where($m='',$aoo='AND')
    {
        $arr=array();
        if(empty($m)){
            return $this;
        }
        if(is_string($m)){
            $arr[]=$m;
        }
        if(is_array($m)){
            foreach ($m as $key => $value) {
                if(is_array($value)){
                    $this->check_field($key,'10002');
                    foreach ($value as $k => $v) {
                        switch ($k) {
                            case 'eq':
                                $arr[]=$key.'="'.$v.'"';
                                break;
                            case 'neq':
                                $arr[]=$key.'<>"'.$v.'"';
                                break;
                            case 'gt':
                                $arr[]=$key.'>"'.$v.'"';
                                break;
                            case 'lgt':
                                $arr[]=$key.'>="'.$v.'"';
                                break;
                            case 'lt':
                                $arr[]=$key.'<"'.$v.'"';
                                break;
                            case 'elt':
                                $arr[]=$key.'<="'.$v.'"';
                                break;
                            case 'like':
                                $arr[]=$key.' LIKE "%'.$v.'%"';
                                break;

                            case 'noin':
                                $arr[]=$key.' NOT IN ('.$v.')';
                                break;

                            case 'in':
                                $arr[]=$key.' IN ('.$v.')';
                                break;

                            case 'finset':
                                $arr[]=' FIND_IN_SET ("'.$v.'",'.$key.')';
                                break;
                        }
                    }
                }else{
                    $this->check_field($key,'10002');
                    $arr[]=$key.'="'.$value.'"';
                }
            }
        }
        $this->link_where($arr,$aoo);
        return $this;
    }

    /*
    链接where语句
    */
    protected function link_where($arr,$aoo='AND')
    {
        $str=implode(' '.$aoo.' ', $arr);

        // var_dump($this->where);

        if(!$this->where){
            $this->where=' WHERE ('.$str.')';
        }else{
            $this->where.=' '.$aoo.' '.$str;
        }
        // print_r($this->where);
        return $this->where;
    }

    //获取当前表中的字段
    public function getFiled()
    {
        return $this->field_array;
    }

    //获取当前表中的主键
    public function getmainkey()
    {
        return $this->mainkey;
    }


    /*
    指定操作的数据表
    parms $table  数据库表名
    */
    public function table($table)
    {
        $this->table=$table;
        $this->check_table();
        return $this;
    }

    /*
    设置当前数据表的别名
    便于使用其他的连贯操作例如join方法
    parms $a  数据表的别名
    */
    public function alias($a)
    {
        $this->alias=' as '.$a;
        return $this;
    }


    /*
    设置当前要操作的数据对象的值
    parms $data 1级数组 连续拼接$this->data(array('b'=>1))
    */
    public function data($data)
    {
        foreach ($data as $key => $value) {
            $this->check_field($key,'10004');
            $this->data[$key]=$value;
        }
        return $this;
    }

    /*
    主要目的是标识要返回或者操作的字段，可以用于查询和写入操作
    parms $field  操作的字段 $this->field('id,title,content')
    parms $aoo  only 只查询  no 排除查询
    */
    public function field($field,$aoo='only')
    {


        $arr=explode(',', $field);
        $array=array();
        $str=($this->alias)?str_replace(' as ', '', $this->alias).'.':'';

        foreach ($arr as $value) {
            $this->check_field($value,'10003');
            $array[]=$str.$value;
        }

        if($aoo=='no'){
            $array=array_diff($this->field_array,$array);
        }

        $this->field=implode(',', $array);
        return $this;
    }

    /*
    用于对操作的结果排序
    如果没有指定desc或者asc排序规则的话，默认为asc
    parms $order  $this->order('id desc,status')               order by id desc,status asc
    parms $order  $this->order('id desc')                      order by id desc
    */
    public function order($order)
    {
        $arr=explode(',',$order);
        $array=array();
        foreach ($arr as $value) {
            $array[]=explode(' ',$value);
        }
        $arr=array();
        foreach ($array as $value) {
            $this->check_field($value['0'],'10005');
            $arr[$value['0']]=isset($value['1'])?$value['1']:'asc';
        }
        $array=array();
        foreach ($arr as $key => $value) {
            $array[]=$key.' '.$value;
        }

        $order=implode(',', $array);
        $this->order=' ORDER BY '.$order.' ';
        return $this;
    }


    /*
    主要用于指定查询和操作的数量
    parms $limit  操作的数量 $this->limit(10)       limit 0,10
    parms $limit  操作的数量 $this->limit('10,25')  limit 10,25
    */
    public function limit($limit)
    {

        $arr=explode(',',$limit);
        foreach ($arr as $value) {
            if(preg_match('/^[0-9]*$/', $value)==0){
                $this->errno='20001';
                $this->error();
            }
        }

        $this->limit=' LIMIT '.$limit;
        return $this;
    }


    /*
    分页查询
    parms $page  $this->page('1,10')           limit 0,10
    parms $page  $this->page(1,2)           limit 0,10
    */
    public function page($page,$page2='')
    {

        if(is_numeric($page)){
            $page=$page>1?$page:1;
            $this->limit=' LIMIT '.($page-1)*$page2.','.$page2;
        }else{
            $arr=explode(',',$page);
            foreach ($arr as $value) {
                if(preg_match('/^[0-9]*$/', $value)==0){
                    $this->errno='20002';
                    $this->error();
                }
            }
            $this->limit=' LIMIT '.($arr[0]-1)*$arr[1].','.$arr[1];
        }
        return $this;
    }


    /*
    结合合计函数,根据一个或多个列对结果集进行分组
    parms $group   $this->group('user_id')              GROUP BY user_id
    parms $group   $this->group('user_id,test_time')    GROUP BY user_id,test_time
    */
    public function group($group)
    {

        $arr=explode(',',$group);
        foreach ($arr as $value) {
            $this->check_field($value,'10007');
        }

        $this->group=' GROUP BY '.$group;
        return $this;
    }

    /*
    配合group方法完成从分组的结果中筛选
    having方法只有一个参数，并且只能使用字符串
    parms $having  操作的字段 $this->having('count(test_time)>3')     HAVING count(test_time)>3

    $this->field('username,max(score)')->group('user_id')->having('count(test_time)>3')->select();
    SELECT username,max(score) FROM think_score GROUP BY user_id HAVING count(test_time)>3

    */
    public function having($having)
    {
        $this->having=' HAVING '.$having;
        return $this;
    }

    /*
    根据两个或多个表中的列之间的关系
    要配合jionfield使用
    parms $join  字段名 $this->join('think_work','w','RIGHT')
    join方法的第三个参数支持的类型包括：INNER LEFT RIGHT FULL。
    */
    public function join($join,$as,$in='INNER')
    {
        if(strpos($join, $this->pref)===false){
            $join=$this->pref.$join;
        }
        $this->check_join_table($join);
        $this->join.=' '.$in.' join '.$join.' as '.$as.' ';
        return $this;
    }

    /*
    根据两个或多个表中的列之间的关系
    要配合jion使用
    parms $field  操作的字段 $this->jionField('w.id=s.fid')
    $this->table('ssssss')->alias('a')->join('think_work','w','RIGHT')->jionField('w.id=a.fid')->select()
    select * from ssssss as a right join think_work as w on w.id=a.fid
    */
    public function joinLink($field)
    {
        $this->join.=' ON '.$field;
        return $this;
    }

    /*
    join专用field
    要配合jion使用
    parms $field  操作的字段 $this->jionField('w.id,f.id as d')

    */
    public function joinField($field)
    {

        $this->field=' '.$field.' ';
        return $this;
    }

    /*
    用于在生成的SQL语句中添加注释内容
    parms $comment  字段名 $this->comment('查询考试前十名分数')   LIMIT 10 // 查询考试前十名分数
    */
    public function comment($comment)
    {
        $this->comment='/*'.$comment.'*/';
        return $this;
    }



    /*
    返回写入数据库的sql
    return sql
    */
    public function fetchSql($num=1)
    {
        $this->fetchSql=$num;
        $this->db->num=$num;
        return $this;
    }

    /*
    系统根据数据源是否包含主键数据来自动判断，
    如果存在主键数据更新数据
    如果不存在主键数据新增数据

    独立使用不支持连贯操作

    $data
    return bool
    */
    public function create($data='')
    {

        $this->check_table();
        $crea=0;
        if($data!=''){
            $this->data=$data;
        }

        foreach ($data as $key => $value) {
            $this->check_field($key,'10008');
            if($key==$this->mainkey){
                $crea=$value;
            }
        }

        if($crea==0){
            $rs=$this->autoExecute();
        }else{
            unset($this->data[$this->mainkey]);
            $rs=$this->autoExecute('update',' where '.$this->mainkey.'='.$crea);

        }

        if($rs){
            $this->clear();
            return true;
        }else{
            $this->clear();
            return false;
        }
    }


    /*
    更新操作包括更新数据和更新字段方法
    支持where连贯操作
    $data
    return bool
    */
    public function save($data='',$id=0)
    {
        $this->check_table();
        $crea=0;
        if($data!=''){
           $this->data=$data;
        }else{
            $data=$this->data;
        }


        foreach ($data as $key => $value) {
            $this->check_field($key,'10008');
            $crea=($key==$this->mainkey)?$value:0;
        }
        if($crea==0){
            if($this->where){
                $rs=$this->autoExecute('update',$this->where);
            }else{
                $rs=$id?$this->autoExecute('update',' where '.$this->mainkey.'='.$id):false;
            }
        }else{
            unset($this->data[$this->mainkey]);
            $rs=$this->autoExecute('update',' where '.$this->mainkey.'='.$crea);
        }

        $this->clear();
        return $rs?true:false;
    }


    /**
     * [autoExecute 自动合成sql]
     * @param  string $mode  [Insert新增 update更新]
     * @param  string $where [where数据]
     * @return [type]        [返回sql之后的数据]
     */
    protected function autoExecute($mode='insert',$where = ' ')
    {
        /*    insert into tbname (username,passwd,email) values ('',)
        /// 把所有的键名用','接起来
        // implode(',',array_keys($arr));
        // implode("','",array_values($arr));
        */
        $arr=$this->data;
        if(!is_array($arr)) {
            return false;
        }

        if($mode == 'update'){
            $sql = 'update ' . $this->table .' set ';

            foreach($arr as $k=>$v) {
                if(strpos($v,$k) === false){
                    if(is_numeric($v)){
                        $sql .= $k . "=" . $v .",";
                    }else{
                        $sql .= $k . "='" . $v ."',";
                    }
                }else{
                    $sql .= $k . "=" . $v .",";
                }
            }
            $sql = rtrim($sql,',');
            $sql .= $where;

        }else{
            $sql = 'insert into ' . $this->table . ' (' . implode(',',array_keys($arr)) . ')';
            $sql .= ' values (\'';

            // foreach($arr as $k=>$v) {
            //     if(is_numeric($v)){
            //         $sql .=    $v .",";
            //     }else{
            //         $sql .=  "'" . $v ."',";
            //     }
            // }
            // $sql = rtrim($sql,',');
            $sql .= implode("','",array_values($arr));
            $sql .= '\')';
        }

        return $this->db->query($sql);

    }


    /*
    删除数据使用delete方法
    支持where  order  limit连贯操作
    $delete    $User->delete('1,2,5')  删除主键为1,2和5的用户数据
    return bool
    */
    public function delete($delete='')
    {

        $this->check_table();
        if($delete==''){
            if($this->where){
                $sql = 'delete from ' .$this->table . $this->where .' '.$this->order.' '.$this->limit;
                if($this->db->query($sql)) {
                    $this->clear();
                    return $this->db->affected_rows();
                }else {
                    $this->clear();
                    return false;
                }
            }else{
                return false;
            }
        }else {
            $arr=explode(',',$delete);
            $num='0';
            foreach ($arr as $value) {
                if(preg_match('/^[0-9]*$/', $value)==0){
                    $this->errno='30003';
                    $this->error();
                }

                $sql = 'delete from ' .$this->table . ' where ' . $this->mainkey . '=' .$value;

                if($this->db->query($sql)) {
                    $num+=$this->db->affected_rows();
                } else {
                    $this->clear();
                    return false;
                }

            }
            $this->clear();
            return $num;
        }


    }
    /*
    查询数据
    return bool
    */
    public function select($num=0)
    {
        if($num){
            return $this->count();
        }
        $this->link_sql();
        $arr=$this->db->getAll($this->str);
        $this->clear();
        return $arr;
    }

    /*
    自定义查询
    return bool
    */
    public function diySelect($num=0)
    {

        $arr=$this->db->getAll($this->str);

        return $arr;
    }


    /*
    查询单个数据
    */
    public function getOne()
    {
        $this->link_sql();
        $arr=$this->db->getOne($this->str);
        $this->clear();
        return $arr;
    }

    /*
    查询单个数据
    */
    public function find($id)
    {
        $this->where($this->mainkey.'='.$id);
        $this->link_sql();
        $arr=$this->db->getOne($this->str);
        $this->clear();
        return $arr;
    }

    /*
    查询数据总量
    $save  0  清除参数
           1  不清除参数
    */
    public function count()
    {
        $this->field='count(*) as a';
        $this->limit='';
        $this->link_sql();
        $arr=$this->db->getOne($this->str);
        $arr=$arr['a'];
        $this->clear();
        return $arr;
    }

    /*
    **连接数据库语句
    */
    protected function link_sql()
    {
        $this->check_table();
        $this->str='SELECT '.
        $this->field. ' FROM ' .
        $this->table.
        $this->alias.
        $this->join.
        $this->where.
        $this->group.
        $this->having.
        $this->order.
        $this->limit;
    }

    /*
    **清除相关字段
    */
    protected function clear()
    {
        $this->where = NULL;              // 是where 操作的字段
        $this->field = '*';               // 是field 操作的字段
        $this->order = NULL;              // 是order 操作的字段
        $this->alias = NULL;              // 是alias 操作的字段
        $this->limit = NULL;              // 是page limit 操作的字段
        $this->group = NULL;              // 是 group 操作的字段
        $this->data = array();            // 是data 操作的字段
        $this->join = NULL;               // 是join 操作的字段
        $this->having = NULL;             // 是having 操作的字段
        $this->comment = NULL;            // 是comment 操作的字段
        $this->fetchSql = 0;              // 是fetchSql 操作的字段默认为0  运行为1
        $this->errno = NULL;                 // 是errno错误码
    }

    /*
    获取新增的id
    */
    public function insert_id()
    {
        return $this->db->insert_id();
    }

    /*
    开启事务
    */
    public function autocommit($bool=false){
        $this->db->autocommit($bool);
    }

    /*
    提交事务
    */
    public function commit(){
        $this->db->commit();
    }

    /*
    回滚事务
    */
    public function rollback(){
        $this->db->rollback();
    }

    /*
    直接执行sql语句
    */
    public function query($sql)
    {
        $rs=$this->db->query($sql);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

}

