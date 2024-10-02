<?php
	Class PPOBCompany extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('Android_model');
			$this->load->model('ppob/PPOBCompany_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->model('PpobPulsaPrabayar_model');
			$this->load->model('PpobPulsaPascabayar_model');
			$this->load->model('PpobPaymentTelkomApi_model');
			$this->load->model('PpobPlnPrepaid_model');
			$this->load->model('PpobPlnPostpaid_model');
			$this->load->model('PpobPaymentTopUpApi_model');
			$this->load->model('PpobPaymentBpjs_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->database('cipta');
			$this->load->library('configuration');
			$this->load->library('fungsi');
		}
		
		public function index(){
			$data = array (
				// 'msisdn' 			=> $this->input->post('phone_number',true),
				'msisdn' 			=> '085725288892',
			);

			// $inquiry_data = $this->PpobPulsaPrabayar_model->inquiry($data);
			
			$hasil=$this->PpobPulsaPrabayar_model->info();
			var_dump($hasil); exit;
		}

		//------------------------------- P P O B -------------------------------------

		// SALDO PPOB

			public function getPPOBBalance(){
				$base_url 	= base_url();
				$auth 		= $this->session->userdata('auth');

				$response = array(
					'error'				=> FALSE,
					'error_msg'			=> "",
					'error_msg_title'	=> "",
					'ppobbalance'		=> "",
				);

				$data = array(
					'user_id'		=> $this->input->post('user_id',true),
				);

				$database 			= $this->db->database;

				$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

				$ppob_agen_id		= $data['user_id'];


				if($response["error"] == FALSE){

					$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

					if(empty($ppob_balance)){
						$ppob_balance 	= 0;
					}

					$ppobbalance[0]['ppob_balance']		= $ppob_balance;
							
					$response['error'] 					= FALSE;
					$response['error_msg_title'] 		= "Success";
					$response['error_msg'] 				= "Data Exist";
					$response['ppobbalance'] 			= $ppobbalance;
				}

				echo json_encode($response);
			}

		// END SALDO PPOB

		// TOPUP PPOB 

		public function processAddAcctSavingsPPOBMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'branch_id'								=> $this->input->post('branch_from_id', true),
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $this->input->post('username', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			$savings_account_from_id 	= $this->input->post('savings_account_from_id', true);

			// $savings_account_from_id 	= 31048;


			$preferencecompany 			= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

			// $data = array(
			// 	'branch_id'								=> 2,
			// 	'savings_transfer_mutation_date'		=> date('Y-m-d'),
			// 	'savings_transfer_mutation_amount'		=> 500000,
			// 	'savings_transfer_mutation_status'		=> 3,
			// 	'operated_name'							=> 'SAIFUDIN',
			// 	'created_id'							=> 31048,
			// 	'created_on'							=> date('Y-m-d H:i:s'),
			// );


			$response = array(
				'error'										=> FALSE,
				'error_insertacctsavingsppob'				=> FALSE,
				'error_msg_title_insertacctsavingsppob'		=> "",
				'error_msg_insertacctsavingsppob'			=> "",
			);

			if($response["error_insertacctsavingsppob"] == FALSE){
				if(!empty($data)){	
					$check_topupppob = $this->PPOBCompany_model->checkAcctSavingsTransfer($savings_account_from_id, $preferencecompany['savings_account_ppob_id']);

					// print_r($check_topupppob->num_rows());exit;

					if($check_topupppob->num_rows() == 0){
						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data)){
							$transaction_module_code 		= "TPPPOB";
	
							$transaction_module_id 			= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);
	
							$savings_transfer_mutation_id 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);
	
							
	
							//----- Simpan data transfer from
	
							$datafrom = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $this->input->post('savings_account_from_id', true),
								'savings_id'								=> $this->input->post('savings_from_id', true),
								'member_id'									=> $this->input->post('member_from_id', true),
								'branch_id'									=> $this->input->post('branch_from_id', true),
								'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
								'savings_account_opening_balance'			=> $this->input->post('savings_account_from_opening_balance', true),
								'savings_transfer_mutation_from_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
								'savings_account_last_balance'				=> $this->input->post('savings_account_from_opening_balance', true) - $this->input->post('savings_transfer_mutation_amount', true),
							);
	
	
							$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datafrom['member_id']);
	
							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
	
								//----- Simpan data jurnal
								$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_id']);
								
								$journal_voucher_period 	= date("Ym", strtotime($data['savings_transfer_mutation_date']));
								
								$data_journal = array(
									'branch_id'						=> $data['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
									'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
									'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
									'created_id' 					=> $data['created_id'],
									'created_on' 					=> $data['created_on'],
								);
								
								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);
	
								$journal_voucher_id 			= $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);
	
								
								//----- Simpan data jurnal debit
								$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);
	
								$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);
	
								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'NOTA DEBET '.$member_name,
									'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_transfer_mutation_amount'],
									'account_id_status'				=> 1,
								);
	
								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
	
	
								//----- Simpan data transfer to
								$savingsaccountto 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsAccount_Detail($preferencecompany['savings_account_ppob_id']);
	
								$datato = array (
									'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
									'savings_account_id'						=> $preferencecompany['savings_account_ppob_id'],
									'savings_id'								=> $savingsaccountto['savings_id'],
									'member_id'									=> $savingsaccountto['member_id'],
									'branch_id'									=> $savingsaccountto['branch_id'],
									'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
									'savings_account_opening_balance'			=> $savingsaccountto['savings_account_last_balance'],
									'savings_transfer_mutation_to_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
									'savings_account_last_balance'				=> $savingsaccountto['savings_account_last_balance'] + $this->input->post('savings_transfer_mutation_amount', true),
								);
	
								$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datato['member_id']);
	
								if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){
	
									//----- Simpan data jurnal kredit
									$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);
	
									$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);
	
									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $account_id,
										'journal_voucher_description'	=> 'NOTA KREDIT '.$member_name,
										'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
										'journal_voucher_credit_amount'	=> $data['savings_transfer_mutation_amount'],
										'account_id_status'				=> 0,
									);
	
									$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
	
							$database 			= $this->db->database;
	
							$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);
	
							$ppob_agen_id		= $this->input->post('member_from_id', true);
	
							$ppob_agen_name 	= $this->AcctSavingsTransferMutation_model->getMemberName($ppob_agen_id);
	
							$data_ppob = array (
								'ppob_company_id'	=> $ppob_company_id,
								'ppob_agen_id'		=> $ppob_agen_id,
								'ppob_agen_name'	=> $ppob_agen_name,
								'ppob_topup_amount'	=> $data['savings_transfer_mutation_amount'],
								'ppob_topup_status'	=> 0,
								'ppob_topup_date'	=> date('Y-m-d'),
								'created_id'		=> $ppob_agen_id,
								'created_on'		=> date('Y-m-d H:i:s')
							);
	
							$this->PPOBCompany_model->insertPPOBTopUP($data_ppob);
	
							$response['error_insertacctsavingsppob'] 		= FALSE;
							$response['error_msg_title'] 					= "Success";
							$response['error_msg'] 							= "Data Exist";
							$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
						}else{
							$response['error_insertacctsavingsppob'] 		= TRUE;
							$response['error_msg_title'] 					= "Failed";
							$response['error_msg'] 							= "Data Exist";
						}
					} else {
						$response['error_insertacctsavingsppob'] 		= TRUE;
						$response['error_msg_title'] 					= "Gagal Top Up";
						$response['error_msg'] 							= "Top Up masih ada yang belum diproses";
					}
				}
			}

			echo json_encode($response);

		}

		// END TOP UP

		// HISTORY PPOB PENDING

		public function getHistoryPPOB(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'historyppob'				=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'user_id'		=> $this->input->post('user_id',true),
				'ppob_status'	=> $this->input->post('ppob_status',true),
			);

			// $data['user_id']	= 32891;

			// $status 			= 1;

			$ppobstatus 		= $this->configuration->PpobStatus();

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);
			
			if($response["error"] == FALSE){

				$ppobtransactionlog = $this->PPOBCompany_model->getPPOBTransactionLog($ppob_company_id, $data['user_id']);

				// print_r($ppobtransactionlog);

				if(!$ppobtransactionlog){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($ppobtransactionlog)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$no = 0;
						foreach ($ppobtransactionlog as $key => $val) {

							if($val['ppob_transaction_code'] == "Top Up"){
								$ppobtopup 			= $this->PPOBCompany_model->getPPOBTopUP_Detail($val['ppob_transaction_id'], $data['ppob_status']);


								if(!empty($ppobtopup)){
									if($ppobtopup['ppob_topup_amount'] == null){
										$ppobtopup['ppob_topup_amount'] = 0;
									}

									$historyppob[$no]['ppob_transaction_title']				= "Top Up";
									$historyppob[$no]['ppob_transaction_date']				= date('d M Y H:i:s', strtotime($ppobtopup['created_on']));
									$historyppob[$no]['ppob_transaction_description']		= "No. trx ".$ppobtopup['ppob_topup_no']." Top Up saldo";
									$historyppob[$no]['ppob_transaction_amount']			= $ppobtopup['ppob_topup_amount'];
									$historyppob[$no]['ppob_transaction_status_name']		= $ppobstatus[$ppobtopup['ppob_topup_status']];

									$no++;
								}
	
							} else {
								$ppobtransaction 	= $this->PPOBCompany_model->getPPOBTransaction_Detail($val['ppob_transaction_id'], $data['ppob_status']);
								

								if(!empty($ppobtransaction)){
									if($ppobtransaction['ppob_transaction_amount'] == null){
										$ppobtransaction['ppob_transaction_amount'] = 0;
									}

									$historyppob[$no]['ppob_transaction_title']				= $ppobtransaction['ppob_product_category_name'];
									$historyppob[$no]['ppob_transaction_date']				= date('d M Y H:i:s', strtotime($ppobtransaction['created_on']));
									$historyppob[$no]['ppob_transaction_description']		= "No. trx ".$ppobtransaction['ppob_transaction_no']." Transaksi ".$ppobtransaction['ppob_product_category_name']." ".$ppobtransaction['ppob_product_name'];
									$historyppob[$no]['ppob_transaction_amount']			= $ppobtransaction['ppob_transaction_amount'];
									$historyppob[$no]['ppob_transaction_status_name']		= $ppobstatus[$ppobtransaction['ppob_transaction_status']];

									$no++;
								}
	
							}

							
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['historyppob'] 			= $historyppob;
					}
				}
			}
			
			echo json_encode($response);
		}

		// END HISTORY PPOB PENDING

		// PPOB PULSA DATA PRABAYAR
		
		public function getPPOBPulsaPrabayarProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobpulsaprabayarproduct'		=> "",
			);
		
			
			$datafilter = array (
				'type_pulsa' 		=> $this->input->post('type_pulsa',true),
				'user_id' 			=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$data = array (
				'msisdn' 			=> $this->input->post('phone_number',true),
				// 'msisdn' 			=> '085728528036',
			);

			$inquiry_data = $this->PpobPulsaPrabayar_model->inquiry($data);



			if($inquiry_data['errCode'] == 00){
				if($datafilter['type_pulsa'] == 0){
					$no = 0;
					foreach ($inquiry_data as $key => $val){
						$explode_voucher = explode(" ",$val['voucher']);
	
						// print_r($explode_voucher);
						
						if(count($explode_voucher) == 1){
							
							$ppob_product_type	= 'Pulsa';
	
							$ppobpulsaprabayarproduct[$no]['ppob_product_type']		= $ppob_product_type;
							$ppobpulsaprabayarproduct[$no]['ppob_product_code']		= $val['product_id'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_name']		= $val['voucher'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_cost']		= $val['nominal'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_price']	= $val['price'];

							$no++;
						}
					}
				} else {
					$no = 0;
					foreach ($inquiry_data as $key => $val){
						$explode_voucher = explode(" ",$val['voucher']);
	
						// print_r($explode_voucher);
						
						if(count($explode_voucher) > 1){
							
							$ppob_product_type	= 'Data';
	
							$ppobpulsaprabayarproduct[$no]['ppob_product_type']		= $ppob_product_type;
							$ppobpulsaprabayarproduct[$no]['ppob_product_code']		= $val['product_id'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_name']		= $val['voucher'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_cost']		= $val['nominal'];
							$ppobpulsaprabayarproduct[$no]['ppob_product_price']	= $val['price'];

							$no++;
						}
					}
				}

				$response['error'] 								= FALSE;
				$response['error_msg_title'] 					= "Success";
				$response['error_msg'] 							= "Data Exist";
				$response['ppob_balance'] 						= $ppob_balance;
				$response['ppobpulsaprabayarproduct'] 			= $ppobpulsaprabayarproduct;
			} else {
				$response['error'] 								= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= $val;
				$response['ppob_balance'] 						= $ppob_balance;
			}

			echo json_encode($response);
		}

		public function processPaymentPPOBPulsaData(){
			$response = array(
				'error'											=> FALSE,
				'error_paymentppobpulsaprabayar'				=> FALSE,
				'error_msg_title_paymentppobpulsaprabayar'		=> "",
				'error_msg_paymentppobpulsaprabayar'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= $this->input->post('productID',true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$ppob_product_price 		= $this->input->post('productPrice',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			if($ppob_balance < $ppob_product_price){

				$response['error_paymentppobpulsaprabayar'] 	= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Saldo Anda tidak mencukupi";

			} else {
				$data = array (
					'product_id'        => $ppob_product_code,
					'msisdn'            => $this->input->post('phone_number',true),
					'purchase_amount'   => $this->input->post('productPrice',true)
				);
	
				$payment_data = $this->PpobPulsaPrabayar_model->payment($data);

				// print_r($payment_data);exit;
	
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['errCode'] == $key){
	
						if($payment_data['errCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' VoucherSN '.$payment_data['VoucherSN'].' Nomor HP '.$payment_data['msisdn'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsaprabayar'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' VoucherSN '.$payment_data['VoucherSN'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsaprabayar'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						} else {
							$response['error_paymentppobpulsaprabayar'] 	= TRUE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB PULSA DATA PRABAYAR

		// PPOB PULSA PASCABAYAR

		public function getPPOBPulsaPascabayar(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobpulsapascabayar'			=> "",
			);
			
			$ppob_product_category_id 	= 33;
			$ppobproductlist 			= $this->PPOBCompany_model->getPPOBProduct($ppob_product_category_id);
			
			$ppobpulsapascabayar[0]['typeproduct']				= 'TELKOM';
			$ppobpulsapascabayar[0]['ppob_product_category_id']	= 35;
			$ppobpulsapascabayar[0]['ppob_product_id']			= 0;
			$ppobpulsapascabayar[0]['ppob_product_code']		= '';
			$ppobpulsapascabayar[0]['ppob_product_name']		= 'TELKOM';
		
			$no = 1;
			foreach($ppobproductlist as $key => $val){
				$ppobpulsapascabayar[$no]['typeproduct']					= 'PULSA PASCABAYAR';
				$ppobpulsapascabayar[$no]['ppob_product_category_id']	= $val['ppob_product_category_id'];
				$ppobpulsapascabayar[$no]['ppob_product_id']			= $val['ppob_product_id'];
				$ppobpulsapascabayar[$no]['ppob_product_code']			= $val['ppob_product_code'];
				$ppobpulsapascabayar[$no]['ppob_product_name']			= $val['ppob_product_name'];

				$no++;
			}
			
			$response['error'] 						= FALSE;
			$response['error_msg_title'] 			= "Success";
			$response['error_msg'] 					= "Data Exist";
			$response['ppobpulsapascabayar'] 		= $ppobpulsapascabayar;

			echo json_encode($response);
		}

		public function getPPOBPulsaPascabayarBill(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobpulsapascabayarbill'		=> "",
			);

			$datafilter = array (
				'user_id' 		=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$ppobresponstatus 	= $this->configuration->PpobResponeCode();
			
			$data = array (
				'productCode' 	=> $this->input->post('productCode', true),
				'idPel' 		=> $this->input->post('phone_number', true),
				'idPel2'	    => '',
			 	'miscData'	  	=> ''
			);

			$inquiry = $this->PpobPulsaPascabayar_model->inquiry($data);


			if($inquiry['responseCode'] == '00'){
				$ppobpulsapascabayarbill[0]['id_pelanggan']		= $inquiry['idPel'];
				$ppobpulsapascabayarbill[0]['nama']				= $inquiry['nama'];
				$ppobpulsapascabayarbill[0]['periode']			= $inquiry['periode'];
				$ppobpulsapascabayarbill[0]['jumlahTagihan']	= $inquiry['jumlahTagihan'];
				$ppobpulsapascabayarbill[0]['tagihan']			= $inquiry['tagihan'];
				$ppobpulsapascabayarbill[0]['admin']			= $inquiry['admin'];
				$ppobpulsapascabayarbill[0]['totalTagihan']		= $inquiry['totalTagihan'];
				$ppobpulsapascabayarbill[0]['refID']			= $inquiry['refID'];
				

				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobpulsapascabayarbill'] 		= $ppobpulsapascabayarbill;
			} else {

				$response['error'] 							= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Error";
				$response['ppob_balance'] 					= $ppob_balance;
			}

			echo json_encode($response);
		}

		public function processPaymentPPOBPulsaPascabayar(){
			$response = array(
				'error'											=> FALSE,
				'error_paymentppobpulsapascabayar'				=> FALSE,
				'error_msg_title_paymentppobpulsapascabayar'	=> "",
				'error_msg_paymentppobpulsapascabayar'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= $this->input->post('productCode', true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			$data = array (
				'productCode' 	=> $this->input->post('productCode', true),
				'refID' 		=> $this->input->post('refID', true),
				'nominal' 		=> $this->input->post('totalTagihan', true)
			);


			$payment_data = $this->PpobPulsaPascabayar_model->payment($data);

			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobpulsapascabayar'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['responseCode'] == $key){
	
						if($payment_data['responseCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$payment_data['idPel'].' Nama '.$payment_data['nama'].' Periode '.$payment_data['periode'].' No. Ref '.$payment_data['ref'].' Jumlah Tagihan '.$payment_data['jumlahTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsapascabayar'] 	= FALSE;
							$response['error_msg_title'] 					= "Success";
							$response['error_msg'] 							= "Data Exist";
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$payment_data['idPel'].' Nama '.$payment_data['nama'].' Periode '.$payment_data['periode'].' No. Ref '.$payment_data['ref'].' Jumlah Tagihan '.$payment_data['jumlahTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsapascabayar'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						} else {
							$response['error_paymentppobpulsapascabayar'] 	= TRUE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB PULSA PASCABAYAR

		// PPOB PULSA TELKOM

		public function getPPOBPulsaPascabayarTelkom(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobpulsatelkom'				=> "",
			);
			
			$ppob_product_category_id 	= 35;
			$ppobproductlist 			= $this->PPOBCompany_model->getPPOBProduct($ppob_product_category_id);
			

			foreach($ppobproductlist as $key => $val){
				$ppobpulsatelkom[$key]['ppob_product_category_id']	= $val['ppob_product_category_id'];
				$ppobpulsatelkom[$key]['ppob_product_id']			= $val['ppob_product_id'];
				$ppobpulsatelkom[$key]['ppob_product_code']			= $val['ppob_product_code'];
				$ppobpulsatelkom[$key]['ppob_product_name']			= $val['ppob_product_name'];
			}
			
			$response['error'] 					= FALSE;
			$response['error_msg_title'] 		= "Success";
			$response['error_msg'] 				= "Data Exist";
			$response['ppobpulsatelkom'] 		= $ppobpulsatelkom;

			echo json_encode($response);
		}

		public function getPPOBPulsaPascabayarTelkomBill(){
			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobpulsatelkomdata'		=> "",
				'ppobpulsatelkombill'		=> "",
			);

			$datafilter = array (
				'user_id' 		=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$ppobresponstatus 	= $this->configuration->PpobResponeCode();
			
			$data = array (
				'productCode' 	=> $this->input->post('productCode', true),
				'idPel' 		=> $this->input->post('phone_number', true),
				// 'productCode' 	=> 'TELKOMSELLHALO ',
				// 'idPel' 		=> '081177889001',
			);

			$inquiry = $this->PpobPaymentTelkomApi_model->inquiry($data);


			if($inquiry['responseCode'] == '00'){
				$ppobpulsatelkomdata[0]['id_pelanggan']		= $inquiry['idpel'];
				$ppobpulsatelkomdata[0]['nama']				= $inquiry['nama'];
				$ppobpulsatelkomdata[0]['kodeArea']			= $inquiry['kodeArea'];
				$ppobpulsatelkomdata[0]['jumlahTagihan']	= $inquiry['jumlahTagihan'];
				$ppobpulsatelkomdata[0]['divre']			= $inquiry['divre'];
				$ppobpulsatelkomdata[0]['totalTagihan']		= $inquiry['totalTagihan'];
				$ppobpulsatelkomdata[0]['refID']			= $inquiry['refID'];

				$detailtagihan = $inquiry['tagihan'];

				foreach($detailtagihan as $k => $v){
					$ppobpulsatelkombill[$k]['periodeTagihanTelkom']	= $v['periode'];
					$ppobpulsatelkombill[$k]['nilaiTagihanTelkom']		= $v['nilaiTagihan'];
					$ppobpulsatelkombill[$k]['adminTagihanTelkom']		= $v['admin'];
					$ppobpulsatelkombill[$k]['totalTagihanTelkom']		= $v['total'];
					$ppobpulsatelkombill[$k]['feeTagihanTelkom']		= $v['fee'];
				}
				

				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobpulsatelkomdata'] 			= $ppobpulsatelkomdata;
				$response['ppobpulsatelkombill'] 			= $ppobpulsatelkombill;
			} else {

				$response['error'] 							= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Error";
				$response['ppob_balance'] 					= $ppob_balance;
			}


			echo json_encode($response);
		}

		public function processPaymentPPOBPulsaPascabayarTelkom(){
			$response = array(
				'error'										=> FALSE,
				'error_paymentppobpulsatelkom'				=> FALSE,
				'error_msg_title_paymentppobpulsatelkom'	=> "",
				'error_msg_paymentppobpulsatelkom'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= $this->input->post('productCode', true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			$data = array (
				'productCode' 	=> $this->input->post('productCode', true),
				'refID' 		=> $this->input->post('refID', true),
				'nominal' 		=> $this->input->post('totalTagihan', true)
			);


			$payment_data = $this->PpobPaymentTelkomApi_model->payment($data);

			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobpulsatelkom'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['responseCode'] == $key){
	
						if($payment_data['responseCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$payment_data['idPel'].' Nama '.$payment_data['nama'].' Periode '.$payment_data['periode'].' No. Ref '.$payment_data['ref'].' Jumlah Tagihan '.$payment_data['jumlahTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsatelkom'] 	= FALSE;
							$response['error_msg_title'] 					= "Success";
							$response['error_msg'] 							= "Data Exist";
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$payment_data['idPel'].' Nama '.$payment_data['nama'].' Periode '.$payment_data['periode'].' No. Ref '.$payment_data['ref'].' Jumlah Tagihan '.$payment_data['jumlahTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobpulsatelkom'] 	= FALSE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						} else {
							$response['error_paymentppobpulsatelkom'] 	= TRUE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB PULSA TELKOM

		// PPOB PLN PREPAID

		public function getPPOBPLNPrepaidProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobplnprepaidproduct'			=> "",
			);

			$datafilter = array (
				'user_id' 			=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$ppobresponstatus 	= $this->configuration->PpobResponeCode();
			
			$data = array (
				'productCode' 	=> 'PLNPREPAIDB',
				'idPel' 		=> $this->input->post('id_pelanggan_pln', true),
				// 'idPel' 		=> '04000000001',
			);

			$inquiry = $this->PpobPlnPrepaid_model->inquiry($data);

			// print_r($inquiry);

			if($inquiry['responseCode'] == 00){
				$ppobplnprepaidproduct[0]['msn']			= $inquiry['data']['msn'];
				$ppobplnprepaidproduct[0]['id_pelanggan']	= $inquiry['data']['subscriberID'];
				$ppobplnprepaidproduct[0]['tarif']			= $inquiry['data']['tarif'];
				$ppobplnprepaidproduct[0]['daya']			= $inquiry['data']['daya'];
				$ppobplnprepaidproduct[0]['nama']			= $inquiry['data']['nama'];
				$ppobplnprepaidproduct[0]['admin']			= $inquiry['data']['admin'];
				$ppobplnprepaidproduct[0]['refID']			= $inquiry['refID'];
				
				$nominalPLN = $inquiry['powerPurchaseDenom'];
				
				foreach($nominalPLN as $key => $val){
					$ppobplnprepaidnominal[$key]['nominalPLN']	= $val;
				}
	
				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobplnprepaidproduct'] 			= $ppobplnprepaidproduct;
				$response['ppobplnprepaidnominal'] 			= $ppobplnprepaidnominal;
			} else {
				$response['error'] 							= TRUE;
				$response['error_msg_title'] 				= "COnfirm";
				$response['error_msg'] 						= "Error";
				$response['ppob_balance'] 					= $ppob_balance;
			}
			

			echo json_encode($response);
		}

		public function ccMasking($data) {
    		return substr($data, 0, 4)."-".substr($data, 4, 4)."-".substr($data,8, 4)."-".substr($data,12, 4)."-".substr($data,16, 4);
		}

		public function processPaymentPPOBPLNPrepaid(){
			$response = array(
				'error'										=> FALSE,
				'error_paymentppobplnprepaid'				=> FALSE,
				'error_msg_title_paymentppobplnprepaid'		=> "",
				'error_msg_paymentppobplnprepaid'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= 'PLNPREPAIDB';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$nominal 					= $this->input->post('nominalPLN', true);
			$by_admin 					= $this->input->post('adminPLN', true);
			$totalnominal				= $nominal + $by_admin;

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			$data = array (
				'productCode' 	=> 'PLNPREPAIDB',
				'refID' 		=> $this->input->post('refID', true),
				// 'refID' 		=> '38713447',
				'nominal' 		=> $totalnominal,
				'miscData'		=> ''
			);


			$payment_data = $this->PpobPlnPrepaid_model->payment($data);

			if($ppob_balance < $totalnominal){

				$response['error_paymentppobplnprepaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['responseCode'] == $key){
	
						if($payment_data['responseCode'] == 00){
							$ppob_transaction_status = 1;
	
							$token 	= $this->ccMasking($payment_data['data']['tokenNumber']);
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['data']['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name', true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $totalnominal,
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'ID Pelanggan '.$payment_data['data']['msn'].' Nama '.$payment_data['data']['nama'].' Tarif/Daya '.$payment_data['data']['tarif'].'/'.$payment_data['data']['daya'].' No. Ref '.$payment_data['data']['ref'].' Jumlah KWH '.$payment_data['data']['kwh'].' Token '.$token,
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobplnprepaid'] 	= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$token 	= $this->ccMasking($payment_data['data']['tokenNumber']);
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['data']['ref'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name', true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $totalnominal,
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'ID Pelanggan '.$payment_data['data']['msn'].' Nama '.$payment_data['data']['nama'].'<br> Tarif/Daya '.$payment_data['data']['tarif'].'/'.$payment_data['data']['daya'].' No. Ref '.$payment_data['data']['ref'].' Jumlah KWH '.$payment_data['data']['kwh'].' Token '.$token,
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobplnprepaid'] 	= FALSE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						} else {
							$response['error_paymentppobplnprepaid'] 	= TRUE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		public function getPPOBPLNPrepaidData(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobplnprepaiddata'			=> "",
			);
			
			$ppob_product_code 			= 'PLNPREPAIDB';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppobtransaction_plnprepaid	= $this->PPOBCompany_model->getPPOBTransaction_Product($ppob_company_id, $ppob_agen_id, $ppobproduct['ppob_product_id']);

			if(!$ppobtransaction_plnprepaid){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Error Query Data";
			}else{
				if (empty($ppobtransaction_plnprepaid)){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Data Does Not Exist";
				} else {
					foreach ($ppobtransaction_plnprepaid as $key => $val) {

						$ppobplnprepaiddata[$key]['ppob_transaction_id']			= $val['ppob_transaction_id'];
						$ppobplnprepaiddata[$key]['ppob_transaction_remark']		= $val['ppob_transaction_remark'];
					}
					
					$response['error'] 					= FALSE;
					$response['error_msg_title'] 		= "Success";
					$response['error_msg'] 				= "Data Exist";
					$response['ppobplnprepaiddata'] 	= $ppobplnprepaiddata;
				}
			}

			echo json_encode($response);
		}

		// END PPOB PLN PREPAID

		// PPOB PLN POSTPAID

		public function getPPOBPLNPostpaidProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobplnpostpaidproduct'		=> "",
			);

			$datafilter = array (
				'user_id' 			=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$ppobresponstatus 	= $this->configuration->PpobResponeCode();
			
			$data = array (
				'productCode' 	=> 'PLNPOSTPAIDB',
				'idPel' 		=> $this->input->post('id_pelanggan_pln', true),
				'idPel2' 		=> '',
				'miscData'		=> '',
				// 'idPel' 		=> '530000000001',
			);

			$inquiry = $this->PpobPlnPostpaid_model->inquiry($data);

			// print_r($inquiry);

			if($inquiry['responseCode'] == 00){
				$ppobplnpostpaidproduct[0]['id_pelanggan']	= $inquiry['subscriberID'];
				$ppobplnpostpaidproduct[0]['tarif']			= $inquiry['tarif'];
				$ppobplnpostpaidproduct[0]['daya']			= $inquiry['daya'];
				$ppobplnpostpaidproduct[0]['nama']			= $inquiry['nama'];
				$ppobplnpostpaidproduct[0]['totalTagihan']	= $inquiry['totalTagihan'];
				$ppobplnpostpaidproduct[0]['lembarTagihan']	= $inquiry['lembarTagihanTotal'];
				$ppobplnpostpaidproduct[0]['refID']			= $inquiry['refID'];
	
				$detilTagihan = $inquiry['detilTagihan'];
				
				foreach($detilTagihan as $key => $val){
					$ppobplnpostpaidbill[$key]['periodeTagihan']	= $val['periode'];
					$ppobplnpostpaidbill[$key]['nilaiTagihan']		= $val['nilaiTagihan'];
					$ppobplnpostpaidbill[$key]['dendaTagihan']		= $val['denda'];
					$ppobplnpostpaidbill[$key]['adminTagihan']		= $val['admin'];
					$ppobplnpostpaidbill[$key]['jumlahTagihan']		= $val['total'];
				}
				
				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobplnpostpaidproduct'] 		= $ppobplnpostpaidproduct;
				$response['ppobplnpostpaidbill'] 			= $ppobplnpostpaidbill;
			} else {
				$response['error'] 							= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Error";
				$response['ppob_balance'] 					= $ppob_balance;
			}

			

			echo json_encode($response);
		}

		public function processPaymentPPOBPLNPostpaid(){
			$response = array(
				'error'										=> FALSE,
				'error_paymentppobplnpostpaid'				=> FALSE,
				'error_msg_title_paymentppobplnpostpaid'	=> "",
				'error_msg_paymentppobplnpostpaid'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= 'PLNPOSTPAIDB';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			$data = array (
				'productCode' 	=> 'PLNPOSTPAIDB',
				'refID' 		=> $this->input->post('refID', true),
				'nominal' 		=> $this->input->post('totalTagihan', true),
				'miscData'		=> ''
			);


			$payment_data = $this->PpobPlnPostpaid_model->payment($data);

			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobplnpostpaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['responseCode'] == $key){
	
						if($payment_data['responseCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['refnumber'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'ID Pelanggan '.$payment_data['subscriberID'].' Nama '.$payment_data['nama'].' Tarif/Daya '.$payment_data['tarif'].'/'.$payment_data['daya'].' No. Ref '.$payment_data['refnumber'].' Lembar Tagihan '.$payment_data['lembarTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobplnpostpaid'] 	= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['refnumber'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $payment_data['totalTagihan'],
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'ppob_transaction_remark'	=> 'ID Pelanggan '.$payment_data['subscriberID'].' Nama '.$payment_data['nama'].' Tarif/Daya '.$payment_data['tarif'].'/'.$payment_data['daya'].' No. Ref '.$payment_data['refnumber'].' Lembar Tagihan '.$payment_data['lembarTagihan'],
								'created_id'				=> $this->input->post('member_id',true),
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobplnpostpaid'] 	= FALSE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						} else {
							$response['error_paymentppobplnpostpaid'] 	= TRUE;
							$response['error_msg_title'] 				= "Confirm";
							$response['error_msg'] 						= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB PLN POSTPAID

		// PPOB VOUCHER GAME
		
		public function getPPOBVoucherGameProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobvouchergameproduct'		=> "",
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $this->input->post('user_id', true);

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}
	

			$ppob_product_category_id 	= 32;
			$ppobproductlist 			= $this->PPOBCompany_model->getPPOBProduct($ppob_product_category_id);


			if(!empty($ppobproductlist)){

				foreach($ppobproductlist as $key => $val){
					$ppobvouchergameproduct[$key]['ppob_product_category_id']	= $val['ppob_product_category_id'];
					$ppobvouchergameproduct[$key]['ppob_product_id']			= $val['ppob_product_id'];
					$ppobvouchergameproduct[$key]['ppob_product_code']			= $val['ppob_product_code'];
					$ppobvouchergameproduct[$key]['ppob_product_name']			= $val['ppob_product_name'];
					$ppobvouchergameproduct[$key]['ppob_product_price']			= $val['ppob_product_price'];
				}
			

				$response['error'] 								= FALSE;
				$response['error_msg_title'] 					= "Success";
				$response['error_msg'] 							= "Data Exist";
				$response['ppobvouchergameproduct'] 			= $ppobvouchergameproduct;
				$response['ppob_balance']						= $ppob_balance;
			} else {
				$response['error'] 								= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Data Kosong";
				$response['ppob_balance']						= $ppob_balance;
			}


			echo json_encode($response);
		}

		public function processPaymentPPOBVoucherGame(){
			$response = array(
				'error'									=> FALSE,
				'error_paymentvouchergame'				=> FALSE,
				'error_msg_title_paymentvouchergame'	=> "",
				'error_msg_paymentvouchergame'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= $this->input->post('productCode',true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$ppob_product_price 		= $this->input->post('productPrice',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			if($ppob_balance < $ppob_product_price){

				$response['error_paymentvouchergame'] 	= TRUE;
				$response['error_msg_title'] 			= "Confirm";
				$response['error_msg'] 					= "Saldo Anda tidak mencukupi";

			} else {
				$data = array (
					'idPel'         => $this->input->post('id_pelanggan',true),
					'productCode'   => $this->input->post('productCode',true),
					// 'idPel'         => '53299384993',
					// 'productCode'   => 'BSF10',
					'idPel2'        => '',
					'miscData'      => ''
				);
	
				$payment_data = $this->PpobPaymentTopUpApi_model->topup($data);

				// print_r($payment_data);exit;
	
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['errCode'] == $key){
	
						if($payment_data['errCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' Voucher Code '.$payment_data['voucher'].' ID Pelanggan '.$payment_data['idpel'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$payment_data['ref'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentvouchergame'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' Voucher Code '.$payment_data['voucher'].' ID Pelanggan '.$payment_data['idpel'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$payment_data['ref'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentvouchergame'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						} else {
							$response['error_paymentvouchergame'] 	= TRUE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB VOUCHER GAME

		// PPOB TOPUP EMONEY
		
		public function getPPOBTopUpEmoney(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'ppobtopupemoney'		=> "",
			);

		
			$ppobtopupemoney[0]['ppob_product_category_id']		= 28;
			$ppobtopupemoney[0]['ppob_product_category_name']	= 'Topup Dana';
			$ppobtopupemoney[1]['ppob_product_category_id']		= 29;
			$ppobtopupemoney[1]['ppob_product_category_name']	= 'Topup OVO';
			$ppobtopupemoney[2]['ppob_product_category_id']		= 30;
			$ppobtopupemoney[2]['ppob_product_category_name']	= 'Topup GoPay';
			$ppobtopupemoney[3]['ppob_product_category_id']		= 31;
			$ppobtopupemoney[3]['ppob_product_category_name']	= 'Topup E-Toll';

		

			$response['error'] 						= FALSE;
			$response['error_msg_title'] 			= "Success";
			$response['error_msg'] 					= "Data Exist";
			$response['ppobtopupemoney'] 			= $ppobtopupemoney;



			echo json_encode($response);
		}

		public function getPPOBTopUpEmoneyProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobtopupemoneyproduct'		=> "",
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $this->input->post('user_id', true);

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}
	

			$ppob_product_category_id 	= $this->input->post('ppob_product_category_id', true);
			$ppobproductlist 			= $this->PPOBCompany_model->getPPOBProduct($ppob_product_category_id);


			if(!empty($ppobproductlist)){

				foreach($ppobproductlist as $key => $val){
					$ppobtopupemoneyproduct[$key]['ppob_product_category_id']	= $val['ppob_product_category_id'];
					$ppobtopupemoneyproduct[$key]['ppob_product_id']			= $val['ppob_product_id'];
					$ppobtopupemoneyproduct[$key]['ppob_product_code']			= $val['ppob_product_code'];
					$ppobtopupemoneyproduct[$key]['ppob_product_name']			= $val['ppob_product_name'];
					$ppobtopupemoneyproduct[$key]['ppob_product_price']			= $val['ppob_product_price'];
				}
			

				$response['error'] 								= FALSE;
				$response['error_msg_title'] 					= "Success";
				$response['error_msg'] 							= "Data Exist";
				$response['ppobtopupemoneyproduct'] 			= $ppobtopupemoneyproduct;
				$response['ppob_balance']						= $ppob_balance;
			} else {
				$response['error'] 								= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Data Kosong";
				$response['ppob_balance']						= $ppob_balance;
			}


			echo json_encode($response);
		}

		public function processPaymentPPOBTopUpEmoney(){
			$response = array(
				'error'									=> FALSE,
				'error_paymenttopupemoney'				=> FALSE,
				'error_msg_title_paymenttopupemoney'	=> "",
				'error_msg_paymenttopupemoney'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= $this->input->post('productCode',true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$ppob_product_price 		= $this->input->post('productPrice',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			if($ppob_balance < $ppob_product_price){

				$response['error_paymenttopupemoney'] 	= TRUE;
				$response['error_msg_title'] 			= "Confirm";
				$response['error_msg'] 					= "Saldo Anda tidak mencukupi";

			} else {
				$data = array (
					'idPel'         => $this->input->post('id_pelanggan',true),
					'productCode'   => $this->input->post('productCode',true),
					// 'idPel'         => '53299384993',
					// 'productCode'   => 'BSF10',
					'idPel2'        => '',
					'miscData'      => ''
				);
	
				$payment_data = $this->PpobPaymentTopUpApi_model->topup($data);

				// print_r($payment_data);exit;
	
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['errCode'] == $key){
	
						if($payment_data['errCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' Voucher Code '.$payment_data['voucher'].' ID Pelanggan '.$payment_data['idpel'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$payment_data['ref'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymenttopupemoney'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
	
						} else if($payment_data['errCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['trxID'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('productPrice',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'trxID '.$payment_data['trxID'].' Voucher Code '.$payment_data['voucher'].' ID Pelanggan '.$payment_data['idpel'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$payment_data['ref'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymenttopupemoney'] 	= FALSE;
							$response['error_msg_title'] 			= "Confirm";
							$response['error_msg'] 					= $val;
						} else {
							$response['error_paymenttopupemoney'] 	= TRUE;
							$response['error_msg_title'] 			= "Confirm";
							$response['error_msg'] 					= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB TOPUP EMONEY

		// PPOB BPJS KESEHATAN
		
		public function getPPOBBPJSKesehatanProduct(){
			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobbpjskesehatanproduct'		=> "",
			);

			$datafilter = array (
				'user_id' 			=> $this->input->post('user_id',true),
			);

			$database 			= $this->db->database;

			$ppob_company_id	= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id		= $datafilter['user_id'];

			$ppob_balance 		= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			if(empty($ppob_balance)){
				$ppob_balance 	= 0;
			}

			$ppobresponstatus 	= $this->configuration->PpobResponeCode();
			
			$data = array (
				'productCode' 	=> 'BPJSKES',
				'idPel' 		=> $this->input->post('noVA', true),
				'idPel2' 		=> $this->input->post('jmlBulan', true),
				'miscData'		=> $this->input->post('phone_number', true),
			
				// 'idPel' 		=> '0001436861946',
				// 'idPel2' 		=> 1,
				// 'miscData'		=> '08123456789',
			);

			$inquiry = $this->PpobPaymentBpjs_model->inquiry($data);

			// print_r($inquiry); exit;

			if($inquiry['responseCode'] == 00){
				$ppobbpjskesehatanproduct[0]['noVA']			= $inquiry['noVA'];
				$ppobbpjskesehatanproduct[0]['nama']			= $inquiry['nama'];
				$ppobbpjskesehatanproduct[0]['namaCabang']		= $inquiry['namaCabang'];
				$ppobbpjskesehatanproduct[0]['jumlahPeriode']	= $inquiry['jumlahPeriode'];
				$ppobbpjskesehatanproduct[0]['jumlahPeserta']	= $inquiry['jumlahPeserta'];
				$ppobbpjskesehatanproduct[0]['nilaiTagihan']	= $inquiry['tagihan'];
				$ppobbpjskesehatanproduct[0]['adminTagihan']	= $inquiry['admin'];
				$ppobbpjskesehatanproduct[0]['totalTagihan']	= $inquiry['total'];
				$ppobbpjskesehatanproduct[0]['customerData']	= $inquiry['customerData'];
				$ppobbpjskesehatanproduct[0]['refID']			= $inquiry['refID'];
	
				$detailPeserta = $inquiry['detailPeserta'];
				
				foreach($detailPeserta as $key => $val){
					$ppobbpjskesehatanpeserta[$key]['noPeserta']		= $val['noPeserta'];
					$ppobbpjskesehatanpeserta[$key]['namaPeserta']		= $val['nama'];
					$ppobbpjskesehatanpeserta[$key]['premiPeserta']		= $val['premi'];
					$ppobbpjskesehatanpeserta[$key]['saldoPeserta']		= $val['saldo'];
				}
				
				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobbpjskesehatanproduct'] 		= $ppobbpjskesehatanproduct;
				$response['ppobbpjskesehatanpeserta'] 		= $ppobbpjskesehatanpeserta;
			} else {
				$response['error'] 							= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Error";
				$response['ppob_balance'] 					= $ppob_balance;
			}

			

			echo json_encode($response);
		}

		public function processPaymentPPOBBPJSKesehatan(){
			$response = array(
				'error'											=> FALSE,
				'error_paymentppobbpjskesehatan'				=> FALSE,
				'error_msg_title_paymentppobbpjskesehatan'		=> "",
				'error_msg_paymentppobbpjskesehatan'			=> "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= 'BPJSKES';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->PPOBCompany_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->PPOBCompany_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->PPOBCompany_model->getPPOBBalance($ppob_company_id, $ppob_agen_id);

			$totalTagihan 				= $this->input->post('totalTagihan',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			if($ppob_balance < $totalTagihan){

				$response['error_paymentppobbpjskesehatan'] 	= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Saldo Anda tidak mencukupi";

			} else {

				$data = array (
					'productCode'   => 'BPJSKES',
					'refID'			=> $this->input->post('refID',true),
					'nominal'       => $totalTagihan,
					// 'refID'			=> 38812721,
					// 'nominal'       => 155500,
					'miscData'      => ''
				);
	
				$payment_data = $this->PpobPaymentBpjs_model->payment($data);

				// print_r($payment_data);exit;
	
				foreach($ppobresponstatus as $key => $val){
					if($payment_data['responseCode'] == $key){
	
						if($payment_data['responseCode'] == 00){
							$ppob_transaction_status = 1;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['noReferensi'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('totalTagihan',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'No. VA '.$payment_data['noVA'].' Nama '.$payment_data['nama'].' Jumlah Peserta'.$ppobproduct['jumlahPeserta'].' Jumlah Periode '.$ppobproduct['jumlahPeriode'].' No. Referensi '.$payment_data['noReferensi'].' Customer Code '.$payment_data['customerCode'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobbpjskesehatan'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
	
						} else if($payment_data['responseCode'] == 99){
							$ppob_transaction_status = 2;
	
							$datappob_transaction = array (
								'ppob_unique_code'			=> $payment_data['noReferensi'],
								'ppob_company_id'			=> $ppob_company_id,
								'ppob_agen_id'				=> $this->input->post('member_id',true),
								'ppob_agen_name'			=> $this->input->post('member_name',true),
								'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
								'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
								'ppob_transaction_amount'	=> $this->input->post('totalTagihan',true),
								'ppob_transaction_date'		=> date('Y-m-d'),
								'ppob_transaction_status'	=> $ppob_transaction_status,
								'created_id'				=> $this->input->post('member_id',true),
								'ppob_transaction_remark'	=> 'No. VA '.$payment_data['noVA'].' Nama '.$payment_data['nama'].' Jumlah Peserta'.$ppobproduct['jumlahPeserta'].' Jumlah Periode '.$ppobproduct['jumlahPeriode'].' No. Referensi '.$payment_data['noReferensi'].' Customer Code '.$payment_data['customerCode'],
								'created_on'				=> date('Y-m-d H:i:s')
							);
				
							$this->PPOBCompany_model->insertPPOBTransaction($datappob_transaction);
				
							$response['error_paymentppobbpjskesehatan'] 	= FALSE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						} else {
							$response['error_paymentppobbpjskesehatan'] 	= TRUE;
							$response['error_msg_title'] 					= "Confirm";
							$response['error_msg'] 							= $val;
						}
					}
				}
			}

			echo json_encode($response);
		}

		// END PPOB BPJS KESEHATAN
		
	}
?>