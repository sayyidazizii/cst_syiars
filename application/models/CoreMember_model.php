<?php
	class CoreMember_model extends CI_Model {
		var $table = "core_member";
		var $table_credits = "acct_credits_account";
		var $column_order = array(null, 'member_no','member_name','user_alamat','member_address',); //field yang ada di table user
		var $column_search = array('member_id','member_name','member_no','member_address'); //field yang diizin untuk pencarian 
		var $order = array('member_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getDataCoreMember($branch_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			// $this->db->where('core_member.branch_id', $branch_id);
			$this->db->where('core_member.data_state', 0);
			$this->db->order_by('core_member.member_no', 'ASC');
			/* print_r($this->db->last_query());
			exit; */
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getIDMenu($id){
			$this->db->select('system_menu.id_menu');
			$this->db->from('system_menu');
			$this->db->where('system_menu.id', $id);
			$result = $this->db->get()->row_array();
			return $result['id_menu'];
		}
		
		public function getIDMenuOnSystemMapping($id_menu, $level){
			$this->db->select('system_menu_mapping.id_menu');
			$this->db->from('system_menu_mapping');
			$this->db->where('system_menu_mapping.id_menu', $id_menu);
			$this->db->where('system_menu_mapping.user_group_level', $level);
			$result = $this->db->get()->row_array();
			if($result['id_menu'] != ''){
				$return = 1;
			}else{
				$return = 0;
			}
			return $return;
		}

		public function getUserGroupLevel($user_group_id){
			$this->db->select('system_user_group.user_group_level');
			$this->db->from('system_user_group');
			$this->db->where('system_user_group.user_group_id', $user_group_id);
			$result = $this->db->get()->row_array();
			return $result['user_group_level'];
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctMutation(){
			$this->db->select('mutation_id, mutation_name');
			$this->db->from('acct_mutation');
			$this->db->where('data_state', 0);
			$this->db->where('mutation_module', 'TAB');
			return $this->db->get()->result_array();
		}

		public function getMutationFunction($mutation_id){
			$this->db->select('mutation_function');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_function'];
		}

		public function getCoreProvince(){
			$this->db->select('core_province.province_id, core_province.province_name');
			$this->db->from('core_province');
			$this->db->where('core_province.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCity($province_id){
			$this->db->select('core_city.city_id, core_city.city_name');
			$this->db->from('core_city');
			$this->db->where('core_city.province_id', $province_id);
			$this->db->where('core_city.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKecamatan($city_id){
			$this->db->select('core_kecamatan.kecamatan_id, core_kecamatan.kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('core_kecamatan.city_id', $city_id);
			$this->db->where('core_kecamatan.data_state', '0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreKelurahan($kecamatan_id){
			$this->db->select('core_kelurahan.kelurahan_id, core_kelurahan.kelurahan_name');
			$this->db->from('core_kelurahan');
			$this->db->where('core_kelurahan.kecamatan_id', $kecamatan_id);
			$this->db->where('core_kelurahan.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreDusun($kelurahan_id){
			$this->db->select('core_dusun.dusun_id, core_dusun.dusun_name');
			$this->db->from('core_dusun');
			$this->db->where('core_dusun.kelurahan_id', $kelurahan_id);
			$this->db->where('core_dusun.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreJob(){
			$this->db->select('job_id, job_name');
			$this->db->from('core_job');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreIdentity(){
			$this->db->select('identity_id, identity_name');
			$this->db->from('core_identity');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getLastMemberNo(){
			$this->db->select('RIGHT(core_member.member_no,8) as last_member_no');
			$this->db->from('core_member');
			$this->db->limit(1);
			$this->db->order_by('core_member.member_id', 'DESC');
			$result = $this->db->get();
			return $result;
		}

		public function getMemberToken($member_token){
			$this->db->select('member_token');
			$this->db->from('core_member');
			$this->db->where('member_token', $member_token);
			$result = $this->db->get()->num_rows();
			return $result;
		}
		
		public function insertCoreMember($data){
			$query = $this->db->insert('core_member',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.kelurahan_id, core_member.dusun_id, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance, core_member.member_last_number');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getMemberTokenEdit($member_token_edit){
			$this->db->select('member_token_edit');
			$this->db->from('core_member');
			$this->db->where('member_token_edit', $member_token_edit);
			return $this->db->get();
		}
		
		public function updateCoreMember($data){
			$this->db->where("member_id",$data['member_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				// if($data['member_principal_savings_last_balance'] == 0){
				// 	$this->db->set('data_state', 2);
				// 	$this->db->where("member_id",$data['member_id']);
				// 	if($this->db->update('core_member')){
						return true;
					// } else {
					// 	return false;
					// }
				// } else {
				// 	return true;
				// }
				
			}else{
				return false;
			}
		}

		public function getSavingsMemberDetailID($member_id){
			$this->db->select('savings_member_detail_id');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->order_by('savings_member_detail_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_member_detail_id'];
		}

		public function updateOpeningMemberDetail($data){
			$this->db->where('savings_member_detail_id', $data['savings_member_detail_id']);
			if($this->db->update('acct_savings_member_detail', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function updatePPOBStatus($member_id){
			$data = array(
				'ppob_status' => 1
			);
			$this->db->where("core_member.member_id",$member_id);
			$query = $this->db->update('core_member', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getSavingsMemberDetailToken($savings_member_detail_token){
			$this->db->select('savings_member_detail_token');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('savings_member_detail_token', $savings_member_detail_token);
			return $this->db->get();
		}

		public function insertAcctSavingsMemberDetail($data){
			$query = $this->db->insert('acct_savings_member_detail',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getLastAcctSavingsMemberDetail($member_id){
			$this->db->select('acct_savings_member_detail.savings_member_detail_id, acct_savings_member_detail.member_id, core_member.member_no, core_member.member_name, core_member.member_address, acct_savings_member_detail.branch_id, acct_savings_member_detail.mutation_id, acct_mutation.mutation_code, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_savings_member_detail.special_savings_amount, acct_savings_member_detail.mandatory_savings_amount, acct_savings_member_detail.last_balance, acct_savings_member_detail.operated_name, acct_savings_member_detail.last_balance_principal, acct_savings_member_detail.last_balance_special, acct_savings_member_detail.last_balance_mandatory');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);

			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');
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

		public function getAccoutCapitalID($branch_id){
			$this->db->select('core_branch.account_capital_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_capital_id'];
		}

		public function getAccountRAKID($branch_id){
			$this->db->select('core_branch.account_rak_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_rak_id'];
		}

		public function getAcctAccountSetting($account_setting_code){
			$this->db->select('acct_account_setting.account_id, acct_account_setting.account_setting_status, acct_account_setting.account_setting_name, acct_account_setting.section_id');
			$this->db->from('acct_account_setting');
			$this->db->where('acct_account_setting.account_setting_code', $account_setting_code);
			$this->db->where('acct_account_setting.data_state', 0);
			$result = $this->db->get()->result_array();
			
			return $result;
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}

		public function getJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
		}
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getAcctSavingsMemberDetail($member_id, $start_date, $end_date){
			$this->db->select('acct_savings_member_detail.savings_member_detail_id, acct_savings_member_detail.member_id, core_member.member_no, acct_savings_member_detail.branch_id, acct_savings_member_detail.mutation_id, acct_mutation.mutation_code, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_savings_member_detail.special_savings_amount, acct_savings_member_detail.mandatory_savings_amount, acct_savings_member_detail.last_balance, acct_savings_member_detail.operated_name, acct_savings_member_detail.last_balance_principal, acct_savings_member_detail.last_balance_special, acct_savings_member_detail.last_balance_mandatory');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.transaction_date >=', $start_date);
			$this->db->where('acct_savings_member_detail.transaction_date <=', $end_date);
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.savings_print_status', 0);
			return $this->db->get()->result_array();
		}

		public function getMemberLastNumber($member_id){
			$this->db->select('member_last_number');
			$this->db->from('core_member');
			$this->db->where('member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['member_last_number'];
		}

		public function updatePrintMutationStatus($data){
			$this->db->set('acct_savings_member_detail.savings_print_status', $data['savings_print_status']);
			$this->db->where('acct_savings_member_detail.savings_member_detail_id', $data['savings_member_detail_id']);
			if($this->db->update('acct_savings_member_detail')){
				$this->db->set('core_member.member_last_number', $data['member_last_number']);
				$this->db->where('core_member.member_id', $data['member_id']);
				if($this->db->update('core_member')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getSavingsAccountToken($savings_account_token){
			$this->db->select('savings_account_token');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_token', $savings_account_token);
			return $this->db->get();
		}


		public function insertAcctSavingsAccount($data){
			return $this->db->insert('acct_savings_account', $data);
		}

		public function getAcctSavingsAccount_Member($member_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings.savings_name');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctCreditsAccount_Member($member_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits.credits_name');
			$this->db->from('acct_credits_account');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_account.member_id', $member_id);
			$this->db->where('acct_credits_account.credits_account_last_balance_principal <> 0');
			$this->db->where('acct_credits_account.data_state', 0);
			return $this->db->get()->result_array();
		}
		
		public function deleteCoreMember($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getCoreMemberStatus(){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_status', 0);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccount($principal_savings_id, $member_id){
			$this->db->select('*');
			$this->db->from('acct_savings_member_detail');
			// $this->db->where('savings_id', $principal_savings_id);
			$this->db->where('member_id', $member_id);
			// $this->db->where('data_state', 0);
			return $this->db->get();
		}

		public function updateCoreMemberStatus($member_id){
			$this->db->where("member_id",$member_id);
			$query = $this->db->update($this->table, array('member_status'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function countData($data_state, $branch_id){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('branch_id', $branch_id);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		public function countData1($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_principal_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		public function countData2($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_special_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		public function countData3($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_mandatory_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		public function getCoreMember($data_state, $branch_id){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('branch_id', $branch_id);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember1($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_principal_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember2($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_special_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember3($data_state){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('data_state', $data_state);
			$this->db->where('member_mandatory_savings_last_balance >=', 1);
			$this->db->order_by('member_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getExport($branch_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.branch_id', $branch_id);
			$this->db->order_by('core_member.member_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		private function _get_datatables_query($data_state, $branch_id)
    {
         
        $this->db->from($this->table);
 		$this->db->where('data_state', $data_state);
 		if(!empty($branch_id)){
 			$this->db->where('branch_id', $branch_id);
 		}
 		
 		$this->db->order_by('member_no', 'ASC');
        $i = 0;
     
        foreach ($this->column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }


    function get_datatables($data_state,  $branch_id)
    {
        $this->_get_datatables_query($data_state, $branch_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    	//$this->db->where('member_principal_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered($data_state, $branch_id)
    {
        $this->_get_datatables_query($data_state, $branch_id);
        //$this->db->where('member_principal_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all($data_state, $branch_id)
    {
        $this->db->from($this->table);
        $this->db->where('data_state', $data_state);
        if(!empty($branch_id) || $branch_id != 0){
				$this->db->where('core_member.branch_id', $branch_id);
			}
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_member_out()
    {
         
        $this->db->from($this->table);
        //$this->db->where('member_principal_savings_last_balance >=', 1);
 		$this->db->where('data_state !=', 0);
 		if(!empty($branch_id)){
 			$this->db->where('branch_id', $branch_id);
 		}
 		
 		$this->db->order_by('member_no', 'ASC');
        $i = 0;
     
        foreach ($this->column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables1($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    	$this->db->where('member_principal_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered1($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        $this->db->where('member_principal_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all1($data_state, $member_savings)
    {
        $this->db->from($this->table);
        $this->db->where('data_state', $data_state);
        //$this->db->where('member_principal_savings_last_balance', $member_savings);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_member_out1()
    {
         
        $this->db->from($this->table);
        //$this->db->where('member_principal_savings_last_balance >=', 1);
 		$this->db->where('data_state !=', 0);
 		// if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
 		
 		$this->db->order_by('member_no', 'ASC');
        $i = 0;
     
        foreach ($this->column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables2($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    	$this->db->where('member_special_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered2($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        $this->db->where('member_special_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all2($data_state, $member_savings)
    {
        $this->db->from($this->table);
        $this->db->where('data_state', $data_state);
        //$this->db->where('member_special_savings_last_balance', $member_savings);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_member_out2()
    {
         
        $this->db->from($this->table);
        //$this->db->where('member_principal_savings_last_balance >=', 1);
 		$this->db->where('data_state !=', 0);
 		// if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
 		
 		$this->db->order_by('member_no', 'ASC');
        $i = 0;
     
        foreach ($this->column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

     function get_datatables3($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    	$this->db->where('member_mandatory_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered3($data_state, $member_savings)
    {
        $this->_get_datatables_query($data_state, $member_savings);
        $this->db->where('member_mandatory_savings_last_balance >=', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all3($data_state)
    {
        $this->db->from($this->table);
        $this->db->where('data_state', $data_state);
        //$this->db->where('member_mandatory_savings_last_balance', $member_savings);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_member_out3()
    {
         
        $this->db->from($this->table);
        //$this->db->where('member_principal_savings_last_balance >=', 1);
 		$this->db->where('data_state !=', 0);
 		// if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
 		
 		$this->db->order_by('member_no', 'ASC');
        $i = 0;
     
        foreach ($this->column_search as $item) // looping awal
        {
            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {
                 
                if($i===0) // looping awal
                {
                    $this->db->group_start(); 
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $i++;
        }
         
        if(isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables_member_out()
    {
        $this->_get_datatables_query_member_out();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered_member_out()
    {
        $this->_get_datatables_query_member_out();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_member_out()
    {
        $this->db->from($this->table);
        $this->db->where('data_state !=', 0);
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }

    public function insertCoreMemberSyncronizeLog($data){
			if($this->db->insert('acct_member_syncronize_log', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getOpeningBalance($member_id, $date){
			$this->db->select('opening_balance');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date', $date);
			$this->db->order_by('savings_member_detail_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getLastDate($member_id, $start_date){
			$this->db->select_max('transaction_date');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date <', $start_date);
			$result = $this->db->get()->row_array();
			return $result['transaction_date'];
		}

		public function getLastBalance($member_id, $date){
			$this->db->select('last_balance');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date', $date);
			$this->db->order_by('savings_member_detail_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getLastBalancePrincipal($member_id, $date){
			$this->db->select('last_balance_principal');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date >=', $date);
			$this->db->order_by('savings_member_detail_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['last_balance_principal'];
		}

		public function getLastBalanceSpecial($member_id, $date){
			$this->db->select('last_balance_special');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date >=', $date);
			$this->db->order_by('savings_member_detail_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['last_balance_special'];
		}

		public function getLastBalanceMandatory($member_id, $date){
			$this->db->select('last_balance_mandatory');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('member_id', $member_id);
			$this->db->where('transaction_date >=', $date);
			$this->db->order_by('savings_member_detail_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['last_balance_mandatory'];
		}

		public function updateAcctSavingsMemberDetail($data){
			$this->db->set('member_principal_savings_last_balance', $data['last_balance_principal']);
			$this->db->set('member_special_savings_last_balance', $data['last_balance_special']);
			$this->db->set('member_mandatory_savings_last_balance', $data['last_balance_mandatory']);
			$this->db->where('member_id', $data['member_id']);
			if($this->db->update('core_member')){
				$this->db->where('savings_member_detail_id', $data['savings_member_detail_id']);
				if($this->db->update('acct_savings_member_detail', $data)){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
			
		}
	}
?>