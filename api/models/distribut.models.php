<?php

/**
 *分销商模版
 *
 *
 *在上一级同意之后在去把当前商户分类下的商品加到fenxiao_sp表中
 *在上一级同意之后在去把数量加一
 *
 *
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class distribut extends core\models
{

    public $table = 'user_shop';


    public function shoplist()
    {
    	$re=$this->table('user_shop')->field('id,shopName,face')->select();

        return $re;
    }
    public function become($arr)
    {

        $a=$this->checkFenxiao($arr);
        if(!$a){
            return false;
        }
        $arr['state']=1;

    	$re=$this->table('fenxiao_time')->create($arr);

        return $re;
    }
    public function checkFenxiao($arr)
    {
        $a=$this
        ->table('user')
        ->field('id,is_fenxiao')
        ->find($arr['userid']);

        if($a['is_fenxiao']){
            $this->error='fxUserYes';
            return false;
        }
        $a['is_fenxiao']=1;
        $this->table('user')->create($a);
        return true;
    }
    public function typelist($arr)
    {
    	$re=$this
    	->table('sp_classify')
    	->field('id,title')
    	->where($arr)
    	->fetchSql(0)
    	->select();
    	return $re;
    }
    public function classify($arr)
    {
    	$array=array();
    	foreach ($arr as $key => $value) {
    		$array[]=' f.'.$key.'='.$value;
    	}

    	$a=$this
    	->table('sp_classify')
    	->field('onenum,twonum,threenum')
    	->find($arr['classify']);

    	$array[]=' f.state=2';
    	$str=implode(' and ', $array);
    	$re=$this
    	->table('fenxiao')
        ->alias('f')
        ->join('user','u')
        ->joinLink('u.id=f.userid')
    	->joinField('f.id,f.userid,u.face,u.UserName')
    	->where($str)
    	->fetchSql(0)
    	->select();
    	$a['list']=$re;
    	return $a;
    }


    public function splist($arr)
    {
    	$a=$this
    	->table('fenxiao_sp')
        ->alias('fs')
        ->join('sp','s')
        ->joinLink('s.id=fs.spid')
    	->joinField('fs.id,fs.creattime,s.name,s.jg,s.onemoney,s.twomoney,s.threemoney,fs.level')
    	->where('fs.userid='.$arr['userid'])
    	->where(' is_show=1 and is_delete=1')
    	->page($arr['page'],$arr['pagesize'])
    	->order('id desc')
    	->fetchSql(0)
    	->select();

    	if(!$a){
    		return array();
    	}

    	foreach ($a as $key => $value) {
    		switch ($value['level']) {
    			case '1':
    				$a[$key]['commission']=$value['onemoney'];
    				break;
    			case '2':
    				$a[$key]['commission']=$value['twomoney'];
    				break;
    			case '3':
    				$a[$key]['commission']=$value['threemoney'];
    				break;

    			default:
    				$a[$key]['commission']=0;
    				break;
    		}
    		unset($a[$key]['onemoney']);
    		unset($a[$key]['level']);
    		unset($a[$key]['twomoney']);
    		unset($a[$key]['threemoney']);
    	}

    	return $a;
    }

    public function junior($arr)
    {
    	// $a=$this->successLow();
    	// var_dump($a);
    	// exit;

    	$a=$this
    	->table('fenxiao')
        ->alias('f')
        ->join('user','u')
        ->joinLink('u.id=f.userid')
    	->joinField('u.id,f.creattime,u.UserName,u.face')
    	->where('f.pid='.$arr['userid'])
    	->where(' state=2')
        ->page($arr['page'],$arr['pagesize'])
    	->fetchSql(0)
    	->select();
    	return $a;
    }

    public function orderList($arr)
    {
        $userid=$arr['userid'];
        $a=$this
        ->table('order')
        ->alias('o')
        ->join('sp','s')
        ->joinLink('s.id=o.fxspid')
        ->joinField('o.id,o.realprice,o.createtime,o.orderno,o.fxoneuid,o.fxtwouid,o.fxthreeuid,s.onemoney,s.twomoney,s.threemoney,o.relainfo')
        ->where(' o.fxoneuid='.$userid,'OR')
        ->where(' o.fxtwouid='.$userid,'OR')
        ->where(' o.fxthreeuid='.$userid,'OR')
        ->page($arr['page'],$arr['pagesize'])
        ->fetchSql(0)
        ->select();


        if($a){
            foreach ($a as $key => $value) {
                $re=json_decode($value['relainfo'],true);
                $a[$key]['money']=$this->searchMoney($value,$userid);
                $a[$key]['money']*=$re['num'];
                unset($a[$key]['fxoneuid']);
                unset($a[$key]['onemoney']);
                unset($a[$key]['fxtwouid']);
                unset($a[$key]['twomoney']);
                unset($a[$key]['fxthreeuid']);
                unset($a[$key]['threemoney']);
                unset($a[$key]['relainfo']);
            }
        }

        return $a;
    }

    public function searchMoney($arr,$userid)
    {
        if($arr['fxoneuid']==$userid){
            $money=$arr['onemoney'];
        }elseif($arr['fxtwouid']==$userid){
            $money=$arr['twomoney'];
        }elseif($arr['fxthreeuid']==$userid){
            $money=$arr['threemoney'];
        }

        return $money;
    }

    public function orderInfo($arr)
    {
        $userid=$arr['userid'];
        $a=$this
        ->table('order')
        ->alias('o')
        ->join('sp','s')
        ->joinLink('s.id=o.fxspid')
        ->joinField('s.sico,o.relainfo,o.id,o.realprice,o.createtime,o.orderno,o.fxoneuid,o.fxtwouid,o.fxthreeuid,s.onemoney,s.twomoney,s.threemoney')
        ->where(' o.id='.$arr['id'])
        ->where('(o.fxoneuid='.$userid.' or o.fxtwouid='.$userid.' or o.fxthreeuid='.$userid.')')
        ->fetchSql(0)
        ->getOne();
// {"id":"1","money":"30","shopid":"1","type":2,"title":"随手杯","psf":"0","sjid":"1","shopName":"","num":1,"headerpic":"/image/201610/14/1476442672991.png","state":2}
        if(!$a){
            $this->error='orderMiss';
            return false;
        }
        $re=json_decode($a['relainfo'],true);
        $a['num']=$re['num'];
        $a['price']=$re['money'];
        $a['state']=$re['state'];
        $a['title']=$re['title'];
        $a['fxmoney']=$this->searchMoney($a,$userid);
        $a['fxmoney']*=$re['num'];
        unset($a['fxoneuid']);
        unset($a['onemoney']);
        unset($a['fxtwouid']);
        unset($a['twomoney']);
        unset($a['fxthreeuid']);
        unset($a['threemoney']);
        unset($a['relainfo']);

        return $a;
    }

    public function userinfo($arr)
    {

        $a=$this
        ->table('user')
        ->field('telphone,UserName,face')
        ->find($arr['id']);
        $b=$this
        ->table('fenxiao')
        ->field('creattime')
        ->where('userid='.$arr['id'])
        ->order('id asc')
        ->getOne();
        if(!$a){
            $this->error='usermiss';
            return false;
        }
        if(!$b){
            $this->error='fxUserNo';
            return false;
        }
        $a=array_merge($a,$b);

        $c=$this
        ->table('order')
        ->alias('o')
        ->join('sp','s')
        ->joinLink('s.id=o.fxspid')
        ->joinField('s.sico,o.relainfo,o.id,o.realprice,o.createtime,o.orderno,o.fxoneuid,o.fxtwouid,o.fxthreeuid,s.onemoney,s.twomoney,s.threemoney')
        ->where('o.fxoneuid='.$arr['userid'].' and o.fxtwouid='.$arr['id'].')  or (fxtwouid='.$arr['userid'].' and fxthreeuid='.$arr['id'])
        ->order('id desc')
        ->fetchSql(0)
        ->select();

        if($c){
            foreach ($c as $key => $value) {
                $re=json_decode($value['relainfo'],true);
                $c[$key]['fxmoney']=$this->searchMoney($value,$arr['id']);
                $c[$key]['fxmoney']*=$re['num'];
                $c[$key]['num']=$re['num'];
                $c[$key]['title']=$re['title'];
                $c[$key]['state']=$re['state'];
                $c[$key]['money']=$re['money'];
                unset($c[$key]['fxoneuid']);
                unset($c[$key]['onemoney']);
                unset($c[$key]['fxtwouid']);
                unset($c[$key]['twomoney']);
                unset($c[$key]['fxthreeuid']);
                unset($c[$key]['threemoney']);
                unset($c[$key]['relainfo']);
            }
        }
        $a['list']=$c;

        return $a;

    }

    public function type($arr)
    {
        $a=$this
        ->table('fenxiao')
        ->alias('f')
        ->join('sp_classify','s')
        ->joinLink('f.classify=s.id')
        ->joinfield('f.id,s.title')
        ->where($arr)
        ->select();
        // print_r($a);
        // print_r($arr);
        // exit;
        return $a;
    }

    public function money($arr)
    {

        $a=$this
        ->table('fenxiao_money')
        ->alias('f')
        ->join('order','o')
        ->joinLink('f.orderid=o.id')
        ->joinField('o.createtime,f.money')
        ->select();

        return $a;

    }

    public function lower($arr)
    {

    	if($arr['classify']){
    		$a=$this->addchief($arr);
    	}else{
    		$a=$this->addLower($arr);
    	}
    	if(!$a){
    		return false;
    	}
    	return true;

    }

    public function addchief($arr)
    {

    	$a=$this->checkFx($arr['userid']);
    	if(!$a){
    		return false;
    	}
		$classify=$this->checkClass($arr['classify'],1);
    	if(!$classify){
    		$this->error='manOver';
    		return false;
    	}
    	$array['shopid']=$classify['shopid'];
    	$array['userid']=$arr['userid'];
    	$array['classify']=$arr['classify'];
    	$array['state']=2;

    	$a=$this->checkNo($array);

    	if($a){
    		$this->error='fxNo';
    		return false;
    	}

    	$a=$this->successLow(array(
    		'classify'=>$arr['classify'],
			'userid'=>$arr['userid'],
			'level'=>1
			));
    	if(!$a){
    		return false;
    	}

    	$array['creattime']=time();
    	$array['level']=1;
    	$array['pid']=0;

        $this->table('sp_classify')->create($classify);
    	$this->table('fenxiao')->create($array);

    	return true;
    }

    public function addLower($arr)
    {

    	$a=$this->checkFx($arr['userid']);
    	if(!$a){
    		return false;
    	}

    	$re=$this
    	->table('fenxiao')
    	->find($arr['id']);

    	if(!$re){
    		$this->error='fxMiss';
    		return false;
    	}

    	if($re['state']!=2){
    		$this->error='fxError';
    		return false;
    	}
    	if($re['level']==3){
    		$this->error='fxLevel';
    		return false;
    	}


    	$array['userid']=$arr['userid'];
    	$array['shopid']=$re['shopid'];
    	$array['classify']=$re['classify'];
    	$array['state']=2;

    	$a=$this->checkNo($array);

    	if($a){
    		$this->error='fxNo';
    		return false;
    	}

    	$classify=$this->checkClass($re['classify'],$re['level']+1);

    	if(!$classify){
    		$this->error='manOver';
    		return false;
    	}

    	$a=$this->successLow(array(
    		'classify'=>$re['classify'],
			'userid'=>$arr['userid'],
			'level'=>$re['level']+1
			));
    	if(!$a){
    		return false;
    	}

    	$array['creattime']=time();
    	$array['level']=$re['level']+1;
    	$array['pid']=$re['userid'];

        $this->table('sp_classify')->create($classify);
    	$this->table('fenxiao')->create($array);

    	return true;

    }

    public function checkFx($uid)
    {
    	$a=$this->table('user')->field('is_fenxiao')->find($uid);

    	if(!$a){
    		$this->error='usermiss';
    		return false;
    	}
    	if($a['is_fenxiao']==0){
    		$this->error='fxUserNo';
    		return false;
    	}
    	return true;
    }

    public function successLow($arr=array())
    {
    	// $arr['classify']=1;
    	// $arr['userid']=1;
    	// $arr['level']=1;

    	$a=$this->table('fenxiao_sp')->where('userid='.$arr['userid'].' and classify='.$arr['classify'])->count();

    	if($a){
    		$this->error='fxNo';
    		return false;
    	}


    	$a=$this
    	->table('sp')
    	->field('id,publishuserid')
    	->where('classify='.$arr['classify'])
    	->select();

    	$array['is_show']=1;
    	$array['userid']=$arr['userid'];
    	$array['is_delete']=1;
    	$array['sort']=0;
    	$array['classify']=$arr['classify'];
    	$array['creattime']=time();
    	$array['level']=$arr['level'];

    	foreach ($a as $key => $value) {
	    	$array['spid']=$value['id'];
	    	$array['shopid']=$value['publishuserid'];
	    	$this->table('fenxiao_sp')->create($array);
    	}

    	return true;
    }

    public function checkNo($arr)
    {
    	$a=$this->table('fenxiao')->where($arr)->select();

    	return $a;
    }



    public function checkClass($id,$level)
    {


    	$a=$this->table('sp_classify')->find($id);

    	if(!$a){
    		$this->error='classifyError';
    		return false;
    	}

    	switch ($level) {
    		case '1':
    			if($a['onenum']<=$a['onenow']){
    				$this->error='manOver';
    				return false;
    			}
    			$a['onenow']+=1;
    			break;
    		case '2':
    			if($a['twonum']<=$a['twonow']){
    				$this->error='manOver';
    				return false;
    			}
    			$a['twonow']+=1;
    			break;
    		case '3':
    			if($a['threenum']<=$a['threenow']){
    				$this->error='manOver';
    				return false;
    			}
    			$a['threenow']+=1;
    			break;

    		default:
    			# code...
    			break;
    	}
    	// $this->create($a);
    	return $a;

    }

}
?>