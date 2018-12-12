<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 13:32
 */
namespace Ecjia\App\Payment\Contracts;

interface PayPayment
{

    /**
     * @param string $order_trade_no 交易号
     * @param int $record_id 交易记录id
     * @return array | \ecjia_error
     */
    public function pay($order_trade_no);

}