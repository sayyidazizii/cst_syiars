<?php
	Class AcctCreditsAccountMasterData extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAccountMasterData_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsAccountMasterData_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsAccountMasterData/ListAcctCreditsAccountMasterData_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> $this->input->post('start_date',true),
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-masterdatacreditsaccount',$data);
			redirect('AcctCreditsAccountMasterData');
		}

		public function getAcctCreditsAccountMasterDataList(){
			$auth = $this->session->userdata('auth');

			$sesi = $this->session->userdata('filter-masterdatacreditsaccount');
			if(!is_array($sesi)){
				$sesi['start_date']			= date('Y-m-d');
				if($auth['branch_status'] == 1){
					$sesi['branch_id']			= '';
				} else {
					$sesi['branch_id']			= $auth['branch_id'];
				}
			}

			if($auth['branch_status'] == 1){
				$sesi['branch_id']			= '';
			} else {
				$sesi['branch_id']			= $auth['branch_id'];
			}

			/* print_r("branch_status ");
			print_r($auth['branch_status']); */

			$date 	= date('d', strtotime($sesi['start_date']));
			$month 	= date('m', strtotime($sesi['start_date']));
			$list = $this->AcctCreditsAccountMasterData_model->get_datatables($date, $sesi['branch_id']);


			$membergender 	= $this->configuration->MemberGender();
			$memberidentity = $this->configuration->MemberIdentity();

			/* print_r($list);exit; */



			$data = array();
			$no = $_POST['start'];
			foreach ($list as $creditsaccount) {
				$savings_account_no = $this->AcctCreditsAccountMasterData_model->getSavingsAccountNo($creditsaccount->savings_account_id);
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $creditsaccount->credits_account_serial;
				$row[] = $creditsaccount->branch_name;
				$row[] = $creditsaccount->member_no;
				$row[] = $creditsaccount->member_name;
				$row[] = $membergender[$creditsaccount->member_gender];
				$row[] = tgltoview($creditsaccount->member_date_of_birth);
				$row[] = $creditsaccount->member_job;
				$row[] = $creditsaccount->member_address;
				$row[] = $memberidentity[$creditsaccount->member_identity];
				$row[] = $creditsaccount->member_identity_no;
				$row[] = $creditsaccount->member_phone;
				$row[] = $creditsaccount->credits_name;
				$row[] = $creditsaccount->credits_account_period;
				$row[] = tgltoview($creditsaccount->credits_account_date);
				$row[] = tgltoview($creditsaccount->credits_account_due_date);
				$row[] = number_format($creditsaccount->credits_account_net_price, 2);
				$row[] = number_format($creditsaccount->credits_account_net_price, 2);
				$row[] = number_format($creditsaccount->credits_account_margin, 2);
				$row[] = number_format($creditsaccount->credits_account_principal_amount, 2);
				$row[] = number_format($creditsaccount->credits_account_margin_amount, 2);
				$row[] = number_format($creditsaccount->credits_account_last_balance_principal, 2);
				$row[] = number_format($creditsaccount->credits_account_last_balance_margin, 2);
				$data[] = $row;
			}



			// print_r($list);exit;
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->AcctCreditsAccountMasterData_model->count_all($date, $sesi['branch_id']),
							"recordsFiltered" => $this->AcctCreditsAccountMasterData_model->count_filtered($date, $sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function exportAcctCreditsAccountMasterData(){
			$auth = $this->session->userdata('auth');

			$sesi = $this->session->userdata('filter-masterdatacreditsaccount');
			if(!is_array($sesi)){
				$sesi['start_date']			= date('Y-m-d');
				if($auth['branch_status'] == 1){
					$sesi['branch_id']			= '';
				} else {
					$sesi['branch_id']			= $auth['branch_id'];
				}
			}

			/* if($auth['branch_status'] == 1){
				$sesi['branch_id']			= '';
			} else {
				$sesi['branch_id']			= $auth['branch_id'];
			} */

			$date 	= date('d', strtotime($sesi['start_date']));
			$month 	= date('m', strtotime($sesi['start_date']));

			$acctcreditsaccountmasterdata	= $this->AcctCreditsAccountMasterData_model->getExport($sesi['branch_id']);
			$membergender 	= $this->configuration->MemberGender();
			$memberidentity = $this->configuration->MemberIdentity();

			
			if($acctcreditsaccountmasterdata->num_rows()!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									->setLastModifiedBy("SIS")
									->setTitle("Master Data Pembiayaan")
									->setSubject("")
									->setDescription("Master Data Pembiayaan")
									->setKeywords("Master, Data, Pembiayaan")
									->setCategory("Master Data Pembiayaan");
									
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(20);


				
				$this->excel->getActiveSheet()->mergeCells("B1:X1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:X3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:X3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:X3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Pembiayaan");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Akad");
				$this->excel->getActiveSheet()->setCellValue('D3',"No. Rekening");
				$this->excel->getActiveSheet()->setCellValue('E3',"No. Anggota");
				$this->excel->getActiveSheet()->setCellValue('F3',"Nama");
				$this->excel->getActiveSheet()->setCellValue('G3',"JNS Kel");
				$this->excel->getActiveSheet()->setCellValue('H3',"Tanggal Lahir");
				$this->excel->getActiveSheet()->setCellValue('I3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('J3',"Pekerjaan");
				$this->excel->getActiveSheet()->setCellValue('K3',"Identitas");
				$this->excel->getActiveSheet()->setCellValue('L3',"No Identitas");
				$this->excel->getActiveSheet()->setCellValue('M3',"Telp");
				$this->excel->getActiveSheet()->setCellValue('N3',"Pembiayaan");
				$this->excel->getActiveSheet()->setCellValue('O3',"JK Waktu");
				$this->excel->getActiveSheet()->setCellValue('P3',"TG Pinjam");
				$this->excel->getActiveSheet()->setCellValue('Q3',"TG JT Tempo");
				$this->excel->getActiveSheet()->setCellValue('R3',"JML Plafon");
				$this->excel->getActiveSheet()->setCellValue('S3',"Pokok");
				$this->excel->getActiveSheet()->setCellValue('T3',"Margin");
				$this->excel->getActiveSheet()->setCellValue('U3',"ANG Pokok");
				$this->excel->getActiveSheet()->setCellValue('V3',"ANG Margin");
				$this->excel->getActiveSheet()->setCellValue('W3',"Saldo Pokok");
				$this->excel->getActiveSheet()->setCellValue('X3',"Saldo Margin");

				
				$j=4;
				$no=0;
				
				foreach($acctcreditsaccountmasterdata->result_array() as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':X'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('R'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('S'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('T'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('U'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('V'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('W'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('X'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

						$savings_account_no = $this->AcctCreditsAccountMasterData_model->getSavingsAccountNo($val['savings_account_id']);

						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $savings_account_no);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, "'".$val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $membergender[$val['member_gender']]);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, tgltoview($val['member_date_of_birth']));
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['member_job']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $memberidentity[$val['member_identity']]);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['member_identity_no']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $val['member_phone']);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $val['credits_name']);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, $val['credits_account_period']);
						$this->excel->getActiveSheet()->setCellValue('P'.$j, tgltoview($val['credits_account_date']));
						$this->excel->getActiveSheet()->setCellValue('Q'.$j, tgltoview($val['credits_account_due_date']));
						$this->excel->getActiveSheet()->setCellValue('R'.$j, number_format($val['credits_account_net_price'], 2));
						$this->excel->getActiveSheet()->setCellValue('S'.$j, number_format($val['credits_account_net_price'], 2));
						$this->excel->getActiveSheet()->setCellValue('T'.$j, number_format($val['credits_account_margin'], 2));
						$this->excel->getActiveSheet()->setCellValue('U'.$j, number_format($val['credits_account_principal_amount'], 2));	
						$this->excel->getActiveSheet()->setCellValue('V'.$j, number_format($val['credits_account_margin_amount'], 2));	
						$this->excel->getActiveSheet()->setCellValue('W'.$j, number_format($val['credits_account_last_balance_principal'], 2));	
						$this->excel->getActiveSheet()->setCellValue('X'.$j, number_format($val['credits_account_last_balance_margin'], 2));	
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Pembiayaan.xls';
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

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctcreditsaccountmasterdata-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctcreditsaccountmasterdata-'.$unique['unique'],$sessions);
		}	
		
	}
?>