<?php

/**
 * 公共的信息处理，对请求统一处理
 */
namespace api\controls;

use core\Models;
use core\Controls;
use api\models as am;

defined('ACC')||exit('ACC Denied');


class all extends Controls
{

	/**
	 * [before 运行之前的函数]
	 *
	 * 1.存入log信息
	 * 2.如果$check为1则对登录进行验证
	 */
    public function before()
    {

		// $a = new am\all();
    	// $a->setLog();

    	if ($this->check == 1) {

    		$this->checkToken();

    	}
    }

    /**
     * [checkToken 验证信息]
     * 1.验证数组中token字段
     * 2.删除数组中token字段
     * 3.验证通过存入用户id，没通过报错误
     */
    public function checkToken()
    {

		$post = $this->handle([
			'token' => ['length', 'token', '32,50'],
		]);

		unset($this->handle_array['token']);
		$a = new am\all();
		$a = $a->checkToken($post['token']);

		if ($a) {
			Models::$user_id = $a;
		} else {
			$this->errorMsg('token');
		}
    }

    /**
     * [checkSafePass 设置安全密码]
     *
     * 1.验证数组中pay_password字段
     * 2.删除数组中pay_password字段
     * 3.验证不通过报错，通过则什么都不做
     */
    public function checkSafePass()
    {
		$post = $this->handle([
            'type' => 'fill',
		]);
        if($post['type']==1){
            $post = $this->handle([
                'pay_password' => ['length', 'safePassError', '6,6'],
            ]);
    		unset($this->handle_array['pay_password']);
    		$a = new am\all();
    		$a = $a->checkSafePass($post['pay_password']);
        }

    }

    public function getZkOnlineKey()
    {
        $a = new am\all();
        $re = $a->getOnlineKey(1);
        return $re;
    }


    public function apiDoc()
    {
        $a = IS_POST ? $_POST : $_GET;
    	$str='';
    	foreach ($a as $key => $value) {
			$str.='|'.$key.'| 是 | string | 唯一标识符 |'."\r\n";
		}
		echo $str;
		exit;
    }
}