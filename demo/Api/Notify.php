<?php
namespace App\Api;

use PhalApi\Api;

/*
 * +----------------------------------------------------------------------
 * | 支付异步/同步回调
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015 summer All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: summer <aer_c@qq.com> <qq7579476>
 * +----------------------------------------------------------------------
 * | This is not a free software, unauthorized no use and dissemination.
 * +----------------------------------------------------------------------
 * | Date
 * +----------------------------------------------------------------------
 */

class Notify extends Api {

	public function getRules() {
        return array(
            'index' => array(
                'type' 	=> array('name' => 'type', 'type' =>'string', 'require' => true, 'desc' => '引擎类型，比如alipay'),
                'method'    => array('name' => 'method', 'type' =>'string', 'desc' => '回调类型，notify异步/return同步'),
            ),
        );
	}
	
    /**
     * 支付异步/同步回调
     * @return string 无 根据不同的引擎，返回不同的信息，如果错误信息，则存入日志
     */
	public function index() {
        $di = \PhalApi\DI();

        //获取对应的支付引擎
        $di->pay->set($this->type);
        
        //获取回调信息
        $notify = $GLOBALS['PAY_NOTIFY'];
        
        if(!$notify) {
            $di->logger->log('payError','Not data commit', array('Type' => $this->type));
            exit; //直接结束程序，不抛出错误
        }

        //验证
        if($di->pay->verifyNotify($notify) == true){
            //获取订单信息
            $info = $di->pay->getInfo();

            //TODO 更新对应的订单信息,返回布尔类型
            $res = true;
            
            //订单更新成功
            if($res){
                if ($this->method == "return") {
                    //TODO 同步回调需要跳转的页面
                } else {
                    $di->logger->log('paySuccess', 'Pay Success',array('Type' => $this->type, 'Method' => $this->method, 'Data'=> $info));

                    //移除超全局变量
                    unset($GLOBALS['PAY_NOTIFY']);

                    //支付接口需要返回的信息，通知接口我们已经接收到了支付成功的状态
                    $di->pay->notifySuccess();
                    exit; //需要结束程序
                }
            }else{
                $di->pay->notifyError();
                $di->logger->log('payError','Failed to pay', $info);
                exit;
            }
        }else{
            $di->pay->notifyError();
            $di->logger->log('payError','Verify error', array('Type' => $this->type, 'Method'=> $this->method, 'Data' => $notify));
            exit;
        }
	}
}









