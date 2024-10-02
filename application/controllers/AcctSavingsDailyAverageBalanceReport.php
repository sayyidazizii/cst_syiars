<?php
	Class AcctSavingsDailyAverageBalanceReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDailyAverageBalanceReport_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctsavingsdailyaveragebalancereport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
				$sesi['savings_id']			= '';
			}

			if(empty($sesi['branch_id'])){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			if($sesi['branch_id'] ==0){
				$branch_id 	= 0;
			}


			$period 	= $sesi['month_period'].$sesi['year_period'];

			$list 		= $this->AcctSavingsDailyAverageBalanceReport_model->get_datatables($period, $branch_id, $sesi['savings_id']);
			
			$count_data = count($list);

			$rows 		= ceil($count_data / 500);



			$corebranch 									= create_double_branch($this->AcctSavingsDailyAverageBalanceReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);

			$acctsavings									= create_double_branch($this->AcctSavingsDailyAverageBalanceReport_model->getAcctSavings(), 'savings_id', 'savings_name');
			$acctsavings[0] 								= 'Semua Simpanan';
			ksort($acctsavings);

			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['acctsavings']				= $acctsavings;
			$data['main_view']['month']						= $this->configuration->Month();
			$data['main_view']['file']						= $rows;	
			$data['main_view']['content']					= 'AcctSavingsDailyAverageBalanceReport/ListAcctSavingsDailyAverageBalanceReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id"					=> $this->input->post('branch_id',true),
				"month_period"				=> $this->input->post('month_period',true),
				"year_period"				=> $this->input->post('year_period',true),
				"savings_id"				=> $this->input->post('savings_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsdailyaveragebalancereport',$data);
			redirect('AcctSavingsDailyAverageBalanceReport');
		}

		public function getSavingsDailyAverageBalance(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctsavingsdailyaveragebalancereport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
				$sesi['savings_id']			= '';
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

			$list = $this->AcctSavingsDailyAverageBalanceReport_model->get_datatables($period, $branch_id, $sesi['savings_id']);

	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $profitsharing) {

	            $no++;
	            $row = array();
	            $row[] 	= $no;
				$row[] 	= $profitsharing->savings_name;
	            $row[] 	= $profitsharing->savings_account_no;
	            $row[] 	= $profitsharing->member_name;
	            $row[] 	= $profitsharing->member_address;
	            $row[] 	= number_format($profitsharing->savings_daily_average_balance, 2);
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsDailyAverageBalanceReport_model->count_all($period, $branch_id, $sesi['savings_id']),
	                        "recordsFiltered" => $this->AcctSavingsDailyAverageBalanceReport_model->count_filtered($period, $branch_id, $sesi['savings_id']),
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
			$sesi = $this->session->userdata('filter-acctsavingsdailyaveragebalancereport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
				$sesi['savings_id']			= '';
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

			$list 	= $this->AcctSavingsDailyAverageBalanceReport_model->get_datatables($period, $branch_id, $sesi['savings_id']);

			foreach ($list as $profitsharing) {

				$no++;
	            $data[] = array(
	            	'no'								=> $no,
					'savings_name'						=> $profitsharing->savings_name,
	            	'savings_account_no'				=> $profitsharing->savings_account_no,
	            	'member_name' 						=> $profitsharing->member_name,
	            	'member_address'					=> $profitsharing->member_address,
	            	'savings_daily_average_balance'		=> $profitsharing->savings_daily_average_balance,
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
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('filter-acctsavingsdailyaveragebalancereport');
			if(!is_array($sesi)){
				$sesi['month_period']		= date('m');
				$sesi['year_period']		= date('Y');
				$sesi['savings_id']			= '';
			}

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$monthlist 		= $this->configuration->Month();


			$preference		= $this->AcctSavingsDailyAverageBalanceReport_model->getPreferenceCompany();


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
			        <td width=\"100%\"><div style=\"text-align: left; font-size:14px; font-weight:bold\">DAFTAR BAGI HASIL SIMPANAN ".strtoupper($monthlist[$sesi['month_period']])." ".$sesi['year_period']."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"5%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">No</div></td>
					<td width=\"12%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Nama Simpanan</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">No. Rekening</div></td>
			        <td width=\"22%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Nama</div></td>
			        <td width=\"28%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Alamat</div></td>
			    </tr>			
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($data as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">$no</div></td>
						<td width=\"12%\"><div style=\"text-align: left;\">".$val['savings_name']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
				        <td width=\"22%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"28%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				    </tr>
				";

				$total += $val['savings_profit_sharing_amount'];
				$total_pajak += $val['tax'];

				$no++;
			}
			
			
			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsDailyAverageBalanceReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Saldo Rata Rata Harian Periode '.$period.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsaccount-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsaccount-'.$unique['unique'],$sessions);
		}	
		
	}
?>