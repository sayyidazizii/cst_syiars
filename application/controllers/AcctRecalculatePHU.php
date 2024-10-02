<?php defined('BASEPATH') OR exit('No direct script access allowed');
	/*ini_set('memory_limit', '512M');*/

	Class AcctRecalculatePHU extends CI_Controller{
		public function __construct(){
			parent::__construct();


			$this->load->model('MainPage_model');
			$this->load->model('Connection_model');
			$this->load->model('AcctRecalculatePHU_model');
			$this->load->library('configuration');
			$this->load->helper('sistem');
			$this->load->database('default');
		}
		
		public function index(){
			$data['main_view']['bagihasilanggota']		= $this->AcctRecalculatePHU_model->getBagiHasilAnggota();
			$data['main_view']['content']				= 'AcctRecalculatePHU/ListAcctRecalculatePHU_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processAcctRecalculatePHU(){
			$bagihasilanggota		= $this->AcctRecalculatePHU_model->getBagiHasilAnggota();

			foreach ($bagihasilanggota as $key => $val){
				$data_mutation = array(
					'branch_id'								=> $val['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $val['bagi_hasil'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $val['member_name'],
					'created_id'							=> $val['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);
		
		
				if($this->AcctRecalculatePHU_model->insertAcctSavingsTransferMutation($data_mutation)){
					$savings_transfer_mutation_id 		= $this->AcctRecalculatePHU_model->getSavingsTransferMutationID($data_mutation['created_id']);

					$transaction_module_code 	        = "BSHU";
					$transaction_module_id 		        = 14;
					$journal_voucher_period 			= date("Ym");

					$data_journal = array(
						'branch_id'						=> $val['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> date('Y-m-d'),
						'journal_voucher_title'			=> 'PEMBAGIAN SHU 2021 '.$val['member_no'],
						'journal_voucher_description'	=> 'PEMBAGIAN SHU 2021 '.$val['member_no'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $savings_transfer_mutation_id,
						'transaction_journal_no' 		=> 'PHU_ANGGOTA_2021_'.$val['member_no'],
						'created_id' 					=> $val['member_id'],
						'created_on' 					=> date("Y-m-d H:i:s"),
					);

					if ($this->AcctRecalculatePHU_model->insertAcctJournalVoucher($data_journal)){
						$journal_voucher_id 				= $this->AcctRecalculatePHU_model->getJournalVoucherID($data_journal['created_id']);
						$savings_account_opening_balance	= $this->AcctRecalculatePHU_model->getSavingsAccountOpeningBalance($val['savings_account_id_from']);

						if ($savings_account_opening_balance == null){
							$savings_account_opening_balance = 0;
						}

						$datafrom = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $val['savings_account_id_from'],
							'savings_id'								=> $val['savings_id_from'],
							'member_id'									=> $val['member_id'],
							'branch_id'									=> $val['branch_id'],
							'mutation_id'								=> 6,
							'savings_account_opening_balance'			=> $savings_account_opening_balance,
							'savings_transfer_mutation_from_amount'		=> $val['bagi_hasil'],
							'savings_account_last_balance'				=> $savings_account_opening_balance - $val['bagi_hasil'],
						);

						if($this->AcctRecalculatePHU_model->insertAcctSavingsTransferMutationFrom($datafrom)){   
			
							$data_debit = array(
								'journal_voucher_id'					=> $journal_voucher_id,
								'account_id'							=> $val['account_id_from'],
								'journal_voucher_description'			=> 'PEMBAGIAN SHU 2021 '.$val['member_no'],
								'journal_voucher_amount'				=> $val['bagi_hasil'],
								'journal_voucher_debit_amount'			=> $val['bagi_hasil'],
								'account_id_default_status'				=> $val['account_default_status_from'],
								'account_id_status'						=> 0,
							);
	
							$this->AcctRecalculatePHU_model->insertAcctJournalVoucherItem($data_debit);
						}

						$savings_account_opening_balance	= $this->AcctRecalculatePHU_model->getSavingsAccountOpeningBalance($val['savings_account_id']);

						if ($savings_account_opening_balance == null){
							$savings_account_opening_balance = 0;
						}
						
						$datato = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $val['savings_account_id'],
							'savings_id'								=> $val['savings_id'],
							'member_id'									=> $val['member_id'],
							'branch_id'									=> $val['branch_id'],
							'mutation_id'								=> 6,
							'savings_account_opening_balance'			=> $savings_account_opening_balance,
							'savings_transfer_mutation_to_amount'		=> $val['bagi_hasil'],
							'savings_account_last_balance'				=> $savings_account_opening_balance + $val['bagi_hasil'],
						);

						if($this->AcctRecalculatePHU_model->insertAcctSavingsTransferMutationTo($datato)){   
							$data_credit = array(
								'journal_voucher_id'					=> $journal_voucher_id,
								'account_id'							=> $val['account_id'],
								'journal_voucher_description'			=> 'PEMBAGIAN SHU 2021 '.$val['member_no'],
								'journal_voucher_amount'				=> $val['bagi_hasil'],
								'journal_voucher_credit_amount'			=> $val['bagi_hasil'],
								'account_id_default_status'				=> $val['account_default_status'],
								'account_id_status'						=> 1,
							);

							$this->AcctRecalculatePHU_model->insertAcctJournalVoucherItem($data_credit);
						}
					}
				}
				
			}

			redirect('AcctRecalculatePHU');
		}
	}
?>