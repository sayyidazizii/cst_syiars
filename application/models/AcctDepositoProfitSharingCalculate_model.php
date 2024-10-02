<?php
	class AcctDepositoProfitSharingCalculate_model extends CI_Model {
		var $table = "acct_deposito_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getAcctDeposito(){
			$this->db->select('deposito_id, deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoAccount($branch_id){
			$this->db->select('acct_deposito_account.branch_id, acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_id, acct_deposito.deposito_name, acct_deposito_account.deposito_account_amount, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.savings_account_id');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			// $this->db->join('acct_savings_account', 'acct_deposito_account.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('MONTH(acct_deposito_account.deposito_account_due_date) >= 03');
			$this->db->where('YEAR(acct_deposito_account.deposito_account_due_date) >= 2019');
			// $this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			// $this->db->where('acct_deposito_account.branch_id', $branch_id);
			return $this->db->get()->result_array();
		}

		public function getAcctDepositoProfitSharingBackup(){
			$this->db->select('acct_deposito_profit_sharing.*, acct_savings_account.savings_id, acct_savings_account.savings_account_opening_balance, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			return $this->db->get()->result_array();
		}

		public function getSavingsAccount($member_id){
			$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->where('member_id', $member_id);
			$this->db->where('data_state', 0);
			$this->db->where('savings_id !=', 1);
			$this->db->where('savings_id !=', 5);
			$this->db->where('savings_id !=', 9);
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		// public function getSavingsAccount($member_id){
		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 3);
		// 	$this->db->limit(1);
		// 	$result1 = $this->db->get()->row_array();
			
		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 8);
		// 	$this->db->limit(1);
		// 	$result2 = $this->db->get()->row_array();
			
		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 11);
		// 	$this->db->limit(1);
		// 	$result3 = $this->db->get()->row_array();
			
		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 10);
		// 	$this->db->limit(1);
		// 	$result4 = $this->db->get()->row_array();

		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 4);
		// 	$this->db->limit(1);
		// 	$result5 = $this->db->get()->row_array();
			
		// 	$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
		// 	$this->db->from('acct_savings_account');
		// 	$this->db->where('member_id', $member_id);
		// 	$this->db->where('data_state', 0);
		// 	$this->db->where('savings_id', 6);
		// 	$this->db->limit(1);
		// 	$result6 = $this->db->get()->row_array();	

		// 	$result = array ();
		// 	$result = array_merge($result1, $result2, $result3, $result4, $result5, $result6);
		// 	return $result; 
		// }

		public function getSavingsAccountDetail($savings_account_id){
			$this->db->select('savings_account_id, savings_id, savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getDepositoProfitSharingTotalAmount($deposito_id, $branch_id, $period){
			$deposito_index_amount = $this->getDepositoIndexAmount($deposito_id, $period);

			$this->db->select('SUM(acct_deposito_account.deposito_account_amount * '.$deposito_index_amount.') AS deposito_profit_sharing_amount ');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_profit_sharing_amount'];
		}

		public function getDepositoAccountDailyAverageBalance($deposito_id, $branch_id, $deposito_daily_average_balance_minimum){
			$this->db->select('deposito_account_daily_average_balance');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->where('acct_deposito_account.deposito_account_daily_average_balance >=', $deposito_daily_average_balance_minimum);
			$result = $this->db->get()->row_array();
			return $result['deposito_account_daily_average_balance'];
		}

		public function getDepositoIndexAmount($deposito_id, $period){
			$this->db->select('deposito_index_amount');
			$this->db->from('acct_deposito_index');
			$this->db->where('deposito_id', $deposito_id);
			$this->db->where('deposito_index_period', $period);
			$this->db->order_by('last_update', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['deposito_index_amount'];
		}

		public function getDepositoAccountID($data){
			$this->db->select('deposito_account_id');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('deposito_profit_sharing_period', $data['deposito_profit_sharing_period']);
			$this->db->where('deposito_account_id', $data['deposito_account_id']);
			return $this->db->get();
		}

		public function getDepositoProfitSharingToken($deposito_profit_sharing_token){
			$this->db->select('deposito_profit_sharing_token');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('deposito_profit_sharing_token', $deposito_profit_sharing_token);
			return $this->db->get();
		}

		public function updateAcctDepositoProfitSharingLog($data){
			$deposito_profit_sharing_total_deposito_process = 1;
			$this->db->set('acct_deposito_profit_sharing_log.deposito_profit_sharing_total_deposito_process','acct_deposito_profit_sharing_log.deposito_profit_sharing_total_deposito_process + '. (int)$deposito_profit_sharing_total_deposito_process, FALSE);
			$this->db->where('acct_deposito_profit_sharing_log.deposito_profit_sharing_log_id', $data['deposito_profit_sharing_log_id']);
			if($this->db->update('acct_deposito_profit_sharing_log')){
				return true;
			} else {
				return false;
			}
		}
		
		public function insertAcctDepositoProfitSharingCalculate($data){
			// print_r($data);exit;
			return $query = $this->db->insert('acct_deposito_profit_sharing',$data);			
		}

		public function getTotalDepositoProfitSharing($deposito_id, $period, $date){
			$this->db->select('SUM(deposito_profit_sharing_amount) AS deposito_profit_sharing_amount');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('deposito_id', $deposito_id);
			$this->db->where('deposito_profit_sharing_period', $period);
			$this->db->where('deposito_profit_sharing_date', $date);
			$result = $this->db->get()->row_array();
			return $result['deposito_profit_sharing_amount'];
		}

		public function insertAcctDepositoProfitSharingLog($data){
			return $query = $this->db->insert('acct_deposito_profit_sharing_log',$data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function insertAcctSavingsTransferMutation($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation',$data);
		}

		public function getSavingsTranferMutationID($created_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('acct_savings_transfer_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_mutation.created_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_mutation_id'];
		}

		public function insertAcctSavingsTransferMutationTo($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation_to',$data);
		}

		public function getAcctDepositoProfitSharing_Detail(){
			$this->db->select('acct_deposito_profit_sharing.branch_id, acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_profit_sharing_period, acct_deposito_profit_sharing.member_id, core_member.member_name, acct_deposito_profit_sharing.savings_account_id, acct_savings_account.savings_id, acct_deposito_profit_sharing.deposito_id,acct_deposito_profit_sharing.deposito_profit_sharing_amount, acct_deposito_profit_sharing.deposito_profit_sharing_period, acct_deposito_profit_sharing.deposito_profit_sharing_date, acct_savings_account.savings_account_no, acct_deposito_profit_sharing.deposito_daily_average_balance, acct_deposito_profit_sharing.deposito_index_amount, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_due_date');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('core_member', 'acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_profit_sharing.deposito_id = acct_deposito.deposito_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			
			// $this->db->where('acct_deposito_profit_sharing.deposito_account_id', $deposito_account_id);
			// $this->db->where('acct_deposito_profit_sharing.branch_id', $branch_id);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_period', '42019');
			$this->db->order_by('acct_deposito_profit_sharing.deposito_profit_sharing_id', 'DESC');
			// $this->db->limit(1);
			return $this->db->get()->result_array();

		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_account_id, acct_savings_account.savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();

		}
		
		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
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