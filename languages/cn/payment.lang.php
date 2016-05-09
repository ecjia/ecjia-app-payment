<?php
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * ECJia 管理中心支付方式管理语言文件
 */

return array(
	'02_payment_list' 	=> '支付方式',
	'payment' 			=> '支付方式',
	'payment_name' 		=> '名称',
	'version' 			=> '版本',
	'payment_desc' 		=> '描述',
	'short_pay_fee' 	=> '费用',
	'payment_author' 	=> '插件作者',
	'payment_is_cod' 	=> '货到付款？',
	'payment_is_online' => '在线支付？',
	
	'enable' 	=> '启用',
	'disable' 	=> '关闭',
	
	'name_edit' 	=> '支付方式名称',
	'payfee_edit' 	=> '支付方式费用',
	'payorder_edit' => '支付方式排序',
	'name_is_null' 	=> '您没有输入支付方式名称！',
	'name_exists' 	=> '该支付方式名称已存在！',
	'pay_fee' 		=> '支付手续费',
	'back_list' 	=> '返回支付方式列表',
	'install_ok' 	=> '安装成功',
	'edit_ok' 		=> '编辑成功',
	'edit_falid' 	=> '编辑失败',
	'uninstall_ok' 	=> '卸载成功',
	
	'invalid_pay_fee' 	=> '支付费用不是一个合法的价格',
	'decide_by_ship' 	=> '配送决定',
	
	'edit_after_install' 	=> '该支付方式尚未安装，请你安装后再编辑',
	'payment_not_available' => '该支付插件不存在或尚未安装',
	
	'js_languages' 	=> array('lang_removeconfirm' => '您确定要卸载该支付方式吗？'),
	/* 支付确认部分 */
	'pay_status' 	=> '支付状态',
	'pay_not_exist' => '此支付方式不存在或者参数错误！',
	'pay_disabled' 	=> '此支付方式还没有被启用！',
	'pay_success' 	=> '您此次的支付操作已成功！',
	'pay_fail' 		=> '支付操作失败，请返回重试！',
	
	// 'ctenpay' 		=> '立即注册财付通商户号';
	// 'ctenpay_url' 	=> 'http://union.tenpay.com/mch/mch_register_b2c.shtml?sp_suggestuser=542554970';
	// 'ctenpayc2c_url' => 'https://www.tenpay.com/mchhelper/mch_register_c2c.shtml?sp_suggestuser=542554970';
	// 'tenpay'  		=> '即时到账';
	// 'tenpayc2c'		=> '中介担保';
			
	//追加
	'repeat'		=> '已存在',
	'buyer'			=> '买家',
	'surplus_type_0'=> '充值',
	'order_gift_integral'		=> '订单 %s 赠送的积分',
	'please_view_order_detail' 	=> '商品已发货，详情请到用户中心订单详情查看',
	
);

// end