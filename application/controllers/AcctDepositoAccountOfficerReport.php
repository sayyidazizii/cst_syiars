<?php
	Class AcctDepositoAccountOfficerReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoAccountOfficerReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('Configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$data['main_view']['coreoffice']	= create_double($this->AcctDepositoAccountOfficerReport_model->getCoreOffice(),'office_id', 'office_name');	
			$corebranch 						= create_double_branch($this->AcctDepositoAccountOfficerReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctDepositoAccountOfficerReport/FormFilterAcctDepositoAccountOfficerReport_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 

			$data = array (
				'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			if(empty($data['branch_id']) || $data['branch_id'] == 0){
				$branch_id = '';
			} else {
				$branch_id = $data['branch_id'];
			}

			$acctdeposito 			= $this->AcctDepositoAccountOfficerReport_model->getAcctDeposito();
			$preferencecompany 		= $this->AcctDepositoAccountOfficerReport_model->getPreferenceCompany();
			

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

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
				        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">".$preferencecompany['company_name']."</div></td>		       
				    </tr>						
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			
			if(!empty($data['office_id'])){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR NASABAH BERJANGKA : ".$this->AcctDepositoAccountOfficerReport_model->getOfficeName($data['office_id'])."</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($data['start_date'])." S.D ".tgltoview($data['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
					        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
					        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
					        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nominal</div></td>
					        <td width=\"7%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JK Waktu</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">TGL Mulai</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT Tempo</div></td>
					       
					    </tr>				
					</table>";

					

					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
					foreach ($acctdeposito as $kD => $vD) {
						$acctdepositoaccount 		= $this->AcctDepositoAccountOfficerReport_model->getAcctDepositoAccount($data['office_id'], $data['start_date'], $data['end_date'], $vD['deposito_id'], $branch_id);

						if(!empty($acctdepositoaccount)){
							$tbl3 .= "
								<br>
								<tr>
									<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
								</tr>
							";
							$no = 1;
							foreach ($acctdepositoaccount as $key => $val) {				
								$tbl3 .= "
									<tr>
								    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
								        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
								        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
								        <td width=\"7%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
								        <td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($val['deposito_account_date'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
								    </tr>
								";
								$no++;
								$totalsaldo += $val['deposito_account_amount'];
							}

							$tbl3 .= "	
								<tr>
									<td colspan =\"3\" style=\"border-top: 1px solid black;\"></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Subtotal </div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
								</tr>";

							$grandtotalsaldo += $totalsaldo;
						}
					}

					

					$tbl4 = "	
						<br>
						<tr>
							<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoAccountOfficerReport_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
						</tr>						
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">DAFTAR NASABAH BERJANGKA</div></td>
					        <td><div style=\"text-align: left;font-size:10; font-weight:bold\">Mulai Tgl. ".tgltoview($data['start_date'])." S.D ".tgltoview($data['end_date'])."</div></td>			       
					    </tr>						
					</table>";

					$pdf->writeHTML($tbl, true, false, false, false, '');

					$tbl1 = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
					    <tr>
					        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
					        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
					        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">BO</div></td>
					        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
					        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nominal</div></td>
					        <td width=\"7%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JK Waktu</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">TGL Mulai</div></td>
					        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT Tempo</div></td>
					       
					    </tr>				
					</table>";

					$no = 1;

					$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
					foreach ($acctdeposito as $kD => $vD) {
						$acctdepositoaccount 		= $this->AcctDepositoAccountOfficerReport_model->getAcctDepositoAccount($data['office_id'], $data['start_date'], $data['end_date'], $vD['deposito_id'], $branch_id);

						if(!empty($acctdepositoaccount)){
							$tbl3 .= "
								<br>
								<tr>
									<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
								</tr>
							";
							$no = 1;
							foreach ($acctdepositoaccount as $key => $val) {				
								$tbl3 .= "
									<tr>
								    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
								        <td width=\"10%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
								        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
								        <td width=\"5%\"><div style=\"text-align: left;\">".$this->AcctDepositoAccountOfficerReport_model->getOfficeCode($val['office_id'])."</div></td>
								        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
								        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
								        <td width=\"7%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
								        <td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($val['deposito_account_date'])."</div></td>
								        <td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
								    </tr>
								";
								$no++;
								$totalsaldo += $val['deposito_account_amount'];
							}

							$tbl3 .= "	
								<tr>
									<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Subtotal </div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
									<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
								</tr>";

							$grandtotalsaldo += $totalsaldo;
						}
					}
					

					$tbl4 = "
						<br>	
						<tr>
							<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoAccountOfficerReport_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
						</tr>						
					</table>";

					$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');
			}
			


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