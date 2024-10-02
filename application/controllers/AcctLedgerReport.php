<?php
	Class AcctLedgerReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctLedgerReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctaccount']		= create_double($this->AcctLedgerReport_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['corebranch']		= create_double($this->AcctLedgerReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctLedgerReport/ListAcctLedgerReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 		=> tgltodb($this->input->post('start_date',true)),
				"end_date" 			=> tgltodb($this->input->post('end_date',true)),
				"account_id" 		=> $this->input->post('account_id',true),
				"branch_id"			=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id'])){
				$branch_id = $auth['branch_id'];
			} else {
				$branch_id = $sesi['branch_id'];
			}

			$branch_name = $this->AcctLedgerReport_model->getBranchName($branch_id);


			$accountbalancedetail	= $this->AcctLedgerReport_model->getAcctAccountBalanceDetail($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

			$opening_date = $this->AcctLedgerReport_model->getOpeningDate($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

			$opening_balance = $this->AcctLedgerReport_model->getOpeningBalance($opening_date, $sesi['account_id'], $branch_id);

			if(empty($opening_balance)){
				$opening_date = $this->AcctLedgerReport_model->getLastDate($sesi['account_id'], $sesi['start_date'], $sesi['end_date'], $branch_id);

				$opening_balance = $this->AcctLedgerReport_model->getLastBalance($opening_date, $sesi['account_id'], $branch_id);
			}

			$account_id_status 	= $this->AcctLedgerReport_model->getAccountIDDefaultStatus($sesi['account_id']);
			$accountstatus 		= $this->configuration->AccountStatus();

			// print_r($accountbalancedetail);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

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

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">LAPORAN BUKU BESAR (LEDGER)</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">".$branch_name."</div></td>
				    </tr>
				</table>";

			$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">No. Rek</div></td>
				        <td width=\"25%\"><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountCode($sesi['account_id'])."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Saldo</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".number_format($opening_balance, 2)."</div></td>
				    </tr>
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Nama</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountName($sesi['account_id'])."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left; font-size:12px\">Saldo Normal</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".$accountstatus[$account_id_status]."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
			        <td width=\"40%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Uraian</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Debit (Rp)</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Kredit (Rp)</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($accountbalancedetail as $key => $val) {
				$description = $this->AcctLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);
				// print_r($acctcreditspayment);exit;

				if($account_id_status == 0){
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_in'];
					$totalkredit += $val['account_out'];
					$sisasaldo = ($opening_balance + $totaldebit) - $totalkredit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				} else {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".tgltoview($val['transaction_date'])."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;\">".$description."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
					    </tr>
					";

					$totaldebit += $val['account_out'];
					$totalkredit += $val['account_in'];
					$sisasaldo = ($opening_balance + $totalkredit) - $totaldebit;
					$no++;

					$tbl4 = "
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Jumlah Mutasi</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaldebit, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalkredit, 2)."</div></td>
							
						</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:right;font-style:italic\">Saldo Akhir</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($sisasaldo, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							
						</tr>
									
					</table>";
				}

				
				
			}

			
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan_buku_besar_'.tgltoview($sesi['start_date']).'_'.$branch_name.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function cashTellerReport(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctLedgerReportTeller');
			if(!is_array($sesi)){
				$sesi['start_date']				= date("Y-m-d");
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			$month 	= date('m', strtotime($sesi['start_date']));
			$year 	= date('Y', strtotime($sesi['start_date']));

			if($month == '01'){
				$last_month 	= '12';
				$last_year 		= $year - 1;
			} else {
				$last_month 	= $month - 1;
				$last_year		= $year;
			}

			$start_date 		= $year.'-'.$month.'-01';
			$end_date 			= tgltodb($sesi['start_date']);

			$preferencecompany 	= $this->AcctLedgerReport_model->getPreferenceCompany();

			$opening_balance 	= $this->AcctLedgerReport_model->getOpeningBalance($preferencecompany['account_cash_id'], $month, $year, $sesi['branch_id']);

			// print_r($opening_balance);exit;

			$mutation_in 		= $this->AcctLedgerReport_model->getMutationIn($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$mutation_out 		= $this->AcctLedgerReport_model->getMutationOut($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$new_opening_balance	= ($opening_balance + $mutation_in) - $mutation_out;

			$accountbalancedetail	= $this->AcctLedgerReport_model->getAcctAccountBalanceDetailTeller($preferencecompany['account_cash_id'], $end_date, $sesi['branch_id']);

			$accountname 			= $this->AcctLedgerReport_model->getAccountName($preferencecompany['account_cash_id']);

			if(!empty($accountbalancedetail)){
				$last_balance 		= $new_opening_balance;
				foreach ($accountbalancedetail as $key => $val) {
					$description 		= $this->AcctLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$journal_voucher_no = $this->AcctLedgerReport_model->getJournalVoucherNo($val['transaction_id']);

					$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];

					$debet 	= $val['account_in'];
					$kredit = $val['account_out'];

					if($last_balance >= 0){
						$last_balance_debet 	= $last_balance;
						$last_balance_kredit 	= 0;
					} else {
						$last_balance_debet 	= 0;
						$last_balance_kredit 	= $last_balance;
					}
					

					

					$data_acctaccountbalance[] = array (
						'transaction_date'			=> $val['transaction_date'],
						'transaction_no'			=> $journal_voucher_no,
						'transaction_description'	=> $description,
						'account_name'				=> $accountname,
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'last_balance_debet'		=> $last_balance_debet,
						'last_balance_credit'		=> $last_balance_kredit,
					);
				}

				$count_data = count($accountbalancedetail);

				$rows 		= ceil($count_data / 400);
				$rowsexcel 	= ceil($count_data / 1000);
			} else {
				$data_acctaccountbalance = array ();

				$count_data = 0;

				$rows 		= 0;

				$rowsexcel 	= 0;
			}

			$data['main_view']['AcctGeneralLedgerReport']	= $data_acctaccountbalance;

			$data['main_view']['file']						= $rows;
			
			$data['main_view']['opening_balance']			= $new_opening_balance;

			$data['main_view']['corebranch']				= create_double($this->AcctLedgerReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']					= 'AcctLedgerReport/ListAcctLedgerReportTellerNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filtercash(){
			$data = array (
				'start_date'				=> $this->input->post('start_date',true),
				'branch_id'					=> $this->input->post('branch_id',true),
			);
			$this->session->set_userdata('filter-AcctLedgerReportTeller',$data);
			redirect('AcctLedgerReport/cashTellerReport');
		}

		public function pdf(){
			$baris 	= $this->uri->segment(3);
			$file 	= $this->uri->segment(4);


			// print_r($key);exit;

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-AcctLedgerReportTeller');
			if(!is_array($sesi)){
				$sesi['start_date']				= date("Y-m-d");
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			$month 	= date('m', strtotime($sesi['start_date']));
			$year 	= date('Y', strtotime($sesi['start_date']));

			if($month == '01'){
				$last_month 	= '12';
				$last_year 		= $year - 1;
			} else {
				$last_month 	= $month - 1;
				$last_year		= $year;
			}

			$start_date 		= $year.'-'.$month.'-01';
			$end_date 			= tgltodb($sesi['start_date']);

			$preferencecompany 	= $this->AcctLedgerReport_model->getPreferenceCompany();

			$opening_balance 	= $this->AcctLedgerReport_model->getOpeningBalance($preferencecompany['account_cash_id'], $month, $year, $sesi['branch_id']);

			$mutation_in 		= $this->AcctLedgerReport_model->getMutationIn($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$mutation_out 		= $this->AcctLedgerReport_model->getMutationOut($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$new_opening_balance	= ($opening_balance + $mutation_in) - $mutation_out;

			$accountbalancedetail	= $this->AcctLedgerReport_model->getAcctAccountBalanceDetailTeller($preferencecompany['account_cash_id'], $end_date, $sesi['branch_id']);

			$no = 0;
			if(!empty($accountbalancedetail)){
				$last_balance 		= $new_opening_balance;
				foreach ($accountbalancedetail as $key => $val) {
					

					$description = $this->AcctLedgerReport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$last_balance = ($last_balance + $val['account_in']) - $val['account_out'];


					$debet 	= $val['account_in'];
					$kredit = $val['account_out'];

					if($last_balance >= 0){
						$last_balance_debet 	= $last_balance;
						$last_balance_kredit 	= 0;
					} else {
						$last_balance_debet 	= 0;
						$last_balance_kredit 	= $last_balance;
					}


					

					$data_acctaccountbalance[] = array (
						'no'						=> $no,
						'transaction_date'			=> $val['transaction_date'],
						'transaction_description'	=> $description,
						'account_name'				=> $accountname,
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'last_balance_debet'		=> $last_balance_debet,
						'last_balance_credit'		=> $last_balance_kredit,
					);
					
				}
				$sisa = $no % 400;
			} else {
				$data_acctaccountbalance = array ();
				$sisa = 0;
			}

			

			// print_r($data_acctaccountbalance);exit;

			for ($i=0; $i <= $baris ; $i++) {
				
				if($i == $baris){
					// print_r("a");exit;
					$rows = $sisa;
				} else {
					$rows = 400;
				}

				$array_terpecah[$i] = array_splice($data_acctaccountbalance, 0, $rows);

				// print_r($array_terpecah);exit;

				
			}

			$datacetak = $array_terpecah[$file];

			// print_r($array_terpecah[$file]);exit;

			$this->processPrintingCashTellerReport($datacetak);
		}

		public function processPrintingCashTellerReport($data){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi	= 	$this->session->userdata('filter-AcctLedgerReportTeller');
			if(!is_array($sesi)){
				$sesi['start_date']				= date("Y-m-d");
			}

			if(empty($sesi['branch_id'])){
				$sesi['branch_id']		= $auth['branch_id'];
			}

			$month 	= date('m', strtotime($sesi['start_date']));
			$year 	= date('Y', strtotime($sesi['start_date']));

			if($month == '01'){
				$last_month 	= '12';
				$last_year 		= $year - 1;
			} else {
				$last_month 	= $month - 1;
				$last_year		= $year;
			}

			$start_date 		= $year.'-'.$month.'-01';
			$end_date 			= tgltodb($sesi['start_date']);

			
			$preferencecompany 	= $this->AcctLedgerReport_model->getPreferenceCompany();

			$opening_balance 	= $this->AcctLedgerReport_model->getOpeningBalance($preferencecompany['account_cash_id'], $month, $year, $sesi['branch_id']);

			$mutation_in 		= $this->AcctLedgerReport_model->getMutationIn($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$mutation_out 		= $this->AcctLedgerReport_model->getMutationOut($preferencecompany['account_cash_id'], $start_date, $end_date, $sesi['branch_id']);

			$new_opening_balance	= ($opening_balance + $mutation_in) - $mutation_out;


			$branch_name 		= $this->AcctLedgerReport_model->getBranchName($branch_id);

			$account_id_status 	= $this->AcctLedgerReport_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

			// print_r($accountbalancedetail);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

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

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">LAPORAN ARUS KAS HARIAN</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Per ".tgltoview($sesi['start_date'])."</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">".$branch_name."</div></td>
				    </tr>
				</table>";

			$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">No. Rek</div></td>
				        <td width=\"25%\"><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountCode($preferencecompany['account_cash_id'])."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Saldo</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".number_format($new_opening_balance, 2)."</div></td>
				    </tr>
				    <tr>
				        <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Nama</div></td>
				        <td><div style=\"text-align: left; font-size:12px\">".$this->AcctLedgerReport_model->getAccountName($preferencecompany['account_cash_id'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" rowspan=\"2\"><div style=\"text-align: center;\">No</div></td>
			        <td width=\"12%\" rowspan=\"2\"><div style=\"text-align: center;\">Tanggal</div></td>
			        <td width=\"25%\" rowspan=\"2\"><div style=\"text-align: center;\">Uraian</div></td>
			        <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center;\">Debet </div></td>
			        <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center;\">Kredit </div></td>
			        <td width=\"30%\" colspan=\"2\"><div style=\"text-align: center;\">Saldo </div></td>
				</tr>
				
				<tr>
			        <td width=\"15%\"><div style=\"text-align: center;\">Debet </div></td>
			        <td width=\"15%\"><div style=\"text-align: center;\">Kredit </div></td>
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\" border=\"1\">";
		
			foreach ($data as $key => $val) {
					$tbl3 .= "
						<tr>			
							<td width=\"5%\" style=\"text-align:center\">$no.</td>
							<td width=\"12%\" style=\"text-align:center\">".tgltoview($val['transaction_date'])."</td>
							<td width=\"25%\">".$val['transaction_description']."</td>
							<td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['account_in'], 2)."</div></td>
							<td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['account_out'], 2)."</div></td>
							<td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['last_balance_debet'], 2)."</div></td>
							<td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['last_balance_credit'], 2)."</div></td>
						</tr>
					";

					$no++;

					$tbl4 = "
									
					</table>";
				
			}

			
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan_kas_harian_'.tgltoview($sesi['start_date']).'_'.$branch_name.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

	}
?>