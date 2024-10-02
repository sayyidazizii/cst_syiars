<?php
	Class AcctTopupPPOB extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('ppob/AcctCreditsPPOB_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$data['main_view']['content']			= 'ppob/AcctCreditsPPOB/ListAcctCreditsPPOB_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
			);

			$this->session->set_userdata('filter-acctcreditsppob',$data);
			redirect('ppob/topup-saldo');
		}

		public function getData(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctcreditsppob');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				
			}


			$list = $this->AcctCreditsPPOB_model->get_datatables($sesi['start_date'], $sesi['end_date']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $data) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $data->credits_ppob_no;
				$row[] = tgltoview($data->credits_ppob_date);
	            $row[] = $data->credits_ppob_amount;
				$row[] = '<a href="'.base_url().'AcctSavingsCashMutation/printNoteAcctSavingsCashMutation/'.$savingsaccount->credits_ppob_id.'" class="btn btn-info btn-xs" role="button"><span class="glyphicon glyphicon-print"></span> Kwitansi</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditsPPOB_model->count_all($sesi['start_date'], $sesi['end_date']),
	                        "recordsFiltered" => $this->AcctCreditsPPOB_model->count_filtered($sesi['start_date'], $sesi['end_date']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}

		public function create(){
			$data['main_view']['content']					= 'ppob/CreditsPPOB/FormAdd_view';
			$this->load->view('MainPage_view',$data);
		}

		
		public function store(){
			$auth = $this->session->userdata('auth');
			
			$preferencecompany 						= $this->AcctCreditsPPOB_model->getPreferenceCompany();

			$data = array(
				'credits_ppob_account_id'					=> $preferencecompany['account_savings_transfer_from_id'],
				'credits_ppob_from_account_id'				=> $preferencecompany['account_savings_transfer_from_id'],
				'credits_ppob_no'							=> $this->input->post('credits_ppob_no', true),
				'credits_ppob_date'							=> tgltodb($this->input->post('credits_ppob_date', true)),
				'credits_ppob_amount'						=> $this->input->post('credits_ppob_amount', true),
				'credits_ppob_amount_outstanding'			=> $this->input->post('credits_ppob_no', true),
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('credits_ppob_date', 'Tanggal Transaksi', 'required');
			$this->form_validation->set_rules('credits_ppob_amount', 'Jumlah Transaksi', 'required');
			
			if($this->form_validation->run()==true){
					if($this->AcctCreditsPPOB_model->insertData($data)){
						$auth = $this->session->userdata('auth');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Topup PPOB Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->set_userdata('message',$msg);
						redirect('ppob/topup-saldo');
					}else{
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Topup PPOB Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('ppob/topup-saldo');
					}
				
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('ppob/topup-saldo');
			}
		}

	}
?>