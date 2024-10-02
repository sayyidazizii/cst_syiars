<?php
	Class PreferencePPOB extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PreferencePPOB_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$unique 										= $this->session->userdata('unique');

			$data['main_view']['acctaccount']				= create_double($this->PreferencePPOB_model->getAcctAccount(),'account_id', 'account_code');

			$data['main_view']['preferenceppob']			= $this->PreferencePPOB_model->getPreferencePPOB();
			
			
			$data['main_view']['content']					= 'PreferencePPOB/FormAddPreferencePPOB_view';
			$this->load->view('MainPage_view',$data);
		}
		
				
		public function processEditPreferencePPOB(){
			$auth 					= $this->session->userdata('auth');
			$unique 				= $this->session->userdata('unique');

			$datapreferenceppob 	= array (
				'id'							=> $this->input->post('id_preference_ppob', true),
				'ppob_mbayar_admin'				=> $this->input->post('ppob_mbayar_admin', true),
				'ppob_account_income_mbayar'	=> $this->input->post('ppob_account_income_mbayar', true),
				'ppob_account_down_payment'		=> $this->input->post('ppob_account_down_payment', true),
				'ppob_account_income'			=> $this->input->post('ppob_account_income', true),
				'ppob_account_cost'				=> $this->input->post('ppob_account_cost', true),
			);

			if($this->PreferencePPOB_model->updatePreferencePPOB($datapreferenceppob)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
							Edit Setting PPOB Sukses
						</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('PreferencePPOB');		
			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
					Edit Setting PPOB Gagal
				</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PreferencePPOB');
			}
		}
	}
?>