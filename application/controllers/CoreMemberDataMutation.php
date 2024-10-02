<?php
	Class CoreMemberDataMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberDataMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
		}

		public function DataMutation(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-corememberdatamutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			$member_id = $this->uri->segment(3);
			if($member_id == ''){
				$member_id = $sesi['member_id'];
			}

			$data['main_view']['printstatus']					= $this->configuration->PrintStatus();
			$data['main_view']['coremember']					= $this->CoreMemberDataMutation_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsmemberdetail']		= $this->CoreMemberDataMutation_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);	

			$data['main_view']['content']			= 'CoreMemberDataMutation/ListCoreMemberDataMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterMutation(){
			$data = array (
				"start_date"	=> tgltodb($this->input->post('start_date',true)),
				"end_date"		=> tgltodb($this->input->post('end_date',true)),
				"member_id"		=> $this->input->post('member_id', true),
			);

			$this->session->set_userdata('filter-corememberdatamutation',$data);
			redirect('CoreMemberDataMutation/DataMutation');
		}

		public function getListCoreMember(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$list = $this->CoreMemberDataMutation_model->get_datatables($data_state);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'CoreMemberDataMutation/DataMutation/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMemberDataMutation_model->count_all($data_state),
	                        "recordsFiltered" => $this->CoreMemberDataMutation_model->count_filtered($data_state),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-corememberdatamutation');
			redirect('AcctCreditsDataMutation/DataMutation');
		}

		public function processUpdateCoreMemberStatus(){
			$no = 1;

			foreach($_POST as $key=>$val){
				$cek 		= $this->input->post($no.'_cek',true);
				$cek_non 	= $this->input->post($no.'_cek_non',true);

				if($cek == 1){
					$data_item_detail[$no]=array(
						'savings_member_detail_id' 	=> $this->input->post($no.'_savings_member_detail_id',true),
						'savings_print_status'		=> 0,
					);
				} else if($cek_non == 1){
					$data_item_detail[$no]=array(
						'savings_member_detail_id' 	=> $this->input->post($no.'_savings_member_detail_id',true),
						'savings_print_status'		=> 1,
					);
				}
				
				$dataarray = $data_item_detail;
				$no++;
			}

			// print_r($dataarray);exit;

			foreach ($dataarray as $k => $v) {
				$dataupdate = array (
					'savings_member_detail_id'		=> $v['savings_member_detail_id'],
					'savings_print_status'			=> $v['savings_print_status'],
				);

				if($this->CoreMemberDataMutation_model->updatePrintMutationStatus($dataupdate)){
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
			
			redirect('CoreMemberDataMutation/DataMutation');
		}	

	}
?>