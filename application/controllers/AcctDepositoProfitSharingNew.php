<?php
	ini_set('memory_limit', '256M');

	Class AcctDepositoProfitSharingNew extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoProfitSharingNew_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			// $this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){

			$data['main_view']['content']			= 'AcctDepositoProfitSharingNew/ListAcctDepositoProfitSharingNew_view';
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

			$step3 	= $this->AcctDepositoProfitSharingNew_model->getDataLogStep3($data);

			// print_r($step5);exit;

			if(empty($step3)){
				if($this->AcctDepositoProfitSharingNew_model->deleteDataLog($data)){
					redirect('AcctDepositoProfitSharingNew');
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Proses Hitung Ulang Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctDepositoProfitSharingNew/listdata');
				}
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Hitung Ulang Gagal, Basil Simp. Berjangka Sudah Diproses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingNew/listdata');
			}
			
		}
		
		public function processAddAcctDepositoProfitSharingNew(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'last_date'								=> tgltodb($this->input->post('last_date', true)),
			);

			$this->form_validation->set_rules('last_date', 'Tanggal', 'required');

			if($this->form_validation->run()==true){
				$deposito_profit_sharing_period 		= date('mY', strtotime($data['last_date']));
				$fisrt_date 							= date('Y-m-01');


				//---------------Step 1 Create Table-----------------------------//

				$log_step1 	= array (
					'branch_id'			=> $auth['branch_id'],
					'created_id'		=> $auth['user_id'],
					'created_on'		=> date('Y-m-d'),
					'periode'			=> $deposito_profit_sharing_period,
					'step'				=> 1,
				);

				// print_r($log_step1);exit;

				$path 		= 'assets/';
				$table 		= 'table_temp_deposito.sql';

				$file 		= file_get_contents($path.$table);

				$data_log_step1 = $this->AcctDepositoProfitSharingNew_model->getDataLogStep1($log_step1);

				if(empty($data_log_step1)){
					$this->AcctDepositoProfitSharingNew_model->insertDataLogStep1($log_step1, $file);		
				} else {
					if($data_log_step1['status_process'] == 0){
						$this->AcctDepositoProfitSharingNew_model->createTable($log_step1, $file);
					}
				}

				//---------------Step 2 Hitung dan Simpan Basil Deposito-----------------------------//

				$log_step2 	= array (
					'branch_id'			=> $auth['branch_id'],
					'created_id'		=> $auth['user_id'],
					'created_on'		=> date('Y-m-d'),
					'periode'			=> $deposito_profit_sharing_period,
					'step'				=> 2,
				);

				$preferencecompany 		= $this->AcctDepositoProfitSharingNew_model->getPreferenceCompany();

				$profitsharing 	 		= $this->AcctDepositoProfitSharingNew_model->getPreferenceProfitSharing();

				$acctdeposito 			= $this->AcctDepositoProfitSharingNew_model->getAcctDeposito();

				$acctdepositoaccount 	= $this->AcctDepositoProfitSharingNew_model->getAcctDepositoAccount($auth['branch_id'], $fisrt_date);

				$data_log_step2 		= $this->AcctDepositoProfitSharingNew_model->getDataLogStep2($log_step2);


				if(empty($data_log_step2)){
					foreach ($acctdepositoaccount as $k => $v) {
						// $deposito_index_amount 			= $this->AcctDepositoProfitSharingNew_model->getDepositoIndexAmount($v['deposito_id'], $deposito_profit_sharing_period);

						if($v['deposito_period'] >= $profitsharing['deposito_period_start_1'] && $v['deposito_period'] <= $profitsharing['deposito_period_end_1']){

							$deposito_index_amount 		= ($profitsharing['deposito_index_amount_1'] / 100) / 12;

						} else if($v['deposito_period'] >= $profitsharing['deposito_period_start_2'] && $v['deposito_period'] <= $profitsharing['deposito_period_end_2']){

							$deposito_index_amount 		= ($profitsharing['deposito_index_amount_2'] / 100) / 12;

						} else if($v['deposito_period'] > $profitsharing['deposito_period_start_3']){

							$deposito_index_amount 		= ($profitsharing['deposito_index_amount_3'] / 100) / 12;
						}

						if($v['savings_account_id'] == 0){
							$datasavingsaccount 		= $this->AcctDepositoProfitSharingNew_model->getSavingsAccount($v['member_id']);
						} else {
							$datasavingsaccount 		= $this->AcctDepositoProfitSharingNew_model->getSavingsAccountDetail($v['savings_account_id']);
						}

						if(empty($datasavingsaccount)){
							$datasavingsaccount['savings_id']					= 0;
							$datasavingsaccount['savings_account_id']			= 0;
							$datasavingsaccount['savings_account_last_balance']	= 0;
						}

						$deposito_profit_sharing_amount 	= $v['deposito_account_amount'] * $deposito_index_amount;

						$data_detail[] = array (
							'deposito_account_id'							=> $v['deposito_account_id'],
							'branch_id'										=> $v['branch_id'],
							'deposito_id'									=> $v['deposito_id'],
							'member_id'										=> $v['member_id'],
							'member_name'									=> $v['member_name'],
							'deposito_profit_sharing_date'					=> $data['last_date'],
							'deposito_index_amount'							=> $deposito_index_amount,
							'deposito_daily_average_balance'				=> $v['deposito_account_amount'],
							'deposito_profit_sharing_amount'				=> $deposito_profit_sharing_amount,
							'deposito_profit_sharing_period'				=> $deposito_profit_sharing_period,
							'deposito_profit_sharing_token'					=> $deposito_profit_sharing_period.$v['deposito_account_id'],
							'savings_account_id'							=> $datasavingsaccount['savings_account_id'],
							'savings_id'									=> $datasavingsaccount['savings_id'],
							'savings_account_opening_balance'				=> $datasavingsaccount['savings_account_last_balance'],
							'savings_transfer_mutation_amount'				=> $deposito_profit_sharing_amount,
							'savings_account_last_balance'					=> $datasavingsaccount['savings_account_last_balance']+$deposito_profit_sharing_amount,
							'operated_name'									=> 'SYSTEM',
							'created_id'									=> $auth['user_id'],
							'created_on'									=> date('Y-m-d H:i:s')
						);

						
					} 

					$datadetail = $data_detail;

					// print_r($datadetail);
					// exit;

					$log_step2 	= array (
						'branch_id'			=> $auth['branch_id'],
						'created_id'		=> $auth['user_id'],
						'created_on'		=> date('Y-m-d'),
						'periode'			=> $deposito_profit_sharing_period,
						'step'				=> 2,
						'total_account'		=> count($datadetail),
					);

					if($this->AcctDepositoProfitSharingNew_model->insertDataLogStep($log_step2)){
						$this->AcctDepositoProfitSharingNew_model->insertAcctDepositoProfitSharingTemp($datadetail, $log_step2);
						
						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Basil Simpanan Berjangka Sukses
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctDepositoProfitSharingNew/listdata');
					} else {
						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-danger alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Perhitungan Basil Simpanan Berjangka Gagal
								</div> ";

						$this->session->set_userdata('message',$msg);
						redirect('AcctDepositoProfitSharingNew');
					}
				} else {
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-danger alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Perhitungan Basil Simpanan Berjangka Sudah Dihitung
							</div> ";

					$this->session->set_userdata('message',$msg);
					redirect('AcctDepositoProfitSharingNew/listdata');
				}
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingNew');
			}			
		}

		//----------------------View Hasil perhitungan----------------------------------------//

		public function listdata(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['monthname']							= $this->configuration->Month();


			$data['main_view']['acctdepositoprofitsharingtemp']		= $this->AcctDepositoProfitSharingNew_model->getAcctDepositoProfitSharingTemp($auth['branch_id']);

			$data['main_view']['content']			= 'AcctDepositoProfitSharingNew/ListDataDepositoProfitSharingNew_view';
			$this->load->view('MainPage_view',$data);
		}

		//---------------------------Step 3 Update Data Basil -----------------------------------//

		public function processUpdateAcctDepositoProfitSharing(){
			$auth = $this->session->userdata('auth');

			$periode 	= $this->AcctDepositoProfitSharingNew_model->getPeriode($auth);

			$log_step3 	= array (
				'branch_id'			=> $auth['branch_id'],
				'created_id'		=> $auth['user_id'],
				'created_on'		=> date('Y-m-d'),
				'periode'			=> $periode,
				'step'				=> 3,
			);

			$data_log_step3 = $this->AcctDepositoProfitSharingNew_model->getDataLogStep3($log_step3);	

			$month 			= date('m', strtotime($periode));	
			$year 			= date('Y', strtotime($periode));
			$lastdate 		= date('t', strtotime($periode));
			$date 			= $year.'-'.$month.'-'.$lastdate;

			if(empty($data_log_step3)){
				if($this->AcctDepositoProfitSharingNew_model->insertDataLogStep($log_step3)){
					if($this->AcctDepositoProfitSharingNew_model->insertAcctDepositoProfitSharingFix()){
						$corebranch = $this->AcctDepositoProfitSharingNew_model->getCoreBranch();

						foreach ($corebranch as $key => $vCB) {
							$deposito_profit_sharing_amount = $this->AcctDepositoProfitSharingNew_model->getTotalDepositoProfitSharing($vCB['branch_id']);

							$data_transfer = array (
								'branch_id'									=> $vCB['branch_id'],
								'savings_transfer_mutation_date'			=> date('Y-m-d'),
								'savings_transfer_mutation_amount'			=> $deposito_profit_sharing_amount,
								'operated_name'								=> 'SYSTEM',
								'created_id'								=> $auth['user_id'],
								'created_on'								=> date('Y-m-d H:i:s'),
							);

							if($this->AcctDepositoProfitSharingNew_model->insertAcctSavingsTransferMutation($data_transfer)){
								$savings_transfer_mutation_id = $this->AcctDepositoProfitSharingNew_model->getSavingsTranferMutationID($data_transfer['created_id']);

								$this->AcctDepositoProfitSharingNew_model->insertAcctSavingsTransferMutationTo($savings_transfer_mutation_id, $vCB['branch_id']);
							}

							$acctdeposito 	= $this->AcctDepositoProfitSharingNew_model->getAcctDeposito();
							$acctsavings 	= $this->AcctDepositoProfitSharingNew_model->getAcctSavings();


							//-------------------------------------Jurnal--------------------------------------------------------//
							
							foreach ($acctdeposito as $key => $val) {

								foreach ($acctsavings as $k => $v){
									
									$totaldepositoprofitsharing 	= $this->AcctDepositoProfitSharingNew_model->getSubTotalDepositoProfitSharing($val['deposito_id'], $v['savings_id'], $vCB['branch_id']);

									if(!empty($totaldepositoprofitsharing)){
										$transaction_module_code 	= "BSDEP";

										$transaction_module_id 		= $this->AcctDepositoProfitSharingNew_model->getTransactionModuleID($transaction_module_code);

										
											
										$journal_voucher_period 	= $periode;
										
										$data_journal 				= array(
											'branch_id'						=> $vCB['branch_id'],
											'journal_voucher_period' 		=> $journal_voucher_period,
											'journal_voucher_date'			=> date('Y-m-d'),
											'journal_voucher_title'			=> 'BAGI HASIL SIMPANAN BERJANGKA'.$val['deposito_name'].' PERIODE '.$periode,
											'journal_voucher_description'	=> 'BAGI HASIL SIMPANAN BERJANGKA'.$val['deposito_name'].' PERIODE '.$periode,
											'transaction_module_id'			=> $transaction_module_id,
											'transaction_module_code'		=> $transaction_module_code,
											// 'transaction_journal_id' 		=> $acctsavingsprofitsharing['savings_profit_sharing_log_id'],
											// 'transaction_journal_no' 		=> $acctsavingsprofitsharing['savings_profit_sharing_period'],
											'created_id' 					=> $auth['user_id'],
											'created_on' 					=> date('Y-m-d H:i:s'),
										);
										
										$this->AcctDepositoProfitSharingNew_model->insertAcctJournalVoucher($data_journal);

										$journal_voucher_id 			= $this->AcctDepositoProfitSharingNew_model->getJournalVoucherID($data_journal['created_id']);

										$account_basil_id 				= $this->AcctDepositoProfitSharingNew_model->getAccountBasilID($val['deposito_id']);

										$account_id_default_status 		= $this->AcctDepositoProfitSharingNew_model->getAccountIDDefaultStatus($account_basil_id);

										$data_debet = array (
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_basil_id,
											'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
											'journal_voucher_amount'		=> $totaldepositoprofitsharing,
											'journal_voucher_debit_amount'	=> $totaldepositoprofitsharing,
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
										);

										$this->AcctDepositoProfitSharingNew_model->insertAcctJournalVoucherItem($data_debet);

										$account_id 					= $this->AcctDepositoProfitSharingNew_model->getAccountID($v['savings_id']);

										$account_id_default_status 		= $this->AcctDepositoProfitSharingNew_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
											'journal_voucher_amount'		=> $totaldepositoprofitsharing,
											'journal_voucher_credit_amount'	=> $totaldepositoprofitsharing,
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
										);

										$this->AcctDepositoProfitSharingNew_model->insertAcctJournalVoucherItem($data_credit);
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
					redirect('AcctDepositoProfitSharingNew');
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Proses Basil Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctDepositoProfitSharingNew/listdata');
				}
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Basil Sudah Selesai Diproses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingNew');
			}
		}
	}
?>