<?php

namespace Ecjia\App\Payment;

class PaymentOutput 
{
    /**
     * 订单ID
     * @var integer
     */
    protected $order_id;
    
    /**
     * 订单编号
     * @var string
     */
    protected $order_sn;
    
    /**
     * 订单金额
     * @var float
     */
    protected $order_amount;
    
    /**
     * 订单支付状态
     * @var string
     */
    protected $order_pay_status;
    
    /**
     * 支付日志ID
     * @var integer
     */
    protected $pay_logid;
    
    /**
     * 支付插件code
     * @var string
     */
    protected $pay_code;
    
    /**
     * 支付插件名称
     * @var string
     */
    protected $pay_name;
    

    /**
     * 支付插件相关的数据
     * @var array
     */
    protected $pay_data = array(
    	'notify_url',
        'callback_url',
        'pay_order_sn',
        'subject',
        
        //余额支付
        'pay_status',
        'order_surplus',
    );
    
    /**
     * 支付相关的加密数据
     * @var array
     */
    protected $encrypted_data = array(
    	'app_secret',
        'private_key',
    );
    
    
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
        
        return $this;
    }
    
    
    public function getOrderId()
    {
        return $this->order_id;
    }
    
    
    public function setOrderSn($order_sn)
    {
        $this->order_sn = $order_sn;
        
        return $this;
    }
    
    
    public function setOrderAmount($order_amount)
    {
        $this->order_amount = $order_amount;
        
        return $this;
    }
    
    
    public function getOrderAmount()
    {
        return $this->order_amount;
    }
    
    
    public function setOrderPayStatus($order_pay_status)
    {
        $this->order_pay_status = $order_pay_status;
        
        return $this;
    }
    
    
    public function getOrderPayStatus()
    {
        return $this->order_pay_status;
    }
    
    
    public function setPayLogId($pay_logid)
    {
        $this->pay_logid = $pay_logid;
        
        return $this;
    }
    
    
    public function getPayLogId()
    {
        return $this->pay_logid;
    }
    
    public function setPayCode($pay_code)
    {
        $this->pay_code = $pay_code;
        
        return $this;
    }
    
    
    public function getPayCode()
    {
        return $this->pay_code;
    }
    
    public function setPayName($pay_name)
    {
        $this->pay_name = $pay_name;
        
        return $this;
    }
    
    
    public function getPayName()
    {
        return $this->pay_name;
    }
    
    
    public function setPayData(array $pay_data)
    {
        $this->pay_data = $pay_data;
        
        return $this;
    }
    
    public function getPayData()
    {
        return $this->pay_data;
    }
    
    
    public function setEncryptedData(array $encrypted_data)
    {
        $this->encrypted_data = $encrypted_data;
        
        return $this;
    }
    
    public function getEncryptedData()
    {
        return $this->encrypted_data;
    }
    
}