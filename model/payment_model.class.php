<?php
defined('IN_ECJIA') or exit('No permission resources.');

class payment_model extends Component_Model_Model {
	public $table_name = '';
	public function __construct() {
		$this->db_config = RC_Config::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'payment';
		parent::__construct();
	}
	
	public function payment_select($order) {
		return $this->order($order)->select();
	}
	
	public function payment_manage($data, $where=array()) {
		if (empty($where)) {
			return $this->insert($data);
		}
		return $this->where($where)->update($data);
	}
	
	public function payment_field($where, $field) {
		return $this->where($where)->get_field($field);
	}
	
	public function payment_find($where) {
		return $this->where($where)->find();
	}
	
	public function is_only($where) {
		return $this->where($where)->count();
	}
}

// end