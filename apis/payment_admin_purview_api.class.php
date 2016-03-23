<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * 后台权限API
 * @author royalwang
 *
 */
class payment_admin_purview_api extends Component_Event_Api {
    
    public function call(&$options) {
        $purviews = array(
            array('action_name' => __('支付方式管理'), 'action_code' => 'payment_manage', 'relevance'   => ''),
        	array('action_name' => __('支付方式更新'), 'action_code' => 'payment_update', 'relevance'   => ''),
        );
        
        return $purviews;
    }
}

// end