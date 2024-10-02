<?php
	class Android_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		}

		public function getCoreMember($branch_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no');
			$this->db->from('core_member');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('core_member.branch_id', $branch_id);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreProgram(){
			$this->db->select('core_program.program_id, core_program.program_name, core_program.program_remark');
			$this->db->from('core_program');
			$this->db->where('core_program.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_name,core_branch.branch_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$this->db->order_by('core_branch.branch_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMemberTotal(){
			$this->db->select('core_member.member_id');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCoreMemberTotal_LastMonth($last_month,$year){
			$this->db->select('core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('month(core_member.member_register_date)', $last_month);
			$this->db->where('year(core_member.member_register_date)', $year);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCoreMemberTotal_ThisDay($day,$month,$year){
			$this->db->select('core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('day(core_member.member_register_date)', $day);
			$this->db->where('month(core_member.member_register_date)', $month);
			$this->db->where('year(core_member.member_register_date)', $year);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCoreMemberTotal_ThisDayBranch($day,$month,$year, $branch){
			$this->db->select('core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('day(core_member.member_register_date)', $day);
			$this->db->where('month(core_member.member_register_date)', $month);
			$this->db->where('year(core_member.member_register_date)', $year);
			$this->db->where('core_member.branch_id', $branch);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCoreMemberTotal_ThisMonth($month,$year){
			$this->db->select('core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('month(core_member.member_register_date)', $month);
			$this->db->where('year(core_member.member_register_date)', $year);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCoreMemberTotal_ThisMonthBranch($month,$year,$branch){
			$this->db->select('core_member.member_register_date');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('month(core_member.member_register_date)', $month);
			$this->db->where('year(core_member.member_register_date)', $year);
			$this->db->where('core_member.branch_id', $branch);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getItemPicture($program_id){
			$this->db->select('core_program.program_photo');
			$this->db->from('core_program');
			$this->db->where('core_program.program_id', $program_id);
			$result = $this->db->get()->row_array();
			return $result['program_photo'];
		}

		public function getSystemUserDusun($user_id){
			$this->db->select('system_user_dusun.dusun_id');
			$this->db->from('system_user_dusun');
			$this->db->where('system_user_dusun.user_id', $user_id);
			$result = $this->db->get()->result_array();
			return array_column($result, 'dusun_id');
		}
		
		public function getAcctSavingsAccount($member_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->order_by('acct_savings.savings_code', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSystemUser($user_id, $password){
			$this->db->select('system_user.user_id');
			$this->db->from('system_user');
			$this->db->where('system_user.user_id', $user_id);
			$this->db->where('system_user.password', $password);
			$result = $this->db->get()->num_rows();

			if ($result > 0){
				return true;
			} else {
				return false;
			}
		}

		public function getMemberPassword($member_id, $member_password){
			$this->db->select('core_member.member_id');
			$this->db->from('core_member');
			$this->db->where('core_member.member_id', $member_id);
			$this->db->where('core_member.member_password', $member_password);
			$result = $this->db->get()->num_rows();

			if ($result > 0){
				return true;
			} else {
				return false;
			}
		}
		
		public function insertAcctSavingsCashMutation($data){
			return $query = $this->db->insert('acct_savings_cash_mutation',$data);
		}

		public function getAcctCreditsAccount($member_id, $branch_id){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_id, acct_credits.credits_name, acct_credits.credits_code, acct_credits_account.member_id, acct_credits_account.credits_account_serial, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_payment_amount, acct_credits_account.credits_account_period, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_payment_date, acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_accumulated_fines, acct_credits_account.payment_type_id, acct_credits_account.credits_account_interest, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, acct_credits.credits_name, acct_credits.credits_fine');
			$this->db->from('acct_credits_account');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_credits_account.member_id', $member_id);
			$this->db->where('acct_credits_account.data_state', 0);
			$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}
		

		public function insertAcctCreditsPayment($data){
			return $query = $this->db->insert('acct_credits_payment',$data);
		}

		public function updateAcctCreditsAccount($data){
			$credits_payment_principal 		= $data['credits_payment_principal'];
			$credits_payment_margin 		= $data['credits_payment_margin'];


			$this->db->set('acct_credits_account.credits_account_last_balance_principal', 'acct_credits_account.credits_account_last_balance_principal - '.(int)$credits_payment_principal, FALSE);
			$this->db->set('acct_credits_account.credits_account_last_balance_margin', 'acct_credits_account.credits_account_last_balance_margin - '.(int)$credits_payment_margin, FALSE);
			$this->db->where('acct_credits_account.credits_account_id', $data['credits_account_id']);
			if($this->db->update('acct_credits_account')){
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

		public function getCreditsAccountPaymentDate($credits_account_id){
			$this->db->select('acct_credits_account.credits_account_payment_date');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_account_id', $credits_account_id);
			$result = $this->db->get()->row_array();
			return $result['credits_account_payment_date'];
		}

		public function getAcctCreditsPayment_Detail($credits_payment_id){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.member_id, core_member.member_name, core_member.member_address, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.credits_payment_amount, acct_credits_payment.branch_id, core_branch.branch_city, acct_credits_account.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member', 'acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->join('core_branch', 'acct_credits_account.branch_id = core_branch.branch_id');
			$this->db->where('acct_credits_payment.credits_payment_id', $credits_payment_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getSavingsCashDepositAmount($user_id, $savings_cash_mutation_date, $mutation_id){
			$this->db->select('SUM(acct_savings_cash_mutation.savings_cash_mutation_amount) AS savings_cash_mutation_amount');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->where('acct_savings_cash_mutation.created_id', $user_id);
			$this->db->where('acct_savings_cash_mutation.mutation_id', $mutation_id);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date', $savings_cash_mutation_date);
			$result = $this->db->get()->row_array();
			return $result['savings_cash_mutation_amount'];
		}

		public function getCreditsPaymentAmount($user_id, $credits_payment_date){
			$this->db->select('SUM(acct_credits_payment.credits_payment_principal) AS credits_payment_principal, SUM(acct_credits_payment.credits_payment_margin) AS credits_payment_margin');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.created_id', $user_id);
			$this->db->where('acct_credits_payment.credits_payment_date', $credits_payment_date);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getCoreMember_Detail($member_id, $branch_id, $data){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance ');
			$this->db->from('core_member');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('core_member.member_id', $member_id);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getCoreMember_Login($member_no, $member_password){
			$this->db->select('core_member.member_id, core_member.branch_id, core_member.member_no, core_member.member_name, core_member.savings_account_id');
			$this->db->from('core_member');
			$this->db->where('core_member.member_no', $member_no);
			$this->db->where('core_member.member_password', $member_password);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctCreditsPayment_Member($member_id, $credits_payment_date){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.member_id, core_member.member_name, core_member.member_address, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.credits_payment_date, acct_credits_payment.credits_payment_principal, acct_credits_payment.credits_payment_interest, acct_credits_payment.branch_id, core_branch.branch_city, acct_credits_account.credits_id, acct_credits.credits_code, acct_credits.credits_name');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member', 'acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->join('core_branch', 'acct_credits_account.branch_id = core_branch.branch_id');
			$this->db->where('acct_credits_payment.member_id', $member_id);
			/*$this->db->where('acct_credits_payment.credits_payment_date', $credits_payment_date);*/
			$this->db->where('acct_credits_payment.credits_payment_status', 1);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount_Detail($member_id, $savings_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, core_member.member_no, core_member.member_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->order_by('acct_savings_account.savings_account_id', 'ASC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
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

		public function getSystemUserPassword($user_id, $password){
			$this->db->select('system_user.user_id');
			$this->db->from('system_user');
			$this->db->where('system_user.data_state', 0);
			$this->db->where('system_user.user_id', $user_id);
			$this->db->where('system_user.password', $password);
			$result = $this->db->get();
			/*print_r($this->db->last_query());*/
			if ($result->num_rows() == 0){
				return false;
			} else {
				return true;	
			}
		}

		public function updateSystemUser($data){
			$this->db->where('system_user.user_id', $data['user_id']);
			$query = $this->db->update('system_user', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}


		public function getCoreMemberPassword($member_id, $member_password){
			$this->db->select('core_member.member_id');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			$this->db->where('core_member.member_password', $member_password);
			$result = $this->db->get();
			/*print_r($this->db->last_query());*/
			if ($result->num_rows() == 0){
				return false;
			} else {
				return true;	
			}
		}

		public function updateCoreMember($data){
			$this->db->where('core_member.member_id', $data['member_id']);
			$query = $this->db->update('core_member', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getCoreMemberNo_Detail($member_no, $branch_id, $data){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no');
			$this->db->from('core_member');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('core_member.member_no', $member_no);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsAccountDetailNo($savings_account_no){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, core_member.member_no, core_member.member_name, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.savings_account_no', $savings_account_no);
			$this->db->where('acct_savings_account.savings_account_last_balance >', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}


		public function getAcctCreditsAccountDetailSerial($credits_account_serial){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_id, acct_credits.credits_name, acct_credits.credits_code, acct_credits_account.member_id, acct_credits_account.credits_account_serial, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_payment_amount, acct_credits_account.credits_account_period, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_interest_last_balance, acct_credits_account.credits_account_payment_date, acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_accumulated_fines, acct_credits_account.payment_type_id, acct_credits_account.credits_account_interest, acct_credits_account.credits_account_period, acct_credits_account.credits_payment_period, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, acct_credits.credits_name, acct_credits.credits_fine');
			$this->db->from('acct_credits_account');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_credits_account.credits_account_serial', $credits_account_serial);
			$this->db->where('acct_credits_account.data_state', 0);
			$result = $this->db->get()->row_array();
			/* print_r($this->db->last_query()); */
			return $result;
		}


		public function getAcctSavingsCashMutation($created_id, $savings_cash_mutation_date, $mutation_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name, core_member.member_no, acct_savings_cash_mutation.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.mutation_id, acct_savings_cash_mutation.savings_cash_mutation_opening_balance, acct_savings_cash_mutation.savings_cash_mutation_last_balance');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_cash_mutation.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date', $savings_cash_mutation_date);
			$this->db->where('acct_savings_cash_mutation.created_id', $created_id);
			$this->db->where('acct_savings_cash_mutation.mutation_id', $mutation_id);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_status', 1);
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}


		public function getAcctCreditsPayment($created_id, $credits_payment_date){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.credits_id, acct_credits.credits_code, acct_credits.credits_name, acct_credits_payment.credits_payment_date, acct_credits_payment.member_id, core_member.member_name, core_member.member_no, acct_credits_payment.credits_payment_amount, acct_credits_payment.credits_principal_opening_balance, acct_credits_payment.credits_interest_opening_balance, acct_credits_payment.credits_principal_last_balance, acct_credits_payment.credits_interest_last_balance, acct_credits_payment.credits_payment_to');
			$this->db->from('acct_credits_payment');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('core_member', 'acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits', 'acct_credits_payment.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_payment.credits_payment_date', $credits_payment_date);
			$this->db->where('acct_credits_payment.created_id', $created_id);
			$this->db->where('acct_credits_payment.credits_payment_status', 1);
			$this->db->where('acct_credits_payment.data_state', 0);
			$this->db->order_by('acct_credits_payment.credits_payment_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccountDetail($savings_account_id){
			$this->db->select('acct_savings_account_detail.savings_account_detail_id, acct_savings_account_detail.savings_account_id, acct_savings_account_detail.mutation_id, acct_mutation.mutation_code, acct_mutation.mutation_name, acct_savings_account_detail.today_transaction_date, acct_savings_account_detail.mutation_in, acct_savings_account_detail.mutation_out, acct_savings_account_detail.last_balance');
			$this->db->from('acct_savings_account_detail');
			$this->db->join('acct_mutation', 'acct_savings_account_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_account_detail.savings_account_id', $savings_account_id);
			$this->db->order_by('acct_savings_account_detail.savings_account_detail_id', 'DESC');
			$this->db->limit(6);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsMandatoryDetail($member_id){
			$this->db->select('acct_savings_member_detail.member_id, core_member.member_no, core_member.member_name, core_member.member_mandatory_savings_last_balance, acct_savings_member_detail.transaction_date, acct_savings_member_detail.mandatory_savings_amount');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('core_member', 'acct_savings_member_detail.member_id = core_member.member_id');
			$this->db->where('acct_savings_member_detail.mutation_id',1);	
			$this->db->where('acct_savings_member_detail.member_id', $member_id);	
			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');	
			$this->db->limit(6);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function insertAcctSavingsMandatoryLog($data){
			return $query = $this->db->insert('acct_savings_mandatory_log',$data);
		}

		public function getSavingsMandatoryLogOD($created_id){
			$this->db->select('acct_savings_mandatory_log.savings_mandatory_log_id');
			$this->db->from('acct_savings_mandatory_log');
			$this->db->where('acct_savings_mandatory_log.created_id', $created_id);
			$this->db->order_by('acct_savings_mandatory_log.savings_mandatory_log_id','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_mandatory_log_id'];
		}

		public function getAcctSavingsMandatoryLog($created_id, $savings_mandatory_log_date){
			$this->db->select('acct_savings_mandatory_log.savings_mandatory_log_id, acct_savings_mandatory_log.member_id, core_member.member_no, core_member.member_name, acct_savings_mandatory_log.savings_mandatory_log_date, acct_savings_mandatory_log.savings_mandatory_log_amount');
			$this->db->from('acct_savings_mandatory_log');
			$this->db->join('core_member', 'acct_savings_mandatory_log.member_id = core_member.member_id');
			$this->db->where('acct_savings_mandatory_log.savings_mandatory_log_date', $savings_mandatory_log_date);
			$this->db->where('acct_savings_mandatory_log.created_id', $created_id);
			$this->db->order_by('acct_savings_mandatory_log.savings_mandatory_log_id', 'DESC');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getAcctSavingsMandatoryLog_Detail($savings_mandatory_log_id){
			$this->db->select('acct_savings_mandatory_log.savings_mandatory_log_id, acct_savings_mandatory_log.member_id, core_member.member_no, core_member.member_name, core_member.member_address, acct_savings_mandatory_log.branch_id, core_branch.branch_city,  acct_savings_mandatory_log.savings_mandatory_log_date, acct_savings_mandatory_log.savings_mandatory_log_amount');
			$this->db->from('acct_savings_mandatory_log');
			$this->db->join('core_member', 'acct_savings_mandatory_log.member_id = core_member.member_id');
			$this->db->join('core_branch', 'acct_savings_mandatory_log.branch_id = core_branch.branch_id');
			$this->db->where('acct_savings_mandatory_log.savings_mandatory_log_id', $savings_mandatory_log_id);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsCashMutation_Total($created_id, $savings_cash_mutation_date, $mutation_id){
			$this->db->select_sum('acct_savings_cash_mutation.savings_cash_mutation_amount');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date', $savings_cash_mutation_date);
			$this->db->where('acct_savings_cash_mutation.created_id', $created_id);
			$this->db->where('acct_savings_cash_mutation.mutation_id', $mutation_id);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_status', 1);
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['savings_cash_mutation_amount'];
		}

		public function getAcctCreditsPayment_Total($created_id, $credits_payment_date){
			$this->db->select_sum('acct_credits_payment.credits_payment_amount');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.credits_payment_date', $credits_payment_date);
			$this->db->where('acct_credits_payment.created_id', $created_id);
			$this->db->where('acct_credits_payment.credits_payment_status', 1);
			$this->db->where('acct_credits_payment.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['credits_payment_amount'];
		}

		public function getAcctSavingsMandatoryLog_Total($created_id, $savings_mandatory_log_date){
			$this->db->select_sum('acct_savings_mandatory_log.savings_mandatory_log_amount');
			$this->db->from('acct_savings_mandatory_log');
			$this->db->where('acct_savings_mandatory_log.savings_mandatory_log_date', $savings_mandatory_log_date);
			$this->db->where('acct_savings_mandatory_log.created_id', $created_id);
			$result = $this->db->get()->row_array();
			return $result['savings_mandatory_log_amount'];
		}

		public function getSavingsID(){
			$this->db->select('preference_company.savings_id');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result['savings_id'];
		}

		public function getAcctSavingsAccount_NoTo($savings_id, $savings_account_no){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, core_member.member_no, core_member.member_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.savings_account_no', $savings_account_no);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getLastTransactionPrincipal($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_mutation.mutation_name');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.principal_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getLastTransactionMandatory($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.mandatory_savings_amount, acct_mutation.mutation_name');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.mandatory_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getLastTransactionSpecial($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.special_savings_amount, acct_mutation.mutation_name');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.special_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccountMember($member_id, $savings_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.member_id', $member_id);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_status', 0);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getLastTransactionSavings($savings_account_id){
			$this->db->select('acct_savings_account_detail.mutation_id, acct_savings_account_detail.today_transaction_date, acct_savings_account_detail.mutation_in, acct_savings_account_detail.mutation_out, acct_mutation.mutation_name');
			$this->db->from('acct_savings_account_detail');
			$this->db->join('acct_mutation', 'acct_savings_account_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_account_detail.savings_account_id', $savings_account_id);
			$this->db->order_by('acct_savings_account_detail.today_transaction_date', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name, acct_savings.savings_code');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings.data_state', 0);
			$this->db->order_by('acct_savings.savings_code', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsPrincipal($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.principal_savings_amount, acct_mutation.mutation_name, acct_savings_member_detail.last_update');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.principal_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');
			$this->db->limit(6);
			$result = $this->db->get()->result_array();
			/* print_r($this->db->last_query());
			exit; */
			return $result;
		}

		public function getAcctSavingsMandatory($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.mandatory_savings_amount, acct_mutation.mutation_name, acct_savings_member_detail.last_update');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.mandatory_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');
			$this->db->limit(6);
			$result = $this->db->get()->result_array();
			/* print_r($this->db->last_query());
			exit; */
			return $result;
		}

		public function getAcctSavingsSpecial($member_id){
			$this->db->select('acct_savings_member_detail.mutation_id, acct_savings_member_detail.transaction_date, acct_savings_member_detail.special_savings_amount, acct_mutation.mutation_name, acct_savings_member_detail.last_update');
			$this->db->from('acct_savings_member_detail');
			$this->db->join('acct_mutation', 'acct_savings_member_detail.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_member_detail.member_id', $member_id);
			$this->db->where('acct_savings_member_detail.special_savings_amount > 0');
			$this->db->order_by('acct_savings_member_detail.transaction_date', 'DESC');
			$this->db->order_by('acct_savings_member_detail.savings_member_detail_id', 'DESC');
			$this->db->limit(6);
			$result = $this->db->get()->result_array();
			/* print_r($this->db->last_query());
			exit; */
			return $result;
		}

		public function getSpecialSavingsTotal($awal_tahun,$date){
			$this->db->select_sum('acct_savings_member_detail.special_savings_amount');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('acct_savings_member_detail.transaction_date>=', $awal_tahun);
			$this->db->where('acct_savings_member_detail.transaction_date<=', $date);
			$result = $this->db->get()->row_array();
			return $result['special_savings_amount'];
		}

		public function getMandatorySavingsTotal($awal_tahun,$date){
			$this->db->select_sum('acct_savings_member_detail.mandatory_savings_amount');
			$this->db->from('acct_savings_member_detail');
			$this->db->where('acct_savings_member_detail.transaction_date>=', $awal_tahun);
			$this->db->where('acct_savings_member_detail.transaction_date<=', $date);
			$result = $this->db->get()->row_array();
			return $result['mandatory_savings_amount'];
		}

		public function getSavingsDepositTotal($savings_id,$awal_tahun,$date){
			$this->db->select_sum('acct_savings_account.savings_account_first_deposit_amount');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->where('acct_savings_account.savings_account_date>=', $awal_tahun);
			$this->db->where('acct_savings_account.savings_account_date<=', $date);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['savings_account_first_deposit_amount'];
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.*');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCreditsAccountTotal($credits_id,$awal_tahun,$date){
			$this->db->select('acct_credits_account.credits_account_id');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_id', $credits_id);
			$this->db->where('acct_credits_account.credits_account_date>=', $awal_tahun);
			$this->db->where('acct_credits_account.credits_account_date<=', $date);
			$this->db->where('acct_credits_account.data_state', 0);
			$result = $this->db->get()->num_rows();
			return $result;
		}

		public function getCreditsPaymentTotal($credits_id,$awal_tahun,$date){
			$this->db->select_sum('acct_credits_account.credits_account_payment_amount');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_id', $credits_id);
			$this->db->where('acct_credits_account.credits_account_date>=', $awal_tahun);
			$this->db->where('acct_credits_account.credits_account_date<=', $date);
			$this->db->where('acct_credits_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['credits_account_payment_amount'];
		}

		public function getAcctDeposito(){
			$this->db->select('acct_deposito.*');
			$this->db->from('acct_deposito');
			$this->db->where('acct_deposito.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getDepositoAccountTotal($deposito_id,$awal_tahun,$date){
			$this->db->select_sum('acct_deposito_account.deposito_account_amount');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_account.deposito_account_date>=', $awal_tahun);
			$this->db->where('acct_deposito_account.deposito_account_date<=', $date);
			$this->db->where('acct_deposito_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['deposito_account_amount'];
		}

		public function getSavingsWithdrawalTotal($savings_id,$awal_tahun,$date){
			$this->db->select_sum('acct_savings_cash_mutation.savings_cash_mutation_amount');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->where('acct_savings_cash_mutation.savings_id', $savings_id);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date>=', $awal_tahun);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date<=', $date);
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$this->db->where('acct_savings_cash_mutation.mutation_id', 2);
			$result = $this->db->get()->row_array();
			return $result['savings_cash_mutation_amount'];
		}

		public function getAcctSavingsCashMutation_History($member_id, $data_mutation, $savings_account_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_cash_mutation.mutation_id, acct_mutation.mutation_name, acct_savings_cash_mutation.member_id, acct_savings_cash_mutation.branch_id, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.created_on, acct_savings_cash_mutation.savings_cash_mutation_status, acct_savings_cash_mutation.created_id');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_cash_mutation.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$this->db->where('acct_savings_cash_mutation.member_id', $member_id);
			$this->db->where('acct_savings_cash_mutation.savings_account_id', $savings_account_id);
			$this->db->where_in('acct_savings_cash_mutation.mutation_id', $data_mutation);
			$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_date','DESC');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsTransferMutationTo_History($member_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_date, acct_savings_transfer_mutation_to.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.savings_account_id as savings_account_from_id, acct_savings_transfer_mutation_to.mutation_id, acct_mutation.mutation_name, acct_savings_transfer_mutation_to.savings_transfer_mutation_to_amount, acct_savings_transfer_mutation_to.savings_account_id as savings_account_to_id, acct_savings_transfer_mutation.created_on');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_savings_transfer_mutation_to', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_to.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_to.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 1);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_date', 'DESC');
			$this->db->limit(4);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavingsAccount_DetailMBayar($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings.savings_code, acct_savings_account.member_id, core_member.member_no, core_member.member_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
			$this->db->from('acct_savings_account');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			$this->db->where('acct_savings_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAcctSavingsTransferMutationFrom_History($member_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_date, acct_savings_transfer_mutation_from.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.savings_account_id as savings_account_from_id, acct_savings_transfer_mutation_from.mutation_id, acct_mutation.mutation_name, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_amount, acct_savings_transfer_mutation_to.savings_account_id as savings_account_to_id, acct_savings_transfer_mutation.created_on');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_savings_transfer_mutation_to', 'acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->join('acct_mutation', 'acct_savings_transfer_mutation_from.mutation_id = acct_mutation.mutation_id');
			$this->db->where('acct_savings_transfer_mutation.data_state', 0);
			$this->db->where('acct_savings_transfer_mutation_from.member_id', $member_id);
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 1);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_date', 'DESC');
			$this->db->limit(4);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctCreditsPayment_History($member_id){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.member_id, core_member.member_name, core_member.member_address, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_payment.credits_payment_amount, acct_credits_payment.branch_id, core_branch.branch_city, acct_credits_account.credits_id, acct_credits.credits_name, acct_credits_payment.created_on, acct_credits_payment.credits_payment_status');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member', 'acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account', 'acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->join('core_branch', 'acct_credits_account.branch_id = core_branch.branch_id');
			$this->db->where('acct_credits_payment.member_id', $member_id);
			$this->db->where('acct_credits_payment.data_state', 0);
			$this->db->order_by('acct_credits_payment.credits_payment_id', 'DESC');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getBranchID($member_id){
			$this->db->select('core_member.branch_id');
			$this->db->from('core_member');
			$this->db->where('core_member.member_id', $member_id);
			$result = $this->db->get()->row_array();
			return $result['branch_id'];
		}

		public function getAcctSavingsTransferMutation_ZISWAF($member_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id, acct_savings_transfer_mutation_from.member_id as member_from_id, acct_savings_transfer_mutation.created_on, acct_savings_transfer_mutation_from.savings_transfer_mutation_from_amount, acct_savings_transfer_mutation_to.savings_account_id as savings_account_to_id, acct_savings_transfer_mutation_from.savings_account_id as savings_account_from_id, acct_savings_transfer_mutation_to.member_id as member_to_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->join('acct_savings_transfer_mutation_from','acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_from.savings_transfer_mutation_id');
			$this->db->join('acct_savings_transfer_mutation_to','acct_savings_transfer_mutation.savings_transfer_mutation_id = acct_savings_transfer_mutation_to.savings_transfer_mutation_id');
			$this->db->where('acct_savings_transfer_mutation.savings_transfer_mutation_status', 2);
			$this->db->where('acct_savings_transfer_mutation_from.member_id', $member_id);
			$this->db->order_by('acct_savings_transfer_mutation.savings_transfer_mutation_id','DESC');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferencePPOB(){
			$this->db->select('*');
			$this->db->from('preference_ppob');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function updateAcctSavingsAccount($datasavings, $datadetail){
			$this->db->where('savings_account_id', $datasavings['savings_account_id']);
			if($this->db->update('acct_savings_account', $datasavings)){
				if($this->db->insert('acct_savings_account_detail', $datadetail)){
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