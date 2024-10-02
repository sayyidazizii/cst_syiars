<?php
	Class AcctCreditsRescheduleReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsRescheduleReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsRescheduleReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsRescheduleReport/ListAcctCreditsRescheduleReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id'])){
				$branch_id = $auth['branch_id'];
			} else {
				$branch_id = $sesi['branch_id'];
			}


			$acctcreditsaccountreschedule	= $this->AcctCreditsRescheduleReport_model->getCreditsAccountReschedule($sesi['start_date'], $sesi['end_date'], $branch_id);

			// print_r($acctcreditsaccount);exit;


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
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR RESCHEDULLING PEMBIAYAAN</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\" >No.</div></td>
			        <td width=\"10%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\" >No. Akad</div></td>
			        <td width=\"12%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"15%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\" colspan=\"4\">Pembiayaan Lama</div></td>
			        <td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\" colspan=\"4\">Pembiayaan Baru</div></td>
			    </tr>
			    <tr>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Margin</div></td>
			        <td width=\"4%\"style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"6%\"style=\"border-bottom: 1px solid black;border-right: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Margin</div></td>
			        <td width=\"4%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"6%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($acctcreditsaccountreschedule as $key => $val) {
				// print_r($acctcreditspayment);exit;

				$tbl3 .= "
					<tr>
				    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"10%\" style=\"border-left: 1px solid black;\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_principal_old'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_margin_old'], 2)."</div></td>
				       	<td width=\"4%\"><div style=\"text-align: center;\">".$val['credits_account_period_old']."</div></td>
				       	<td width=\"6%\" style=\"border-right: 1px solid black;\"><div style=\"text-align: center;\">".tgltoview($val['credits_account_due_date_old'])."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_principal_new'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_margin_new'], 2)."</div></td>
				       	<td width=\"4%\"><div style=\"text-align: center;\">".$val['credits_account_period_new']."</div></td>
				       	<td width=\"6%\"><div style=\"text-align: center;\">".tgltoview($val['credits_account_due_date_new'])."</div></td>
				    </tr>
				";
				$totalpokokold 	+= $val['credits_account_last_balance_principal_old'];
				$totalmarginold	+= $val['credits_account_last_balance_margin_old'];
				$totalpokoknew 	+= $val['credits_account_last_balance_principal_new'];
				$totalmarginnew	+= $val['credits_account_last_balance_margin_new'];

				$no++;
			}

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsRescheduleReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalpokokold, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalmarginold, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalpokoknew, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalmarginnew, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					
					
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