<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 13:33
 */

namespace Ecjia\App\Payment\Contracts;


interface RefundPayment
{

    /**
     * 确认退款
     * @param string $order_trade_no 订单交易号
     * @param float $refund_amount 退款金额
     * @param string $operator 操作员
     * @return ecjia_error | array
     */
    public function refund($order_trade_no, $refund_amount, $operator);

}