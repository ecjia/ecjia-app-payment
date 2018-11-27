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

    public function refund($trade_no);

}