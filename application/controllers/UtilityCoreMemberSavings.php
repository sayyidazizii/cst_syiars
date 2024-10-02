<?php
	Class UtilityCoreMemberSavings extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('UtilityCoreMemberSavings_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreMember-'.$unique['unique']);	
			$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
			$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);

			$data['main_view']['corebranch']		= create_double($this->UtilityCoreMemberSavings_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'UtilityCoreMemberSavings/ListUtilityCoreMemberSavings_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-utilitycoremember',$data);
			redirect('UtilityCoreMemberSavings');
		}

		public function getCoreMemberList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-utilitycoremember');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$data_state = 0;

			$list = $this->CoreMember_model->get_datatables($data_state);

			// print_r($list);exit;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = $memberstatus[$customers->member_status];
	            $row[] = $membercharacter[$customers->member_character];
	            $row[] = $customers->member_phone;
	            $row[] = number_format($customers->member_principal_savings_last_balance, 2);
	            $row[] = number_format($customers->member_special_savings_last_balance, 2);
	            $row[] = number_format($customers->member_mandatory_savings_last_balance, 2);
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMember_model->count_all($data_state),
	                        "recordsFiltered" => $this->CoreMember_model->count_filtered($data_state),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}		

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addCoreMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addCoreMember-'.$unique['unique'],$sessions);
		}

		public function reset_add(){
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
			redirect('CoreMember/addCoreMember');
		}

		public function getCoreCity(){
			$province_id 		= $this->uri->segment(3);
			
			$item = $this->UtilityCoreMemberSavings_model->getCoreCity($province_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['city_id']	= $mp['city_id'];
				$jsond[$i]['city_name']	= $mp['city_name'];
				$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreKecamatan(){
			$city_id 		= $this->uri->segment(3);
			
			$item = $this->UtilityCoreMemberSavings_model->getCoreKecamatan($city_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['kecamatan_id']		= $mp['kecamatan_id'];
				$jsond[$i]['kecamatan_name'] 	= $mp['kecamatan_name'];
			$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreKelurahan(){
			$kecamatan_id 		= $this->uri->segment(3);
			
			$item = $this->UtilityCoreMemberSavings_model->getCoreKelurahan($kecamatan_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['kelurahan_id']		= $mp['kelurahan_id'];
				$jsond[$i]['kelurahan_name']	= $mp['kelurahan_name'];
				$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreDusun(){
			$kelurahan_id 		= $this->uri->segment(3);
			
			$item = $this->UtilityCoreMemberSavings_model->getCoreDusun($kelurahan_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['dusun_id']		= $mp['dusun_id'];
				$jsond[$i]['dusun_name']	= $mp['dusun_name'];
				$i++;
			}
			echo json_encode($jsond);
		}

		public function function_elements_edit(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('editCoreMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('editCoreMember-'.$unique['unique'],$sessions);
		}

		public function reset_edit_member(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('editCoreMember-'.$unique['unique']);
			redirect('UtilityCoreMemberSavings/editCoreMemberSavings/'.$member_id);
		}

		public function editCoreMemberSavings(){
			$member_id 	= $this->uri->segment(3);
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('coremembertokenedit-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('coremembertokenedit-'.$unique['unique'], $token);
			}

			$data['main_view']['coremember']		= $this->UtilityCoreMemberSavings_model->getCoreMember_Detail($member_id);

			$data['main_view']['coreprovince']		= create_double($this->UtilityCoreMemberSavings_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']		= create_double($this->UtilityCoreMemberSavings_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']		= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	

			$data['main_view']['content']			= 'UtilityCoreMemberSavings/FormEditCoreMemberSavings_view';

			$this->load->view('MainPage_view',$data);
		}

		public function getListCoreMemberEdit(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$list = $this->CoreMember_model->get_datatables($data_state);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'UtilityCoreMemberSavings/editCoreMemberSavings/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMember_model->count_all($data_state),
	                        "recordsFiltered" => $this->CoreMember_model->count_filtered($data_state),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}	

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->UtilityCoreMemberSavings_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}	
		
		
		public function processEditCoreMemberSavings(){
			$auth = $this->session->userdata('auth');

			$username = $this->UtilityCoreMemberSavings_model->getUserName($auth['user_id']);

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'branch_id'								=> $auth['branch_id'],
				'member_name'							=> $this->input->post('member_name', true),
				'member_address'						=> $this->input->post('member_address', true),
				'city_id'								=> $this->input->post('city_id', true),
				'kecamatan_id'							=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'							=> $this->input->post('kelurahan_id', true),
				'dusun_id'								=> $this->input->post('dusun_id', true),
				'member_character'						=> $this->input->post('member_character', true),
				'member_principal_savings'				=> $this->input->post('member_principal_savings', true),
				'member_special_savings'				=> $this->input->post('member_special_savings', true),
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings_last_balance', true),
				'member_special_savings_last_balance'	=> $this->input->post('member_special_savings_last_balance', true),
				'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings_last_balance', true),
				'member_token_edit'						=> $this->input->post('member_token_edit', true),
				
			);


			$total_cash_amount = $data['member_principal_savings'] + $data['member_special_savings'] + $data['member_mandatory_savings'];
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$member_token_edit = $this->UtilityCoreMemberSavings_model->getMemberTokenEdit($data['member_token_edit']);
			
			if($this->form_validation->run()==true){
				if($member_token_edit->num_rows() == 0){
					if($this->UtilityCoreMemberSavings_model->updateCoreMember($data)){
						if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> '' || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){

							$data_detail = array (
								'branch_id'						=> $auth['branch_id'],
								'member_id'						=> $data['member_id'],
								'mutation_id'					=> $this->input->post('mutation_id', true),
								'transaction_date'				=> date('Y-m-d'),
								'principal_savings_amount'		=> $data['member_principal_savings'],
								'special_savings_amount'		=> $data['member_special_savings'],
								'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
								'operated_name'					=> $auth['username'],
								'savings_member_utility_token'	=> $data['member_token_edit'],
							);

							$this->UtilityCoreMemberSavings_model->insertAcctSavingsMemberUtility($data_detail);
						}





						$auth = $this->session->userdata('auth');
					
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Sukses
								</div> ";

						$unique = $this->session->userdata('unique');
						$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('UtilityCoreMemberSavings');
					}else{
						$msg = "<div class='alert alert-danger alert-dismissable'> 
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('UtilityCoreMemberSavings/editCoreMemberSavings/'.$data['member_id']);
					}
				} else {
					if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> '' || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){

						$data_detail = array (
							'branch_id'						=> $auth['branch_id'],
							'member_id'						=> $data['member_id'],
							'mutation_id'					=> $this->input->post('mutation_id', true),
							'transaction_date'				=> date('Y-m-d'),
							'principal_savings_amount'		=> $data['member_principal_savings'],
							'special_savings_amount'		=> $data['member_special_savings'],
							'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
							'operated_name'					=> $auth['username'],
							'savings_member_utility_token'	=> $data['member_token_edit'],
						);

						$savings_member_utility_token = $this->UtilityCoreMemberSavings_model->getSavingsMemberDetailToken($data['member_token_edit']);

						if($savings_member_utility_token->num_rows() == 0){
							$this->UtilityCoreMemberSavings_model->insertAcctSavingsMemberUtility($data_detail);
						}
					}
						
						



					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('UtilityCoreMemberSavings');
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('UtilityCoreMemberSavings/editCoreMemberSavings/'.$data['member_id']);
			}				
		}

		public function printBookCoreMember(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['coremember']		= $this->UtilityCoreMemberSavings_model->getDataCoreMember($auth['branch_id']);
			$data['main_view']['memberstatus']		= $this->configuration->MemberStatus();	
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['content']			= 'CoreMember/ListPrintBookCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getListCoreMemberBook(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$membership 		= $this->configuration->MembershipStatus();
			$list = $this->UtilityCoreMemberSavings_model->get_datatables($data_state);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = $memberstatus[$customers->member_status];
	            $row[] = $membercharacter[$customers->member_character];
	            $row[] = $customers->member_phone;
	            $row[] = '<a href="'.base_url().'CoreMember/processPrintCoverBookCoreMember/'.$customers->member_id.'" class="btn btn-info btn-xs" role="button"><span class="glyphicon glyphicon-print"></span> Cetak Cover</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->UtilityCoreMemberSavings_model->count_all($data_state),
	                        "recordsFiltered" => $this->UtilityCoreMemberSavings_model->count_filtered($data_state),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}	

		public function processPrintCoverBookCoreMember(){
			$member_id 			= $this->uri->segment(3);
			$coremember			= $this->UtilityCoreMemberSavings_model->getCoreMember_Detail($member_id);
			$MemberCharacter 	= $this->configuration->MemberCharacter();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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

			$pdf->SetMargins(7, 4, 7, 7); // put space of 10 on top
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

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\"></td>
			        <td width=\"12%\"><div style=\"text-align: left;\">No. Anggota</div></td>
			        <td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"3%\"></td>
			        <td width=\"12%\"><div style=\"text-align: left;\">Keanggotaan</div></td>
			        <td width=\"40%\"><div style=\"text-align: left;\">: ".$MemberCharacter[$coremember['member_character']]."</div></td>
			    </tr>
			    <tr>
			        <td width=\"3%\"></td>
			        <td width=\"12%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_name']."</div></td>
			    </tr>
			     <tr>
			        <td width=\"3%\"></td>
			        <td width=\"12%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_address']."</div></td>
			    </tr>
			     <tr>
			        <td width=\"3%\"></td>
			        <td width=\"12%\"><div style=\"text-align: left;\">No. Identitas</div></td>
			        <td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_identity_no']."</div></td>
			    </tr>				
			</table>";


			$pdf->writeHTML($tbl1, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cover Buku '.$coremember['member_name'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function printMutationCoreMember(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-coremembermutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			$this->session->unset_userdata('datamutasianggota-'.$unique['unique']);

			$member_id = $this->uri->segment(3);
			if($member_id == ''){
				$member_id = $sesi['member_id'];
			}

			$data['main_view']['coremember']					= $this->UtilityCoreMemberSavings_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsmemberdetail']		= $this->UtilityCoreMemberSavings_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);	

			$data['main_view']['content']			= 'CoreMember/ListPrintMutationCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterMutation(){
			$data = array (
				"start_date"	=> tgltodb($this->input->post('start_date',true)),
				"end_date"		=> tgltodb($this->input->post('end_date',true)),
				"member_id"		=> $this->input->post('member_id', true),
			);

			$this->session->set_userdata('filter-coremembermutation',$data);
			redirect('CoreMember/printMutationCoreMember');
		}

		public function getListCoreMemberMutation(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$list = $this->UtilityCoreMemberSavings_model->get_datatables($data_state);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'CoreMember/printMutationCoreMember/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->UtilityCoreMemberSavings_model->count_all($data_state),
	                        "recordsFiltered" => $this->UtilityCoreMemberSavings_model->count_filtered($data_state),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}	


		public function processPrintMutasiCoreMember(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-coremembermutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			$datamutasianggota = $this->session->userdata('datamutasianggota-'.$unique['unique']);

			$status 				= $this->uri->segment(3);
			$member_id 				= $this->uri->segment(4);
			$member_last_number 	= $this->uri->segment(5);

			if(empty($datamutasianggota)){
				$coremember				= $this->UtilityCoreMemberSavings_model->getCoreMember_Detail($member_id);
				$mutasicoremember		= $this->UtilityCoreMemberSavings_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);


				if(empty($member_last_number) || $member_last_number == 0){
					$no = 1;
				} else {
					$no = $member_last_number + 1;
				}

				if(empty($mutasicoremember)){
					$last_balance = $coremember['member_principal_savings_last_balance'] + $coremember['member_special_savings_last_balance'] + $coremember['member_mandatory_savings_last_balance'];

					$data[0] = array (
						'no'							=> $no,
						'savings_member_utility_id'		=> '',
						'member_id'						=> $coremember['member_id'],
						'transaction_date'				=> date('d-m-Y'),
						'transaction_code'				=> '',
						'principal_savings_amount'		=> $coremember['member_principal_savings_last_balance'],
						'special_savings_amount'		=> $coremember['member_special_savings_last_balance'],
						'mandatory_savings_amount'		=> $coremember['member_mandatory_savings_last_balance'],
						'last_balance'					=> $last_balance,
						'operated_name'					=> '',	
					);
				} else {
					foreach ($mutasicoremember as $key => $val) {
						if($no == 31){
							$no = 1;
						} else {
							$no = $no;
						}


						$data[] = array (
							'no'							=> $no,
							'savings_member_utility_id'		=> $val['savings_member_utility_id'],
							'member_id'						=> $val['member_id'],
							'transaction_date'				=> $val['transaction_date'],
							'transaction_code'				=> $val['mutation_code'],
							'principal_savings_amount'		=> $val['principal_savings_amount'],
							'special_savings_amount'		=> $val['special_savings_amount'],
							'mandatory_savings_amount'		=> $val['mandatory_savings_amount'],
							'last_balance'					=> $val['last_balance'],
							'operated_name'					=> $val['operated_name'],	
						);

						
						$no++;
						
					}
				}

				

				$this->session->set_userdata('datamutasianggota-'.$unique['unique'], $data);
			}

			$datamutasianggota = $this->session->userdata('datamutasianggota-'.$unique['unique']);

			// print_r($datamutasianggota);exit;

			if($status == 'print'){
				foreach ($datamutasianggota as $k => $v) {
					$update_data = array(
						'savings_member_utility_id'		=> $v['savings_member_utility_id'],
						'member_id'						=> $v['member_id'],
						'savings_print_status'			=> 1,
						'member_last_number'			=> $v['no'],
					);

					$this->UtilityCoreMemberSavings_model->updatePrintMutationStatus($update_data);
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

			$pdf->SetMargins(4, 26, 7, 7); // put space of 10 on top
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
			$resolution= array(180, 170);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------

			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			if($member_last_number > 1){
				for ($i=1; $i <= $member_last_number ; $i++) { 
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
			foreach ($datamutasianggota as $key => $val) { 
				if($val['no'] == 1){
					$tbl1 .= "
							<tr>
						    	<td width=\"4%\"><div style=\"text-align: center;\">No</div></td>
						    	<td width=\"10%\"><div style=\"text-align: center;\">Tanggal</div></td>
						    	<td width=\"9%\"><div style=\"text-align: center;\">Sandi</div></td>
						    	<td width=\"12%\"><div style=\"text-align: center;\">S.Pokok</div></td>
						    	<td width=\"13%\"><div style=\"text-align: center;\">S.Khusus</div></td>
						    	<td width=\"12%\"><div style=\"text-align: center;\">S Wajib</div></td>
						    	<td width=\"12%\"><div style=\"text-align: center;\">Saldo</div></td>
						    	<td width=\"5%\"><div style=\"text-align: center;\">Opt</div></td>
						    </tr>";
				}

				$tbl1 .= "
					<tr>
				    	<td width=\"3%\"><div style=\"text-align: left;\">".$val['no'].".</div></td>
				        <td width=\"10%\"><div style=\"text-align: center;\">".date('d-m-y',strtotime(($val['transaction_date'])))."</div></td>
				        <td width=\"9%\"><div style=\"text-align: center;\">".$val['transaction_code']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['principal_savings_amount'], 2)." &nbsp;</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['special_savings_amount'], 2)." &nbsp;</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['mandatory_savings_amount'], 2)." &nbsp;</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp;</div></td>
				        <td width=\"5%\"><div style=\"text-align: center;\">".substr($val['operated_name'],0,3)."</div></td>
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

			$pdf->writeHTML($tbl.$tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak Mutasi Anggota.pdf';

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
	}
?>