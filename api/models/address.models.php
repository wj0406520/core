<?php

/**
 * 用户模版
 */


namespace api\models;

use core;

defined('ACC')||exit('ACC Denied');


class address extends core\models
{

	public $table='address';

    public function setDefault($uid,$id)
    {

    	$a=$this
    	->where(array('userid'=>$uid))
    	->save(array('isdefault'=>0));

    	$a=$this
    	->where(array('id'=>$id))
    	->save(array('isdefault'=>1));

        return $a;
    }

}
?>