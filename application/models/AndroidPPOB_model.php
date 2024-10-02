<?php
	class AndroidPPOB_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			$this->dbcipta = $this->load->database('cipta', true);
		} 

		//Check
		public function checkAcctSavingsTransfer($savings_account_from_id, $savings_account_to_id){
			$this->db->select('acct_savings_transfer_mutation.*');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_to','acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_savings_transfer_mutation_from','acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 3);
			$this->db->where('acct_savings_transfer_mutation_from.savings_account_id', $savings_account_from_id);
			$this->db->where('acct_savings_transfer_mutation_to.savings_account_id', $savings_account_to_id);
			$this->db->where('acct_savings_transfer_mutation.ppob_topup_status', 0);
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			return $this->db->get();
			// print_r($this->dbcipta->last_query());exit;
		}

		//PPOB//
		public function getPPOBCompanyID($database){
			$this->dbcipta->select('ppob_company_id');
			$this->dbcipta->from('ppob_company');
			$this->dbcipta->where('ppob_company_database', $database);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result['ppob_company_id'];
		}

		public function getPpobCompanyBalance2($ppob_company_id){
			$this->db_cipta = $this->load->database('cipta', true);

			$this->db_cipta->select('ppob_company.ppob_company_balance') ;
			$this->db_cipta->from('ppob_company');
			$this->db_cipta->where('ppob_company.ppob_company_id ', $ppob_company_id);
			$result = $this->db_cipta->get()->row_array();
			return $result['ppob_company_balance'];
		}

		public function insertPPOBTopUP($data){
			$query = $this->dbcipta->insert('ppob_topup', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getPPOBTopUP_Detail($ppob_topup_id, $ppob_topup_status){
			$this->dbcipta->select('ppob_topup_id, ppob_topup_no, ppob_topup_date, created_on, ppob_topup_amount, ppob_topup_status');
			$this->dbcipta->from('ppob_topup');
			$this->dbcipta->where('ppob_topup_id', $ppob_topup_id);
			$this->dbcipta->where('ppob_topup_status', $ppob_topup_status);
			return $this->dbcipta->get()->row_array();
		}

		public function getPPOBBalance($ppob_company_id, $ppob_agen_id){
			$this->dbcipta->select('ppob_balance_amount');
			$this->dbcipta->from('ppob_balance');
			$this->dbcipta->where('ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_agen_id', $ppob_agen_id);
			$result = $this->dbcipta->get()->row_array();
			return $result['ppob_balance_amount'];
		}

		public function getPPOBTransactionLog($ppob_company_id, $ppob_agen_id){
			$this->dbcipta->select('ppob_transaction_id, ppob_unique_code, ppob_transaction_code');
			$this->dbcipta->from('ppob_transaction_log');
			$this->dbcipta->where('ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->group_by('ppob_transaction_id');
			$this->dbcipta->group_by('ppob_unique_code');
			$this->dbcipta->group_by('ppob_transaction_code');
			$this->dbcipta->order_by('ppob_transaction_id', 'DESC');
			$result = $this->dbcipta->get()->result_array();
			return $result;
		}

		

		public function getPPOBProduct($ppob_product_category_id){
			$this->dbcipta->select('ppob_product_id, ppob_product_category_id, ppob_product_code, ppob_product_name, ppob_product_price');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_category_id', $ppob_product_category_id);
			$this->dbcipta->where('data_state', 0);
			$this->dbcipta->order_by('ppob_product_id','ASC');
			return $this->dbcipta->get()->result_array();
		}

		public function getPPOBProduct_Detail($ppob_product_code){
			$this->dbcipta->select('ppob_product_id, ppob_product_category_id, ppob_product_code, ppob_product_name, ppob_product_title, ppob_product_cost, ppob_product_price');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_code', $ppob_product_code);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result;
		}

		public function getPPOBProduct_DetailByID($ppob_product_id){
			$this->dbcipta->select('ppob_product_name, ppob_product_price, ppob_product_category_id');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_id', $ppob_product_id);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result;
		}

		public function getPPOBProductCategoryID($ppob_product_id){
			$this->dbcipta->select('ppob_product_category_id');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_id', $ppob_product_id);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result['ppob_product_category_id'];
		}

		public function insertPPOBTransaction($data){
			$query = $this->db->insert('ppob_transaction', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function insertPPOBTransaction_Company($data){
			$query = $this->dbcipta->insert('ppob_transaction', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function insertPPOBProfitShare_Company($data){
			$query = $this->db->insert('ppob_profit_share', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getPPOBTransaction($ppob_company_id, $ppob_agen_id, $ppob_transaction_status){
			$this->dbcipta->select('ppob_transaction.ppob_transaction_id, ppob_transaction.ppob_transaction_no, ppob_transaction.ppob_transaction_date, ppob_transaction.created_on, ppob_transaction.ppob_transaction_amount, ppob_transaction.ppob_transaction_status, ppob_product.ppob_product_name, ppob_product_category.ppob_product_category_name, ppob_transaction.ppob_transaction_remark');
			$this->dbcipta->from('ppob_transaction');
			$this->dbcipta->join('ppob_product', 'ppob_transaction.ppob_product_id = ppob_product.ppob_product_id');
			$this->dbcipta->join('ppob_product_category', 'ppob_transaction.ppob_product_category_id = ppob_product_category.ppob_product_category_id');
			$this->dbcipta->where('ppob_transaction.ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_transaction.ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->where('ppob_transaction.ppob_transaction_status', $ppob_transaction_status);
			$this->dbcipta->order_by('ppob_transaction.ppob_transaction_id','DESC');
			$this->dbcipta->limit(10);
			$result = $this->dbcipta->get()->result_array();
			return $result;
		}

		public function getPPOBTransaction_Product($ppob_company_id, $ppob_agen_id, $ppob_product_id){
			$this->dbcipta->select('ppob_transaction.ppob_transaction_id, ppob_transaction.ppob_transaction_no, ppob_transaction.ppob_transaction_date, ppob_transaction.created_on, ppob_transaction.ppob_transaction_amount, ppob_transaction.ppob_transaction_status, ppob_product.ppob_product_name, ppob_product_category.ppob_product_category_name, ppob_transaction.ppob_transaction_remark');
			$this->dbcipta->from('ppob_transaction');
			$this->dbcipta->join('ppob_product', 'ppob_transaction.ppob_product_id = ppob_product.ppob_product_id');
			$this->dbcipta->join('ppob_product_category', 'ppob_transaction.ppob_product_category_id = ppob_product_category.ppob_product_category_id');
			$this->dbcipta->where('ppob_transaction.ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_transaction.ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->where('ppob_transaction.ppob_product_id', $ppob_product_id);
			$this->dbcipta->where('ppob_transaction.ppob_transaction_status', 1);
			$this->dbcipta->order_by('ppob_transaction.created_on','DESC');
			$this->dbcipta->limit(10);
			return $this->dbcipta->get()->result_array();
			// print_r($this->dbcipta->last_query());exit;
		}

		//END PPOB//

		/* PPOB COMPANY */
		public function getPPOBCompanyBalance($ppob_company_id){
			$this->dbcipta->select('ppob_company_balance');
			$this->dbcipta->from('ppob_company');
			$this->dbcipta->where('ppob_company_id', $ppob_company_id);
			$result = $this->dbcipta->get()->row_array();
			return $result['ppob_company_balance'];
		}

		public function updatePPOBCompanyBalance($data){
			$this->dbcipta->where('ppob_company_id', $data['ppob_company_id']);
			$query = $this->dbcipta->update('ppob_company', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}


		//JURNAL
		public function getPreferencePpob(){
			$this->db->select('preference_ppob.*') ;
			$this->db->from('preference_ppob');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getPpobTransactionMember_Last($ppob_agen_id, $ppob_company_id){
			$this->dbcipta->select('ppob_transaction.ppob_transaction_id, ppob_transaction.ppob_transaction_no');
			$this->dbcipta->from('ppob_transaction');
			$this->dbcipta->where('ppob_transaction.ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->where('ppob_transaction.ppob_company_id', $ppob_company_id);
			$this->dbcipta->order_by('ppob_transaction.ppob_transaction_id','DESC');
			$this->dbcipta->limit(1);
			$result = $this->dbcipta->get()->row_array();
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



		/* NEW UPDATE 29042021 */

		public function getPPOBBalanceSavingsAccount($member_id){
			$this->db->select('core_member.member_id, core_member.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance');
			$this->db->from('core_member');
			$this->db->join('acct_savings_account','core_member.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings','acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('core_member.member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getTopupBranchBalance($branch_id){
			$this->db->select('ppob_topup_branch.topup_branch_balance');
			$this->db->from('ppob_topup_branch');
			$this->db->where('ppob_topup_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['topup_branch_balance'];
		}

		public function getAccountID($savings_id){
			$this->db->select('acct_savings.account_id');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsTransferMutationTo_History($member_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id, acct_savings_transfer_mutation.savings_transfer_mutation_date,  acct_savings_transfer_mutation_to.mutation_id, acct_mutation.mutation_name, acct_savings_transfer_mutation_to.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_to.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_transfer_mutation_to.member_id, core_member.member_name, acct_savings_transfer_mutation_to.savings_transfer_mutation_to_amount, acct_savings_transfer_mutation.created_on');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_to', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_to.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_to.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_transfer_mutation_to.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_to.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_to.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 3);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_id', 'DESC');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}


		public function getAcctSavingsAccount_DetailAccount($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, core_member.member_no, core_member.member_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->order_by('acct_savings_account.savings_account_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsTransferMutationFrom_History($member_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id, acct_savings_transfer_mutation.savings_transfer_mutation_date, acct_savings_transfer_mutation_from.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.mutation_id, acct_mutation.mutation_name, 
			acct_savings_transfer_mutation_from.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_from.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_transfer_mutation_from.member_id, core_member.member_name, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_amount, acct_savings_transfer_mutation.created_on');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_from.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_transfer_mutation_from.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_transfer_mutation_from.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_transfer_mutation_from.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_from.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 3);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_id', 'DESC');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsTransferMutationTo_Last($created_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id, acct_savings_transfer_mutation_to.savings_account_id, acct_savings_account.savings_account_no, acct_savings_transfer_mutation_to.member_id, core_member.member_name');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_to','acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_savings_account','acct_savings_transfer_mutation_to.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member','acct_savings_transfer_mutation_to.member_id = core_member.member_id');
			$this->db->where('acct_savings_transfer_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_id','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

	}
?>