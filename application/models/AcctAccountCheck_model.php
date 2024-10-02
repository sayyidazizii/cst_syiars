<?php
	class AcctAccountCheck_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccountBalanceReport(){
			$this->db->select('acct_account_balance_report.account_id, acct_account_balance_report.account_code, acct_account_balance_report.account_name, acct_account_balance_report.account_default_status');
			$this->db->from('acct_account_balance_report');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccountPeriod(){
			$this->db->select('acct_account_period.account_start_date, acct_account_period.account_end_date, acct_account_period.month_period_opening, acct_account_period.year_period_opening, acct_account_period.month_period_last, acct_account_period.year_period_last');
			$this->db->from('acct_account_period');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getOpeningBalance($account_id, $month_period, $year_period, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->where('acct_account_opening_balance.month_period', $month_period);
			$this->db->where('acct_account_opening_balance.year_period', $year_period);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getLastBalance($account_id, $month_period, $year_period, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->where('acct_account_opening_balance.month_period', $month_period);
			$this->db->where('acct_account_opening_balance.year_period ', $year_period);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getAccountIn($account_id, $start_date, $end_date, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('acct_account_balance_detail.transaction_date >= ', $start_date);			
			$this->db->where('acct_account_balance_detail.transaction_date <= ', $end_date);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_in'];
		}

		public function getAccountOut($account_id, $start_date, $end_date, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_out) AS account_out');
			$this->db->from('acct_account_balance_detail');
			$this->db->where('acct_account_balance_detail.account_id', $account_id);
			$this->db->where('acct_account_balance_detail.transaction_date >=', $start_date);
			$this->db->where('acct_account_balance_detail.transaction_date <=', $end_date);
			$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_out'];
		}

		public function insertAcctAccountCheck($data){
			if($this->db->insert('acct_account_check',$data)){
				
				return true;

			} else {
				return false;
			}

		}

		public function getAcctSavingsNominative(){
			$this->db->select('acct_savings_nominative.branch_id, acct_savings_nominative.savings_id, acct_savings_nominative.account_id, acct_savings_nominative.nominative_start_date, acct_savings_nominative.nominative_end_date, acct_savings_nominative.savings_profit_sharing_period1');
			$this->db->from('acct_savings_nominative');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSavingsNominativeBalanceOther($branch_id, $savings_id, $nominative_start_date, $nominative_end_date, $savings_profit_sharing_period1){
			$query = $this->db->query("SELECT SUM(savings_account_last_balance) AS savings_account_last_balance FROM acct_savings_account
				WHERE savings_account_id NOT IN (SELECT savings_account_id FROM acct_savings_profit_sharing
					WHERE savings_id = '".$savings_id."'
					AND savings_profit_sharing_period1 = '".$savings_profit_sharing_period1."')
					AND '".$nominative_start_date."' <= savings_account_date
					AND savings_account_date <= '".$nominative_end_date."'
					AND branch_id = '".$branch_id."'
					AND savings_id = '".$savings_id."'");
		
			$hasil = $query->row_array();
			return $hasil['savings_account_last_balance'];
		}

		public function updateAcctSavingsNominative($data){
			$this->db->where("acct_savings_nominative.branch_id", $data['branch_id']);
			$this->db->where("acct_savings_nominative.savings_id", $data['savings_id']);
			$this->db->where("acct_savings_nominative.account_id", $data['account_id']);
			$this->db->where("acct_savings_nominative.nominative_start_date", $data['nominative_start_date']);
			$this->db->where("acct_savings_nominative.nominative_end_date", $data['nominative_end_date']);
			$this->db->where("acct_savings_nominative.savings_profit_sharing_period1", $data['savings_profit_sharing_period1']);
			$query = $this->db->update('acct_savings_nominative', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>