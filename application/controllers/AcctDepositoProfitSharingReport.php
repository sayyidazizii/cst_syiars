<?php
	Class AcctDepositoProfitSharingReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoProfitSharingReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctdepositoprofitsharingreport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
			}

			if(empty($sesi['branch_id'])){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			if($sesi['branch_id'] ==0){
				$branch_id 	= 0;
			}

			if($sesi['month_period'] < 10){
				$sesi['month_period'] = substr($sesi['month_period'], 1);
			} else {
				$sesi['month_period'] = $sesi['month_period'];
			}

			$period 	= $sesi['month_period'].$sesi['year_period'];

			/*print_r($sesi);exit;*/

			$list 		= $this->AcctDepositoProfitSharingReport_model->get_datatables($period, $branch_id);
			
			$count_data = count($list);

			$rows 		= ceil($count_data / 500);



			$corebranch 									= create_double_branch($this->AcctDepositoProfitSharingReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['month']						= $this->configuration->Month();
			$data['main_view']['file']						= $rows;	
			$data['main_view']['content'] = 'AcctDepositoProfitSharingReport/FormFilterAcctDepositoProfitSharingReport_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filter(){
			$data = array (
				"branch_id"					=> $this->input->post('branch_id',true),
				"month_period"				=> $this->input->post('month_period',true),
				"year_period"				=> $this->input->post('year_period',true),
			);

			$this->session->set_userdata('filter-acctdepositoprofitsharingreport',$data);
			redirect('AcctDepositoProfitSharingReport');
		}

		public function getDepositoProfitSharing(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctdepositoprofitsharingreport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
			}

			if(empty($sesi['branch_id'])){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			if($sesi['branch_id'] ==0){
				$branch_id 	= 0;
			}

			$period 		= $sesi['month_period'].$sesi['year_period'];

			$list = $this->AcctDepositoProfitSharingReport_model->get_datatables($period, $branch_id);

	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $profitsharing) {
	        	if($profitsharing->deposito_profit_sharing_amount >= 240000){
	        		$pajak = ($profitsharing->deposito_profit_sharing_amount * 10) / 100;
	        	} else {
	        		$pajak = 0;
	        	}

	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $profitsharing->deposito_account_no;
	            $row[] = $profitsharing->member_name;
	            $row[] = $profitsharing->member_address;
	            $row[] = number_format($profitsharing->deposito_daily_average_balance, 2);
	            $row[] = number_format($profitsharing->deposito_profit_sharing_amount, 2);
	            $row[] = number_format($pajak, 2);
	            $row[] = $profitsharing->savings_account_no;
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctDepositoProfitSharingReport_model->count_all($period, $branch_id),
	                        "recordsFiltered" => $this->AcctDepositoProfitSharingReport_model->count_filtered($period, $branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function view(){
			$baris 	= $this->uri->segment(3);
			$key 	= $this->uri->segment(4);

			/*$sisa = 5052 % 500;
			print_r($sisa);exit;*/

			$auth 	= $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctdepositoprofitsharingreport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
			}

			if(empty($sesi['branch_id'])){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			if($sesi['branch_id'] ==0){
				$branch_id 	= 0;
			}

			$period 		= $sesi['month_period'].$sesi['year_period'];

			$list 	= $this->AcctDepositoProfitSharingReport_model->get_datatables($period, $branch_id);

			foreach ($list as $profitsharing) {
	        	if($profitsharing->deposito_profit_sharing_amount >= 240000){
	        		$pajak = ($profitsharing->deposito_profit_sharing_amount * 10) / 100;
	        	} else {
	        		$pajak = 0;
	        	}

				$no++;
	            $data[] = array(
	            	'no'								=> $no,
	            	'deposito_account_no'				=> $profitsharing->deposito_account_no,
	            	'member_name' 						=> $profitsharing->member_name,
	            	'member_address'					=> $profitsharing->member_address,
	            	'deposito_daily_average_balance'	=> $profitsharing->deposito_daily_average_balance,
	            	'deposito_profit_sharing_amount'	=> $profitsharing->deposito_profit_sharing_amount,
	            	'tax'								=> $pajak,
	            	'savings_account_no'				=> $profitsharing->savings_account_no,
	            );
			}

			$sisa = $no % 500;

			/*print_r($sisa);exit;*/

			for ($i=0; $i < $baris ; $i++) {
				
				if($i == $baris){
					$rows = $sisa;
				} else {
					$rows = 500;
				}

				$array_terpecah[$i] = array_splice($data, 0, $rows);

				
			}

			$datacetak = $array_terpecah[$key];

			/*print_r($datacetak);exit;*/

			$this->processPrinting($datacetak);
		}

		public function processPrinting($data){
			$auth 	= $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctdepositoprofitsharingreport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
			}

			if(empty($sesi['branch_id'])){
				$branch_id 	= $auth['branch_id'];
			} else if($sesi['branch_id'] ==0){
				$branch_id 	= 0;
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$month 						= $this->configuration->Month();
			$preference					= $this->AcctDepositoProfitSharingReport_model->getPreferenceCompany();


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

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------
			

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;\">DAFTAR BAGI HASIl SIMPANAN BERJANGKA BULAN ".strtoupper($month[$sesi['month_period']])." ".$sesi['year_period']."</div></td>			       
			    </tr>						
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. SimpKa</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Saldo</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">BG Hasil</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Pajak</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Transfer</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($data as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['deposito_daily_average_balance'], 2)."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['deposito_profit_sharing_amount'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['tax'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".$val['savings_account_no']."</div></td>
				    </tr>
				";

				$totalnominal 	+= $val['deposito_profit_sharing_amount'];
				$totalpajak 	+= $val['tax'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoProfitSharingReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalpajak, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Daftar Basil Simpanan Berjangka Periode '.$month[$sesi['month_period']].' '.$sesi['year_period'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function addCashWithdrawal(){
			$data['main_view']['content'] = 'AcctDepositoProfitSharingReport/FormAddAcctSavingsCashWithdrawal_view';
			$this->load->view('MainPage_view', $data);
		}

		public function processPrintingCashWithdrawal(){
			$auth 	=	$this->session->userdata('auth'); 

			$start_date	= tgltodb($this->input->post('start_date', true));
			$end_date	= tgltodb($this->input->post('end_date', true));


			
			$preference	= $this->AcctDepositoProfitSharingReport_model->getPreferenceCompany();

			$acctsavingscashwithdrawal	= $this->AcctDepositoProfitSharingReport_model->getAcctSavings_CashWithdrawal($start_date, $end_date, $preference['cash_withdrawal_id']);


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
			        <td><div style=\"text-align: left;font-size:12;\">BMT INDONESIA</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI PENARIKAN TUNAI TGL : &nbsp;&nbsp; ".tgltoview($start_date)."&nbsp;&nbsp; S.D &nbsp;&nbsp;".tgltoview($end_date)."</div></td>		
			       	       
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

			foreach ($acctsavingscashwithdrawal as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_cash_mutation_date'])."</div></td>
				        <td width=\"16%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
				        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctDepositoProfitSharingReport_model->getMutationCode($preference['cash_withdrawal_id'])."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_amount'], 2)."</div></td>
				        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_last_balance'], 2)."</div></td>
				    </tr>
				";

				$totalnominal 	+= $val['savings_cash_mutation_amount'];
				$totalsaldo 	+= $val['savings_cash_mutation_last_balance'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoProfitSharingReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
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