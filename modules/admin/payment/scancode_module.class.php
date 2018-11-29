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

        $result = (new Ecjia\App\Payment\Refund\CancelManager(null))->cancel($trade_no);

        $paymentRecordRepository = new Ecjia\App\Payment\Repositories\PaymentRecordRepository();

        $record_model = $paymentRecordRepository->find($record_id);
        if (empty($record_model)) {
            return new ecjia_error('payment_record_not_found', '此笔交易记录未找到');
        }

        if ($record_model->pay_code != 'pay_shouqianba') {
            return new ecjia_error('payment_order_not_match', '此笔订单支付方式不匹配');
        }

        $payment_plugin	= new Ecjia\App\Payment\PaymentPlugin();
        $plugin_handler = $payment_plugin->channel($record_model->pay_code);
        $plugin_handler->setPaymentRecord($paymentRecordRepository);

        if ($record_model->trade_type == 'buy') {

            $orderinfo 	= $this->buyOrderProcessHandler($record_model);

        } elseif ($record_model->trade_type == 'quickpay') {

            $orderinfo = $this->quickpayOrderProcessHandler($record_model);

        } elseif ($record_model->trade_type == 'surplus') {

            $orderinfo = $this->surplusOrderProcessHandler($record_model);

        }

        if (empty($orderinfo)) {
            return new ecjia_error('order_dose_not_exist', $record_model->order_sn . '未找到该订单信息');
        }

        //小票打印数据
        $print_data = $this->_GetPrintData($record_model->trade_type, $orderinfo);

        $result = (new Ecjia\App\Payment\Pay\ScanManager(null))->scan($trade_no, $dynamic_code);
        if (is_ecjia_error($result)) {
            return $result;
        }

        $result['print_data'] = $print_data;

        return $result;
    }

    /**
     * 会员充值订单处理
     *
     * @param $record_model
     */
    protected function surplusOrderProcessHandler($record_model)
    {
        /* 查询订单信息 */
        $orderinfo = RC_Api::api('finance', 'user_account_order_info', array('order_sn' => $record_model->order_sn));

        return $orderinfo;
    }

    /**
     * 买单订单支付处理
     *
     * @param $record_model
     */
    protected function quickpayOrderProcessHandler($record_model)
    {
        /* 查询订单信息 */
        $orderinfo = RC_Api::api('quickpay', 'quickpay_order_info', array('order_sn' => $record_model->order_sn));

        return $orderinfo;
    }

    /**
     * 普通订单支付处理
     *
     * @param $record_model
     * @return array
     */
    protected function buyOrderProcessHandler($record_model)
    {
        /* 查询订单信息 */
        $orderinfo = RC_Api::api('orders', 'order_info', array('order_sn' => $record_model->order_sn));

        return $orderinfo;
    }


	
    /**
     * 获取小票打印数据
     */
    private function _GetPrintData($trade_type = '', $order_info)
    {
    	$printdata = [];
    	if (!empty($trade_type) && !empty($order_info)) {
    		if ($trade_type == 'buy' ) {
    			$printdata = $this->get_buy_printdata($order_info);
    		} elseif ($trade_type == 'quickpay') {
    			$printdata = $this->get_quickpay_printdata($order_info);
    		} elseif ($trade_type == 'surplus') {
    			$printdata = $this->get_surplus_printdata($order_info);
    		}
    	}
    	return $printdata;
    }
    
    /**
     * 获取消费订单打印数据
     */
    private function get_buy_printdata($order_info = array())
    {
    	$buy_print_data = array();
    	if (!empty($order_info)) {
    		$payment_record_info 	= $this->_payment_record_info($order_info['order_sn'], 'buy');
    		$order_goods 			= $this->get_order_goods($order_info['order_id']);
    		$total_discount 		= $order_info['discount'] + $order_info['integral_money'] + $order_info['bonus'];
    		$money_paid 			= $order_info['money_paid'] + $order_info['surplus'];
    		
    		//下单收银员
    		$cashier_name = RC_DB::table('cashier_record as cr')
    							->leftJoin('staff_user as su', RC_DB::raw('cr.staff_id'), '=', RC_DB::raw('su.user_id'))
    							->where(RC_DB::raw('cr.order_id'), $order_info['order_id'])
    							->whereIn('action', array('check_order', 'billing'))
    							->pluck('name');
    		
    		$user_info = [];
    		//有没用户
    		if ($order_info['user_id'] > 0) {
    			$userinfo = $this->get_user_info($order_info['user_id']);
    			if (!empty($userinfo)) {
    				$user_info = array(
    						'user_name' 			=> empty($userinfo['user_name']) ? '' : trim($userinfo['user_name']),
    						'mobile'				=> empty($userinfo['mobile_phone']) ? '' : trim($userinfo['mobile_phone']),
    						'user_points'			=> $userinfo['pay_points'],
    						'user_money'			=> $userinfo['user_money'],
    						'formatted_user_money'	=> $userinfo['user_money'] > 0 ? price_format($userinfo['user_money'], false) : '',
    				);
    			}
    		}
    		
    		$buy_print_data = array(
    				'order_sn' 						=> $order_info['order_sn'],
    				'trade_no'						=> empty($payment_record_info['trade_no']) ? '' : $payment_record_info['trade_no'],
    				'trade_type'					=> 'buy',
    				'trade_type'					=> empty($order_info['pay_time']) ? '' : RC_Time::local_date(ecjia::config('time_format'), $order_info['pay_time']),
    				'goods_list'					=> $order_goods['list'],
    				'total_goods_number' 			=> $order_goods['total_goods_number'],
    				'total_goods_amount'			=> $order_goods['taotal_goods_amount'],
    				'formatted_total_goods_amount'	=> $order_goods['taotal_goods_amount'] > 0 ? price_format($order_goods['taotal_goods_amount'], false) : '',
    				'total_discount'				=> $total_discount,
    				'formatted_total_discount'		=> $total_discount > 0 ? price_format($total_discount, false) : '',
    				'money_paid'					=> $money_paid,
    				'formatted_money_paid'			=> $money_paid > 0 ? price_format($money_paid, false) : '',
    				'integral'						=> intval($order_info['integral']),
    				'integral_money'				=> $order_info['integral_money'],
    				'formatted_integral_money'		=> $order_info['integral_money'] > 0 ? price_format($order_info['integral_money'], false) : '',
    				'pay_name'						=> !empty($order_info['pay_name']) ? $order_info['pay_name'] : '',
    				'payment_account'				=> '',
    				'user_info'						=> $user_info,
    				'refund_sn'						=> '',
    				'refund_total_amount'			=> 0,
    				'formatted_refund_total_amount' => '',
    				'cashier_name'					=> empty($cashier_name) ? '' : $cashier_name
    		);
    	}
    	
    	return $buy_print_data;
    }
    
    /**
     * 获取快捷收款买单订单打印数据
     */
    private function get_quickpay_printdata($order_info = array()) 
    {
    	$quickpay_print_data = [];
    	if ($order_info) {
    		$payment_record_info 	= $this->_payment_record_info($order_info['order_sn'], 'quickpay');
    		$total_discount 		= $order_info['discount'] + $order_info['integral_money'] + $order_info['bonus'];
    		$money_paid 			= $order_info['order_amount'] + $order_info['surplus'];
    		
    		//下单收银员
    		$cashier_name = RC_DB::table('cashier_record as cr')
    						->leftJoin('staff_user as su', RC_DB::raw('cr.staff_id'), '=', RC_DB::raw('su.user_id'))
    						->where(RC_DB::raw('cr.order_id'), $order_info['order_id'])
    						->where('action', 'receipt')
    						->pluck('name');
    		
    		$user_info = [];
    		//有没用户
    		if ($order_info['user_id'] > 0) {
    			$userinfo = $this->get_user_info($order_info['user_id']);
    			if (!empty($userinfo)) {
    				$user_info = array(
    						'user_name' 			=> empty($userinfo['user_name']) ? '' : trim($userinfo['user_name']),
    						'mobile'				=> empty($userinfo['mobile_phone']) ? '' : trim($userinfo['mobile_phone']),
    						'user_points'			=> $userinfo['pay_points'],
    						'user_money'			=> $userinfo['user_money'],
    						'formatted_user_money'	=> $userinfo['user_money'] > 0 ? price_format($userinfo['user_money'], false) : '',
    				);
    			}
    		}
    		
    		$quickpay_print_data = array(
    			'order_sn' 						=> $order_info['order_sn'],
    			'trade_no'						=> empty($payment_record_info['trade_no']) ? '' : $payment_record_info['trade_no'],
    			'trade_type'					=> 'quickpay',
    			'goods_list'					=> [],
    			'total_goods_number' 			=> 0,
    			'total_goods_amount'			=> $order_info['goods_amount'],
    			'formatted_total_goods_amount'	=> $order_info['goods_amount'] > 0 ? price_format($order_info['goods_amount'], false) : '',
    			'total_discount'				=> $total_discount,
    			'formatted_total_discount'		=> $total_discount > 0 ? price_format($total_discount, false) : '',
    			'money_paid'					=> $money_paid,
    			'formatted_money_paid'			=> $money_paid > 0 ? price_format($money_paid, false) : '',
    			'integral'						=> intval($order_info['integral']),
    			'integral_money'				=> $order_info['integral_money'],
    			'formatted_integral_money'		=> $order_info['integral_money'] > 0 ? price_format($order_info['integral_money'], false) : '',
    			'pay_name'						=> !empty($order_info['pay_name']) ? $order_info['pay_name'] : '',
    			'payment_account'				=> '',
    			'user_info'						=> $user_info,
    			'refund_sn'						=> '',
    			'refund_total_amount'			=> 0,
    			'formatted_refund_total_amount' => '',
    			'cashier_name'					=> empty($cashier_name) ? '' : $cashier_name
    		);
    	}
    	
    	return $quickpay_print_data;
    }
    
    /**
     * 获取充值订单打印数据
     */
    private function get_surplus_printdata($order_info = array())
    {
    	$surplus_print_data = [];
    	if (!empty($order_info)) {
    		$payment_record_info 	= $this->_payment_record_info($order_info['order_sn'], 'surplus');
    		$pay_name				= RC_DB::table('payment')->where('pay_code', $order_info['payment'])->pluck('pay_name');
    		
    		$user_info = [];
    		//有没用户
    		if ($order_info['user_id'] > 0) {
    			$userinfo = $this->get_user_info($order_info['user_id']);
    			if (!empty($userinfo)) {
    				$user_info = array(
    						'user_name' 			=> empty($userinfo['user_name']) ? '' : trim($userinfo['user_name']),
    						'mobile'				=> empty($userinfo['mobile_phone']) ? '' : trim($userinfo['mobile_phone']),
    						'user_points'			=> $userinfo['pay_points'],
    						'user_money'			=> $userinfo['user_money'],
    						'formatted_user_money'	=> $userinfo['user_money'] > 0 ? price_format($userinfo['user_money'], false) : '',
    				);
    			}
    		}
    		
    		//充值操作收银员
    		$cashier_name = empty($order_info['admin_user']) ? '' : $order_info['admin_user'];
    		
    		$surplus_print_data = array(
    				'order_sn' 						=> trim($order_info['order_sn']),
    				'trade_no'						=> empty($payment_record_info['trade_no']) ? '' : $payment_record_info['trade_no'],
    				'trade_type'					=> 'surplus',
    				'goods_list'					=> [],
    				'total_goods_number' 			=> 0,
    				'total_goods_amount'			=> $order_info['amount'],
    				'formatted_total_goods_amount'	=> $order_info['amount'] > 0 ? price_format($order_info['amount'], false) : '',
    				'total_discount'				=> 0,
    				'formatted_total_discount'		=> '',
    				'money_paid'					=> $order_info['amount'],
    				'formatted_money_paid'			=> $order_info['amount'] > 0 ? price_format($order_info['amount'], false) : '',
    				'integral'						=> 0,
    				'integral_money'				=> '',
    				'formatted_integral_money'		=> '',
    				'pay_name'						=> empty($pay_name) ? '' : $pay_name,
    				'payment_account'				=> '',
    				'user_info'						=> $user_info,
    				'refund_sn'						=> '',
    				'refund_total_amount'			=> 0,
    				'formatted_refund_total_amount' => '',
    				'cashier_name'					=> $cashier_name
    		);
    	}
    	
    	return $surplus_print_data;
    }
    
    /**
     * 支付交易记录信息
     * @param string $order_sn
     * @param string $trade_type
     * @return array
     */
    private function _payment_record_info($order_sn = '', $trade_type = '')
    {
    	$payment_revord_info = [];
    	if (!empty($order_sn) && !empty($trade_type)) {
    		$payment_revord_info = RC_DB::table('payment_record')->where('order_sn', $order_sn)->where('trade_type', $trade_type)->first();
    	}
    	return $payment_revord_info;
    }
    
    /**
     * 订单商品
     */
    private function get_order_goods ($order_id) {
    	$field = 'goods_id, goods_name, goods_number, (goods_number*goods_price) as subtotal';
    	$order_goods = RC_DB::table('order_goods')->where('order_id', $order_id)->select(RC_DB::raw($field))->get();
    	$total_goods_number = 0;
    	$taotal_goods_amount = 0;
    	$list = [];
    	if ($order_goods) {
    		foreach ($order_goods as $row) {
    			$total_goods_number += $row['goods_number'];
    			$taotal_goods_amount += $row['subtotal'];
    			$list[] = array(
    					'goods_id' 			=> $row['goods_id'],
    					'goods_name'		=> $row['goods_name'],
    					'goods_number'		=> $row['goods_number'],
    					'subtotal'			=> $row['subtotal'],
    					'formatted_subtotal'=> price_format($row['subtotal'], false),
    			);
    		}
    	}
    	 
    	return array('list' => $list, 'total_goods_number' => $total_goods_number, 'taotal_goods_amount' => $taotal_goods_amount);
    }
    
    /**
     * 用户信息
     */
    private function get_user_info ($user_id = 0) {
    	$user_info = RC_DB::table('users')->where('user_id', $user_id)->first();
    	return $user_info;
    }
}