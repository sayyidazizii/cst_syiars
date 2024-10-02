<?php ob_start(); ?>
<?php
	ini_set('memory_limit', '512M');
	ini_set('max_execution_time', 600);
	Class AcctNominativeSavingsReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeSavingsReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctnominativesavingsreport');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['savings_id'] 	= '';
			}

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$list 		= $this->AcctNominativeSavingsReport_model->get_datatables($sesi['savings_id'], $branch_id);
			
			$count_data = count($list);

			$rows 		= ceil($count_data / 500);

			/*print_r($list);exit;*/

			$corebranch 									= create_double_branch($this->AcctNominativeSavingsReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['acctsavings']				= create_double($this->AcctNominativeSavingsReport_model->getAcctSavings(),'savings_id','savings_name');
			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanSimpanan();

			$data['main_view']['file']						= $rows;

			/*$data['main_view']['acctnominativesavings'] 	= $array_terpecah;*/


			$data['main_view']['content']					= 'AcctNominativeSavingsReport/ListAcctNominativeSavingsReportNew_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"branch_id"					=> $this->input->post('branch_id',true),
				"savings_id"				=> $this->input->post('savings_id',true),
			);

			$this->session->set_userdata('filter-acctnominativesavingsreport',$data);
			redirect('AcctNominativeSavingsReport');
		}

		public function getListAcctNominativeSavingsReport(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctnominativesavingsreport');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['savings_id'] 	= '';
			}

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$period 					= date('mY', strtotime($sesi['start_date']));

			$list = $this->AcctNominativeSavingsReport_model->get_datatables($sesi['savings_id'], $branch_id);

	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	        	$acctsavingsprofitsharing 	 		= $this->AcctNominativeSavingsReport_model->getAcctSavingsProfitSharing($savingsaccount->savings_account_id, $period);

	        	if(empty($acctsavingsprofitsharing)){
					$savings_daily_average_balance 	= 0;
					$savings_profit_sharing_amount 	= 0;
					$savings_account_last_balance 	= $savingsaccount->savings_account_last_balance;
				} else {
					$savings_daily_average_balance 	= $acctsavingsprofitsharing['savings_daily_average_balance'];
					$savings_profit_sharing_amount 	= $acctsavingsprofitsharing['savings_profit_sharing_amount'];
					$savings_account_last_balance 	= $acctsavingsprofitsharing['savings_account_last_balance'];
				}

	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = $savingsaccount->savings_name;
	            $row[] = number_format($savings_daily_average_balance, 2);
	            $row[] = number_format($savings_profit_sharing_amount, 2);
	            $row[] = number_format($savings_account_last_balance, 2);
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctNominativeSavingsReport_model->count_all($sesi['savings_id'], $branch_id),
	                        "recordsFiltered" => $this->AcctNominativeSavingsReport_model->count_filtered($sesi['savings_id'], $branch_id),
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
			$sesi	= $this->session->userdata('filter-acctnominativesavingsreport');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['savings_id'] 	= '';
			}

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$period = date('mY', strtotime($sesi['start_date']));

			$list 	= $this->AcctNominativeSavingsReport_model->get_datatables($sesi['savings_id'], $branch_id);

			foreach ($list as $savingsaccount) {
	        	$acctsavingsprofitsharing 	 		= $this->AcctNominativeSavingsReport_model->getAcctSavingsProfitSharing($savingsaccount->savings_account_id, $period);

	        	if(empty($acctsavingsprofitsharing)){
					$savings_daily_average_balance 	= 0;
					$savings_profit_sharing_amount 	= 0;
					$savings_account_last_balance 	= $savingsaccount->savings_account_last_balance;
				} else {
					$savings_daily_average_balance 	= $acctsavingsprofitsharing['savings_daily_average_balance'];
					$savings_profit_sharing_amount 	= $acctsavingsprofitsharing['savings_profit_sharing_amount'];
					$savings_account_last_balance 	= $acctsavingsprofitsharing['savings_account_last_balance'];
				}

				$no++;
	            $data[] = array(
	            	'no'								=> $no,
	            	'savings_account_no'				=> $savingsaccount->savings_account_no,
	            	'member_name' 						=> $savingsaccount->member_name,
	            	'member_address'					=> $savingsaccount->member_address,
	            	'savings_name'						=> $savingsaccount->savings_name,
	            	'savings_daily_average_balance'		=> $savings_daily_average_balance,
	            	'savings_profit_sharing_amount'		=> $savings_profit_sharing_amount,
	            	'savings_account_last_balance'		=> $savings_account_last_balance,
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

// 		public function export($data){
// 			$auth = $this->session->userdata('auth');
// 			$sesi	= $this->session->userdata('filter-acctnominativesavingsreport');

// 			if(!is_array($sesi)){
// 				$sesi['start_date']		= date('Y-m-d');
// 				$sesi['savings_id'] 	= '';
// 			}

// 			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
// 				$branch_id 	= $auth['branch_id'];
// 			} else {
// 				$branch_id 	= $sesi['branch_id'];
// 			}

// 			$savings_name 	= $this->AcctNominativeSavingsReport_model->getSavingsName($sesi['savings_id']);

// 			$jumlah = count($data);

			
// 			if($jumlah!=0){
// 				$this->load->library('Excel');
				
// 				$this->excel->getProperties()->setCreator("SIS")
// 									 ->setLastModifiedBy("SIS")
// 									 ->setTitle("Data Nominatif Simpanan")
// 									 ->setSubject("")
// 									 ->setDescription("Data Nominatif Simpanan")
// 									 ->setKeywords("Data, Nominatif, Simpanan")
// 									 ->setCategory("Data Nominatif Simpanan");
									 
// 				$this->excel->setActiveSheetIndex(0);
// 				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
// 				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
// 				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
// 				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
// 				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
// 				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
// 				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
// 				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
// 				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);	
// 				/*$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);	
// 				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);	
// 				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);	
// 				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
// 				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
// 				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);	
// 				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);	
// 				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);*/	

				
// 				$this->excel->getActiveSheet()->mergeCells("B1:H1");
// 				$this->excel->getActiveSheet()->mergeCells("B2:H2");
// 				$this->excel->getActiveSheet()->mergeCells("B3:H3");
// 				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// 				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
// 				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(14);
// 				$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(14);
// 				$this->excel->getActiveSheet()->getStyle('B5:H5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 				$this->excel->getActiveSheet()->getStyle('B5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// 				$this->excel->getActiveSheet()->getStyle('B5:H5')->getFont()->setBold(true);	
// 				$this->excel->getActiveSheet()->setCellValue('B1',"Data Nominatif Simpanan");
// 				$this->excel->getActiveSheet()->setCellValue('B2',"Cabang ".$this->AcctNominativeSavingsReport_model->getBranchName($branch_id));
// 				$this->excel->getActiveSheet()->setCellValue('B3',"Periode ".tgltoview($sesi['start_date']));

					
				
// 				$this->excel->getActiveSheet()->setCellValue('B3',"No");
// 				$this->excel->getActiveSheet()->setCellValue('C3',"No Rekening");
// 				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
// 				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
// 				$this->excel->getActiveSheet()->setCellValue('F3',"SRH");
// 				$this->excel->getActiveSheet()->setCellValue('G3',"Bagi Hasil");
// 				$this->excel->getActiveSheet()->setCellValue('H3',"Saldo");
// 				/*$this->excel->getActiveSheet()->setCellValue('I3',"Sifat");
// 				$this->excel->getActiveSheet()->setCellValue('J3',"No. Telp");
// 				$this->excel->getActiveSheet()->setCellValue('K3',"Pekerjaan");
// 				$this->excel->getActiveSheet()->setCellValue('L3',"Identitas");
// 				$this->excel->getActiveSheet()->setCellValue('M3',"No. Identitas");
// 				$this->excel->getActiveSheet()->setCellValue('N3',"Simpanan Pokok");
// 				$this->excel->getActiveSheet()->setCellValue('O3',"Simpanan Khusus");
// 				$this->excel->getActiveSheet()->setCellValue('P3',"Simpanan Wajib");*/
				
// 				$j=6;
// 				$no=0;
				
// 				foreach($data as $key=>$val){
// 					if(is_numeric($key)){
// 						$no++;
// 						$this->excel->setActiveSheetIndex(0);
// 						$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// 						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// 						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// 						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// 						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// 						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						/*$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// 						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
// */

// 						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
// 						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['savings_account_no']);
// 						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
// 						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
// 						$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['savings_daily_average_balance'], 2));
// 						$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['savings_profit_sharing_amount'], 2));
// 						$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['savings_account_last_balance'], 2));	
			
						
// 					}else{
// 						continue;
// 					}
// 					$j++;
// 				}
// 				$filename='Nominatif Simpanan.xls';
// 				header('Content-Type: application/vnd.ms-excel');
// 				header('Content-Disposition: attachment;filename="'.$filename.'"');
// 				header('Cache-Control: max-age=0');
							 
// 				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
// 				ob_end_clean();
// 				$objWriter->save('php://output');
// 			}else{
// 				echo "Maaf data yang di eksport tidak ada !";
// 			}
// 		}

		public function processPrinting($data){
			$auth 	= $this->session->userdata('auth'); 
			$sesi	= $this->session->userdata('filter-acctnominativesavingsreport');

			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['savings_id'] 	= '';
			}

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == 0){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$savings_name 	= $this->AcctNominativeSavingsReport_model->getSavingsName($sesi['savings_id']);

			/*print_r($savings_name);exit;
			*/

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
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NOMINATIF SIMPANAN ".strtoupper($savings_name)."</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">".$this->AcctNominativeSavingsReport_model->getBranchName($branch_id)."</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
				    </tr>
				</table>";
		

			$pdf->writeHTML($tbl, true, false, false, false, '');
			
			/*print_r($data);exit;*/
			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SRH</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Basil</div></td>
			        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Saldo</div></td>
			       
			    </tr>				
			</table>";

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

						$nov = 1;
						$totalbasilperjenis = 0;
						$totalsaldoperjenis = 0;

						
						foreach ($data as $key => $val) {
							

							$tbl3 .= "
								<tr>
							    	<td width=\"5%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"11%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
							        <td width=\"16%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
							        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
							        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['savings_daily_average_balance'], 2)."</div></td>
							        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['savings_profit_sharing_amount'], 2)."</div></td>
							        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['savings_account_last_balance'], 2)."</div></td>
							    </tr>

							";

							$totalbasilperjenis += $val['savings_profit_sharing_amount'];
							$totalsaldoperjenis += $val['savings_account_last_balance'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"4\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalbasilperjenis, 2)."</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldoperjenis, 2)."</div></td>
							</tr>
							</table>
							<br>
						";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Nominatif'.$savings_name.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


	}
?>