<?php
	class PreferencePPOB_model extends CI_Model {
		var $table = "preference_income";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getPPOBSettingPrice($setting_price_type){
			$this->db->select('setting_price_id, setting_price_type, setting_price_fee');
			$this->db->from('ppob_setting_price');
			$this->db->where('setting_price_type', $setting_price_type);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getPreferencePPOB(){
			$this->db->select('preference_ppob.id, preference_ppob.ppob_account_down_payment, preference_ppob.ppob_account_payable_member, preference_ppob.ppob_account_income, preference_ppob.ppob_mutation_id, preference_ppob.ppob_account_income_mbayar, preference_ppob.ppob_mbayar_admin, preference_ppob.ppob_adm_mutation_id, preference_ppob.ppob_account_cost');
			$this->db->from('preference_ppob');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							FROM acct_account
							WHERE acct_account.data_state='0'");
			return $hasil->result_array();
		}

		public function getAccountName($account_id){
			$this->db->select('CONCAT(account_code, " - " ,account_name) AS account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}
		
		public function updatePreferencePPOB($data){
			$this->db->where("id",$data['id']);
			$query = $this->db->update("preference_ppob", $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function updatePPOBSettingPrice($data){
			$this->db->where("setting_price_id",$data['setting_price_id']);
			$query = $this->db->update("ppob_setting_price", $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>