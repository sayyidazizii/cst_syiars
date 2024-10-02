<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 600);
	Class PPOBRefund extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PPOBRefund_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Android_model');
			$this->load->model('AndroidPPOB_model');
			$this->load->model('TopupPPOB_model');
			$this->load->model('SettingPrice_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			/* $this->load->library(array('PHPExcel','PHPExcel/IOFactory')); */
		}
		
		public function addRefundTransaction(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-PPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('addPPOBRefund-'.$unique['unique']);
			$this->session->unset_userdata('PPOBRefundtoken-'.$unique['unique']);

			$data['main_view']['successtransaction']		= $this->PPOBRefund_model->getSuccessTransaction($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']);
			$data['main_view']['corebranch']				= create_double($this->PPOBRefund_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']					= 'PPOBRefund/ListSuccessTransaction_view';
			$this->load->view('MainPage_view',$data);
		}

		public function index(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-PPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('addPPOBRefund-'.$unique['unique']);
			$this->session->unset_userdata('PPOBRefundtoken-'.$unique['unique']);

			$data['main_view']['refundtransaction']		= $this->PPOBRefund_model->getRefundTransaction();
			$data['main_view']['corebranch']		= create_double($this->PPOBRefund_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'PPOBRefund/ListRefundTransaction_view';
			$this->load->view('MainPage_view',$data);
		}

		public function detailRefundTransaction(){
			$auth 					= $this->session->userdata('auth');
			$ppob_transaction_id 	= $this->uri->segment(3);

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('addPPOBRefund-'.$unique['unique']);
			$this->session->unset_userdata('PPOBRefundtoken-'.$unique['unique']);

			$data['main_view']['transactiondetail']		= $this->PPOBRefund_model->getTransactionDetail($ppob_transaction_id);
			$data['main_view']['content']				= 'PPOBRefund/FormDetailRefundTransaction_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-PPOBRefund',$data);
			redirect('PPOBRefund/addRefundTransaction');
		}

		public function filter_refund(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-PPOBRefund',$data);
			redirect('PPOBRefund');
		}

		public function getPPOBRefundList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-PPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}



			
			$list = $this->PPOBRefund_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $savingsaccount) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $savingsaccount->savings_account_no;
				$row[] = $savingsaccount->member_name;
				$row[] = $savingsaccount->savings_name;
				$row[] = tgltoview($savingsaccount->savings_account_date);
				$row[] = number_format($savingsaccount->savings_account_first_deposit_amount, 2);
				$row[] = number_format($savingsaccount->savings_account_last_balance, 2);
				if($savingsaccount->validation == 0){
					$row[] = '<a href="'.base_url().'PPOBRefund/printNotePPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>
						<a href="'.base_url().'PPOBRefund/validationPPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs green-jungle" role="button"><i class="fa fa-check"></i> Validasi</a>
						<a href="'.base_url().'PPOBRefund/editPPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-edit"></i> Edit</a>';
						// <a href="'.base_url().'PPOBRefund/voidPPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs red" role="button"><i class="fa fa-trash-o"></i> Batal</a>';
				} else {
					$row[] = '<a href="'.base_url().'PPOBRefund/printNotePPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>
						<a href="'.base_url().'PPOBRefund/editPPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs purple" role="button"><i class="fa fa-edit"></i> Edit</a>';
						// <a href="'.base_url().'PPOBRefund/voidPPOBRefund/'.$savingsaccount->savings_account_id.'" class="btn btn-xs red" role="button"><i class="fa fa-trash-o"></i> Batal</a>';
				}
				
				$data[] = $row;
			}



			// print_r($list);exit;
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->PPOBRefund_model->count_all_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
							"recordsFiltered" => $this->PPOBRefund_model->count_filtered_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function getMasterDataSavingsAccount(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-masterdataPPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}

			if(empty($sesi['branch_id'])){
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}
			

			$list = $this->PPOBRefund_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
			
			$count_data = count($list);

			$rows 		= ceil($count_data / 1000);

			// print_r($rows);exit;

			$data['main_view']['acctsavings']		= create_double($this->PPOBRefund_model->getAcctSavings(),'savings_id', 'savings_name');
			$data['main_view']['corebranch']		= create_double($this->PPOBRefund_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['file']				= $rows;
			$data['main_view']['content']			= 'PPOBRefund/ListMasterDataSavingsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filtermasterdata(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"savings_id"	=> $this->input->post('savings_id',true),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-masterdataPPOBRefund',$data);
			redirect('PPOBRefund/getMasterDataSavingsAccount');
		}

		public function getMasterDataSavingsAccountList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-masterdataPPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}

			if(empty($sesi['branch_id'])){
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}
			

				/*  print_r($sesi);exit; */

			$list = $this->PPOBRefund_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);

			/* print_r($list);exit; */
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $savingsaccount) {

				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $savingsaccount->savings_account_no;
				$row[] = $savingsaccount->member_name;
				$row[] = $savingsaccount->member_address;
				$row[] = $savingsaccount->savings_name;
				$row[] = tgltoview($savingsaccount->savings_account_date);
				$row[] = number_format($savingsaccount->savings_account_first_deposit_amount, 2);
				$row[] = number_format($savingsaccount->savings_account_last_balance, 2);
				$data[] = $row;
			}



			// print_r($list);exit;
	
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->PPOBRefund_model->count_all_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
				"recordsFiltered" => $this->PPOBRefund_model->count_filtered_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
				"data" => $data,
			);

			// print_r($output['recordsTotal']);exit;
			//output to json format
			echo json_encode($output);
		}

		public function export(){
			$baris 	= $this->uri->segment(3);
			$key 	= $this->uri->segment(4);


			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-masterdataPPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}

			if(empty($sesi['branch_id'])){
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}

			$list 	= $this->PPOBRefund_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
			$no 	= 0;

			foreach ($list as $savingsaccount) {

				$no++;
				$data[] = array(
					'savings_account_no' 					=> $savingsaccount->savings_account_no,
					'member_name' 							=> $savingsaccount->member_name,	
					'member_address' 						=> $savingsaccount->member_address,
					'savings_name' 							=> $savingsaccount->savings_name,		
					'city_id'								=> $savingsaccount->city_id,
					'kecamatan_id'							=> $savingsaccount->kecamatan_id,
					'kelurahan_id'							=> $savingsaccount->kelurahan_id,			
					'savings_account_date' 					=> tgltoview($savingsaccount->savings_account_date),
					'savings_account_first_deposit_amount' 	=> number_format($savingsaccount->savings_account_first_deposit_amount, 2),
					'savings_account_last_balance' 			=> number_format($savingsaccount->savings_account_last_balance, 2),
				);
			}

			$sisa = $no % 1000;

			for ($i=0; $i < $baris ; $i++) {
				
				if($i == $baris){
					$rows = $sisa;
				} else {
					$rows = 1000;
				}

				$array_terpecah[$i] = array_splice($data, 0, $rows);

				
			}

			$datacetak = $array_terpecah[$key];

			$this->exportMasterDataPPOBRefund($datacetak);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPPOBRefund-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addPPOBRefund-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addPPOBRefund-'.$unique['unique']);
			redirect('PPOBRefund/addPPOBRefund');
		}

		public function getListCoreMember(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$branch_id = '';
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = '<a href="'.base_url().'PPOBRefund/addPPOBRefund/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
				$data[] = $row;
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
		
		public function addPPOBRefund(){
			$member_id 	= $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');
			$token 		= $this->session->userdata('PPOBRefundtoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('PPOBRefundtoken-'.$unique['unique'], $token);
			}


			$data['main_view']['coremember']			= $this->PPOBRefund_model->getCoreMember_Detail($this->uri->segment(3));	
			$data['main_view']['acctsavings']			= create_double($this->PPOBRefund_model->getAcctSavings(),'savings_id', 'savings_name');	
			$data['main_view']['coreoffice']			= create_double($this->PPOBRefund_model->getCoreOffice(),'office_id', 'office_name');	
			$data['main_view']['membergender']			= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['familyrelationship']	= $this->configuration->FamilyRelationship();
			$data['main_view']['content']				= 'PPOBRefund/FormAddPPOBRefund_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getCoreMember_Detail(){
			$member_id 	= $this->input->post('member_id');

			// $member_id = 25;
			
			$data 			= $this->PPOBRefund_model->getCoreMember_Detail($member_id);
			// print_r($data);
			$membergender	= $this->configuration->MemberGender();
			$memberidentity = $this->configuration->MemberIdentity();

			$result = array();
			$result = array(
				"member_no"					=> $data['member_no'], 
				"member_date_of_birth" 		=> $data['member_date_of_birth'], 
				"member_gender"				=> $membergender[$data['member_gender']],
				"member_address"			=> $data['member_address'],
				"city_name"					=> $data['city_name'],
				"kecamatan_name"			=> $data['kecamatan_name'],
				"member_job"				=> $data['member_job'],
				"identity_name"				=> $memberidentity[$data['identity_id']],
				"member_identity_no"		=> $data['member_identity_no'],
				"member_phone"				=> $data['member_phone'],
			);
			echo json_encode($result);		
		}

		public function getSavingsAccountNo(){
			$auth = $this->session->userdata('auth');

			$savings_id 	= $this->input->post('savings_id');

			// $savings_id = 3;
			
			$branchcode = $this->PPOBRefund_model->getBranchCode($auth['branch_id']);
			$savingscode = $this->PPOBRefund_model->getSavingsCode($savings_id);
			$lastsavingsaccountno = $this->PPOBRefund_model->getLastAccountSavingsNo($auth['branch_id'], $savings_id);
			$savingsnisbah = $this->PPOBRefund_model->getSavingsNisbah($savings_id);

			if($lastsavingsaccountno->num_rows() <> 0){      
			//jika kode ternyata sudah ada.      
			$data = $lastsavingsaccountno->row_array();    
			$kode = intval($data['last_savings_account_no']) + 1;    
			} else {      
			//jika kode belum ada      
			$kode = 1;    
			}
			
			$kodemax 				= str_pad($kode, 5, "0", STR_PAD_LEFT);
			$new_savings_account_no = $savingscode.$branchcode.$kodemax;

			$result = array ();
			$result = array (
				'savings_account_no'		=> $new_savings_account_no,
				'savings_nisbah'			=> $savingsnisbah,
			);

			echo json_encode($result);			
		}
		
		public function processAddPPOBRefund(){
			$auth = $this->session->userdata('auth');

			$username = $this->PPOBRefund_model->getUsername($auth['user_id']);

			
			$data = array(
				'member_id'									=> $this->input->post('member_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'office_id'									=> $this->input->post('office_id', true),
				'savings_account_date'						=> date('Y-m-d'),
				'branch_id'									=> $auth['branch_id'],
				'savings_account_no'						=> $this->input->post('savings_account_no', true),
				'savings_account_first_deposit_amount'		=> $this->input->post('savings_account_first_deposit_amount', true),
				'savings_account_last_balance'				=> $this->input->post('savings_account_first_deposit_amount', true),
				'savings_account_adm_amount'				=> $this->input->post('savings_account_adm_amount', true),
				'savings_member_heir'						=> $this->input->post('savings_member_heir', true),
				'savings_member_heir_address'				=> $this->input->post('savings_member_heir_address', true),
				'savings_member_heir_relationship'			=> $this->input->post('savings_member_heir_relationship', true),
				'savings_account_token'						=> $this->input->post('savings_account_token', true),
				'operated_name'								=> $username,
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('member_id', 'Anggota', 'required');
			$this->form_validation->set_rules('savings_id', 'Jenis Simpanan', 'required');
			$this->form_validation->set_rules('savings_account_first_deposit_amount', 'Setoran', 'required');
			$this->form_validation->set_rules('savings_account_adm_amount', 'Biaya Adm', 'required');

			$transaction_module_code 	= "TAB";
			$transaction_module_id 		= $this->PPOBRefund_model->getTransactionModuleID($transaction_module_code);

			$savings_account_token 		= $this->PPOBRefund_model->getSavingsAccountToken($data['savings_account_token']);
			
			if($this->form_validation->run()==true){
				if($savings_account_token->num_rows() == 0){
					if($this->PPOBRefund_model->insertPPOBRefund($data)){
						$PPOBRefund_last 	= $this->PPOBRefund_model->getPPOBRefund_Last($data['created_on']);
							
						$journal_voucher_period = date("Ym", strtotime($data['savings_account_date']));
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TABUNGAN '.$PPOBRefund_last['member_name'],
							'journal_voucher_description'	=> 'SETORAN TABUNGAN '.$PPOBRefund_last['member_name'],
							'journal_voucher_token'			=> $data['savings_account_token'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $PPOBRefund_last['savings_account_id'],
							'transaction_journal_no' 		=> $PPOBRefund_last['savings_account_no'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->PPOBRefund_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id = $this->PPOBRefund_model->getJournalVoucherID($data['created_id']);

						$preferencecompany = $this->PPOBRefund_model->getPreferenceCompany();

						$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_account_first_deposit_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_account_first_deposit_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['savings_account_token'].$preferencecompany['account_cash_id'],
						);

						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_debet);

						$account_id = $this->PPOBRefund_model->getAccountID($data['savings_id']);

						$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($account_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_account_first_deposit_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_account_first_deposit_amount'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['savings_account_token'].$account_id,
						);

						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_credit);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferencecompany['account_cash_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_account_adm_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_account_adm_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['savings_account_token'].$data['savings_account_adm_amount'],
						);

						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_debet);

						$preferenceinventory = $this->PPOBRefund_model->getPreferenceInventory();

						$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceinventory['inventory_adm_id'],
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $data['savings_account_adm_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_account_adm_amount'],
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['savings_account_token'].$preferenceinventory['inventory_adm_id'],
						);

						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_credit);

						// $auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addPPOBRefund-'.$sesi['unique']);
						$this->session->unset_userdata('PPOBRefundtoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('PPOBRefund/printNotePPOBRefund/'.$PPOBRefund_last['savings_account_id']);
					}else{
						// $this->session->set_userdata('addPPOBRefund',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('PPOBRefund');
					}
				} else {
					$PPOBRefund_last 	= $this->PPOBRefund_model->getPPOBRefund_Last($data['created_on']);
						
					$journal_voucher_period = date("Ym", strtotime($data['savings_account_date']));
					
					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'SETORAN TABUNGAN '.$PPOBRefund_last['member_name'],
						'journal_voucher_description'	=> 'SETORAN TABUNGAN '.$PPOBRefund_last['member_name'],
						'journal_voucher_token'			=> $data['savings_account_token'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $PPOBRefund_last['savings_account_id'],
						'transaction_journal_no' 		=> $PPOBRefund_last['savings_account_no'],
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);

					$journal_voucher_token = $this->PPOBRefund_model->getJournalVoucherToken($data['savings_account_token']);

					if($journal_voucher_token->num_rows() == 0){
						$this->PPOBRefund_model->insertAcctJournalVoucher($data_journal);
					}
					
					$journal_voucher_id = $this->PPOBRefund_model->getJournalVoucherID($data['created_id']);

					$preferencecompany = $this->PPOBRefund_model->getPreferenceCompany();

					$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['savings_account_first_deposit_amount'],
						'journal_voucher_debit_amount'	=> $data['savings_account_first_deposit_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data['savings_account_token'].$preferencecompany['account_cash_id'],
					);

					$journal_voucher_item_token = $this->PPOBRefund_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_debet);
					}

					$account_id = $this->PPOBRefund_model->getAccountID($data['savings_id']);

					$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($account_id);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $account_id,
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['savings_account_first_deposit_amount'],
						'journal_voucher_credit_amount'	=> $data['savings_account_first_deposit_amount'],
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['savings_account_token'].$account_id,
					);

					$journal_voucher_item_token = $this->PPOBRefund_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_credit);
					}

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferencecompany['account_cash_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['savings_account_adm_amount'],
						'journal_voucher_debit_amount'	=> $data['savings_account_adm_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data['savings_account_token'].$data['savings_account_adm_amount'],
					);

					$journal_voucher_item_token = $this->PPOBRefund_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_debet);
					}

					$preferenceinventory = $this->PPOBRefund_model->getPreferenceInventory();

					$account_id_default_status = $this->PPOBRefund_model->getAccountIDDefaultStatus($preferenceinventory['inventory_adm_id']);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferenceinventory['inventory_adm_id'],
						'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
						'journal_voucher_amount'		=> $data['savings_account_adm_amount'],
						'journal_voucher_credit_amount'	=> $data['savings_account_adm_amount'],
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['savings_account_token'].$preferenceinventory['inventory_adm_id'],
					);

					$journal_voucher_item_token = $this->PPOBRefund_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PPOBRefund_model->insertAcctJournalVoucherItem($data_credit);
					}

					// $auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Rekening Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addPPOBRefund-'.$sesi['unique']);
						$this->session->unset_userdata('PPOBRefundtoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('PPOBRefund/printNotePPOBRefund/'.$PPOBRefund_last['savings_account_id']);
				}
				
			}else{
				$this->session->set_userdata('addPPOBRefund',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund');
			}
		}

		public function processAddRefundTransaction(){
			$auth = $this->session->userdata('auth');
			$ppob_transaction_id = $this->uri->segment(3);

			$data = array(
				'ppob_transaction_id'		=> $ppob_transaction_id,
				'ppob_transaction_status'	=> 3,
			);

			$data_log = array(
				'ppob_transaction_id'		=> $ppob_transaction_id,
				'ppob_transaction_status'	=> 3,
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d'),
				'last_update'				=> date('Y-m-d H:i:s'),
			);
			
			if($this->PPOBRefund_model->updatePPOBRefund($data)){
				$this->PPOBRefund_model->insertPPOBRefundLog($data_log);

				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Refund Transaksi Sukses
						</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addPPOBRefund-'.$sesi['unique']);
				$this->session->unset_userdata('PPOBRefundtoken-'.$sesi['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund/addRefundTransaction');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Refund Transaksi Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund/addRefundTransaction');
			}
		}
		
		public function processRefundTransaction(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'ppob_transaction_id'		=> $this->input->post('ppob_transaction_id',true),
				'ppob_transaction_status'	=> 4,
			);

			$data_log = array(
				'ppob_transaction_id'				=> $this->input->post('ppob_transaction_id',true),
				'ppob_transaction_status'			=> 4,
				'ppob_transaction_refund_remark' 	=> $this->input->post('remark',true),
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d'),
				'last_update'						=> date('Y-m-d H:i:s'),
			);
			
			if($this->PPOBRefund_model->updatePPOBRefund($data)){
				$this->PPOBRefund_model->insertPPOBRefundLog($data_log);

				//JURNAL---------------------------------------------------------------------------
				/* SAVINGS TRANSFER TO */

				$member_name    = $this->AcctSavingsTransferMutation_model->getMemberName($this->input->post('member_id',true));
				$ppobproduct    = $this->AndroidPPOB_model->getPPOBProduct_DetailByID($this->input->post('ppob_product_id',true));

				if($ppobproduct['ppob_product_category_id'] == 33 || $ppobproduct['ppob_product_category_id'] == 35){
					//PULSA SAMA TElKOM
					$data = array (
						'branch_id'             => $this->input->post('branch_id', true),
						'ppob_company_id'       => $this->input->post('ppob_company_id',true),
						'member_id'             => $this->input->post('member_id', true),
						'member_name'           => $member_name,
						'product_name'          => $ppobproduct['ppob_product_name'],
						'ppob_agen_price'       => $this->input->post('ppob_transaction_amount',true),
						'ppob_company_price'    => $this->input->post('ppob_transaction_default_amount',true),
						'ppob_fee'              => $this->input->post('ppob_transaction_fee_amount',true),
					);
				}else{
					$data           = array (
						'branch_id'                     => $this->input->post('branch_id',true),
						'ppob_company_id'               => $this->input->post('ppob_company_id',true),
						'member_id'                     => $this->input->post('member_id',true),
						'member_name'                   => $member_name,
						'product_name'                  => $ppobproduct['ppob_product_name'],
						'ppob_agen_price'               => $this->input->post('ppob_transaction_amount',true),
						'ppob_company_price'            => $this->input->post('ppob_transaction_default_amount',true),
						'ppob_admin'                    => 0,
						'ppob_fee'                      => $this->input->post('ppob_transaction_fee_amount',true),
						'ppob_commission'               => $this->input->post('ppob_transaction_commission_amount',true),
						'savings_account_id'            => $this->input->post('savings_account_id',true),
						'savings_id'                    => $this->input->post('savings_id',true),
						'journal_status'                => 1,
					);
				}

				/* print_r("data ");
				print_r($data); */

				$preferenceppob 			= $this->AndroidPPOB_model->getPreferencePpob();

				$data_transfermutationto = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $data['ppob_agen_price'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationto)){
					$transaction_module_code 	        = "TRPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationto['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


					/* SIMPAN DATA TRANSFER TO */

					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datato = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_to_amount'		=> $data['ppob_agen_price'],
						'savings_account_last_balance'				=> $savings_account_opening_balance + $data['ppob_agen_price'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){   
						$acctsavingstr_last 		= $this->AndroidPPOB_model->getAcctSavingsTransferMutationTo_Last($data_transfermutationto['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationto['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationto['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationto['created_id'],
							'created_on' 					=> $data_transfermutationto['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationto['created_id']);


						/* SIMPAN DATA JOURNAL DEBIT */
						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

						if ($data['ppob_admin'] > 0){
							$ppob_company_price             = $data['ppob_company_price'];
							$ppob_admin                     = $data['ppob_admin'];
							$journal_voucher_amount         = $ppob_company_price + $ppob_admin;
						} else {
							$journal_voucher_amount         = $data['ppob_company_price'];
						}

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_down_payment'],
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $journal_voucher_amount,
							'journal_voucher_debit_amount'	=> $journal_voucher_amount,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data['ppob_fee'] + $data['ppob_commission'],
							'journal_voucher_debit_amount'	=> $data['ppob_fee'] + $data['ppob_commission'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						
						/* SIMPAN DATA JOURNAL CREDIT */
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationto['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationto['savings_transfer_mutation_amount'],
							'account_id_status'				=> 1,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);

					}

				}


				/* SAVINGS TRANSFER FROM */

				$data_transfermutationfrom = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $data['ppob_commission'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfrom)){
					$transaction_module_code 	        = "PSPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationfrom['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

					/* SIMPAN DATA TRANSFER FROM */
					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datafrom = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_from_amount'		=> $data['ppob_commission'],
						'savings_account_last_balance'				=> $savings_account_opening_balance - $data['ppob_commission'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
						$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutationfrom['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfrom['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationfrom['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationfrom['created_id'],
							'created_on' 					=> $data_transfermutationfrom['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfrom['created_id']);


						//----- Simpan data jurnal debit
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Bagi Hasil PPOB '.$member_name,
							'journal_voucher_amount'		=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


						/* SIMPAN DATA JOURNAL KREDIT */

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_kredit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Bagi Hasil PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_kredit);
					}          
				}



				/* SIMPAN TRANSFER FROM FEE BASE PPOB */
				$data_transfermutationfromfeebase = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $preferenceppob['ppob_mbayar_admin'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfromfeebase)){
					$transaction_module_code 	        = "FBPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationfromfeebase['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


					/* SIMPAN DATA TRANSFER TO */

					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datato = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_to_amount'		=> $preferenceppob['ppob_mbayar_admin'],
						'savings_account_last_balance'				=> $savings_account_opening_balance + $preferenceppob['ppob_mbayar_admin'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){   
						$acctsavingstr_last 		= $this->AndroidPPOB_model->getAcctSavingsTransferMutationTo_Last($data_transfermutationfromfeebase['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfromfeebase['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationfromfeebase['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND FEE BASE PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND FEE BASE PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationfromfeebase['created_id'],
							'created_on' 					=> $data_transfermutationfromfeebase['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfromfeebase['created_id']);


						/* SIMPAN DATA JOURNAL DEBIT */

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Fee Base PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						
						/* SIMPAN DATA JOURNAL KREDIT */
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Fee Base PPOB '.$member_name,
							'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
					}

				}

				$ppob_company_last_balance = $this->AndroidPPOB_model->getPPOBCompanyBalance($data['ppob_company_id']);
				$ppob_company_balance = $ppob_company_last_balance + $data['ppob_agen_price'];

				$data_company = array(
					'ppob_company_id'				=> $data['ppob_company_id'],
					'ppob_company_balance'			=> $ppob_company_balance,
				);
				$this->AndroidPPOB_model->updatePPOBCompanyBalance($data_company);

				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Refund Transaksi Sukses
						</div> ";
				$sesi = $this->session->userdata('unique');
				$this->session->unset_userdata('addPPOBRefund-'.$sesi['unique']);
				$this->session->unset_userdata('PPOBRefundtoken-'.$sesi['unique']);
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Refund Transaksi Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund');
			}
		}

		public function printNotePPOBRefund(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);
			$preferencecompany 	= $this->PPOBRefund_model->getPreferenceCompany();
			$PPOBRefund	= $this->PPOBRefund_model->getPPOBRefund_Detail($savings_account_id);


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
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI SETORAN AWAL SIMPANAN</div></td>
				</tr>
				<tr>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima uang dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$PPOBRefund['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$PPOBRefund['savings_account_no']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$PPOBRefund['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($PPOBRefund['savings_account_first_deposit_amount'])."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: SETORAN AWAL SIMPANAN</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($PPOBRefund['savings_account_first_deposit_amount'], 2)."</div></td>
				</tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">".$this->PPOBRefund_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
				</tr>
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
				</tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function validationPPOBRefund(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);

			$data = array (
				'savings_account_id'  	=> $savings_account_id,
				'validation'			=> 1,
				'validation_id'			=> $auth['user_id'],
				'validation_on'			=> date('Y-m-d H:i:s'),
			);

			if($this->PPOBRefund_model->validationPPOBRefund($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund/printValidationPPOBRefund/'.$savings_account_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund');
			}
		}

		public function printValidationPPOBRefund(){
			$savings_account_id = $this->uri->segment(3);
			$PPOBRefund	= $this->PPOBRefund_model->getPPOBRefund_Detail($savings_account_id);


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

			$pdf->SetFont('helveticaI', '', 7);

			// -----------------------------------------------------------------------------

			$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				<tr>
					<td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$PPOBRefund['savings_account_no']."</div></td>
					<td width=\"40%\"><div style=\"text-align: right; font-size:14px\">".$PPOBRefund['member_name']."</div></td>
					<td width=\"5%\"><div style=\"text-align: right; font-size:14px\">".$PPOBRefund['office_id']."</div></td>
				</tr>
				<tr>
					<td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$PPOBRefund['validation_on']."</div></td>
					<td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->PPOBRefund_model->getUsername($PPOBRefund['validation_id'])."</div></td>
					<td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($PPOBRefund['savings_account_first_deposit_amount'], 2)."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			
			
			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Validasi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function voidPPOBRefund(){
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['PPOBRefund']		= $this->PPOBRefund_model->getPPOBRefund_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'PPOBRefund/FormVoidPPOBRefund_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidPPOBRefund(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_account_id"	=> $this->input->post('savings_account_id',true),
				"voided_on"				=> date('Y-m-d H:i:s'),
				'data_state'			=> 2,
				"voided_remark" 		=> $this->input->post('voided_remark',true),
				"voided_id"				=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->PPOBRefund_model->voidPPOBRefund($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBRefund');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBRefund');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund');
			}
		}
		
		public function exportMasterDataPPOBRefund($data){	
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-masterdataPPOBRefund');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}


			
			if(count($data) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									->setLastModifiedBy("SIS")
									->setTitle("Master Data Simpanan")
									->setSubject("")
									->setDescription("Master Data Simpanan")
									->setKeywords("Master, Data, Simpanan")
									->setCategory("Master Data Simpanan");
									
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:I1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Simpanan");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Alamat Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Jenis Simpanan");
				$this->excel->getActiveSheet()->setCellValue('F3',"No. Rekening");
				$this->excel->getActiveSheet()->setCellValue('G3',"Tanggal Buka");
				$this->excel->getActiveSheet()->setCellValue('H3',"Setoran Awal");
				$this->excel->getActiveSheet()->setCellValue('I3',"Saldo");
				
				$j=4;
				$no=0;
				
				foreach($data as $key=>$val){
					$city_name 			= $this->PPOBRefund_model->getCityName($val['city_id']);
					$kecamatan_name 	= $this->PPOBRefund_model->getKecamatanName($val['kecamatan_id']);
					$kelurahan_name 	= $this->PPOBRefund_model->getKelurahanName($val['kelurahan_id']);
					$dusun_name 		= $this->PPOBRefund_model->getDusunName($val['dusun_id']);

					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_address'].', '.$dusun_name.', '.$kelurahan_name.', '.$kecamatan_name.', '.$city_name);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['savings_name']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, tgltoview($val['savings_account_date']));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['savings_account_first_deposit_amount']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['savings_account_last_balance']);	
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Simpanan.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPPOBRefund-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addPPOBRefund-'.$unique['unique'],$sessions);
		}

		public function editPPOBRefund(){
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['PPOBRefund']		= $this->PPOBRefund_model->getPPOBRefund_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'PPOBRefund/FormEditPPOBRefund_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processEditPPOBRefund(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_account_id"	=> $this->input->post('savings_account_id',true),
				"savings_account_no"	=> $this->input->post('savings_account_no',true),
			);
			
			$this->form_validation->set_rules('savings_account_no', 'No Rekening', 'required');

			if($this->form_validation->run()==true){
				if($this->PPOBRefund_model->updatePPOBRefund($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Perubahan No. Rekening Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBRefund/editPPOBRefund/'.$newdata['savings_account_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Perubahan No. Rekening Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBRefund/editPPOBRefund/'.$newdata['savings_account_id']);
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('PPOBRefund/editPPOBRefund/'.$newdata['savings_account_id']);
			}
		}
		
	}
?>