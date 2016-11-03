<?php
// +----------------------------------------------------------------------
// | author     王杰
// +----------------------------------------------------------------------
// | time       2016-11-01
// +----------------------------------------------------------------------
// | version    3.0.1
// +----------------------------------------------------------------------
// | introduce  数据库连贯操作类 接口
// +----------------------------------------------------------------------
namespace core;

defined('ACC')||exit('ACC Denied');

abstract class linksql {

    //数据库语句
    public $str;


    /*
    判断table是否正确
    */
    protected abstract function check_table();

    /*
    错误信息
    */
    public abstract function error();

    /*
    错误码
    */
    public abstract function errno();

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
                        [NOT] BETWEEN   （不在）区间查询
                        [NOT] IN    （不在）IN 查询

    parms $aoo 操作符  and or or
    return this
    */
    public abstract function where($m, $aoo = 'AND');


    //获取当前表中的字段
    public abstract function getFiled();

    //获取当前表中的主键
    public abstract function getmainkey();

    /*
    指定操作的数据表
    parms $table  数据库表名
    */
    public abstract function table($table);

    /*
    设置当前数据表的别名
    便于使用其他的连贯操作例如join方法
    parms $a  数据表的别名
    */
    public abstract function alias($a);


    /*
    设置当前要操作的数据对象的值
    parms $data 1级数组 连续拼接$this->data(array('b'=>1))
    */
    public abstract function data($data);

    /*
    主要目的是标识要返回或者操作的字段，可以用于查询和写入操作
    parms $field  操作的字段 $this->field('id,title,content')
    parms $aoo  only 只查询  no 排除查询
    */
    public abstract function field($field, $aoo = 'only');

    /*
    检测输入的字段问题
    parms $key  检测字段
    parms $errno 错误编码
    */
    protected abstract function check_field($key, $errno);



    /*
    用于对操作的结果排序
    如果没有指定desc或者asc排序规则的话，默认为asc
    parms $order  $this->order('id desc,status')               order by id desc,status asc
    parms $order  $this->order('id desc')                      order by id desc
    parms $order  $this->order(array('order','id'=>'desc'))    order by order asc , id desc
    */
    public abstract function order($order);

    /*
    主要用于指定查询和操作的数量
    parms $limit  操作的数量 $this->limit(10)       limit 0,10
    parms $limit  操作的数量 $this->limit('10,25')  limit 10,25
    */
    public abstract function limit($limit);

    /*
    分页查询
    parms $page  $this->page('1,10')           limit 0,10
    */
    public abstract function page($page);

    /*
    结合合计函数,根据一个或多个列对结果集进行分组
    parms $group   $this->group('user_id')              GROUP BY user_id
    parms $group   $this->group('user_id,test_time')    GROUP BY user_id,test_time
    */
    public abstract function group($group);

    /*
    配合group方法完成从分组的结果中筛选
    having方法只有一个参数，并且只能使用字符串
    parms $having  操作的字段 $this->having('count(test_time)>3')     HAVING count(test_time)>3

    $this->field('username,max(score)')->group('user_id')->having('count(test_time)>3')->select();
    SELECT username,max(score) FROM think_score GROUP BY user_id HAVING count(test_time)>3

    */
    public abstract function having($having);

    /*
    根据两个或多个表中的列之间的关系
    要配合jionfield使用
    parms $join  字段名 $this->join('think_work','w','RIGHT')
    join方法的第三个参数支持的类型包括：INNER LEFT RIGHT FULL。
    */
    public abstract function join($join, $as, $in = 'INNER');

    /*
    根据两个或多个表中的列之间的关系
    要配合jion使用
    parms $field  操作的字段 $this->jionField('w.id=s.fid')

    $this->table('ssssss')->alias('a')->join('think_work','w','RIGHT')->jionField('w.id=a.fid')->select()

    select * from ssssss as a right join think_work as w on w.id=a.fid

    */
    public abstract function joinLink($field);

    /*
    join专用field
    要配合jion使用
    parms $field  操作的字段 $this->jionField('w.id,f.id as d')

    */
    public abstract function joinField($field);

    /*
    用于在生成的SQL语句中添加注释内容
    parms $comment  字段名 $this->comment('查询考试前十名分数')   LIMIT 10 // 查询考试前十名分数
    */
    public abstract function comment($comment);

    /*
    返回写入数据库的sql
    return sql
    */
    public abstract function fetchSql();

    /*
    系统根据数据源是否包含主键数据来自动判断，
    如果存在主键数据更新数据
    如果不存在主键数据新增数据

    独立使用不支持连贯操作

    $data
    return bool
    */
    public abstract function create($data);

    /*
    更新操作包括更新数据和更新字段方法
    支持where连贯操作
    $data
    return bool
    */
    public abstract function save($data);

    /*
    删除数据使用delete方法
    支持where  order  limit连贯操作
    $delete    $User->delete('1,2,5')  删除主键为1,2和5的用户数据
    return bool
    */
    public abstract function delete($delete);

    /*
    查询数据
    return bool
    */
    public abstract function select();


    /*
    获取新增的id
    */
    public abstract function insert_id();
}


