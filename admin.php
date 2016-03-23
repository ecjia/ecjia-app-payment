<?php
/**
 * ECJIA 支付方式管理
 */
defined('IN_ECJIA') or exit('No permission resources.');
RC_Loader::load_sys_class('ecjia_admin', false);

class admin extends ecjia_admin 
{
	
	//定义数据库对象
	private $db;	
	public function __construct() 
	{
		parent::__construct();
		RC_Lang::load('payment');
		//RC_Loader::load_app_func('global');
		
		$this->db = RC_Loader::load_app_model('payment_model');	
		
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
	public function init () 
	{
		$this->admin_priv('payment_manage',ecjia::MSGTYPE_JSON);
		
		$plugins = ecjia_config::instance()->get_addon_config('payment_plugins', true, true);
		$data = $this->db->order('pay_order')->select();
		$data or $data = array();
		$modules = array();
		foreach($data as $_key => $_value) {
		    if (isset($plugins[$_value['pay_code']])) {
		    		$modules[$_key]['id'] 		= $_value['pay_id'];
		        $modules[$_key]['code'] 		= $_value['pay_code'];
		        $modules[$_key]['name'] 		= $_value['pay_name'];
		        $modules[$_key]['pay_fee'] 	= $_value['pay_fee'];
		        $modules[$_key]['is_cod'] 	= $_value['is_cod'];
		        $modules[$_key]['desc'] 		= $_value['pay_desc'];
		        $modules[$_key]['pay_order'] = $_value['pay_order'];
		        $modules[$_key]['enabled'] 	= $_value['enabled'];
		    }
		}
		
		ecjia_screen::get_current_screen()->remove_last_nav_here();
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('支付方式')));
		ecjia_screen::get_current_screen()->add_help_tab( array(
		'id'		=> 'overview',
		'title'		=> __('概述'),
		'content'	=>
		'<p>' . __('欢迎访问ECJia智能后台支付方式页面，系统中所有的支付方式都会显示在此列表中。') . '</p>'
		) );
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
		'<p><strong>' . __('更多信息:') . '</strong></p>' .
		'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:支付方式" target="_blank">关于支付方式帮助文档</a>') . '</p>'
		);
		
		$this->assign('ur_here', __('支付方式'));
		$this->assign('modules', $modules);
		
		$this->assign_lang();
		$this->display('payment_list.dwt');
	}

	/**
	 * 禁用支付方式
	 */
	public function disable() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
				
		$code = trim($_GET['code']);
		$data = array(
			'enabled' => 0
		);
		$this->db->where(array('pay_code' => $code))->update($data);
		
		$pay_name = $this->db->where(array('pay_code' => $code))->get_field('pay_name');
		
		ecjia_admin::admin_log($pay_name, 'stop', 'payment');
		
		$refresh_url = RC_Uri::url('payment/admin/init');
		$this->showmessage( "插件<strong>已停用</strong>", ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS , array( 'refresh_url' => $refresh_url));
	}
	
	/**
	 * 启用支付方式
	 */
	public function enable() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		$code = trim($_GET['code']);
		$data = array(
			'enabled' => 1
		);
		
		$this->db->where(array('pay_code' => $code))->update($data);
		
		$pay_name = $this->db->where(array('pay_code' => $code))->get_field('pay_name');
		
		ecjia_admin::admin_log($pay_name, 'use', 'payment');
		
		$refresh_url = RC_Uri::url('payment/admin/init');
		$this->showmessage( "插件<strong>已启用</strong>", ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS , array( 'refresh_url' => $refresh_url));
	}
	
	
	// 	public function get_config() {
	// 		$this->admin_priv('payment');
			
	// 		$code = $_REQUEST['code'];
		
	// 		/* 取相应插件信息 */
	// 		$modules[0] = RC_Loader::load_app_config($code,"payment");
	// 		//加载语言包
	// // 		$_LANG = array_merge($_LANG,RC_Loader::load_app_lang($code,"payment"));
	// 		RC_Lang::load("payment/$code");
	// 		$data = $modules[0]['config'];
	// 		$config = '<table>';
	// 		$range = '';
	// 		foreach($data AS $key => $value)
	// 		{
	// 			$config .= "<tr><td width=80><span class='label'>";
	// 			$config .= RC_Lang::lang($data[$key]['name']);
	// 			$config .= "</span></td>";
	// 			if($data[$key]['type'] == 'text')
	// 			{
	// 				if($data[$key]['name'] == 'alipay_account')
	// 				{
	// 					$config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' /><a href=\"https://www.alipay.com/himalayas/practicality.htm\" target=\"_blank\">".RC_Lang::lang('alipay_look')."</a></td>";
	// 				}
	// 				elseif($data[$key]['name'] == 'tenpay_account')
	// 				{
	// 					$config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' />" . RC_Lang::lang('penpay_register') . "</td>";
	// 				}
	// 				else
	// 				{
	// 					$config .= "<td><input name='cfg_value[]' type='text' value='" . $data[$key]['value'] . "' /></td>";
	// 				}
	// 			}
	// 			elseif($data[$key]['type'] == 'select')
	// 			{
	// 				$range = RC_Lang::lang($data[$key]['name'] . '_range');
	// 				$config .= "<td><select name='cfg_value[]'>";
	// 				foreach($range AS $index => $val)
	// 				{
	// 					$config .= "<option value='$index'>" . $range[$index] . "</option>";
	// 				}
	// 				$config .= "</select></td>";
	// 			}
	// 			$config .= "</tr>";
	// 			//$config .= '<br />';
	// 			$config .= "<input name='cfg_name[]' type='hidden' value='" .$data[$key]['name'] . "' />";
	// 			$config .= "<input name='cfg_type[]' type='hidden' value='" .$data[$key]['type'] . "' />";
	// 			$config .= "<input name='cfg_lang[]' type='hidden' value='" .$data[$key]['lang'] . "' />";
	// 		}
	// 		$config .= '</table>';
	// 		make_json_result($config);
			
	// 		// 		global $ecs, $db, $sess;
	// 		// 		$exc = new exchange($ecs->table('payment'), $db, 'pay_code', 'pay_name');
	// 		//		check_authz_json('payment');
			
	// 	}
	
	/**
	 * 编辑支付方式 code={$code}
	 */
	public function edit() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		if (isset($_GET['code'])) {
		    $pay_code = trim($_GET['code']); 
		} else {
		    $this->showmessage(__('invalid parameter'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		/* 查询该支付方式内容 */
		$pay = $this->db->where(array('pay_code' => $pay_code, 'enabled' => 1))->find();
		if (empty($pay)) {
		    $this->showmessage(RC_Lang::lang('payment_not_available'), ecjia::MSGTYPE_HTML | ecjia::MSGSTAT_ERROR);
		}
		
		/* 取得配置信息 */
		if (is_string($pay['pay_config'])) {
		    $pay_config = unserialize($pay['pay_config']);
		    /* 取出已经设置属性的code */
		    $code_list = array();
		    if (!empty($pay_config)) {
		        foreach ($pay_config as $key => $value) {
		            $code_list[$value['name']] = $value['value'];
		        }
		    }
		    $payment_handle = new payment_factory($pay_code);
		    $pay['pay_config'] = $payment_handle->configure_forms($code_list, true);

		}
		
		/* 如果以前没设置支付费用，编辑时补上 */
		if (!isset($pay['pay_fee'])) {
		    $pay['pay_fee'] = 0;
		}	
 		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑支付方式')));
	
		$this->assign('action_link',  array('text' =>'支付方式列表', 'href' => RC_Uri::url('payment/admin/init')));
		$this->assign('ur_here',      RC_Lang::lang('edit') . RC_Lang::lang('payment'));
		$this->assign('form_action',  RC_Uri::url('payment/admin/save'));
		$this->assign('pay',          $pay);
		
		$this->assign_lang();
		$this->display('payment_edit.dwt');
	}
	
	/**
	 * 提交支付方式 post
	 */
	public function save() 
	{	
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		$name = trim($_POST['pay_name']);
		$code = trim($_POST['pay_code']);
		/* 检查输入 */
		if (empty($name)) {
			$this->showmessage(RC_Lang::lang('payment_name') . RC_Lang::lang('empty'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$data = $this->db->where(array('pay_name' => $name, 'pay_code' => array('neq' => $code)))->count();
		if ($data > 0) {
			$this->showmessage(RC_Lang::lang('payment_name') . RC_Lang::lang('repeat'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		/* 取得配置信息 */
		$pay_config = array();
		if (isset($_POST['cfg_value']) && is_array($_POST['cfg_value'])) {
			
			for ($i = 0; $i < count($_POST['cfg_value']); $i++) {
				$pay_config[] = array(
						'name'  => trim($_POST['cfg_name'][$i]),
						'type'  => trim($_POST['cfg_type'][$i]),
						'value' => trim($_POST['cfg_value'][$i])
					);
			}
		}
		
		$pay_config = serialize($pay_config);
		/* 取得和验证支付手续费 */
		$pay_fee    = empty($_POST['pay_fee'])? 0: intval($_POST['pay_fee']);

		if ($_POST['pay_id']) {
			/* 编辑 */
			$array = array(
					'pay_name'   => $name,
					'pay_desc'   => trim($_POST['pay_desc']),
					'pay_config' => $pay_config,
					'pay_fee'    => $pay_fee
			);
			$this->db->where(array('pay_code' => $code))->update($array);
			 
			/* 记录日志 */
			ecjia_admin::admin_log($name, 'edit', 'payment');
			// 			$this->showmessage(RC_Lang::lang('edit_ok'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array("links" => $link,"auto"=> 1,"autoclose"=> 5000,"postion"=> 'top-center'));
			$this->showmessage(RC_Lang::lang('edit_ok'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		} else {
			$data_one = $this->db->where(array('pay_code' => $code))->count();
			
			if ($data_one > 0) {
				/* 该支付方式已经安装过, 将该支付方式的状态设置为 enable */
				$data = array(
						'pay_name'   => $name,
						'pay_desc'   => trim($_POST['pay_desc']),
						'pay_config' => $pay_config,
						'pay_fee'    => $pay_fee,
						'enabled'    => '1'						
				);
			    $this->db->where(array('pay_code' => $code))->update($data);
			} else {
				/* 该支付方式没有安装过, 将该支付方式的信息添加到数据库 */				
				$data =array(
					    'pay_code' => $code,
						'pay_name' => $name,
						'pay_desc' => trim($_POST['pay_desc']),
						'pay_config' => $pay_config,
						'is_cod'   => intval($_POST['is_cod']),
						'pay_fee'  => $pay_fee,
						'enabled'  => '1',
						'is_online' => intval($_POST['is_online'])
				);
				
	            $this->db->insert($data);
			}
			
			/* 记录日志 */
			ecjia_admin::admin_log($name, 'edit', 'payment');
			$refresh_url = RC_Uri::url('payment/admin/edit', array('code' => $code));
			$this->showmessage(RC_Lang::lang('install_ok'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('refresh_url' => $refresh_url));
		}			
	}
	
	/**
	 * 修改支付方式名称
	 */
	public function edit_name() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		/* 取得参数 */
		$pay_id  = intval($_POST['pk']);
		$pay_name = trim($_POST['value']);
		
		/* 检查名称是否为空 */
		if (empty($pay_name) || $pay_id==0 ) {
			$this->showmessage(RC_Lang::lang('name_is_null') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR );
		} else {
			/* 检查名称是否重复 */
			if( $this->db->where(array('pay_name' => $pay_name, 'pay_id' => array('neq' => $pay_id)))->count() > 0) {
				$this->showmessage(RC_Lang::lang('name_exists') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR );
			} else {
				$this->db->where(array('pay_id' => $pay_id ))->update(array('pay_name' => stripcslashes($pay_name)));
				
				ecjia_admin::admin_log(stripcslashes($pay_name), 'edit', 'payment');
				$this->showmessage(RC_Lang::lang('name_edit').RC_Lang::lang('edit_ok') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
			}
		}
	}
	

	/**
	 * 修改支付方式排序
	 */
	public function edit_order() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		if ( !is_numeric($_POST['value']) ) {
			$this->showmessage('请输入合法数字', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		} else {
			/* 取得参数 */
			$pay_id    = intval($_POST['pk']);
			$pay_order = intval($_POST['value']);
		
			$this->db->where(array('pay_id' => $pay_id))->update(array('pay_order' => $pay_order));
			
			$this->showmessage(RC_Lang::lang('payorder_edit'). RC_Lang::lang('edit_ok') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS,array('pjaxurl' => RC_uri::url('payment/admin/init')) );
		}
	}
	
	/**
	 * 修改支付方式费用
	 */
	public function edit_pay_fee() 
	{
		$this->admin_priv('payment_update',ecjia::MSGTYPE_JSON);
		
		/* 取得参数 */
		$pay_id  = intval($_POST['pk']);
		$pay_fee = trim($_POST['value']);
		
		if (empty($pay_fee) && !($pay_fee === '0')) {
			$this->showmessage(RC_Lang::lang('invalid_pay_fee') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		} else {
			$pay_insure = make_semiangle($pay_fee); //全角转半角
			if (strpos($pay_insure, '%') === false) { //不包含百分号
				if ( !is_numeric($pay_fee) ) {
					$this->showmessage('请输入合法数字或百分比%', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
				} else {
					$pay_fee = floatval($pay_insure);
				}
			}
			else {
				$pay_fee = floatval($pay_insure) . '%';
			}
			$pay_name = $this->db->where(array('pay_id' => $pay_id))->get_field('pay_name');
			
			$this->db->where(array('pay_id' => $pay_id))->update(array('pay_fee' => stripcslashes($pay_fee)));
			
			ecjia_admin::admin_log($pay_name.'，'.'修改费用为 '.$pay_fee, 'setup', 'payment');
			$this->showmessage(RC_Lang::lang('payfee_edit').RC_Lang::lang('edit_ok') , ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
		}
	}
	
}

// end