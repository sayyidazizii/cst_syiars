<?php
	Class AcctAccount extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctaccount']			= $this->AcctAccount_model->getDataAcctAccount();
			$data['main_view']['kelompokperkiraan']		= $this->configuration->KelompokPerkiraan();
			$data['main_view']['accountstatus']			= $this->configuration->AccountStatus();	
			$data['main_view']['content']				= 'AcctAccount/ListAcctAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctAccount(){
			$data['main_view']['kelompokperkiraan']		= $this->configuration->KelompokPerkiraan();
			$data['main_view']['accountstatus']			= $this->configuration->AccountStatus();
			$data['main_view']['content']				= 'AcctAccount/FormAddAcctAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctAccount(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'account_code'				=> $this->input->post('account_code', true),
				'account_name'				=> $this->input->post('account_name', true),
				'account_type_id'			=> $this->input->post('account_type_id', true),
				'account_group'				=> $this->input->post('account_group', true),
				'account_status'			=> $this->input->post('account_status', true),
				'account_default_status'	=> $this->input->post('account_status', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('account_code', 'Nomor Perkiraan', 'required');
			$this->form_validation->set_rules('account_name', 'Nama Perkiraan', 'required');
			$this->form_validation->set_rules('account_group', 'Golongan Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctAccount_model->insertAcctAccount($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Perkiraan Sukses
							</div> ";
					$this->session->unset_userdata('addacctsavings');
					$this->session->set_userdata('message',$msg);
					redirect('AcctAccount/addAcctAccount');
				}else{
					$this->session->set_userdata('addacctsavings',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Perkiraan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctAccount/addAcctAccount');
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctAccount/editAcctAccount/'.$data['account_id']);
			}
		}
		
		public function editAcctAccount(){
			$data['main_view']['acctaccount']		= $this->AcctAccount_model->getAcctAccount_Detail($this->uri->segment(3));
			$data['main_view']['kelompokperkiraan']	= $this->configuration->KelompokPerkiraan();
			$data['main_view']['accountstatus']		= $this->configuration->AccountStatus();
			$data['main_view']['content']			= 'AcctAccount/FormEditAcctAccount_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctAccount(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'account_id'				=> $this->input->post('account_id', true),
				'account_code'				=> $this->input->post('account_code', true),
				'account_name'				=> $this->input->post('account_name', true),
				'account_type_id'			=> $this->input->post('account_type_id', true),
				'account_group'				=> $this->input->post('account_group', true),
				'account_status'			=> $this->input->post('account_status', true),
				'account_default_status'	=> $this->input->post('account_status', true),
			);
			
			$this->form_validation->set_rules('account_code', 'Nomor Perkiraan', 'required');
			$this->form_validation->set_rules('account_name', 'Nama Perkiraan', 'required');
			$this->form_validation->set_rules('account_group', 'Golongan Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctAccount_model->updateAcctAccount($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Perkiraan Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctAccount/editAcctAccount/'.$data['account_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Perkiraan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctAccount/editAcctAccount/'.$data['account_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctAccount/editAcctAccount/'.$data['account_id']);
			}				
		}
		
		public function deleteAcctAccount(){
			if($this->AcctAccount_model->deleteAcctAccount($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Perkiraan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctAccount');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctAccount');
			}
		}
	}
?>