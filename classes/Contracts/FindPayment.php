<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 13:32
 */
namespace Ecjia\App\Payment\Contracts;

interface FindPayment
{

    /**
     * @param $order_trade_no 交易号
     * @return mixed
     */
    public function find($order_trade_no);

}