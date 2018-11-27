<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 20:28
 */

namespace Ecjia\App\Payment\Query;

use Ecjia\App\Payment\Contracts\FindPayment;
use Ecjia\App\Payment\PaymentManagerAbstract;
use ecjia_error;

class FindManager extends PaymentManagerAbstract
{

    protected $order_sn;

    protected $pay_code;

    protected $plugin_handler;

    protected $payment_record;

    public function __construct($order_sn)
    {
        $this->order_sn = $order_sn;
    }

    public function find($order_trade_no = null)
    {
        return $this->initPaymentRecord($order_trade_no);
    }

    /**
     * 插件查询订单处理
     *
     * @return array|ecjia_error
     */
    protected function pluginHandler()
    {
        if (! ($this->plugin_handler instanceof FindPayment)) {
            return new ecjia_error('payment_plugin_not_support__cancel_payment', $this->plugin_handler->getName().'支付方式不支持退款操作');
        }

        $result = $this->plugin_handler->find($this->record_model->order_trade_no);

        return $result;
    }

}