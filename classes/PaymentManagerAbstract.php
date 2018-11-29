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

    protected $orderSn;

    protected $payCode;

    /**
     * @var \Ecjia\App\Payment\PaymentAbstract
     */
    protected $pluginHandler;

    protected $paymentRecord;

    /**
     * @var PaymentRecordRepository
     */
    protected $paymentRecordRepository;

    public function __construct($order_sn, $order_trade_no = null)
    {
        $this->order_sn = $order_sn;

        $this->paymentRecordRepository = new PaymentRecordRepository();

        if (! is_null($order_trade_no)) {
            $this->payment_record = $this->paymentRecordRepository->findBy('order_trade_no', $order_trade_no);
        } else {
            $this->payment_record = $this->paymentRecordRepository->findBy('order_sn', $this->order_sn);
        }
    }

    public function initPaymentRecord()
    {
        if (empty($this->payment_record)) {
            return new ecjia_error('payment_record_not_found', __('此笔交易记录未找到', 'app-payment'));
        }

        $this->pay_code = $this->payment_record->pay_code;

        $payment_plugin	= new PaymentPlugin();
        $this->plugin_handler = $payment_plugin->channel($this->payCode);
        if (is_ecjia_error($this->pluginHandler))
        {
            return $this->pluginHandler;
        }

        $this->pluginHandler->setPaymentRecord($this->paymentRecordRepository);

        return $this->doPluginHandler();
    }

    /**
     * 转让插件处理
     *
     * @return mixed
     */
    abstract protected function doPluginHandler();

}