<?php
	Class AcctCreditsAgunan extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAgunan_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsAgunan_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsAgunan/ListAcctCreditsAgunan_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctcreditsagunan',$data);
			redirect('AcctCreditsAgunan');
		}

		public function getAcctCreditsAgunanList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditsagunan');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->AcctCreditsAgunan_model->get_datatables($sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $agunan) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $agunan->credits_account_serial;
	            $row[] = $this->AcctCreditsAgunan_model->getMemberName($agunan->member_id);
	            $row[] = $agunan->credits_agunan_shm_no_sertifikat;
	            $row[] = $agunan->credits_agunan_shm_luas;
	            $row[] = $agunan->credits_agunan_shm_atas_nama;
	            $row[] = $agunan->credits_agunan_shm_kedudukan;
	            $row[] = number_format($agunan->credits_agunan_shm_taksiran, 2);
	            $row[] = $agunan->credits_agunan_bpkb_nomor;
	            $row[] = $agunan->credits_agunan_bpkb_nama;
	            $row[] = $agunan->credits_agunan_bpkb_nopol;
	            $row[] = $agunan->credits_agunan_bpkb_no_rangka;
	            $row[] = $agunan->credits_agunan_bpkb_no_mesin;
	            $row[] = number_format($agunan->credits_agunan_bpkb_taksiran, 2);
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditsAgunan_model->count_all($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditsAgunan_model->count_filtered($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function export(){	
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditsagunan');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$acctcreditsagunan	= $this->AcctCreditsAgunan_model->getExportAcctCreditsAgunan($sesi['branch_id']);

			
			if($acctcreditsagunan->num_rows()!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									 ->setLastModifiedBy("SIS")
									 ->setTitle("Master Data Agunan")
									 ->setSubject("")
									 ->setDescription("Master Data Agunan")
									 ->setKeywords("Master, Data, Agunan")
									 ->setCategory("Master Data Agunan");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
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

				
				$this->excel->getActiveSheet()->mergeCells("B1:O1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:O3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:O3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Agunan");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Akad");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Sertifikat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Luas");
				$this->excel->getActiveSheet()->setCellValue('G3',"Atas Nama");
				$this->excel->getActiveSheet()->setCellValue('H3',"Kedudukan");
				$this->excel->getActiveSheet()->setCellValue('I3',"Taksiran");
				$this->excel->getActiveSheet()->setCellValue('J3',"BPKB");
				$this->excel->getActiveSheet()->setCellValue('K3',"Atas Nama");
				$this->excel->getActiveSheet()->setCellValue('L3',"No. Polisi");
				$this->excel->getActiveSheet()->setCellValue('M3',"No. Rangka");
				$this->excel->getActiveSheet()->setCellValue('N3',"No. Mesin");
				$this->excel->getActiveSheet()->setCellValue('O3',"Taksiran");
				
				$j=4;
				$no=0;
				
				foreach($acctcreditsagunan->result_array() as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':O'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $this->AcctCreditsAgunan_model->getMemberName($val['member_id']));
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['credits_agunan_shm_no_sertifikat']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['credits_agunan_shm_luas']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['credits_agunan_shm_atas_nama']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['credits_agunan_shm_kedudukan']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_agunan_shm_taksiran'], 2));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['credits_agunan_bpkb_nomor']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_agunan_bpkb_nama']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['credits_agunan_bpkb_nopol']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $val['credits_agunan_bpkb_no_rangka']);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $val['credits_agunan_bpkb_no_mesin']);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, number_format($val['credits_agunan_bpkb_taksiran'], 2));		
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Agunan.xls';
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
		
	}
?>