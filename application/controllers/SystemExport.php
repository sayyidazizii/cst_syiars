<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	set_time_limit(600);
	ini_set('memory_limit', '2048M');
	Class SystemExport extends MY_Controller{
		public function __construct(){
			parent::__construct();

			$menu = 'export';

			$this->cekLogin();
			$this->accessMenu($menu);

			$this->load->model('MainPage_model');
			$this->load->model('SystemExport_model');
			$this->load->helper('sistem');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->helper('url');
			$this->load->database('default');
		}
		
		public function index(){
			$auth 		= $this->session->userdata('auth');

			$data['main_view']['corebranch']		= create_double($this->SystemExport_model->getCoreBranch(), 'branch_id', 'branch_name');

			$data['main_view']['content']			= 'SystemExport/ListSystemExport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processExportCoreMember(){
			$core_member_export_start_date		= tgltodb($this->input->post('core_member_export_start_date',true));
			$core_member_export_end_date		= tgltodb($this->input->post('core_member_export_end_date',true));
			$core_member_export_branch_id		= $this->input->post('core_member_export_branch_id',true);
			
			
			$coremember 						= $this->SystemExport_model->getCoreMember($core_member_export_branch_id, $core_member_export_start_date, $core_member_export_end_date );

			$branch_name 						= $this->SystemExport_model->getBranchName($core_member_export_branch_id);
			
			if(is_array($coremember)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
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
			
				$this->excel->getActiveSheet()->mergeCells("B1:T1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:T5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:T5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:T5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL MASTER DAFTAR ANGGOTA KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode Daftar : ".tgltoview($core_member_export_start_date)." s/d ".tgltoview($core_member_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('D5',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E5',"Jenis Kelamin");
				$this->excel->getActiveSheet()->setCellValue('F5',"Tempat Lahir");
				$this->excel->getActiveSheet()->setCellValue('G5',"Tanggal Lahir");
				$this->excel->getActiveSheet()->setCellValue('H5',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('I5',"Desa");
				$this->excel->getActiveSheet()->setCellValue('J5',"Kecamatan");
				$this->excel->getActiveSheet()->setCellValue('K5',"Kelurahan");
				$this->excel->getActiveSheet()->setCellValue('L5',"No Telp / HP");
				$this->excel->getActiveSheet()->setCellValue('M5',"Pekerjaan");
				$this->excel->getActiveSheet()->setCellValue('N5',"Kartu Identitas");
				$this->excel->getActiveSheet()->setCellValue('O5',"No ID / KTP");
				$this->excel->getActiveSheet()->setCellValue('P5',"Sifat Anggota");
				$this->excel->getActiveSheet()->setCellValue('Q5',"Tanggal Daftar");
				$this->excel->getActiveSheet()->setCellValue('R5',"Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('S5',"Simpanan Khusus");
				$this->excel->getActiveSheet()->setCellValue('T5',"Simpanan Wajib");
			
				
				$j	= 6;
				$no	= 0;

				$membercharacter	= $this->configuration->MemberCharacter();
				$membergender	 	= $this->configuration->MemberGender();
				$memberidentity	 	= $this->configuration->MemberIdentity();
				
				foreach($coremember as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':T'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('R'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('S'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('T'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);



						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, "'".$val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $membergender[$val['member_gender']]);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_place_of_birth']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, tgltoview($val['member_date_of_birth']));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['dusun_name']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['kecamatan_name']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['kelurahan_name']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['member_phone']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $val['member_job']);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $memberidentity[$val['member_identity']]);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, "'".$val['member_identity_no']);
						$this->excel->getActiveSheet()->setCellValue('P'.$j, $membercharacter[$val['member_character']]);
						$this->excel->getActiveSheet()->setCellValue('Q'.$j, tgltoview($val['member_register_date']));
						$this->excel->getActiveSheet()->setCellValue('R'.$j, $val['member_principal_savings_last_balance']);
						$this->excel->getActiveSheet()->setCellValue('S'.$j, $val['member_special_savings_last_balance']);
						$this->excel->getActiveSheet()->setCellValue('T'.$j, $val['member_mandatory_savings_last_balance']);
					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABLE MASTER DATA ANGGOTA PERIODE '.tgltoview($core_member_export_start_date).' sd '.tgltoview($core_member_export_end_date).'.xls';
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


		public function processExportAcctSavingsAccount(){
			$acct_savings_account_export_start_date		= tgltodb($this->input->post('acct_savings_account_export_start_date',true));
			$acct_savings_account_export_end_date		= tgltodb($this->input->post('acct_savings_account_export_end_date',true));
			$acct_savings_account_export_branch_id		= $this->input->post('acct_savings_account_export_branch_id',true);
			
			
			$acctsavingsaccount 						= $this->SystemExport_model->getAcctSavingsAccount($acct_savings_account_export_branch_id, $acct_savings_account_export_start_date, $acct_savings_account_export_end_date );

			$branch_name 								= $this->SystemExport_model->getBranchName($acct_savings_account_export_branch_id);
			
			if(is_array($acctsavingsaccount)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);				
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
			
				$this->excel->getActiveSheet()->mergeCells("B1:M1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:M5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:M5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:M5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL MASTER DATA SIMPANAN KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($acct_savings_account_export_start_date)." s/d ".tgltoview($acct_savings_account_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Rekening");
				$this->excel->getActiveSheet()->setCellValue('D5',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('E5',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('F5',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('G5',"Desa");
				$this->excel->getActiveSheet()->setCellValue('H5',"Kecamatan");
				$this->excel->getActiveSheet()->setCellValue('I5',"Kelurahan");
				$this->excel->getActiveSheet()->setCellValue('J5',"Jenis / Produk Simpanan");
				$this->excel->getActiveSheet()->setCellValue('K5',"Tgl Buka Rekening");
				$this->excel->getActiveSheet()->setCellValue('L5',"Saldo Akhir Simpanan");
				$this->excel->getActiveSheet()->setCellValue('M5',"Tgl Trx Terakhir");
			
				
				$j	= 6;
				$no	= 0;
				
				foreach($acctsavingsaccount as $key => $val){
					if(is_numeric($key)){
						$no++;

						$savings_account_last_date 		= $this->SystemExport_model->getSavingsAccountLastDate($val['savings_account_id']);

						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('R'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('S'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('T'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, "'".$val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['dusun_name']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['kecamatan_name']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['kelurahan_name']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['savings_name']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, tgltoview($val['savings_account_date']));
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['savings_account_last_balance']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, tgltoview($savings_account_last_date));

					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABEL MASTER DATA SIMPANAN KSPPS MADANI JATIM '.tgltoview($acct_savings_account_export_start_date).' sd '.tgltoview($acct_savings_account_export_end_date).'.xls';
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

		public function processExportAcctDepositoAccount(){
			$acct_deposito_account_export_start_date	= tgltodb($this->input->post('acct_deposito_account_export_start_date',true));
			$acct_deposito_account_export_end_date		= tgltodb($this->input->post('acct_deposito_account_export_end_date',true));
			$acct_deposito_account_export_branch_id		= $this->input->post('acct_deposito_account_export_branch_id',true);
			
			
			$acctdepositoaccount 						= $this->SystemExport_model->getAcctDepositoAccount($acct_deposito_account_export_branch_id, $acct_deposito_account_export_start_date, $acct_deposito_account_export_end_date );

			$branch_name 								= $this->SystemExport_model->getBranchName($acct_deposito_account_export_branch_id);
			
			if(is_array($acctdepositoaccount)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);				
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
			
				$this->excel->getActiveSheet()->mergeCells("B1:P1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:P5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:P5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:P5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL MASTER DATA SIMPANAN BERJANGKA KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($acct_deposito_account_export_start_date)." s/d ".tgltoview($acct_deposito_account_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Deposito");
				$this->excel->getActiveSheet()->setCellValue('D5',"No Seri");
				$this->excel->getActiveSheet()->setCellValue('E5',"No Rekening");
				$this->excel->getActiveSheet()->setCellValue('F5',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('G5',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('H5',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('I5',"Desa");
				$this->excel->getActiveSheet()->setCellValue('J5',"Kecamatan");
				$this->excel->getActiveSheet()->setCellValue('K5',"Kelurahan");
				$this->excel->getActiveSheet()->setCellValue('L5',"Jenis Deposito");
				$this->excel->getActiveSheet()->setCellValue('M5',"Tanggal Buka");
				$this->excel->getActiveSheet()->setCellValue('N5',"Tanggal Mulai Aktif");
				$this->excel->getActiveSheet()->setCellValue('O5',"Tanggal Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('P5',"Saldo ");
			
				
				$j	= 6;
				$no	= 0;

				foreach($acctdepositoaccount as $key => $val){
					if(is_numeric($key)){
						$no++;

						$savings_account_last_date 		= $this->SystemExport_model->getSavingsAccountLastDate($val['savings_account_id']);

						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			

						
						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['deposito_account_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['deposito_account_serial_no']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, "'".$val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['dusun_name']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['kecamatan_name']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['kelurahan_name']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['deposito_name']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, tgltoview($val['deposito_account_date']));
						$this->excel->getActiveSheet()->setCellValue('N'.$j, );
						$this->excel->getActiveSheet()->setCellValue('O'.$j, tgltoview($val['deposito_account_due_date']));
						$this->excel->getActiveSheet()->setCellValue('P'.$j, $val['deposito_account_amount']);

					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABEL MASTER DATA SIMPANAN BERJANGKA KSPPS MADANI JATIM '.tgltoview($acct_deposito_account_export_start_date).' sd '.tgltoview($acct_deposito_account_export_end_date).'.xls';
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


		public function processExportAcctJournalVoucher(){
			$acct_journal_voucher_export_start_date		= tgltodb($this->input->post('acct_journal_voucher_export_start_date',true));
			$acct_journal_voucher_export_end_date		= tgltodb($this->input->post('acct_journal_voucher_export_end_date',true));
			$acct_journal_voucher_export_branch_id		= $this->input->post('acct_journal_voucher_export_branch_id',true);
			
			
			$acctjournalvoucher 						= $this->SystemExport_model->getAcctJournalVoucher($acct_journal_voucher_export_branch_id, $acct_journal_voucher_export_start_date, $acct_journal_voucher_export_end_date );

			$branch_name 								= $this->SystemExport_model->getBranchName($acct_journal_voucher_export_branch_id);
			
			if(is_array($acctjournalvoucher)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);				

			
				$this->excel->getActiveSheet()->mergeCells("B1:K1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:K5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:K5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL DAFTAR JURNAL TRANSAKSI KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($acct_journal_voucher_export_start_date)." s/d ".tgltoview($acct_journal_voucher_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Transaksi");
				$this->excel->getActiveSheet()->setCellValue('D5',"No Bukti");
				$this->excel->getActiveSheet()->setCellValue('E5',"Tanggal Transaksi");
				$this->excel->getActiveSheet()->setCellValue('F5',"No Perkiraan");
				$this->excel->getActiveSheet()->setCellValue('G5',"Nama Perkiraan");
				$this->excel->getActiveSheet()->setCellValue('H5',"Uraian");
				$this->excel->getActiveSheet()->setCellValue('I5',"Jumlah");
				$this->excel->getActiveSheet()->setCellValue('J5',"D/K");
				$this->excel->getActiveSheet()->setCellValue('K5',"Operator / Validasi");
				
			
				
				$j	= 6;
				$no	= 0;

				foreach($acctjournalvoucher as $key => $val){
					if(is_numeric($key)){
						$no++;

						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

						$journal_voucher_item_id = $this->SystemExport_model->getJournalVoucherItemID($val['journal_voucher_id']);

						
						if($val['journal_voucher_debit_amount'] <> 0 ){
							$nominal 	= $val['journal_voucher_debit_amount'];
							$status 	= "D";
						} else if($val['journal_voucher_credit_amount'] <> 0){
							$nominal 	= $val['journal_voucher_credit_amount'];
							$status 	= "K";
						} else {
							$nominal = 0;
							$status = 'Kosong';
						}
						
						$username 					= $this->SystemExport_model->getUsername($val['created_id']);


						if($val['journal_voucher_item_id'] == $journal_voucher_item_id){
							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValue('C'.$j, );
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['journal_voucher_no']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, tgltoview($val['journal_voucher_date']));
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['account_code']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['account_name']);
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['journal_voucher_description']);
							$this->excel->getActiveSheet()->setCellValue('I'.$j, $nominal);
							$this->excel->getActiveSheet()->setCellValue('J'.$j, $status);
							$this->excel->getActiveSheet()->setCellValue('K'.$j, $username);

							$no++;
						} else {
							$this->excel->getActiveSheet()->setCellValue('B'.$j, );
							$this->excel->getActiveSheet()->setCellValue('C'.$j, );
							$this->excel->getActiveSheet()->setCellValue('D'.$j, );
							$this->excel->getActiveSheet()->setCellValue('E'.$j, );
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['account_code']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['account_name']);
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['journal_voucher_description']);
							$this->excel->getActiveSheet()->setCellValue('I'.$j, $nominal);
							$this->excel->getActiveSheet()->setCellValue('J'.$j, $status);
							$this->excel->getActiveSheet()->setCellValue('K'.$j, $username);
						}


						

					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABEL DAFTAR JURNAL TRANSAKSI KSPPS MADANI JATIM '.tgltoview($acct_journal_voucher_export_start_date).' sd '.tgltoview($acct_journal_voucher_export_end_date).'.xls';
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


		public function processExportAcctCreditsAccount(){
			$acct_credits_account_export_start_date		= tgltodb($this->input->post('acct_credits_account_export_start_date',true));
			$acct_credits_account_export_end_date		= tgltodb($this->input->post('acct_credits_account_export_end_date',true));
			$acct_credits_account_export_branch_id		= $this->input->post('acct_credits_account_export_branch_id',true);
			
			
			$acctcreditsaccount 						= $this->SystemExport_model->getAcctCreditsAccount($acct_credits_account_export_branch_id, $acct_credits_account_export_start_date, $acct_credits_account_export_end_date );

			$branch_name 								= $this->SystemExport_model->getBranchName($acct_credits_account_export_branch_id);
			
			if(is_array($acctcreditsaccount)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
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

			
				$this->excel->getActiveSheet()->mergeCells("B1:W1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:W5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:W5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:W5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL MASTER DATA PEMBIAYAAN KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($acct_credits_account_export_start_date)." s/d ".tgltoview($acct_credits_account_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Akad Pembiayaan");
				$this->excel->getActiveSheet()->setCellValue('D5',"No Rekening");
				$this->excel->getActiveSheet()->setCellValue('E5',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('F5',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('G5',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('H5',"Desa");
				$this->excel->getActiveSheet()->setCellValue('I5',"Kecamatan");
				$this->excel->getActiveSheet()->setCellValue('J5',"Kelurahan");
				$this->excel->getActiveSheet()->setCellValue('K5',"Jenis Akad");
				$this->excel->getActiveSheet()->setCellValue('L5',"Agunan / Jaminan");
				$this->excel->getActiveSheet()->setCellValue('M5',"Sistem Angsur");
				$this->excel->getActiveSheet()->setCellValue('N5',"Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('O5',"Tanggal Realisasi");
				$this->excel->getActiveSheet()->setCellValue('P5',"Tanggal Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('Q5',"Pokok Pembiayaan");
				$this->excel->getActiveSheet()->setCellValue('R5',"Margin / Keuntungan");
				$this->excel->getActiveSheet()->setCellValue('S5',"Angsuran Pokok / Bulan");
				$this->excel->getActiveSheet()->setCellValue('T5',"Angsuran Margin / Bulan");
				$this->excel->getActiveSheet()->setCellValue('U5',"Saldo Pokok");
				$this->excel->getActiveSheet()->setCellValue('V5',"Saldo Margin");
				$this->excel->getActiveSheet()->setCellValue('W5',"Tanggal Angsur Terakhir");
			
				
				$j	= 6;
				$no	= 0;

				foreach($acctcreditsaccount as $key => $val){
					if(is_numeric($key)){
						$no++;

						$savings_account_last_date 		= $this->SystemExport_model->getSavingsAccountLastDate($val['savings_account_id']);

						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':W'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('R'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('S'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('T'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('U'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('V'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('W'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, "'".$val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['dusun_name']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['kecamatan_name']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['kelurahan_name']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_name']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, );
						$this->excel->getActiveSheet()->setCellValue('M'.$j, );
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $val['credits_account_period']);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, tgltoview($val['credits_account_date']));
						$this->excel->getActiveSheet()->setCellValue('P'.$j, tgltoview($val['credits_account_due_date']));
						$this->excel->getActiveSheet()->setCellValue('Q'.$j, $val['credits_account_net_price']);
						$this->excel->getActiveSheet()->setCellValue('R'.$j, $val['credits_account_margin']);
						$this->excel->getActiveSheet()->setCellValue('S'.$j, $val['credits_account_principal_amount']);
						$this->excel->getActiveSheet()->setCellValue('T'.$j, $val['credits_account_margin_amount']);
						$this->excel->getActiveSheet()->setCellValue('U'.$j, $val['credits_account_last_balance_principal']);
						$this->excel->getActiveSheet()->setCellValue('V'.$j, $val['credits_account_last_balance_margin']);
						$this->excel->getActiveSheet()->setCellValue('W'.$j, tgltoview($val['credits_account_last_payment_date']));

					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABEL MASTER DATA PEMBIAYAAN KSPPS MADANI JATIM '.tgltoview($acct_credits_account_export_start_date).' sd '.tgltoview($acct_credits_account_export_end_date).'.xls';
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


		public function processExportAcctGeneralLedger(){
			$acct_general_ledger_export_start_date		= tgltodb($this->input->post('acct_credits_account_export_start_date',true));
			$acct_general_ledger_export_end_date		= tgltodb($this->input->post('acct_general_ledger_export_end_date',true));
			$acct_general_ledger_export_branch_id		= $this->input->post('acct_general_ledger_export_branch_id',true);

			$branch_name 								= $this->SystemExport_model->getBranchName($acct_general_ledger_export_branch_id);

			$accountbalancedetail						= $this->SystemExport_model->getAcctAccountBalanceDetail($acct_general_ledger_export_branch_id, $acct_general_ledger_export_start_date, $acct_general_ledger_export_end_date);

			if(!empty($accountbalancedetail)){
				foreach ($accountbalancedetail as $key => $val) {
					$description 			= $this->SystemExport_model->getJournalVoucherDescription($val['transaction_id'], $val['account_id']);

					$journal_voucher_no 	= $this->SystemExport_model->getJournalVoucherNo($val['transaction_id']);

					

					if($val['account_default_status'] == 0 ){
						$debet 		= $val['account_in'];
						$kredit 	= $val['account_out'];

						$opening_balance 		= $val['last_balance'] - $debet + $kredit;

						$last_balance 			= $val['last_balance'];
					} else {
						$debet 	= $val['account_out'];
						$kredit = $val['account_in'];

						$opening_balance 		= $val['last_balance'] - $debet + $kredit;

						$last_balance 			= $val['last_balance'];
					}

					

					$data_acctaccountbalance[] = array (
						'transaction_date'			=> $val['transaction_date'],
						'transaction_no'			=> $journal_voucher_no,
						'transaction_description'	=> $description,
						'account_code'				=> $val['account_code'],
						'account_name'				=> $val['account_name'],
						'account_in'				=> $debet,
						'account_out'				=> $kredit,
						'opening_balance'			=> $opening_balance,
						'last_balance'				=> $last_balance,
					);
				}
			} else {
				
			}
			
			if(is_array($data_acctaccountbalance)){
				$this->load->library('excel');
				$this->excel->getProperties()->setCreator("CV Keisha")
									->setLastModifiedBy("CV Keisha")
									->setTitle("Sales Return Report")
									->setSubject("")
									->setDescription("Sales Return Report")
									->setKeywords("Sales, Return, Report")
									->setCategory("Sales Return Report");
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

			
				$this->excel->getActiveSheet()->mergeCells("B1:J1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B5:J5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:J5')->getFont()->	setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"TABEL MASTER DATA GENERAL LEDGER KSPPS MADANI JATIM");	
				$this->excel->getActiveSheet()->setCellValue('B2',"Periode : ".tgltoview($acct_general_ledger_export_start_date)." s/d ".tgltoview($acct_general_ledger_export_end_date));	
				$this->excel->getActiveSheet()->setCellValue('B3',"Cabang : ".$branch_name);	

			
				$this->excel->getActiveSheet()->setCellValue('B5',"No");
				$this->excel->getActiveSheet()->setCellValue('C5',"No Jurnal");
				$this->excel->getActiveSheet()->setCellValue('D5',"No Perkiraan");
				$this->excel->getActiveSheet()->setCellValue('E5',"Nama Perkiraan");
				$this->excel->getActiveSheet()->setCellValue('F5',"Tanggal Jurnal");
				$this->excel->getActiveSheet()->setCellValue('G5',"Jumlah Mutasi Debet");
				$this->excel->getActiveSheet()->setCellValue('H5',"Jumlah Mutasi Kredit");
				$this->excel->getActiveSheet()->setCellValue('I5',"Saldo Awal");
				$this->excel->getActiveSheet()->setCellValue('J5',"Saldo Akhir");
			
				
				$j	= 6;
				$no	= 0;

				foreach($data_acctaccountbalance as $key => $val){
					if(is_numeric($key)){
						$no++;

						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['transaction_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, "'".$val['account_code']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['account_name']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['transaction_date']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['account_in']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['account_out']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $val['opening_balance']);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['last_balance']);

					}else{
						continue;
					}
					$j++;
				}
				
				$filename='TABEL MASTER DATA GENERAL LEDGER KSPPS MADANI JATIM '.tgltoview($acct_general_ledger_export_start_date).' sd '.tgltoview($acct_general_ledger_export_end_date).'.xls';
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