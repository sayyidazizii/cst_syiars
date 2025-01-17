<?php
	ob_start();
?>
<?php
	Class AcctCreditsPaymentAgingReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentAgingReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']		= create_double($this->AcctCreditsPaymentAgingReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['corecreditsaging']	= create_double($this->AcctCreditsPaymentAgingReport_model->getCoreCreditsAging(),'credits_aging_id','credits_aging_name');
			$data['main_view']['acctcredits']		= create_double($this->AcctCreditsPaymentAgingReport_model->getAcctCredits(),'credits_id','credits_name');
			$data['main_view']['content']			= 'AcctCreditsPaymentAgingReport/ListAcctCreditsPaymentAgingReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 			=> tgltodb($this->input->post('start_date',true)),
				"end_date" 				=> tgltodb($this->input->post('end_date',true)),
				"branch_id"				=> $this->input->post('branch_id',true),
				"credits_id"			=> $this->input->post('credits_id',true),
				"credits_aging_id"		=> $this->input->post('credits_aging_id',true),
			);

			if(empty($sesi['branch_id'])){
				$branch_id = $auth['branch_id'];
			} else {
				$branch_id = $sesi['branch_id'];
			}

			$branch_name 		= $this->AcctCreditsPaymentAgingReport_model->getBranchName($sesi['branch_id']);
			$corecreditsaging 	= $this->AcctCreditsPaymentAgingReport_model->getCoreCreditsAging_Detail($sesi['credits_aging_id']);

			$acctcreditsaccount	= $this->AcctCreditsPaymentAgingReport_model->getAcctCreditsAccount($sesi['end_date'], $branch_id, $sesi['credits_id'], $corecreditsaging['credits_aging_start_day'], $corecreditsaging['credits_aging_end_day']);


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

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			$tbl = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR PEMBIAYAAN JATUH TEMPO TGL ".tgltoview($sesi['start_date'])." s.d. ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
					<tr>
				        <td><div style=\"text-align: center; font-size:14px\">CABANG : ".$branch_name."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
					<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Produk</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Plafon</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Angs Pokok</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Angs Margin</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">SLD Pokok (outstanding)</div></td>
			        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tgl Terakhir Angsur</div></td>
					<td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Terlambat</div></td>
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($acctcreditsaccount as $key => $val) {

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						<td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_name']."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"13%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_net_price'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_principal_amount'], 2)."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_margin_amount'], 2)."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_principal'], 2)."</div></td>
				        <td width=\"7%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_last_payment_date'])."</div></td>
						<td width=\"5%\"><div style=\"text-align: left;\">".$val['total_days']."</div></td>
				    </tr>
				";

				$totalplafon 		+= $val['credits_account_net_price'];
				$totalangspokok 	+= $val['credits_account_principal_amount'];
				$totalangsmargin 	+= $val['credits_account_margin_amount'];
				$totalsisa 			+= $val['credits_account_last_balance_principal'];

				$no++;
			}

			$tbl4 = "
				<tr>
					<td colspan =\"4\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsPaymentAgingReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalplafon, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangspokok, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangsmargin, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsisa, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
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