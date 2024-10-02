<?php
	Class AcctNominativeDepositoReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeDepositoReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->AcctNominativeDepositoReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['kelompoklaporansimpananberjangka']	= $this->configuration->KelompokLaporanSimpananBerjangka();
			$data['main_view']['content']			= 'AcctNominativeDepositoReport/ListAcctNominativeDepositoReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
				"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
				"branch_id"								=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id = '';
			} else {
				$branch_id = $sesi['branch_id'];
			}


			$acctdepositoaccount	= $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport($sesi['start_date'], $branch_id);
			$acctdeposito = $this->AcctNominativeDepositoReport_model->getAcctDeposito();

			// print_r($acctdepositoaccount);exit;

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

			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN BERJANGKA</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
					    </tr>
					</table>";
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN BERJANGKA PER JENIS</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
					    </tr>
					</table>";
			}
			

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Nominal</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal Mulai</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			$totalglobal = 0;
			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				foreach ($acctdepositoaccount as $key => $val) {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_date'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_due_date'], 2)."</div></td>
					    </tr>
					";

					$totalglobal += $val['deposito_account_amount'];

					$no++;
				}
			} else {
				foreach ($acctdeposito as $kSavings => $vSavings) {
					$acctdepositoaccount_deposito = $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport_Deposito($sesi['start_date'], $vSavings['deposito_id'], $branch_id);
					
					if(!empty($acctdepositoaccount_deposito)){
						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"6\" width=\"95%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">".$vSavings['deposito_name']."</div></td>
							</tr>
							<br>
						";
						$nov = 1;
						$totalperjenis = 0;
						foreach ($acctdepositoaccount_deposito as $k => $v) {
							$tbl3 .= "
								<tr>
							    	<td width=\"5%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">".$v['deposito_account_no']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: left;\">".$v['member_name']."</div></td>
							        <td width=\"20%\"><div style=\"text-align: left;\">".$v['member_address']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($v['deposito_account_amount'], 2)."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".$v['deposito_account_period']."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_date'], 2)."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_due_date'], 2)."</div></td>
							    </tr>

							";

							$totalperjenis += $v['deposito_account_amount'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalperjenis, 2)."</div></td>
							</tr>
							<br>
						";

						$totalglobal += $totalperjenis;
					}

					
				}
			}

			$tbl4 = "
					<tr>
						<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:left\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctNominativeDepositoReport_model->getUserName($auth['user_id'])."</div></td>
						<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Total</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalglobal, 2)."</div></td>
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