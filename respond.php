<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * ECSHOP 支付响应页面
 */
RC_Loader::load_sys_class('ecjia_front', false);

class respond extends ecjia_front {

	public function __construct() {
		parent::__construct();
		
		RC_Loader::load_app_func('order','orders');
		
		RC_Lang::load('payment/payment');
	}
	
	public function init() {
	    
	}
	
	
	public function response() {
	    RC_Log::write('GET: ' . json_encode($_GET), RC_Log::DEBUG);

		/* 支付方式代码 */
		$pay_code = !empty($_GET['code']) ? trim($_GET['code']) : '';
		unset($_GET['code']);
		
		/* 参数是否为空 */
		if (empty($pay_code)) {
			$msg = RC_Lang::lang('pay_not_exist');
		} else {
		    $payment_method = RC_Loader::load_app_class('payment_method', 'payment');
		    
		    $payment_list = $payment_method->available_payment_list();

			/* 判断是否启用 */
			if (count($payment_list) == 0) {
				$msg = RC_Lang::lang('pay_disabled');
			} else {
			    $payment = $payment_method->get_payment_instance($pay_code);
				/* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
				if (!$payment) {
				    $msg = RC_Lang::lang('pay_not_exist');
				} 
				/* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
				elseif ($payment->response()) {
				    $msg = RC_Lang::lang('pay_success');
				} else {
				    $msg = RC_Lang::lang('pay_fail');
				}
			}
		}

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
            		if (/android/i.test(navigator.userAgent)){
        			    // todo : android
        			    window.ecmoban.back();
        			}
        			
        			if (/ipad|iphone|mac/i.test(navigator.userAgent)){
        			    // todo : ios
        			    window.location.href="objc://payback";
        			}
            	}
            	window.onload = goback;
            </script>
        </head>
        <body >
            <div style="width:100%;overflow: hidden;margin:0px;padding:0px 0px;text-align: center;">
                <h2 style="background:#ccc;line-height:40px;height:40px;">提示信息</h2>
                <p style="font-size:20px; line-height:25px;min-height:100px;padding-top:20px;">{$msg}</p>
                <a style="font-size:18px;" href="#" onclick="goback()">返回</a>
            </div>
        </body>
        </html>
RESPOND;
        
        echo $respond;
	}
	
	
	
	public function notify() {
	    RC_Log::write('POST: ' . json_encode($_POST), RC_Log::DEBUG);
	    
	    /* 支付方式代码 */
	    $pay_code = !empty($_GET['code']) ? trim($_GET['code']) : '';
	    
	    /* 参数是否为空 */
	    if (empty($pay_code)) {
	        RC_Log::write('paycode_not_exist', RC_Log::INFO);
	        die();
	    } else {
	        $payment_method = RC_Loader::load_app_class('payment_method', 'payment');
	    
	        $payment_list = $payment_method->available_payment_list();
	    
	        /* 判断是否启用 */
	        if (count($payment_list) == 0) {
	            RC_Log::write('payment_disabled', RC_Log::INFO);
	            die();
	        } else {
	            $payment = $payment_method->get_payment_instance($pay_code);
	            /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
	            if (!$payment) {
	                RC_Log::write('payment_not_exist', RC_Log::INFO);
	                die();
	            }
	            /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
	            $result = $payment->notify();
	            if (is_ecjia_error($result)) {
	                RC_Log::write('pay_fail: ' . $result->get_error_message());
	                echo "fail";
	                die();
	            } else {
	                RC_Log::write('pay_success', RC_Log::INFO);
	                echo "success";
	                die();
	            }
	        }
	    }

	}
	
}

// end