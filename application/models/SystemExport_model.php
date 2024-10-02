<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class SystemExport_model extends CI_Model {
		var $table = "core_SystemExport";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember($branch_id, $core_member_export_start_date, $core_member_export_end_date){
			$this->db->select('core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.dusun_id, core_dusun.dusun_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.kelurahan_id, core_kelurahan.kelurahan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_character, core_member.member_register_date, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance, core_member.member_gender');
			$this->db->from('core_member');
			$this->db->join('core_dusun', 'core_member.dusun_id = core_dusun.dusun_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_kelurahan', 'core_member.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->where('core_member.member_register_date >=', $core_member_export_start_date);
			$this->db->where('core_member.member_register_date <=', $core_member_export_end_date);
			$this->db->where('core_member.branch_id', $branch_id);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getBranchName($branch_id){
			$this->db->select('core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}


		public function getAcctSavingsAccount($branch_id, $acct_savings_account_export_start_date, $acct_savings_account_export_end_date){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_no, core_member.member_name, core_member.member_address, core_member.dusun_id, core_dusun.dusun_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.kelurahan_id, core_kelurahan.kelurahan_name, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_date, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('core_dusun', 'core_member.dusun_id = core_dusun.dusun_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_kelurahan', 'core_member.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.savings_account_date >=', $acct_savings_account_export_start_date);
			$this->db->where('acct_savings_account.savings_account_date <=', $acct_savings_account_export_end_date);
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsAccountLastDate($savings_account_id){
			$this->db->select('MAX(acct_savings_account_detail.today_transaction_date) as today_transaction_date');
			$this->db->from('acct_savings_account_detail');
			$this->db->where('acct_savings_account_detail.savings_account_id', $savings_account_id);
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['today_transaction_date'];
		}

		public function getAcctDepositoAccount($branch_id, $acct_deposito_account_export_start_date, $acct_deposito_account_export_end_date){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_serial_no, acct_deposito_account.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_account.member_id, core_member.member_no, core_member.member_name, core_member.member_address, core_member.dusun_id, core_dusun.dusun_name, core_member.kelurahan_id, core_kelurahan.kelurahan_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, acct_deposito_account.deposito_id, acct_deposito.deposito_name, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_amount');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_deposito_account.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_dusun', 'core_member.dusun_id = core_dusun.dusun_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_kelurahan', 'core_member.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.deposito_account_date >=', $acct_deposito_account_export_start_date);
			$this->db->where('acct_deposito_account.deposito_account_date <=', $acct_deposito_account_export_end_date);
			$this->db->where('acct_deposito_account.branch_id', $branch_id);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctJournalVoucher($branch_id, $acct_journal_voucher_export_start_date, $acct_journal_voucher_export_end_date){
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_description, acct_journal_voucher_item.journal_voucher_debit_amount, acct_journal_voucher_item.journal_voucher_credit_amount, acct_journal_voucher_item.account_id, acct_account.account_code, acct_account.account_name, acct_journal_voucher_item.account_id_status, acct_journal_voucher.transaction_module_code, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_id, acct_journal_voucher.journal_voucher_no, acct_journal_voucher.created_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_journal_voucher','acct_journal_voucher_item.journal_voucher_id = acct_journal_voucher.journal_voucher_id');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->where('acct_journal_voucher.journal_voucher_date >=',$acct_journal_voucher_export_start_date);
			$this->db->where('acct_journal_voucher.journal_voucher_date <=',$acct_journal_voucher_export_end_date);		
			$this->db->where('acct_journal_voucher.branch_id', $branch_id);
			$this->db->where('acct_journal_voucher.data_state', 0);	
			$this->db->where('acct_journal_voucher_item.data_state', 0);		
			$this->db->where('acct_journal_voucher_item.journal_voucher_amount <>', 0);		
			$this->db->order_by('acct_journal_voucher.journal_voucher_date','desc');
			$this->db->order_by('acct_journal_voucher_item.journal_voucher_item_id','ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getJournalVoucherItemID($journal_voucher_id){
			$this->db->select_min('acct_journal_voucher_item.journal_voucher_item_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('acct_journal_voucher_item.journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_item_id'];
		}

		public function getUsername($user_id){
			$this->db->select('system_user.username');
			$this->db->from('system_user');
			$this->db->where('system_user.user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getAcctCreditsAccount($branch_id, $acct_credits_account_export_start_date, $acct_credits_account_export_end_date){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.savings_account_id, acct_savings_account.savings_account_no, acct_credits_account.member_id, core_member.member_no, core_member.member_name, core_member.member_address, core_member.dusun_id, core_dusun.dusun_id, core_member.kelurahan_id, core_kelurahan.kelurahan_name, core_kecamatan.kecamatan_name, acct_credits_account.credits_id, acct_credits.credits_name, acct_credits_account.credits_account_agunan, acct_credits_account.credits_account_period, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_margin, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_balance_margin, acct_credits_account.credits_account_last_payment_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_credits_account.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_dusun', 'core_member.dusun_id = core_dusun.dusun_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_kelurahan', 'core_member.kelurahan_id = core_kelurahan.kelurahan_id');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_account.credits_account_date >=', $acct_credits_account_export_start_date);
			$this->db->where('acct_credits_account.credits_account_date <=', $acct_credits_account_export_end_date);
			$this->db->where('acct_credits_account.branch_id', $branch_id);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccountBalanceDetail($branch_id, $acct_general_ledger_export_start_date, $acct_general_ledger_export_end_date){
			$this->db->select('acct_account_balance_detail.account_balance_detail_id, acct_account_balance_detail.transaction_type, acct_account_balance_detail.transaction_code, acct_account_balance_detail.transaction_date, acct_account_balance_detail.transaction_id, acct_account_balance_detail.account_id, acct_account.account_code, acct_account.account_name, acct_account_balance_detail.opening_balance, acct_account_balance_detail.account_in, acct_account_balance_detail.account_out, acct_account_balance_detail.last_balance, acct_account.account_default_status');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account', 'acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account_balance_detail.transaction_date >=', $acct_general_ledger_export_start_date);
			$this->db->where('acct_account_balance_detail.transaction_date <=', $acct_general_ledger_export_end_date);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$this->db->order_by('acct_account_balance_detail.account_id', 'ASC');	
			$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');	
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'ASC');	
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getJournalVoucherDescription($journal_voucher_id, $account_id){
			$this->db->select('acct_journal_voucher_item.journal_voucher_description');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('acct_journal_voucher_item.journal_voucher_id', $journal_voucher_id);
			$this->db->where('acct_journal_voucher_item.account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_description'];
		}

		public function getJournalVoucherNo($journal_voucher_id){
			$this->db->select('acct_journal_voucher.journal_voucher_no');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_no'];
		}
	}
?>