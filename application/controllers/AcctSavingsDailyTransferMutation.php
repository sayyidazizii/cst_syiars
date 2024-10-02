<?php
	Class AcctSavingsDailyTransferMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDailyTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$corebranch 						= create_double_branch($this->AcctSavingsDailyTransferMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctSavingsDailyTransferMutation/FormFilterAcctSavingsDailyTransferMutation_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 

			$start_date	= tgltodb($this->input->post('start_date', true));
			$end_date	= tgltodb($this->input->post('end_date', true));
			$branch_id 	= $this->input->post('branch_id', true);

			if(empty($branch_id) || $branch_id == 0){
				$branch_id = '';
			}


			
			$preference		= $this->AcctSavingsDailyTransferMutation_model->getPreferenceCompany();

			$acctsavingstransfermutation	= $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutation($start_date, $end_date, $branch_id);


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
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI SIMPANAN NON TUNAI TGL : &nbsp;&nbsp; ".tgltoview($start_date)."&nbsp;&nbsp; S.D &nbsp;&nbsp;".tgltoview($end_date)."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">TANGGAL</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">SANDI</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">NOMINAL</div></td>
			        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Saldo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctsavingstransfermutation as $key => $val) {
				$acctsavingstransfermutationfrom = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationFrom($val['savings_transfer_mutation_id']);

				foreach ($acctsavingstransfermutationfrom as $kFrom => $vFrom) {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_transfer_mutation_date'])."</div></td>
					        <td width=\"16%\"><div style=\"text-align: left;\">".$vFrom['savings_account_no']."</div></td>
					        <td width=\"25%\"><div style=\"text-align: left;\">".$vFrom['member_name']."</div></td>
					        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyTransferMutation_model->getMutationCode($vFrom['mutation_id'])."</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($vFrom['savings_transfer_mutation_from_amount'], 2)."</div></td>
					        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($vFrom['savings_account_last_balance'], 2)."</div></td>
					    </tr>
					";

					$totalnominalfrom 	+= $vFrom['savings_transfer_mutation_from_amount'];
					$totalsaldofrom 	+= $vFrom['savings_account_last_balance'];

					$no++;
				}

				$acctsavingstransfermutationto = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationTo($val['savings_transfer_mutation_id']);

				foreach ($acctsavingstransfermutationto as $kTo => $vTo) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_transfer_mutation_date'])."</div></td>
				        <td width=\"16%\"><div style=\"text-align: left;\">".$vTo['savings_account_no']."</div></td>
				        <td width=\"25%\"><div style=\"text-align: left;\">".$vTo['member_name']."</div></td>
				        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyTransferMutation_model->getMutationCode($vTo['mutation_id'])."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($vTo['savings_transfer_mutation_to_amount'], 2)."</div></td>
				        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($vTo['savings_account_last_balance'], 2)."</div></td>
				    </tr>
				";

				$totalnominalto 	+= $vTo['savings_transfer_mutation_to_amount'];
				$totalsaldoto 		+= $vTo['savings_account_last_balance'];

				$no++;
			}
			}

			$grandtotalnominal 	= $totalnominalfrom + $totalnominalto;
			$grandtotalsaldo	= $totalsaldoto + $totalsaldofrom;
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsDailyTransferMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalnominal, 2)."</div></td>
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