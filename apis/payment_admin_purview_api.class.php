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
            array('action_name' => RC_Lang::get('payment::payment.payment_manage'), 'action_code' => 'payment_manage', 'relevance' => ''),
        	array('action_name' => RC_Lang::get('payment::payment.payment_update'), 'action_code' => 'payment_update', 'relevance' => ''),
        );
        
        return $purviews;
    }
}

// end