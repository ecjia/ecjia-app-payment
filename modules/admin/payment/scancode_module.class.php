<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/10/29
 * Time: 3:38 PM
 */

defined('IN_ECJIA') or exit('No permission resources.');

use Royalcms\Component\Shouqianba\Gateways\Shouqianba\Orders\PayOrder;

/**
 * 订单支付
 * @author royalwang
 * 16-12-09 增加支付状态
 */
class admin_payment_scancode_module extends api_admin implements api_interface
{

    /**
     * @param int $record_id 支付流水记录
     * @param string $dynamic_code 二维码或条码内容
     *
     * @param \Royalcms\Component\Http\Request $request
     */
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request)
    {
        if ($_SESSION['admin_id'] <= 0 && $_SESSION['staff_id'] <= 0) {
            return new ecjia_error(100, 'Invalid session');
        }

        $record_id = $this->requestData('record_id');
        $dynamic_code = $this->requestData('dynamic_code');

        if (empty($dynamic_code)) {
            return new ecjia_error('payment_scancode_content_not_empty', '扫码支付的二维码内容不能为空');
        }

        $record_model = (new Ecjia\App\Payment\Repositories\PaymentRecordRepository())->find($record_id);
        if (empty($record_model)) {
            return new ecjia_error('payment_record_not_found', '此笔交易记录未找到');
        }

        if ($record_model->pay_code != 'pay_shouqianba') {
            return new ecjia_error('payment_order_not_match', '此笔订单支付方式不匹配');
        }

        $payment_plugin	= new Ecjia\App\Payment\PaymentPlugin();
        $plugin_handler = $payment_plugin->channel($record_model->pay_code);

        $plugin_config = $plugin_handler->getConfig();

        if ($record_model->trade_type == 'buy') {
            /* 查询订单信息 */
            $orderinfo = RC_Api::api('orders', 'order_info', array('order_sn' => $record_model->order_sn));
            if (empty($orderinfo)) {
                return new ecjia_error('order_dose_not_exist', $record_model->order_sn . '未找到该订单信息');
            }

            $order = new PayOrder();
            $order->setClientSn($record_model->order_trade_no);
            $order->setTotalAmount($record_model->total_fee * 100);
            $order->setDynamicId($dynamic_code);
            $order->setSubject($_SESSION['store_name'] . '商户的订单：' . $orderinfo['order_sn']);
            $order->setOperator($_SESSION['staff_name']);

            $config = config('shouqianba::pay.shouqianba');
            $config['terminal_sn'] = $plugin_config['shouqianba_terminal_sn'];
            $config['terminal_key'] = $plugin_config['shouqianba_terminal_key'];
            $shouqianba = RC_Pay::shouqianba($config);
            $result = $shouqianba->pay(null, $order);

        }


//        dd($result);

        return $result;
    }

}