<?php
	class AcctSavingsBankMutation_model extends CI_Model {
		var $table = "acct_savings_bank_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavingsBankMutation($start_date, $end_date, $savings_account_id){
			$this->db->select('acct_savings_bank_mutation.savings_bank_mutation_id, acct_savings_bank_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_bank_mutation.savings_bank_mutation_date, acct_savings_bank_mutation.savings_bank_mutation_amount, acct_savings_bank_mutation.bank_account_id, acct_bank_account.bank_account_name, acct_bank_account.account_id, acct_account.account_code');
			$this->db->from('acct_savings_bank_mutation');
			$this->db->join('acct_bank_account', 'acct_savings_bank_mutation.bank_account_id = acct_bank_account.bank_account_id');
			$this->db->join('acct_account', 'acct_bank_account.account_id = acct_account.account_id');
			$this->db->join('acct_savings_account', 'acct_savings_bank_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_bank_mutation.savings_bank_mutation_date >=', $start_date);
			$this->db->where('acct_savings_bank_mutation.savings_bank_mutation_date <=', $end_date);
			if(!empty($savings_account_id)){
				$this->db->where('acct_savings_bank_mutation.savings_account_id', $savings_account_id);
			}
			$this->db->where('acct_savings_bank_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount(){
			$this->db->select('acct_savings_account.savings_account_id, CONCAT(acct_savings_account.savings_account_no," - ",core_member.member_name) AS savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctBankAccount(){
			$this->db->select('acct_bank_account.bank_account_id, CONCAT(acct_account.account_code," - ", acct_bank_account.bank_account_name) AS bank_account_code');
			$this->db->from('acct_bank_account');
			$this->db->join('acct_account', 'acct_bank_account.account_id = acct_account.account_id');
			$this->db->where('acct_bank_account.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_identity.identity_name, core_member.member_identity_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_identity', 'core_member.identity_id = core_identity.identity_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}
		
		public function insertAcctSavingsBankMutation($data){
			return $query = $this->db->insert('acct_savings_bank_mutation',$data);
		}
		
		public function getAcctSavingsBankMutation_Detail($savings_bank_mutation_id){
			$this->db->select('acct_savings_bank_mutation.savings_bank_mutation_id, acct_savings_bank_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_bank_mutation.bank_account_id, acct_bank_account.bank_account_name, acct_bank_account.account_id, acct_account.account_code, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_identity.identity_name, core_member.member_identity_no, acct_savings_bank_mutation.savings_bank_mutation_date, acct_savings_bank_mutation.savings_bank_mutation_amount, acct_savings_bank_mutation.savings_bank_mutation_amount, acct_savings_bank_mutation.savings_bank_mutation_opening_balance, acct_savings_bank_mutation.savings_bank_mutation_last_balance, acct_savings_bank_mutation.voided_remark');
			$this->db->from('acct_savings_bank_mutation');
			$this->db->join('acct_bank_account', 'acct_savings_bank_mutation.bank_account_id = acct_bank_account.bank_account_id');
			$this->db->join('acct_account', 'acct_bank_account.account_id = acct_account.account_id');
			$this->db->join('acct_savings_account', 'acct_savings_bank_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_identity', 'core_member.identity_id = core_identity.identity_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_bank_mutation.data_state', 0);
			$this->db->where('acct_savings_bank_mutation.savings_bank_mutation_id', $savings_bank_mutation_id);
			return $this->db->get()->row_array();
		}

		public function voidAcctSavingsBankMutation($data){
			$this->db->where("savings_bank_mutation_id",$data['savings_bank_mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>