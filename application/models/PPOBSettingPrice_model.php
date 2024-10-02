<?php
	class PPOBSettingPrice_model extends CI_Model {
		var $table = "ppob_setting_price";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getPPOBSettingPrice(){
			$this->db->select('ppob_setting_price.setting_price_id, ppob_setting_price.setting_price_code, ppob_setting_price.setting_price_fee, ppob_setting_price.setting_price_commission, ppob_setting_price.setting_price_max, ppob_setting_price.setting_price_status');
			$this->db->from('ppob_setting_price');
			$this->db->where('ppob_setting_price.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getPPOBSettingPrice_Detail($setting_price_id){
			$this->db->select('ppob_setting_price.setting_price_id, ppob_setting_price.setting_price_code, ppob_setting_price.setting_price_fee, ppob_setting_price.setting_price_commission, ppob_setting_price.setting_price_max, ppob_setting_price.setting_price_status');
			$this->db->from('ppob_setting_price');
			$this->db->where('ppob_setting_price.data_state', 0);
			$this->db->where('ppob_setting_price.setting_price_id', $setting_price_id);
			return $this->db->get()->row_array();
		}
		
		public function updatePPOBSettingPrice($data){
			$this->db->where("setting_price_id", $data['setting_price_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function insertPPOBSettingPriceLog($data){
			$query = $this->db->insert('ppob_setting_price_log', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>