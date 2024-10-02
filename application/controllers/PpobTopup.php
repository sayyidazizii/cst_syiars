<?php
	Class PpobTopup extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PpobTopup_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
			require 'vendor/autoload.php';
		}
		
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$sesi	= $this->session->userdata('filter-ppobtopup');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				
			}
			$this->session->unset_userdata('ppobtopuptoken-'.$unique['unique']);
			$this->session->unset_userdata('addPpobTopup-'.$unique['unique']);

			$start_date = tgltodb($sesi['start_date']);
			$end_date	= tgltodb($sesi['end_date']);

			$data['main_view']['corebranch']		= create_double($this->PpobTopup_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['ppobtopup']			= $this->PpobTopup_model->getPpobTopup($start_date, $end_date);

			$data['main_view']['content']			= 'PpobTopup/ListPpobTopup_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
			);

			$this->session->set_userdata('filter-ppobtopup',$data);
			redirect('PpobTopup');
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPpobTopup-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addPpobTopup-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addPpobTopup-'.$unique['unique']);
			redirect('PpobTopup/addPpobTopup');
		}
		
		public function addPpobTopup(){

			$date = date('Y-m-d');

			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('ppobtopuptoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('ppobtopuptoken-'.$unique['unique'], $token);
			}

			//grab database default
			$database 				= $this->db->database;

			/* print_r("database ");
			print_r($database);
			exit; */

			//company_id madani dari database ciptasolutindo
			$ppobcompany 		= $this->PpobTopup_model->getPpobCompanyID($database);

			//saldo ppob madani dari database ciptasolutindo
			$ppob_company_balance 	= $this->PpobTopup_model->getPpobCompanyBalance($ppobcompany['ppob_company_id']);

			$data['main_view']['corebranch']			= create_double($this->PpobTopup_model->getCoreBranch(),'branch_id','branch_name');

			$data['main_view']['acctaccount']			= create_double($this->PpobTopup_model->getAcctAccount(),'account_id','account_code');

			$data['main_view']['ppob_company_balance']	= $ppob_company_balance;

			$data['main_view']['content']				= 'PpobTopup/FormAddPpobTopupNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getTopupAmountBranch(){
			$branch_id = $this->input->post('branch_id');

			$data = $this->PpobTopup_model->getTopupBranchBalance($branch_id);

				$result = array();
				$result = array(
					"branch_id"					=> trim($branch_id,' '), 
					"topup_branch_balance"		=> $data['topup_branch_balance'], 
				);
			echo json_encode($result);
		
		}

		public function processAddPpobTopup(){
			$unique 						= $this->session->userdata('unique');
			$auth							= $this->session->userdata('auth');

			//grab database default
			$database 						= $this->db->database;

			//company_id madani dari database ciptasolutindo
			$ppobcompany 					= $this->PpobTopup_model->getPpobCompanyID($database);
			$ppob_company_id 				= $ppobcompany['ppob_company_id'];
			$ppob_company_code 				= $ppobcompany['ppob_company_code'];

			

			$data_ppob = array(
				'account_id'			=> $this->input->post('account_id', true),
				'branch_id'				=> $this->input->post('branch_id', true),
				'ppob_company_id'		=> $ppob_company_id,
				'ppob_company_code'		=> $ppob_company_code,
				'ppob_topup_date'		=> date('Y-m-d'),
				'ppob_topup_amount'		=> $this->input->post('ppob_topup_amount', true),
				'ppob_topup_remark'		=> $this->input->post('ppob_topup_remark', true),
				'ppob_topup_token'		=> $this->input->post('ppob_topup_token', true),
				'created_id'			=> $auth['user_id'],
				'created_on'			=> date('Y-m-d H:i:s'),
			);

			
			$this->form_validation->set_rules('branch_id', 'Cabang', 'required');
			$this->form_validation->set_rules('account_id', 'Kas / Bank', 'required');
			$this->form_validation->set_rules('ppob_topup_amount', 'Jumlah Top Up', 'required');

			//Cek Token Topup PPOB
			$ppob_topup_token			= $this->PpobTopup_model->getPpobTopupToken($data_ppob['ppob_topup_token']);

			$branch_name				= $this->PpobTopup_model->getBranchName($data_ppob['branch_id']);
			
			$transaction_module_code 	= "PPOB";
			$transaction_module_id 		= $this->PpobTopup_model->getTransactionModuleID($transaction_module_code);

			if($this->form_validation->run()==true){
				if($ppob_topup_token->num_rows() == 0){
					//Jika Token PPOB 0
					$client     = new GuzzleHttp\Client();
					$url        = 'http://127.0.0.1:8000/api/ppob/topup';
					try {
						# guzzle post request example with form parameter
						$response = $client->request( 'POST', $url, [ 
													'form_params' 
															=> [ 
															'account_id' 		=> $data_ppob["account_id"],
															'branch_id' 		=> $data_ppob["branch_id"],
															'ppob_company_id' 	=> $data_ppob["ppob_company_id"],
															'ppob_company_code' => $data_ppob["ppob_company_code"],
															'ppob_topup_date' 	=> $data_ppob["ppob_topup_date"],
															'ppob_topup_amount' => $data_ppob["ppob_topup_amount"],
															'ppob_topup_remark' => $data_ppob["ppob_topup_remark"],
															'ppob_topup_token' 	=> $data_ppob["ppob_topup_token"],
															'created_id' 		=> $data_ppob["created_id"], 
															] 
													]
													);
						$status_code = $response->getStatusCode();
						$response_data = $response->getBody()->getContents();
						if($status_code == 201){
							
							//ppob_topup_id terakhir
							$ppobtopup 		= $this->PpobTopup_model->getLastPPOBTopUp($data_ppob['created_id']);

							//data topup yang akan disimpan di database ciptasolutindo
							$data_cipta = array(
								'ppob_company_id'				=> $ppob_company_id,
								'ppob_topup_id'					=> $ppobtopup['ppob_topup_id'],
								'ppob_topup_no'					=> $ppobtopup['ppob_topup_no'],
								'ppob_topup_company_date'		=> date('Y-m-d'),
								'ppob_topup_company_opening'	=> $this->input->post('ppob_topup_balance', true),
								'ppob_topup_company_amount'		=> $this->input->post('ppob_topup_amount', true),
								'ppob_topup_company_balance'	=> $this->input->post('ppob_topup_balance', true) + $this->input->post('ppob_topup_amount', true),
								'ppob_topup_company_token'		=> $this->input->post('ppob_topup_token', true),
								'created_id'					=> $auth['user_id'],
								'created_on'					=> date('Y-m-d H:i:s'),
							);
				

							if($this->PpobTopup_model->insertPpobTopUpCipta($data_cipta)){

								//start JURNAL
								$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_date']));

								$ppobtopup_last 			= $this->PpobTopup_model->getPpobTopup_Last($data_ppob['created_id']);

								$data_journal = array(
									'branch_id'							=> $data_ppob['branch_id'],
									'journal_voucher_period' 			=> $journal_voucher_period,
									'journal_voucher_date'				=> date('Y-m-d'),
									'journal_voucher_title'				=> 'TOP UP PPOB '.$branch_name,
									'journal_voucher_description'		=> 'TOP UP PPOB '.$branch_name,
									'journal_voucher_token'				=> $data_ppob['ppob_topup_token'],
									'transaction_module_id'				=> $transaction_module_id,
									'transaction_module_code'			=> $transaction_module_code,
									'transaction_journal_id' 			=> $ppobtopup_last['ppob_topup_id'],
									'transaction_journal_no' 			=> $ppobtopup_last['ppob_topup_no'],
									'created_id' 						=> $data_ppob['created_id'],
									'created_on' 						=> $data_ppob['created_on'],
								);

								$journal_voucher_token 					= $this->PpobTopup_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

								if($journal_voucher_token->num_rows() == 0){
									$this->PpobTopup_model->insertAcctJournalVoucher($data_journal);
								}

								$journal_voucher_id 					= $this->PpobTopup_model->getJournalVoucherID($data_journal['created_id']);

								//DEBET
								$preferenceppob 						= $this->PpobTopup_model->getPreferencePpob();

								$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

								$data_debet = array (
									'journal_voucher_id'				=> $journal_voucher_id,
									'account_id'						=> $preferenceppob['ppob_account_down_payment'],
									'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
									'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
									'journal_voucher_debit_amount'		=> $data_ppob['ppob_topup_amount'],
									'account_id_default_status'			=> $account_id_default_status,
									'account_id_status'					=> 0,
									'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$preferenceppob['ppob_account_down_payment'],
								);

								$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->PpobTopup_model->insertAcctJournalVoucherItem($data_debet);
								}

								//KREDIT
								$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($data_ppob['account_id']);

								$data_credit =array(
									'journal_voucher_id'				=> $journal_voucher_id,
									'account_id'						=> $data_ppob['account_id'],
									'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
									'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
									'journal_voucher_credit_amount'		=> $data_ppob['ppob_topup_amount'],
									'account_id_default_status'			=> $account_id_default_status,
									'account_id_status'					=> 1,
									'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$data_ppob['account_id'],
								);

								$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->PpobTopup_model->insertAcctJournalVoucherItem($data_credit);
								}
								//end JURNAL

								$auth = $this->session->userdata('auth');
								$msg = "<div class='alert alert-success alert-dismissable'>  
										<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
											Tambah Top Up PPOB Sukses
										</div> ";
								$sesi = $this->session->userdata('unique');
								$this->session->unset_userdata('addPpobTopup-'.$sesi['unique']);
								$this->session->unset_userdata('ppobtopuptoken-'.$sesi['unique']);
								$this->session->set_userdata('message',$msg);
								redirect('PpobTopup/addPpobTopup');
							} else {
								$this->session->set_userdata('addPpobTopup',$data_ppob);
								$msg = "<div class='alert alert-danger alert-dismissable'>
										<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
											Tambah Top Up PPOB Tidak Berhasil
										</div> ";
								$this->session->set_userdata('message',$msg);
								redirect('PpobTopup/addPpobTopup');
							}
						} else{
							$this->session->set_userdata('addPpobTopup',$data_ppob);
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Top Up PPOB Tidak Berhasilz
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('PpobTopup/addPpobTopup');
						}
					} catch (GuzzleHttp\Exception\BadResponseException $e) {
						#guzzle repose for future use
						$response = $e->getResponse();
						
						$this->session->set_userdata('addPpobTopup',$data_ppob);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Top Up PPOB Tidak Berhasila
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('PpobTopup/addPpobTopup');
					}


					if($this->PpobTopup_model->insertPpobTopup($data_ppob)){
						
						//ppob_topup_id terakhir
						$ppobtopup 		= $this->PpobTopup_model->getLastPPOBTopUp($data_ppob['created_id']);

						//data topup yang akan disimpan di database ciptasolutindo
						$data_cipta = array(
							'ppob_company_id'				=> $ppob_company_id,
							'ppob_topup_id'					=> $ppobtopup['ppob_topup_id'],
							'ppob_topup_no'					=> $ppobtopup['ppob_topup_no'],
							'ppob_topup_company_date'		=> date('Y-m-d'),
							'ppob_topup_company_opening'	=> $this->input->post('ppob_topup_balance', true),
							'ppob_topup_company_amount'		=> $this->input->post('ppob_topup_amount', true),
							'ppob_topup_company_balance'	=> $this->input->post('ppob_topup_balance', true) + $this->input->post('ppob_topup_amount', true),
							'ppob_topup_company_token'		=> $this->input->post('ppob_topup_token', true),
							'created_id'					=> $auth['user_id'],
							'created_on'					=> date('Y-m-d H:i:s'),
						);
			

						if($this->PpobTopup_model->insertPpobTopUpCipta($data_cipta)){

							//start JURNAL
							$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_date']));

							$ppobtopup_last 			= $this->PpobTopup_model->getPpobTopup_Last($data_ppob['created_id']);

							$data_journal = array(
								'branch_id'							=> $data_ppob['branch_id'],
								'journal_voucher_period' 			=> $journal_voucher_period,
								'journal_voucher_date'				=> date('Y-m-d'),
								'journal_voucher_title'				=> 'TOP UP PPOB '.$branch_name,
								'journal_voucher_description'		=> 'TOP UP PPOB '.$branch_name,
								'journal_voucher_token'				=> $data_ppob['ppob_topup_token'],
								'transaction_module_id'				=> $transaction_module_id,
								'transaction_module_code'			=> $transaction_module_code,
								'transaction_journal_id' 			=> $ppobtopup_last['ppob_topup_id'],
								'transaction_journal_no' 			=> $ppobtopup_last['ppob_topup_no'],
								'created_id' 						=> $data_ppob['created_id'],
								'created_on' 						=> $data_ppob['created_on'],
							);

							$journal_voucher_token 					= $this->PpobTopup_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

							if($journal_voucher_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucher($data_journal);
							}

							$journal_voucher_id 					= $this->PpobTopup_model->getJournalVoucherID($data_journal['created_id']);

							//DEBET
							$preferenceppob 						= $this->PpobTopup_model->getPreferencePpob();

							$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

							$data_debet = array (
								'journal_voucher_id'				=> $journal_voucher_id,
								'account_id'						=> $preferenceppob['ppob_account_down_payment'],
								'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
								'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
								'journal_voucher_debit_amount'		=> $data_ppob['ppob_topup_amount'],
								'account_id_default_status'			=> $account_id_default_status,
								'account_id_status'					=> 0,
								'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$preferenceppob['ppob_account_down_payment'],
							);

							$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucherItem($data_debet);
							}

							//KREDIT
							$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($data_ppob['account_id']);

							$data_credit =array(
								'journal_voucher_id'				=> $journal_voucher_id,
								'account_id'						=> $data_ppob['account_id'],
								'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
								'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
								'journal_voucher_credit_amount'		=> $data_ppob['ppob_topup_amount'],
								'account_id_default_status'			=> $account_id_default_status,
								'account_id_status'					=> 1,
								'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$data_ppob['account_id'],
							);

							$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucherItem($data_credit);
							}
							//end JURNAL

							$auth = $this->session->userdata('auth');
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Top Up PPOB Sukses
									</div> ";
							$sesi = $this->session->userdata('unique');
							$this->session->unset_userdata('addPpobTopup-'.$sesi['unique']);
							$this->session->unset_userdata('ppobtopuptoken-'.$sesi['unique']);
							$this->session->set_userdata('message',$msg);
							redirect('PpobTopup/addPpobTopup');
						} else {
							$this->session->set_userdata('addPpobTopup',$data_ppob);
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Top Up PPOB Tidak Berhasil
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('PpobTopup/addPpobTopup');
						}
					} else {
						$this->session->set_userdata('addPpobTopup',$data_ppob);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Top Up PPOB Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('PpobTopup/addPpobTopup');
					}
				} else {
					//ppob_topup_id terakhir
					$ppobtopup 		= $this->PpobTopup_model->getLastPPOBTopUp($data_ppob['created_id']);

					//grab database default
					$database 			= $this->db->database;

					//company_id madani dari database ciptasolutindo
					$ppob_company_id 	= $this->PpobTopup_model->getPpobCompanyID($database);


					//data topup yang akan disimpan di database ciptasolutindo
					$data_cipta = array(
						'ppob_company_id'				=> $ppob_company_id,
						'ppob_topup_id'					=> $ppobtopup['ppob_topup_id'],
						'ppob_topup_no'					=> $ppobtopup['ppob_topup_no'],
						'ppob_topup_company_date'		=> date('Y-m-d'),
						'ppob_topup_company_opening'	=> $this->input->post('ppob_topup_balance', true),
						'ppob_topup_company_amount'		=> $this->input->post('ppob_topup_amount', true),
						'ppob_topup_company_balance'	=> $this->input->post('ppob_topup_balance', true) + $this->input->post('ppob_topup_amount', true),
						'ppob_topup_company_token'		=> $this->input->post('ppob_topup_token', true),
						'created_id'					=> $auth['user_id'],
						'created_on'					=> date('Y-m-d H:i:s'),
					);

					$ppob_topup_company_token 		= $this->PpobTopup_model->getPpobTopupCompanyToken($data_cipta['ppob_topup_company_token']);
		
					if(empty($ppob_topup_company_token)){
						if($this->PpobTopup_model->insertPpobTopUpCipta($data_cipta)){

							//start JURNAL
							$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_date']));
	
							$ppobtopup_last 			= $this->PpobTopup_model->getPpobTopup_Last($data_ppob['created_id']);
	
							$data_journal = array(
								'branch_id'							=> $data_ppob['branch_id'],
								'journal_voucher_period' 			=> $journal_voucher_period,
								'journal_voucher_date'				=> date('Y-m-d'),
								'journal_voucher_title'				=> 'TOP UP PPOB '.$branch_name,
								'journal_voucher_description'		=> 'TOP UP PPOB '.$branch_name,
								'journal_voucher_token'				=> $data_ppob['ppob_topup_token'],
								'transaction_module_id'				=> $transaction_module_id,
								'transaction_module_code'			=> $transaction_module_code,
								'transaction_journal_id' 			=> $ppobtopup_last['ppob_topup_id'],
								'transaction_journal_no' 			=> $ppobtopup_last['ppob_topup_no'],
								'created_id' 						=> $data_ppob['created_id'],
								'created_on' 						=> $data_ppob['created_on'],
							);
	
							$journal_voucher_token 					= $this->PpobTopup_model->getJournalVoucherToken($data_journal['journal_voucher_token']);
	
							if($journal_voucher_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucher($data_journal);
							}
	
							$journal_voucher_id 					= $this->PpobTopup_model->getJournalVoucherID($data_journal['created_id']);
	
							//DEBET
							$preferenceppob 						= $this->PpobTopup_model->getPreferencePpob();
	
							$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);
	
							$data_debet = array (
								'journal_voucher_id'				=> $journal_voucher_id,
								'account_id'						=> $preferenceppob['ppob_account_down_payment'],
								'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
								'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
								'journal_voucher_debit_amount'		=> $data_ppob['ppob_topup_amount'],
								'account_id_default_status'			=> $account_id_default_status,
								'account_id_status'					=> 0,
								'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$preferenceppob['ppob_account_down_payment'],
							);
	
							$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);
	
							if($journal_voucher_item_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucherItem($data_debet);
							}
	
							//KREDIT
							$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($data_ppob['account_id']);
	
							$data_credit =array(
								'journal_voucher_id'				=> $journal_voucher_id,
								'account_id'						=> $data_ppob['account_id'],
								'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
								'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
								'journal_voucher_credit_amount'		=> $data_ppob['ppob_topup_amount'],
								'account_id_default_status'			=> $account_id_default_status,
								'account_id_status'					=> 1,
								'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$data_ppob['account_id'],
							);
	
							$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
							if($journal_voucher_item_token->num_rows() == 0){
								$this->PpobTopup_model->insertAcctJournalVoucherItem($data_credit);
							}
							//end JURNAL
	
							$auth = $this->session->userdata('auth');
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Top Up PPOB Sukses
									</div> ";
							$sesi = $this->session->userdata('unique');
							$this->session->unset_userdata('addPpobTopup-'.$sesi['unique']);
							$this->session->unset_userdata('ppobtopuptoken-'.$sesi['unique']);
							$this->session->set_userdata('message',$msg);
							redirect('PpobTopup/addPpobTopup');
						} else {
							$this->session->set_userdata('addPpobTopup',$data_ppob);
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Top Up PPOB Tidak Berhasil
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('PpobTopup/addPpobTopup');
						}
					} else {
						//start JURNAL
						$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_date']));
	
						$ppobtopup_last 			= $this->PpobTopup_model->getPpobTopup_Last($data_ppob['created_id']);

						$data_journal = array(
							'branch_id'							=> $data_ppob['branch_id'],
							'journal_voucher_period' 			=> $journal_voucher_period,
							'journal_voucher_date'				=> date('Y-m-d'),
							'journal_voucher_title'				=> 'TOP UP PPOB '.$branch_name,
							'journal_voucher_description'		=> 'TOP UP PPOB '.$branch_name,
							'journal_voucher_token'				=> $data_ppob['ppob_topup_token'],
							'transaction_module_id'				=> $transaction_module_id,
							'transaction_module_code'			=> $transaction_module_code,
							'transaction_journal_id' 			=> $ppobtopup_last['ppob_topup_id'],
							'transaction_journal_no' 			=> $ppobtopup_last['ppob_topup_no'],
							'created_id' 						=> $data_ppob['created_id'],
							'created_on' 						=> $data_ppob['created_on'],
						);

						$journal_voucher_token 					= $this->PpobTopup_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

						if($journal_voucher_token->num_rows() == 0){
							$this->PpobTopup_model->insertAcctJournalVoucher($data_journal);
						}

						$journal_voucher_id 					= $this->PpobTopup_model->getJournalVoucherID($data_journal['created_id']);

						//DEBET
						$preferenceppob 						= $this->PpobTopup_model->getPreferencePpob();

						$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

						$data_debet = array (
							'journal_voucher_id'				=> $journal_voucher_id,
							'account_id'						=> $preferenceppob['ppob_account_down_payment'],
							'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
							'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
							'journal_voucher_debit_amount'		=> $data_ppob['ppob_topup_amount'],
							'account_id_default_status'			=> $account_id_default_status,
							'account_id_status'					=> 0,
							'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$preferenceppob['ppob_account_down_payment'],
						);

						$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows() == 0){
							$this->PpobTopup_model->insertAcctJournalVoucherItem($data_debet);
						}

						//KREDIT
						$account_id_default_status 				= $this->PpobTopup_model->getAccountIDDefaultStatus($data_ppob['account_id']);

						$data_credit =array(
							'journal_voucher_id'				=> $journal_voucher_id,
							'account_id'						=> $data_ppob['account_id'],
							'journal_voucher_description'		=> 'Top Up PPOB '.$branch_name,
							'journal_voucher_amount'			=> $data_ppob['ppob_topup_amount'],
							'journal_voucher_credit_amount'		=> $data_ppob['ppob_topup_amount'],
							'account_id_default_status'			=> $account_id_default_status,
							'account_id_status'					=> 1,
							'journal_voucher_item_token'		=> $data_ppob['ppob_topup_token'].$data_ppob['account_id'],
						);

						$journal_voucher_item_token 			= $this->PpobTopup_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows() == 0){
							$this->PpobTopup_model->insertAcctJournalVoucherItem($data_credit);
						}
						//end JURNAL

						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Top Up PPOB Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addPpobTopup-'.$sesi['unique']);
						$this->session->unset_userdata('ppobtopuptoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('PpobTopup/addPpobTopup');
					}
					
				}
				
			} else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}

		public function showdetail(){
			$savings_transfer_ppob_id = $this->uri->segment(3);

			$data['main_view']['PpobTopup']		= $this->PpobTopup_model->getPpobTopup_Detail($savings_transfer_ppob_id);
			$data['main_view']['PpobTopupitem']	= $this->PpobTopup_model->getPpobTopupItem_Detail($savings_transfer_ppob_id);

			$data['main_view']['content']								= 'PpobTopup/FormDetailPpobTopup_view';
			$this->load->view('MainPage_view',$data);
		}				

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPpobTopup-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addPpobTopup-'.$unique['unique'],$sessions);
		}
	}
?>