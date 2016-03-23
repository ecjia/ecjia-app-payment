<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * 后台工具菜单API
 * @author royalwang
 */
class payment_tool_menu_api extends Component_Event_Api 
{
	
	public function call(&$options) 
	{	
		$menus = ecjia_admin::make_admin_menu('01_payment_list', __('支付方式'), RC_Uri::url('payment/admin/init'), 1)->add_purview('payment_manage');
		return $menus;
	}
}

// end