<?php
	class AcctSavingsDailyCashMutation_model extends CI_Model {
		var $table = "acct_deposito_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavings_CashDeposit($start_date, $end_date, $cash_deposit_id, $savings_id, $branch_id){
			$this->db->select('acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.savings_cash_mutation_last_balance');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date >=', $start_date);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date <=', $end_date);
			$this->db->where('acct_savings_cash_mutation.mutation_id', $cash_deposit_id);
			$this->db->where('acct_savings_cash_mutation.savings_id', $savings_id);
			if(!empty($branch_id)){
				$this->db->where('acct_savings_cash_mutation.branch_id', $branch_id);
			}
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSavings_CashWithdrawal($start_date, $end_date, $cash_withdrawal_id, $savings_id, $branch_id){
			$this->db->select('acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.savings_cash_mutation_last_balance');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date >=', $start_date);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date <=', $end_date);
			$this->db->where('acct_savings_cash_mutation.mutation_id', $cash_withdrawal_id);
			$this->db->where('acct_savings_cash_mutation.savings_id', $savings_id);
			if(!empty($branch_id)){
				$this->db->where('acct_savings_cash_mutation.branch_id', $branch_id);
			}
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getMutationCode($mutation_id){
			$this->db->select('mutation_code');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_code'];
		}

		public function getMemberName($member_id){
			$this->db->select('member_name');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}


		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}
	}
?>