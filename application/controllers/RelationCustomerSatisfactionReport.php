<?php ob_start(); ?>
<?php
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	Class RelationCustomerSatisfactionReport extends MY_Controller{
		public function __construct(){
			parent::__construct();
			
			$menu = 'customer-satisfaction-report';

			$this->cekLogin();
			$this->accessMenu($menu);

			$this->load->model('MainPage_model');
			$this->load->model('RelationCustomerSatisfactionReport_model');
			$this->load->model('Library_model');
			$this->load->helper('sistem');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->helper('url');
			$this->load->database('default');
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-RelationCustomerSatisfactionReport');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('d-m-Y');
				$sesi['end_date']		= date('d-m-Y');
				$sesi['branch_id']		= '';
			}
			
			$start_date 		= tgltodb($sesi['start_date']);
			$end_date 			= tgltodb($sesi['end_date']);

			$startdate1 		= strtotime('0 day', strtotime($start_date));
			$startdate 			= date("Y-m-d", $startdate1);
			$enddate1 			= strtotime('1 day', strtotime($end_date));
			$enddate 			= date("Y-m-d", $enddate1);

			$query_count 		= "";
			
			while ($startdate != $enddate){
				$day_status_yes		= date("d", strtotime($startdate))."_Y";
				$day_status_no		= date("d", strtotime($startdate))."_N";

				$query_count .= "(
					SELECT COUNT(customer_satisfaction_status) FROM relation_customer_satisfaction relation
						WHERE relation.branch_id = relation_customer_satisfaction.branch_id
						AND relation.customer_satisfaction_status = 0
						AND relation.customer_satisfaction_date = '".$startdate."'
						GROUP BY relation.branch_id, relation.customer_satisfaction_status
				) AS ".$day_status_no.",
				(
					SELECT COUNT(customer_satisfaction_status) FROM relation_customer_satisfaction relation
						WHERE relation.branch_id = relation_customer_satisfaction.branch_id
						AND relation.customer_satisfaction_status = 1
						AND relation.customer_satisfaction_date = '".$startdate."'
						GROUP BY relation.branch_id, relation.customer_satisfaction_status
				) AS ".$day_status_yes.", ";


				$startdate1			= strtotime('1 day', strtotime($startdate));
				$startdate 			= date("Y-m-d", $startdate1);
			}
				
			/*print_r("employee_shift_id ");
			print_r($employee_shift_id);
			exit;*/

			$query_count = substr(trim($query_count), 0, strlen($query_count) - 2);

			/* print_r("query_count ");
			print_r($query_count);
			print_r("<BR> ");
			print_r("<BR> "); */

			$data['main_view']['corebranch']		= create_double($this->RelationCustomerSatisfactionReport_model->getCoreBranch(), 'branch_id', 'branch_name');

			$relationcustomersatisfactionreport 	= $this->RelationCustomerSatisfactionReport_model->getRelationCustomerSatisfactionReport($sesi['branch_id'], $query_count);

			/* print_r("relationcustomersatisfactionreport ");
			print_r($relationcustomersatisfactionreport);
			print_r("<BR> ");
			print_r("<BR> ");
			exit; */
			
			$data['main_view']['relationcustomersatisfactionreport'] 	= $relationcustomersatisfactionreport;

			$data['main_view']['content']								= 'RelationCustomerSatisfactionReport/ListRelationCustomerSatisfactionReport_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function filter(){
			$data = array (
				'start_date'	=> $this->input->post('start_date',true),
				'end_date'		=> $this->input->post('end_date',true),
				'branch_id'		=> $this->input->post('branch_id',true),
			);
			
			$this->session->set_userdata('filter-RelationCustomerSatisfactionReport',$data);
			redirect('customer-satisfaction-report');
		}
		
		public function reset_search(){
			$sesi= $this->session->userdata('filter-RelationCustomerSatisfactionReport');
			$this->session->unset_userdata('filter-RelationCustomerSatisfactionReport');
			redirect('customer-satisfaction-report');
		}

		public function exportRelationCustomerSatisfactionReport(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-RelationCustomerSatisfactionReport');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('d-m-Y');
				$sesi['end_date']		= date('d-m-Y');
				$sesi['branch_id']		= '';
			}
			
			$start_date 		= tgltodb($sesi['start_date']);
			$end_date 			= tgltodb($sesi['end_date']);

			$startdate1 		= strtotime('0 day', strtotime($start_date));
			$startdate 			= date("Y-m-d", $startdate1);
			$enddate1 			= strtotime('1 day', strtotime($end_date));
			$enddate 			= date("Y-m-d", $enddate1);

			$query_count 		= "";
			
			while ($startdate != $enddate){
				$day_status_yes		= date("d", strtotime($startdate))."_Y";
				$day_status_no		= date("d", strtotime($startdate))."_N";

				$query_count .= "(
					SELECT COUNT(customer_satisfaction_status) FROM relation_customer_satisfaction relation
						WHERE relation.branch_id = relation_customer_satisfaction.branch_id
						AND relation.customer_satisfaction_status = 0
						AND relation.customer_satisfaction_date = '".$startdate."'
						GROUP BY relation.branch_id, relation.customer_satisfaction_status
				) AS ".$day_status_no.",
				(
					SELECT COUNT(customer_satisfaction_status) FROM relation_customer_satisfaction relation
						WHERE relation.branch_id = 1
						AND relation.customer_satisfaction_status = 1
						AND relation.customer_satisfaction_date = '".$startdate."'
						GROUP BY relation.branch_id, relation.customer_satisfaction_status
				) AS ".$day_status_yes.", ";


				$startdate1			= strtotime('1 day', strtotime($startdate));
				$startdate 			= date("Y-m-d", $startdate1);
			}
				
			/*print_r("employee_shift_id ");
			print_r($employee_shift_id);
			exit;*/

			$query_count = substr(trim($query_count), 0, strlen($query_count) - 2);

			/* print_r("query_count ");
			print_r($query_count);
			print_r("<BR> ");
			print_r("<BR> "); */

			$relationcustomersatisfactionreport 	= $this->RelationCustomerSatisfactionReport_model->getRelationCustomerSatisfactionReport($sesi['branch_id'], $query_count);

			$preferencecompany 						= $this->RelationCustomerSatisfactionReport_model->getPreferenceCompany();

			/*print_r("item");
			print_r($relationcustomersatisfactionreport);
			exit;*/
			
			if(!empty($relationcustomersatisfactionreport)){
				$spreadsheet = new Spreadsheet();

				$spreadsheet->getProperties()->setCreator("Puskesmas Purwosari")
					->setLastModifiedBy("Puskesmas Purwosari")
					->setTitle("Relation Customer Satisfaction Report")
					->setSubject("")
					->setDescription("Relation Customer Satisfaction Report")
					->setKeywords("Relation, Customer, Satisfaction, Report")
					->setCategory("Relation Customer Satisfaction Report");
									
				$spreadsheet->setActiveSheetIndex(0);
				$spreadsheet->setActiveSheetIndex(0);
				$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
				$spreadsheet->getActiveSheet()->mergeCells("B1:E1");
				$spreadsheet->getActiveSheet()->mergeCells("B2:E2");
				$spreadsheet->getActiveSheet()->mergeCells("B3:E3");
				$spreadsheet->getActiveSheet()->mergeCells("B4:E4");
				/* $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); */
				$spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(16);
				$spreadsheet->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(16);
				$spreadsheet->getActiveSheet()->getStyle('B4')->getFont()->setBold(true)->setSize(16);
				/* $spreadsheet->getActiveSheet()->getStyle('B3:K3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$spreadsheet->getActiveSheet()->getStyle('B3:K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); */
				$spreadsheet->getActiveSheet()->getStyle('B3:E3')->getFont()->setBold(true);	
				$spreadsheet->getActiveSheet()->setCellValue('B1', $preferencecompany['company_name']);	
				$spreadsheet->getActiveSheet()->setCellValue('B2',"Laporan Tingkat Kepuasan Pengunjung ");	
				$spreadsheet->getActiveSheet()->setCellValue('B3',"Periode ".tgltoview($start_date)." s/d ".tgltoview($end_date));	

				$spreadsheet->getActiveSheet()->setCellValue('B5',"No");

				$array_key 	= array_keys($relationcustomersatisfactionreport[0]);

				$count 		= count($array_key);

				$cell 		= 'C';

				for($i=0; $i<$count; $i++){
					$index_array = $array_key[$i];

					$spreadsheet->getActiveSheet()->setCellValue($cell.'5', $index_array);
					$cell++;
				}

				$spreadsheet->getActiveSheet()->setCellValue($cell.'5', 'Total Tidak Puas');
				$cell++;
				$spreadsheet->getActiveSheet()->setCellValue($cell.'5', 'Total Puas');
				$cell++;
				$spreadsheet->getActiveSheet()->setCellValue($cell.'5', 'Total Kunjungan');
				
				$count_satisfaction 	= count($relationcustomersatisfactionreport);

				$array_key 				= array_keys($relationcustomersatisfactionreport[0]);

				$count 					= count($array_key);

				/* print_r("count_satisfaction ");
				print_r($count_satisfaction);
				print_r("<BR> ");
				print_r("<BR> ");

				exit; */

				$j		= 6;
				$no		= 0;
				for ($k=0; $k<$count_satisfaction; $k++){
					$total_visit 		= 0;
					$total_visit_yes 	= 0;
					$total_visit_no 	= 0;
					$cell 				= 'C';
					$no++;
					$spreadsheet->setActiveSheetIndex(0);
					$spreadsheet->getActiveSheet()->setCellValue('B'.$j, $no);

					for($i=0; $i<$count; $i++){
						

						$index_array = $array_key[$i];
						
						$array_value = $relationcustomersatisfactionreport[$k][$index_array];

						if ($array_value == ''){
							$array_value = 0;
						}

						if ($i > 0){
							$total_visit += $array_value;
						}
				

						$status = substr($index_array, -1);

						/* print_r("status ");
						print_r($status);
						print_r("<BR> "); */

						if ($status == "Y"){
							$total_visit_yes += $array_value;
						}
						
						if ($status == "N"){
							$total_visit_no += $array_value;
						}

						$spreadsheet->getActiveSheet()->setCellValue($cell.$j, $array_value);
						$cell++;
					}
					
					$spreadsheet->getActiveSheet()->setCellValue($cell.$j, $total_visit_no);
					$cell++;
					$spreadsheet->getActiveSheet()->setCellValue($cell.$j, $total_visit_yes);
					$cell++;
					$spreadsheet->getActiveSheet()->setCellValue($cell.$j, $total_visit);
					$j++;
				}
				
				
				$writer = new Xlsx($spreadsheet);
				$filename='Laporan Tingkat Kepuasan Pengunjung '.$preferencecompany['company_name'].' Periode '.tgltoview($start_date).' s/d '.tgltoview($end_date);
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
				header('Cache-Control: max-age=0');
		
				$writer->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}

		












		

		
		
	}
?>