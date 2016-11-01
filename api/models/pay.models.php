<?php

/**
 * 订单模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class pay extends core\models
{

    public static $fxarr=array();

    public function generateOrder($arr,$uid)
    {
        $array=array();

        if(is_array($arr)){
            $str=implode(' or id= ',$arr);
        }else{
            $str=$arr;
        }

        $re=$this
        ->table('gouwuche')
        ->field('id,money,shopid,type,money,title,psf,sjid,shopName,num,headerpic,state')
        ->where('userid='.$uid)
        ->where('id='.$str)
        ->where('state=1')
        ->select();

        if(count($arr)!=count($re)){
            $this->error='cartIdError';
            return false;
        }
        $this
        ->table('gouwuche')
        ->where('id='.$str)
        ->save(array('state'=>2));

        $money=array_column($re,'money');
        $num=array_column($re,'num');
        $psf=array_column($re,'psf');
        $all=0;
        foreach ($money as $key => $value) {
            $all+=$num[$key]*$value;
        }
        $all=array_sum($psf)+$all;
        $id=array_column($re,'id');

        $array['orderno']=orderId();
        $array['createtime']=time();
        $array['statue']=0;
        $array['uid']=$uid;
        $array['totalprice']=$all;
        $array['realprice']=$all;
        $array['gwcid']=implode(',', $id);
        $array['relainfo']=json_encode($re,JSON_UNESCAPED_UNICODE);

        $re=$this
        ->table('order')
        ->create($array);


        return array('id'=>$this->insert_id());
    }


    public function getlist($arr,$id)
    {
        $addressid=$arr['addressid'];
        $re=$this
        ->table('order')
        ->field('gwcid,orderno,realprice,statue,createtime,relainfo')
        ->where('id='.$arr['id'])
        ->order('id desc')
        ->getOne();

        if(!$re){
            $this->error='orderMiss';
            return false;
        }
        $array['orderno']=$re['orderno'];
        $array['realprice']=$re['realprice'];
        $array['createtime']=date('Y-m-d H:i:s',$re['createtime']);
        $array['statue']=$re['statue'];
        $arr=explode(',', $re['gwcid']);

        if(is_array($arr)){
            $str=implode(' or id= ',$arr);
        }else{
            $str=$arr;
        }

        $re=new cart();
        $a=$re->getlist($str,0);
        // $a=json_decode($re['relainfo'],true);

        $arr=array(
                'userid'=>$id,
            );

        $addressid && $arr['id']=$addressid;
        $addressid || $arr['isdefault']=1;

        $re=$this
        ->table('address')
        ->where($arr)
        ->getOne();

        $array['order']=$a;
        if($re){
            $array['address']=$re;
        }

        return $array;
    }

    public function pay($arr,$id)
    {


        $re=$this
        ->table('order')
        ->field('gwcid,orderno,realprice,statue,relainfo')
        ->where('id='.$arr['id'])
        ->getOne();

        if(!$re){
            $this->error='orderMiss';
            return false;
        }
        if($re['statue']!=0){
            $this->error='orderPayd';
            return false;
        }
        $array=$this->coupon($arr,$id,$re['realprice']);

        $relainfo=json_decode($re['relainfo'],true);

        $this->changeShopMoney($relainfo);
// yhq_type
// yhq_other
        // print_r($coupon);
        // print_r($couponid);
        // exit;

        $re=$this
        ->table('address')
        ->where(array('userid'=>$id,'id'=>$arr['addressid']))
        ->getOne();

        if(!$re){
            $this->error='addressMiss';
            return false;
        }

        $a=$this->money($arr,$id,$array['realprice']);
        if(!$a){
            return false;
        }

        $array['statue']=1;
        $array['useraddid']=$arr['addressid'];
        $array['paytype']=$arr['paytype'];
        $array['zhifutime']=time();
        $array['id']=$arr['id'];


        $re=$this
        ->table('order')
        ->where(array('uid'=>$id))
        ->create($array);


        if(!$re){
            $this->error='payError';
            return false;
        }

        $array=$this->getlist($arr,$id);


        return $array;
    }

    public function money($arr,$id,$money)
    {

        if($arr['paytype']==4){
            $usermoney=$this
            ->table('user')
            ->field('money')
            ->find($id);
            $usermoney['money']-=$money;
            if($usermoney['money']<0){
                $this->error='lackMoney';
                return false;
            }
            $usermoney['id']=$id;
        }

        $moneyLog=array(
            'userid'=>$id,
            'changetype'=>1,
            'money'=>$money,
            'paytype'=>$arr['paytype'],
            'typeid'=>$arr['id'],
            'type'=>1,
            'createtime'=>time(),
        );
        if($arr['paytype']==4){
            $moneyLog['nowmoney']=$usermoney['money']-$money;
            $moneyLog['changemoney']=$usermoney['money'];
            $moneyLog['money']=$money;
            $this
            ->table('user')
            ->create($usermoney);
        }else{
            $moneyLog['paymoney']=$money;
            $moneyLog['money']=$money;
        }
        $re=$this
        ->table('money')
        ->create($moneyLog);
        return 1;
    }


    public function checkPay($uid)
    {
        $a=$this->table('user')->field('is_renzhen')->find($uid);

        return $a;
    }

    public function paywx($arr)
    {
        // print_r($arr);
        $a=$this
        ->table('sp')
        ->find($arr['shopid']);
        // print_r($a);
        if(!$a){
            $this->error='shopMiss';
            return false;
        }
        $money=$a['jg']*$arr['num']+$a['psf'];

        $re=$this->fenxiao(array('userid'=>$arr['userid'],'classify'=>$a['classify']));

        $re=$this->money(array('paytype'=>$arr['paytype'],'id'=>$arr['shopid']),$arr['userid'],$money);
        if(!$re){
            return false;
        }

        $re=array();
        $re['id']=$a['id'];
        $re['money']=$a['jg'];
        $re['shopid']=$a['id'];
        $re['type']=2;
        $re['title']=$a['name'];
        $re['psf']=$a['psf'];
        $re['sjid']=$a['publishuserid'];
        $re['shopName']='';
        $re['num']=$arr['num'];
        $re['headerpic']=$a['headeImg'];
        $re['state']=1;

        $array['fxspid']=$a['id'];
        $array['useraddid']=$arr['addressid'];
        $array['paytype']=$arr['paytype'];
        $array['zhifutime']=time();
        $array['orderno']=orderId();
        $array['createtime']=time();
        $array['statue']=1;
        $array['uid']=$arr['userid'];
        $array['totalprice']=$money;
        $array['realprice']=$money;
        $array['gwcid']=0;
        $array['relainfo']=json_encode($re,JSON_UNESCAPED_UNICODE);

        $arr=self::$fxarr;

        if($arr){
            foreach ($arr as  $value) {
                switch ($value['level']) {
                    case '1':
                        $array['fxoneuid']=$value['userid'];
                        break;
                    case '2':
                        $array['fxtwouid']=$value['userid'];
                        # code...
                        break;
                    case '3':
                        $array['fxthreeuid']=$value['userid'];
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        $re=$this
        ->table('order')
        ->create($array);

        return true;
    }

    public function fenxiao($arr)
    {
        //是分销商的情况下把分销的金额生成
        $re=$this
        ->table('fenxiao')
        ->where($arr)
        ->getOne();

        if(!$re){
            return true;
        }
        $arr=array();
        $arr['pid']=$re['pid'];
        $arr['classify']=$re['classify'];
        $a=$this->fxsj($arr);
        if(!$a){
            return false;
        }

    }

    public function fxsj($arr)
    {
        $re=$this
        ->table('fenxiao')
        ->where($arr)
        ->getOne();
        if(!$re){
            $this->error='fxMiss';
            return false;
        }
        self::$fxarr[]=array('level'=>$re['level'],'userid'=>$re['userid']);
        if($re['level']==1){
            return true;
        }
        $arr=array();
        $arr['userid']=$re['pid'];
        $arr['classify']=$re['classify'];

        $this->fxsj($arr);
    }

    public function coupon($arr,$id,$realprice)
    {

        $coupon= new coupon();
        $couponid=$coupon->shopList($arr,$id);

        $couponarr=array_column($couponid,'id');
        $this->couponid=$code='';

        $array['realprice']=$realprice;
        if(in_array($arr['couponid'],$couponarr)){
            foreach ($couponid as $value) {
                if($value['id']==$arr['couponid']){
                    $array['yhj_id']=$value['id'];
                    switch ($value['yhq_type']) {
                        case '1':
                            $array['realprice']=$realprice-$value['yhq_other'];
                            $array['yhxx']=$value['yhq_other'];
                            break;
                        case '2':
                            $array['yhxx']='咖啡券';
                            $code=token();
                            break;
                        case '3':
                            $array['realprice']=$realprice-($realprice*$value['yhq_other']);
                            $array['yhxx']=$realprice*$value['yhq_other'];
                            break;
                        case '4':

                            $coupon->checkCoupon(array('id'=>$value['yhq_other'],'uid'=>$id));
                            break;
                        case '5':
                            $array['yhxx']=$value['yhq_other'];
                            break;
                        default:
                            # code...
                            break;
                    }
                    $coupon->useCoupon($arr['id'],$arr['couponid'],$value['yhq_type'],$code);
                    $this->couponid=$arr['couponid'];
                }
            }
        }
        return $array;
    }


    public function changeShopMoney($arr)
    {
        $array=array();


        $aff=$this->getCouponMoney();

        $check=$money=0;

        foreach ($arr as $key => $value) {
            if($aff && $check==0){
                if($value['type']==$aff['limit_type'] &&
                    in_array($value['shopid'],explode(',', $aff['type_id'])) &&
                    ($aff['limit_money']<$value['money']*$value['num'])){
                    $check=1;
                    if($aff['yhq_type']==1){
                        $money=$aff['yhq_other'];
                    }elseif($aff['yhq_type']==2){
                        $money=$value['money']*$value['num']*$aff['yhq_other'];
                    }
                }
            }
            $array['id']=$value['sjid'];
            $array['money_no']=' money_no + '.($value['money']*$value['num']-$money);
            $this
            ->table('user_shop')
            ->create($array);
        }
        return true;
    }

    public function getCouponMoney()
    {
        $re='';
        if($this->couponid){
            $info=new coupon();
            $re=$info->getInfo($this->couponid);
        }
        // $this->table('youhuiquan')->find($this->couponid);
        return $re;
    }
}
?>