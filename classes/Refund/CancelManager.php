<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 10:26
 */

namespace Ecjia\App\Payment\Refund;

use Ecjia\App\Payment\Contracts\CancelPayment;
use ecjia_error;

class CancelManager extends RefundAbstract
{

    /**
     * 退款插件处理
     *
     * @return array|ecjia_error
     */
    protected function refundPluginHandler()
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



        return $result;
    }

}