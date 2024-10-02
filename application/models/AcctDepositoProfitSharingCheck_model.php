<?php
	class AcctDepositoProfitSharingCheck_model extends CI_Model {
		var $table = "acct_deposito_account";
		
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
		
		public function getAcctDepositoProfitSharingCheck($start_date, $end_date, $branch_id){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no, acct_deposito_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_profit_sharing.member_id, core_member.member_name, core_member.member_address, acct_deposito_profit_sharing.deposito_profit_sharing_amount, acct_deposito_profit_sharing.deposito_account_last_balance, acct_deposito_profit_sharing.deposito_profit_sharing_date, acct_deposito_profit_sharing.deposito_profit_sharing_due_date, acct_deposito_profit_sharing.deposito_profit_sharing_status');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date >=', $start_date);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date <=', $end_date);
			$this->db->where('acct_deposito_profit_sharing.branch_id', $branch_id);
			$this->db->where('acct_deposito_account.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctDepositoProfitSharing_Detail($deposito_profit_sharing_id){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_id, acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.deposito_account_period, acct_deposito_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id AS member_id_savings, acct_deposito_profit_sharing.member_id, core_member.member_name, core_member.member_address, core_member.member_phone, acct_deposito_profit_sharing.deposito_profit_sharing_amount, acct_deposito_profit_sharing.deposito_account_last_balance, acct_deposito_profit_sharing.deposito_profit_sharing_date, acct_deposito_profit_sharing.deposito_profit_sharing_status');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_id', $deposito_profit_sharing_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, CONCAT(acct_savings_account.savings_account_no," - ",core_member.member_name) AS savings_account_no, acct_savings_account.savings_id, acct_savings_account.member_id, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getDepositoIndexAmount($period, $deposito_id){
			$this->db->select('deposito_index_amount');
			$this->db->from('acct_deposito_index');
			$this->db->where('deposito_index_period', $period);
			$this->db->where('deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_index_amount'];
		}

		public function updateAcctDepositoProfitSharing($data){
			$this->db->where('deposito_profit_sharing_id', $data['deposito_profit_sharing_id']);
			if($this->db->update('acct_deposito_profit_sharing', $data)){
				return true;
			} else {
				return false;
			}
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

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function getAcctDepositoProfitSharing_Last($deposito_profit_sharing_id){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('core_member','acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account','acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_id', $deposito_profit_sharing_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherID($created_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.created_id', $created_id);
			$this->db->order_by('acct_journal_voucher.journal_voucher_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_id'];
		}

		public function getAccountID($savings_id){
			$this->db->select('acct_savings.account_id');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getAccountBasilID($deposito_id){
			$this->db->select('acct_deposito.account_basil_id');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.deposito_id', $deposito_id);
			$result = $this->db->get()->row_array();
			return $result['account_basil_id'];
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}
	}
?>