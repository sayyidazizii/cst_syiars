<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 600);
	Class AcctSavingsProfitSharingNew extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsProfitSharingNew_model');
			$this->load->model('AcctDailyAverageBalanceCalculate_model');
			$this->load->model('AcctSavingsIndex_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['month']				= $this->configuration->Month();


			$data['main_view']['acctsavings']		= create_double($this->AcctSavingsProfitSharingNew_model->getAcctSavings(), 'savings_id', 'savings_name');
			$data['main_view']['content']			= 'AcctSavingsProfitSharing/ListAcctSavingsProfitSharingNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function recalculate(){
			$auth = $this->session->userdata('auth');


			$periode 	= $this->uri->segment(3);
			$data 		= array (
				'created_id'		=> $auth['user_id'],
				'branch_id'			=> $auth['branch_id'],
				'periode'			=> $periode
			);

			$step5 	= $this->AcctSavingsProfitSharingNew_model->getDataLogStep5($data);

			// print_r($step5);exit;

			if(empty($step5)){
				if($this->AcctSavingsProfitSharingNew_model->deleteDataLog($data)){
					redirect('AcctSavingsProfitSharingNew');
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Proses Hitung Ulang Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsProfitSharingNew/listdata');
				}
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Hitung Ulang Gagal, Basil Sudah Diproses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsProfitSharingNew/listdata');
			}
			
		}
		
		public function processAddAcctSavingsProfitSharing(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('unique');

			// $auth['branch_id'] = 2;

			$data = array (
				'month_period' 	=> $this->input->post('month_period', true),
				'year_period'	=> $this->input->post('year_period', true),
				'srh_minimal'	=> $this->input->post('savings_daily_average_balance_minimum', true),
				'income_amount'	=> $this->input->post('income_amount', true),
			);

			$this->form_validation->set_rules('month_period', 'Periode Bulan', 'required');
			$this->form_validation->set_rules('year_period', 'Periode Tahun', 'required');
			$this->form_validation->set_rules('savings_daily_average_balance_minimum', 'SRH Minimal', 'required');
			$this->form_validation->set_rules('income_amount', 'Jumlah Pendapatan', 'required');

			if($this->form_validation->run()==true){
				$savings_profit_sharing_period 	= $data['month_period'].$data['year_period'];

				$last_date 	= date('t', strtotime($data['month_period']));
				$date 		= $data['year_period'].'-'.$data['month_period'].'-'.$last_date;

				

				//---------------Step 1 Create Table-----------------------------//

				$log_step1 	= array (
					'branch_id'			=> $auth['branch_id'],
					'created_id'		=> $auth['user_id'],
					'created_on'		=> date('Y-m-d'),
					'periode'			=> $savings_profit_sharing_period,
					'step'				=> 1,
				);

				$path 		= 'assets/';
				$table 		= 'table_temp.sql';

				$file 		= file_get_contents($path.$table);

				$data_log_step1 = $this->AcctSavingsProfitSharingNew_model->getDataLogStep1($log_step1);

				if(empty($data_log_step1)){
					$this->AcctSavingsProfitSharingNew_model->insertDataLogStep1($log_step1, $file);		
				} else {
					if($data_log_step1['status_process'] == 0){
						$this->AcctSavingsProfitSharingNew_model->createTable($log_step1, $file);
					}
				}

				// exit;
				//----------------Step 2 Insert SRH----------------------------------//
				$log_step2 = array (
					'branch_id'			=> $auth['branch_id'],
					'created_id'		=> $auth['user_id'],
					'created_on'		=> date('Y-m-d'),
					'periode'			=> $savings_profit_sharing_period,
					'step'				=> 2,
					'total_account'		=> count($dataacctsavingsaccountdetail),
				);

				$data_log_step2 = $this->AcctSavingsProfitSharingNew_model->getDataLogStep2($log_step2);
				
				if(empty($data_log_step2)){

					$acctsavingsaccountforsrh 		= $this->AcctSavingsProfitSharingNew_model->getAcctSavingsAccountfoSRH($auth['branch_id']);

					if(!empty($acctsavingsaccountforsrh)){
						foreach ($acctsavingsaccountforsrh as $key => $val) {
							// if($val['savings_account_daily_average_balance'] == 0){
								$yesterday_transaction_date = $this->AcctSavingsProfitSharingNew_model->getYesterdayTransactionDate($val['savings_account_id']);

								$last_balance_SRH 			= $this->AcctSavingsProfitSharingNew_model->getLastBalanceSRH($val['savings_account_id']);

								if(empty($last_balance_SRH)){
									$last_balance_SRH = 0;
								}

								$last_date 	= date('t', strtotime($data['month_period']));
								$date 		= $data['year_period'].'-'.$data['month_period'].'-'.$last_date;

								$date1 		= date_create($date);
								$date2 		= date_create($yesterday_transaction_date);

								// $range_date = date_diff($date1, $date2)->format('%d');
								$interval     = $date1->diff($date2);
                                $range_date   = $interval->days;

								if($range_date == 0){
									$range_date = 1;
								}

								$daily_average_balance = ($last_balance_SRH * $range_date) / $last_date;

								$dataacctsavingsaccountdetail[] = array (
									'savings_account_id'				=> $val['savings_account_id'],
									'branch_id'							=> $val['branch_id'],
									'savings_id'						=> $val['savings_id'],
									'member_id'							=> $val['member_id'],
									'today_transaction_date'			=> $date,
									'yesterday_transaction_date'		=> $yesterday_transaction_date,
									'transaction_code'					=> 'Penutupan Akhir Bulan',
									'opening_balance'					=> $last_balance_SRH,
									'last_balance'						=> $last_balance_SRH,
									'daily_average_balance'				=> $daily_average_balance,
									'operated_name'						=> 'SYSTEM',
								);

								$daily_average_balance_total = $this->AcctSavingsProfitSharingNew_model->getDailyAverageBalanceTotal($val['savings_account_id'], $data['month_period'], $data['year_period']);

								$data_savings[] = array (
									'savings_account_id'					=> $val['savings_account_id'],
									'branch_id'								=> $val['branch_id'],
									'savings_id'							=> $val['savings_id'],
									'savings_account_daily_average_balance' => $daily_average_balance_total + $daily_average_balance,
								);
							// }


							
						}
					}

					$log_step2 = array (
						'branch_id'			=> $auth['branch_id'],
						'created_id'		=> $auth['user_id'],
						'created_on'		=> date('Y-m-d'),
						'periode'			=> $savings_profit_sharing_period,
						'step'				=> 2,
						'total_account'		=> count($dataacctsavingsaccountdetail),
					);

					if($this->AcctSavingsProfitSharingNew_model->insertDataLogStep($log_step2)){
						if($this->AcctSavingsProfitSharingNew_model->insertAcctSavingsAccountDetail($dataacctsavingsaccountdetail, $log_step2)){
							$this->AcctSavingsProfitSharingNew_model->insertAcctSavingsAccountTemp($data_savings);
						}
					}
				}

				// exit;

				//----------------Step 3 Insert Index----------------------------------//

				// $log_step3 = array (
				// 	'branch_id'			=> $auth['branch_id'],
				// 	'created_id'		=> $auth['user_id'],
				// 	'created_on'		=> date('Y-m-d'),
				// 	'periode'			=> $savings_profit_sharing_period,
				// 	'step'				=> 3,
				// 	'total_account'		=> count($dataacctsavingsprofitsharing),
				// );
				
				// $data_log_step3 = $this->AcctSavingsProfitSharingNew_model->getDataLogStep3($log_step3);	

				// if(empty($data_log_step3)){	
				// 	if($this->AcctSavingsProfitSharingNew_model->insertDataLogStep($log_step3)){
				// 		$preferencecompany 			= $this->AcctSavingsProfitSharingNew_model->getPreferenceCompany();

				// 		if($auth['branch_id'] == 2){
				// 			$daily_average_balance_accumulation = $this->AcctSavingsProfitSharingNew_model->getDailyAverageBalanceAccumulation($auth['branch_id']);

				// 			$savings_last_balance_accumulation 	= $this->AcctSavingsProfitSharingNew_model->getSavingsLastBalanceAccumulation($data['month_period'], $data['year_period'], $auth['branch_id']);

				// 			$deposito_last_balance_accumulation	= $this->AcctSavingsProfitSharingNew_model->getDepositoLastBalanceAccumulation($auth['branch_id']);

				// 			$total_accumulation = $daily_average_balance_accumulation + $deposito_last_balance_accumulation;

				// 			$acctsavings 		= $this->AcctSavingsIndex_model->getAcctSavings();

				// 			$acctdeposito		= $this->AcctSavingsIndex_model->getAcctDeposito(); 

				// 			foreach ($acctsavings as $keyS => $valS) {
				// 				$bmt_percentage 		= 100 - $valS['savings_nisbah'];
				// 				$daily_avreage_balance 	= $this->AcctSavingsProfitSharingNew_model->getDailyAverageBalance_Savings($valS['savings_id'], $auth['branch_id']);

				// 				$portion_per_savings 	= ($daily_avreage_balance / $total_accumulation) * $data['income_amount'];

				// 				if($portion_per_savings == 0 || empty($portion_per_savings)){
				// 					$savings_index_amount 	= 0;
				// 				} else {
				// 					$savings_index_amount 	= (($portion_per_savings * $valS['savings_nisbah']) / 100 ) / $daily_avreage_balance;
				// 				}


				// 				$savings_member_portion = ($portion_per_savings * $valS['savings_nisbah']) / 100;
				// 				$savings_bmt_portion 	= ($portion_per_savings * $bmt_percentage) / 100;

				// 				$dataacctsavingsindex = array (
				// 					'savings_id'								=> $valS['savings_id'],
				// 					'branch_id'									=> $auth['branch_id'],
				// 					'income_amount'								=> $data['income_amount'],
				// 					'daily_average_balance_accumulation' 		=> $total_accumulation,
				// 					'savings_account_last_balance_accumulation' => $savings_last_balance_accumulation,
				// 					'savings_index_amount'						=> $savings_index_amount,
				// 					'savings_index_period'						=> $savings_profit_sharing_period,
				// 					'savings_nisbah'							=> $valS['savings_nisbah'],
				// 					'savings_portion_total'						=> $portion_per_savings,
				// 					'savings_member_portion'					=> $savings_member_portion,
				// 					'savings_bmt_portion'						=> $savings_bmt_portion,
				// 				);

				// 				$this->AcctSavingsIndex_model->insertAcctSavingsIndex($dataacctsavingsindex);
				// 			}


				// 			foreach ($acctdeposito as $keyD => $valD) {
				// 				$bmt_percentage 			= 100 - $valD['deposito_interest_rate'];
				// 				$daily_avreage_balance 		= $this->AcctSavingsProfitSharingNew_model->getDepositoLastBalance_Deposito($valD['deposito_id'], $auth['branch_id']);

				// 				$portion_per_deposito 		= ($daily_avreage_balance / $total_accumulation) * $data['income_amount'];

				// 				if($portion_per_deposito == 0 || empty($portion_per_deposito)){
				// 					$deposito_index_amount 		= 0;
				// 				} else {
				// 					$deposito_index_amount 		= (($portion_per_deposito * $valD['deposito_interest_rate']) / 100 ) / $daily_avreage_balance;
				// 				}

								
				// 				$deposito_member_portion 	= ($portion_per_deposito * $valD['deposito_interest_rate']) / 100;
				// 				$deposito_bmt_portion 		= ($portion_per_deposito * $bmt_percentage) / 100;

				// 				$dataacctdepositoindex = array (
				// 					'deposito_id'									=> $valD['deposito_id'],
				// 					'branch_id'										=> $auth['branch_id'],
				// 					'income_amount'									=> $data['income_amount'],
				// 					'daily_average_balance_accumulation' 			=> $total_accumulation,
				// 					'deposito_account_last_balance_accumulation'	=> $deposito_last_balance_accumulation,
				// 					'deposito_index_amount'							=> $deposito_index_amount,
				// 					'deposito_index_period'							=> $savings_profit_sharing_period,
				// 					'deposito_nisbah'								=> $valD['deposito_interest_rate'],
				// 					'deposito_portion_total'						=> $portion_per_deposito,
				// 					'deposito_member_portion'						=> $deposito_member_portion,
				// 					'deposito_bmt_portion'							=> $deposito_bmt_portion,
				// 				);


				// 				$this->AcctSavingsIndex_model->insertAcctDepositoIndex($dataacctdepositoindex);

				// 			}
				// 		}
				// 	}
				// }

				//----------------Step 4 Insert Basil----------------------------------//
				$log_step4 = array (
					'branch_id'			=> $auth['branch_id'],
					'created_id'		=> $auth['user_id'],
					'created_on'		=> date('Y-m-d'),
					'periode'			=> $savings_profit_sharing_period,
					'step'				=> 4,
					'total_account'		=> count($dataacctsavingsprofitsharing),
				);

				$last_date 	= date('t', strtotime($data['month_period']));
				$date 		= $data['year_period'].'-'.$data['month_period'].'-'.$last_date;

				$data_log_step4 = $this->AcctSavingsProfitSharingNew_model->getDataLogStep4($log_step4);	

				if(empty($data_log_step4)){	
					$savings_daily_average_balance_minimum 	= $data['srh_minimal'];
					$acctsavingsaccount 					= $this->AcctSavingsProfitSharingNew_model->getAcctSavingsAccountforBasil($auth['branch_id'], $savings_daily_average_balance_minimum);
					$profitsharing 							= $this->AcctSavingsProfitSharingNew_model->getPreferenceProfitSharing();

					$no = 1;
					foreach ($acctsavingsaccount as $k => $v) {
						// $savings_index_amount 					= $this->AcctSavingsProfitSharingNew_model->getSavingsIndexAmount($v['savings_id'], $savings_profit_sharing_period);

						$savings_account_daily_average_balance 	= $this->AcctSavingsProfitSharingNew_model->getSavingsAccountDailyAverage($v['savings_account_id']);

						$savings_profit_sharing_amount 			= $savings_account_daily_average_balance * ($v['savings_index_amount'] / 100);

						$savings_profit_sharing_allocation 		= $savings_profit_sharing_amount * ($profitsharing['profit_sharing_allocation_percentage'] / 100);

						$savings_profit_sharing_amount_final 	= $savings_profit_sharing_amount - $savings_profit_sharing_allocation;

						$savings_account_last_balance 			= $v['savings_account_last_balance'] + $savings_profit_sharing_amount_final;

						$dataacctsavingsprofitsharing[] = array (
							'savings_account_id'							=> $v['savings_account_id'],
							'branch_id'										=> $v['branch_id'],
							'savings_id'									=> $v['savings_id'],
							'member_id'										=> $v['member_id'],
							'savings_profit_sharing_temp_date'				=> $date,
							'savings_index_amount'							=> $v['savings_index_amount'],
							'savings_daily_average_balance_minimum'			=> $savings_daily_average_balance_minimum,
							'savings_daily_average_balance'					=> $savings_account_daily_average_balance,
							'savings_profit_sharing_temp_total'				=> $savings_profit_sharing_amount,
							'savings_profit_sharing_temp_amount'			=> $savings_profit_sharing_amount_final,
							'savings_profit_sharing_temp_allocation_amount'	=> $savings_profit_sharing_allocation,
							'savings_profit_sharing_temp_period'			=> $savings_profit_sharing_period,
							'savings_account_last_balance'					=> $savings_account_last_balance,
							'savings_profit_sharing_temp_token'				=> $savings_profit_sharing_period.$v['savings_account_id'],
							'operated_name'									=> 'SYSTEM',
							'created_id'									=> $auth['user_id'],
							'created_on'									=> date('Y-m-d H:i:s'),
						);

						$no++;
					}

					$log_step4 = array (
						'branch_id'			=> $auth['branch_id'],
						'created_id'		=> $auth['user_id'],
						'created_on'		=> date('Y-m-d'),
						'periode'			=> $savings_profit_sharing_period,
						'step'				=> 4,
						'total_account'		=> count($dataacctsavingsprofitsharing),
					);	

			
					if($this->AcctSavingsProfitSharingNew_model->insertDataLogStep($log_step4)){
						$this->AcctSavingsProfitSharingNew_model->insertAcctSavingsProfitSharingTemp($dataacctsavingsprofitsharing, $log_step4);
					}
				}


					redirect('AcctSavingsProfitSharingNew/listdata');
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsProfitSharingNew');
			}			
		}

		public function listdata(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['monthname']							= $this->configuration->Month();


			$data['main_view']['acctsavingsprofitsharingtemp']		= $this->AcctSavingsProfitSharingNew_model->getAcctSavingsProfitSharingTemp($auth['branch_id']);

			$data['main_view']['content']							= 'AcctSavingsProfitSharing/ListDataAcctSavingsProfitSharingNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processUpdateAcctSavingsProfitSharing(){
			$auth = $this->session->userdata('auth');

			// $auth['branch_id'] = 2;

			$periode 	= $this->AcctSavingsProfitSharingNew_model->getPeriode($auth);

			$log_step5 	= array (
				'branch_id'			=> $auth['branch_id'],
				'created_id'		=> $auth['user_id'],
				'created_on'		=> date('Y-m-d'),
				'periode'			=> $periode,
				'step'				=> 5,
			);

			$data_log_step5 = $this->AcctSavingsProfitSharingNew_model->getDataLogStep5($log_step5);

			$dataperiod = array (
				'period'		=> $periode,
				'created_id'	=> $auth['user_id'],
				'created_on'	=> date('Y-m-d'),
			);

			// $month 			= date('m', strtotime($periode));	
			// $year 			= date('Y', strtotime($periode));
			// $lastdate 		= date('t', strtotime($periode));
			// $date 			= $year.'-'.$month.'-'.$lastdate;

			// print_r($date);exit;

			if(empty($data_log_step5)){	
				if($this->AcctSavingsProfitSharingNew_model->insertDataLogStep($log_step5)){

					$this->AcctSavingsProfitSharingNew_model->insertSystemPeriodLog($dataperiod);
					if($this->AcctSavingsProfitSharingNew_model->insertAcctSavingsProfitSharingFix()){

						$corebranch = $this->AcctSavingsProfitSharingNew_model->getCoreBranch();

						foreach ($corebranch as $key => $vCB) {
							$savings_profit_sharing_amount = $this->AcctSavingsProfitSharingNew_model->getTotalSavingsProfitSharing($vCB['branch_id']);
							$data_transfer = array (
								'branch_id'									=> $vCB['branch_id'],
								'savings_transfer_mutation_date'			=> date('Y-m-d'),
								'savings_transfer_mutation_amount'			=> $savings_profit_sharing_amount,
								'operated_name'								=> 'SYSTEM',
								'created_id'								=> $auth['user_id'],
								'created_on'								=> date('Y-m-d H:i:s'),
							);

							if($this->AcctSavingsProfitSharingNew_model->insertAcctSavingsTransferMutation($data_transfer)){
								$savings_transfer_mutation_id = $this->AcctSavingsProfitSharingNew_model->getSavingsTranferMutationID($data_transfer['created_id']);

								$this->AcctSavingsProfitSharingNew_model->insertAcctSavingsTransferMutationTo($savings_transfer_mutation_id, $vCB['branch_id']);
							}

							$acctsavings 	= $this->AcctSavingsProfitSharingNew_model->getAcctSavings();


							//-------------------------------------Jurnal--------------------------------------------------------//
							foreach ($acctsavings as $key => $val) {
								$totalsavingsprofitsharing 				= $this->AcctSavingsProfitSharingNew_model->getSubTotalSavingsProfitSharing($val['savings_id'], $vCB['branch_id']);

								$totalsavingsprofitsharingallocation 	= $this->AcctSavingsProfitSharingNew_model->getSubTotalSavingsProfitSharingAllocation($val['savings_id'], $vCB['branch_id']);

								$preferenceprofitsharingallocation 		= $this->AcctSavingsProfitSharingNew_model->getPreferenceProfitSharingAllocation();
								$preferenceprofitsharing 				= $this->AcctSavingsProfitSharingNew_model->getPreferenceProfitSharing();
						
								$transaction_module_code 	= "BS";

								$transaction_module_id 		= $this->AcctSavingsProfitSharingNew_model->getTransactionModuleID($transaction_module_code);

								
									
								$journal_voucher_period 	= $periode;
								
								$data_journal 				= array(
									'branch_id'						=> $vCB['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'BAGI HASIL SIMPANAN '.$val['savings_name'].' PERIODE '.$journal_voucher_period,
									'journal_voucher_description'	=> 'BAGI HASIL SIMPANAN '.$val['savings_name'].' PERIODE '.$journal_voucher_period,
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->AcctSavingsProfitSharingNew_model->insertAcctJournalVoucher($data_journal);

								$journal_voucher_id 			= $this->AcctSavingsProfitSharingNew_model->getJournalVoucherID($data_journal['created_id']);

								$account_basil_id 				= $this->AcctSavingsProfitSharingNew_model->getAccountBasilID($val['savings_id']);

								$account_id_default_status 		= $this->AcctSavingsProfitSharingNew_model->getAccountIDDefaultStatus($account_basil_id);

								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_basil_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $totalsavingsprofitsharing + $totalsavingsprofitsharingallocation,
									'journal_voucher_debit_amount'	=> $totalsavingsprofitsharing + $totalsavingsprofitsharingallocation,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsProfitSharingNew_model->insertAcctJournalVoucherItem($data_debet);

								$account_id 					= $this->AcctSavingsProfitSharingNew_model->getAccountID($val['savings_id']);

								$account_id_default_status 		= $this->AcctSavingsProfitSharingNew_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $totalsavingsprofitsharing,
									'journal_voucher_credit_amount'	=> $totalsavingsprofitsharing,
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsProfitSharingNew_model->insertAcctJournalVoucherItem($data_credit);

								if(!empty($preferenceprofitsharingallocation)){
									foreach ($preferenceprofitsharingallocation as $kA => $vA){

										$account_id_default_status 		= $this->AcctSavingsProfitSharingNew_model->getAccountIDDefaultStatus($vA['account_id']);
	
										$allocation_amount 				= $totalsavingsprofitsharingallocation * ($vA['allocation_amount'] / $preferenceprofitsharing['profit_sharing_allocation_percentage']);
	
										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $vA['account_id'],
											'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
											'journal_voucher_amount'		=> $allocation_amount,
											'journal_voucher_credit_amount'	=> $allocation_amount,
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
										);
	
										$this->AcctSavingsProfitSharingNew_model->insertAcctJournalVoucherItem($data_credit);
									}
								}
								
							}
						}
						

						
					}
					$msg = "<div class='alert alert-success alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Basil Selesai Diproses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsProfitSharingNew');
				}
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Basil Sudah Selesai Diproses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsProfitSharingNew');
			}
		}

		public function viewindex(){
			$auth 		= $this->session->userdata('auth');
			$periode 	= $this->uri->segment(3);
		

			$acctsavingsindex	= $this->AcctSavingsIndex_model->getAcctSavingsIndexMAX($auth['branch_id'], $periode);
			$acctdepositoindex	= $this->AcctSavingsIndex_model->getAcctDepositoIndexMAX($auth['branch_id'], $periode);
			$total 				= $this->AcctSavingsIndex_model->getTotal($auth['branch_id'], $periode);
			

			foreach ($acctsavingsindex as $key => $val) {
				$datasavingsindex = $this->AcctSavingsIndex_model->getAcctSavingsIndex($val['savings_index_id']);
				$datasavings[] = array (
					'savings_name'					=> $datasavingsindex['savings_name'],
					'savings_nisbah'				=> $datasavingsindex['savings_nisbah'],
					'savings_index'					=> $datasavingsindex['savings_index_amount'],
					'savings_member_portion'		=> $datasavingsindex['savings_member_portion'],
					'savings_bmt_portion'			=> $datasavingsindex['savings_bmt_portion'],
				);
			}

			foreach ($acctdepositoindex as $key => $val) {
				$datadepositoindex = $this->AcctSavingsIndex_model->getAcctDepositoIndex($val['deposito_index_id']);
				$datadeposito[] = array (
					'deposito_name'					=> $datadepositoindex['deposito_name'],
					'deposito_nisbah'				=> $datadepositoindex['deposito_nisbah'],
					'deposito_index'				=> $datadepositoindex['deposito_index_amount'],
					'deposito_member_portion'		=> $datadepositoindex['deposito_member_portion'],
					'deposito_bmt_portion'			=> $datadepositoindex['deposito_bmt_portion'],
				);
			}





			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"80%\"><div style=\"text-align: left;font-size: 16px\">KSPPS MADANI JAWA TIMUR </div></td>
				    </tr>
				    <tr>
						<td width=\"80%\"><div style=\"text-align: left;font-size: 14px\">HASIL PERHITUNGAN SRH DAN INDEX </div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">TOTAL SALDO SIMP & BERJANGKA </div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_savings_deposito'], 2)."</div></td>
				    </tr>
				    <tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">TOTAL SALDO RATA2 HARIAN </div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_srh'], 2)."</div></td>
				    </tr>
				    <tr>
						<td width=\"30%\"><div style=\"text-align: left;font-size: 12px\">JML PENDAPATAN BULAN INI</div></td>
						<td width=\"60%\"><div style=\"text-align: left;font-size: 12px\">:  ".number_format($total['total_income'], 2)."</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					<tr>
						<td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Nama Simp.</div></td>
						<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Nisbah</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Index</div></td>
				        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Porsi Nasabah (+/-)</div></td>
				        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;\">Porsi BMT (+/-)</div></td>
				    </tr>
				</table>
			";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			$tbl1a = "
				<tr>
					<td colspan=\"5\">( Beban Bulan ini )</td>
				</tr>
			";

			foreach ($datasavings as $key => $val) {
				
				$tbl1a .= "
					<tr>
						<td width=\"25%\"><div style=\"text-align: left;\">&nbsp; ".$val['savings_name']."</div></td>
						<td width=\"10%\"><div style=\"text-align: right;\">".$val['savings_nisbah']." &nbsp;</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$val['savings_index']." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_member_portion'], 2)." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['savings_bmt_portion'], 2)." &nbsp;</div></td>
				    </tr>
				";

				$totalmembersavings += $val['savings_member_portion'];
				$totalbmtsavings 	+= $val['savings_bmt_portion'];
			}

			$tbl1b = "
				<tr>
					<td colspan=\"5\"></td>
				</tr>
				<tr>
					<td colspan=\"5\">( Beban Bulan depan )</td>
				</tr>
			";

			foreach ($datadeposito as $key => $val) {
				
				$tbl1b .= "
					<tr>
						<td width=\"25%\"><div style=\"text-align: left;\">&nbsp; ".$val['deposito_name']."</div></td>
						<td width=\"10%\"><div style=\"text-align: right;\">".$val['deposito_nisbah']." &nbsp;</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$val['deposito_index']." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['deposito_member_portion'], 2)." &nbsp;</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['deposito_bmt_portion'], 2)." &nbsp;</div></td>
				    </tr>
				";
				$totalmemberdeposito += $val['deposito_member_portion'];
				$totalbmtdeposito 	+= $val['deposito_bmt_portion'];
			}

			$totalmember = $totalmembersavings + $totalmemberdeposito;
			$totalbmt = $totalbmtdeposito + $totalbmtsavings;

			$tbl2 = "
					<tr>
						<td colspan =\"2\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmember, 2)."</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalbmt, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl.$tbl1a.$tbl1b.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Hasil perhitungan SRH dan Index.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
			
		}

		public function SyncronizeData(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctsavingsmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			if(!empty($sesi['savings_account_id'])){
				$datalog = array (
					'savings_syncronize_log_date' 		=> date('Y-m-d'),
					'savings_syncronize_log_start_date'	=> $sesi['start_date'],
					'savings_syncronize_log_end_date'	=> $sesi['end_date'],
					'savings_account_id'				=> $sesi['savings_account_id'],
					'branch_id'							=> $auth['branch_id'],
					'created_id'						=> $auth['user_id'],
					'created_on'						=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsPrintSavingsMonitor_model->insertAcctSavingsSyncronizeLog($datalog)){
					$opening_balance 			= $this->AcctSavingsPrintSavingsMonitor_model->getOpeningBalance($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date']);

					if(!is_array($opening_balance)){
						$opening_date 			= $this->AcctSavingsPrintSavingsMonitor_model->getLastDate($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date']);
						$opening_balance 		= $this->AcctSavingsPrintSavingsMonitor_model->getLastBalance($datalog['savings_account_id'], $opening_date);
					}

					$acctsavingsaccountdetail 	= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccountDetail($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date'], $datalog['savings_syncronize_log_end_date']);

					foreach ($acctsavingsaccountdetail as $key => $val) {
						$last_balance = ($opening_balance + $val['mutation_in']) - $val['mutation_out'];

						$newdata = array (
							'savings_account_detail_id'		=> $val['savings_account_detail_id'],
							'savings_account_id'			=> $val['savings_account_id'],
							'opening_balance'				=> $opening_balance,
							'last_balance'					=> $last_balance,
						);

						$opening_balance = $last_balance;

						if($this->AcctSavingsPrintSavingsMonitor_model->updateAcctSavingsAccountDetail($newdata)){
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
											Syncronize Data Sukses
										</div> ";
							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Syncronize Data Gagal
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation');
							break;
						}

						print_r($newdata);
						print_r("<BR>");
					}
					// exit;
					redirect('AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation');

				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Syncronize Data Gagal
							</div> ";
					$sesi = $this->session->userdata('unique');
					redirect('AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation');
				}

			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							No. Rekening Simpanan Masih Kosong
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation');
			}
		}

		public function cekSRH(){
			$data['month_period']		= '07';
			$savings_account_id 		= '32982';
			$acctsavingsaccountdetail 	= $this->AcctSavingsProfitSharingNew_model->getAcctSavingsAccountDetail($data['month_period'], $savings_account_id);

			foreach ($acctsavingsaccountdetail as $key => $val) {
				$last_date 					= date('t', strtotime($data['month_period']));
				$date 						= $val['today_transaction_date'];
				$yesterday_transaction_date = $val['yesterday_transaction_date'];
				$date1 						= date_create($date);
				$date2 						= date_create($yesterday_transaction_date);


				$interval     = $date1->diff($date2);
				$range_date   = $interval->days;

				if($range_date == 0){
					$range_date = 1;
				}

				$last_balance_SRH 			= $val['opening_balance'];
				$daily_average_balance 		= ($last_balance_SRH * $range_date) / $last_date;

				$detail[] = array (
					'today_transaction_date'		=> $val['today_transaction_date'],
					'yesterday_transaction_date'	=> $val['yesterday_transaction_date'],
					'range_date'					=> $range_date,
					'opening_balance'				=> $val['opening_balance'],
					'last_balance'					=> $val['last_balance'],
					'daily_average_balance_old'		=> $val['daily_average_balance'],
					'daily_average_balance_new'		=> $daily_average_balance,
				);
			}

			$data['main_view']['hitungsrh']			= $detail;
			$data['main_view']['content']			= 'AcctSavingsProfitSharing/ListTestSRH_view';
			$this->load->view('MainPage_view',$data);
		}
	}	
?>