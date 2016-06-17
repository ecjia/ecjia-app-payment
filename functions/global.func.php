<?php
/**
 * ECJia 支付接口函数库
 */

defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 取得返回信息地址
 * @param   string  $code   支付方式代码
 */
function return_url($code) {
    return $GLOBALS['ecs']->url() . 'respond.php?code=' . $code;
}

/**
 *  取得某支付方式信息
 *  @param  string  $code   支付方式代码
 */
function get_payment($code) {
	$db = RC_Loader::load_app_model('payment_model', 'payment');
	$payment = $db->find('pay_code = "'. $code. '" AND enabled = "1"');

    if ($payment) {
        $config_list = unserialize($payment['pay_config']);
        foreach ($config_list AS $config) {
            $payment[$config['name']] = $config['value'];
        }
    }
    return $payment;
}

/**
 *  通过订单sn取得订单ID
 *  @param  string  $order_sn   订单sn
 *  @param  blob    $voucher    是否为会员充值
 */
function get_order_id_by_sn($order_sn, $voucher = 'false') {
	$db_pay = RC_Loader::load_app_model('pay_log_model','orders');
	$db_order = RC_Loader::load_app_model('order_info_model', 'orders');
    if ($voucher == 'true') {
        if(is_numeric($order_sn)) {
			return $db_pay->field('log_id')->find('order_id = "'. $order_sn .'" AND order_type = 1');
        } else {
            return "";
        }
    } else {
        if(is_numeric($order_sn)) {
        		$order_id = $db_order->field('order_id')->find('order_sn = "'. $order_sn .'"');
        } 
        if (!empty($order_id)) {
        		$pay_log_id = $db_pay->field('log_id')->find('order_id = "'. $order_id .'"');
        		return $pay_log_id;       	
        } else {
            return "";
        }
    }
}

/**
 *  通过订单ID取得订单商品名称
 *  @param  string  $order_id   订单ID
 */
function get_goods_name_by_id($order_id) {
	$db = RC_Loader::load_app_model('order_goods_model', 'orders');
	$goods_name = $db->field('goods_name')->find('order_id = "'. $order_id .'"');
	return implode(',', $goods_name);
}

/**
 * 检查支付的金额是否与订单相符
 *
 * @access  public
 * @param   string   $log_id      支付编号
 * @param   float    $money       支付接口返回的金额
 * @return  true
 */
function check_money($log_id, $money) {
	$db_pay = RC_Loader::load_app_model('pay_log_model','orders');
    if(is_numeric($log_id)) {
    		$amount = $db_pay->field('order_amount')->find('log_id = "'. $log_id .'"');
    } else {
        return false;
    }
    if ($money == $amount) {
        return true;
    } else {
        return false;
    }
}

/**
 * 修改订单的支付状态
 *
 * @access  public
 * @param   string  $log_id     支付编号
 * @param   integer $pay_status 状态
 * @param   string  $note       备注
 * @return  void
 */
function order_paid($log_id, $pay_status = PS_PAYED, $note = '') {
	$db_pay = RC_Loader::load_app_model('pay_log_model','orders');
	$db_order = RC_Loader::load_app_model('order_info_model','orders');
	$db_user = RC_Loader::load_app_model('user_account_model','user');
    /* 取得支付编号 */
    $log_id = intval($log_id);
    if ($log_id > 0) {
        /* 取得要修改的支付记录信息 */
        $pay_log = $db_pay->find('log_id = '.$log_id.'');
        if ($pay_log && $pay_log['is_paid'] == 0) {
            /* 修改此次支付操作的状态为已付款 */
	        $data = array( 'is_paid' => '1' );
			$db_pay->where('log_id = '.$log_id.'')->update($data);

            /* 根据记录类型做相应处理 */
            if ($pay_log['order_type'] == PAY_ORDER) {
                /* 取得订单信息 */
                $order = $db_order->field('order_id, user_id, order_sn, consignee, address, tel, shipping_id, extension_code, extension_id, goods_amount')->find('order_id = '. $pay_log['order_id']. '');
                $order_id = $order['order_id'];
                $order_sn = $order['order_sn'];

                /* 修改订单状态为已付款 */
                $data = array(
                	'order_status' => OS_CONFIRMED,
                	'confirm_time' => RC_Time::gmtime(),
                	'pay_status'   => $pay_status,
                	'pay_time'     => RC_Time::gmtime(),
                	'money_paid'   => order_amount,
                	'order_amount' => 0,
                );
                
                $db_order->where('order_id = '.$order_id.'')->update($data);

                /* 记录订单操作记录 */
                order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, $pay_status, $note, RC_Lang::get('payment::payment.buyer'));

                /* 如果需要，发短信 */
//                 if ($GLOBALS['_CFG']['sms_order_payed'] == '1' && $GLOBALS['_CFG']['sms_shop_mobile'] != '')
//                 {
// 					//include_once(ROOT_PATH.'includes/cls_sms.php');                
//                     $sms = new sms();
//                     $sms->send($GLOBALS['_CFG']['sms_shop_mobile'],
//                     sprintf($GLOBALS['_LANG']['order_payed_sms'], $order_sn, $order['consignee'], $order['tel']),'', 13,1);
//                 }

                /* 对虚拟商品的支持 */
                $virtual_goods = get_virtual_goods($order_id);
                if (!empty($virtual_goods)) {
                    $msg = '';
                    if (!virtual_goods_ship($virtual_goods, $msg, $order_sn, true)) {
                        $GLOBALS['_LANG']['pay_success'] .= '<div style="color:red;">'.$msg.'</div>'.$GLOBALS['_LANG']['virtual_goods_ship_fail'];
                    }

                    /* 如果订单没有配送方式，自动完成发货操作 */
                    if ($order['shipping_id'] == -1) {
                        /* 将订单标识为已发货状态，并记录发货记录 */
	                    	$data = array(
	                    		'shipping_status' => SS_SHIPPED,
	                    		'shipping_time' => RC_Time::gmtime(),
	                    	);
                    		$db_order->where('order_id = '.$order_id.'')->update($data);

                         /* 记录订单操作记录 */
                        order_action($order_sn, OS_CONFIRMED, SS_SHIPPED, $pay_status, $note, RC_Lang::get('payment::payment.buyer'));
                        $integral = integral_to_give($order);
                        $options = array(
                        	'user_id'		=> $order['user_id'],
                        	'rank_points'	=> intval($integral['rank_points']),
                        	'pay_points'	=> intval($integral['custom_points']),
                        	'change_desc'	=> sprintf(RC_Lang::get('payment::payment.order_gift_integral'), $order['order_sn'])
                        );
                        RC_Api::api('user', 'account_change_log',$options);
//                         log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($GLOBALS['_LANG']['order_gift_integral'], $order['order_sn']));
                    }
                }

            } elseif ($pay_log['order_type'] == PAY_SURPLUS) {
            		$res_id = $db_user -> field('`id`')->find('`id` = '.$pay_log['order_id'].' AND `is_paid` = 1');
                if (empty($res_id)) {
					/* 更新会员预付款的到款状态 */
	                	$data = array(
                			'paid_time' => RC_Time::gmtime(),
                			'is_paid'   => 1
                		);
                	
                		$db_user->where('`id` = '.$pay_log['order_id'].'')->update($data);
                    /* 取得添加预付款的用户以及金额 */
                		$arr = $db_user->field('user_id, amount')->find('`id` = '. $pay_log['order_id'].'');
                    /* 修改会员帐户金额 */
//                     $_LANG = array();
//                     include_once(ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/user.php');
                    $options = array(
                    	'user_id'		=> $arr['user_id'],
                    	'user_money'	=> $arr['amount'],
                    	'change_desc'	=> RC_Lang::get('payment::payment.surplus_type_0'),
                    	'change_type'	=> ACT_SAVING
                    );
                    RC_Api::api('user', 'account_change_log',$options);
//                     log_account_change($arr['user_id'], $arr['amount'], 0, 0, 0, RC_Lang::get('payment::payment.surplus_type_0'), ACT_SAVING);
                }
            }
        } else {
            /* 取得已发货的虚拟商品信息 */
            $post_virtual_goods = get_virtual_goods($pay_log['order_id'], true);

            /* 有已发货的虚拟商品 */
            if (!empty($post_virtual_goods)) {
                $msg = '';
                /* 检查两次刷新时间有无超过12小时 */
                $row = $db_order->field('pay_time, order_sn')->find('`order_id` = '. $pay_log['order_id'].'');
                $intval_time = RC_Time::gmtime() - $row['pay_time'];
                if ($intval_time >= 0 && $intval_time < 3600 * 12) {
                    $virtual_card = array();
                    foreach ($post_virtual_goods as $code => $goods_list) {
                        /* 只处理虚拟卡 */
                        if ($code == 'virtual_card') {
                            foreach ($goods_list as $goods) {
                                if ($info = virtual_card_result($row['order_sn'], $goods)) {
                                    $virtual_card[] = array('goods_id'=>$goods['goods_id'], 'goods_name'=>$goods['goods_name'], 'info'=>$info);
                                }
                            }
                            ecjia::$view_object->assign('virtual_card', $virtual_card);
                        }
                    }
                } else {
                    $msg = '<div>' .  RC_Lang::get('payment::payment.please_view_order_detail') . '</div>';
                }
                $GLOBALS['_LANG']['pay_success'] .= $msg;
			}

			/* 取得未发货虚拟商品 */
			$virtual_goods = get_virtual_goods($pay_log['order_id'], false);
            if (!empty($virtual_goods)) {
				$GLOBALS['_LANG']['pay_success'] .= '<br />' . $GLOBALS['_LANG']['virtual_goods_ship_fail'];
            }
		}
    }
}

/**
 * 取得货到付款和非货到付款的支付方式
 * @return  array('is_cod' => '', 'is_not_cod' => '')
 */
function get_pay_ids() {
	$db = RC_Loader::load_app_model('payment_model', 'payment');
	$ids = array('is_cod' => '0', 'is_not_cod' => '0');
	$data = $db->field('pay_id, is_cod')->where('enabled = 1')->select();
	if(!empty($data)) {
		foreach ($data as $row) {
			if ($row['is_cod']) {
				$ids['is_cod'] .= ',' . $row['pay_id'];
			} else {
				$ids['is_not_cod'] .= ',' . $row['pay_id'];
			}
		}
	}
	return $ids;
}

// end