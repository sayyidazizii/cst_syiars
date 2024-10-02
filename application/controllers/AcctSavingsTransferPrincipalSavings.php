<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 600);
	Class AcctSavingsTransferPrincipalSavings extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsTransferPrincipalSavings_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 			= $this->session->userdata('auth');
			$principal 		= 10000;
			$dataanggota 	= $this->AcctSavingsTransferPrincipalSavings_model->getCoreMember();

			$no = 1;

			foreach ($dataanggota as $key => $val) {
				$acctsavingaccount = $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavingsAccount($val['member_id']);

				if(!empty($acctsavingaccount)){
					$data_anggota[] = array (
						'no'										=> $no,
						'member_id'									=> $val['member_id'],
						'member_name'								=> $val['member_name'],
						'member_address'							=> $val['member_address'],
						'process_date'								=> date('Y-m-d'),
						'savings_account_id'						=> $acctsavingaccount['savings_account_id'],
						'savings_account_no'						=> $acctsavingaccount['savings_account_no'],
						'savings_id'								=> $acctsavingaccount['savings_id'],
						'savings_name'								=> $acctsavingaccount['savings_name'],
						'savings_account_opening_balance' 			=> $acctsavingaccount['savings_account_last_balance'],
						'member_principal_savings_last_balance'		=> $principal,
						'savings_account_last_balance'				=> $acctsavingaccount['savings_account_last_balance'] - $principal,
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
						'operated_name'								=> 'SYSTEM',
					);

					$no++;
				}

				
			}

			$data['main_view']['datacoremember']	= $data_anggota;

			$data['main_view']['content']			= 'AcctSavingsTransferPrincipalSavings/ListProcessPrincipalSavings_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processAdd(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('unique');

			$principal 		= 10000;
			$dataanggota 	= $this->AcctSavingsTransferPrincipalSavings_model->getCoreMember();

			$no = 1;

			foreach ($dataanggota as $key => $val) {
				$acctsavingaccount = $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavingsAccount($val['member_id']);

				if(!empty($acctsavingaccount)){
					$data_anggota[] = array (
						'branch_id'									=> $auth['branch_id'],
						'member_id'									=> $val['member_id'],
						'process_date'								=> date('Y-m-d'),
						'savings_account_id'						=> $acctsavingaccount['savings_account_id'],
						'savings_id'								=> $acctsavingaccount['savings_id'],
						'savings_account_opening_balance' 			=> $acctsavingaccount['savings_account_last_balance'],
						'member_principal_savings_last_balance'		=> $principal,
						'principal_savings_amount'					=> $principal,
						'savings_account_last_balance'				=> $acctsavingaccount['savings_account_last_balance'] - $principal,
						'mutation_member_id'						=> 1,
						'mutation_savings_id'						=> 11,
						'created_id'								=> $auth['user_id'],
						'created_on'								=> date('Y-m-d H:i:s'),
						'operated_name'								=> 'SYSTEM',
					);

					$no++;
				}

				
			}

			if($this->AcctSavingsTransferPrincipalSavings_model->insertAcctSavingsTransferPrincipalSavings($data_anggota)){
				$acctsavings = $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavings();

				foreach ($dataanggota as $key => $val) {
					$acctsavingaccount = $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavingsAccount($val['member_id']);

					if(!empty($acctsavingaccount)){
						$data_anggota_savings[$acctsavingaccount['savings_id']][] = array (
							'savings_id'								=> $acctsavingaccount['savings_id'],
							'savings_account_opening_balance' 			=> $acctsavingaccount['savings_account_last_balance'],
							'principal_savings_amount'					=> $principal,
						);

						$total_principalsavings[$acctsavingaccount['savings_id']] = $total_principalsavings[$acctsavingaccount['savings_id']] + $principal;

						$no++;
					}

					
				}

				// foreach ($acctsavings as $k => $v) {
				// 	print_r($total_principalsavings[$v['savings_id']]);
				// 	print_r("<BR>");
				// 	print_r("<BR>");
				// }

				// exit;

				foreach ($acctsavings as $k => $v) {
					if(!empty($total_principalsavings[$v['savings_id']])){
						$transaction_module_code 	= "AD";
						$transaction_module_id 		= $this->AcctSavingsTransferPrincipalSavings_model->getTransactionModuleID($transaction_module_code);

						$journal_voucher_period 	= date("Ym");

						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'AUTO DEBET '.$v['savings_name'],
							'journal_voucher_description'	=> 'AUTO DEBET '.$v['savings_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						
						$this->AcctSavingsTransferPrincipalSavings_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id = $this->AcctSavingsTransferPrincipalSavings_model->getJournalVoucherID($data_journal['created_id']);

						$account_id 		= $this->AcctSavingsTransferPrincipalSavings_model->getAccountID($v['savings_id']);

						$account_id_default_status = $this->AcctSavingsTransferPrincipalSavings_model->getAccountIDDefaultStatus($account_id);


						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $total_principalsavings[$v['savings_id']],
							'journal_voucher_debit_amount'	=> $total_principalsavings[$v['savings_id']],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AcctSavingsTransferPrincipalSavings_model->insertAcctJournalVoucherItem($data_debet);

						$preferencecompany 			= $this->AcctSavingsTransferPrincipalSavings_model->getPreferenceCompany();

						$account_id_principal 		= $this->AcctSavingsTransferPrincipalSavings_model->getAccountID($preferencecompany['principal_savings_id']);

						$account_id_default_status 	= $this->AcctSavingsTransferPrincipalSavings_model->getAccountIDDefaultStatus($account_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id_principal,
							'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
							'journal_voucher_amount'		=> $total_principalsavings[$v['savings_id']],
							'journal_voucher_credit_amount'	=> $total_principalsavings[$v['savings_id']],
							'account_id_status'				=> 1,
						);

						$this->AcctSavingsTransferPrincipalSavings_model->insertAcctJournalVoucherItem($data_credit);
					}
					
				}
				

				// exit;
				redirect('AcctSavingsTransferPrincipalSavings');
			}

		}

		public function getData(){
			$auth 			= $this->session->userdata('auth');
			$data['main_view']['datacoremember']	= $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavingsTransferPrincipal();

			$data['main_view']['content']			= 'AcctSavingsTransferPrincipalSavings/ListDataPrincipalSavings_view';
			$this->load->view('MainPage_view',$data);
		}

		public function export(){
			$auth = $this->session->userdata('auth'); 	
			$datacoremember	= $this->AcctSavingsTransferPrincipalSavings_model->getAcctSavingsTransferPrincipal();

			
			if(count($datacoremember)!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									 ->setLastModifiedBy("SIS")
									 ->setTitle("Master Data Anggota")
									 ->setSubject("")
									 ->setDescription("Master Data Anggota")
									 ->setKeywords("Master, Data, Anggota")
									 ->setCategory("Master Data Anggota");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);	
				// $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);	
				// $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);	
				// $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
				// $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
				// $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);	
				// $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);	
				// $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:I1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Anggota");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Jenis Simpanan");
				$this->excel->getActiveSheet()->setCellValue('G3',"No. Rekening");
				$this->excel->getActiveSheet()->setCellValue('H3',"Opening Balance");
				$this->excel->getActiveSheet()->setCellValue('I3',"Last Balance");
				
				$j=4;
				$no=0;
				
				foreach($datacoremember as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						// $this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['savings_name']);
						$this->excel->getActiveSheet()->setCellValueExplicit('G'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['savings_account_opening_balance']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['savings_account_last_balance']);
						// $this->excel->getActiveSheet()->setCellValue('J'.$j, $val['member_phone']);
						// $this->excel->getActiveSheet()->setCellValue('K'.$j, $val['member_job']);
						// $this->excel->getActiveSheet()->setCellValue('L'.$j, $memberidentity[$val['member_identity']]);
						// $this->excel->getActiveSheet()->setCellValue('M'.$j, $val['member_identity_no']);
						// $this->excel->getActiveSheet()->setCellValue('N'.$j, number_format($val['member_principal_savings'], 2));
						// $this->excel->getActiveSheet()->setCellValue('O'.$j, number_format($val['member_special_savings'], 2));
						// $this->excel->getActiveSheet()->setCellValue('P'.$j, number_format($val['member_mandatory_savings'], 2));	
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Anggota.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}
	}
?>