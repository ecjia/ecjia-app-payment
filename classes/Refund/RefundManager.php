<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/11/27
 * Time: 10:26
 */

namespace Ecjia\App\Payment\Refund;

use Ecjia\App\Payment\Repositories\PaymentRecordRepository;
use Ecjia\App\Payment\PaymentPlugin;
use RC_Pay;
use ecjia_error;

class RefundManager
{

    protected $pay_code;

    protected $plugin_handler;

    protected $payment_record;

    public function __construct($order_sn)
    {
//        $this->pay_code = $pay_code;



    }

    public function refund($trade_no)
    {

        $paymentRecordRepository = new PaymentRecordRepository();

        $record_model = $paymentRecordRepository->findBy('trade_no', $trade_no);
        if (empty($record_model)) {
            return new ecjia_error('payment_record_not_found', '此笔交易记录未找到');
        }

        $payment_plugin	= new PaymentPlugin();
        $plugin_handler = $payment_plugin->channel($record_model->pay_code);
        if (is_ecjia_error($payment_plugin))
        {
            return $payment_plugin;
        }
        
        $plugin_handler->setPaymentRecord($paymentRecordRepository);

        $plugin_config = $plugin_handler->getConfig();

        $config = config('shouqianba::pay.shouqianba');
        $config['terminal_sn'] = $plugin_config['shouqianba_terminal_sn'];
        $config['terminal_key'] = $plugin_config['shouqianba_terminal_key'];
        $shouqianba = RC_Pay::shouqianba($config);
        $result = $shouqianba->cancel($trade_no);

        return $result;
    }

}