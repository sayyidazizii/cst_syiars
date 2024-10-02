<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 600);
	Class CoreMember extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
			// require 'vendor/autoload.php';
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreMember-'.$unique['unique']);	
			$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
			$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);

			$data['main_view']['corebranch']		= create_double($this->CoreMember_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'CoreMember/ListCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			//print_r($data);exit;

			$this->session->set_userdata('filter-coremember',$data);
			redirect('CoreMember');
		}

		public function getCoreMemberList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-coremember');

			$create_password_menu_id 			= $this->CoreMember_model->getIDMenu('CoreMemberPassword');

			$level 								= $this->CoreMember_model->getUserGroupLevel($auth['user_group_level']);

			$create_password_menu_id_mapping 	= $this->CoreMember_model->getIDMenuOnSystemMapping($create_password_menu_id, $level);

			$data_state = 0;
			$branch_id = "";

			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);

			//print_r($branch_id);exit;
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
				if($customers->ppob_status==0){
					if ($create_password_menu_id_mapping == 1){
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/createPasswordCoreMember/'.$customers->member_id.'" class="btn default btn-xs blue", onClick="javascript:return confirm(\'apakah yakin ingin buat password baru ?\')"><i class="fa fa-edit"></i> Buat Password</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';
					} else {
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';
					}
				}else if($customers->block_state == 1){
					if ($create_password_menu_id_mapping == 1){
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/openBlockCoreMember/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon", onClick="javascript:return confirm(\'apakah yakin ingin buka block anggota ?\')" ><i class="fa fa-edit"></i> Buka Block</a>
							<a href="'.base_url().'CoreMember/resetPasswordCoreMember/'.$customers->member_no.'/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon", onClick="javascript:return confirm(\'apakah yakin ingin reset password anggota ?\')"><i class="fa fa-edit"></i> Reset Password</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';					
					} else {
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';					
					}
				}else{
					if ($create_password_menu_id_mapping == 1){
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/resetPasswordCoreMember/'.$customers->member_no.'/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon", onClick="javascript:return confirm(\'apakah yakin ingin reset password anggota ?\')"><i class="fa fa-edit"></i> Reset Password</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';
					} else {
						$row[] = '
							<a href="'.base_url().'CoreMember/editCoreMember/'.$customers->member_id.'" class="btn default btn-xs purple"><i class="fa fa-edit"></i> Edit</a>
							<a href="'.base_url().'CoreMember/deleteCoreMember/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';
					}
				}
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function getMasterDataCoreMember(){
			$auth = $this->session->userdata('auth');
			
			$sesi	= 	$this->session->userdata('filter-coremembermasterdata');

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == ''){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$data_state = 0;
			$branch_id = "";

			$list 			= $this->CoreMember_model->get_datatables($data_state, $branch_id);

			$count_data 	= count($list);

	
			// print_r($count_data);exit;
			
			// $count_data = count($list);

			$rows 			= ceil($count_data / 1000);

			$data['main_view']['file']				= $rows;

			$data['main_view']['membershipstatus'] 	= $this->configuration->MembershipStatus();
			$data['main_view']['membersavings'] 	= $this->configuration->MemberSavings();
			$data['main_view']['corebranch']		= create_double($this->CoreMember_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'CoreMember/ListMasterDataCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterMasterData(){
			$data = array (
				"data_state" 		=> 0,
				"branch_id" 	=> $this->input->post('branch_id',true),
			);
			//print_r($data);exit;
			$this->session->set_userdata('filter-coremembermasterdata',$data);
			redirect('CoreMember/getMasterDataCoreMember');
		}


		public function getMasterDataCoreMemberList(){
			$auth = $this->session->userdata('auth');

			$sesi	= 	$this->session->userdata('filter-coremembermasterdata');

			// if(!is_array($sesi)){
			// 	$sesi['data_state'] 	= 0;

			// 	$sesi['member_savings'] = '';
			// }

			// $member_savings = $sesi['member_savings'];

			// if ($member_savings == 0) {
		
			// $list = $this->CoreMember_model->get_datatables($sesi['data_state'], $member_savings);

			// }
			// else if ($member_savings == 1) {
		
			// $list = $this->CoreMember_model->get_datatables1($sesi['data_state'], $member_savings);

			// }
			// else if($member_savings == 2){

			// $list = $this->CoreMember_model->get_datatables2($sesi['data_state'], $member_savings);	

			// }else if($member_savings == 3){

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == ''){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$data_state = 0;

			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);


			//print_r($list);exit;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$membership 		= $this->configuration->MembershipStatus();
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
				$row[] = $membergender[$customers->member_gender];
				$row[] = number_format($customers->member_principal_savings_last_balance, 2);
				$row[] = number_format($customers->member_special_savings_last_balance, 2);
				$row[] = number_format($customers->member_mandatory_savings_last_balance, 2);
				$row[] = $membership[$customers->data_state];
				$row[] = '
					
					<a href="'.base_url().'CoreMember/showdetail/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon"><i class="fa fa-bars"></i> Detail</a>';
				$data[] = $row;
			}
	
			//if ($member_savings == 0){ 
		//    $output = array(
		//                    "draw" => $_POST['draw'],
		//                    "recordsTotal" => $this->CoreMember_model->count_all($sesi['data_state']),
		//                    "recordsFiltered" => $this->CoreMember_model->count_filtered($sesi['data_state'], $member_savings),
		//                    "data" => $data,
		//            );
			// }else if ($member_savings == 1){ 
		//    $output = array(
		//                    "draw" => $_POST['draw'],
		//                    "recordsTotal" => $this->CoreMember_model->count_all1($sesi['data_state']),
		//                    "recordsFiltered" => $this->CoreMember_model->count_filtered1($sesi['data_state'], $member_savings),
		//                    "data" => $data,
		//            );
			// }else if($member_savings == 2){

			// 	 $output = array(
		//                    "draw" => $_POST['draw'],
		//                    "recordsTotal" => $this->CoreMember_model->count_all2($sesi['data_state']),
		//                    "recordsFiltered" => $this->CoreMember_model->count_filtered2($sesi['data_state'], $member_savings),
		//                    "data" => $data,
		//            );
			// }else if($member_savings == 3){

			// 	 $output = array(
		//                    "draw" => $_POST['draw'],
		//                    "recordsTotal" => $this->CoreMember_model->count_all3($sesi['data_state']),
		//                    "recordsFiltered" => $this->CoreMember_model->count_filtered3($sesi['data_state'], $member_savings),
		//                    "data" => $data,
		//            );
			// }
			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
						"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

		public function showdetail(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsaccount']		= $this->CoreMember_model->getAcctSavingsAccount_Member($member_id);
			$data['main_view']['acctcreditsaccount']		= $this->CoreMember_model->getAcctCreditsAccount_Member($member_id);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();	
			$data['main_view']['membergender']				= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']			= $this->configuration->MemberCharacter();	

			$data['main_view']['content']					= 'CoreMember/FormDetailCoreMember_view';

			$this->load->view('MainPage_view',$data);
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
		
		public function addCoreMember(){
			$unique = $this->session->userdata('unique');
			$auth 	= $this->session->userdata('auth');
			$token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);

			if(empty($token)){
				$member_token = md5(date("YmdHis"));
				$this->session->set_userdata('coremembertoken-'.$unique['unique'], $member_token);
			}

			$branchcode 	= $this->CoreMember_model->getBranchCode($auth['branch_id']);
			
			$last_member_no = $this->CoreMember_model->getLastMemberNo($auth['branch_id']);

			if($last_member_no->num_rows() <> 0){      
				//jika kode ternyata sudah ada.      
				$data = $last_member_no->row_array();    
				$kode = intval($data['last_member_no']) + 1;    
			} else {      
				//jika kode belum ada      
				$kode = 1;    
			}

			$kodemax 		= str_pad($kode, 8, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0

			/* print_r("last_member_no ");
			print_r($last_member_no);
			print_r("<BR> ");
			print_r("<BR> ");

			print_r("data ");
			print_r($data);
			print_r("<BR> ");
			print_r("<BR> ");

			print_r("kode ");
			print_r($kode);
			print_r("<BR> ");
			print_r("<BR> ");

			print_r("kodemax ");
			print_r($kodemax);
			print_r("<BR> ");
			print_r("<BR> ");
			exit; */

			$new_member_no 	= $kodemax;    // hasilnya ODJ-9921-0001 dst.

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');
			$data['main_view']['coreidentity']		= create_double($this->CoreMember_model->getCoreIdentity(),'identity_id', 'identity_name');
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	
			/* $data['main_view']['new_member_no']		= $new_member_no; */
			$data['main_view']['content']			= 'CoreMember/FormAddCoreMemberUi_view';
			$this->load->view('MainPage_view',$data);
		}

		public function CoreMemberOutList(){
			$auth 		= $this->session->userdata('auth');

			$list = $this->CoreMember_model->get_datatables_member_out();

			
			$membership 		= $this->configuration->MembershipStatus();
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = $membership[$customers->data_state];
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all_member_out(),
							"recordsFiltered" => $this->CoreMember_model->count_filtered_member_out(),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function getCoreCity(){
			$province_id 		= $this->uri->segment(3);
			
			$item = $this->CoreMember_model->getCoreCity($province_id);
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
			
			$item = $this->CoreMember_model->getCoreKecamatan($city_id);
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
			
			$item = $this->CoreMember_model->getCoreKelurahan($kecamatan_id);
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
			
			$item = $this->CoreMember_model->getCoreDusun($kelurahan_id);
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
		
		public function processAddCoreMember(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$member_password = rand();

			$data = array(
				'branch_id'					=> $auth['branch_id'],
				'member_no'					=> $this->input->post('member_no', true),
				'member_name'				=> $this->input->post('member_name', true),
				'member_gender'				=> $this->input->post('member_gender', true),
				'province_id'				=> $this->input->post('province_id', true),
				'city_id'					=> $this->input->post('city_id', true),
				'kecamatan_id'				=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'				=> $this->input->post('kelurahan_id', true),
				'dusun_id'					=> $this->input->post('dusun_id', true),
				'member_job'				=> $this->input->post('member_job', true),
				'member_identity'			=> $this->input->post('member_identity', true),
				'member_place_of_birth'		=> $this->input->post('member_place_of_birth', true),
				'member_date_of_birth'		=> tgltodb($this->input->post('member_date_of_birth', true)),
				'member_address'			=> $this->input->post('member_address', true),
				'member_phone'				=> $this->input->post('member_phone', true),
				'member_identity_no'		=> $this->input->post('member_identity_no', true),
				'member_character'			=> $this->input->post('member_character', true),
				'member_postal_code'		=> $this->input->post('member_postal_code', true),
				'member_mother'				=> $this->input->post('member_mother', true),
				'member_token'				=> $this->input->post('member_token', true),
				'member_password_default'	=> $member_password,
				'member_password'			=> md5($member_password),
				'member_register_date'		=> date('Y-m-d H:i:s'),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);


			// print_r($data);exit;
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_place_of_birth', 'Tempat Lahir', 'required');
			$this->form_validation->set_rules('member_date_of_birth', 'Tanggal Lahir', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('member_phone', 'Nomor Telp', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$membertoken = $this->CoreMember_model->getMemberToken($data['member_token']);

			
			if($this->form_validation->run()==true){
				if($membertoken == 0){
					if($this->CoreMember_model->insertCoreMember($data)){
						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.CoreMember.processAddCoreMember',$auth['user_id'],'Add New Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Sukses
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
						$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember');
					}
				} else {
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Anggota Tidak Berhasil - Data Anggota Sudah Ada
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember/addCoreMember');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/addCoreMember');
			}
		}

		public function editCoreMember(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']			= $this->CoreMember_model->getCoreMember_Detail($member_id);

			$data['main_view']['coreprovince']			= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']			= create_double($this->CoreMember_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']			= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']		= $this->configuration->MemberCharacter();	

			$data['main_view']['familyrelationship']	= $this->configuration->FamilyRelationship();	

			$data['main_view']['content']				= 'CoreMember/FormEditCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		public function createPasswordCoreMember2(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']			= $this->CoreMember_model->getCoreMember_Detail($member_id);	

			$data['main_view']['content']				= 'CoreMember/FormCreatePasswordCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		public function createPasswordCoreMember(){
			$auth = $this->session->userdata('auth');
			$member_id 	= $this->uri->segment(3);

			/* print_r("create passrowr");
			exit; */

			
			$client     = new GuzzleHttp\Client();
			$url        = 'https://www.ciptapro.com/madani-api/api/create_password_member';
			try {
				$response 		= $client->request( 'GET', $url, [] );
				$status_code 	= $response->getStatusCode();
				$response_data 	= $response->getBody()->getContents();

				/* print_r("response_data ");
				print_r($response_data);
				exit; */
				
				if($status_code == 201){


				}
			} catch (GuzzleHttp\Exception\BadResponseException $e) {
				#guzzle repose for future use
				$response = $e->getResponse();
				$responseBodyAsString = $response->getBody()->getContents();
				print_r($responseBodyAsString);
				$msg = "<div class='alert alert-danger alert-dismissable'>Reset Password Gagal</div>";
				$this->session->set_userdata('message',$msg);
			}

			/* print_r("response_data ");
			print_r($response_data);
			exit; */

			$data['main_view']['createpassword']		= json_decode($response_data, true);

			$data['main_view']['coremember']			= $this->CoreMember_model->getCoreMember_Detail($member_id);	

			$data['main_view']['content']				= 'CoreMember/FormCreatePasswordCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		public function openBlockCoreMember(){
			$member_id 	= $this->uri->segment(3);
			$client     = new GuzzleHttp\Client();
			$auth 		= $this->session->userdata('auth');
			$url        = 'https://www.ciptapro.com/madani-api/api/member/open/'.$member_id.'/'.$auth['user_id'];
			try {
				$response = $client->request( 'GET', $url, [] );
				$status_code = $response->getStatusCode();
				$response_data = $response->getBody()->getContents();
				
				$msg = "<div class='alert alert-success alert-dismissable'>  
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
					Buka Block Sukses
				</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember');
			} catch (GuzzleHttp\Exception\BadResponseException $e) {
				#guzzle repose for future use
				$response = $e->getResponse();
				$responseBodyAsString = $response->getBody()->getContents();
				print_r($responseBodyAsString);
				$msg = "<div class='alert alert-danger alert-dismissable'>Buka Block Gagal</div>";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember');
			}
		}

		public function resetPasswordCoreMember(){
			$auth = $this->session->userdata('auth');

			$member_no 	= $this->uri->segment(3);
			$member_id 	= $this->uri->segment(4);
			$client     = new GuzzleHttp\Client();
			$url        = 'https://www.ciptapro.com/madani-api/api/member/reset_password/'.$member_no.'/'.$member_id.'/'.$auth['user_id'];
			try {
				$response = $client->request( 'GET', $url, [] );
				$status_code = $response->getStatusCode();
				$response_data = $response->getBody()->getContents();
				
				if($status_code == 201){
					/* $client     = new GuzzleHttp\Client();
					$url        = 'https://www.ciptapro.com/madani-api/api/log-reset-password';
					try {
						# guzzle post request example with form parameter
						$response = $client->request( 'POST', $url, [ 
							'form_params' 
									=> [ 
									'user_id' => $auth['user_id'],
									'member_id' => $member_id, 
									'member_no' => $member_no, 
									] 
								]
							);
					}catch (GuzzleHttp\Exception\BadResponseException $e) {
						#guzzle repose for future use
						$response = $e->getResponse();
						$responseBodyAsString = $response->getBody()->getContents();
						print_r($responseBodyAsString);
					} */
				
					$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Reset Password Sukses
					</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember');
				}
			} catch (GuzzleHttp\Exception\BadResponseException $e) {
				#guzzle repose for future use
				$response = $e->getResponse();
				$responseBodyAsString = $response->getBody()->getContents();
				print_r($responseBodyAsString);
				$msg = "<div class='alert alert-danger alert-dismissable'>Reset Password Gagal</div>";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember');
			}
		}

		public function processCreatePasswordCoreMember(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'member_id'						=> $this->input->post('member_id', true),
				'member_no'						=> $this->input->post('member_no', true),
				'branch_id'						=> $this->input->post('branch_id', true),
				'member_name'					=> $this->input->post('member_name', true),
				'password'						=> $this->input->post('password', true),
				'password_transaksi'			=> $this->input->post('password_transaksi', true),
				'member_phone'					=> $this->input->post('member_phone', true),
				'user_id'						=> $auth['user_id'],
			);

			$expired_on = date("Y-m-d H:i:s", strtotime('+1 hours'));

			/* print_r("new_time ");
			print_r($new_time);
			exit; */

			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('password_transaksi', 'Password Transaksi', 'required');
			$this->form_validation->set_rules('member_phone', 'No HP', 'required');
			
			if($this->form_validation->run()==true){
				$client     = new GuzzleHttp\Client();
				$url        = 'https://www.ciptapro.com/madani-api/api/register';
				try {
					# guzzle post request example with form parameter
					$response = $client->request( 'POST', $url, [ 
												'form_params' 
														=> [ 
															'member_id' 			=> $data["member_id"],
															'member_no' 			=> $data["member_no"],
															'branch_id' 			=> $data["branch_id"],
															'member_name' 			=> $data["member_name"],
															'password' 				=> $data["password"],
															'password_transaksi' 	=> $data["password_transaksi"],
															'member_phone' 			=> $data["member_phone"], 
															'member_user_status' 	=> 0, 
															'expired_on' 			=> $expired_on, 
														] 
												]
												);
					#guzzle repose for future use
					// echo $response->getStatusCode(); // 200
					// echo $response->getReasonPhrase(); // OK
					// echo $response->getProtocolVersion(); // 1.1
					$status_code = $response->getStatusCode();
					$response_data = $response->getBody()->getContents();
					if($status_code == 201){
						$client     = new GuzzleHttp\Client();
						$url        = 'https://www.ciptapro.com/madani-api/api/log-create-password';
						try {
							# guzzle post request example with form parameter
							$response = $client->request( 'POST', $url, [ 
														'form_params' 
																=> [ 
																'user_id'   => $data["user_id"],
																'member_id' => $data["member_id"], 
																'member_no' => $data["member_no"], 
																] 
														]
														);
						}catch (GuzzleHttp\Exception\BadResponseException $e) {
							#guzzle repose for future use
							$response = $e->getResponse();
							$responseBodyAsString = $response->getBody()->getContents();
							print_r($responseBodyAsString);
						}
						$this->CoreMember_model->updatePPOBStatus($data['member_id']);
						$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Buat Password Anggota Sukses
						</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember');
					} else if ($status_code == 200){
						$this->session->set_userdata('editmachine',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>Duplicate Data</div>";
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember/createPasswordCoreMember/'.$data['member_id']);
					}
				} catch (GuzzleHttp\Exception\BadResponseException $e) {
					#guzzle repose for future use
					$response = $e->getResponse();
					$responseBodyAsString = $response->getBody()->getContents();
					print_r($responseBodyAsString);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/createPasswordCoreMember/'.$data['member_id']);
			}				
		}

		public function updatePhoneCoreMember(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']			= $this->CoreMember_model->getCoreMember_Detail($member_id);	

			$data['main_view']['content']				= 'CoreMember/FormUpdatePhoneCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		

		public function processEditCoreMember(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'member_no'								=> $this->input->post('member_no', true),
				'branch_id'								=> $auth['branch_id'],
				'member_name'							=> $this->input->post('member_name', true),
				'member_address'						=> $this->input->post('member_address', true),
				'city_id'								=> $this->input->post('city_id', true),
				'kecamatan_id'							=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'							=> $this->input->post('kelurahan_id', true),
				'dusun_id'								=> $this->input->post('dusun_id', true),
				'member_character'						=> $this->input->post('member_character', true),
				'member_mother'							=> $this->input->post('member_mother', true),
				'member_heir'							=> $this->input->post('member_heir', true),
				'member_family_relationship'			=> $this->input->post('member_family_relationship', true),
				'member_identity_no'					=> $this->input->post('member_identity_no', true),
				'member_phone'							=> $this->input->post('member_phone', true),
				
			);

			// print_r($data);exit;
				/* 'member_principal_savings'				=> $this->input->post('member_principal_savings', true),
				'member_special_savings'				=> $this->input->post('member_special_savings', true),
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings', true),
				'member_special_savings_last_balance'	=> $this->input->post('member_special_savings', true),
				'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings', true), */

			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			
			if($this->form_validation->run()==true){
				if($this->CoreMember_model->updateCoreMember($data)){
					$savings_member_detail_id = $this->CoreMember_model->getSavingsMemberDetailID($data['member_id']);

					$datadetail = array (
						'savings_member_detail_id'		=> $savings_member_detail_id,
						'member_id'						=> $data['member_id'],
						'last_balance_principal'		=> $this->input->post('opening_member_principal_savings', true),
						'last_balance_special'			=> $this->input->post('opening_member_special_savings', true),
						'last_balance_mandatory'		=> $this->input->post('opening_member_mandatory_savings', true),
					);

					$this->CoreMember_model->updateOpeningMemberDetail($datadetail);


					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember/editCoreMember/'.$data['member_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember/editCoreMember/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/editCoreMember/'.$data['member_id']);
			}				
		}

		public function function_elements_edit(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('editCoreMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('editCoreMember-'.$unique['unique'],$sessions);
		}

		public function reset_edit(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('editCoreMember-'.$unique['unique']);
			redirect('CoreMember/editCoreMember/'.$member_id);
		}

		public function reset_edit_create_password(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('createPasswordCoreMember-'.$unique['unique']);
			redirect('CoreMember/createPasswordCoreMember/'.$member_id);
		}

		public function reset_edit_member(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('editCoreMember-'.$unique['unique']);
			redirect('CoreMember/editCoreMemberSavings/'.$member_id);
		}

		public function editCoreMemberSavings(){
			$member_id 	= $this->uri->segment(3);
			$unique = $this->session->userdata('unique');
			$token 	= $this->session->userdata('coremembertokenedit-'.$unique['unique']);

			if(empty($token)){
				$token = md5(rand());
				$this->session->set_userdata('coremembertokenedit-'.$unique['unique'], $token);
			}

			$data['main_view']['coremember']		= $this->CoreMember_model->getCoreMember_Detail($member_id);

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']		= create_double($this->CoreMember_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']		= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	

			$data['main_view']['content']			= 'CoreMember/FormEditCoreMemberSavings_view';

			$this->load->view('MainPage_view',$data);
		}

		public function getListCoreMemberEdit(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$branch_id = '';
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = '<a href="'.base_url().'CoreMember/editCoreMemberSavings/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}	

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->CoreMember_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}	
		
		
		public function processEditCoreMemberSavings(){
			$auth = $this->session->userdata('auth');

			$username = $this->CoreMember_model->getUserName($auth['user_id']);

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

			$member_token_edit = $this->CoreMember_model->getMemberTokenEdit($data['member_token_edit']);
			
			if($this->form_validation->run()==true){
				if($member_token_edit->num_rows() == 0){
					if($this->CoreMember_model->updateCoreMember($data)){
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
								'savings_member_detail_token'	=> $data['member_token_edit'],
							);

							if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
								$transaction_module_code 	= "AGT";

								$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
								$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
								$coremember 				= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
									
								$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

								//-------------------------Jurnal Cabang----------------------------------------------------
								
								$data_journal_cabang = array(
									'branch_id'						=> $auth['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $coremember['member_id'],
									'transaction_journal_no' 		=> $coremember['member_no'],
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);

								$journal_voucher_id = $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

								$preferencecompany 	= $this->CoreMember_model->getPreferenceCompany();

								if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){			

									$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_debet = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

									$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_central_capital_id'],
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
								} else {
									$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

									$data_debit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_central_capital_id'],
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);

									$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);

									
								}
								

								//-------------------------Jurnal Pusat----------------------------------------------------

								$data_journal_pusat = array(
									'branch_id'						=> $preferencecompany['central_branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_token'			=> $data['member_token_edit'].$preferencecompany['central_branch_id'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $coremember['member_id'],
									'transaction_journal_no' 		=> $coremember['member_no'],
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->CoreMember_model->insertAcctJournalVoucher($data_journal_pusat);

								$journal_voucher_pusat_id 	= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

								$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

								if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

									$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

									$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

									$data_debet = array (
										'journal_voucher_id'			=> $journal_voucher_pusat_id,
										'account_id'					=> $account_rak_id,
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
											'account_id_status'				=> 1,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
											'account_id_status'				=> 1,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_credit_amount'	=> $data['member_special_savings'],
											'account_id_status'				=> 1,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}
								} else {
									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
											'account_id_status'				=> 0,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
											'account_id_status'				=> 0,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_debit_amount'	=> $data['member_special_savings'],
											'account_id_status'				=> 0,
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

									$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_pusat_id,
										'account_id'					=> $account_rak_id,
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}




						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Sukses
								</div> ";

						$unique = $this->session->userdata('unique');
						$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember/processPrinting/'.$data['member_id']);
					}else{
						$msg = "<div class='alert alert-danger alert-dismissable'> 
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember/editCoreMemberSavings/'.$data['member_id']);
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
							'savings_member_detail_token'	=> $data['member_token_edit'],
						);

						$savings_member_detail_token = $this->CoreMember_model->getSavingsMemberDetailToken($data['member_token_edit']);

						if($savings_member_detail_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail);
						}
						
						$transaction_module_code = "AGT";

						$transaction_module_id 	= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						$coremember 			= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
							
						$journal_voucher_period = date("Ym", strtotime($coremember['member_register_date']));
						
						//-------------------------Jurnal Cabang----------------------------------------------------
							
						$data_journal_cabang = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
							'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$journal_voucher_token = $this->CoreMember_model->getJournalVoucherToken($data_journal_cabang['journal_voucher_token']);
						
						if($journal_voucher_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);
						}


						$journal_voucher_id = $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany = $this->CoreMember_model->getPreferenceCompany();


						if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){					

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);


							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);
							}

							$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_central_capital_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						} else {
							$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_central_capital_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
							}


							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						}

						//-------------------------Jurnal Pusat----------------------------------------------------

						$data_journal_pusat = array(
							'branch_id'						=> $preferencecompany['central_branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
							'journal_voucher_token'			=> $data['member_token_edit'].$preferencecompany['central_branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						
						$journal_voucher_token = $this->CoreMember_model->getJournalVoucherToken($data_journal_pusat['journal_voucher_token']);
						
						if($journal_voucher_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctJournalVoucher($data_journal_pusat);
						}

						$journal_voucher_pusat_id 	= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

						if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

							$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

							$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_pusat_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);
							}						

							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_credit_amount'	=> $data['member_special_savings'],
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}
						} else {
							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_debit_amount'	=> $data['member_special_savings'],
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

							$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_pusat_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'].' '.$coremember['member_no'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
							);

							if($journal_voucher_item_token->num_rows()==0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
					}
					



					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember/processPrinting/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/editCoreMemberSavings/'.$data['member_id']);
			}				
		}

		public function processPrinting(){
			$auth 						= $this->session->userdata('auth');
			$member_id 					= $this->uri->segment(3);
			$acctsavingsmemberdetail	= $this->CoreMember_model->getLastAcctSavingsMemberDetail($member_id);
			$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

			
			if($acctsavingsmemberdetail['mutation_id'] == $preferencecompany['cash_deposit_id']){
				$keperluan = 'SETORAN TUNAI';
				$keterangan = 'Telah diterima uang dari';
			} else if($acctsavingsmemberdetail['mutation_id'] == $preferencecompany['cash_withdrawal_id']){
				$keperluan = 'PENARIKAN TUNAI';
				$keterangan = 'Telah diserahkan uang kepada';
			}

			$total = $acctsavingsmemberdetail['principal_savings_amount'] + $acctsavingsmemberdetail['special_savings_amount'] + $acctsavingsmemberdetail['mandatory_savings_amount'];

			// print_r($keterangan);

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			// print_r($preferencecompany['logo_koperasi']);exit;

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				<tr>
					<td rowspan=\"2\" width=\"20%\">".$img."</td>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI ".$keperluan." ANGGOTA</div></td>
				</tr>
				<tr>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = 
			$keterangan .":
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_no']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$keperluan."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Pokok</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsmemberdetail['principal_savings_amount'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Khusus</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsmemberdetail['special_savings_amount'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Wajib</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\"><u>: Rp. &nbsp;".number_format($acctsavingsmemberdetail['mandatory_savings_amount'], 2)."</u></div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($total, 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($total)."</div></td>
				</tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">".$this->CoreMember_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
				</tr>
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
				</tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi_Simpanan_Anggota_'.$acctsavingsmemberdetail['member_name'].'.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function deleteCoreMember(){
			if($this->CoreMember_model->deleteCoreMember($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.deleteCoreMember',$auth['user_id'],'Delete Member');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember');
			}
		}

		public function updateCoreMemberStatus(){	
			$data['main_view']['content']			= 'CoreMember/ListUpdateCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getUpdateCoreMemberStatusList(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$branch_id = "";
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);

			// print_r($list);exit;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$preferencecompany = $this->CoreMember_model->getPreferenceCompany();

				$acctsavingsaccount = $this->CoreMember_model->getAcctSavingsAccount($preferencecompany['principal_savings_id'], $customers->member_id);

				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = $memberstatus[$customers->member_status];
				$row[] = $membercharacter[$customers->member_character];
				$row[] = $customers->member_phone;
				$row[] = number_format($customers->member_principal_savings, 2);
				$row[] = number_format($customers->member_special_savings, 2);
				$row[] = number_format($customers->member_mandatory_savings, 2);
				// if($acctsavingsaccount->num_rows() > 0 ){
					if($customers->member_status == 0){
						$row[] = '<a href="'.base_url().'CoreMember/processUpdateCoreMemberStatus/'.$customers->member_id.'" onClick="javascript:return confirm(\'Yakin status anggota akan diupdate ?\')" class="btn default btn-xs purple" role="button"><i class="fa fa-edit"></i> Update</a>';
					} else {
						$row[] = '';
					}
				// } else {
				// 	$row[] = '';
				// }

				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function processUpdateCoreMemberStatus(){
			if($this->CoreMember_model->updateCoreMemberStatus($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1006','Application.CoreMember.processUpdateCoreMemberStatus',$auth['user_id'],'Update Member Status');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Update Status Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/updateCoreMemberStatus');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Update Status Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/updateCoreMemberStatus');
			}
		}

		public function printBookCoreMember(){
			$auth = $this->session->userdata('auth');

			/* $coremember		= $this->CoreMember_model->getDataCoreMember($auth['branch_id']);

			print_r("coremember ");
			print_r($coremember);
			exit; */

			/* $data['main_view']['coremember']		= $this->CoreMember_model->getDataCoreMember($auth['branch_id']); */
			$data['main_view']['memberstatus']		= $this->configuration->MemberStatus();	
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['content']			= 'CoreMember/ListPrintBookCoreMember_view';
			$this->load->view('MainPage_view', $data);
		}

		public function getListCoreMemberBook(){
			$auth = $this->session->userdata('auth');
			$data_state = 0;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$membership 		= $this->configuration->MembershipStatus();
			$branch_id 			= "";
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
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
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}	

		public function processPrintCoverBookCoreMember(){
			$member_id 			= $this->uri->segment(3);
			$coremember			= $this->CoreMember_model->getCoreMember_Detail($member_id);
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

			$data['main_view']['coremember']					= $this->CoreMember_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsmemberdetail']		= $this->CoreMember_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);	

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
			$branch_id = "";
			$list = $this->CoreMember_model->get_datatables($data_state, $branch_id);
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
							"recordsTotal" => $this->CoreMember_model->count_all($data_state, $branch_id),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($data_state, $branch_id),
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
				$coremember				= $this->CoreMember_model->getCoreMember_Detail($member_id);
				$mutasicoremember		= $this->CoreMember_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);


				if(empty($member_last_number) || $member_last_number == 0){
					$no = 1;
				} else {
					$no = $member_last_number + 1;
				}

				if(empty($mutasicoremember)){
					$last_balance = $coremember['member_principal_savings_last_balance'] + $coremember['member_special_savings_last_balance'] + $coremember['member_mandatory_savings_last_balance'];

					$data[0] = array (
						'no'							=> $no,
						'savings_member_detail_id'		=> '',
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
							'savings_member_detail_id'		=> $val['savings_member_detail_id'],
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
						'savings_member_detail_id'		=> $v['savings_member_detail_id'],
						'member_id'						=> $v['member_id'],
						'savings_print_status'			=> 1,
						'member_last_number'			=> $v['no'],
					);

					$this->CoreMember_model->updatePrintMutationStatus($update_data);
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

		public function view(){
			$baris 	= $this->uri->segment(3);
			$key1 	= $this->uri->segment(4);

			/*$sisa = 5052 % 500;
			print_r($sisa);exit;*/

			$auth 	= $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-coremembermasterdata');

			if(empty($sesi['branch_id']) || $sesi['branch_id'] == ''){
				$branch_id 	= $auth['branch_id'];
			} else {
				$branch_id 	= $sesi['branch_id'];
			}

			$data_state = 0;

		
			
			$list 	= $this->CoreMember_model->get_datatables($data_state, $branch_id);

			
			$no=0;
			foreach ($list as $key => $member) {		
				$no++;
				$data[] = array(
					'no'										=> $no,
					'member_no'									=> $member->member_no,
					'member_name' 								=> $member->member_name,
					'member_address'							=> $member->member_address,
					'member_place_of_birth'						=> $member->member_place_of_birth,
					'member_date_of_birth'						=> $member->member_date_of_birth,
					'member_status'								=> $member->member_status,
					'member_character'							=> $member->member_character,
					'member_phone'								=> $member->member_phone,
					'member_gender'								=> $member->member_gender,
					'member_job'								=> $member->member_job,
					'member_identity'							=> $member->member_identity,
					'member_identity_no'						=> $member->member_identity_no,
					'member_principal_savings_last_balance'		=> $member->member_principal_savings_last_balance,
					'member_special_savings_last_balance'		=> $member->member_special_savings_last_balance,
					'member_mandatory_savings_last_balance'		=> $member->member_mandatory_savings_last_balance,
				);
			}

			$sisa = $no % 1000;

			/* print_r($data);exit; */

			for ($i=0; $i < $baris ; $i++) {
				
				if($i == $baris){
					$rows = $sisa;
				} else {
					$rows = 1000;
				}

				$array_terpecah[$i] = array_splice($data, 0, $rows);

				
			}

			$datacetak = $array_terpecah[$key1];

			//print_r($datacetak);exit;

			$this->exportMasterDataCoreMember($datacetak);
		}

		public function exportMasterDataCoreMember($data){
			$auth = $this->session->userdata('auth'); 	
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$memberidentity 	= $this->configuration->MemberIdentity();

			$coremember = count($data);

			/* print_r("membergender ");
			print_r($membergender);exit; */

			if(count($data) >= 0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									->setLastModifiedBy("SIS")
									->setTitle("Master Data Anggota")
									->setSubject("")
									->setDescription("Master Data Anggota")
									->setKeywords("Master, Data, Anggota")
									->setCategory("Master Data Anggota");
									
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
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:Q1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:Q3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:Q3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:Q3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Anggota");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Tempat Lahir");
				$this->excel->getActiveSheet()->setCellValue('G3',"Tanggal lahir");
				$this->excel->getActiveSheet()->setCellValue('H3',"Status");
				$this->excel->getActiveSheet()->setCellValue('I3',"Sifat");
				$this->excel->getActiveSheet()->setCellValue('J3',"No. Telp");
				$this->excel->getActiveSheet()->setCellValue('K3',"Jenis Kelamin");
				$this->excel->getActiveSheet()->setCellValue('L3',"Pekerjaan");
				$this->excel->getActiveSheet()->setCellValue('M3',"Identitas");
				$this->excel->getActiveSheet()->setCellValue('N3',"No. Identitas");
				$this->excel->getActiveSheet()->setCellValue('O3',"Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('P3',"Simpanan Khusus");
				$this->excel->getActiveSheet()->setCellValue('Q3',"Simpanan Wajib");
				
				$j=4;
				$no=0;
				
				foreach($data as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':P'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
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
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['member_no'] ,PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_place_of_birth']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, tgltoview($val['member_date_of_birth']));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $memberstatus[$val['member_status']]);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $membercharacter[$val['member_character']]);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['member_phone']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $membergender[$val['member_gender']]);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['member_job']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $memberidentity[$val['member_identity']]);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $val['member_identity_no']);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, number_format($val['member_principal_savings_last_balance'], 2));
						$this->excel->getActiveSheet()->setCellValue('P'.$j, number_format($val['member_special_savings_last_balance'], 2));
						$this->excel->getActiveSheet()->setCellValue('Q'.$j, number_format($val['member_mandatory_savings_last_balance'], 2));	
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Anggota.xls';
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

		public function addCoreMemberUtility(){
			$unique = $this->session->userdata('unique');
			$auth 	= $this->session->userdata('auth');
			$token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);

			if(empty($token)){
				$member_token = md5(rand());
				$this->session->set_userdata('coremembertoken-'.$unique['unique'], $member_token);
			}

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');
			$data['main_view']['coreidentity']		= create_double($this->CoreMember_model->getCoreIdentity(),'identity_id', 'identity_name');
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	
			$data['main_view']['content']			= 'CoreMember/FormAddCoreMemberUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processAddCoreMemberUtility(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$member_password = rand();

			$data = array(
				'branch_id'					=> $auth['branch_id'],
				'member_no'					=> $this->input->post('member_no', true),
				'member_name'				=> $this->input->post('member_name', true),
				'member_gender'				=> $this->input->post('member_gender', true),
				'province_id'				=> $this->input->post('province_id', true),
				'city_id'					=> $this->input->post('city_id', true),
				'kecamatan_id'				=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'				=> $this->input->post('kelurahan_id', true),
				'dusun_id'					=> $this->input->post('dusun_id', true),
				'member_job'				=> $this->input->post('member_job', true),
				'member_identity'			=> $this->input->post('member_identity', true),
				'member_place_of_birth'		=> $this->input->post('member_place_of_birth', true),
				'member_date_of_birth'		=> tgltodb($this->input->post('member_date_of_birth', true)),
				'member_address'			=> $this->input->post('member_address', true),
				'member_phone'				=> $this->input->post('member_phone', true),
				'member_identity_no'		=> $this->input->post('member_identity_no', true),
				'member_character'			=> $this->input->post('member_character', true),
				'member_postal_code'		=> $this->input->post('member_postal_code', true),
				'member_mother'				=> $this->input->post('member_mother', true),
				'member_token'				=> $this->input->post('member_token', true),
				'member_password_default'	=> $member_password,
				'member_password'			=> md5($member_password),
				'member_register_date'		=> date('Y-m-d H:i:s'),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);


			// print_r($data);exit;
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_place_of_birth', 'Tempat Lahir', 'required');
			$this->form_validation->set_rules('member_date_of_birth', 'Tanggal Lahir', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('member_phone', 'Nomor Telp', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$membertoken = $this->CoreMember_model->getMemberToken($data['member_token']);

			
			if($this->form_validation->run()==true){
				if($membertoken == 0){
					if($this->CoreMember_model->insertCoreMember($data)){
						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.CoreMember.processAddCoreMember',$auth['user_id'],'Add New Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Sukses
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
						$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('CoreMember');
					}
				} else {
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Anggota Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMember/addCoreMemberUtility');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/addCoreMember');
			}
		}

		public function SyncronizeData(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-coremembermutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			// print_r($sesi);exit;

			if(!empty($sesi['member_id'])){
				$datalog = array (
					'member_syncronize_log_date' 		=> date('Y-m-d'),
					'member_syncronize_log_start_date'	=> $sesi['start_date'],
					'member_syncronize_log_end_date'	=> $sesi['end_date'],
					'member_id'							=> $sesi['member_id'],
					'branch_id'							=> $auth['branch_id'],
					'created_id'						=> $auth['user_id'],
					'created_on'						=> date('Y-m-d H:i:s'),
				);

				if($this->CoreMember_model->insertCoreMemberSyncronizeLog($datalog)){
					$opening_balance 			= $this->CoreMember_model->getOpeningBalance($datalog['member_id'], $datalog['member_syncronize_log_start_date']);

					if(!is_array($opening_balance)){
						$opening_date 					= $this->CoreMember_model->getLastDate($datalog['member_id'], $datalog['member_syncronize_log_start_date']);

						$opening_balance 				= $this->CoreMember_model->getLastBalance($datalog['member_id'], $opening_date);
					}

					$opening_balance_principal 		= $this->CoreMember_model->getLastBalancePrincipal($datalog['member_id'], $datalog['member_syncronize_log_start_date']);

					$opening_balance_special 		= $this->CoreMember_model->getLastBalanceSpecial($datalog['member_id'], $datalog['member_syncronize_log_start_date']);

					$opening_balance_mandatory 		= $this->CoreMember_model->getLastBalanceMandatory($datalog['member_id'], $datalog['member_syncronize_log_start_date']);

					$acctsavingsmemberdetail 		= $this->CoreMember_model->getAcctSavingsMemberDetail($datalog['member_id'], $datalog['member_syncronize_log_start_date'], $datalog['member_syncronize_log_end_date']);


					foreach ($acctsavingsmemberdetail as $key => $val) {
						$mutation_function 			= $this->CoreMember_model->getMutationFunction($val['mutation_id']);

						if($mutation_function == '+'){
							$last_balance_principal 	= $opening_balance_principal + $val['principal_savings_amount'];
							$last_balance_special 		= $opening_balance_special + $val['special_savings_amount'];
							$last_balance_mandatory 	= $opening_balance_mandatory + $val['mandatory_savings_amount'];

							
						} else if($mutation_function == '-'){
							$last_balance_principal 	= $opening_balance_principal - $val['principal_savings_amount'];
							$last_balance_special 		= $opening_balance_special - $val['special_savings_amount'];
							$last_balance_mandatory 	= $opening_balance_mandatory - $val['mandatory_savings_amount'];
						}
						
						$last_balance 					= $last_balance_principal + $last_balance_special + $last_balance_mandatory;

						$newdata = array (
							'savings_member_detail_id'		=> $val['savings_member_detail_id'],
							'member_id'						=> $val['member_id'],
							'opening_balance'				=> $opening_balance,
							'last_balance_principal'		=> $last_balance_principal,
							'last_balance_special'			=> $last_balance_special,
							'last_balance_mandatory'		=> $last_balance_mandatory,
							'last_balance'					=> $last_balance,
						);

						$opening_balance 					= $last_balance;
						$opening_balance_principal 			= $last_balance_principal;
						$opening_balance_special			= $last_balance_special;
						$opening_balance_mandatory 			= $last_balance_mandatory;

						if($this->CoreMember_model->updateAcctSavingsMemberDetail($newdata)){
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
											Syncronize Data Sukses
										</div> ";
							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Syncronize Data Gagal
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('CoreMember/printMutationCoreMember');
							break;
						}

						print_r($newdata);
						print_r("<BR>");
					}
					// exit;
					redirect('CoreMember/printMutationCoreMember');

				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Syncronize Data Gagal
							</div> ";
					$sesi = $this->session->userdata('unique');
					redirect('CoreMember/printMutationCoreMember');
				}

			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							No. Rekening Simpanan Masih Kosong
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('CoreMember/printMutationCoreMember');
			}
		}

		public function getLogTemp(){
			$code 		= $this->input->post('code',true);
			$client     = new GuzzleHttp\Client();
			$url        = 'http://127.0.0.1:8000/api/log-temp/'.$code;
			try {
				$response = $client->request( 'GET', $url, [] );
				$status_code = $response->getStatusCode();
				$response_data = $response->getBody()->getContents();
				$jsondata = json_decode($response_data);
				
				$msg = "<div class='alert alert-success alert-dismissable'>  
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
					".$jsondata->message."
				</div> ";
				$this->session->set_userdata('message',$msg);

				$data['main_view']['logtemp']		= $jsondata->log_temp;
				$data['main_view']['content']		= 'CoreMember/ListLogTemp_view';
				$this->load->view('MainPage_view',$data);
			} catch (GuzzleHttp\Exception\BadResponseException $e) {
				#guzzle repose for future use
				$response = $e->getResponse();
				$responseBodyAsString = $response->getBody()->getContents();
				print_r($responseBodyAsString);
				$msg = "<div class='alert alert-danger alert-dismissable'>
				API Endpoint Error Call
				</div>";
				$this->session->set_userdata('message',$msg);
			}
		}
	}
?>