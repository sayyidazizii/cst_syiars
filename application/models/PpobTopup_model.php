<?php
	class PpobTopup_model extends CI_Model {
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

		public function getPreferencePpob(){
			$this->db->select('preference_ppob.*') ;
			$this->db->from('preference_ppob');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getPpobTopup($start_date, $end_date){
			$this->db->select('ppob_topup.ppob_topup_no, ppob_topup.branch_id, core_branch.branch_name, ppob_topup.account_id, acct_account.account_name, ppob_topup.ppob_topup_date, ppob_topup.ppob_topup_amount, ppob_topup.ppob_topup_remark');
			$this->db->from('ppob_topup');
			$this->db->join('core_branch', 'ppob_topup.branch_id = core_branch.branch_id');
			$this->db->join('acct_account', 'ppob_topup.account_id = acct_account.account_id');
			$this->db->where('ppob_topup.ppob_topup_date >=', $start_date);
			$this->db->where('ppob_topup.ppob_topup_date <=', $end_date);
			$this->db->where('ppob_topup.data_state =', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name') ;
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getBranchName($branch_id){
			$this->db->select('core_branch.branch_name') ;
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							from acct_account
							where acct_account.data_state = 0");
			return $hasil->result_array();
		}

		public function getLastPPOBTopUp($created_id){
			$this->db->select('ppob_topup.ppob_topup_id, ppob_topup.ppob_topup_no');
			$this->db->from('ppob_topup');
			$this->db->where('ppob_topup.created_id', $created_id);
			$this->db->order_by('ppob_topup.ppob_topup_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getPpobCompanyID($company_database){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_company.ppob_company_id, ppob_company.ppob_company_code') ;
			$this->db_cipta->from('ppob_company');
			$this->db_cipta->where('ppob_company.ppob_company_database ', $company_database);
			$this->db_cipta->limit(1);
			$result = $this->db_cipta->get()->row_array();
			return $result;
		}

		public function getTopupBranchBalance($branch_id){
			$this->db_api = $this->load->database('api', true);

			$this->db_api->select('ppob_topup_branch.topup_branch_balance');
			$this->db_api->from('ppob_topup_branch');
			$this->db_api->where('ppob_topup_branch.branch_id', $branch_id);
			$result = $this->db_api->get()->row_array();
			return $result;
		}

		public function getPpobTopupToken($ppob_topup_token){
			$this->db->select('ppob_topup_token');
			$this->db->from('ppob_topup');
			$this->db->where('ppob_topup_token', $ppob_topup_token);
			return $this->db->get();
		}

		public function insertPpobTopup($data){
			if($this->db->insert('ppob_topup', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getPpobTopupID($created_id){
			$this->db->select('ppob_topup_id');
			$this->db->from('ppob_topup');
			$this->db->where('ppob_topup.created_id', $created_id);
			$this->db->order_by('ppob_topup.ppob_topup_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['ppob_topup_id'];
		}

		

		public function getPpobCompanyBalance($ppob_company_id){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_company.ppob_company_balance') ;
			$this->db_cipta->from('ppob_company');
			$this->db_cipta->where('ppob_company.ppob_company_id ', $ppob_company_id);
			$result = $this->db_cipta->get()->row_array();
			return $result['ppob_company_balance'];
		}

		public function insertPpobTopUpCipta($data){
			$this->db_cipta = $this->load->database('cipta', true);
			return $query = $this->db_cipta->insert('ppob_topup_company',$data);
		}

		public function getPpobTopupCompanyToken($ppob_topup_company_token){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_topup_company_token');
			$this->db_cipta->from('ppob_topup_company');
			$this->db_cipta->where('ppob_topup_company_token', $ppob_topup_company_token);
			return $this->db_cipta->get();
		}

		public function getPpobTopup_Last($created_id){
			$this->db->select('ppob_topup.ppob_topup_id, ppob_topup.ppob_topup_no');
			$this->db->from('ppob_topup');
			$this->db->where('ppob_topup.created_id', $created_id);
			$this->db->order_by('ppob_topup.ppob_topup_id','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
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
	}
?>