<?php
	ini_set('memory_limit', '512M');
	ini_set('max_execution_time', 12000);

	Class AcctAccountCheck extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('AcctAccountCheck_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$acctsavingsnominative 				= $this->AcctAccountCheck_model->getAcctSavingsNominative();

			/* print_r("acctsavingsnominative ");
			print_r($acctsavingsnominative);
			print_r("<BR> ");
			print_r("<BR> ");
			exit; */

			foreach ($acctsavingsnominative as $keyNominative => $valNominative){
				
				$branch_id 						= $valNominative['branch_id'];
				$savings_id 					= $valNominative['savings_id'];
				$account_id 					= $valNominative['account_id'];
				$nominative_start_date 			= $valNominative['nominative_start_date'];
				$nominative_end_date			= $valNominative['nominative_end_date'];

				$savings_profit_sharing_period1	= $valNominative['savings_profit_sharing_period1'];	

				$savings_nominative_balance_other	= $this->AcctAccountCheck_model->getSavingsNominativeBalanceOther($branch_id, $savings_id, $nominative_start_date, $nominative_end_date, $savings_profit_sharing_period1);

				if (empty($savings_nominative_balance_other)){
					$savings_nominative_balance_other = 0;
				}

				$data = array (
					'branch_id'							=> $branch_id, 
					'savings_id'						=> $savings_id, 
					'account_id'						=> $account_id, 
					'nominative_start_date'				=> $nominative_start_date, 
					'nominative_end_date'				=> $nominative_end_date,
					'savings_profit_sharing_period1'	=> $savings_profit_sharing_period1,
					'savings_nominative_balance_other'	=> $savings_nominative_balance_other,
				);

				$this->AcctAccountCheck_model->updateAcctSavingsNominative($data);
			}

			


			$data['main_view']['corebranch']		= create_double($this->AcctAccountCheck_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctAccountCheck/ListAcctAccountCheck_view';
			$this->load->view('MainPage_view',$data);
		}


		public function processCalculateAcctAccountCheck(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$corebranch 				= $this->AcctAccountCheck_model->getCoreBranch();
			$acctaccountbalancereport 	= $this->AcctAccountCheck_model->getAcctAccountBalanceReport();
			$acctaccountperiod 			= $this->AcctAccountCheck_model->getAcctAccountPeriod();

			/* print_r("corebranch ");
			print_r($corebranch);
			print_r("<BR> ");
			print_r("<BR> ");

			print_r("acctaccountbalancereport ");
			print_r($acctaccountbalancereport);
			print_r("<BR> ");
			print_r("<BR> ");

			print_r("acctaccountperiod ");
			print_r($acctaccountperiod);
			print_r("<BR> ");
			print_r("<BR> ");

			exit; */

			foreach ($corebranch as $keyBranch => $valBranch){
				foreach ($acctaccountperiod as $keyPeriod => $valPeriod){
					foreach ($acctaccountbalancereport as $keyBalance => $valBalance){
						$opening_balance 				= $this->AcctAccountCheck_model->getOpeningBalance($valBalance['account_id'], $valPeriod['month_period_opening'], $valPeriod['year_period_opening'], $valBranch['branch_id']);

						$account_in 					= $this->AcctAccountCheck_model->getAccountIn($valBalance['account_id'], $valPeriod['account_start_date'], $valPeriod['account_end_date'], $valBranch['branch_id']);

						$account_out 					= $this->AcctAccountCheck_model->getAccountOut($valBalance['account_id'], $valPeriod['account_start_date'], $valPeriod['account_end_date'], $valBranch['branch_id']);

						$last_balance 					= $this->AcctAccountCheck_model->getLastBalance($valBalance['account_id'], $valPeriod['month_period_last'], $valPeriod['year_period_last'], $valBranch['branch_id']);

						$index 							= $valBranch['branch_id'].$valPeriod['month_period_opening'].$valPeriod['year_period_opening'].$valPeriod['month_period_last'].$valPeriod['year_period_last'].$valBalance['account_id'];

						$account_difference				= ABS($account_in - $account_out);

						$data = array(
							'account_id'				=> $valBalance['account_id'],
							'branch_id'					=> $valBranch['branch_id'],
							'account_code'				=> $valBalance['account_code'],
							'account_name'				=> $valBalance['account_name'],
							'account_default_status'	=> $valBalance['account_default_status'],
							'month_period_opening'		=> $valPeriod['month_period_opening'],
							'year_period_opening'		=> $valPeriod['year_period_opening'],
							'month_period_last'			=> $valPeriod['month_period_last'],
							'year_period_last'			=> $valPeriod['year_period_last'],
							'opening_balance'			=> $opening_balance,
							'account_in'				=> $account_in,
							'account_out'				=> $account_out,
							'account_difference'		=> $account_difference,
							'last_balance'				=> $last_balance,
						);

						$this->AcctAccountCheck_model->insertAcctAccountCheck($data);
					}		
				}
			}

			


			$data['main_view']['corebranch']		= create_double($this->AcctAccountCheck_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctAccountCheck/ListAcctAccountCheck_view';
			$this->load->view('MainPage_view',$data);
		}

	}
		
?>