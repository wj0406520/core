<?php

/**
 * banner模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class receipt extends core\models
{

    public $table = 'order';

    public function post($arr)
    {

    	$a=$this
    	->field('statue')
    	->find($arr['id']);
    	if(!$a){
    		$this->error='orderMiss';
    		return false;
    	}
    	if($a['statue']!=1){
    		$this->error='orderSh';
    		return false;
    	}


    	$this->create($arr);

        return true;
    }

}
?>