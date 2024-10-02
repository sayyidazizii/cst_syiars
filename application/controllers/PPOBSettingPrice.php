<?php
	Class PPOBSettingPrice extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('PPOBSettingPrice_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['settingpricestatus']		= $this->configuration->SettingPriceStatus();
			$data['main_view']['ppobsettingprice']			= $this->PPOBSettingPrice_model->getPPOBSettingPrice();
			$data['main_view']['content']					= 'PPOBSettingPrice/ListPPOBSettingPrice_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function editPPOBSettingPrice(){
			$auth 				= $this->session->userdata('auth');
			$setting_price_id 	= $this->uri->segment(3);

			$data['main_view']['ppobsettingprice']		= $this->PPOBSettingPrice_model->getPPOBSettingPrice_Detail($setting_price_id);

			$data['main_view']['settingpricestatus']	= $this->configuration->SettingPriceStatus();
			
			$data['main_view']['content']				= 'PPOBSettingPrice/FormEditPPOBSettingPrice_view';

			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditPPOBSettingPrice(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'setting_price_id'				=> $this->input->post('setting_price_id', true),
				'setting_price_fee'				=> $this->input->post('setting_price_fee', true),
				'setting_price_commission'		=> $this->input->post('setting_price_commission', true),
				'updated_id'					=> $auth['user_id'],
				'updated_on'					=> date("Y-m-d H:i:s"),
			);
			
			$this->form_validation->set_rules('setting_price_fee', 'Fee PPOB', 'required');
			$this->form_validation->set_rules('setting_price_commission', 'Komisi PPOB', 'required');
			
			if($this->form_validation->run()==true){
				if($this->PPOBSettingPrice_model->updatePPOBSettingPrice($data)){
					
					/* $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine'); */
					$data_log = array (
						'setting_price_id'					=> $this->input->post('setting_price_id', true),
						'setting_price_code'				=> $this->input->post('setting_price_code', true),
						'setting_price_fee'					=> $this->input->post('setting_price_fee', true),
						'setting_price_commission'			=> $this->input->post('setting_price_commission', true),
						'setting_price_max'					=> $this->input->post('setting_price_max', true),
						'setting_price_fee_old'				=> $this->input->post('setting_price_fee_old', true),
						'setting_price_commission_old'		=> $this->input->post('setting_price_commission_old', true),
						'setting_price_max_old'				=> $this->input->post('setting_price_max_old', true),
						'setting_price_log_date'			=> date("Y-m-d"),
						'setting_price_log_on'				=> date("Y-m-d H:i:s"),
						'created_id'						=> $auth['user_id'],
						'created_on'						=> date("Y-m-d H:i:s"),
					);	

					$this->PPOBSettingPrice_model->insertPPOBSettingPriceLog($data_log);

					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Setting Price Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBSettingPrice/editPPOBSettingPrice/'.$data['setting_price_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Setting Price Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('PPOBSettingPrice/editPPOBSettingPrice/'.$data['setting_price_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('PPOBSettingPrice/editPPOBSettingPrice/'.$data['setting_price_id']);
			}				
		}
		
		public function deletePPOBSettingPrice(){
			if($this->PPOBSettingPrice_model->deletePPOBSettingPrice($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Perkiraan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBSettingPrice');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('PPOBSettingPrice');
			}
		}
	}
?>