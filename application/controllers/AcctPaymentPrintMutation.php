<?php
	Class AcctPaymentPrintMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctPaymentPrintMutation_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function indPayment(){
			$unique = $this->session->userdata('unique');
			$sesi	= $this->session->userdata('filter-acctpaymentmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_account_id'] 	= '';
			}

			$dataangsuran = $this->session->unset_userdata('dataangsuran-'.$unique['unique']);
			// print_r($sesi);exit;

			$credits_account_id = $this->uri->segment(3);
			if($credits_account_id == ''){
				$credits_account_id = $sesi['credits_account_id'];
			}

			$data['main_view']['acctcreditsaccount']	= $this->AcctPaymentPrintMutation_model->getAcctCreditAccountDetail($credits_account_id);

			$data['main_view']['acctcreditspayment']	= $this->AcctPaymentPrintMutation_model->getAcctCreditPaymentDetail($credits_account_id, $sesi['start_date'], $sesi['end_date']);		

			$data['main_view']['content']				= 'AcctPaymentPrintMutation/ListAcctPaymentPrintMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date"			=> tgltodb($this->input->post('start_date',true)),
				"end_date"				=> tgltodb($this->input->post('end_date',true)),
				"credits_account_id"	=> $this->input->post('credits_account_id', true),
			);

			$this->session->set_userdata('filter-acctpaymentmonitor',$data);
			redirect('AcctPaymentPrintMutation/indPayment');
		}

		public function getListAcctCreditAccount(){
			$auth = $this->session->userdata('auth');
			$branch_id = '';
			$list = $this->AcctCreditAccount_model->get_datatables_all($branch_id);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->credits_account_serial;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'AcctPaymentPrintMutation/indPayment/'.$savingsaccount->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all_all($branch_id),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered_all($branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}


		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-acctpaymentmonitor');
			redirect('AcctPaymentPrintMutation');
		}

		public function processPrinting(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctpaymentmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_account_id'] 	= '';
			}

			$dataangsuran = $this->session->userdata('dataangsuran-'.$unique['unique']);

			$status 						= $this->uri->segment(3);
			$credits_account_id 			= $this->uri->segment(4);
			$credits_account_last_number 	= $this->uri->segment(5);

			if(empty($dataangsuran)){
				$acctcreditspayment		= $this->AcctPaymentPrintMutation_model->getAcctCreditPaymentDetail($credits_account_id, $sesi['start_date'], $sesi['end_date']);

				if(empty($credits_account_last_number) || $credits_account_last_number == 0){
					$no = 1;
				} else {
					$no = $credits_account_last_number + 1;
				}

				foreach ($acctcreditspayment as $key => $val) {
					if($no == 31 ){
						$no = 1;
						// $this->processPrinting();
					} else {
						$no = $no;
					}


					$data[] = array (
						'no'						=> $no,
						'credits_payment_id' 		=> $val['credits_payment_id'],
						'credits_account_id'		=> $val['credits_account_id'],
						'transaction_date'			=> $val['credits_payment_date'],
						'transaction_code'			=> '',
						'transaction_in'			=> '',
						'transaction_out'			=> $val['credits_payment_amount'],
						'last_balance'				=> $val['credits_principal_last_balance'] + $val['credits_margin_last_balance'],
						'operated_name'				=> $val['operated_name'],	
					);

					
					$no++;
					
				}

				$this->session->set_userdata('dataangsuran-'.$unique['unique'], $data);
			}
			
			$dataangsuran = $this->session->userdata('dataangsuran-'.$unique['unique']);

			if($status == 'print'){
				foreach ($dataangsuran as $k => $v) {
					$update_data = array(
						'credits_payment_id'			=> $v['credits_payment_id'],
						'credits_account_id'			=> $v['credits_account_id'],
						'credits_print_status'			=> 1,
						'credits_account_last_number'	=> $v['no'],
					);

					$this->AcctPaymentPrintMutation_model->updatePrintMutationStatus($update_data);
				}
			}

			// print_r($data);
			// 	print_r("<BR>");
			// 	print_r("<BR>");

			// exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

			$pdf->SetMargins(5, 26, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/**/

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

			$resolution= array(180, 170);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------

			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			if($credits_account_last_number > 1){
				for ($i=1; $i <= $credits_account_last_number ; $i++) { 
					if($i == 13){
						$tbl1 .= "
						<tr>
					    	<td></td>
					    </tr>
					    <tr>
					    	<td></td>
					    </tr>
					    <tr>
					    	<td></td>
					    </tr>
					    ";

					} else {
						$tbl1 .= "
						<tr>
					    	<td></td>
					    </tr>";
					}
				}
			}

			for ($i=0; $i < 15; $i++) { 
							$tblkosong .= "
								<tr>
									<td></td>
								</tr>
							";
						}
			foreach ($dataangsuran as $key => $val) {
				
					
						
					if($val['no'] == 1){
						$tbl1a .= "
								<tr>
							    	<td width=\"3%\"><div style=\"text-align: center;\">No</div></td>
							    	<td width=\"10%\"><div style=\"text-align: center;\">Tanggal</div></td>
							    	<td width=\"7%\"><div style=\"text-align: center;\">Sandi</div></td>
							    	<td width=\"14%\"><div style=\"text-align: center;\"></div></td>
							    	<td width=\"13%\"><div style=\"text-align: center;\">Angsuran</div></td>
							    	<td width=\"25%\"><div style=\"text-align: center;\">Saldo</div></td>
							    	<td width=\"5%\"><div style=\"text-align: center;\">Opt</div></td>
							    </tr>";
					} else {
						$tbl1a .= "
							<tr>
								<td></td>
							</tr>
						";
					}
						
					
					$tbl1 .= "
						<tr>
					    	<td width=\"3%\"><div style=\"text-align: left;\">".$val['no'].".</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".date('d-m-y',strtotime(($val['transaction_date'])))."</div></td>
					        <td width=\"7%\"><div style=\"text-align: center;\">".$val['transaction_code']."</div></td>
					        <td width=\"14%\"><div style=\"text-align: right;\">".$val['transaction_in']." &nbsp;</div></td>
					        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['transaction_out'], 2)." &nbsp;</div></td>
					        <td width=\"25%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp;</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".substr($val['operated_name'],0,3)."</div></td>
					    </tr>
					";

					if($val['no'] == 13){
						$tbl1 .= "
							<tr>
						    	<td></td>
						    </tr>
						    <tr>
					    	<td></td>
					    </tr>
						";
					}

					if($val['no'] == 26){
						$tbl1 .= "
							<tr>
						    	<td></td>
						    </tr>

						";
					}
				
			}

			$tbl2 = "</table>";

			$pdf->writeHTML($page.$tbl.$tblkosong.$tbl1a.$tbl1.$tbl2, true, false, false, false, '');

			// if($last_no == 30){
			// 	$this->processPrinting();
			// } 

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak Mutasi.pdf';

			if($status == 'preview'){

				$pdf->Output($filename, 'I');

			} else if($status == 'print'){

				// force print dialog
				$js .= 'print(true);';

				// set javascript
				$pdf->IncludeJS($js);

				$pdf->Output($filename, 'I');

			}
			

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