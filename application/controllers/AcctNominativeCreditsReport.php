<?php
	Class AcctNominativeCreditsReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeCreditsReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->AcctNominativeCreditsReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanPembiayaan();
			$data['main_view']['content']					= 'AcctNominativeCreditsReport/ListAcctNominativeCreditsReport_View';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
			$sesi = array (
				"start_date" 					=> tgltodb($this->input->post('start_date',true)),
				"end_date" 						=> tgltodb($this->input->post('end_date',true)),
				"kelompok_laporan_pembiayaan"	=> $this->input->post('kelompok_laporan_pembiayaan',true),
				"branch_id"					=> $this->input->post('branch_id',true),
			);

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id = '';
			} else {
				$branch_id = $sesi['branch_id'];
			}


			$acctcreditsaccount	= $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport($sesi['start_date'], $sesi['end_date'], $branch_id);
			$acctcredits 		= $this->AcctNominativeCreditsReport_model->getAcctCredits();
			$acctsourcefund 	= $this->AcctNominativeCreditsReport_model->getAcctSourceFund();

			// print_r($acctsavingsprofitsharing);exit;


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

			if($sesi['kelompok_laporan_pembiayaan'] == 0){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PEMBIAYAAN GLOBAL</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					    </tr>
					</table>";
			} else if($sesi['kelompok_laporan_pembiayaan'] == 1){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PEMBIAYAAN PER JENIS AKAD</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					    </tr>
					</table>";
			} else if($sesi['kelompok_laporan_pembiayaan'] == 2){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF PEMBIAYAAN PER JENIS SUMBER DANA</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					    </tr>
					</table>";
			}
			

			$pdf->writeHTML($tbl, true, false, false, false, '');
			
			if($sesi['kelompok_laporan_pembiayaan'] == 0){
				$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
				        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Plafon</div></td>
				        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Outstd</div></td>
				        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Pinjam</div></td>
				        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl JT Tempo</div></td>
				       
				    </tr>				
				</table>";

				$no = 1;

				$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
				$totalbasilglobal = 0;
				$totalsaldoglobal = 0;
			
				foreach ($acctcreditsaccount as $key => $val) {
					$month 	= date('m', strtotime($sesi['end_date']));
					$year	= date('Y', strtotime($sesi['end_date']));
					$period = $month.$year;

					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"11%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
					        <td width=\"16%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['credits_account_net_price'], 2)."</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_principal'], 2)."</div></td>
					        <td width=\"17%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_date'])."</div></td>
					         <td width=\"17%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_due_date'])."</div></td>
					    </tr>
					";

					$totalbasilglobal += $val['credits_account_net_price'];
					$totalsaldoglobal += $val['credits_account_last_balance_principal'];

					$no++;
				}

				$tbl4 = "
					<tr>
						<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctNominativeCreditsReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalbasilglobal, 2)."</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldoglobal, 2)."</div></td>
					</tr>
							
			</table>";
			} else if($sesi['kelompok_laporan_pembiayaan'] == 1){
				$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
				        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pokok</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Margin</div></td>
				        <td width=\"12%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>
				        <td width=\"12%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Margin</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Realisasi</div></td>
				       
				    </tr>				
				</table>";
				
				foreach ($acctcredits as $kCredits => $vCredits) {
					$acctcreditsaccount_credits = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_Credits($sesi['start_date'], $sesi['end_date'], $vCredits['credits_id'], $branch_id);
					
					if(!empty($acctcreditsaccount_credits)){
						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"8\" width=\"100%\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vCredits['credits_name']."</div></td>
							</tr>
							<br>
						";
						$nov = 1;
						$totalbasilperjenis = 0;
						$totalsaldoperjenis = 0;
						foreach ($acctcreditsaccount_credits as $k => $v) {
							$month 	= date('m', strtotime($sesi['end_date']));
							$year	= date('Y', strtotime($sesi['end_date']));
							$period = $month.$year;


							$tbl3 .= "
								<tr>
							    	<td width=\"3%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">".$v['credits_account_serial']."</div></td>
							        <td width=\"13%\"><div style=\"text-align: left;\">".$v['member_name']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: left;\">".$v['member_address']."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_net_price'], 2)."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_margin'], 2)."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_last_balance_principal'], 2)."</div></td>
					         		<td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_last_balance_margin'], 2)."</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($v['credits_account_date'])."</div></td>
							    </tr>

							";

							$totalpokok += $v['credits_account_net_price'];
							$totalmargin += $v['credits_account_margin'];
							$totalsisapokok += $v['credits_account_last_balance_principal'];
							$totalsisamargin += $v['credits_account_last_balance_margin'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokok, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmargin, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisapokok, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisamargin, 2)."</div></td>
							</tr>
							<br>
						";

						$totalpokokglobal += $totalpokok;
						$totalmarginglobal += $totalmargin;
						$totalsisapokokglobal += $totalsisapokok;
						$totalsisamarginglobal += $totalsisamargin;
					}
				}

				$tbl4 = "
						<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\"> </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">POKOK</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">MARGIN</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">SISA POKOK</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">SISA MARGIN</div></td>
							</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctNominativeCreditsReport_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokokglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmarginglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisapokokglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisamarginglobal, 2)."</div></td>
						</tr>
								
				</table>";
			}  else if($sesi['kelompok_laporan_pembiayaan'] == 2){
				$tbl1 = "
				<br>
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				    <tr>
				        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
				        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
				        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
				        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Pokok</div></td>
				        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Margin</div></td>
				        <td width=\"12%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Pokok</div></td>
				        <td width=\"12%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Sisa Margin</div></td>
				        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Tgl Realisasi</div></td>
				       
				    </tr>				
				</table>";
				
				foreach ($acctsourcefund as $kSF => $vSF) {
					$acctcreditsaccount_sourcefund = $this->AcctNominativeCreditsReport_model->getAcctNomintiveCreditsReport_SourceFund($sesi['start_date'], $sesi['end_date'], $vSF['source_fund_id'], $branch_id);
					
					if(!empty($acctcreditsaccount_sourcefund)){
						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"8\" width=\"100%\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vSF['source_fund_name']."</div></td>
							</tr>
							<br>
						";
						$nov = 1;
						$totalbasilperjenis = 0;
						$totalsaldoperjenis = 0;
						foreach ($acctcreditsaccount_sourcefund as $k => $v) {
							$month 	= date('m', strtotime($sesi['end_date']));
							$year	= date('Y', strtotime($sesi['end_date']));
							$period = $month.$year;


							$tbl3 .= "
								<tr>
							    	<td width=\"3%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">".$v['credits_account_serial']."</div></td>
							        <td width=\"13%\"><div style=\"text-align: left;\">".$v['member_name']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: left;\">".$v['member_address']."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_net_price'], 2)."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_margin'], 2)."</div></td>
							        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_last_balance_principal'], 2)."</div></td>
					         		<td width=\"12%\"><div style=\"text-align: right;\">".number_format($v['credits_account_last_balance_margin'], 2)."</div></td>
					         		<td width=\"10%\"><div style=\"text-align: right;\">".tgltoview($v['credits_account_date'])."</div></td>
							    </tr>

							";

							$totalpokok += $v['credits_account_net_price'];
							$totalmargin += $v['credits_account_margin'];
							$totalsisapokok += $v['credits_account_last_balance_principal'];
							$totalsisamargin += $v['credits_account_last_balance_margin'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokok, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmargin, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisapokok, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisamargin, 2)."</div></td>
							</tr>
							<br>
						";

						$totalpokokglobal += $totalpokok;
						$totalmarginglobal += $totalmargin;
						$totalsisapokokglobal += $totalsisapokok;
						$totalsisamarginglobal += $totalsisamargin;
					}
				}

				$tbl4 = "
						<tr>
								<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\"> </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">POKOK</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">MARGIN</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">SISA POKOK</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">SISA MARGIN</div></td>
							</tr>
						<tr>
							<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctNominativeCreditsReport_model->getUserName($auth['user_id'])."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokokglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmarginglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisapokokglobal, 2)."</div></td>
							<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsisamarginglobal, 2)."</div></td>
						</tr>
								
				</table>";
			}


			

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