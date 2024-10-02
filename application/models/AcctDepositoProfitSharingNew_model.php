<?php
	class AcctDepositoProfitSharingNew_model extends CI_Model {
		var $table = "acct_deposito_cash_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->dbforge();
		} 

		public function getPeriodLog(){
			$this->db->select('*');
			$this->db->from('system_period_log');
			$this->db->order_by('period_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctDeposito(){
			$this->db->select('deposito_id, deposito_name');
			$this->db->from('acct_deposito');
			$this->db->where('data_state', 0);
			$this->db->where('acct_deposito.deposito_profit_sharing', 1);
			return $this->db->get()->result_array();
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.data_state', 0);
			$this->db->where('acct_savings.savings_nisbah > 0');
			return $this->db->get()->result_array();
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function insertDataLogStep($data){
			return $query = $this->db->insert('acct_deposito_profit_sharing_log', $data);
			
		}

		public function deleteDataLog($data){
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			if($this->db->delete('acct_deposito_profit_sharing_log')){
				return true;
			} else {
				return false;
			}
		}

		//--------------------Step 1 Create Table-----------------------------------//

		public function insertDataLogStep1($data, $file){
			$val = $this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = 'cst_koperasi' AND table_name = 'acct_deposito_profit_sharing_temp' LIMIT 1")->row_array();
			// print_r($val);exit;

			if(!empty($val))
			{
					// print_r("true");exit;
			   if($this->dbforge->drop_table('acct_deposito_profit_sharing_temp')){
					$sql_contents = explode(";", $file);

					$no = 0;
					foreach($sql_contents as $key => $query){
						$result = $this->db->query($query);
					}

					if($this->db->insert('acct_deposito_profit_sharing_log', $data)){
						$data_log_step1 = $this->getDataLogStep1($data);

						$this->db->set('status_process', 1);
						$this->db->where('deposito_profit_sharing_log_id', $data_log_step1['deposito_profit_sharing_log_id']);
						if($this->db->update('acct_deposito_profit_sharing_log')){
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
			else
			{

				// print_r("false");exit;
			    $sql_contents = explode(";", $file);

				$no = 0;
				foreach($sql_contents as $key => $query){
					$result = $this->db->query($query);
				}

				if($this->db->insert('acct_deposito_profit_sharing_log', $data)){
					$data_log_step1 = $this->getDataLogStep1($data);

					$this->db->set('status_process', 1);
					$this->db->where('deposito_profit_sharing_log_id', $data_log_step1['deposito_profit_sharing_log_id']);
					if($this->db->update('acct_deposito_profit_sharing_log')){
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
			
			
		}

		public function getDataLogStep1($data){
			$this->db->select('deposito_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('acct_deposito_profit_sharing_log');
			$this->db->where('step', 1);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->where('status_process', 1);
			$this->db->order_by('deposito_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function createTable($data, $file){
			if($this->dbforge->drop_table('acct_deposito_profit_sharing_temp')){
				$sql_contents = explode(";", $file);

				$no = 0;
				foreach($sql_contents as $key => $query){
					$result = $this->db->query($query);
				}

				$data_log_step1 = $this->getDataLogStep1($data);

				$this->db->set('status_process', 1);
				$this->db->where('deposito_profit_sharing_log_id', $data_log_step1['deposito_profit_sharing_log_id']);
				if($this->db->update('acct_deposito_profit_sharing_log')){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		//--------------------Step 2 Hitung dan Simpan Basil Deposito-----------------------------------//

		public function getDataLogStep2($data){
			$this->db->select('deposito_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('acct_deposito_profit_sharing_log');
			$this->db->where('step', 2);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->where('status_process', 1);
			$this->db->order_by('deposito_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getPreferenceProfitSharing(){
			$this->db->select('*');
			$this->db->from('preference_profit_sharing');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getAcctDepositoAccount($branch_id, $last_date){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.branch_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_id, acct_deposito.deposito_name, acct_deposito.deposito_period, acct_deposito_account.deposito_account_amount, acct_deposito_account.member_id, core_member.member_name, acct_deposito_account.savings_account_id, acct_deposito_account.deposito_account_basil');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito.deposito_profit_sharing', 1);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_account.deposito_account_date <', $last_date);
			// $this->db->where('acct_deposito_account.branch_id', $branch_id);
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
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito.deposito_profit_sharing', 1);
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
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito.deposito_profit_sharing', 1);
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

		public function insertAcctDepositoProfitSharingTemp($data, $log){

			if($this->db->insert_batch('acct_deposito_profit_sharing_temp',$data)){
				$this->db->set('status_process', 1);
				$this->db->where('created_id', $log['created_id']);
				$this->db->where('periode', $log['periode']);
				$this->db->where('branch_id', $log['branch_id']);
				if($this->db->update('acct_deposito_profit_sharing_log')){
					
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		//---------------------------Step 3 Update Data Basil -----------------------------------//

		public function getCoreBranch(){
			$this->db->select('*');
			$this->db->from('core_branch');
			// $this->db->where('branch_id != 1');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getPeriode($data){
			$this->db->select('periode');
			$this->db->from('acct_deposito_profit_sharing_log');
			$this->db->where('created_id', $data['user_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->order_by('deposito_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['periode'];
		}

		public function getDataLogStep3($data){
			$this->db->select('deposito_profit_sharing_log_id, branch_id, created_id, created_on, periode, step, total_account, total_process, status_process');
			$this->db->from('acct_deposito_profit_sharing_log');
			$this->db->where('step', 3);
			$this->db->where('created_id', $data['created_id']);
			$this->db->where('branch_id', $data['branch_id']);
			$this->db->where('periode', $data['periode']);
			$this->db->order_by('deposito_profit_sharing_log_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}


		public function getAcctDepositoProfitSharingTemp($branch_id){
			$this->db->select('acct_deposito_profit_sharing_temp.*, acct_deposito_account.deposito_account_no, acct_savings_account.savings_account_no, core_member.member_address, acct_deposito.deposito_period, acct_deposito_profit_sharing_temp.deposito_profit_sharing_period');
			$this->db->from('acct_deposito_profit_sharing_temp');
			$this->db->join('core_member', 'acct_deposito_profit_sharing_temp.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing_temp.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_deposito', 'acct_deposito_profit_sharing_temp.deposito_id = acct_deposito.deposito_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing_temp.savings_account_id = acct_savings_account.savings_account_id');
			// $this->db->where('acct_deposito_profit_sharing_temp.branch_id', $branch_id);
			return $this->db->get()->result_array();
		}

		public function insertAcctDepositoProfitSharingFix(){
			$query = $this->db->query("INSERT INTO acct_deposito_profit_sharing_new (deposito_account_id, branch_id, deposito_id, member_id, deposito_profit_sharing_date, deposito_index_amount, deposito_daily_average_balance, deposito_profit_sharing_amount, deposito_account_last_balance, deposito_profit_sharing_period, savings_account_id, deposito_profit_sharing_status,  deposito_profit_sharing_token, operated_name, created_id, created_on) SELECT deposito_account_id, branch_id, deposito_id, member_id, deposito_profit_sharing_date, deposito_index_amount, deposito_daily_average_balance, deposito_profit_sharing_amount, deposito_daily_average_balance, deposito_profit_sharing_period, savings_account_id, 1,  deposito_profit_sharing_token, operated_name, created_id, created_on FROM acct_deposito_profit_sharing_temp");
			if($query){
				return true;
			} else {
				return false;
			}
		}

		public function getTotalDepositoProfitSharing($branch_id){
			$this->db->select('SUM(deposito_profit_sharing_amount) AS deposito_profit_sharing_amount');
			$this->db->from('acct_deposito_profit_sharing_temp');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_profit_sharing_amount'];
		}

		public function insertAcctSavingsTransferMutation($data){
			return $query = $this->db->insert('acct_savings_transfer_mutation',$data);
		}

		public function getSavingsTranferMutationID($created_id){
			$this->db->select('acct_savings_transfer_mutation.savings_transfer_mutation_id');
			$this->db->from('acct_savings_transfer_mutation');
			$this->db->where('acct_savings_transfer_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_transfer_mutation.created_on', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['savings_transfer_mutation_id'];
		}

		public function insertAcctSavingsTransferMutationTo($savings_transfer_mutation_id, $branch_id){
			$preference_company = $this->getPreferenceCompany();

			$query = $this->db->query("INSERT INTO acct_savings_transfer_mutation_to (savings_transfer_mutation_id, savings_account_id, savings_id, branch_id, member_id, mutation_id, savings_transfer_mutation_to_amount, savings_account_last_balance) SELECT ".$savings_transfer_mutation_id.", savings_account_id, savings_id, branch_id, member_id, 9, savings_transfer_mutation_amount, savings_account_last_balance FROM acct_deposito_profit_sharing_temp WHERE branch_id = ".$branch_id."");
			if($query){
				return true;
			} else {
				return false;
			}
		}

		public function getAcctDepositoProfitSharingTemp_Deposito($deposito_id, $branch_id){
			$this->db->select('acct_deposito_profit_sharing_temp.*, acct_deposito_account.deposito_account_no, acct_savings_account.savings_account_no, core_member.member_address, acct_deposito.deposito_period, acct_deposito_profit_sharing_temp.deposito_profit_sharing_period');
			$this->db->from('acct_deposito_profit_sharing_temp');
			$this->db->join('core_member', 'acct_deposito_profit_sharing_temp.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing_temp.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_deposito', 'acct_deposito_profit_sharing_temp.deposito_id = acct_deposito.deposito_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing_temp.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('acct_deposito_profit_sharing_temp.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_profit_sharing_temp.branch_id', $branch_id);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSubTotalDepositoProfitSharing($deposito_id, $savings_id, $branch_id){
			$this->db->select('SUM(acct_deposito_profit_sharing_temp.deposito_profit_sharing_amount) AS deposito_profit_sharing_amount');
			$this->db->from('acct_deposito_profit_sharing_temp');
			$this->db->where('acct_deposito_profit_sharing_temp.deposito_id', $deposito_id);
			$this->db->where('acct_deposito_profit_sharing_temp.savings_id', $savings_id);
			$this->db->where('acct_deposito_profit_sharing_temp.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['deposito_profit_sharing_amount'];
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

		// public function getAccountID($deposito_id){
		// 	$this->db->select('acct_deposito.account_id');
		// 	$this->db->from('acct_deposito');
		// 	$this->db->where('acct_deposito.deposito_id', $deposito_id);
		// 	$result = $this->db->get()->row_array();
		// 	return $result['account_id'];
		// }

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


		public function getAcctDepositoProfitSharing_Detail($branch_id, $deposito_account_id, $period){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_profit_sharing_period');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->where('acct_deposito_profit_sharing.deposito_account_id', $deposito_account_id);
			$this->db->where('acct_deposito_profit_sharing.branch_id', $branch_id);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_period', $period);
			$this->db->order_by('acct_deposito_profit_sharing.deposito_profit_sharing_id', 'DESC');
			$this->db->limit(1);
			return $this->db->get()->row_array();

		}
		
		
	
	}
?>