<?php
/**
 * ECJIA 支付方式管理
 */
defined('IN_ECJIA') or exit('No permission resources.');

class admin_payment_record extends ecjia_admin {
	
	private $db;	
	public function __construct() {
		parent::__construct();
		
		$this->db = RC_Model::model('payment/payment_model');
		
		/* 加载全局 js/css */
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');		
		
		/* 支付方式 列表页面 js/css */

		RC_Script::enqueue_script('payment_admin', RC_App::apps_url('statics/js/payment_admin.js',__FILE__),array(), false, true);
		RC_Script::enqueue_script('bootstrap-editable.min', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js'));
		RC_Style::enqueue_style('bootstrap-editable', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css'));

		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('chosen');
		RC_Script::enqueue_script('jquery-chosen');

		RC_Loader::load_app_class('payment_factory', null, false);
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('支付方式'), RC_Uri::url('payment/admin/init')));
	}

	/**
	 * 支付方式列表
	 */
	public function init() {
	    $this->admin_priv('payment_manage', ecjia::MSGTYPE_JSON);
		RC_Loader::load_app_func('global');

	    $db_payment_record = get_payment_record_list($_REQUEST);

	    $this->assign('modules', $db_payment_record);
		$this->assign('search_action',	RC_Uri::url('payment/admin_payment_record/init'));

		$this->display('payment_record_list.dwt');
	}

	/**
	 * 禁用支付方式
	 */
	public function info() {
		
		
		$this->display('payment_record_info.dwt');
	}
	
	
}

// end