<?php
	class AcctRecapitulationReport_model extends CI_Model {
		var $table = "acct_savings_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getBranchName($branch_id){
			$this->db->select('branch_name');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount($savings_id, $branch_id){
			$this->db->select('SUM(acct_savings_account.savings_account_last_balance) AS total_savings_account, COUNT(acct_savings_account.member_id) AS total_member');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			return $this->db->get()->row_array();
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctCreditsAccount($credits_id, $branch_id){
			$this->db->select('SUM(acct_credits_account.credits_account_last_balance_principal) AS total_credits_account, COUNT(acct_credits_account.member_id) AS total_member');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_id', $credits_id);
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			return $this->db->get()->row_array();
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.deposito_id, acct_deposito.deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccount($deposito_id, $branch_id){
			$this->db->select('SUM(acct_deposito_account.deposito_account_amount) AS total_deposito_account, COUNT(acct_deposito_account.member_id) AS total_member');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			return $this->db->get()->row_array();
		}

		public function getAcctSourceFund(){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}
	}
?>