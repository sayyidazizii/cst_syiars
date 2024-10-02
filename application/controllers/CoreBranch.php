<?php
	Class CoreBranch extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreBranch_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= $this->CoreBranch_model->getDataCoreBranch();
			$data['main_view']['content']			= 'CoreBranch/ListCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreBranch(){
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= create_double($this->CoreBranch_model->getCoreBranch(),'branch_id','branch_code');
			$data['main_view']['acctaccount']		= create_double($this->CoreBranch_model->getAcctAccount(),'account_id','account_code');	
			$data['main_view']['content']			= 'CoreBranch/FormAddCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function getBranchName(){
			$branch_parent_id		= $this->input->post('branch_parent_id',true);
			$branch_name = $this->CoreBranch_model->getBranchName($branch_parent_id);
			
			$result = array();
			$result = array("status" => "true", "branch_parent_id"=>trim($branch_parent_id,' '), "branch_name" => $branch_name);
			
			echo json_encode($result);
		}
		
		public function processAddCoreBranch(){
			$data = array(
				'branch_code'				=> $this->input->post('branch_code', true),
				'branch_name'				=> $this->input->post('branch_name', true),
				'branch_city'				=> $this->input->post('branch_city', true),
				'branch_address'			=> $this->input->post('branch_address', true),
				'branch_contact_person'		=> $this->input->post('branch_contact_person', true),
				'branch_phone1'				=> $this->input->post('branch_phone1', true),
				'branch_email'				=> $this->input->post('branch_email', true),
				'account_rak_id'			=> $this->input->post('account_rak_id', true),
				'account_aka_id'			=> $this->input->post('account_aka_id', true),
				'branch_manager'			=> $this->input->post('branch_manager', true),
				'branch_no_sk'				=> $this->input->post('branch_no_sk', true),
			);
			
			$this->form_validation->set_rules('branch_code', 'Code', 'required');
			$this->form_validation->set_rules('branch_name', 'Name', 'required');
			$this->form_validation->set_rules('branch_city', 'Kota', 'required');
			$this->form_validation->set_rules('branch_address', 'Address', 'required');
			$this->form_validation->set_rules('branch_contact_person', 'Contact Person', 'required');
			$this->form_validation->set_rules('branch_phone1', 'Phone', 'required');
			$this->form_validation->set_rules('branch_email', 'Email', 'required');
			$this->form_validation->set_rules('account_rak_id', 'Perkiraan RAK', 'required');
			$this->form_validation->set_rules('account_aka_id', 'Perkiraan AKA', 'required');
			$this->form_validation->set_rules('branch_manager', 'Kepala Cabang', 'required');
			$this->form_validation->set_rules('branch_no_sk', 'NO SK', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreBranch_model->insertCoreBranch($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Cabang Sukses
							</div> ";
					$this->session->unset_userdata('addcorebranch');
					$this->session->set_userdata('message',$msg);
					redirect('CoreBranch/addCoreBranch');
				}else{
					$this->session->set_userdata('addcorebranch',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Cabang Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreBranch/addCoreBranch');
				}
			}else{
				$this->session->set_userdata('addcorebranch',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreBranch/addCoreBranch');
			}
		}
		
		public function editCoreBranch(){
			$data['main_view']['corebranchparent']	= create_double($this->CoreBranch_model->getCoreBranch(),'branch_id','branch_code');
			$data['main_view']['acctaccount']		= create_double($this->CoreBranch_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= $this->CoreBranch_model->getCoreBranch_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreBranch/FormEditCoreBranch_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreBranch(){
			$data = array(
				'branch_id'					=> $this->input->post('branch_id', true),
				'branch_code'				=> $this->input->post('branch_code', true),
				'branch_name'				=> $this->input->post('branch_name', true),
				'branch_city'				=> $this->input->post('branch_city', true),
				'branch_address'			=> $this->input->post('branch_address', true),
				'branch_contact_person'		=> $this->input->post('branch_contact_person', true),
				'branch_phone1'				=> $this->input->post('branch_phone1', true),
				'branch_email'				=> $this->input->post('branch_email', true),
				'account_rak_id'			=> $this->input->post('account_rak_id', true),
				'account_aka_id'			=> $this->input->post('account_aka_id', true),
				'branch_manager'			=> $this->input->post('branch_manager', true),
				'branch_no_sk'				=> $this->input->post('branch_no_sk', true),
			);
			
			$this->form_validation->set_rules('branch_code', 'Code', 'required');
			$this->form_validation->set_rules('branch_name', 'Name', 'required');
			$this->form_validation->set_rules('branch_city', 'Kota', 'required');
			$this->form_validation->set_rules('branch_address', 'Address', 'required');
			$this->form_validation->set_rules('branch_contact_person', 'Contact Person', 'required');
			$this->form_validation->set_rules('branch_phone1', 'Phone', 'required');
			$this->form_validation->set_rules('branch_email', 'Email', 'required');
			$this->form_validation->set_rules('account_rak_id', 'Perkiraan RAK', 'required');
			$this->form_validation->set_rules('account_aka_id', 'Perkiraan AKA', 'required');
			$this->form_validation->set_rules('branch_manager', 'Kepala Manager', 'required');
			$this->form_validation->set_rules('branch_no_sk', 'NO SK', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreBranch_model->updateCoreBranch($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Cabang Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreBranch/editCoreBranch/'.$data['branch_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Cabang Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreBranch/editCoreBranch/'.$data['branch_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreBranch/editCoreBranch/'.$data['branch_id']);
			}				
		}
		
		public function deleteCoreBranch(){
			if($this->CoreBranch_model->deleteCoreBranch($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Cabang Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreBranch');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Cabang Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreBranch');
			}
		}
		
		// public function export(){
		// 	$item = $this->CoreBranch_model->getexport();
			
		// 	if($item->num_rows()!=0){
		// 		$this->load->library('excel');
				
		// 		$this->excel->getProperties()->setCreator("IBS CJDW")
		// 							 ->setLastModifiedBy("IBS CJDW")
		// 							 ->setTitle("Branch Report")
		// 							 ->setSubject("")
		// 							 ->setDescription("Branch Report")
		// 							 ->setKeywords("Asset, Type, Report")
		// 							 ->setCategory("Branch Report");
									 
		// 		$this->excel->setActiveSheetIndex(0);
		// 		$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		// 		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
		// 		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		// 		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		// 		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		// 		$this->excel->getActiveSheet()->mergeCells("B1:E1");
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getFont()->setBold(true);
		// 		$this->excel->getActiveSheet()->setCellValue('B1',"Machine");
		// 		$this->excel->getActiveSheet()->setCellValue('B3',"No");
		// 		$this->excel->getActiveSheet()->setCellValue('C3',"Branch Code");
		// 		$this->excel->getActiveSheet()->setCellValue('D3',"Branch Name");
		// 		$this->excel->getActiveSheet()->setCellValue('E3',"Description");
				
		// 		$j=4;
		// 		$no=0;
		// 		foreach($item->result_array() as $key=>$val){
		// 			if(is_numeric($key)){
		// 				$no++;
		// 				$this->excel->setActiveSheetIndex(0);
		// 				$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 				$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						
		// 				$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
		// 				$this->excel->getActiveSheet()->setCellValue('C'.$j, $val[branch_code]);
		// 				$this->excel->getActiveSheet()->setCellValue('D'.$j, $val[branch_name]);
		// 				$this->excel->getActiveSheet()->setCellValue('E'.$j, $val[branch_description]);
		// 			}else{
		// 				continue;
		// 			}
		// 			$j++;
		// 		}
		// 		$filename='AssetType.xls';
		// 		header('Content-Type: application/vnd.ms-excel');
		// 		header('Content-Disposition: attachment;filename="'.$filename.'"');
		// 		header('Cache-Control: max-age=0');
							 
		// 		$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
		// 		ob_end_clean();
		// 		$objWriter->save('php://output');
		// 	}else{
		// 		echo "No available data !";
		// 	}
		// }
		
		// function import(){
		// 	$data['main_view']['content']	= 'CoreBranch/formimportcorebranch_view';
		// 	$this->load->view('MainPage_view',$data);
		// }
		
		// function processimportmachine(){
		// 	$auth = $this->session->userdata('auth');
		// 	$fileName 	= $_FILES['filexls']['name'];
		// 	$fileSize 	= $_FILES['filexls']['size'];
		// 	$fileError 	= $_FILES['filexls']['error'];
		// 	$fileType 	= $_FILES['filexls']['type'];
		// 	$config['upload_path'] = 'dataupload/';
  //           $config['file_name'] = $fileName;
  //           $config['allowed_types'] = 'xls|xlsx';
  //           $config['max_size']        = 10000;
		// 	$this->load->library('upload');
  //           $this->upload->initialize($config);
		// 	if(! $this->upload->do_upload('filexls') ){
		// 		$msg = "<div class='alert alert-danger alert-dismissable'>                
		// 			".$this->upload->display_errors('', '')."
		// 			</div> ";
		// 		$this->session->set_userdata('message',$msg);
		// 		redirect('machine');
		// 	}else{
		// 		$media = $this->upload->data('filexls');
		// 		$inputFileName = 'dataupload/'.$media['file_name'];
		// 		try {
		// 			$inputFileType = IOFactory::identify($inputFileName);
		// 			$objReader = IOFactory::createReader($inputFileType);
		// 			$objPHPExcel = $objReader->load($inputFileName);
		// 		} catch(Exception $e) {
		// 			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		// 		}
		// 		$sheet = $objPHPExcel->getSheet(0);
		// 		$highestRow = $sheet->getHighestRow();
		// 		$highestColumn = $sheet->getHighestColumn();
		// 		$sukses = 0;
		// 		$gagal = 0;
		// 		for ($row = 2; $row <= $highestRow; $row++){ 
		// 			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
  //                                               NULL,
  //                                               TRUE,
  //                                               FALSE);
					
		// 			$data= array (
		// 				'branch_code'		=> $rowData[0][0],
		// 				'branch_name'		=> $rowData[0][1],
		// 				'branch_description'	=> $rowData[0][2],
		// 			);
		// 			if($data['branch_code'] != ''){
		// 				if($this->CoreBranch_model->cekcorebranchcode($data['branch_code'])==0){
		// 					if($this->machine_model->insertmachine($data)){
		// 						$sukses++;
		// 						continue;
		// 					}else{
		// 						$dataArrayItem 	= $this->session->userdata('importcorebranch');
		// 						$dataArrayItem[$data['branch_code']] = $data;
		// 						$this->session->set_userdata('importcorebranch',$dataArrayItem);
		// 						continue;
		// 					}
		// 				}else{
		// 					$dataArrayItem 	= $this->session->userdata('importcorebranch');
		// 					$dataArrayItem[$data['branch_code']] = $data;
		// 					$this->session->set_userdata('importcorebranch',$dataArrayItem);
		// 				}
		// 			}
		// 		}
		// 		$auth = $this->session->userdata('auth');
		// 		// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processimportmachine',$auth['username'],'Import New machine');
		// 		$msg = "<div class='alert alert-success'>                
		// 					Import Data Branch Successfully ".$sukses." record
		// 				</div> ";
		// 		$this->session->set_userdata('message',$msg);
		// 		redirect('CoreBranch');
		// 	}
		// }
	}
?>