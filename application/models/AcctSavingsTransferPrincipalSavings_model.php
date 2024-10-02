<?php
	class AcctSavingsTransferPrincipalSavings_model extends CI_Model {
		var $table = "acct_deposito_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->dbforge();
		}

		public function getCoreMember(){
			$this->db->select('*');
			$this->db->from('core_member');
			$this->db->where('data_state', 0);
			$this->db->where('member_principal_savings_last_balance',0);
			// $this->db->limit(20);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccount($member_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_id !=', 1);
			$this->db->where('acct_savings_account.savings_id !=', 5);
			$this->db->where('acct_savings_account.savings_id !=', 9);
			$this->db->where('acct_savings_account.savings_account_last_balance > 10000');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function insertAcctSavingsTransferPrincipalSavings($data){
			return $this->db->insert_batch('acct_savings_transfer_principal', $data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
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

		public function getAcctSavingsTransferPrincipal(){
			$this->db->select('acct_savings_transfer_principal.*, core_member.member_no, core_member.member_name, core_member.member_address, acct_savings.savings_name, acct_savings_account.savings_account_no');
			$this->db->from('acct_savings_transfer_principal');
			$this->db->join('core_member', 'acct_savings_transfer_principal.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_transfer_principal.savings_id = acct_savings.savings_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_principal.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->order_by('core_member.member_no', 'ASC');
			return $this->db->get()->result_array();
		}



		
	
	}
?>