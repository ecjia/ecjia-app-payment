<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 *  支付响应页面
 */
class respond extends ecjia_front {

	public function __construct() {
		parent::__construct();
		RC_Loader::load_app_func('admin_order', 'orders');
	}
	
	public function init() {
	    
	}
	
	public function response() {
	    RC_Logger::getLogger('pay')->debug('GET: ' . json_encode($_GET));
	    
		/* 支付方式代码 */
		$pay_code = !empty($_GET['code']) ? trim($_GET['code']) : '';
		unset($_GET['code']);
		
		/* 参数是否为空 */
		if (empty($pay_code)) {
			$msg = RC_Lang::get('payment::payment.pay_not_exist');
		} else {
		    $payment_method = RC_Loader::load_app_class('payment_method', 'payment');
		    
		    $payment_list = $payment_method->available_payment_list();

			/* 判断是否启用 */
			if (count($payment_list) == 0) {
				$msg = RC_Lang::get('payment::payment.pay_disabled');
			} else {
			    $payment = $payment_method->get_payment_instance($pay_code);
				/* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
				if (!$payment) {
				    $msg = RC_Lang::get('payment::payment.pay_not_exist');
				} 
				/* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
				elseif ($payment->response()) {
				    $msg = RC_Lang::get('payment::payment.pay_success');
				} else {
				    $msg = RC_Lang::get('payment::payment.pay_fail');
				}
			}

		}
		
		$touch_url = ecjia::config('shop_touch_url');
		
        $respond =<<<RESPOND
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
            <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
            <title>支付通知</title>
            
            <style type="text/css">
                body,html,div,p,h2,span{
                    margin:0px;
                    padding:0px;
                }
            </style>
            <script type="text/javascript">
            	function goback() {
            		var useragent = navigator.userAgent;
            		if (useragent.indexOf("ECJiaBrowse") >= 0) {
						var url="ecjiaopen://app?open_type=main";  
    					document.location = url;
					} else {
						window.history.go(-1);
					}  
            	}
            </script>
        </head>
        <body >
            <div style="width:100%;overflow: hidden;margin:0px;padding:0px 0px;text-align: center;">
				<h2 style="background:#18B0EF;line-height:2.5em;height:2.5em;color: #fff;">提示信息</h2>
				<p style="font-size:1.5em; line-height:25px;min-height:100px;padding-top:2em;">{$msg}</p>
				<div class="two-btn" style="margin:1em">
RESPOND;
        if (!empty($touch_url)) {
        	$respond .= '<a class="btn btn-info" href="'.$touch_url.'" style="display: inline-block;background:#4AB9EE;width: 48%;padding: 1em 0;border-radius: 6px;color: #fff;text-decoration: blink;margin-right: 2%;">去微商城购物</a>';
        }
		$respond.=<<<RESPOND
					
					<a class="btn btn-info" href="javascript:goback()" style="display: inline-block;background:#4AB9EE;width: 48%;padding: 1em 0;border-radius: 6px;color: #fff;text-decoration: blink;">返回APP</a>
				</div>
			</div>
        </body>
        </html>
RESPOND;
        
//         echo RC_Hook::apply_filters('payment_respond_template', $respond, $msg);

		$payment_info = $payment_method->payment_info_by_code($pay_code);
		$info = array(
		    'pay_name' => $payment_info['pay_name'],
		    'amount'   => '',
		    'order_id' => $pay_code == 'pay_alipay' ? $_GET['out_trade_no'] : '',
		);
		return $this->response_template($msg, $info);
	}
	
	
	
	public function notify() {
	    RC_Logger::getLogger('pay')->debug('POST: ' . json_encode($_POST));
	      
	    /* 支付方式代码 */
	    $pay_code = !empty($_GET['code']) ? trim($_GET['code']) : '';
	    unset($_GET['code']);
	    
	    /* 参数是否为空 */
	    if (empty($pay_code)) {
	        RC_Logger::getLogger('pay')->debug('paycode_not_exist');
	        die();
	    } else {
	        $payment_method = RC_Loader::load_app_class('payment_method', 'payment');
	    
	        $payment_list = $payment_method->available_payment_list();
	    
	        /* 判断是否启用 */
	        if (count($payment_list) == 0) {
	            RC_Logger::getLogger('pay')->debug('payment_disabled');
	            die();
	        } else {
	            $payment = $payment_method->get_payment_instance($pay_code);
	            /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
	            if (!$payment) {
	                RC_Logger::getLogger('pay')->debug('payment_not_exist');
	                die();
	            }
	            /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
	            $result = $payment->notify();
	            if (is_ecjia_error($result)) {
	                RC_Logger::getLogger('pay')->debug('pay_fail: ' . $result->get_error_message());
	                echo "fail";
	                die();
	            } else {
	                RC_Logger::getLogger('pay')->debug('pay_success');
	                echo "success";
	                die();
	            }
	        }
	    }

	}
	
	public function response_template($msg, $info) {
	    
	    $this->assign('msg', $msg);
	    $this->assign('info', $info);
	    $url['index'] = RC_Cookie::set('pay_response_index');
	    $url['order'] = RC_Cookie::get('pay_response_order');
	    $this->assign('url', $url);
	    
	    $this->display('response.dwt');
	}
	
}

// end