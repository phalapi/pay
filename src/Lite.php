<?php
namespace PhalApi\Pay;

/*
 * +----------------------------------------------------------------------
 * | 支付接口
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015 summer All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: summer <aer_c@qq.com> <qq7579476>
 * +----------------------------------------------------------------------
 * | Modified: dogstar <chanzonghuang@gmail.com>
 * +----------------------------------------------------------------------
 * | Date
 * +----------------------------------------------------------------------
 */

class Lite {

    /**
     * 支付驱动实例
     * @var Object
     */
    private $payer;

    /**
     * 配置参数
     * @var type 
     */
    private $config;

    /**
     * 获取引擎
     * @var string
     */
    private $engine;

    /**
     * 获取错误
     * @var string
     */
    public $error = '';

    public function __call($method, $arguments) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->payer) && $this->payer && method_exists($this->payer, $method)) {
            return call_user_func_array(array(&$this->payer, $method), $arguments);
        }
    }

    /**
     * 设置配置信息
     * @param string $engine 要使用的支付引擎
     * @param array  $config 配置
     */
    public function set($engine) {
        $di = \PhalApi\DI();

        /* 配置 */
        $this->engine = strtolower($engine);
        $this->config = array();

        $this->config['notify_url'] = $di->config->get('app.Pay.notify_url') . $this->engine . '/notify.php';
        $this->config['return_url'] = $di->config->get('app.Pay.notify_url') . $this->engine . '/return.php';
        
        //获取配置
        $config = $di->config->get('app.Pay.' . $this->engine);

        if(!$config){
          $di->logger->log('payError','No engine config', $this->engine);
          return false;
        }

        //合并配置
        $this->config = array_merge($this->config, $config);

        //设置引擎
        $engine = '\\PhalApi\\Pay\\Engine\\' . ucfirst(strtolower($this->engine));
        $this->payer = new $engine($this->config);
        if (!$this->payer) {
            $di->logger->log('payError','No engine class', $engine);
            return false;
        }
        
        return true;
    }
}
