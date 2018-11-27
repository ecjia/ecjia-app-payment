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

    public function refund($order_trade_no = null, $total_fee = null, $operator)
    {
        $this->total_fee = $total_fee;
        $this->operator = $operator;

        $paymentRecordRepository = new PaymentRecordRepository();

        if (!is_null($order_trade_no)) {
            $this->record_model = $paymentRecordRepository->findBy('order_trade_no', $order_trade_no);
        } else {
            $this->record_model = $paymentRecordRepository->findBy('order_sn', $this->order_sn);
        }

        if (empty($this->record_model)) {
            return new ecjia_error('payment_record_not_found', __('此笔交易记录未找到', 'app-payment'));
        }

        $this->pay_code = $this->record_model->pay_code;

        $payment_plugin	= new PaymentPlugin();
        $this->plugin_handler = $payment_plugin->channel($this->pay_code);
        if (is_ecjia_error($this->plugin_handler))
        {
            return $this->plugin_handler;
        }

        $this->plugin_handler->setPaymentRecord($paymentRecordRepository);

        return $this->pluginHandler();
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