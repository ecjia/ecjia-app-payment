<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 10:26
 */

namespace Ecjia\App\Payment\Pay;

use Ecjia\App\Payment\Contracts\ScanPayment;
use Ecjia\App\Payment\PaymentManagerAbstract;
use ecjia_error;

class ScanManager extends PaymentManagerAbstract
{

    protected $dynamic_code;

    public function scan($order_trade_no = null, $dynamic_code)
    {
        $this->dynamic_code = $dynamic_code;

        return $this->initPaymentRecord($order_trade_no);
    }

    /**
     * 退款插件处理
     *
     * @return array|ecjia_error
     */
    protected function pluginHandler()
    {
        if (! ($this->plugin_handler instanceof ScanPayment)) {
            return new ecjia_error('payment_plugin_not_support_scan_payment', $this->plugin_handler->getName().'支付方式不支持扫码收款操作');
        }

        $result = $this->plugin_handler->scan($this->payment_record->order_trade_no, $this->dynamic_code);

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