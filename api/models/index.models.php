<?php

/**
 * index模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class index extends core\models
{


    public function server()
    {

        $re=$this
        ->table('hotshow')
        ->alias('h')
        ->join('service','s')
        ->joinLink('s.id=h.seriveid')
        ->joinField('s.id,s.name,s.larioc,s.jg')
        ->where('h.type=1')
        ->limit(6)
        ->fetchsql(0)
        ->select();

        return $re;
    }

    public function shop()
    {

        $re=$this
        ->table('hotshow')
        ->alias('h')
        ->join('sp','s')
        ->joinLink('s.id=h.seriveid')
        ->joinField('s.id,s.name,s.larioc,s.jg')
        ->where('h.type=2')
        ->limit(4)
        ->fetchsql(0)
        ->select();

        return $re;
    }

    public function sendmsg($arr)
    {

        $a=$this
        ->table('phone_code')
        ->field('createtime')
        ->where(array('phone'=>$arr['phone'],'num'=>0))
        ->order('createtime desc')
        ->getOne();
        $time=$a['createtime']+60*30;

        if($a && $time>time()){
            $this->error='msgerror';
            return false;
        }

        $conf = core\conf::getIns();

        $code=mt_rand(100000,999999);
        $msg['userid']='';
        $msg['account']=$conf->account;
        $msg['password']=$conf->password;
        $msg['mobile']=$arr['phone'];
        $msg['content']=$conf->contentleft.$code.$conf->contentright;
        $msg['sendTime']='';
        $msg['action']='send';
        $msg['extno']='';
        $url=$conf->url;

        // $phone=$arr['phone'];

        $http=new \tool\HttpTool();
        $a=$http->get($url,$msg);
        $a=json_decode($a,true);
        if($a['returnstatus']!='Success'){
            $this->error('msgapierror');
            return false;
        }
        $arr['code']=$code;
        $arr['createtime']=time();
        $arr['num']=0;
        $a=$this
        ->table('phone_code')
        ->create($arr);
        return true;

    }

    public function search($arr)
    {
    // 瀚客资讯、瀚客公益、投融资、瀚客活动、瀚客预约、服务、商品

        // $a=$this
        // ->table('other_news')
        // ->joinfield('id,title,introduce,url,"1" as type')
        // ->where(array('title'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();

        // // print_r($a);

        // $a=$this
        // ->table('other_free')
        // ->joinfield('id,title,introduce,url,"2" as type')
        // ->where(array('title'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();

        // // print_r($a);
        // $a=$this
        // ->table('other_finance')
        // ->joinfield('id,title,introduce,url,"3" as type')
        // ->where(array('title'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();
        // // print_r($a);

        // $a=$this
        // ->table('huodong')
        // ->joinfield('id,hdbt as title,"" as introduce , "" as url,"4" as type')
        // ->where(array('hdbt'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();

        // $a=$this
        // ->table('yuyue_sp')
        // ->joinfield('id,spbt as title,"" as introduce , "" as url,"5" as type')
        // ->where(array('spbt'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();

        // $a=$this
        // ->table('service')
        // ->joinfield('id,name as title,"" as introduce , "" as url,"6" as type')
        // ->where(array('name'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();

        // $a=$this
        // ->table('sp')
        // ->joinfield('id,name as title,"" as introduce , "" as url,"7" as type')
        // ->where(array('name'=>array('like'=>$arr['search'])))
        // ->fetchSql(1)
        // ->select();
    // 1.瀚客资讯、2.瀚客公益、3.投融资、4.瀚客活动、5.瀚客预约、6.瀚客服务、7.瀚客商品

        $arr['page']=$arr['page']>1?$arr['page']:1;
        $this->str='SELECT  id,concat("【瀚客资讯】",title) as title,introduce,url,"1" as type  FROM t_other_news WHERE (title LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【瀚客公益】",title) as title,introduce,url,"2" as type  FROM t_other_free WHERE (title LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【投融资】",title) as title,introduce,url,"3" as type  FROM t_other_finance WHERE (title LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【瀚客活动】",hdbt) as title,"" as introduce , "" as url,"4" as type  FROM t_huodong WHERE (hdbt LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【瀚客预约】",spbt) as title,"" as introduce , "" as url,"5" as type  FROM t_yuyue_sp WHERE (spbt LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【瀚客服务】",name) as title,"" as introduce , "" as url,"6" as type  FROM t_service WHERE (name LIKE "%'.$arr['search'].'%")
              union
              SELECT  id,concat("【瀚客商品】",name) as title,"" as introduce , "" as url,"7" as type  FROM t_sp WHERE (name LIKE "%'.$arr['search'].'%")
              order by id desc
              limit '.($arr['page']-1)*$arr['pagesize'].','.$arr['pagesize'].'
        ';
        $a=$this->diySelect();

        return $a;


    }

    public function checkphone($phone,$code)
    {

        $a=$this
        ->table('phone_code')
        ->field('id')
        ->where(array('phone'=>$phone,'num'=>0,'code'=>$code))
        ->getOne();
        $arr=$a;
        $arr['num']=1;
        if($a){
            $this
            ->create($arr);
            return true;
        }else{
            $this->errorMsg('phonecodeerror');
        }

    }


    //输出错误
    public function errorMsg($data='')
    {
      if($data){
        $error=diyError($data);
      }else{
        $error=diyError($this->error);
      }
      $arr['code']=$error[0];
      $arr['msg']=$error[1];
      $this->renderForAjax($arr);
    }

    //输出json
    protected function renderForAjax($arr)
    {
      echo json_encode($arr,JSON_UNESCAPED_UNICODE);
      exit;
    }

}
?>