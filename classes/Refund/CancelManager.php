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

    protected $pay_code;

    protected $plugin_handler;

    protected $payment_record;

    public function __construct()
    {

    }

    public function cancel($trade_no)
    {

        $paymentRecordRepository = new PaymentRecordRepository();

        $this->record_model = $paymentRecordRepository->findBy('trade_no', $trade_no);
        if (empty($this->record_model)) {
            return new ecjia_error('payment_record_not_found', '此笔交易记录未找到');
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

        return $this->plugin_handler->cancel($this->record_model->trade_no);
    }

}