<?php
	class PpobTopupMember_model extends CI_Model {
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

		public function getPpobTopupMember($start_date, $end_date){
			$this->db->select('ppob_topup_member.*, core_member.member_no, core_member.member_name, acct_savings_account.savings_account_no, acct_savings.savings_name');
			$this->db->from('ppob_topup_member');
			$this->db->join('core_member', 'ppob_topup_member.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'ppob_topup_member.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('ppob_topup_member.ppob_topup_member_date >=', $start_date);
			$this->db->where('ppob_topup_member.ppob_topup_member_date <=', $end_date);
			$this->db->where('ppob_topup_member.data_state =', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.member_no, core_member.member_name');
			$this->db->from('core_member');
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccount($member_id){
			$this->db->select('acct_savings_account.savings_account_id, CONCAT(acct_savings_account.savings_account_no, " - " ,acct_savings.savings_name) AS savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.member_id', $member_id);
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.voided_remark, acct_savings_account.validation, acct_savings_account.validation_on, acct_savings_account.validation_id, acct_savings_account.office_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getPpobTopupMemberToken($ppob_topup_member_token){
			$this->db->select('ppob_topup_member_token');
			$this->db->from('ppob_topup_member');
			$this->db->where('ppob_topup_member_token', $ppob_topup_member_token);
			return $this->db->get();
		}

		public function insertPpobTopupMember($data){
			if($this->db->insert('ppob_topup_member', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getPpobTopupMemberID($created_id){
			$this->db->select('ppob_topup_member_id');
			$this->db->from('ppob_topup_member');
			$this->db->where('ppob_topup_member.created_id', $created_id);
			$this->db->order_by('ppob_topup_member.ppob_topup_member_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['ppob_topup_member_id'];
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

		public function getPPOBBalance($ppob_company_id, $ppob_agen_id){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_balance_amount');
			$this->db_cipta->from('ppob_balance');
			$this->db_cipta->where('ppob_company_id', $ppob_company_id);
			$this->db_cipta->where('ppob_agen_id', $ppob_agen_id);
			$result = $this->db_cipta->get()->row_array();
			return $result['ppob_balance_amount'];
		}

		public function insertPpobTopUpCipta($data){
			$this->db_cipta = $this->load->database('cipta', true);
			return $query = $this->db_cipta->insert('ppob_topup',$data);
		}

		public function getPpobTopupToken($ppob_topup_token){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_topup_token');
			$this->db_cipta->from('ppob_topup');
			$this->db_cipta->where('ppob_topup_token', $ppob_topup_token);
			return $this->db_cipta->get();
		}

		public function getPpobTopupMember_Last($created_id){
			$this->db->select('ppob_topup_member.ppob_topup_member_id');
			$this->db->from('ppob_topup_member');
			$this->db->where('ppob_topup_member.created_id', $created_id);
			$this->db->order_by('ppob_topup_member.ppob_topup_member_id','DESC');
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
	}
?>