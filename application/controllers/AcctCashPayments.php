<?php
	Class AcctCashPayments extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('AcctCashPayment_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function indAcctCashPayment(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('acctcreditspaymentcashtoken-'.$unique['unique']);

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCashPayment/ListAcctCashPayment_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcashpayment(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcashpayment', $data);
			redirect('AcctCashPayments/indAcctCashPayment');
		}

		public function getAcctCashPayment(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcashpayment');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}

				/*print_r(" Sesi");*/
			}

			$list = $this->AcctCashPayment_model->get_datatables($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $cashpayment) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $cashpayment->credits_account_serial;
	            $row[] = $cashpayment->member_name;
	            $row[] = $cashpayment->credits_name;
	            $row[] = tgltoview($cashpayment->credits_payment_date);
	            $row[] = number_format($cashpayment->credits_payment_principal, 2);
	            $row[] = number_format($cashpayment->credits_payment_margin, 2);
			    $row[] = '<a href="'.base_url().'AcctCashPayments/printNoteCashPayment/'.$cashpayment->credits_payment_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>';
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCashPayment_model->count_all($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCashPayment_model->count_filtered($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" 				=> $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addAcctCashPayment(){	
			$credits_account_id 	= $this->uri->segment(3);

			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditspaymentcashtoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('acctcreditspaymentcashtoken-'.$unique['unique'], $token);
			}


			$data['main_view']['accountcredit']			= $this->AcctCreditAccount_model->getDetailByID($credits_account_id);
			$data['main_view']['detailpayment']			= $this->AcctCashPayment_model->getDataByIDCredit($credits_account_id);
			$data['main_view']['content']				= 'AcctCashPayment/FormAddAcctCashPayment2_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function getDetailPayment(){
			$credits_account_id 				= $this->input->get('credits_account_id');
			
			$data['detailpayment']				= $this->AcctCashPayment_model->getDataByIDCredit($credits_account_id);
			$data['content']					= 'AcctCashPayment/ListPaymentByCreditAccount_view';
			$this->load->view('AcctCashPayment/ListPaymentByCreditAccount_view',$data);
		}

		public function akadlisttunai(){
			$auth 	= $this->session->userdata('auth');
			$list 	= $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data 	= array();
	        $no 	= $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = tgltoview($customers->credits_account_date);
	            $row[] = tgltoview($customers->credits_account_due_date);
	             $row[] = '<a href="'.base_url().'AcctCashPayments/addAcctCashPayment/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" 				=> $data,
	                );
	        echo json_encode($output);
			
		}

		public function processAddAcctCashPayment(){
			$auth 									= $this->session->userdata('auth');
			$total_angsuran 						= $this->input->post('jangka_waktu', true);
			$angsuran_ke 							= $this->input->post('credits_payment_to', true);

			if($angsuran_ke < $total_angsuran){
				$credits_account_payment_date_old 	= tgltodb($this->input->post('credits_account_payment_date'));
				$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
			}

			$data = array(
				'branch_id'									=> $auth['branch_id'],
				'member_id'									=> $this->input->post('member_id', true),
				'credits_id'								=> $this->input->post('credits_id', true),
				'credits_account_id'						=> $this->input->post('credits_account_id', true),
				'credits_payment_date'						=> date('Y-m-d'),
				'credits_payment_amount'					=> $this->input->post('jumlah_angsuran', true),
				'credits_payment_principal'					=> $this->input->post('angsuran_pokok', true),
				'credits_payment_margin'					=> $this->input->post('angsuran_margin', true),
				'credits_principal_opening_balance'			=> $this->input->post('sisa_pokok_awal', true),
				'credits_principal_last_balance'			=> $this->input->post('sisa_pokok_awal', true) - $this->input->post('angsuran_pokok', true),
				'credits_margin_opening_balance'			=> $this->input->post('sisa_margin_awal', true),
				'credits_margin_last_balance'				=> $this->input->post('sisa_margin_awal', true) - $this->input->post('angsuran_margin', true),
				'credits_account_payment_date'				=> tgltodb($this->input->post('credits_account_payment_date')),
				'credits_payment_to'						=> $this->input->post('credits_payment_to', true),
				'credits_payment_day_of_delay'				=> $this->input->post('credits_payment_day_of_delay', true),
				'credits_payment_token'						=> $this->input->post('credits_payment_token', true),
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			
			$this->form_validation->set_rules('jumlah_angsuran', 'Jumlah Pembayaran', 'required');
			$this->form_validation->set_rules('angsuran_pokok', 'Pembayaran Pokok', 'required');

			$transaction_module_code 						= 'ANGS';
			$transaction_module_id 							= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 								= $this->AcctCreditAccount_model->getPreferenceCompany();

			$journal_voucher_period 						= date("Ym", strtotime($data['credits_payment_date']));
			
			$credits_payment_token 							= $this->AcctCashPayment_model->getCreditsPaymentToken($data['credits_payment_token']);


			if($this->form_validation->run()==true){
				if($credits_payment_token->num_rows() == 0){
					if($this->AcctCashPayment_model->insert($data)){
						$updatedata = array(
							"credits_account_last_balance_principal" 	=> $data['credits_principal_last_balance'],
							"credits_account_last_balance_margin" 		=> $data['credits_margin_last_balance'],
							"credits_account_last_payment_date"			=> $data['credits_payment_date'],
							"credits_account_payment_date"				=> $credits_account_payment_date,
							"credits_account_payment_to"				=> $data['credits_payment_to'],
						);

						$this->AcctCreditAccount_model->updatedata($updatedata,$data['credits_account_id']);

						$acctcashpayment_last 							= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
							'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
							'journal_voucher_token'			=> $data['credits_payment_token'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
							'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_payment_amount'],
							'journal_voucher_debit_amount'	=> $data['credits_payment_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'],
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

						$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $receivable_account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_payment_amount'],
							'journal_voucher_credit_amount'	=> $data['credits_payment_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_payment_token'].$receivable_account_id,
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

						if($data['credits_id'] == 5 || $data['credits_id'] == 6){

							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);

							$data_debet =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_deferred_margin_income'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_margin'],
								'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_deferred_margin_income'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						} else {
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_margin'],
								'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'].$data['credits_id'],
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}

						$income_account_id 					= $this->AcctCreditAccount_model->getIncomeAccountID($data['credits_id']);

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($income_account_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $income_account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_payment_margin'],
							'journal_voucher_credit_amount'	=> $data['credits_payment_margin'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_payment_token'].$income_account_id,
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);


						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Pembayaran Pinjaman Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctcashpayment-'.$sesi['unique']);
						$this->session->unset_userdata('acctcreditspaymentcashtoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('AcctCashPayments/printNoteCashPayment/'.$acctcashpayment_last['credits_payment_id']);
					}else{
						$this->session->set_userdata('addacctcashpayment-'.$unique['unique'],$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Pembayaran Pinjaman Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('AcctCashPayments/addAcctCashPayment');
					}
				} else {
					$acctcashpayment_last 							= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);

					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
						'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
						'journal_voucher_token'			=> $data['credits_payment_token'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
						'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);
					
					$journal_voucher_token 				= $this->AcctCreditAccount_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);
					}

					$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_payment_amount'],
						'journal_voucher_debit_amount'	=> $data['credits_payment_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'],
					);

					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					}

					$receivable_account_id 				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $receivable_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_payment_amount'],
						'journal_voucher_credit_amount'	=> $data['credits_payment_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['credits_payment_token'].$receivable_account_id,
					);

					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
					}

					if($data['credits_id'] == 5 || $data['credits_id'] == 6){

						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);

						$data_debet =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_deferred_margin_income'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_payment_margin'],
							'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_deferred_margin_income'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}
					} else {
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_payment_margin'],
							'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_payment_token'].$preferencecompany['account_cash_id'].$data['credits_id'],
						);

						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows()==0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}
					}

					$income_account_id 					= $this->AcctCreditAccount_model->getIncomeAccountID($data['credits_id']);

					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($income_account_id);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $income_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_payment_margin'],
						'journal_voucher_credit_amount'	=> $data['credits_payment_margin'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['credits_payment_token'].$income_account_id,
					);

					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
					}


					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctcashpayment-'.$sesi['unique']);
					$this->session->unset_userdata('acctcreditspaymentcashtoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctCashPayments/printNoteCashPayment/'.$acctcashpayment_last['credits_payment_id']);
				}
				
				
			}else{
				$this->session->set_userdata('addacctcashpayment-'.$unique['unique'],$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctCashPayments/addAcctCashPayment');
			}
		}

		public function printNoteCashPayment(){
			$auth = $this->session->userdata('auth');
			$credits_payment_id 	= $this->uri->segment(3);
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();
			$acctcreditspayment	 	= $this->AcctCashPayment_model->getAcctCreditspayment_Detail($credits_payment_id);


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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">".$img."</td>
			        <td width=\"50%\"><div style=\"text-align: left; font-size:14px\">BUKTI SETORAN ANGSURAN</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Akad</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditspayment['credits_payment_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditspayment['credits_payment_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function indCashLessPayment(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCashPayment/ListAcctNonCashPayment_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcashlesspayment(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcashpaymentless', $data);
			redirect('AcctCashPayments/indCashLessPayment');
		}

		public function getAcctCashLessPayment(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcashpaymentless');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}

				/*print_r(" Sesi");*/
			}

			$list = $this->AcctCashPayment_model->get_datatables($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $cashpayment) {
	            $no++;
	            if($cashpayment->credits_payment_type == 1){
	            	 $row = array();
		            $row[] = $no;
		            $row[] = $cashpayment->credits_account_serial;
		            $row[] = $cashpayment->member_name;
		            $row[] = $cashpayment->credits_name;
		            $row[] = $this->AcctCashPayment_model->getSavingsAccountNO($cashpayment->savings_account_id);
		            $row[] = tgltoview($cashpayment->credits_payment_date);
		            $row[] = number_format($cashpayment->credits_payment_principal, 2);
		            $row[] = number_format($cashpayment->credits_payment_margin, 2);
				    $row[] = '<a href="'.base_url().'AcctCashPayments/printNoteCashLessPayment/'.$cashpayment->credits_payment_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>';
		            $data[] = $row;
	            }
	           
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCashPayment_model->count_all($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCashPayment_model->count_filtered($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addCashlessPayment(){	
			$id3 	= $this->uri->segment(3);
			$id4 	= $this->uri->segment(4);

			$data['main_view']['credit_account'] = "";
			$data['main_view']['saving_account'] = "";

			if($id3 != ""){
				$data['main_view']['credit_account'] 	= $this->AcctCreditAccount_model->getDetailByID($id3);
			}
			if($id4 != ""){
				$data['main_view']['saving_account'] 	= $this->AcctSavingsAccount_model->getAcctSavingsAccount_Detail($id4);
			}

			$data['main_view']['content']				= 'AcctCashPayment/FormAddCashPaymentNon_view';
			$this->load->view('MainPage_view',$data);
		}

		public function simpananlist(){
		$auth 		= $this->session->userdata('auth');

		$list 		= $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
        $data 		= array();
		$segment3 	= $this->uri->segment(3);
		$segment4 	= $this->uri->segment(4);
        $no 		= $_POST['start'];

        foreach ($list as $customers) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $customers->member_no;
            $row[] = $customers->member_name;
            $row[] = $customers->savings_account_no;
			$row[] = '<a href="'.base_url().'AcctCashPayments/addCashlessPayment/'.$segment3.'/'.$customers->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
            $data[] = $row;
        }
 
        $output = array(
                        "draw" 				=> $_POST['draw'],
                        "recordsTotal" 		=> $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
                        "recordsFiltered" 	=> $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
                        "data" 				=> $data,
                );
        //output to json format
        echo json_encode($output);
			
		}		

		public function akadlist(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
			$segment3=$this->uri->segment(3);
			$segment4=$this->uri->segment(4);
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = tgltoview($customers->credits_account_date);
	            $row[] = tgltoview($customers->credits_account_due_date);
	             $row[] = '<a href="'.base_url().'AcctCashPayments/addCashlessPayment/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        echo json_encode($output);
			
		}

		public function getCreditAccountDetail(){
			$credits_account_id 	= $this->input->post('credits_account_id');

			// $credits_account_id = 9;
			
			$data 			= $this->AcctCreditAccount_model->getDetailByID($credits_account_id);

			$result = array();
			$result = array(
				"member_id"						=> $data['member_id'],
				"credits_id"					=> $data['credits_id'],
				"pembiayaan" 					=> $data['member_name'], 
				"member_name"					=> $data['member_name'],
				"member_address"				=> $data['member_address'],
				"city_name"						=> $data['city_name'],
				"kecamatan_name"				=> $data['kecamatan_name'],
				"identity_name"					=> $data['identity_name'],
				"member_identity_no"			=> $data['member_identity_no'],
				"jangka_waktu"					=> $data['credits_account_period'],
				"jatuh_tempo"					=> $data['credits_account_due_date'],
				"tanggal_realisasi"				=> $data['credits_account_date'],
				"payment_amount"				=> $data['credits_account_payment_amount'],
				"sisa_pokok"					=> $data['credits_account_last_balance_principal'],
				"sisa_margin"					=> $data['credits_account_last_balance_margin'],
				"jumlah_angsuran"				=> $data['credits_account_payment_amount'],
				"angsuran_pokok"				=> $data['credits_account_principal_amount'],
				"angsuran_margin"				=> $data['credits_account_margin_amount'],
				"saldo_piutang"			=> $data['credits_account_last_balance_principal']+$data['credits_account_last_balance_margin'],
			);
			echo json_encode($result);		
		}

		public function AcctCashPaymentsProcess(){
			$auth 			= $this->session->userdata('auth');
			$norek 			= $this->input->post('savings_account_id');
			$pokok 			= $this->input->post('credits_payment_principal');
			$margin 		= $this->input->post('credits_payment_margin');
			$id_pinjaman 	= $this->input->post('credits_account_id');
			$total 			= $pokok+$margin;
			$simpanan 		= $this->AcctSavingsAccount_model->getAcctSavingsAccount_Detail($norek);
			$pinjaman 		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($id_pinjaman);
			$last_balance 	= $pinjaman['credits_account_last_balance_principal']-$pokok;
			$last_margin 	= $pinjaman['credits_account_last_balance_margin']-$margin;

			if($simpanan['savings_account_last_balance'] < $total){
				$auth 	= $this->session->userdata('auth');
				$msg 	= "<div class='alert alert-danger alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tabungan tidak cukup
							</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addCashlessPayment-'.$sesi['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('AcctCashPayments/addCashlessPayment');
			}

			$total_angsuran = $this->input->post('jangka_waktu', true);
			$angsuran_ke 	= $this->input->post('credits_payment_to', true);

			if($angsuran_ke < $total_angsuran){
				$credits_account_payment_date_old 	= tgltodb($this->input->post('credits_account_payment_date'));
				$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
			}

			$data_cash = array(
				'branch_id'									=> $auth['branch_id'],
				'member_id'									=> $simpanan['member_id'],
				'credits_id'								=> $this->input->post('credits_id', true),
				'credits_account_id'						=> $this->input->post('credits_account_id', true),
				'savings_account_id'						=> $this->input->post('savings_account_id', true),
				'credits_payment_date'						=> date('Y-m-d'),
				'credits_payment_amount'					=> $total,
				'credits_payment_principal'					=> $pokok,
				'credits_payment_margin'					=> $margin,
				'credits_principal_opening_balance'			=> $pinjaman['credits_account_last_balance_principal'],
				'credits_principal_last_balance'			=> $last_balance,
				'credits_margin_opening_balance'			=> $pinjaman['credits_account_last_balance_margin'],
				'credits_margin_last_balance'				=> $last_margin,
				'credits_account_payment_date'				=> tgltodb($this->input->post('credits_account_payment_date')),
				'credits_payment_to'						=> $this->input->post('credits_payment_to', true),
				'credits_payment_day_of_delay'				=> $this->input->post('credits_payment_day_of_delay', true),
				'credits_payment_fine'						=> $this->input->post('credits_payment_fine', true),
				'credits_payment_type'						=> 1,
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('savings_account_id', 'No. Rekening Simpanan', 'required');
			$this->form_validation->set_rules('credits_payment_principal', 'Pembayaran Pokok', 'required');


			$transaction_module_code 	= 'ANGS';
			$transaction_module_id 		= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();

			if($this->form_validation->run()==true){
				if($this->AcctCashPayment_model->insert($data_cash)){
					$updatedata = array(
						"credits_account_last_balance_principal" 	=> $data_cash['credits_principal_last_balance'],
						"credits_account_last_balance_margin" 		=> $data_cash['credits_margin_last_balance'],
						"credits_account_last_payment_date"			=> $data_cash['credits_payment_date'],
						"credits_account_payment_date"				=> $credits_account_payment_date,
						"credits_account_payment_to"				=> $data_cash['credits_payment_to'],
					);
					$this->AcctCreditAccount_model->updatedata($updatedata,$data_cash['credits_account_id']);

					$update_saving = array(
						"savings_account_last_balance" => $simpanan['savings_account_last_balance']-$total
					);

					$this->AcctSavingsAccount_model->updatedata($update_saving,$norek);

					$last_balance 	= $simpanan['savings_account_last_balance'] - $total;
					$mutasi_data 	=array(
						"savings_account_id" 					=> $norek,
						"savings_id" 							=> $simpanan['savings_id'],
						"member_id" 							=> $simpanan['member_id'],
						"branch_id" 							=> $auth['branch_id'],
						"mutation_id" 							=> 4,
						"savings_cash_mutation_date" 			=> date('Y-m-d'),
						"savings_cash_mutation_opening_balance" => $simpanan['savings_account_last_balance'],
						"savings_cash_mutation_last_balance" 	=> $last_balance,
						"savings_cash_mutation_amount" 			=> $total,
						"savings_cash_mutation_remark"	 		=> "Pembayaran Kredit No.".$this->input->post('credits_account_serial',true),
					);

					$this->AcctSavingsCashMutation_model->insertAcctSavingsCashMutation($mutasi_data);
					// print_r($mutasi_data);exit;


					$acctcashpayment_last 	= $this->AcctCashPayment_model->AcctCashPaymentLast($data_cash['created_id']);
						
					$journal_voucher_period = date("Ym", strtotime($data_cash['credits_payment_date']));
					
					$data_journal = array(
						'branch_id'						=> $data_cash['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'ANGSURAN NON TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
						'journal_voucher_description'	=> 'ANGSURAN NON TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
						'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
						'created_id' 					=> $data_cash['created_id'],
						'created_on' 					=> $data_cash['created_on'],
					);

					// print_r($acctcashpayment_last);exit;
					
					$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

					$journal_voucher_id 		= $this->AcctCreditAccount_model->getJournalVoucherID($data_cash['created_id']);

					$savingsaccount_id 			= $this->AcctCashPayment_model->getSavingsAccountID($mutasi_data['savings_id']);

					$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($savingsaccount_id);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $savingsaccount_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data_cash['credits_payment_amount'],
						'journal_voucher_debit_amount'	=> $data_cash['credits_payment_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
					);

					$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

					$receivable_account_id 		= $this->AcctCreditAccount_model->getReceivableAccountID($acctcashpayment_last['credits_id']);

					$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

					$data_credit = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $receivable_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data_cash['credits_payment_amount'],
						'journal_voucher_credit_amount'	=> $data_cash['credits_payment_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
					);

					$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

					if($acctcashpayment_last['credits_id'] == 5 || $acctcashpayment_last['credits_id'] == 6){

						$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);

						$data_debet =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_deferred_margin_income'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data_cash['credits_payment_margin'],
							'journal_voucher_debit_amount'	=> $data_cash['credits_payment_margin'],
							'account_id_status'				=> 0,
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					} else {
						$savingsaccount_id 			= $this->AcctCashPayment_model->getSavingsAccountID($mutasi_data['savings_id']);

						$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($savingsaccount_id);

						$data_debet =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $savingsaccount_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data_cash['credits_payment_margin'],
							'journal_voucher_debit_amount'	=> $data_cash['credits_payment_margin'],
							'account_id_status'				=> 0,
						);

						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					}

					$income_account_id 			= $this->AcctCreditAccount_model->getIncomeAccountID($acctcashpayment_last['credits_id']);

					$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($income_account_id);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $income_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data_cash['credits_payment_margin'],
						'journal_voucher_credit_amount'	=> $data_cash['credits_payment_margin'],
						'account_id_status'				=> 1,
					);

					$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
					
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addCashlessPayment-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctCashPayments/addCashlessPayment');
				}else{
					$this->session->set_userdata('addacctcashpayment-'.$unique['unique'],$data_cash);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctCashPayments/addCashlessPayment');
				}	
			}else{
				$this->session->set_userdata('addacctcashpayment-'.$unique['unique'],$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctCashPayments/addCashlessPayment');
			}
		}

		public function printNoteCashLessPayment(){
			$auth = $this->session->userdata('auth');
			$credits_payment_id 	= $this->uri->segment(3);
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();
			$acctcreditspayment	 	= $this->AcctCashPayment_model->getAcctCreditspayment_Detail($credits_payment_id);


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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">".$img."</td>
			        <td width=\"50%\"><div style=\"text-align: left; font-size:14px\">BUKTI SETORAN ANGSURAN NON TUNAI</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Akad</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['credits_account_serial']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rek. Simpanan</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$this->AcctCashPayment_model->getSavingsAccountNO($acctcreditspayment['savings_account_id'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".$acctcreditspayment['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditspayment['credits_payment_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: ANGUSRAN PEMBIAYAAN KE ".$acctcreditspayment['credits_payment_to']."</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"50%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditspayment['credits_payment_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"10%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctcashpayment-'.$unique['unique']);
			redirect('AcctCashPayments/processAddAcctCashPayment');
		}
		
		public function historyPayment(){
			$auth 	= $this->session->userdata('auth');
			$id3=$this->uri->segment(3);
			$data['main_view']['credit_account']="";
			if($id3 != ""){
				$data['main_view']['credit_account'] = $this->AcctCreditAccount_model->getDetailByID($id3);
			}
			$data['main_view']['acctcreditspayment']		=$this->AcctCreditAccount_model->getAcctCreditsPayment_Detail($id3);
			$data['main_view']['content']					= 'AcctCashPayment/ListHistoryAcctCreditsPayment_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function creditList(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
			$segment3=$this->uri->segment(3);
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	             $row[] = '<a href="'.base_url().'AcctCashPayments/historyPayment/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        echo json_encode($output);
			
		}
		

	}
?>