<?php
	Class AcctDepositoDailyCashMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoDailyCashMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$this->load->view('MainPage_view');
		}

		public function addCashDeposit(){
			$corebranch 						= create_double_branch($this->AcctDepositoDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctDepositoDailyCashMutation/FormAddAcctDepositoCashDeposit_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processPrintingCashDeposit(){
			$auth 	=	$this->session->userdata('auth'); 

			$start_date	= tgltodb($this->input->post('start_date', true));
			$end_date	= tgltodb($this->input->post('end_date', true));
			$branch_id 	= $this->input->post('branch_id', true);

			if(empty($branch_id) || $branch_id == 0){
				$branch_id = '';
			}


			
			$preference		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();
			$acctdeposito 	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();

			


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
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;\">".$preference['company_name']."</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI SETORAN BERJANGKA TGL : &nbsp; ".tgltoview($start_date)." - ".tgltoview($end_date)."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK WAKTU</div></td>
			        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT TEMPO</div></td>
			        <td width=\"18%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SALDO</div></td>
			       
			    </tr>				
			</table>";


			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctdeposito as $kD => $vD) {
				$acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashDeposit($start_date, $end_date, $vD['deposito_id'], $branch_id);
				if(!empty($acctdepositocashdeposit)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
						</tr>
					";

					$no = 1;

					foreach ($acctdepositocashdeposit as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"12%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
						        <td width=\"17%\"><div style=\"text-align: left;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
						        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
						    </tr>
						";

						$totalsaldo 	+= $val['deposito_account_amount'];

						$no++;
					}
					$tbl3 .= "
						<tr>
							<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
						</tr>";

					$grandtotalsaldo += $totalsaldo;
				}
			}

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function addCashWithdrawal(){
			$corebranch 						= create_double_branch($this->AcctDepositoDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctDepositoDailyCashMutation/FormAddAcctDepositoCashWithdrawal_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processPrintingCashWithdrawal(){
			$auth 	=	$this->session->userdata('auth'); 

			$start_date	= tgltodb($this->input->post('start_date', true));
			$end_date	= tgltodb($this->input->post('end_date', true));
			$branch_id 	= $this->input->post('branch_id', true);

			if(empty($branch_id) || $branch_id == 0){
				$branch_id = '';
			}


			
			$preference		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();
			$acctdeposito 	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();



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
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;\">".$preference['company_name']."</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI PENARIKAN BERJANGKA TGL : &nbsp; ".tgltoview($start_date)." - ".tgltoview($end_date)."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK WAKTU</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT TEMPO</div></td>
			        <td width=\"18%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SALDO</div></td>
			       
			    </tr>				
			</table>";

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctdeposito as $kD => $vD) {
				$acctdepositocashwithdrawal	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashWithdrawal($start_date, $end_date, $vD['deposito_id'], $branch_id);
				if(!empty($acctdepositocashwithdrawal)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
						</tr>
					";

					$no = 1;
					foreach ($acctdepositocashwithdrawal as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"12%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
						         <td width=\"16%\"><div style=\"text-align: left;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
						        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
						    </tr>
						";

						$totalsaldo 	+= $val['deposito_account_amount'];

						$no++;
					}
					$tbl3 .= "
						<tr>
							<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
						</tr>";

					$grandtotalsaldo += $totalsaldo;
				}
			}
			

			$tbl4 = "
				<br>
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

	}
?>