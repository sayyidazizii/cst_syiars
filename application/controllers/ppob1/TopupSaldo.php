<?php
	Class TopupSaldo extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AndroidPPOB_model');
			$this->load->model('TopupPPOB_model');
			$this->load->model('CoreMember_model');
			$this->load->model('AcctSavingsTransferPpob_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->helper('api_helper');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('Fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		
		public function getPPOBBalance(){
			$base_url 	= base_url();

			$response = array(
				'error'				=> FALSE,
				'error_msg'			=> "",
				'error_msg_title'	=> "",
				'ppobbalance'		=> "",
			);

			$data = array(
				'user_id'		=> $this->input->post('user_id',true),
			);
			
			// $data = array(
				// 'user_id'		=> '32887',
			// );

			$ppob_agen_id		= $data['user_id'];

			if($response["error"] == FALSE){

				$ppob_balance 		= $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

				if(empty($ppob_balance)){
					$ppob_balance 	= 0;
				}

				$ppobbalance[0]['ppob_balance']		= $ppob_balance;
						
				$response['error'] 					= FALSE;
				$response['error_msg_title'] 		= "Success";
				$response['error_msg'] 				= "Data Exist";
				$response['ppobbalance'] 			= $ppobbalance;
			}

			echo json_encode($response);
		}
		
		public function storePPOBBalance(){
			$base_url 	= base_url();

			$data = array(
				'branch_id'								=> $this->input->post('branch_from_id', true),
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $this->input->post('username', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date('Y-m-d H:i:s'),
			);
			
			// $data = array(
				// 'branch_id'								=> 2,
				// 'savings_transfer_mutation_date'		=> date('Y-m-d'),
				// 'savings_transfer_mutation_amount'		=> 500000,
				// 'savings_transfer_mutation_status'		=> 3,
				// 'operated_name'							=> 'SAIFUDIN, AMD',
				// 'created_id'							=> 31048,
				// 'created_on'							=> date('Y-m-d H:i:s'),
			// );
			
			// $savings_account_from_id 	= $this->input->post('savings_account_from_id', true);
			
			$savings_account_from_id 	= 31048;
			
			$preferencecompany 			= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();
			
			$response = array(
				'error'										=> FALSE,
				'error_insertacctsavingsppob'				=> FALSE,
				'error_msg_title_insertacctsavingsppob'		=> "",
				'error_msg_insertacctsavingsppob'			=> "",
			);
			
			if($response["error_insertacctsavingsppob"] == FALSE){
				if(!empty($data)){
					$transaction_module_code 		= "TPPPOB";
	
					$transaction_module_id 			= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);

					$savings_transfer_mutation_id 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);

					$datafrom = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $this->input->post('savings_account_from_id', true),
						'savings_id'								=> $this->input->post('savings_from_id', true),
						'member_id'									=> $this->input->post('member_from_id', true),
						'branch_id'									=> $this->input->post('branch_from_id', true),
						'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
						'savings_account_opening_balance'			=> $this->input->post('savings_account_from_opening_balance', true),
						'savings_transfer_mutation_from_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
						'savings_account_last_balance'				=> $this->input->post('savings_account_from_opening_balance', true) - $this->input->post('savings_transfer_mutation_amount', true),
					);
					
					$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datafrom['member_id']);
				
					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
						$savingsaccountto 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsAccount_Detail($preferencecompany['savings_account_ppob_id']);
	
						$datato = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $preferencecompany['savings_account_ppob_id'],
							'savings_id'								=> $savingsaccountto['savings_id'],
							'member_id'									=> $savingsaccountto['member_id'],
							'branch_id'									=> $savingsaccountto['branch_id'],
							'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
							'savings_account_opening_balance'			=> $savingsaccountto['savings_account_last_balance'],
							'savings_transfer_mutation_to_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
							'savings_account_last_balance'				=> $savingsaccountto['savings_account_last_balance'] + $this->input->post('savings_transfer_mutation_amount', true),
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato);
					}
					
					$ppob_agen_id		= $this->input->post('member_from_id', true);
					// $ppob_agen_id		= 32891;
	
					$ppob_agen_name 	= $this->AcctSavingsTransferMutation_model->getMemberName($ppob_agen_id);

					$data_ppob = array (
						'ppob_company_id'	=> 1,
						'ppob_agen_id'		=> $ppob_agen_id,
						'ppob_agen_name'	=> $ppob_agen_name,
						'ppob_topup_amount'	=> $data['savings_transfer_mutation_amount'],
						'ppob_topup_status'	=> 0,
						'ppob_topup_date'	=> date('Y-m-d'),
						'created_id'		=> $ppob_agen_id,
						'created_on'		=> date('Y-m-d H:i:s')
					);

					if($this->TopupPPOB_model->insertPPOBTopUP($data_ppob)){
						
						$ppob_balance 		= $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

						if(empty($ppob_balance)){
							$ppob_balance 	= 0;
						}
						
						$dataBalance = array(
							'ppob_company_id' => 1,
							'ppob_agen_id' => $ppob_agen_id,
							'ppob_agen_name' => $ppob_agen_name,
							'ppob_balance_amount' => $ppob_balance+$data['savings_transfer_mutation_amount'],
						);
						
						if(empty($ppob_balance)){
							$this->TopupPPOB_model->insertPPOBBalance($dataBalance);
						}else{
							$this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id,$dataBalance);
						}
						$response['error_insertacctsavingsppob'] 		= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
					}else{
						$response['error_insertacctsavingsppob'] 		= TRUE;
						$response['error_msg_title'] 					= "Failed";
						$response['error_msg'] 							= "Gagal Topup";
					}

					
				}
			}
			
			echo json_encode($response);
		}
		
	}
?>