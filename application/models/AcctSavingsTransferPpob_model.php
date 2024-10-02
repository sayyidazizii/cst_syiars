<?php
	class AcctSavingsTransferPpob_model extends CI_Model {
		var $table = "acct_savings_transfer_mutation";
		var $column_order = array(null, 'acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_savings_transfer_mutation.savings_cash_mutation_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->model('Connection_model');
			$this->CI->load->dbforge();
		} 

		public function getPreferenceCompany(){
			$this->db->select('preference_company.*') ;
			$this->db->from('preference_company');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsTransferPpob($start_date, $end_date){
			$this->db->select('acct_savings_transfer_ppob.*, acct_savings_account.savings_account_no');
			$this->db->from('acct_savings_transfer_ppob');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_ppob.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('acct_savings_transfer_ppob.savings_transfer_ppob_date >=', $start_date);
			$this->db->where('acct_savings_transfer_ppob.savings_transfer_ppob_date <=', $end_date);
			$this->db->where('acct_savings_transfer_ppob.data_state =', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsAccountNo($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_no') ;
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.savings_account_id =', $savings_account_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_no'];
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no, acct_savings_account.savings_account_blockir_type, acct_savings_account.savings_account_blockir_status, acct_savings_account.savings_account_blockir_amount');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsTransferMutation($savings_account_id){
			$this->db->select('acct_savings_transfer_mutation_from.savings_transfer_mutation_from_id, acct_savings_account.savings_account_no, core_member.member_name');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_savings_transfer_mutation_to', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_from.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_from.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation_to.savings_account_id', $savings_account_id);
			$this->db->where('acct_savings_transfer_mutation.ppob_topup_status', 0);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 3);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsTransferMutationFrom_Detail($savings_transfer_mutation_from_id){
			$this->db->select('acct_savings_transfer_mutation_from.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_from.member_id, core_member.member_name, core_member.member_address, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_amount, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_id');
			$this->db->from('acct_savings_transfer_mutation_from');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_from.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_from.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation_from.savings_transfer_mutation_from_id', $savings_transfer_mutation_from_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getSavingsTransferPpobToken($savings_transfer_ppob_token){
			$this->db->select('savings_transfer_ppob_token');
			$this->db->from('acct_savings_transfer_ppob');
			$this->db->where('savings_transfer_ppob_token', $savings_transfer_ppob_token);
			return $this->db->get();
		}

		public function insertAcctSavingsTransferPpob($data){
			if($this->db->insert('acct_savings_transfer_ppob', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getSavingsTransferPpobID($created_id){
			$this->db->select('savings_transfer_ppob_id');
			$this->db->from('acct_savings_transfer_ppob');
			$this->db->where('acct_savings_transfer_ppob.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_ppob.savings_transfer_ppob_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_ppob_id'];
		}

		public function insertAcctSavingsTransferPpobItem($data){
			if($this->db->insert_batch('acct_savings_transfer_ppob_item', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function updateAcctSavingsTransferMutation($data){
			if($this->db->update_batch('acct_savings_transfer_mutation', $data, 'savings_transfer_mutation_id')){
				return true;
			} else {
				return true;
			}
		}

		public function getSavingsCashMutationToken($savings_cash_mutation_token){
			$this->db->select('savings_cash_mutation_token');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->where('savings_cash_mutation_token', $savings_cash_mutation_token);
			return $this->db->get();
		}

		public function insertAcctSavingsCashMutation($data){
			if($this->db->insert('acct_savings_cash_mutation',$data)){
				return true;
			} else {
				return false;
			}
		}

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function getAcctSavingsCashMutation_Last($created_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_savings_account','acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member','acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.created_id', $created_id);
			$this->db->limit(1);
			$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_id','DESC');
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

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
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

		public function getJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
		}

		public function getPpobCompanyID($company_database){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_company.ppob_company_id') ;
			$this->db_cipta->from('ppob_company');
			$this->db_cipta->where('ppob_company.ppob_company_database ', $company_database);
			$this->db_cipta->limit(1);
			$result = $this->db_cipta->get()->row_array();
			return $result['ppob_company_id'];
		}

		public function insertPpobTopUp($data){
			$this->db_cipta = $this->load->database('cipta', true);
			return $query = $this->db_cipta->insert_batch('ppob_topup',$data);
		}

		public function getAcctSavingsTransferPPOB_Detail($savings_transfer_ppob_id){
			$this->db->select('acct_savings_transfer_ppob.*, acct_savings_account.savings_account_no, core_member.member_name');
			$this->db->from('acct_savings_transfer_ppob');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_ppob.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_ppob.savings_transfer_ppob_id', $savings_transfer_ppob_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsTransferPPOBItem_Detail($savings_transfer_ppob_id){
			$this->db->select('acct_savings_transfer_ppob_item.*, acct_savings_account.savings_account_no, core_member.member_name, core_member.member_address');
			$this->db->from('acct_savings_transfer_ppob_item');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_ppob_item.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_ppob_item.savings_transfer_ppob_id', $savings_transfer_ppob_id);
			return $this->db->get()->result_array();
		}
	}
?>