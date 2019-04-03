<?php
namespace App\Api;

use PhalApi\Api;

/*
 * +----------------------------------------------------------------------
 * | 支付
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

class Pay extends Api {

	public function getRules() {
        return array(
            'index' => array(
                'type' 	=> array('name' => 'type', 'type' =>'enum', 'require' => true, 'range' => array('aliwap', 'wechat'), 'desc' => '引擎类型，比如aliwap')
            ),
        );
	}

	/**
	 * 支付接口
	 * @return [type] [description]
	 */
	public function index(){
        $di = \PhalApi\DI();

		//获取对应的支付引擎
        $di->pay->set($this->type);

        $data = array();
        $data['order_no'] = $di->pay->createOrderNo();
        $data['title'] = '测试的订单';
        $data['body'] = '测试的订单';
        $data['price'] = '0.01';
        echo $di->pay->buildRequestForm($data);
        exit;
	}
}
