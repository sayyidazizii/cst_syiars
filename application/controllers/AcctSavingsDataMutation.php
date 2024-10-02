<?php
	Class AcctSavingsDataMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDataMutation_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){}
		
		public function DataSavingsMutation(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsdatamutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			// print_r($sesi);exit;

			$savings_account_id = $this->uri->segment(3);
			if($savings_account_id == ''){
				$savings_account_id = $sesi['savings_account_id'];
			}

			$data['main_view']['printstatus']					= $this->configuration->PrintStatus();
			$data['main_view']['acctsavingsaccount']			= $this->AcctSavingsDataMutation_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['acctsavingsaccountdetail']		= $this->AcctSavingsDataMutation_model->getAcctSavingsAccountDetail($savings_account_id, $sesi['start_date'], $sesi['end_date']);		

			$data['main_view']['content']				= 'AcctSavingsDataMutation/ListAcctSavingsDataMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date"					=> tgltodb($this->input->post('start_date',true)),
				"end_date"						=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id"			=> $this->input->post('savings_account_id', true),
			);

			$this->session->set_userdata('filter-acctsavingsdatamutation',$data);
			redirect('AcctSavingsDataMutation/DataSavingsMutation');
		}

		public function getListAcctSavingsAccount(){
			$auth 		= $this->session->userdata('auth');
			$branch_id 	= '';
			$list 		= $this->AcctSavingsAccount_model->get_datatables($branch_id);
	        $data 		= array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctSavingsDataMutation/DataSavingsMutation/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($branch_id),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}


		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-acctsavingsdatamutation');
			redirect('AcctSavingsDataMutation/DataSavingsMutation');
		}

		public function processAddAcctSavingsDataMutation(){
			$no = 1;

			foreach($_POST as $key=>$val){
				$cek 		= $this->input->post($no.'_cek',true);
				$cek_non 	= $this->input->post($no.'_cek_non',true);

				if($cek == 1){
					$data_item_detail[$no]=array(
						'savings_account_detail_id' => $this->input->post($no.'_savings_account_detail_id',true),
						'savings_print_status'		=> 0,
					);
				} else if($cek_non == 1){
					$data_item_detail[$no]=array(
						'savings_account_detail_id' => $this->input->post($no.'_savings_account_detail_id',true),
						'savings_print_status'		=> 1,
					);
				}
				
				$dataarray = $data_item_detail;
				$no++;
			}

			

			foreach ($dataarray as $k => $v) {
				$dataupdate = array (
					'savings_account_detail_id'			=> $v['savings_account_detail_id'],
					'savings_print_status'				=> $v['savings_print_status'],
				);

				if($this->AcctSavingsDataMutation_model->updatePrintMutationStatus($dataupdate)){
					$this->session->set_userdata('addacctsavingscashmutation',$data);
					$msg = "<div class='alert alert-success alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Status Cetak Berhasil Dirubah
							</div> ";
					$this->session->set_userdata('message',$msg);
					continue;
				} else {
					$this->session->set_userdata('addacctsavingscashmutation',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Status Cetak Gagal Dirubah
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctDailyAverageBalanceRECalculate');
					break;
				}
			}
			
			redirect('AcctSavingsDataMutation/DataSavingsMutation');
		}
		
	}
?>