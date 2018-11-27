<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 15:46
 */

namespace Ecjia\App\Payment;

use Ecjia\App\Payment\Repositories\PaymentRecordRepository;
use ecjia_error;

abstract class PaymentManagerAbstract
{

    protected $order_sn;

    protected $pay_code;

    protected $plugin_handler;

    protected $payment_record;

    public function __construct($order_sn)
    {
        $this->order_sn = $order_sn;
    }

    public function initPaymentRecord($order_trade_no = null)
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

        return $this->pluginHandler();
    }

    /**
     * 退款插件处理
     *
     * @return mixed
     */
    abstract protected function pluginHandler();

}