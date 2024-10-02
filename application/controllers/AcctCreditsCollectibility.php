<?php ini_set('memory_limit', '384M');
	ini_set('max_execution_time', 600);?>
<?php
	Class AcctCreditsCollectibility extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsCollectibility_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$date 	= date('Y-m-d');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditscellectibility');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}


			$preferencecollectibility = $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();

			$list = $this->AcctCreditsCollectibility_model->get_datatables($sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $agunan) {
				$date1 = date_create($date);
				$date2 = date_create($agunan->credits_account_last_payment_date);
				// $tunggakan = date_diff($date1, $date2)->format('%d');

				// $date1 = date_create($credits_payment_date);
				// $date2 = date_create($accountcredit['credits_account_last_payment_date']);

				// $date1 = date_create("2020-02-10");
				// $date2 = date_create("2020-02-08");

				if($date1 > $date2){
					$interval    = $date1->diff($date2);
					$tunggakan   = $interval->days;
				} else {
					$tunggakan 	= 0;
				}

				
				
				foreach ($preferencecollectibility as $k => $v) {
					if($tunggakan >= $v['collectibility_bottom'] && $tunggakan <= $v['collectibility_top']){
						$collectibility = $v['collectibility_id'];
					} 
				}

	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $agunan->credits_account_serial;
	            $row[] = $agunan->member_name;
	            $row[] = $agunan->member_address;
	            $row[] = number_format($agunan->credits_account_last_balance_principal, 2);
	            $row[] = number_format($agunan->credits_account_last_balance_margin, 2);
	            $row[] = $collectibility;
				$data[] = $row;
				
				if($collectibility == 1){
					$total1 = $total1 + $agunan->credits_account_last_balance_principal;
				} else if($collectibility == 2){
					$total2 = $total2 + $agunan->credits_account_last_balance_principal;
				} else if($collectibility == 3){
					$total3 = $total3 + $agunan->credits_account_last_balance_principal;
				} else if($collectibility == 4){
					$total4 = $total4 + $agunan->credits_account_last_balance_principal;
				}

				$totaloutstanding += $agunan->credits_account_last_balance_principal;
				$totalmargin += $agunan->credits_account_last_balance_margin;
			}

			$count_data = count($list);

			$rows 		= ceil($count_data / 1000);

			$datacolectibility	= array (
				'total1'				=> $total1,
				'total2'				=> $total2,
				'total3'				=> $total3,
				'total4'				=> $total4,
				'totaloutstanding'		=> $totaloutstanding,
				'totalmargin'			=> $totalmargin
			);
			
			$data['main_view']['file']						= $rows;
			$data['main_view']['datacolectibility']			= $datacolectibility;
			$data['main_view']['preferencecollectibility']	= $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();
			$data['main_view']['corebranch']				= create_double($this->AcctCreditsCollectibility_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']					= 'AcctCreditsCollectibility/ListAcctCreditsCollectibility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctcreditscellectibility',$data);
			redirect('AcctCreditsCollectibility');
		}

		public function getAcctCreditsCollectibilityList(){
			$auth = $this->session->userdata('auth');
			$date 	= date('Y-m-d');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditscellectibility');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}


			$preferencecollectibility = $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();

			$list = $this->AcctCreditsCollectibility_model->get_datatables($sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $agunan) {
				$date1 = date_create($date);
				$date2 = date_create($agunan->credits_account_last_payment_date);
				// $tunggakan = date_diff($date1, $date2)->format('%d');

				// $date1 = date_create($credits_payment_date);
				// $date2 = date_create($accountcredit['credits_account_last_payment_date']);

				// $date1 = date_create("2020-02-10");
				// $date2 = date_create("2020-02-08");

				if($date1 > $date2){
					$interval    = $date1->diff($date2);
					$tunggakan   = $interval->days;
				} else {
					$tunggakan 	= 0;
				}
				
				
				foreach ($preferencecollectibility as $k => $v) {
					if($tunggakan >= $v['collectibility_bottom'] && $tunggakan <= $v['collectibility_top']){
						$collectibility = $v['collectibility_id'];
					} 
				}

	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $agunan->credits_account_serial;
	            $row[] = $agunan->member_name;
	            $row[] = $agunan->member_address;
	            $row[] = number_format($agunan->credits_account_last_balance_principal, 2);
	            $row[] = number_format($agunan->credits_account_last_balance_margin, 2);
	            $row[] = $collectibility;
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditsCollectibility_model->count_all($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditsCollectibility_model->count_filtered($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function view(){
			$auth 	= $this->session->userdata('auth');
			$baris 	= $this->uri->segment(3);
			$key 	= $this->uri->segment(4);

			/*$sisa = 5052 % 500;
			print_r($sisa);exit;*/

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditscellectibility');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$date 	= date('Y-m-d');

			$period = date('mY', strtotime($date));

			$preferencecollectibility = $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();

			$list = $this->AcctCreditsCollectibility_model->get_datatables($sesi['branch_id']);
	        foreach ($list as $agunan) {
				$date1 = date_create($date);
				$date2 = date_create($agunan->credits_account_last_payment_date);
				// $tunggakan = date_diff($date1, $date2)->format('%d');

				// $date1 = date_create($credits_payment_date);
				// $date2 = date_create($accountcredit['credits_account_last_payment_date']);

				// $date1 = date_create("2020-02-10");
				// $date2 = date_create("2020-02-08");

				if($date1 > $date2){
					$interval    = $date1->diff($date2);
					$tunggakan   = $interval->days;
				} else {
					$tunggakan 	= 0;
				}
				
				
				foreach ($preferencecollectibility as $k => $v) {
					if($tunggakan >= $v['collectibility_bottom'] && $tunggakan <= $v['collectibility_top']){
						$collectibility = $v['collectibility_id'];
					} 
				}

				$no++;
				$data[] = array(
	            	'no'										=> $no,
	            	'credits_account_serial'					=> $agunan->credits_account_serial,
	            	'member_name'								=> $agunan->member_name,
	            	'member_address'							=> $agunan->member_address,
	            	'credits_account_last_balance_principal'	=> $agunan->credits_account_last_balance_principal,
	            	'credits_account_last_balance_margin'		=> $agunan->credits_account_last_balance_margin,
	            	'collectibility'							=> $collectibility,
	            );
	        }

			$sisa = $no % 1000;

			/*print_r($sisa);exit;*/

			for ($i=0; $i < $baris ; $i++) {
				
				if($i == $baris){
					$rows = $sisa;
				} else {
					$rows = 1000;
				}

				$array_terpecah[$i] = array_splice($data, 0, $rows);

				
			}

			$datacetak = $array_terpecah[$key];

			// print_r($data);exit;

			$this->export($datacetak);
		}

		public function export($data){
			$auth = $this->session->userdata('auth'); 	

			$credits = count($data);

			//print_r($data);exit;

			if(count($credits) >= 0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									 ->setLastModifiedBy("SIS")
									 ->setTitle("KOLEKTIBILITAS PEMBIAYAAN")
									 ->setSubject("")
									 ->setDescription("KOLEKTIBILITAS PEMBIAYAAN")
									 ->setKeywords("KOLEKTIBILITAS, PEMBIAYAAN")
									 ->setCategory("KOLEKTIBILITAS PEMBIAYAAN");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:H1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"KOLEKTIBILITAS PEMBIAYAAN");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No Akad");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Outstanding");
				$this->excel->getActiveSheet()->setCellValue('G3',"Sisa Margin");
				$this->excel->getActiveSheet()->setCellValue('H3',"Kolektibilitas");
				
				$j=4;
				$no=0;
				
				foreach($data as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'] ,PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['credits_account_last_balance_principal']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['credits_account_last_balance_margin']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['collectibility']);	
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='KOLEKTIBILITAS PEMBIAYAAN.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}

		public function processPrinting(){
			$auth 	= $this->session->userdata('auth'); 
			$date 	= date('Y-m-d');


			$preferencecollectibility = $this->AcctCreditsCollectibility_model->getPreferenceCollectibility();
			$acctcreditsaccount	= $this->AcctCreditsCollectibility_model->getCreditsAccount();


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
				        <td><div style=\"text-align: center; font-size:14px\">KOLEKTIBILITAS PEMBIAYAAN</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Outstanding</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Sisa Margin</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Kolektibilitas</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			// $tunggakan = 30;
		
			foreach ($acctcreditsaccount as $key => $val) {
				$date1 = date_create($date);
				$date2 = date_create($val['credits_account_last_payment_date']);
				$tunggakan = date_diff($date1, $date2)->format('%d');

				
				
				foreach ($preferencecollectibility as $k => $v) {
					if($tunggakan >= $v['collectibility_bottom'] && $tunggakan <= $v['collectibility_top']){
						$collectibility = $v['collectibility_id'];
					} 
				}

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"16%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_principal'], 2)."</div></td>
				        <td width=\"16%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_margin'], 2)."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: center;\">".$collectibility."</div></td>

				    </tr>
				";

				

				

				$no++;
				// $tunggakan = $tunggakan +10;

				if($collectibility == 1){
					$total1 = $total1 + $val['credits_account_last_balance_principal'];
				} else if($collectibility == 2){
					$total2 = $total2 + $val['credits_account_last_balance_principal'];
				} else if($collectibility == 3){
					$total3 = $total3 + $val['credits_account_last_balance_principal'];
				} else if($collectibility == 4){
					$total4 = $total4 + $val['credits_account_last_balance_principal'];
				}

				$totaloutstanding += $val['credits_account_last_balance_principal'];
				$totalmargin += $val['credits_account_last_balance_margin'];

			}


			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsCollectibility_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaloutstanding, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalmargin, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\"></div></td>
				</tr>
							
			</table>";
			
			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			$tbl5 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td width=\"15%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">REKAPITULASI :</div></td>
				        <td width=\"20%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\"></div></td>
				        <td width=\"20%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\"></div></td>
				    </tr>
				";

			foreach ($preferencecollectibility as $k => $v) {
				if($v['collectibility_id'] == 1){
					$persent1 = ($total1 / $totaloutstanding) * 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total1, 2)." ( ".number_format($persent1, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 2){
					$persent2 = ($total2 / $totaloutstanding) * 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total2, 2)." ( ".number_format($persent2, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 3){
					$persent3 = ($total3 / $totaloutstanding) * 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total3, 2)." ( ".number_format($persent3, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					    </tr>
					";
				} else if($v['collectibility_id'] == 4){
					$persent4 = ($total4 / $totaloutstanding) * 100;
					$tbl6 .= "
						<tr>
					        <td width=\"15%\"><div style=\"text-align: left; font-size:12px;\">JUMLAH KOLEKT ".$v['collectibility_id']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right; font-size:12px;\"> ".number_format($total4, 2)." ( ".number_format($persent4, 2)." ) % &nbsp;&nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: left; font-size:12px;\">".$v['collectibility_name']."</div></td>
					    </tr>
					";
				}
				
			}

			$tbl7 = "
				</table>
			";

			$pdf->writeHTML($tbl5.$tbl6.$tbl7, true, false, false, false, '');


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