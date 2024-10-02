<?php
	Class AcctSavingsDebetNote extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDebetNote_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('acctsavingsdebetnotetoken-'.$unique['unique']);

			$data['main_view']['content']			= 'AcctSavingsDebetNote/ListAcctSavingsDebetNote_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				
			);

			$this->session->set_userdata('filter-acctsavingsdebetnote',$data);
			redirect('AcctSavingsDebetNote');
		}

		public function getAcctSavingsDebetNote(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctsavingsdebetnote');
			if(!is_array($sesi)){
				$sesi['start_date']				= date('Y-m-d');
				$sesi['end_date']				= date('Y-m-d');
				
			}

			$savingscashstatus = $this->configuration->SavingsCashMutationStatus();

			$list = $this->AcctSavingsDebetNote_model->get_datatables($sesi['start_date'], $sesi['end_date'], $auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->savings_name;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = tgltoview($savingsaccount->savings_debet_note_date);
	            $row[] = $savingsaccount->mutation_name;
	            $row[] = $savingsaccount->account_name;
	            $row[] = number_format($savingsaccount->savings_debet_note_amount, 2);
	            $row[] = $savingscashstatus[$savingsaccount->savings_debet_note_status];
	            if($savingsaccount->validation == 0){
	            	$row[] = '<a href="'.base_url().'AcctSavingsDebetNote/validationAcctSavingsDebetNote/'.$savingsaccount->savings_debet_note_id.'" class="btn btn-success btn-xs" role="button"><span class="glyphicon glyphicon-check"></span> Validasi</a>';
	            } else {
	            	 $row[] = '';
	            }
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsDebetNote_model->count_all($sesi['start_date'], $sesi['end_date'], $auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsDebetNote_model->count_filtered($sesi['start_date'], $sesi['end_date'], $auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsdebetnote-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingsdebetnote-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingsdebetnote-'.$unique['unique']);
			redirect('AcctSavingsDebetNote/addAcctSavingsDebetNote');
		}

		public function getListAcctSavingsAccount(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctSavingsDebetNote/addAcctSavingsDebetNote/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);

		}
		
		public function addAcctSavingsDebetNote(){
			$savings_account_id = $this->uri->segment(3);

			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctsavingsdebetnotetoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('acctsavingsdebetnotetoken-'.$unique['unique'], $token);
			}


			$data['main_view']['acctsavingsaccount']		= $this->AcctSavingsDebetNote_model->getAcctSavingsAccount_Detail($savings_account_id);	
			$data['main_view']['acctaccount']				= create_double($this->AcctSavingsDebetNote_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['acctmutation']				= $this->AcctSavingsDebetNote_model->getAcctMutationDebetNote();
			
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['content']					= 'AcctSavingsDebetNote/FormAddAcctSavingsDebetNote_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddAcctSavingsDebetNote(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'savings_account_id'						=> $this->input->post('savings_account_id', true),
				'mutation_id'								=> $this->input->post('mutation_id', true),
				'member_id'									=> $this->input->post('member_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'branch_id'									=> $auth['branch_id'],
				'savings_debet_note_date'					=> tgltodb($this->input->post('savings_debet_note_date', true)),
				'account_id'								=> $this->input->post('account_id', true),
				'savings_debet_note_amount'					=> $this->input->post('savings_debet_note_amount', true),
				'savings_debet_note_token'					=> $this->input->post('savings_debet_note_token', true),
				'operated_name'								=> $auth['username'],
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('savings_account_id', 'No. Mutasi', 'required');
			$this->form_validation->set_rules('savings_debet_note_amount', 'Jumlah Transaksi', 'required');

			$savings_debet_note_token 	= $this->AcctSavingsDebetNote_model->getSavingsCashMutationToken($data['savings_debet_note_token']);

			$transaction_module_code 		= "NTDB";
			$transaction_module_id 			= $this->AcctSavingsDebetNote_model->getTransactionModuleID($transaction_module_code);
			
			$journal_voucher_period 		= date("Ym", strtotime($data['savings_debet_note_date']));
			
			if($this->form_validation->run()==true){
				if($savings_debet_note_token->num_rows()==0){
					if($this->AcctSavingsDebetNote_model->insertAcctSavingsDebetNote($data)){
						$acctsavingscash_last 			= $this->AcctSavingsDebetNote_model->getAcctSavingsDebetNote_Last($data['created_id']);
						$accountname 					= $this->AcctSavingsDebetNote_model->getAccountName($data['account_id']);

						$data_journal = array(
							'branch_id'							=> $auth['branch_id'],
							'journal_voucher_period' 			=> $journal_voucher_period,
							'journal_voucher_date'				=> date('Y-m-d'),
							'journal_voucher_title'				=> 'Nota Debit '.$accountname.' '.$acctsavingscash_last['member_name'],
							'journal_voucher_description'		=> 'Nota Debit '.$accountname.' '.$acctsavingscash_last['member_name'],
							'journal_voucher_token'				=> $data['savings_debet_note_token'],
							'transaction_module_id'				=> $transaction_module_id,
							'transaction_module_code'			=> $transaction_module_code,
							'transaction_journal_id' 			=> $acctsavingscash_last['savings_debet_note_id'],
							'transaction_journal_no' 			=> $acctsavingscash_last['savings_account_no'],
							'created_id' 						=> $data['created_id'],
							'created_on' 						=> $data['created_on'],
						);
						
						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 				= $this->AcctSavingsDebetNote_model->getJournalVoucherID($data['created_id']);

						$preferencecompany 					= $this->AcctSavingsDebetNote_model->getPreferenceCompany();

						$account_id 						= $this->AcctSavingsDebetNote_model->getAccountID($data['savings_id']);

						$account_id_default_status 			= $this->AcctSavingsDebetNote_model->getAccountIDDefaultStatus($account_id);

	
						$data_debet = array (
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_debet_note_amount'],
							'journal_voucher_debit_amount'	=> $data['savings_debet_note_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
							'journal_voucher_item_token'	=> $data['savings_debet_note_token'].$account_id,
						);

						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucherItem($data_debet);

						$account_id_default_status 			= $this->AcctSavingsDebetNote_model->getAccountIDDefaultStatus($data['account_id']);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $data['account_id'],
							'journal_voucher_description'	=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
							'journal_voucher_amount'		=> $data['savings_debet_note_amount'],
							'journal_voucher_credit_amount'	=> $data['savings_debet_note_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
							'journal_voucher_item_token'	=> $data['savings_debet_note_token'].$data['account_id'],
						);

						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucherItem($data_credit);

						

						

						
						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Mutasi Simpanan Non Tunai Nota Debit Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctsavingsdebetnote-'.$sesi['unique']);
						$this->session->unset_userdata('acctsavingsdebetnotetoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsDebetNote/');
					}else{
						$this->session->set_userdata('addacctsavingsdebetnote',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Mutasi Simpanan Non Tunai Nota Debit Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('AcctSavingsDebetNote');
					}
				} else {
					$acctsavingscash_last 			= $this->AcctSavingsDebetNote_model->getAcctSavingsDebetNote_Last($data['created_id']);
					$accountname 					= $this->AcctSavingsDebetNote_model->getAccountName($data['account_id']);
					
					$data_journal = array(
						'branch_id'							=> $auth['branch_id'],
						'journal_voucher_period' 			=> $journal_voucher_period,
						'journal_voucher_date'				=> date('Y-m-d'),
						'journal_voucher_title'				=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
						'journal_voucher_description'		=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
						'journal_voucher_token'				=> $data['savings_debet_note_token'],
						'transaction_module_id'				=> $transaction_module_id,
						'transaction_module_code'			=> $transaction_module_code,
						'transaction_journal_id' 			=> $acctsavingscash_last['savings_debet_note_id'],
						'transaction_journal_no' 			=> $acctsavingscash_last['savings_account_no'],
						'created_id' 						=> $data['created_id'],
						'created_on' 						=> $data['created_on'],
					);

					$journal_voucher_token 	= $this->AcctSavingsDebetNote_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows()== 0){
						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucher($data_journal);
					}

					$journal_voucher_id 	= $this->AcctSavingsDebetNote_model->getJournalVoucherID($data['created_id']);

					$preferencecompany 		= $this->AcctSavingsDebetNote_model->getPreferenceCompany();

			
					$account_id 						= $this->AcctSavingsDebetNote_model->getAccountID($data['savings_id']);

					$account_id_default_status 			= $this->AcctSavingsDebetNote_model->getAccountIDDefaultStatus($account_id);

					$data_debet = array (
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $account_id,
						'journal_voucher_description'	=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
						'journal_voucher_amount'		=> $data['savings_debet_note_amount'],
						'journal_voucher_debit_amount'	=> $data['savings_debet_note_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 0,
						'journal_voucher_item_token'	=> $data['savings_debet_note_token'].$account_id,
					);

					$journal_voucher_item_token 		= $this->AcctSavingsDebetNote_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucherItem($data_debet);
					}

					$account_id_default_status 			= $this->AcctSavingsDebetNote_model->getAccountIDDefaultStatus($data['account_id']);

					$data_credit = array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $data['account_id'],
						'journal_voucher_description'	=> 'NOTA DEBIT '.$accountname.' '.$acctsavingscash_last['member_name'],
						'journal_voucher_amount'		=> $data['savings_debet_note_amount'],
						'journal_voucher_credit_amount'	=> $data['savings_debet_note_amount'],
						'account_id_default_status'		=> $account_id_default_status,
						'account_id_status'				=> 1,
						'journal_voucher_item_token'	=> $data['savings_debet_note_token'].$data['account_id'],
					);

					$journal_voucher_item_token 		= $this->AcctSavingsDebetNote_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows()==0){
						$this->AcctSavingsDebetNote_model->insertAcctJournalVoucherItem($data_credit);
					}

					

					

					
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Mutasi Simpanan Non Tunai Nota Debit Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavingsdebetnote-'.$sesi['unique']);
					$this->session->unset_userdata('acctsavingsdebetnotetoken-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsDebetNote');
				}
				
			}else{
				$this->session->set_userdata('addacctsavingsdebetnote',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsDebetNote');
			}
		}

		public function printNoteAcctSavingsDebetNote(){
			$auth = $this->session->userdata('auth');
			$savings_debet_note_id 	= $this->uri->segment(3);
			$acctsavingsdebetnote	= $this->AcctSavingsDebetNote_model->getAcctSavingsDebetNote_Detail($savings_debet_note_id);
			$preferencecompany 			= $this->AcctSavingsDebetNote_model->getPreferenceCompany();

			if($acctsavingsdebetnote['mutation_id'] == $preferencecompany['cash_deposit_id']){
				$keterangan 	= 'SETORAN TUNAI';
				$keterangan2 	= 'Telah diterima dari';
				$paraf 			= 'Penyetor';
			} else if($acctsavingsdebetnote['mutation_id'] == $preferencecompany['cash_withdrawal_id']){
				$keterangan 	= 'PENARIKAN TUNAI';
				$keterangan2 	= 'Telah dibayarkan kepada';
				$paraf 			= 'Penerima';
			} else if($acctsavingsdebetnote['mutation_id'] == 3){
				$keterangan 	= 'KOREKSI DEBIT';
				$keterangan2 	= 'Telah diterima dari';
				$paraf 			= 'Penyetor';
			} else if($acctsavingsdebetnote['mutation_id'] == 4){
				$keterangan 	= 'KOREKSI DEBET';
				$keterangan2 	= 'Telah dibayarkan kepada';
				$paraf 			= 'Penerima';
			}


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('tcpdf, PDF, example, test, guide');*/

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

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
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

			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"80%\"><div style=\"text-align: center; font-size:14px\">BUKTI ".$keterangan."</div></td>
			    </tr>
			    <tr>
			        <td width=\"80%\"><div style=\"text-align: center; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			".$keterangan2." :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsdebetnote['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsdebetnote['savings_account_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsdebetnote['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctsavingsdebetnote['savings_debet_note_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$keterangan."</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsdebetnote['savings_debet_note_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctSavingsDebetNote_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$paraf."</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi_'.$keterangan.'_'.$acctsavingsdebetnote['member_name'].'.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function validationAcctSavingsDebetNote(){
			$auth = $this->session->userdata('auth');
			$savings_debet_note_id = $this->uri->segment(3);

			$data = array (
				'savings_debet_note_id'  	=> $savings_debet_note_id,
				'validation'					=> 1,
				'validation_id'					=> $auth['user_id'],
				'validation_on'					=> date('Y-m-d H:i:s'),
			);

			if($this->AcctSavingsDebetNote_model->validationAcctSavingsDebetNote($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Setoran Tunai Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsDebetNote/printValidationAcctSavingsDebetNote/'.$savings_debet_note_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Setoran Tunai Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsDebetNote');
			}
		}

		public function printValidationAcctSavingsDebetNote(){
			$savings_debet_note_id 	= $this->uri->segment(3);
			$acctsavingsdebetnote	= $this->AcctSavingsDebetNote_model->getAcctSavingsDebetNote_Detail($savings_debet_note_id);


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('tcpdf, PDF, example, test, guide');*/

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

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
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

			$pdf->SetFont('helveticaI', '', 7);

			// -----------------------------------------------------------------------------

			$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsdebetnote['savings_account_no']."</div></td>
			        <td width=\"45%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsdebetnote['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsdebetnote['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsDebetNote_model->getUsername($acctsavingsdebetnote['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($acctsavingsdebetnote['savings_debet_note_amount'], 2)."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Validasi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function voidAcctSavingsDebetNote(){
			$data['main_view']['acctsavingsdebetnote']	= $this->AcctSavingsDebetNote_model->getAcctSavingsDebetNote_Detail($this->uri->segment(3));
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['content']					= 'AcctSavingsDebetNote/FormVoidAcctSavingsDebetNote_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsDebetNote(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_debet_note_id"	=> $this->input->post('savings_debet_note_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsDebetNote_model->voidAcctSavingsDebetNote($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Simpanan Non Tunai Nota Debit Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsDebetNote');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Simpanan Non Tunai Nota Debit Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctSavingsDebetNote');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('AcctSavingsDebetNote');
			}
		}


		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsdebetnote-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsdebetnote-'.$unique['unique'],$sessions);
		}
	}
?>