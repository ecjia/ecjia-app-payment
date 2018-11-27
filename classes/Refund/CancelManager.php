<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 10:26
 */

namespace Ecjia\App\Payment\Refund;

use Ecjia\App\Payment\Contracts\CancelPayment;
use Ecjia\App\Payment\Repositories\PaymentRecordRepository;
use Ecjia\App\Payment\PaymentPlugin;
use ecjia_error;

class CancelManager
{
    protected $order_sn;

    protected $pay_code;

    protected $plugin_handler;

    protected $payment_record;

    public function __construct($order_sn)
    {
        $this->order_sn = $order_sn;
    }

    public function cancel($order_trade_no = null)
    {
        $paymentRecordRepository = new PaymentRecordRepository();

        if (is_null($order_trade_no)) {
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

        return $this->cancelPluginHandler();
    }


    protected function cancelPluginHandler()
    {
        if (! ($this->plugin_handler instanceof CancelPayment)) {
            return new ecjia_error('payment_plugin_not_support__cancel_payment', $this->plugin_handler->getName().'支付方式不支持退款操作');
        }

        $result = $this->plugin_handler->cancel($this->record_model->trade_no);

        return $this->updateRefundStatus($result);
    }

    /**
     * 更新交易流水记录中的退款状态
     *
     * @param array $result
     */
    protected function updateRefundStatus($result)
    {

        if ($result['result_code'] == 'PAY_SUCCESS') {



        } else {

        }

        return $result;
    }

}