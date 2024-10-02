<?php
	Class AcctCreditsDataMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsDataMutation_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){}
		
		public function DataCreditsMutation(){
			$sesi	= 	$this->session->userdata('filter-acctcreditsdatamutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_account_id'] 	= '';
			}

			// print_r($sesi);exit;

			$credits_account_id = $this->uri->segment(3);
			if($credits_account_id == ''){
				$credits_account_id = $sesi['credits_account_id'];
			}

			$data['main_view']['printstatus']					= $this->configuration->PrintStatus();
			$data['main_view']['acctcreditsaccount']			= $this->AcctCreditsDataMutation_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['acctcreditsaccountdetail']		= $this->AcctCreditsDataMutation_model->getAcctCreditPaymentDetail($credits_account_id, $sesi['start_date'], $sesi['end_date']);		

			$data['main_view']['content']						= 'AcctCreditsDataMutation/ListAcctCreditsDataMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date"					=> tgltodb($this->input->post('start_date',true)),
				"end_date"						=> tgltodb($this->input->post('end_date',true)),
				"credits_account_id"			=> $this->input->post('credits_account_id', true),
			);

			$this->session->set_userdata('filter-acctcreditsdatamutation',$data);
			redirect('AcctCreditsDataMutation/DataCreditsMutation');
		}

		public function getListAcctCreditsAccount(){
			$auth 		= $this->session->userdata('auth');
			$branch_id 	= '';
			$list 		= $this->AcctCreditAccount_model->get_datatables($branch_id);
	        $data 		= array();
	        $no = $_POST['start'];
	        foreach ($list as $creditsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $creditsaccount->credits_account_serial;
	            $row[] = $creditsaccount->member_name;
	            $row[] = $creditsaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctCreditsDataMutation/DataCreditsMutation/'.$creditsaccount->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all($branch_id),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered($branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}


		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-acctcreditsdatamutation');
			redirect('AcctCreditsDataMutation/DataCreditsMutation');
		}

		public function processAddAcctCreditsDataMutation(){
			$no = 1;

			foreach($_POST as $key=>$val){
				$cek 		= $this->input->post($no.'_cek',true);
				$cek_non 	= $this->input->post($no.'_cek_non',true);

				if($cek == 1){
					$data_item_detail[$no]=array(
						'credits_payment_id' 		=> $this->input->post($no.'_credits_payment_id',true),
						'credits_print_status'		=> 0,
					);
				} else if($cek_non == 1){
					$data_item_detail[$no]=array(
						'credits_payment_id' 		=> $this->input->post($no.'_credits_payment_id',true),
						'credits_print_status'		=> 1,
					);
				}
				
				$dataarray = $data_item_detail;
				$no++;
			}

			// print_r($dataarray);exit;

			foreach ($dataarray as $k => $v) {
				$dataupdate = array (
					'credits_payment_id'			=> $v['credits_payment_id'],
					'credits_print_status'			=> $v['credits_print_status'],
				);

				if($this->AcctCreditsDataMutation_model->updatePrintMutationStatus($dataupdate)){
					$this->session->set_userdata('addacctcreditscashmutation',$data);
					$msg = "<div class='alert alert-success alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Status Cetak Berhasil Dirubah
							</div> ";
					$this->session->set_userdata('message',$msg);
					continue;
				} else {
					$this->session->set_userdata('addacctcreditscashmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Status Cetak Gagal Dirubah
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctDailyAverageBalanceRECalculate');
					break;
				}
			}
			
			redirect('AcctCreditsDataMutation/DataCreditsMutation');
		}
		
	}
?>