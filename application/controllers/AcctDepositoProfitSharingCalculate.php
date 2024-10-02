<?php
	ini_set('memory_limit', '256M');

	Class AcctDepositoProfitSharingCalculate extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoProfitSharingCalculate_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['content']			= 'AcctDepositoProfitSharingCalculate/ListAcctDepositoProfitSharingCalculate_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDepositoProfitSharingCalculate(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'last_date'								=> tgltodb($this->input->post('last_date', true)),
			);

			$this->form_validation->set_rules('last_date', 'Tanggal', 'required');

			if($this->form_validation->run()==true){
				$deposito_profit_sharing_period 		= date('mY', strtotime($data['last_date']));
				$preferencecompany 						= $this->AcctDepositoProfitSharingCalculate_model->getPreferenceCompany();

				$acctdeposito 							= $this->AcctDepositoProfitSharingCalculate_model->getAcctDeposito();

				$acctdepositoaccount 					= $this->AcctDepositoProfitSharingCalculate_model->getAcctDepositoAccount($auth['branch_id']);
				$no = 1;

				// print_r(count($acctdepositoaccount));exit;

				foreach ($acctdepositoaccount as $k => $v) {
					$deposito_index_amount 				= $this->AcctDepositoProfitSharingCalculate_model->getDepositoIndexAmount($v['deposito_id'], $deposito_profit_sharing_period);

					$deposito_profit_sharing_amount 	= ($v['deposito_account_amount'] * $deposito_index_amount) * 3;

					// print_r($deposito_profit_sharing_amount);
					// exit;

					if($v['savings_account_id'] == 0){
						$datasavingsaccount 		= $this->AcctDepositoProfitSharingCalculate_model->getSavingsAccount($v['member_id']);
					} else {
						$datasavingsaccount 		= $this->AcctDepositoProfitSharingCalculate_model->getSavingsAccountDetail($v['savings_account_id']);
					}

					if(empty($datasavingsaccount)){
						$datasavingsaccount['savings_id']	= 0;
						$datasavingsaccount['savings_account_id']	= 0;
						$datasavingsaccount['savings_account_last_balance']	= 0;
					}

					$data_detail[$v['deposito_id']][] = array (
						'deposito_account_id'							=> $v['deposito_account_id'],
						'branch_id'										=> $v['branch_id'],
						'deposito_id'									=> $v['deposito_id'],
						'member_id'										=> $v['member_id'],
						'member_name'									=> $v['member_name'],
						'deposito_profit_sharing_date'					=> $data['last_date'],
						'deposito_index_amount'							=> $deposito_index_amount,
						'deposito_daily_average_balance'				=> $v['deposito_account_amount'],
						'deposito_profit_sharing_amount'				=> $deposito_profit_sharing_amount,
						'deposito_profit_sharing_period'				=> $deposito_profit_sharing_period,
						'deposito_profit_sharing_token'					=> $deposito_profit_sharing_period.$v['deposito_account_id'],
						'savings_account_id'							=> $datasavingsaccount['savings_account_id'],
						'savings_id'									=> $datasavingsaccount['savings_id'],
						'savings_account_opening_balance'				=> $datasavingsaccount['savings_account_last_balance'],
						'savings_transfer_mutation_amount'				=> $deposito_profit_sharing_amount,
						'savings_account_last_balance'					=> $datasavingsaccount['savings_account_last_balance']+$deposito_profit_sharing_amount,
					);

					// print_r("<BR>");
					// print_r($no);
					// print_r("<BR>");
					// print_r($data_detail);
					// print_r("<BR>");
					// print_r("<BR>");
					$no++;

					$datadetail = $data_detail;
				}

				// print_r($datadetail);
				// 	print_r("<BR>");
				// 	print_r("<BR>");

				// exit;


				foreach ($acctdeposito as $key => $val) {
					foreach ($datadetail[$val['deposito_id']] as $key => $val2) {
						$dataacctdepositoprofitsharing  = array (
							'deposito_account_id'						=> $val2['deposito_account_id'],
							'branch_id'									=> $val2['branch_id'],
							'deposito_id'								=> $val2['deposito_id'],
							'member_id'									=> $val2['member_id'],
							'deposito_profit_sharing_date'				=> $data['last_date'],
							'deposito_index_amount'						=> $val2['deposito_index_amount'],
							'deposito_daily_average_balance'			=> $val2['deposito_daily_average_balance'],
							'deposito_profit_sharing_amount'			=> $val2['deposito_profit_sharing_amount'],
							'deposito_profit_sharing_period'			=> $val2['deposito_profit_sharing_period'],
							'deposito_profit_sharing_token'				=> $val2['deposito_profit_sharing_period'].$val2['deposito_account_id'],
							'savings_account_id'						=> $val2['savings_account_id'],
							'operated_name'								=> 'SYSTEM',
							'created_id'								=> $auth['user_id'],
							'created_on'								=> date('Y-m-d H:i:s'),
						);

						$data_transfermutation 			= array (
							'branch_id'									=> $val2['branch_id'],
							'savings_transfer_mutation_date'			=> $data['last_date'],
							'savings_transfer_mutation_amount'			=> $val2['deposito_profit_sharing_amount'],
							'operated_name'								=> 'SYSTEM',
							'created_id'								=> $auth['user_id'],
							'created_on'								=> date('Y-m-d H:i:s'),
						);

						$deposito_account_id 							= $this->AcctDepositoProfitSharingCalculate_model->getDepositoAccountID($dataacctdepositoprofitsharing);

						if($deposito_account_id->num_rows() == 0){
							$this->AcctDepositoProfitSharingCalculate_model->insertAcctDepositoProfitSharingCalculate($dataacctdepositoprofitsharing);
						}
					}
				}

				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Perhitungan Basil Simpanan Berjangka Sukses
						 ";

				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingCalculate');
			} else {
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '');
				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingCalculate');
			}			
		}

		public function transfer(){
			$data['main_view']['content']			= 'AcctDepositoProfitSharingCalculate/ListAcctDepositoProfitSharingTransfer_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDepositoProfitSharingTransfer(){
			$auth = $this->session->userdata('auth');

			$acctdepositoprofitsharing = $this->AcctDepositoProfitSharingCalculate_model->getAcctDepositoProfitSharingBackup();

			// print_r(count($acctdepositoprofitsharing));exit;

					foreach ($acctdepositoprofitsharing as $key => $val2) {
						$data_transfermutation 			= array (
							'branch_id'									=> $val2['branch_id'],
							'savings_transfer_mutation_date'			=> '2019-05-31',
							'savings_transfer_mutation_amount'			=> $val2['deposito_profit_sharing_amount'],
							'operated_name'								=> 'SYSTEM',
							'created_id'								=> $auth['user_id'],
							'created_on'								=> date('Y-m-d H:i:s'),
						);


						if($this->AcctDepositoProfitSharingCalculate_model->insertAcctSavingsTransferMutation($data_transfermutation)){

							$savings_transfer_mutation_id 	= $this->AcctDepositoProfitSharingCalculate_model->getSavingsTranferMutationID($data_transfermutation['created_id']);

							$data_transfermutationto 		= array (
								'savings_transfer_mutation_id'			=> $savings_transfer_mutation_id,
								'savings_account_id'					=> $val2['savings_account_id'],
								'savings_id'							=> $val2['savings_id'],
								'branch_id'								=> $val2['branch_id'],
								'member_id'								=> $val2['member_id'],
								'mutation_id'							=> $preferencecompany['deposito_profit_sharing_id'],
								'savings_account_opening_balance'		=> $val2['savings_account_opening_balance'],
								'savings_transfer_mutation_to_amount'	=> $val2['deposito_profit_sharing_amount'],
								'savings_account_last_balance'			=> $val2['savings_account_last_balance'],	
							);

							$this->AcctDepositoProfitSharingCalculate_model->insertAcctSavingsTransferMutationTo($data_transfermutationto);
								
						}
					}

				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Transfer Sukses
						 ";

				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingCalculate');		
		}

		public function jurnal(){
			$data['main_view']['content']			= 'AcctDepositoProfitSharingCalculate/ListAcctDepositoProfitSharingJurnal_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctDepositoProfitSharingJurnal(){
			$auth = $this->session->userdata('auth');

			$acctdepositoprofitsharing 		 	= $this->AcctDepositoProfitSharingCalculate_model->getAcctDepositoProfitSharing_Detail();

			foreach ($acctdepositoprofitsharing as $key => $val) {
				$transaction_module_code 			= "BSDEP";

				$transaction_module_id 				= $this->AcctDepositoProfitSharingCalculate_model->getTransactionModuleID($transaction_module_code);

				
					
				$journal_voucher_period 			= date("Ym", strtotime($val['deposito_profit_sharing_date']));
				
				$data_journal 						= array(
					'branch_id'						=> $val['branch_id'],
					'journal_voucher_period' 		=> $journal_voucher_period,
					'journal_voucher_date'			=> '2019-05-31',
					'journal_voucher_title'			=> 'BAGI HASIL SIMPANAN BERJANGKA '.$val['member_name'],
					'journal_voucher_description'	=> 'BAGI HASIL SIMPANAN BERJANGKA '.$val['member_name'],
					'transaction_module_id'			=> $transaction_module_id,
					'transaction_module_code'		=> $transaction_module_code,
					'transaction_journal_id' 		=> $val['deposito_profit_sharing_id'],
					'transaction_journal_no' 		=> $val['deposito_profit_sharing_period'],
					'created_id' 					=> $auth['user_id'],
					'created_on' 					=> date('Y-m-d H:i:s'),
				);
				
				$this->AcctDepositoProfitSharingCalculate_model->insertAcctJournalVoucher($data_journal);

				$journal_voucher_id 				= $this->AcctDepositoProfitSharingCalculate_model->getJournalVoucherID($data_journal['created_id']);

				$account_basil_id 					= $this->AcctDepositoProfitSharingCalculate_model->getAccountBasilID($val['deposito_id']);

				$account_id_default_status 			= $this->AcctDepositoProfitSharingCalculate_model->getAccountIDDefaultStatus($account_basil_id);

				$data_debet = array (
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_basil_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
					'journal_voucher_amount'		=> $val['deposito_profit_sharing_amount'],
					'journal_voucher_debit_amount'	=> $val['deposito_profit_sharing_amount'],
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 0,
				);

				$this->AcctDepositoProfitSharingCalculate_model->insertAcctJournalVoucherItem($data_debet);

				$account_id 						= $this->AcctDepositoProfitSharingCalculate_model->getAccountID($val['savings_id']);

				$account_id_default_status 			= $this->AcctDepositoProfitSharingCalculate_model->getAccountIDDefaultStatus($account_id);

				$data_credit =array(
					'journal_voucher_id'			=> $journal_voucher_id,
					'account_id'					=> $account_id,
					'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
					'journal_voucher_amount'		=> $val['deposito_profit_sharing_amount'],
					'journal_voucher_credit_amount'	=> $val['deposito_profit_sharing_amount'],
					'account_id_default_status'		=> $account_id_default_status,
					'account_id_status'				=> 1,
				);

				$this->AcctDepositoProfitSharingCalculate_model->insertAcctJournalVoucherItem($data_credit);
			}
			$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Posting Jurnal Sukses
						 ";

				$this->session->set_userdata('message',$msg);
				redirect('AcctDepositoProfitSharingCalculate');
				
		}

		public function listdata(){
			$data['main_view']['acctdepositoprofitsharing']			= $this->AcctDepositoProfitSharingCalculate_model->getAcctDepositoProfitSharing_Detail();
			$data['main_view']['content']							= 'AcctDepositoProfitSharingCalculate/List_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$acctdepositoprofitsharing 		 	= $this->AcctDepositoProfitSharingCalculate_model->getAcctDepositoProfitSharing_Detail();


			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
				    <tr>
				        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">NO.</td>
				        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Anggota (Simp berjangka)</td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">NO. REK (Simp. berjangka)</td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Jatuh Tempo</td>
				        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Anggota (Simpanan)</td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">No. Rek (Simpanan)</td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Simp Berjangka</td>
				        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Index</td>
				        <td width=\"9%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\">Basil</td>
				       
				    </tr>				
				</table>
			";
				

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
				$no = 1;

				foreach ($acctdepositoprofitsharing as $key=>$val){		
					$acctsavingsaccount = $this->AcctDepositoProfitSharingCalculate_model->getAcctSavingsAccount_Detail($val['savings_account_id']);							
						$tbl2 .= "
							<tr>			
								<td style=\"text-align:center\" width=\"5%\">$no.</td>
								<td style=\"text-align:left\" width=\"18%\">".$val['member_name']."</td>
								<td style=\"text-align:left\" width=\"10%\">".$val['deposito_account_no']."</td>
								<td style=\"text-align:left\" width=\"10%\">".tgltoview($val['deposito_account_due_date'])."</td>
								<td width=\"18%\">".$acctsavingsaccount['member_name']."</td>
								<td width=\"10%\">".$acctsavingsaccount['savings_account_no']."</td>

								<td style=\"text-align:right\" width=\"12%\">".number_format($val['deposito_daily_average_balance'], 2)."</td>
								<td style=\"text-align:left\" width=\"7%\">".$val['deposito_index_amount']."</td>
								<td style=\"text-align:right\" width=\"9%\">".number_format($val['deposito_profit_sharing_amount'], 2)."</td>
								
							</tr>
						";
						$no++;
					} 

			
			$tbl3 = "
				</table>
			";

			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3, true, false, false, false, '');

			ob_clean();

			$filename = 'List.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
	}
?>