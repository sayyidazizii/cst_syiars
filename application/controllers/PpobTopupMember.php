<?php
	Class PpobTopupMember extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PpobTopupMember_model');
			$this->load->model('CoreMember_model');
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
			$sesi	= $this->session->userdata('filter-ppobtopupmember');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				
			}
			$this->session->unset_userdata('ppobtopupmembertoken-'.$unique['unique']);
			$this->session->unset_userdata('addPpobTopupMember-'.$unique['unique']);

			$start_date = tgltodb($sesi['start_date']);
			$end_date	= tgltodb($sesi['end_date']);

			$data['main_view']['PpobTopupMember']	= $this->PpobTopupMember_model->getPpobTopupMember($start_date, $end_date);

			$data['main_view']['content']			= 'PpobTopupMember/ListPpobTopupMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
			);

			$this->session->set_userdata('filter-ppobtopupmember',$data);
			redirect('PpobTopupMember');
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPpobTopupMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addPpobTopupMember-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addPpobTopupMember-'.$unique['unique']);
			redirect('PpobTopupMember/addPpobTopupMember');
		}

		public function getListCoreMember(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$list = $this->CoreMember_model->get_datatables($data_state, $auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'PpobTopupMember/addPpobTopupMember/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMember_model->count_all($data_state, $auth['branch_id']),
	                        "recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}	
		
		public function addPpobTopupMember(){
			$member_id 	= $this->uri->segment(3);

			$date = date('Y-m-d');

			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('ppobtopupmembertoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('ppobtopupmembertoken-'.$unique['unique'], $token);
			}

			//grab database default
			$database 			= $this->db->database;

			//company_id madani dari database ciptasolutindo
			$ppob_company_id 	= $this->PpobTopupMember_model->getPpobCompanyID($database);

			//saldo ppob madani dari database ciptasolutindo
			$ppob_balance 		= $this->PpobTopupMember_model->getPPOBBalance($ppob_company_id, $member_id);
			

			$data['main_view']['coremember']		= $this->PpobTopupMember_model->getCoreMember_Detail($member_id);
			
			$data['main_view']['acctsavingsaccount']= create_double($this->PpobTopupMember_model->getAcctSavingsAccount($member_id), 'savings_account_id', 'savings_account_no');	

			$data['main_view']['ppob_balance']		= $ppob_balance;

			$data['main_view']['content']			= 'PpobTopupMember/FormAddPpobTopupMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getAcctSavingsAccount_Detail(){
			$savings_account_id = $this->input->post('savings_account_id');

			$data = $this->PpobTopupMember_model->getAcctSavingsAccount_Detail($savings_account_id);
				$result = array();
				$result = array(
					"savings_id"						=> $data['savings_id'],
					"savings_account_last_balance"		=> $data['savings_account_last_balance'],
				);
			echo json_encode($result);
		
		}

		public function processAddPpobTopupMember(){
			$unique 						= $this->session->userdata('unique');
			$auth							= $this->session->userdata('auth');
			

			$data_ppob = array(
				'member_id'					=> $this->input->post('member_id', true),
				'savings_account_id'		=> $this->input->post('savings_account_id', true),
				'branch_id'					=> $auth['branch_id'],
				'ppob_topup_member_date'	=> date('Y-m-d'),
				'ppob_topup_member_amount'	=> $this->input->post('ppob_topup_member_amount', true),
				'ppob_topup_member_token'	=> $this->input->post('ppob_topup_member_token', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);

			$savings_id = $this->input->post('savings_id', true);

			$this->form_validation->set_rules('member_id', 'Anggota', 'required');
			$this->form_validation->set_rules('savings_account_id', 'No. Rekening', 'required');
			$this->form_validation->set_rules('ppob_topup_member_amount', 'Jumlah Top Up', 'required');

			//Cek Token Topup PPOB
			$ppob_topup_member_token	= $this->PpobTopupMember_model->getPpobTopupMemberToken($data_ppob['ppob_topup_member_token']);

			
			$transaction_module_code 	= "TPPPOB";
			$transaction_module_id 		= $this->PpobTopupMember_model->getTransactionModuleID($transaction_module_code);

			if($this->form_validation->run()==true){
				if($ppob_topup_member_token->num_rows() == 0){
					//Jika Token PPOB 0

					if($this->PpobTopupMember_model->insertPpobTopupMember($data_ppob)){
						
						//start JURNAL
						$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_member_date']));

						$ppobtopupmember_last 			= $this->PpobTopupMember_model->getPpobTopupMember_Last($data_ppob['created_id']);

						$data_journal = array(
							'branch_id'							=> $data_ppob['branch_id'],
							'journal_voucher_period' 			=> $journal_voucher_period,
							'journal_voucher_date'				=> date('Y-m-d'),
							'journal_voucher_title'				=> 'TOP UP PPOB Anggota',
							'journal_voucher_description'		=> 'TOP UP PPOB Anggota',
							'journal_voucher_token'				=> $data_ppob['ppob_topup_member_token'],
							'transaction_module_id'				=> $transaction_module_id,
							'transaction_module_code'			=> $transaction_module_code,
							'transaction_journal_id' 			=> $ppobtopupmember_last['ppob_topup_member_id'],
							'transaction_journal_no' 			=> "",
							'created_id' 						=> $data_ppob['created_id'],
							'created_on' 						=> $data_ppob['created_on'],
						);

						$journal_voucher_token 				= $this->PpobTopupMember_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

						if($journal_voucher_token->num_rows() == 0){
							$this->PpobTopupMember_model->insertAcctJournalVoucher($data_journal);
						}

						$journal_voucher_id 				= $this->PpobTopupMember_model->getJournalVoucherID($data_journal['created_id']);

						//DEBET
						$account_id 						= $this->PpobTopupMember_model->getAccountID($savings_id);

						$account_id_default_status 			= $this->PpobTopupMember_model->getAccountIDDefaultStatus($account_id);

						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'Top Up PPOB Anggota',
							'journal_voucher_amount'		=> $data_ppob['ppob_topup_member_amount'],
							'journal_voucher_debit_amount'	=> $data_ppob['ppob_topup_member_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data_ppob['ppob_topup_member_token'].$account_id,
						);

						$journal_voucher_item_token 		= $this->PpobTopupMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows() == 0){
							$this->PpobTopupMember_model->insertAcctJournalVoucherItem($data_debet);
						}

						//KREDIT
						$preferenceppob 					= $this->PpobTopupMember_model->getPreferencePpob();

						$account_id_default_status 			= $this->PpobTopupMember_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_payable_member']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_payable_member'],
							'journal_voucher_description'	=> 'Top Up PPOB Anggota',
							'journal_voucher_amount'		=> $data_ppob['ppob_topup_member_amount'],
							'journal_voucher_credit_amount'	=> $data_ppob['ppob_topup_member_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data_ppob['ppob_topup_member_token'].$preferenceppob['ppob_account_payable_member'],
						);

						$journal_voucher_item_token 		= $this->PpobTopupMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

						if($journal_voucher_item_token->num_rows() == 0){
							$this->PpobTopupMember_model->insertAcctJournalVoucherItem($data_credit);
						}
						//end JURNAL

						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Top Up PPOB Anggota Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addPpobTopupMember-'.$sesi['unique']);
						$this->session->unset_userdata('ppobtopupmembertoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('PpobTopupMember/addPpobTopupMember');
					} else {
						$this->session->set_userdata('addPpobTopupMember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Top Up PPOB Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('PpobTopupMember/addPpobTopupMember');
					}
				} else {
					//start JURNAL
					$journal_voucher_period 	= date("Ym", strtotime($data_ppob['ppob_topup_member_date']));

					$ppobtopupmember_last 			= $this->PpobTopupMember_model->getPpobTopupMember_Last($data_ppob['created_id']);

					$data_journal = array(
						'branch_id'							=> $data_ppob['branch_id'],
						'journal_voucher_period' 			=> $journal_voucher_period,
						'journal_voucher_date'				=> date('Y-m-d'),
						'journal_voucher_title'				=> 'TOP UP PPOB Anggota',
						'journal_voucher_description'		=> 'TOP UP PPOB Anggota',
						'journal_voucher_token'				=> $data_ppob['ppob_topup_member_token'],
						'transaction_module_id'				=> $transaction_module_id,
						'transaction_module_code'			=> $transaction_module_code,
						'transaction_journal_id' 			=> $ppobtopupmember_last['ppob_topup_member_id'],
						'transaction_journal_no' 			=> "",
						'created_id' 						=> $data_ppob['created_id'],
						'created_on' 						=> $data_ppob['created_on'],
					);

					$journal_voucher_token 				= $this->PpobTopupMember_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows() == 0){
						$this->PpobTopupMember_model->insertAcctJournalVoucher($data_journal);
					}

					$journal_voucher_id 				= $this->PpobTopupMember_model->getJournalVoucherID($data_journal['created_id']);

					//DEBET
					$account_id 						= $this->PpobTopupMember_model->getAccountID($savings_id);

					$account_id_default_status 			= $this->PpobTopupMember_model->getAccountIDDefaultStatus($account_id);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $account_id,
						'journal_voucher_description'	=> 'Top Up PPOB Anggota',
						'journal_voucher_amount'		=> $data_ppob['ppob_topup_member_amount'],
						'journal_voucher_debit_amount'	=> $data_ppob['ppob_topup_member_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data_ppob['ppob_topup_member_token'].$account_id,
					);

					$journal_voucher_item_token 		= $this->PpobTopupMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PpobTopupMember_model->insertAcctJournalVoucherItem($data_debet);
					}

					//KREDIT
					$preferenceppob 					= $this->PpobTopupMember_model->getPreferencePpob();

					$account_id_default_status 			= $this->PpobTopupMember_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_payable_member']);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $preferenceppob['ppob_account_payable_member'],
						'journal_voucher_description'	=> 'Top Up PPOB Anggota',
						'journal_voucher_amount'		=> $data_ppob['ppob_topup_member_amount'],
						'journal_voucher_credit_amount'	=> $data_ppob['ppob_topup_member_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data_ppob['ppob_topup_member_token'].$preferenceppob['ppob_account_payable_member'],
					);

					$journal_voucher_item_token 		= $this->PpobTopupMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->PpobTopupMember_model->insertAcctJournalVoucherItem($data_credit);
					}
					//end JURNAL

					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Top Up PPOB Anggota Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addPpobTopupMember-'.$sesi['unique']);
					$this->session->unset_userdata('ppobtopupmembertoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('PpobTopupMember/addPpobTopupMember');
				}
				
			} else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}

		public function showdetail(){
			$savings_transfer_ppob_id = $this->uri->segment(3);

			$data['main_view']['PpobTopupMember']		= $this->PpobTopupMember_model->getPpobTopupMember_Detail($savings_transfer_ppob_id);
			$data['main_view']['PpobTopupMemberitem']	= $this->PpobTopupMember_model->getPpobTopupMemberItem_Detail($savings_transfer_ppob_id);

			$data['main_view']['content']								= 'PpobTopupMember/FormDetailPpobTopupMember_view';
			$this->load->view('MainPage_view',$data);
		}				

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addPpobTopupMember-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addPpobTopupMember-'.$unique['unique'],$sessions);
		}
	}
?>