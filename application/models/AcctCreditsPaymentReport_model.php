<?php
	class AcctCreditsPaymentReport_model extends CI_Model {
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
		
		public function getCreditsAccount($start_date, $end_date, $branch_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_payment_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('acct_credits_account.credits_account_due_date >=', $start_date);
			$this->db->where('acct_credits_account.credits_account_due_date <=', $end_date);
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}

		public function getAcctCreditsPayment($credits_account_id){
			$this->db->select('acct_credits_payment.credits_payment_date, acct_credits_payment.credits_principal_last_balance');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.data_state ', 0);
			$this->db->where('acct_credits_payment.credits_account_id', $credits_account_id);
			$this->db->order_by('acct_credits_payment.credits_payment_date', 'DESC');	
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			// print_r($result);exit;
			return $result;
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			return $this->db->get()->result_array();
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

		public function getCreditsAccountDueDate($date, $branch_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('DAY(acct_credits_account.credits_account_due_date)', $date);
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			$this->db->where('acct_credits_account.credits_account_last_balance_principal > 0');
			$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');	
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}

		public function exportCreditsAccountDueDate($date, $branch_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->where('DAY(acct_credits_account.credits_account_due_date)', $date);
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			$this->db->where('acct_credits_account.credits_account_last_balance_principal > 0');
			$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');	
			$result = $this->db->get();
			// print_r($result);exit;
			return $result;
		}
	}
?>