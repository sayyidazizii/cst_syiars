<?php
	class SettingPrice_model extends CI_Model {
		var $table = "ppob_setting_price";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

		}
		
		public function getPPOBSettingPrice_Code($setting_price_code){
			$this->db->select('ppob_setting_price.setting_price_id, ppob_setting_price.setting_price_code, ppob_setting_price.setting_price_type, ppob_setting_price.setting_price_fee, ppob_setting_price.setting_price_commission, ppob_setting_price.setting_price_admin_fee');
			$this->db->from('ppob_setting_price');
			$this->db->where('ppob_setting_price.setting_price_code', $setting_price_code);
			$result = $this->db->get()->row_array();
			return $result;
		}

	}
?>