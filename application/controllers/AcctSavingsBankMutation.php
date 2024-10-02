<?php
	Class AcctSavingsBankMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsBankMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsbankmutation');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				$sesi['savings_account_id']		= '';
			}

			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctSavingsBankMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');
			$data['main_view']['acctsavingsbankmutation']		= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation($sesi['start_date'], $sesi['end_date'], $sesi['savings_account_id']);
			$data['main_view']['content']			= 'AcctSavingsBankMutation/ListAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id"		=> $this->input->post('savings_account_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsbankmutation',$data);
			redirect('AcctSavingsBankMutation');
		}
		
		public function addAcctSavingsBankMutation(){
			$data['main_view']['acctsavingsaccount']		= create_double($this->AcctSavingsBankMutation_model->getAcctSavingsAccount(),'savings_account_id', 'savings_account_no');	
			$data['main_view']['acctbankaccount']			= create_double($this->AcctSavingsBankMutation_model->getAcctBankAccount(),'bank_account_id', 'bank_account_code');	
			$data['main_view']['content']					= 'AcctSavingsBankMutation/FormAddAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getAcctSavingsAccount_Detail(){
			$savings_account_id 	= $this->input->post('savings_account_id');

			// $savings_account_id = 4;
			
			$data 			= $this->AcctSavingsBankMutation_model->getAcctSavingsAccount_Detail($savings_account_id);

			$result = array();
			$result = array(
				"savings_name" 					=> $data['savings_name'], 
				"member_name"					=> $data['member_name'],
				"member_address"				=> $data['member_address'],
				"city_name"						=> $data['city_name'],
				"kecamatan_name"				=> $data['kecamatan_name'],
				"identity_name"					=> $data['identity_name'],
				"member_identity_no"			=> $data['member_identity_no'],
				"savings_account_last_balance"	=> $data['savings_account_last_balance'],
			);
			echo json_encode($result);		
		}

		
		public function processAddAcctSavingsBankMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'savings_account_id'					=> $this->input->post('savings_account_id', true),
				'bank_account_id'						=> $this->input->post('bank_account_id', true),
				'savings_bank_mutation_date'			=> date('Y-m-d'),
				'savings_bank_mutation_opening_balance'	=> $this->input->post('savings_bank_mutation_opening_balance', true),
				'savings_bank_mutation_last_balance'	=> $this->input->post('savings_bank_mutation_last_balance', true),
				'savings_bank_mutation_amount'			=> $this->input->post('savings_bank_mutation_amount', true),
				'created_id'							=> $auth['user_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('savings_account_id', 'No. Mutasi', 'required');
			$this->form_validation->set_rules('bank_account_id', 'Transfer Bank', 'required');
			$this->form_validation->set_rules('savings_bank_mutation_amount', 'Jumlah Transaksi', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSavingsBankMutation_model->insertAcctSavingsBankMutation($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingsbankmutation-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsBankMutation/addAcctSavingsBankMutation');
				}else{
					$this->session->set_userdata('addacctsavingsbankmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsBankMutation/addAcctSavingsBankMutation');
				}
			}else{
				$this->session->set_userdata('addacctsavingsbankmutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsBankMutation/addAcctSavingsBankMutation');
			}
		}
		
		public function voidAcctSavingsBankMutation(){
			$data['main_view']['acctsavingsbankmutation']	= $this->AcctSavingsBankMutation_model->getAcctSavingsBankMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'AcctSavingsBankMutation/FormVoidAcctSavingsBankMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsBankMutation(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_bank_mutation_id"	=> $this->input->post('savings_bank_mutation_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsBankMutation_model->voidAcctSavingsBankMutation($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsBankMutation');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsBankMutation');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsBankMutation');
			}
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsbankmutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsbankmutation-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsbankmutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingsbankmutation-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingsbankmutation-'.$unique['unique']);
			redirect('AcctSavingsBankMutation/addAcctSavingsBankMutation');
		}
		
		
	}
?>