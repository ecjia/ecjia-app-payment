<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/26
 * Time: 15:29
 */
namespace Ecjia\App\Payment\Callback;

use Ecjia\App\Payment\PaymentPlugin;
use Ecjia\App\Payment\Repositories\PaymentRecordRepository;

class RefundCallback
{

    protected $pay_code;

    public function __construct($pay_code)
    {
        $this->pay_code = $pay_code;

    }

    /**
     * 回调处理
     */
    public function callback()
    {
        $payment_plugin = new PaymentPlugin();
        $payment_list = $payment_plugin->getEnableList();

        $plugin = collect($payment_list)->first(function($value) {
            return $value['pay_code'] == $this->pay_code;
        });

        if (empty($plugin)) {
            return new \ecjia_error('payment_not_found', '插件未找到或已经被禁用！');
        }

        $payment_handler = $payment_plugin->channel($plugin['pay_code']);
        /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
        if (is_ecjia_error($payment_handler)) {
            return $payment_handler;
        }

        $payment_handler->setPaymentRecord(new PaymentRecordRepository());
        /**
         * 退款回调确认
         */
        return $payment_handler->refundCallback();
    }


}