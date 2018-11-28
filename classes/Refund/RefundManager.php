<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 10:26
 */

namespace Ecjia\App\Payment\Refund;

use Ecjia\App\Payment\Contracts\RefundPayment;
use Ecjia\App\Payment\PaymentManagerAbstract;
use ecjia_error;

class RefundManager extends PaymentManagerAbstract
{

    protected $total_fee;

    protected $operator;

    public function refund($order_trade_no, $total_fee, $operator)
    {
        $this->total_fee = $total_fee;
        $this->operator = $operator;

        return $this->initPaymentRecord($order_trade_no);
    }

    /**
     * 退款插件处理
     *
     * @return array|ecjia_error
     */
    protected function pluginHandler()
    {
        if (! ($this->plugin_handler instanceof RefundPayment)) {
            return new ecjia_error('payment_plugin_not_support__cancel_payment', $this->plugin_handler->getName().'支付方式不支持退款操作');
        }

        $result = $this->plugin_handler->refund($this->payment_record->order_trade_no, $this->total_fee, $this->operator);

        return $this->updateRefundStatus($result);
    }

    /**
     * 更新交易流水记录中的退款状态
     *
     * @param array $result
     */
    protected function updateRefundStatus($result)
    {
        return $result;
    }

}