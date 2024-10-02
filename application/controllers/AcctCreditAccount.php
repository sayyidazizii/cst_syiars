<?php
	Class AcctCreditAccount extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Core_account_Officer_model');
			$this->load->model('Core_source_fund_model');
			$this->load->model('AcctDepositoAccount_model');
			$this->load->model('AcctCredit_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('acctcreditsaccounttoken-'.$unique['unique']);

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccount/ListAcctCreditsAccount_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcreditsaccount(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsaccountlist', $data);
			redirect('AcctCreditAccount');
		}

		public function getAcctCreditsAccountList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsaccountlist');
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

			$list = $this->AcctCreditAccount_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $creditsaccount) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $creditsaccount->credits_account_serial;
				$row[] = $creditsaccount->member_name;
				$row[] = $creditsaccount->credits_name;
				$row[] = $creditsaccount->source_fund_name;
				$row[] = tgltoview($creditsaccount->credits_account_date);
				$row[] = number_format($creditsaccount->credits_account_financing, 2);
		//       if($creditsaccount->validation == 0){
		//       	$row[] = '<a href="'.base_url().'AcctDepositoAccount/printNoteAcctDepositoAccount/'.$creditsaccount->deposito_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>
				//      <a href="'.base_url().'AcctDepositoAccount/validationAcctDepositoAccount/'.$creditsaccount->deposito_account_id.'" class="btn btn-xs green-jungle" role="button"><i class="fa fa-check"></i> Validasi</a>';
				// } else {
					$row[] = '
						<a href="'.base_url().'AcctCreditAccount/printNoteAcctCreditAccount/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a> &nbsp;
						<a href="'.base_url().'AcctCreditAccount/processPrintingAkad/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Akad</a>';
				// }
				$data[] = $row;
			}



			// print_r($list);exit;
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->AcctCreditAccount_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
							"recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcreditaccount-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addcreditaccount-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addcreditaccount-'.$unique['unique']);
			$this->session->unset_userdata('addarrayacctcreditsagunan-'.$unique['unique']);
			$this->session->unset_userdata('acctcreditsaccounttoken-'.$unique['unique']);
			redirect('AcctCreditAccount/addform');
		}

		public function addform(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctcreditsaccounttoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date("YmdHis"));
				$this->session->set_userdata('acctcreditsaccounttoken-'.$unique['unique'], $token);
			}

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccount_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($this->uri->segment(3));
			$data['main_view']['acctsavingsaccount']		= $this->AcctSavingsAccount_model->getAcctSavingsAccount_Detail($this->uri->segment(4));
			$data['main_view']['creditid']					= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['content']					= 'AcctCreditAccount/FormAddAcctCreditAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getCreditsAccountSerial(){
			$auth = $this->session->userdata('auth');

			$credits_id 		= $this->input->post('credits_id');

			// $savings_id = 3;
			
			$branchcode 			= $this->AcctCreditAccount_model->getBranchCode($auth['branch_id']);
			$credits_code 			= $this->AcctCreditAccount_model->getCreditsCode($credits_id);
			$lastcreditsaccountno 	= $this->AcctCreditAccount_model->getLastAccountCreditsNo($auth['branch_id'], $credits_id);

			if($lastcreditsaccountno->num_rows() <> 0){      
				//jika kode ternyata sudah ada.      
				$data = $lastcreditsaccountno->row_array();    
				$kode = intval($data['last_credits_account_serial']) + 1;    
			} else {      
				//jika kode belum ada      
				$kode = 1;    
			}
			
			$kodemax 					= str_pad($kode, 5, "0", STR_PAD_LEFT);
			$new_credits_account_serial = $credits_code.$branchcode.$kodemax;

			$result = array ();
			$result = array (
				'credit_account_serial'		=> $new_credits_account_serial,
			);

			echo json_encode($result);		
		}

		public function memberlist(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$branch_id = '';
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				if($customers->member_status == 1){
					$row = array();
					$row[] = $no;
					$row[] = $customers->member_no;
					$row[] = $customers->member_name;
					$row[] = $customers->member_address;
					$row[] = '<a href="'.base_url().'AcctCreditAccount/addform/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
					$data[] = $row;
				}
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function savingslist(){
			$auth = $this->session->userdata('auth');
			$member_id = $this->uri->segment(3);
			$branch_id = '';
			$list = $this->AcctSavingsAccount_model->get_datatables($branch_id);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->savings_account_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = '<a href="'.base_url().'AcctCreditAccount/addform/'.$member_id.'/'.$customers->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->AcctSavingsAccount_model->count_all($branch_id),
							"recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function processAddArrayAgunan(){
			$date = date('Ymdhis');
			$credits_agunan_type 			= $this->input->post('tipe', true);


				$data_agunan = array(
					'record_id' 						=> $credits_agunan_type.$date,
					'credits_agunan_type' 				=> $this->input->post('tipe', true),
					'credits_agunan_bpkb_nomor' 		=> $this->input->post('bpkb_nomor', true),
					'credits_agunan_bpkb_nopol' 		=> $this->input->post('bpkb_nopol', true),
					'credits_agunan_bpkb_nama' 			=> $this->input->post('bpkb_nama', true),
					'credits_agunan_bpkb_no_mesin' 		=> $this->input->post('bpkb_no_mesin', true),
					'credits_agunan_bpkb_no_rangka'		=> $this->input->post('bpkb_no_rangka', true),
					'credits_agunan_bpkb_taksiran' 		=> $this->input->post('bpkb_taksiran', true),
					'credits_agunan_bpkb_keterangan'	=> $this->input->post('bpkb_keterangan', true),
					'credits_agunan_shm_no_sertifikat' 	=> $this->input->post('shm_no_sertifikat', true),
					'credits_agunan_shm_luas' 			=> $this->input->post('shm_luas', true),
					'credits_agunan_shm_atas_nama' 		=> $this->input->post('shm_atas_nama', true),
					'credits_agunan_shm_kedudukan' 		=> $this->input->post('shm_kedudukan', true),
					'credits_agunan_shm_taksiran' 		=> $this->input->post('shm_taksiran', true),
					'credits_agunan_shm_keterangan'		=> $this->input->post('shm_keterangan', true)
				);


			$unique 			= $this->session->userdata('unique');
			$session_name 		= $this->input->post('session_name',true);
			$dataArrayHeader	= $this->session->userdata('addarrayacctcreditsagunan-'.$unique['unique']);
			
			$dataArrayHeader[$data_agunan['record_id']] = $data_agunan;
			
			$this->session->set_userdata('addarrayacctcreditsagunan-'.$unique['unique'],$dataArrayHeader);
			// $sesi 	= $this->session->userdata('unique');
			// $data_agunan = $this->session->userdata('addacctcreditsagunan-'.$sesi['unique']);
			
			$data_agunan['record_id'] 								= '';
			$data_agunan['credits_agunan_bpkb_nomor'] 				= '';
			$data_agunan['credits_agunan_type'] 					= '';
			$data_agunan['credits_agunan_bpkb_nama'] 				= '';
			$data_agunan['credits_agunan_bpkb_nopol'] 				= '';
			$data_agunan['credits_agunan_bpkb_no_mesin'] 			= '';
			$data_agunan['credits_agunan_bpkb_no_rangka'] 			= '';
			$data_agunan['credits_agunan_bpkb_taksiran'] 			= '';
			$data_agunan['credits_agunan_bpkb_keterangan'] 			= '';
			$data_agunan['credits_agunan_shm_no_sertifikat'] 		= '';
			$data_agunan['credits_agunan_shm_luas'] 				= '';
			$data_agunan['credits_agunan_shm_atas_nama'] 			= '';
			$data_agunan['credits_agunan_shm_kedudukan'] 			= '';
			$data_agunan['credits_agunan_shm_taksiran'] 			= '';
			$data_agunan['credits_agunan_shm_keterangan'] 			= '';

			
			// $this->session->set_userdata('addacctcreditsagunan-'.$sesi['unique'],$data_agunan);
		}

		public function addcreditaccount(){
			$auth 			= $this->session->userdata('auth');
			$sesi 			= $this->session->userdata('unique');
			$daftaragunan 	= $this->session->userdata('addarrayacctcreditsagunan-'.$sesi['unique']);

			$agunan_data 	= $this->session->userdata('agunan_data');
			$agunan 		= $this->session->userdata('agunan_key');
			$a 				= json_encode($agunan_data);
			// print_r($this->session->userdata('agunan_data'));exit;
			$this->session->unset_userdata('agunan_data');
			$this->session->unset_userdata('agunan_key');

			$member_id 		= $this->input->post('member_id',true);
			if(empty($member_id)){
				$member_id 	= $this->uri->segment(3);
			}

			$credits_account_net_price = $this->input->post('credit_account_net_price',true);

			if(empty($credits_account_net_price) || $credits_account_net_price == 0){
				$credits_account_last_balance_principal 	= $this->input->post('credits_account_last_balance_principal',true);
			} else {
				$credits_account_last_balance_principal 	= $credits_account_net_price;
			}

			

			$credits_account_date 							= tgltodb($this->input->post('credit_account_date',true));

			$credits_account_payment_date 					= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_date)));

			$data = array (
				"credits_account_date" 						=> tgltodb($this->input->post('credit_account_date',true)),
				"member_id"									=> $this->input->post('member_id',true),
				"office_id"									=> $this->input->post('office_id',true),
				"source_fund_id"							=> $this->input->post('sumberdana',true),
				"credits_id"								=> $this->input->post('credit_id',true),
				"branch_id"									=> $auth['branch_id'],
				"credits_account_period"					=> $this->input->post('credit_account_period',true),
				"credits_account_due_date"					=>tgltodb($this->input->post('credit_account_due_date',true)),
				"credits_account_materai"					=> $this->input->post('credit_account_materai',true),
				"credits_account_serial"					=> $this->input->post('credit_account_serial',true),
				"credits_account_adm_cost"					=> $this->input->post('credit_account_adm_cost',true),
				"credits_account_net_price"					=> $this->input->post('credit_account_net_price',true),
				"credits_account_sell_price"				=> $this->input->post('credit_account_sell_price',true),
				"credits_account_um"						=> $this->input->post('credit_account_um',true),
				"credits_account_margin"					=> $this->input->post('credit_account_margin',true),
				"credits_account_financing"					=> $this->input->post('credits_account_last_balance_principal',true),
				"credits_account_nisbah_bmt"				=> $this->input->post('credit_account_nisbah_bmt',true),
				"credits_account_nisbah_agt"				=> $this->input->post('credit_account_nisbah_agt',true),
				"credits_account_notaris"					=> $this->input->post('credit_account_notaris',true),
				"credits_account_insurance"					=> $this->input->post('credit_account_insurance',true),
				"credits_account_principal_amount"			=> $this->input->post('credits_account_principal_amount',true),
				"credits_account_margin_amount"				=> $this->input->post('credits_account_margin_amount',true),
				"credits_account_payment_amount"			=> $this->input->post('credit_account_payment_amount',true),
				"credits_account_last_balance_principal"	=> $credits_account_last_balance_principal,
				"credits_account_last_balance_margin"		=> $this->input->post('credit_account_margin',true),
				"credits_account_payment_date"				=> $credits_account_payment_date,
				"savings_account_id"						=> $this->input->post('savings_account_id',true),
				"credits_account_token" 					=> $this->input->post('credits_account_token',true),
				"created_id"								=> $auth['user_id'],
				"created_on"								=> date('Y-m-d H:i:s'),
			);



			$transaction_module_code 				= 'PYB';
			$transaction_module_id 					= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);

			$preferencecompany 						= $this->AcctCreditAccount_model->getPreferenceCompany();
			$preferenceinventory 					= $this->AcctCreditAccount_model->getPreferenceInventory();

			

			$credits_account_token 					= $this->AcctCreditAccount_model->getCreditsAccountToken($data['credits_account_token']);

			
			$journal_voucher_period 				= date("Ym", strtotime($data['credits_account_date']));

			if($credits_account_token == 0){
				if($this->AcctCreditAccount_model->insertAcctCreditAccount($data)){
					$acctcreditsaccount_last 				= $this->AcctCreditAccount_model->getAcctCreditsAccount_Last($data['created_id']);

					$no = 1;
					if(!empty($daftaragunan)){
						foreach ($daftaragunan as $key => $val) {
							if($val['credits_agunan_type'] == 'BPKB'){
								$credits_agunan_type	= 1;
							}else {
								$credits_agunan_type 	= 2;
							}

							$dataagunan = array (
								'credits_account_id'				=> $acctcreditsaccount_last['credits_account_id'],
								'credits_agunan_type'				=> $credits_agunan_type,
								'credits_agunan_shm_no_sertifikat'	=> $val['credits_agunan_shm_no_sertifikat'],
								'credits_agunan_shm_atas_nama'		=> $val['credits_agunan_shm_atas_nama'],
								'credits_agunan_shm_luas'			=> $val['credits_agunan_shm_luas'],
								'credits_agunan_shm_kedudukan'		=> $val['credits_agunan_shm_kedudukan'],
								'credits_agunan_shm_taksiran'		=> $val['credits_agunan_shm_taksiran'],
								'credits_agunan_shm_keterangan'		=> $val['credits_agunan_shm_keterangan'],
								'credits_agunan_bpkb_nomor'			=> $val['credits_agunan_bpkb_nomor'],
								'credits_agunan_bpkb_nama'			=> $val['credits_agunan_bpkb_nama'],
								'credits_agunan_bpkb_nopol'			=> $val['credits_agunan_bpkb_nopol'],
								'credits_agunan_bpkb_no_rangka'		=> $val['credits_agunan_bpkb_no_rangka'],
								'credits_agunan_bpkb_no_mesin'		=> $val['credits_agunan_bpkb_no_mesin'],
								'credits_agunan_bpkb_taksiran'		=> $val['credits_agunan_bpkb_taksiran'],
								'credits_agunan_bpkb_keterangan'	=> $val['credits_agunan_bpkb_keterangan'],
								'credits_agunan_token'				=> $data['credits_account_token'].$no,
							);

							$no++;

							$credits_agunan_token					= $this->AcctCreditAccount_model->getCreditsAgunanToken($dataagunan['credits_agunan_token']);

							if ($credits_agunan_token == 0){
								$this->AcctCreditAccount_model->insertAcctCreditsAgunan($dataagunan);
							}
							
							// print_r($dataagunan);
						}
					}

					$acctcreditsaccount_last 						= $this->AcctCreditAccount_model->getAcctCreditsAccount_Last($data['created_id']);
					
					$data_journal = array(
						'branch_id'									=> $auth['branch_id'],
						'journal_voucher_period' 					=> $journal_voucher_period,
						'journal_voucher_date'						=> date('Y-m-d'),
						'journal_voucher_title'						=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
						'journal_voucher_description'				=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
						'journal_voucher_token'						=> $data['credits_account_token'],
						'transaction_module_id'						=> $transaction_module_id,
						'transaction_module_code'					=> $transaction_module_code,
						'transaction_journal_id' 					=> $acctcreditsaccount_last['credits_account_id'],
						'transaction_journal_no' 					=> $acctcreditsaccount_last['credits_account_serial'],
						'created_id' 								=> $data['created_id'],
						'created_on' 								=> $data['created_on'],
					);

					$journal_voucher_token 							= $this->AcctCreditAccount_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					$status_save 									= 1;
				
					if ($journal_voucher_token == 0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 					= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);
	
	
						$receivable_account_id					= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);
	
						$account_id_default_status 				= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);
	
						$total_payment 							= $data['credits_account_last_balance_principal'] + $data['credits_account_last_balance_margin'];
	
						$data_debet = array (
							'journal_voucher_id'				=> $journal_voucher_id,
							'account_id'						=> $receivable_account_id,
							'journal_voucher_description'		=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'			=> $data['credits_account_financing'],
							'journal_voucher_debit_amount'		=> $data['credits_account_financing'],
							'account_id_default_status'			=> $account_id_default_status,
							'account_id_status'					=> 0,
							'journal_voucher_item_token' 		=> $data['credits_account_token'].$receivable_account_id,
						);

						$journal_voucher_item_token 			= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if ($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
						}
	
						if($data['credits_id'] == 5 || $data['credits_id'] == 6){
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_last_balance_principal'],
								'journal_voucher_credit_amount'	=> $data['credits_account_last_balance_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
							
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);
	
							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_deferred_margin_income'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_last_balance_margin'],
								'journal_voucher_credit_amount'	=> $data['credits_account_last_balance_margin'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_deferred_margin_income'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
						} else {
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $total_payment,
								'journal_voucher_credit_amount'	=> $total_payment,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
						}			
	
						if($data['credits_account_materai'] <> '' || !empty($data['credits_account_materai'])){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_debit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_materai'],
								'journal_voucher_debit_amount'	=> $data['credits_account_materai'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'MTR'.$preferencecompany['account_cash_id'].$preferenceinventory['inventory_stamp_duty_id'],
							);
							
							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
							}
							


							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_stamp_duty_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferenceinventory['inventory_stamp_duty_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_materai'],
								'journal_voucher_credit_amount'	=> $data['credits_account_materai'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'MTR'.$preferenceinventory['inventory_stamp_duty_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
							
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}							
						}
	
						if($data['credits_account_adm_cost'] <> '' || !empty($data['credits_account_adm_cost'])){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_debit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
								'journal_voucher_debit_amount'	=> $data['credits_account_adm_cost'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'ADM'.$preferencecompany['account_cash_id'].$preferenceinventory['inventory_adm_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
	
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
							}
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferenceinventory['inventory_adm_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
								'journal_voucher_credit_amount'	=> $data['credits_account_adm_cost'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'ADM'.$preferenceinventory['inventory_adm_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
							
						}
	
						if($data['credits_account_notaris'] <> '' || !empty($data['credits_account_notaris'])){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_debit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_notaris'],
								'journal_voucher_debit_amount'	=> $data['credits_account_notaris'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'NTR'.$preferencecompany['account_cash_id'].$preferencecompany['account_notari_cost_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
							}
	
							
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_notari_cost_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_notari_cost_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_notaris'],
								'journal_voucher_credit_amount'	=> $data['credits_account_notaris'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'NTR'.$preferencecompany['account_notari_cost_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
							
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
	
						if($data['credits_account_insurance'] <> '' || !empty($data['credits_account_insurance'])){
							$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
							$data_debit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_insurance'],
								'journal_voucher_debit_amount'	=> $data['credits_account_insurance'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'ASR'.$preferencecompany['account_notari_cost_id'].$preferencecompany['account_insurance_cost_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
							}
	
	
							$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_insurance_cost_id']);
	
							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_insurance_cost_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_account_insurance'],
								'journal_voucher_credit_amount'	=> $data['credits_account_insurance'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['credits_account_token'].'ASR'.$preferencecompany['account_insurance_cost_id'],
							);

							$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
							
							if ($journal_voucher_item_token == 0){
								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
							}
						}

						$status_save 							= 1;
					} else {
						$status_save 							= 0;
					}
					
					if ($status_save == 1){
						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Credit Berjangka Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
	
						$this->session->unset_userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
						$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('addcreditaccount-'.$sesi['unique']);
						$this->session->unset_userdata('acctcreditsaccounttoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						$url='AcctCreditAccount/showdetaildata/'.$acctcreditsaccount_last['credits_account_id'];
						redirect($url);
					} else {
						$this->session->set_userdata('addacctdepositoaccount',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Credit Berjangka Tidak Berhasil - Data Journal Gagal Input 1
								</div> ";
						$this->session->set_userdata('message',$msg);
						$url='AcctCreditAccount/addform/'.$member_id;
						redirect($url);
					}
				}else{
					$this->session->set_userdata('addacctdepositoaccount',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					$url='AcctCreditAccount/addform/'.$member_id;
					redirect($url);
				}
			} else {
				$acctcreditsaccount_last 				= $this->AcctCreditAccount_model->getAcctCreditsAccount_Last($data['created_id']);

				$no 									= 1;
				if(!empty($daftaragunan)){
					foreach ($daftaragunan as $key => $val) {
						if($val['credits_agunan_type'] == 'BPKB'){
							$credits_agunan_type	= 1;
						}else {
							$credits_agunan_type 	= 2;
						}

						$dataagunan = array (
							'credits_account_id'				=> $acctcreditsaccount_last['credits_account_id'],
							'credits_agunan_type'				=> $credits_agunan_type,
							'credits_agunan_shm_no_sertifikat'	=> $val['credits_agunan_shm_no_sertifikat'],
							'credits_agunan_shm_atas_nama'		=> $val['credits_agunan_shm_atas_nama'],
							'credits_agunan_shm_luas'			=> $val['credits_agunan_shm_luas'],
							'credits_agunan_shm_kedudukan'		=> $val['credits_agunan_shm_kedudukan'],
							'credits_agunan_shm_taksiran'		=> $val['credits_agunan_shm_taksiran'],
							'credits_agunan_shm_keterangan'		=> $val['credits_agunan_shm_keterangan'],
							'credits_agunan_bpkb_nomor'			=> $val['credits_agunan_bpkb_nomor'],
							'credits_agunan_bpkb_nama'			=> $val['credits_agunan_bpkb_nama'],
							'credits_agunan_bpkb_nopol'			=> $val['credits_agunan_bpkb_nopol'],
							'credits_agunan_bpkb_no_rangka'		=> $val['credits_agunan_bpkb_no_rangka'],
							'credits_agunan_bpkb_no_mesin'		=> $val['credits_agunan_bpkb_no_mesin'],
							'credits_agunan_bpkb_taksiran'		=> $val['credits_agunan_bpkb_taksiran'],
							'credits_agunan_bpkb_keterangan'	=> $val['credits_agunan_bpkb_keterangan'],
							'credits_agunan_token'				=> $data['credits_account_token'].$no,
						);

						$no++;

						$credits_agunan_token					= $this->AcctCreditAccount_model->getCreditsAgunanToken($dataagunan['credits_agunan_token']);

						if ($credits_agunan_token == 0){
							$this->AcctCreditAccount_model->insertAcctCreditsAgunan($dataagunan);
						}
						
						// print_r($dataagunan);
					}
				}
				
				$data_journal = array(
					'branch_id'							=> $auth['branch_id'],
					'journal_voucher_period' 			=> $journal_voucher_period,
					'journal_voucher_date'				=> date('Y-m-d'),
					'journal_voucher_title'				=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
					'journal_voucher_description'		=> 'PEMBIAYAAN '.$acctcreditsaccount_last['credits_name'].' '.$acctcreditsaccount_last['member_name'],
					'journal_voucher_token'				=> $data['credits_account_token'],
					'transaction_module_id'				=> $transaction_module_id,
					'transaction_module_code'			=> $transaction_module_code,
					'transaction_journal_id' 			=> $acctcreditsaccount_last['credits_account_id'],
					'transaction_journal_no' 			=> $acctcreditsaccount_last['credits_account_serial'],
					'created_id' 						=> $data['created_id'],
					'created_on' 						=> $data['created_on'],
				);

				$journal_voucher_token 					= $this->AcctCreditAccount_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

				$status_save 							= 1;

				if($journal_voucher_token == 0){
					$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

					$journal_voucher_id 				= $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

					$receivable_account_id				= $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);
	
					$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);
	
					$total_payment 						= $data['credits_account_last_balance_principal'] + $data['credits_account_last_balance_margin'];
	
					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $receivable_account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['credits_account_financing'],
						'journal_voucher_debit_amount'	=> $data['credits_account_financing'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token' 	=> $data['credits_account_token'].$receivable_account_id,
					);
	
					$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);
	
					if($journal_voucher_item_token == 0){
						$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
					}
	
					if($data['credits_id'] == 5 || $data['credits_id'] == 6){
						$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_last_balance_principal'],
							'journal_voucher_credit_amount'	=> $data['credits_account_last_balance_principal'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_cash_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);
	
						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_deferred_margin_income'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_last_balance_margin'],
							'journal_voucher_credit_amount'	=> $data['credits_account_last_balance_margin'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_deferred_margin_income'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					} else {
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $total_payment,
							'journal_voucher_credit_amount'	=> $total_payment,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].$preferencecompany['account_cash_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
					}			
	
					if($data['credits_account_materai'] <> '' || !empty($data['credits_account_materai'])){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_materai'],
							'journal_voucher_debit_amount'	=> $data['credits_account_materai'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'MTR'.$preferencecompany['account_cash_id'].$preferenceinventory['inventory_stamp_duty_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_stamp_duty_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceinventory['inventory_stamp_duty_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_materai'],
							'journal_voucher_credit_amount'	=> $data['credits_account_materai'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'MTR'.$preferenceinventory['inventory_stamp_duty_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}
	
					if($data['credits_account_adm_cost'] <> '' || !empty($data['credits_account_adm_cost'])){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
							'journal_voucher_debit_amount'	=> $data['credits_account_adm_cost'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'ADM'.$preferencecompany['account_cash_id'].$preferenceinventory['inventory_adm_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceinventory['inventory_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_adm_cost'],
							'journal_voucher_credit_amount'	=> $data['credits_account_adm_cost'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'ADM'.$preferenceinventory['inventory_adm_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}
	
					if($data['credits_account_notaris'] <> '' || !empty($data['credits_account_notaris'])){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_notaris'],
							'journal_voucher_debit_amount'	=> $data['credits_account_notaris'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'NTR'.$preferencecompany['account_cash_id'].$preferencecompany['account_notari_cost_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_notari_cost_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_notari_cost_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_notaris'],
							'journal_voucher_credit_amount'	=> $data['credits_account_notaris'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'NTR'.$preferencecompany['account_notari_cost_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}
	
					if($data['credits_account_insurance'] <> '' || !empty($data['credits_account_insurance'])){
						$preferencecompany 					= $this->AcctCreditAccount_model->getPreferenceCompany();
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
	
						$data_debit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_insurance'],
							'journal_voucher_debit_amount'	=> $data['credits_account_insurance'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'ASR'.$preferencecompany['account_notari_cost_id'].$preferencecompany['account_insurance_cost_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debit);
						}
	
						$account_id_default_status 			= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_insurance_cost_id']);
	
						$data_credit = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_insurance_cost_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['credits_account_insurance'],
							'journal_voucher_credit_amount'	=> $data['credits_account_insurance'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['credits_account_token'].'ASR'.$preferencecompany['account_insurance_cost_id'],
						);
	
						$journal_voucher_item_token 		= $this->AcctCreditAccount_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);
	
						if($journal_voucher_item_token == 0){
							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);
						}
						
					}

					$status_save 							= 1;
				} else {
					$status_save 							= 0;
				}

				if ($status_save == 1){
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');

					$this->session->unset_userdata('addarrayacctcreditsagunan-'.$sesi['unique']);
					$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
					$this->session->unset_userdata('addcreditaccount-'.$sesi['unique']);
					$this->session->unset_userdata('acctcreditsaccounttoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					$url='AcctCreditAccount/showdetaildata/'.$acctcreditsaccount_last['credits_account_id'];
					redirect($url);
				} else {
					$this->session->set_userdata('addacctdepositoaccount',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Tidak Berhasil - Data Journal Gagal Input
							</div> ";
					$this->session->set_userdata('message',$msg);
					$url='AcctCreditAccount/addform/'.$member_id;
					redirect($url);
				}
			}
			
		}

		public function showdetaildata(){
			$auth 					= $this->session->userdata('auth'); 
			$credits_account_id 	= $this->uri->segment(3);
			$type 					= $this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola 			= $this->flat($credits_account_id);
			}else{
				$datapola 			= $this->slidingrate($credits_account_id);
			}


			$detaildata 			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['acctcreditsagunan']			= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);
			$data['main_view']['coreoffice']				= create_double($this->AcctCreditAccount_model->getCoreOffice(),'office_id', 'office_name');
			$data['main_view']['sumberdana']				= create_double($this->Core_source_fund_model->getData(),'source_fund_id', 'source_fund_name');
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($detaildata['member_id']);
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctDepositoAccount_model->getAcctSavingsAccount($auth['branch_id']),'savings_account_id', 'savings_account_no');
			$data['main_view']['creditid']					= create_double($this->AcctCredit_model->getData(),'credits_id', 'credits_name');

			$data['main_view']['creditaccount']				= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']					= $datapola;

			$data['main_view']['content']					= 'AcctCreditAccount/FormSaveSuccessAcctCreditAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function printNoteAcctCreditAccount(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();
			$acctcreditsaccount	 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);



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

			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				<tr>
					<td rowspan=\"2\" width=\"20%\">".$img."</td>
					<td width=\"50%\"><div style=\"text-align: left; font-size:14px\">BUKTI PENCAIRAN PEMBIAYAAN</div></td>
				</tr>
				<tr>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah dibayarkan kepada :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">No. Akad</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_serial']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctcreditsaccount['credits_account_financing'])."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: PENCAIRAN PEMBIAYAAN</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctcreditsaccount['credits_account_financing'], 2)."</div></td>
				</tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctCreditAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
				</tr>
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\">Penerima</div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
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

		public function AcctCreditAccountBook(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditAccount/ListBookAcctCreditsAccount_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filteracctcreditsaccountbook(){
			$data = array (
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsaccountbooklist', $data);
			redirect('AcctCreditAccount/AcctCreditAccountBook');
		}

		public function getAcctCreditsAccountBookList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsaccountbooklist');
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

			$list = $this->AcctCreditAccount_model->get_datatables_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $creditsaccount) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $creditsaccount->credits_account_serial;
				$row[] = $creditsaccount->member_name;
				$row[] = $creditsaccount->credits_name;
				$row[] = $creditsaccount->source_fund_name;
				$row[] = tgltoview($creditsaccount->credits_account_date);
				$row[] = number_format($creditsaccount->credits_account_financing, 2);
		//       if($creditsaccount->validation == 0){
		//       	$row[] = '<a href="'.base_url().'AcctDepositoAccount/printNoteAcctDepositoAccount/'.$creditsaccount->deposito_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>
				//      <a href="'.base_url().'AcctDepositoAccount/validationAcctDepositoAccount/'.$creditsaccount->deposito_account_id.'" class="btn btn-xs green-jungle" role="button"><i class="fa fa-check"></i> Validasi</a>';
				// } else {
					$row[] = '<a href="'.base_url().'AcctCreditAccount/printCover/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Cetak Halaman Depan</a>
							<a href="'.base_url().'AcctCreditAccount/printBook/'.$creditsaccount->credits_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Cetak Pembiayaan</a>';

				// }
				$data[] = $row;
			}



			// print_r($list);exit;
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->AcctCreditAccount_model->count_all_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
							"recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_master($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function printBook(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$acctcreditsaccount	 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$credits_account_payment_date = date('Y-m-d', strtotime("+1 months", strtotime($acctcreditsaccount['credits_account_date'])));



			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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

			$pdf->SetMargins(5, 30, 7, 7); // put space of 10 on top
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
			$resolution= array(200, 200);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			

			$tbl1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">NOMOR KONTRAK</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_serial']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">JUMLAH PEMBIAYAAN</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".number_format($acctcreditsaccount['credits_account_financing'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">TENOR</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['credits_account_period']." Bulan</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">ANGSURAN</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".number_format($acctcreditsaccount['credits_account_payment_amount'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">TGL AKTIVASI</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($acctcreditsaccount['credits_account_date'])."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">JATUH TEMPO PERTAMA</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($credits_account_payment_date)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">JATUH TEMPO TERAKHIR</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($acctcreditsaccount['credits_account_due_date'])."</div></td>
				</tr>			
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">CABANG PENGAJUAN</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctcreditsaccount['branch_code']."</div></td>
				</tr>	
			</table>";


			$pdf->writeHTML($tbl1, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak_pembiayaan_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function printCover(){
			$auth = $this->session->userdata('auth');
			$credits_account_id 	= $this->uri->segment(3);
			$acctcreditsaccount	 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$credits_account_payment_date = date('Y-m-d', strtotime("+1 months", strtotime($acctcreditsaccount['credits_account_date'])));



			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
			$resolution= array(200, 200);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			

			$tbl1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">BUKU ANGSURAN PEMBIAYAAN</div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\"></div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">".$acctcreditsaccount['member_no']."</div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">".$acctcreditsaccount['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\"></div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">".$acctcreditsaccount['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">".$acctcreditsaccount['city_name']."</div></td>
				</tr>
				<tr>
					<td width=\"60%\"><div style=\"text-align: left;\">".$acctcreditsaccount['province_name']."</div></td>
				</tr>	
			</table>";


			$pdf->writeHTML($tbl1, true, false, false, false, '');

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak_halaman_depan_pembiayaan_'.$acctcreditsaccount['member_name'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function detailAcctCreditsAccount(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsAccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id']		= '';
				$sesi['credits_id']		= '';
			}

			$start_date = tgltodb($sesi['start_date']);
			$end_date 	= tgltodb($sesi['end_date']);

			$member_id = $this->uri->segment(3);
			if($member_id == ''){
				$member_id = $sesi['member_id'];
			}

			// $data['main_view']['coremember']				= create_double($this->AcctCreditAccount_model->getCoreMember($auth['branch_id']), 'member_id', 'member_name');

			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($member_id);

			$data['main_view']['acctcredits']				= create_double($this->AcctCreditAccount_model->getAcctCredits(), 'credits_id', 'credits_name');

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount($start_date, $end_date, $auth['branch_id'], $sesi['member_id'], $sesi['credits_id']);

			$data['main_view']['content']					= 'AcctCreditAccount/ListDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				'start_date'			=> $this->input->post('start_date',true),
				'end_date'				=> $this->input->post('end_date',true),
				'member_id'				=> $this->input->post('member_id',true),
				'credits_id'			=> $this->input->post('credits_id',true),
			);
			$this->session->set_userdata('filter-AcctCreditsAccount', $data);
			redirect('AcctCreditAccount/detailAcctCreditsAccount');
		}
		
		public function reset_search(){
			$sesi= $this->session->userdata('filter-AcctCreditsAccount');
			$this->session->unset_userdata('filter-AcctCreditsAccount');
			redirect('AcctCreditAccount/detailAcctCreditsAccount');
		}

		public function getmemberlist(){
		$auth = $this->session->userdata('auth');
		$data_state = 0;
		$branch_id = "";
		$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $customers) {
			$no++;
			if($customers->member_status == 1){
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = '<a href="'.base_url().'AcctCreditAccount/detailAcctCreditsAccount/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
				$data[] = $row;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
						"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
		}

		public function showdetail(){
			$credits_account_id 	= $this->uri->segment(3);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$data['main_view']['acctcreditspayment']		= $this->AcctCreditAccount_model->getAcctCreditsPayment_Detail($credits_account_id);

			$data['main_view']['content']					= 'AcctCreditAccount/FormDetailAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$credits_account_id			= $this->input->post('credits_account_id',true);

			$memberidentity				= $this->configuration->MemberIdentity();

			$acctcreditsaccount			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$acctcreditspayment			= $this->AcctCreditAccount_model->getAcctCreditsPayment_Detail($credits_account_id);

			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

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

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
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

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>HISTORI ANGSURAN PINJAMAN</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"40%\">
							
						</td>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>No. Akad</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"40%\">
							
						</td>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblmember = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">

					<tr>
						<td style=\"text-align:left;\" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Alamat
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"83%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".$acctcreditsaccount['member_address']."
							</div>
						</td>
					</tr>

					<tr>
						<td style=\"text-align:left;\" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Tanggal Realisasi
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"15%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".tgltoview($acctcreditsaccount['credits_account_date'])."
							</div>
						</td>

						<td style=\"text-align:left;\" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Jangka Waktu
							</div>
						</td>
						
						<td style=\"text-align:left; \" width=\"10%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".$acctcreditsaccount['credits_account_period']."
							</div>
						</td>
						<td style=\"text-align:left; \" width=\"17%\">
							<div style=\"font-size:12px;font-weight:bold\">
								Pinjaman
							</div>
						</td>

						<td style=\"text-align:left; \" width=\"33%\">
							<div style=\"font-size:12px;font-weight:bold\">
								: ".nominal($acctcreditsaccount['credits_account_sell_price'] - $acctcreditsaccount['credits_account_um'])."
							</div>
						</td>
					</tr>
				</table>
				<br><br>
			";

			$pdf->writeHTML($tblmember, true, false, false, false, '');


			$tblpaymentheader = "
				<table id=\"items\" width=\"100%\" cellpadding=\"3\" cellspacing=\"0\" border=\"1\">
					<tr>
						<td style=\"text-align:center;\" width=\"5%\">
							<div style=\"font-size:10px\">
								<b>No</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"15%\">
							<div style=\"font-size:10px\">
								<b>Tanggal Angsuran</b>
							</div>
						</td>
					
						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Angsuran Margin</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Saldo Pokok</b>
							</div>
						</td>

						<td style=\"text-align:center;\" width=\"20%\">
							<div style=\"font-size:10px\">
								<b>Saldo Margin</b>
							</div>
						</td>
					</tr>";


			$tblpaymentlist = "";
			$no = 1;
			foreach($acctcreditspayment as $key=>$val){
				$tblpaymentlist .= "
					<tr>
						<td style=\"text-align:center;\" width=\"5%\">
							<div style=\"font-size:10px\">
								".$no."
							</div>
						</td>

						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:10px\">
								".tgltoview($val['credits_payment_date'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_principal'])."
							</div>
						</td>
					
						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_payment_margin'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_principal_last_balance'])."
							</div>
						</td>

						<td style=\"text-align:right;\" width=\"20%\">
							<div style=\"font-size:10px\">
								".nominal($val['credits_margin_last_balance'])."
							</div>
						</td>
					</tr>";

				$no++;
			}

			$tblpaymentfooter = "
				</table>
			";

			

			$pdf->writeHTML($tblpaymentheader.$tblpaymentlist.$tblpaymentfooter, true, false, false, false, '');

			ob_clean();

			$filename = 'Histori_Angsuran_Pinjaman_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function creditlist(){
			$data['main_view']['content']			= 'AcctCreditAccount/Creditlist_view';
			$this->load->view('MainPage_view',$data);
			
		}
		public function creditajax(){
			$list = $this->AcctCreditAccount_model->get_datatables();
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->credits_account_serial;
				$row[] = $customers->member_name;
				$row[] = $customers->member_no;
				$row[] = $customers->credits_account_date;
				$row[] = $customers->credits_account_due_date;
				$row[] = $customers->credits_account_period;
				$row[] = $customers->credits_account_net_price;
				$row[] = $customers->credits_account_sell_price;
				$row[] = $customers->credits_account_margin;
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all(),
							"recordsFiltered" => $this->CoreMember_model->count_filtered(),
							"data" => $data,
					);
			echo json_encode($output);
		}

		public function agunanadd(){
				// $this->session->unset_userdata('agunan_data');
				// $this->session->unset_userdata('agunan_key');
				// exit;
			$data = $this->session->userdata('agunan_data');
			$agunan = $this->session->userdata('agunan_key');
			// echo "<pre>";
			// $a=json_encode($data);
			// print_r($a);
			// exit;
			if(!isset($agunan)){
				$agunan=1;
			}
			$new_key=$agunan+1;
			if($this->uri->segment(3)=="save"){
				$type=$this->input->post('tipe',true);
				if($type == 'Sertifikat'){
					$data[$new_key]=array (
							"shm_no_sertifikat"	=> $this->input->post('shm_no_sertifikat',true),
							"shm_luas"	=> $this->input->post('shm_luas',true),
							"shm_atas_nama"	=> $this->input->post('shm_atas_nama',true),
							"shm_kedudukan"	=> $this->input->post('shm_kedudukan',true),
							"shm_taksiran"	=> $this->input->post('shm_taksiran',true),
							"tipe"	=> $this->input->post('tipe',true),
							"shm_keterangan"	=> $this->input->post('shm_keterangan',true),
							);
				}else{
					$data[$new_key]=array (
							"bpkb_nomor"	=> $this->input->post('bpkb_nomor',true),
							"bpkb_nama"	=> $this->input->post('bpkb_nama',true),
							"bpkb_nopol"	=> $this->input->post('bpkb_nopol',true),
							"bpkb_no_mesin"	=> $this->input->post('bpkb_no_mesin',true),
							"bpkb_no_rangka"	=> $this->input->post('bpkb_no_rangka',true),
							"taksiran"	=> $this->input->post('taksiran',true),
							"tipe"	=> $this->input->post('tipe',true),
							"bpkb_keterangan"	=> $this->input->post('bpkb_keterangan',true),
							);
				}
				
				$this->session->set_userdata('agunan_data',$data);
				$this->session->set_userdata('agunan_key',$new_key);
			}
			$kirim['data']=$data;

			
			$this->load->view('AcctCreditAccount/FormAddAcctCreditAgunan',$kirim);
		}
		
		public function agunanview(){
			$credits_account_id 	= $this->uri->segment(3);
			$detaildata=$this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			// print_r($detaildata['credits_account_agunan']); exit;
			$this->load->view('AcctCreditAccount/FormShowCreditAgunan',$detaildata);
		}
		
		public function polaangsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			$data['main_view']['creditaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			$data['main_view']['datapola']		= $datapola;
			$data['main_view']['content']			= 'AcctCreditAccount/FormPolaAngsuran_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function angsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);
			if($type== '' || $type==0){
				$datapola=$this->flat($id);
			}else{
				$datapola=$this->slidingrate($id);
			}
			
			$creditaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			redirect('AcctCreditAccount/showdetaildata/'.$id.'/'.$type,compact('datapola'));
		}
		
		public function cekPolaAngsuran(){
			$id=$this->input->post('id_credit',true);
			$pola=$this->input->post('pola_angsuran',true);
			$url='AcctCreditAccount/angsuran/'.$id.'/'.$pola;
			redirect($url);
		}

		public function flat($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			/*print_r("credistaccount ");
			print_r($credistaccount);
			exit;*/

			$credits_account_um 			= $credistaccount['credits_account_um'];

			if($credistaccount['credits_account_sell_price'] == '' || $credistaccount['credits_account_sell_price'] == 0){
				$credits_account_net_price 		= $credistaccount['credits_account_financing'];
				$total_credits_account 			= $credits_account_net_price;
			} else {
				$credits_account_net_price 		= $credistaccount['credits_account_net_price']; - $credistaccount['credits_account_um'];
				$total_credits_account 			= $credits_account_net_price;
			}

			$credits_account_margin 		= $credistaccount['credits_account_margin'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

/*			$jangkawaktuth 					= $jangkawaktu/12;
			$percentageth = ($margin*100)/$pinjaman;
			$percentagebl=round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok=round($pinjaman/$jangkawaktuth/12,2);
			$angsuranmargin=round($pinjaman*$percentageth/100/12,2);
			$totangsuran=$angsuranpokok+$angsuranmargin;*/
			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;

			for($i=1; $i<=$credits_account_period; $i++){
				/*$totpokok=$totpokok+$angsuranpokok;
				$sisapokok=$pinjaman-$totpokok;*/

				$angsuran_pokok									= $total_credits_account / $credits_account_period;				

				$angsuran_margin								= $credits_account_margin / $credits_account_period;				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;

				$installment_pattern[$i]['opening_balance']		= $opening_balance;
				$installment_pattern[$i]['ke'] 					= $i;
				$installment_pattern[$i]['angsuran'] 			= $angsuran;
				$installment_pattern[$i]['angsuran_pokok']		= $angsuran_pokok;
				$installment_pattern[$i]['angsuran_margin'] 	= $angsuran_margin;
				$installment_pattern[$i]['akumulasi_pokok'] 	= $totpokok;
				$installment_pattern[$i]['last_balance'] 		= $last_balance;
				
				$opening_balance 								= $last_balance;
			}
			
			return $installment_pattern;
			
		}
		
		public function slidingrate($id){
			$creditsaccount 	= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			/*print_r("detailpinjaman ");
			print_r($detailpinjaman);
			exit;*/
			$credits_account_net_price 		= $creditsaccount['credits_account_net_price'];
			$credits_account_um 			= $creditsaccount['credits_account_um'];
			$credits_account_margin 		= $creditsaccount['credits_account_margin'];
			$credits_account_period 		= $creditsaccount['credits_account_period'];			

			$total_credits_account 			= $credits_account_net_price - $credits_account_um;




			
			$jangkawaktuth 		= $jangkawaktu/12;
			$percentageth 		= ($margin*100)/$pinjaman;
			$percentagebl 		= round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok 		= round($pinjaman/$jangkawaktuth/12,2);
			
			$pola 				= array();
			$totpinjaman 		= $pinjaman;
			$totpokok 			= 0;
			for($i=1; $i<=$jangkawaktu; $i++){
				$angsuranmargin 				= round(($totpinjaman * $percentageth/100)/$jangkawaktu,2);
				$totangsuran 					= $angsuranpokok + $angsuranmargin;
				$totpokok						= $totpokok + $angsuranpokok;
				$sisapokok 						= $pinjaman - $totpokok;
				$pola[$i]['ke']					= $i;
				$pola[$i]['angsuran']			= $totangsuran;
				$pola[$i]['angsuran_pokok']		= $angsuranpokok;
				$pola[$i]['angsuran_margin']	= $angsuranmargin;
				$pola[$i]['akumulasi_pokok']	= $totpokok;
				$pola[$i]['sisa_pokok']			= $sisapokok;
				$totpinjaman					= $totpinjaman - $angsuranpokok;
			}
			
			return $pola;
			
		}
		
		public function anuitas($id){
			
		}
		
		function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
			$rate = $guess;
			if (abs($rate) < $this->FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}
			$y0 = $pv + $pmt * $nper + $fv;
			$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;

			// find root by secant method
			$i  = $x0 = 0.0;
			$x1 = $rate;
			while ((abs($y0 - $y1) > $this->FINANCIAL_PRECISION) && ($i < $this->FINANCIAL_MAX_ITERATIONS)) {
				$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
				$x0 = $x1;
				$x1 = $rate;

				if (abs($rate) < $this->FINANCIAL_PRECISION) {
					$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
				} else {
					$f = exp($nper * log(1 + $rate));
					$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
				}

				$y0 = $y1;
				$y1 = $y;
				++$i;
			}
			return $rate;
		}  
		
		public function printPolaAngsuran(){
			$credits_account_id 	= $this->input->post('id_credit', true);
			$type					= $this->input->post('pola', true);
			if($type== '' || $type==0){
				$datapola=$this->flat($credits_account_id);
			}else{
				$datapola=$this->slidingrate($credits_account_id);
			}

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

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

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
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

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Pola Angsuran</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>No. Akad</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
				<tr>
					<td width=\"5%\"><div style=\"text-align: center;font-size:10;\">Ke</div></td>
					<td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Saldo Pokok</div></td>
					<td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Pokok</div></td>
					<td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Margin</div></td>
					<td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Total Angsuran</div></td>
					<td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Sisa Pokok</div></td>

				
				</tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				// print_r($acctcreditspayment);exit;

				$tbl3 .= "
					<tr>
						<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
						<td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
						<td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
						<td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_margin'], 2)." &nbsp; </div></td>
						<td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran'], 2)." &nbsp; </div></td>
						<td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
						
					</tr>
				";

				$no++;
			}

			$tbl4 = "							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			

			ob_clean();

			$filename = 'Pola_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function processPrintingAkad(){
			$credits_account_id			= $this->uri->segment(3);

			$memberidentity				= $this->configuration->MemberIdentity();
			$dayname 					= $this->configuration->DayName();
			$monthname 					= $this->configuration->Month();

			$acctcreditsaccount			= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$acctcreditsagunan			= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);

			if($acctcreditsaccount['credits_id'] == 5 || $acctcreditsaccount['credits_id'] == 6){
				$credits_name = 'MURABAHAH';
			} else {
				$credits_name = '';
			}

			$date 	= date('d', (strtotime($acctcreditsaccount['credits_account_date'])));
			$day 	= date('D', (strtotime($acctcreditsaccount['credits_account_date'])));
			$month 	= date('m', (strtotime($acctcreditsaccount['credits_account_date'])));
			$year 	= date('Y', (strtotime($acctcreditsaccount['credits_account_date'])));

			$date_jt 	= date('d', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$day_jt 	= date('D', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$month_jt 	= date('m', (strtotime($acctcreditsaccount['credits_account_due_date'])));
			$year_jt 	= date('Y', (strtotime($acctcreditsaccount['credits_account_due_date'])));

			// print_r($acctcreditsaccount);exit;

			$acctcreditsagunan 			= $this->AcctCreditAccount_model->getAcctCreditsAgunan_Detail($credits_account_id);

			$total_agunan = 0;
			foreach ($acctcreditsagunan as $key => $val) {
				if($val['credits_agunan_type'] == 1){
					$agunanbpkb[] = array (
						'credits_agunan_bpkb_nama'				=> $val['credits_agunan_bpkb_nama'],
						'credits_agunan_bpkb_nomor'				=> $val['credits_agunan_bpkb_nomor'],
						'credits_agunan_bpkb_no_mesin'			=> $val['credits_agunan_bpkb_no_mesin'],
						'credits_agunan_bpkb_no_rangka'			=> $val['credits_agunan_bpkb_no_rangka'],	
						'credits_agunan_bpkb_nopol'				=> $val['credits_agunan_bpkb_nopol'],	
						'credits_agunan_bpkb_taksiran'			=> $val['credits_agunan_bpkb_taksiran'],		
					);
				} else if($val['credits_agunan_type'] == 2){
					$agunansertifikat[] = array (
						'credits_agunan_shm_no_sertifikat'		=> $val['credits_agunan_shm_no_sertifikat'],
						'credits_agunan_shm_luas'				=> $val['credits_agunan_shm_luas'],
						'credits_agunan_shm_atas_nama'			=> $val['credits_agunan_shm_atas_nama'],
						'credits_agunan_shm_taksiran'			=> $val['credits_agunan_shm_taksiran'],
						'credits_agunan_shm_kedudukan'			=> $val['credits_agunan_shm_kedudukan'],
						'credits_agunan_shm_keterangan'			=> $val['credits_agunan_shm_keterangan'],
		
					);
				}

				$total_agunan = $total_agunan + $val['credits_agunan_bpkb_taksiran'] + $val['credits_agunan_shm_taksiran'];
			}

			$totalbiaya = $acctcreditsaccount['credits_account_materai'] + $acctcreditsaccount['credits_account_adm_cost'] + $acctcreditsaccount['credits_account_notaris'] + $acctcreditsaccount['credits_account_insurance'];

			if(empty($totalbiaya)){
				$totalbiaya = 0;
			}
			

			// print_r($acctcreditsagunan);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

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

			$pdf->SetMargins(20, 30, 20, 20); // put space of 10 on top
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

			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>“Hai orang-orang yang beriman, penuhilah akad-akad (akad) itu……....”</i></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\";><i>(Terjemahan QS : Al-Maidah 1)</i></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>”Hai orang-orang yang beriman, janganlah kamu saling memakan harta sesamamu dengan jalan bathil, kecuali dengan jalan perniagaan yang berlaku suka sama suka diantaramu......”</i></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>(Terjemahan QS : An-Nisa’ 29)</i></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:10px\"><i>Roh seorang mukmin masih terkatung-katung (sesudah wafatnya ) sampai utangnya di dunia dilunasi ..... (HR. Ahmad )</i></div>
						</td>			
					</tr>
					
				</table>
				<br><br>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px; font-weight:bold\"><u>AKAD PEMBIAYAAN ".$credits_name."</u></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\">No. : ".$acctcreditsaccount['credits_account_serial']."</div>
						</td>			
					</tr>
					
				</table>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px;\">Pada hari ini <b>".$dayname[$day]."</br> tanggal <b>".$date."</b> bulan <b>".$monthname[$month]."</br>  tahun <b>".$year."</br> oleh dan antara pihak-pihak:</div>
						</td>			
					</tr>
					<br>
					<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px;\">Yang bertanda tangan dibawah ini,</div>
						</td>			
					</tr>
					<br>
				</table>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Nama</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$this->AcctCreditAccount_model->getBranchManager($acctcreditsaccount['branch_id'])."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Jabatan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">Kepala KSPP SYARIAH madani Jawa Timur ".$this->AcctCreditAccount_model->getBranchName($acctcreditsaccount['branch_id'])."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Alamat</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$this->AcctCreditAccount_model->getBranchAddress($acctcreditsaccount['branch_id'])."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" colspan=\"3\">
							<div style=\"font-size:12px;\"><br>
								Dalam hal ini bertindak dalam jabatannya dan berdasarkan Surat Kuasa Pengurus No : ".$this->AcctCreditAccount_model->getBranchNoSK($acctcreditsaccount['branch_id'])." dengan sah mewakili Koperasi Syariah “Madani” Jawa Timur yang berkedudukan di Jl. Raya Pasir Putih Kelurahan Tasikmadu Kecamatan Watulimo Kabupaten Trenggalek, untuk selanjutnya disebut sebagai  PIHAK I <br></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Nama</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_name']."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">No. Anggota</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_no']."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">No. Rekening</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$this->AcctCreditAccount_model->getSavingsAccountNo($acctcreditsaccount['savings_account_id'])."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Pekerjaan</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_job']."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"15%\">
							<div style=\"font-size:12px;\">Alamat</div>
						</td>
						<td style=\"text-align:left;\" width=\"2%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:left;\" width=\"80%\">
							<div style=\"font-size:12px;\">".$acctcreditsaccount['member_address']."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" colspan=\"3\">
							<div style=\"font-size:12px;\">Bertindak  untuk  dan  atas  nama  diri   sendiri, untuk selanjutnya disebut  sebagai PIHAK  II <br></div>
						</td>			
					</tr>
				</table>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
							<div style=\"font-size:12px;\">Para pihak terlebih dahulu menerangkan hal-hal berikut ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK I dan PIHAK II, yang secara bersama-sama untuk selanjutnya disebut Para Pihak, bertindak dalam kedudukannya masing-masing sebagaimana tersebut di atas, terlebih dahulu menerangkan bahwa:</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">-</td>
						<td style=\"text-align:justify;\" colspan=\"3\" width=\"95%\">
							<div style=\"font-size:12px;\">Berdasarkan formulir permohonan pembiayaan konsumtif tanggal ".$date." ".$monthname[$month]." ".$year." PIHAK II telah mengajukan permohonan pembiayaan ".$acctcreditsaccount['credits_name'].".</div>
						</td>		
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">-</td>
						<td style=\"text-align:justify;\" colspan=\"3\" width=\"95%\">
							<div style=\"font-size:12px;\">Berdasarkan Surat Keputusan Pembiayaan Nomor tanggal 13 JANUARI 2017 yang  merupakan  bagian  yang  tidak  terpisahkan  dari Akad ini,  PIHAK I  telah menyetujui penyaluran pembiayaan sesuai dengan syarat-syarat dan ketentuan yang diatur dalam Akad ini.</div>
						</td>		
					</tr>
					<br>
					<tr>
						<td style=\"text-align:justify;\" colspan=\"4\" width=\"100%\">
							<div style=\"font-size:12px;\">Berdasarkan hal-hal tersebut di atas, Para Pihak dengan ini sepakat mengadakan Akad Pembiayaan Murabahah (untuk selanjutnya disebut Akad) dengan ketentuan-ketentuan dan syarat-syarat berikut ini.</div>
						</td>			
					</tr>
				</table>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			//---------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 1</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Definisi</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"100%\">
							<div style=\"font-size:12px;\">Dalam Akad ini, yang dimaksud dengan :</div>
						</td>			
					</tr>
				</table>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Akad Pembiayaan Murabahah”</b> adalah akad pembiayaan suatu barang dengan menegaskan harga belinya kepada PIHAK II dan PIHAK II membayar kepada PIHAK I dengan harga yang lebih sebagai keuntungan yang disepakati.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Barang”</b> Adalah barang yang menjadi objek dalam Akad Pembiayaan Murabahah ini, yang meliputi segala jenis atau macam barang yang dihalalkan oleh syariah, baik zat maupun cara perolehannya.
							</div>
						</td>
									
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">3.</div>
						</td>	
						<td style=\"text-align:left;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Pemasok atau Suplier”</b> Adalah pihak ketiga yang ditunjuk atau disetujui oleh PIHAK I untuk menyediakan barang yang akan dibeli oleh PIHAK I dan selanjutnya akan dijual kepada PIHAK II.</div>
						</td>
									
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">4.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Harga Beli”</b> Adalah sejumlah uang yang dikeluarkan PIHAK I untuk membeli barang dari pemasok yang diminta oleh PIHAK II dan disetujui oleh PIHAK I berdasar Surat Persetujuan Prinsip dari PIHAK I kepada PIHAK II, termasuk di dalamnya biaya-biaya langsung yang terkait dengan pembelian barang tersebut.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">5.</div>
						</td>
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Keuntungan”</b> Adalah keuntungan PIHAK I atas terjadinya jual beli al-Murabahah ini yang disetujui oleh PIHAK I dan PIHAK II yang ditetapkan dalam Akad ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">6.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Harga Jual”</b> Adalah harga beli ditambah dengan sejumlah keuntungan PIHAK I yang disepakati oleh PIHAK I dan PIHAK II yang ditetapkan dalam Akad ini.</div>
						</td>	
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">7.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Agunan”</b> Adalah jaminan tambahan, baik berupa benda bergerak maupun benda tidak bergerak yang diserahkan oleh Pemilik Agunan kepada PIHAK I guna menjamin pelunasan utang/kewajiban PIHAK II.</div>
						</td>	
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">8.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Dokumen Jaminan”</b> Adalah segala macam dan bentuk surat bukti tentang kepemilikan atau hak-hak lainnya atas barang yang dijadikan jaminan bagi terlaksananya kewajiban PIHAK II terhadap PIHAK I berdasarkan Akad ini.</div>
						</td>	
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">9.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Hari Kerja PIHAK I”</b> Adalah hari pelayanan kantor dari Senin hingga Jumat mulai pukul 08.30 hingga 16.00.</div>
						</td>	
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;font-weight:bold\">10.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\"><b>“Cidera Janji”</b> Adalah keadaan tidak dilaksanakannya sebagian atau seluruh kewajiban PIHAK II yang menyebabkan PIHAK I dapat menghentikan seluruh atau sebagian pembayaran atas harga beli barang termasuk biaya-biaya yang terkait, serta sebelum berakhirnya jangka waktu akad ini menagih dengan seketika dan sekaligus jumlah kewajiban PIHAK II kepada PIHAK I.</div>
						</td>	
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 2</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pembiayaan</b></div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Harga barang berupa .................................................. yang dijual PIHAK I kepada PIHAK II sebagai pembeli disepakati dan diterima dengan harga Rp ".number_format($acctcreditsaccount['credits_account_financing'], 2)." (".strtolower(numtotxt($acctcreditsaccount['credits_account_financing'])).") dengan perincian sebagai berikut :</div>
						</td>			
					</tr>
				</table>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">•	&nbsp; Harga Perolehan</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"3%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"5%\">
							<div style=\"font-size:12px;\">Rp.</div>
						</td>
						<td style=\"text-align:justify;\" width=\"18%\">
							<div style=\"font-size:12px;text-align: right\">".number_format($acctcreditsaccount['credits_account_net_price'], 2)."</div>
						</td>			
					</tr>
					<tr>	
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">•	&nbsp; Keuntungan PIHAK I</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"3%\">
							<div style=\"font-size:12px;\">:</div>
						</td>
						<td style=\"text-align:justify;\" width=\"5%\">
							<div style=\"font-size:12px;\">Rp.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"18%\">
							<div style=\"font-size:12px;text-align: right\">".number_format($acctcreditsaccount['credits_account_margin'], 2)."</div>
						</td>			
					</tr>
					<tr>	
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">•	&nbsp; Harga Jual PIHAK I</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"3%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"5%\">
							<div style=\"font-size:12px;\">Rp.</div>
						</td>
						<td style=\"text-align:justify;\" width=\"18%\">
							<div style=\"font-size:12px;text-align: right\">".number_format($acctcreditsaccount['credits_account_sell_price'], 2)."</div>
						</td>			
					</tr>
					<tr>	
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">•	&nbsp; Uang Muka PIHAK II</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"3%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:justify;border-bottom: 1px solid black\" width=\"5%\">
							<div style=\"font-size:12px;\">Rp.</div>
						</td>
						<td style=\"text-align:justify;border-bottom: 1px solid black\" width=\"18%\">
							<div style=\"font-size:12px;text-align: right\">".number_format($acctcreditsaccount['credits_account_um'], 2)."</div>
						</td>			
					</tr>
					<tr>	
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">•	&nbsp; Pembiayaan yang Diangsur</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"3%\">
							<div style=\"font-size:12px;\">:</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"5%\">
							<div style=\"font-size:12px;\">Rp.</div>
						</td>
						<td style=\"text-align:justify;\" width=\"18%\">
							<div style=\"font-size:12px;text-align: right\">".number_format($acctcreditsaccount['credits_account_financing'], 2)."</div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Sehingga kewajiban atau utang yang harus dibayar oleh PIHAK II kepada PIHAK I adalah Rp ".number_format($acctcreditsaccount['credits_account_financing'], 2)." (".strtolower(numtotxt($acctcreditsaccount['credits_account_financing'])).")</div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Harga jual PIHAK I tersebut pada ayat 2 tidak termasuk biaya-biaya administrasi, seperti biaya notaris, meterai dan lain-lain sejenisnya, yang oleh kedua belah pihak telah disepakati dibebankan sepenuhnya kepada Pihak Kedua.</div>
						</td>			
					</tr>
				</table>

				
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			//--------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 3</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Jangka Waktu</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">PIHAK II berjanji dan dengan ini mengikatkan diri kepada PIHAK I untuk membayar utang sebagaimana tersebut pada pasal 2 akad ini jangka  waktu ".$acctcreditsaccount['credits_account_period']." bulan terhitung sejak tanggal ditanda-tanganinya Akad ini, atau Berakhir pada tanggal ".$date_jt." ".$monthname[$month_jt]." ".$year_jt.", atau dengan cara mengangsur pada tiap bulan pada hari kerja PIHAK I, masing-masing sebesar Rp. ".number_format($acctcreditsaccount['credits_account_payment_amount'],2)." (".strtolower(numtotxt($acctcreditsaccount['credits_account_payment_amount'])).") sesuai dengan jadwal dan besarnya angsuran yang telah ditetapkan. </div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Bila tanggal jatuh tempo atau saat pembayaran angsuran jatuh tidak pada hari kerja PIHAK I, maka PIHAK II berjanji dan dengan ini mengikatkan diri untuk melakukan pembayaran kepada PIHAK I pada hari pertama PIHAK I bekerja kembali.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 4</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Kuasa PIHAK I atas Rekening PIHAK II</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Untuk memenuhi kewajibannya kepada PIHAK I, dengan ini PIHAK II memberi kuasa kepada PIHAK I, yang mana merupakan bagian yang tidak terpisahkan dari Akad ini yang tidak akan berakhir oleh sebab-sebab yang ditentukan dalam KUH Perdata, untuk sewaktu-waktu tanpa persetujuan terlebih dahulu dari PIHAK II, membebani dan/atau mendebet Tabungan dan/atau rekening lain PIHAK II yang ada pada PIHAK I, untuk pembayaran pembiayaan, Denda, Ganti rugi, Premi asuransi, biaya-biaya pengikatan barang Agunan, dan biaya lainnya yang timbul karena dan untuk pelaksanaan akad ini.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 5</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Agunan</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Segala harta kekayaan PIHAK II, baik yang bergerak maupun yang tidak bergerak, baik yang sudah ada maupun yang akan ada dikemudian hari, menjadi jaminan bagi pelunasan seluruh utang PIHAK II yang timbul karena Akad ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Guna lebih menjamin pembayaran kembali utang, PIHAK II menyerahkan Agunan kepada PIHAK I. Perubahan dan penggantian Agunan-agunan tersebut dapat dilakukan berdasarkan kesepakatan tertulis Para Pihak. Sedangkan jenis dan pengikatan Agunan tersebut sebagaimana tercantum dalam rincian sebagai berikut:</div>
						</td>			
					</tr>
				</table>
				<br><br>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">a.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">Tanah dan Bangunan</div>
						</td>			
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"60%\">
							<div style=\"font-size:12px;\"></div>
						</td>			
					</tr>";

					if(!empty($agunansertifikat)){
						foreach ($agunansertifikat as $key => $val) {
							$tblaguanan .= " 
								<tr>
									<td style=\"text-align:left;\" width=\"5%\"></td>
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\"></div>
									</td>	
									<td style=\"text-align:justify;\" width=\"30%\">
										<div style=\"font-size:12px;\">Atas nama</div>
									</td>			
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\">:</div>
									</td>	
									<td style=\"text-align:justify;\" width=\"60%\">
										<div style=\"font-size:12px;\">".$val['credits_agunan_shm_atas_nama']."</div>
									</td>			
								</tr>
								<tr>
									<td style=\"text-align:left;\" width=\"5%\"></td>
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\"></div>
									</td>	
									<td style=\"text-align:justify;\" width=\"30%\">
										<div style=\"font-size:12px;\">No Dokumen</div>
									</td>			
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\">:</div>
									</td>	
									<td style=\"text-align:justify;\" width=\"60%\">
										<div style=\"font-size:12px;\">".$val['credits_agunan_shm_no_sertifikat']."</div>
									</td>			
								</tr>
								<tr>
									<td style=\"text-align:left;\" width=\"5%\"></td>
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\"></div>
									</td>	
									<td style=\"text-align:justify;\" width=\"30%\">
										<div style=\"font-size:12px;\">Luas</div>
									</td>			
									<td style=\"text-align:left;\" width=\"5%\">
										<div style=\"font-size:12px;\">:</div>
									</td>	
									<td style=\"text-align:justify;\" width=\"60%\">
										<div style=\"font-size:12px;\">".$val['credits_agunan_shm_luas']."</div>
									</td>			
								</tr>
								<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Alamat</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_shm_kedudukan']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Taksiran</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">Rp. ".number_format($val['credits_agunan_shm_taksiran'], 2)."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Keterangan</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_shm_keterangan']."</div>
								</td>			
							</tr>
							";
						}
					} else {
						$tblaguanan = " 
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Atas nama</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Dokumen</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Luas</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Alamat</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Taksiran</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Keterangan</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
						";
					}
					

					$tblakhir = "
					
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket.$tblaguanan.$tblakhir, true, false, false, false, '');

			//--------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblket = "
				<br><br>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">Kendaraan / Sepeda Motor  </div>
						</td>			
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"60%\">
							<div style=\"font-size:12px;\"></div>
						</td>			
					</tr>";

				if(!empty($agunanbpkb)){
					foreach ($agunanbpkb as $key => $val) {
						$tblaguananbpkb .= "
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Nama Pemilik</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nama']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Reg</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nomor']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No. Polisi</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_nopol']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Mesin</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_mesin']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Rangka</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">".$val['credits_agunan_bpkb_no_rangka']."</div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Taksiran</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\">Rp. ".number_format($val['credits_agunan_bpkb_taksiran'], 2)."</div>
								</td>			
							</tr>
						";
					}
				} else {
					$tblaguananbpkb = "
						<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Nama Pemilik</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Reg</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No. Polisi</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Mesin</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">No Rangka</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
							<tr>
								<td style=\"text-align:left;\" width=\"5%\"></td>
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\"></div>
								</td>	
								<td style=\"text-align:justify;\" width=\"30%\">
									<div style=\"font-size:12px;\">Taksiran</div>
								</td>			
								<td style=\"text-align:left;\" width=\"5%\">
									<div style=\"font-size:12px;\">:</div>
								</td>	
								<td style=\"text-align:justify;\" width=\"60%\">
									<div style=\"font-size:12px;\"></div>
								</td>			
							</tr>
					";
				}
				
				$tblakhirbpkb ="	
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Pengikatan : Jaminan diikat Hak Tanggungan sebesar Rp. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".number_format($total_agunan, 2)." Biaya pengikatan menjadi beban Pihak Kedua.</div>
						</td>			
					</tr>
				</table>
			";
				
			$pdf->writeHTML($tblket.$tblaguananbpkb.$tblakhirbpkb, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 6</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Biaya, Potongan dan Pajak-Pajak</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">PIHAK II wajib membayar kepada PIHAK I secara  bayar dimuka biaya-biaya sebagai berikut:</div>
						</td>			
					</tr>
				</table>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">a.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; By Jasa Saksi & Juru Tulis</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\">".number_format($acctcreditsaccount['credits_account_notaris'], 2)."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">b.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; By Admin</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\">".number_format($acctcreditsaccount['credits_account_adm_cost'], 2)."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">c.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; By Materai</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\">".number_format($acctcreditsaccount['credits_account_materai'], 2)."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">d.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; Tabungan</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\"></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">e.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; Simp. Pokok</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\"></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">f.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; Asuransi</div>
						</td>
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;\">".number_format($acctcreditsaccount['credits_account_insurance'], 2)."</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\">g.</div>
						</td>	
						<td style=\"text-align:left;\" colspan=\"3\" width=\"80%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; Biaya Notaris dan biaya lainnya yang timbul karena dan untuk pelaksanaan Akad ini.</div>
						</td>		
					</tr>
					<tr>
						<td style=\"text-align:right;\" width=\"10%\">
							<div style=\"font-size:12px;\"></div>
						</td>	
						<td style=\"text-align:justify;\" width=\"30%\">
							<div style=\"font-size:12px;\">&nbsp;&nbsp; Jumlah</div>
						</td>	
						<td style=\"text-align:right;\" width=\"8%\">
							<div style=\"font-size:12px;font-weight:bold\">: Rp</div>
						</td>
						<td style=\"text-align:right;\" width=\"18%\">
							<div style=\"font-size:12px;font-weight:bold\">".number_format($totalbiaya, 2)."</div>
						</td>	
					</tr>
				</table>
				<br><br>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Dalam hal PIHAK II cidera janji tidak melakukan pembayaran/melunasi utangnya ke-pada PIHAK I, sehingga PIHAK I perlu menggunakan jasa Penasihat Hukum/Kuasa untuk menagihnya, maka PIHAK II berjanji dan dengan ini mengikatkan diri untuk membayar seluruh biaya jasa Penasihat Hukum, jasa penagihan dan jasa-jasa lainnya sepanjang hal itu dapat dibuktikan secara sah menurut hukum.<br><br></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">3.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Setiap pembayaran/pelunasan utang sehubungan dengan Akad ini dan/atau akad lain yang terkait dengan Akad ini dan mengikat PIHAK I dan PIHAK II, dilakukan oleh PIHAK II kepada PIHAK I tanpa potongan, pungutan, bea, pajak dan/atau biaya-biaya lainnya, kecuali jika potongan tersebut diharuskan berdasarkan peraturan perundang-undangan yang berlaku.</div>
						</td>			
					</tr>
				</table>
		
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 7</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Hak PIHAK I untuk Mengakhiri Jangka Waktu Utang</b></div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Menyimpang dari jangka waktu yang telah ditentukan dalam Akad ini, PIHAK I dapat mengakhiri jangka waktu utang dengan mengesampingkan ketentuan yang tercantum dalam Kitab Undang-Undang Hukum Perdata, sehingga PIHAK II wajib membayar lunas seketika dan sekaligus seluruh utangnya dalam tenggang waktu yang ditetapkan oleh PIHAK I kepada PIHAK II, apabila PIHAK II dinyatakan cidera janji (wanprestasi) berdasarkan pasal 11 ayat (1) Akad ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Apabila setelah jangka waktu utang karena sebab apapun juga dan menurut pertimbangan PIHAK I, PIHAK II tidak melunasi utangnya berdasarkan akad ini, PIHAK I berhak mengambil tindakan hukum dengan cara apapun dan melaksanakan haknya berdasarkan Akad ini dan/atau dokumen jaminan yang merupakan satu kesatuan dan bagian yang tak terpisahkan dengan Akad ini.</div>
						</td>			
					</tr>
				</table>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			//--------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 8</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Peristiwa Cidera Janji</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Menyimpang dari ketentuan dalam Pasal 4 Akad ini, PIHAK I berhak untuk menagih pembayaran dari PIHAK II atau siapa pun juga yang memperoleh hak darinya, atas seluruh atau sebagian jumlah utang PIHAK II kepada PIHAK I berdasarkan Akad ini, untuk dibayar dengan seketika dan sekaligus, tanpa diperlukan adanya surat pemberitahuan, surat teguran, atau surat lainnya, apabila terjadi salah satu hal atau peristiwa tersebut di bawah ini :</div>
						</td>			
					</tr>
				</table>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">a.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II tidak melaksanakan kewajiban pembayaran/pelunasan utang tepat pada waktu yang diperjanjikan sesuai dengan tanggal jatuh tempo atau jadwal angsuran yang ditetapkan dalam Surat Sanggup Membayar yang telah diserahkan PIHAK II kepada PIHAK I ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">b.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II tidak melakukan pelunasan utang yang jatuh tempo ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">c.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Kekayaan PIHAK II seluruhnya atau sebagian termasuk tetapi tidak terbatas pada barang yang menjadi Agunan, beralih kepada pihak lain, musnah atau hilang, disita oleh instansi yang berwenang atau mendapat tuntutan dari pihak lain yang menurut pertimbangan PIHAK I dapat mempengaruhi kondisi Utang dan/atau PIHAK II ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">d.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II melakukan perbuatan dan/atau terjadinya peristiwa dalam bentuk dan dengan nama apapun yang atas pertimbangan PIHAK I dapat mengancam kelangsungan pembayaran Utang PIHAK II sehingga kewajiban PIHAK II kepada PIHAK I menjadi tidak terjamin sebagaimana mestinya ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">e.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II dinyatakan tidak berhak lagi menguasai harta kekayaannya baik menurut peraturan perundang-undangan maupun menurut putusan pengadilan, termasuk tetapi tidak terbatas pada pernyataan pailit oleh Pengadilan dan/atau PIHAK II likuidasi ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">f.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Bilamana terhadap PIHAK II diajukan gugatan perdata atau tuntutan pidana dan/atau terdapat putusan atas perkara-perkara tersebut yang menurut pertimbangan PIHAK I pertimbangan mana adalah mengikat terhadap PIHAK II dapat mempengaruhi kemampuan PIHAK II untuk membayar kembali utang kepada PIHAK I ;</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">g.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Terdapat kewajiban atau utang kewajiban pembayaran berdasarkan akad yang dibuat PIHAK II dengan pihak lain, baik sekarang ataupun dikemudian hari, menjadi dapat ditagih pembayarannya dan sekaligus sebelum tanggal pembayaran yang telah ditetapkan, disebabkan PIHAK II melakukan kelalaian atau pelanggaran terhadap akad tersebut.</div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">PIHAK II menyetujui bahwa apabila terjadi kejadian cidera janji sebagaimana dimaksud dalam ayat (1) pasal ini, maka PIHAK I secara sepihak dapat:</div>
						</td>			
					</tr>
				</table>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">a.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Melakukan penyelamatan dan penyelesaian utang sebagaimana dimaksud dalam pasal 12 akad ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">b.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Mengakhiri jangka waktu utang sebagaimana dimaksud dalam pasal 11 akad ini.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 9</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Kewenangan PIHAK I Dalam Rangka,Penyelamatan dan Penyelesaian Utang</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Dalam rangka penyelamatan dan penyelesaian Utang, PIHAK I berwenang melakukan hal-hal sebagai berikut:</div>
						</td>			
					</tr>
				</table>
				<br><br>
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">a.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Menggunakan jasa pihak ketiga untuk melakukan penagihan, pelunasan utang, apabila dianggap perlu oleh PIHAK I</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">b.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Mengumumkan nama PIHAK II berikut agunannya, apabila menurut penilaian PIHAK I, PIHAK II tidak dapat melaksanakan pembayaran utang</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">c.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II mengijinkan memasuki objek Agunan untuk memasang papan tanda, stiker, atau bentuk-bentuk lainnya yang dipasang ke atau dituliskan pada objek Agunan Utang.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">d.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">PIHAK II menyetujui bahwa tindakan-tindakan yang dilakukan PIHAK I dalam pasal ini bukan merupakan pencemaran nama baik PIHAK II ataupun perbuatan tidak menyenangkan dan bukan pula tindakan yang melanggar hukum, sehingga PIHAK II tidak akan mengajukan gugatan perdata maupun pengaduan pidana.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\"></td>	
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">e.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"90%\">
							<div style=\"font-size:12px;\">Melakukan tindakan-tindakan dan upaya-upaya hukum lainnya yang dianggap perlu oleh PIHAK I sebagai upaya penyelamatan dan penyelesaian utang, baik yang dilakukan sendiri oleh PIHAK I maupun oleh pihak ketiga yang ditunjuk.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			//--------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 10</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Penyelesaian Perselisihan</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Dalam hal terjadi perbedaan pendapat atau penafsiran atas hal-hal yang tercantum di dalam Surat Akad ini atau terjadi perselisihan atau sengketa dalam pelaksanaannya, para pihak sepakat untuk menyelesaikannya secara musyawarah untuk mufakat.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Apabila musyawarah untuk mufakat telah diupayakan namun perbedaan pendapat atau penafsiran, perselisihan atau sengketa tidak dapat diselesaikan oleh kedua belah pihak, maka para pihak bersepakat, dan dengan ini berjanji serta mengikatkan diri satu ter-hadap yang lain, untuk menyelesaikannya melalui Pengadilan Agama Kabupaten Trenggalek</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">3.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Para pihak sepakat, dan dengan ini mengikatkan diri satu terhadap yang lain, bahwa pendapat hukum (legal opinion) dan/atau putusan yang ditetapkan oleh Pengadilan Agama Kabupaten Trenggalek tersebut bersifat final dan mengikat (final and binding).</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 11</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Domisili dan Pemberitahuan</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Alamat para pihak sebagaimana yang tercantum pada kalimat-kalimat awal Surat Akad ini merupakan alamat tetap dan tidak berubah bagi masing-masing pihak yang bersangkutan, dan ke alamat-alamat itu pula secara sah segala surat-menyurat atau komunikasi di antara kedua pihak akan dilakukan.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Apabila dalam pelaksanaan akad ini terjadi perubahan alamat, maka pihak yang berubah alamatnya tersebut wajib memberitahukan kepada pihak lainnya alamat barunya dengan surat tercatat atau surat tertulis yang disertai tanda bukti penerimaan dari pihak lainnya.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">3.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Selama tidak ada pemberitahuan tentang perubahan alamat sebagaimana dimaksud pada ayat 2 pasal ini, maka surat-menyurat atau komunikasi yang dilakukan ke alamat yang tercantum pada awal Surat Akad dianggap sah menurut hukum.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 12</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Addendum</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">Hal-hal yang belum diatur dan/atau belum cukup diatur dan/atau diperlukan perubahan syarat-syarat dalam Akad ini, para pihak sepakat untuk menuangkan dalam suatu Persetujuan Perubahan Akad Utang yang ditandatangani oleh Para Pihak, yang merupakan satu kesatuan serta bagian yang tidak terpisahkan dari Akad ini.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			//--------------------------------------------------------------------------------------------------------------------------------

			// add a page
			$pdf->AddPage();
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Pasal 13</b></div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:12px\"><b>Penutup</b></div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">1.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Sebelum Surat Akad ini ditandatangani oleh PIHAK II, PIHAK II mengakui dengan sebenarnya, dan tidak lain dari yang sebenarnya, bahwa PIHAK II telah membaca dengan cermat atau dibacakan kepadanya seluruh isi Akad ini berikut semua surat dan/atau dokumen yang menjadi lampiran Surat Akad ini, sehingga oleh karena itu PIHAK II memahami sepenuhnya segala yang akan menjadi akibat hukum setelah PIHAK II menandatangani Surat Akad ini.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">2.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Apabila ada hal-hal yang belum diatur atau belum cukup diatur dalam Akad ini, maka PIHAK II dan PIHAK I akan mengaturnya bersama secara musyawarah untuk mufakat dalam suatu Addendum.</div>
						</td>			
					</tr>
					<tr>
						<td style=\"text-align:left;\" width=\"5%\">
							<div style=\"font-size:12px;\">3.</div>
						</td>	
						<td style=\"text-align:justify;\" width=\"95%\">
							<div style=\"font-size:12px;\">Tiap Addendum dari Akad ini merupakan satu kesatuan yang tidak terpisahkan dari Akad ini.</div>
						</td>			
					</tr>
				</table>
				<br><br>
			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			$tblket = "			
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:justify;\" width=\"100%\">
							<div style=\"font-size:12px;\">
								<p>Pihak Pertama dan Pihak Kedua sepakat dan dengan ini mengikatkan diri satu terhadap yang lain, bahwa untuk Akad ini dan segala akibatnya memberlakukan syariah Islam dan peraturan perundang-undangan lain yang tidak bertentangan dengan syariah.</p>
								<p>Demikianlah, Surat Akad ini dibuat dan ditandatangani oleh PIHAK I dan PIHAK II di atas kertas yang bermeterai cukup dalam dua rangkap, yang masing-masing disimpan oleh PIHAK I dan PIHAK II, dan masing-masing berlaku sebagai aslinya.</p></div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:right;\" width=\"100%\">
							<div style=\"font-size:12px;\">
								".$this->AcctCreditAccount_model->getBranchCity($acctcreditsaccount['branch_id']).", ".date('d-m-Y')."</div>
						</td>			
					</tr>
				</table>
				<br><br>

				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>	
						<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
							<div style=\"font-size:12px;\">
								PIHAK I,</div>
						</td>
						<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
							<div style=\"font-size:12px;\">
								PIHAK II,</div>
						</td>			
					</tr>
					<tr>	
						<td style=\"text-align:center;\" width=\"50%\">
							<div style=\"font-size:12px;font-weight:bold\">
								".strtoupper($this->AcctCreditAccount_model->getBranchManager($acctcreditsaccount['branch_id']))."</div>
						</td>
						<td style=\"text-align:center;\" width=\"50%\" >
							<div style=\"font-size:12px;font-weight:bold\">
								".strtoupper($acctcreditsaccount['member_name'])."</div>
						</td>			
					</tr>
				</table>

			";
				
			$pdf->writeHTML($tblket, true, false, false, false, '');

			ob_clean();

			$filename = 'Akad_'.$credits_name.'_'.$acctcreditsaccount['member_name'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
	}
?>