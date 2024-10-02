<?php
	Class User extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('User_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->library('fungsi');
			$this->load->database('default');
		}
		
		public function index(){
			$this->lists();
		}
		
		public function lists(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['user']		= $this->User_model->get_list($auth['branch_status'], $auth['branch_id']);
			$data['main_view']['content']	= 'User/ListUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function Add(){
			$auth = $this->session->userdata('auth');
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= create_double($this->User_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['group']				= create_double($this->User_model->getGroup($auth['branch_status']),'user_group_level','user_group_name');
			$data['main_view']['content']			= 'User/FormAddUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processAdduser(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$branch_id = $this->input->post('branch_id',true);
			} else {
				$branch_id = $auth['branch_id'];
			}

			$data = array(
				'username' 				=> str_replace(";","",$this->input->post('username',true)),
				'password' 				=> md5($this->input->post('password',true)),
				'user_group_id'			=> $this->input->post('user_group_id',true),
				'branch_id'				=> $branch_id,
				'branch_status'			=> $this->input->post('branch_status',true),
				// 'database' 				=> $this->input->post('database',true),
				// 'customer_company_code' => $this->input->post('customer_company_code',true),
				'log_stat'		=> 'off'
			);
			$this->form_validation->set_rules('username', 'username', 'required|is_unique[system_user.username]');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->User_model->saveNewuser($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.user.processAdduser',$auth['username'],'Add New user Account');
					$msg = "<div class='alert alert-success alert-dismissable'>                  								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>            
								Add Data user Successfully
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('User/Add');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
								Add Data user UnSuccessful
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('User/Add');
				}
			}else{
				$data['password']='';
				$this->session->set_userdata('Adduser',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('User/Add');
			}
		}
		
		function Edit(){
			$auth 							= $this->session->userdata('auth');

			$data['main_view']['group']		= create_double($this->User_model->getGroup($auth['branch_status']),'user_group_level','user_group_name');
			$data['main_view']['result']	= $this->User_model->getDetail($this->uri->segment(3));
			$data['main_view']['content']	= 'User/FormEditUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processEdituser(){
			$old_avatar 	= $this->input->post('old_avatar',true);
			$last_username 	= str_replace(";","",$this->input->post('last_username',true));
			$password 		= $this->input->post('password',true);
			$repassword 	= $this->input->post('re_password',true);
			
			$data = array(
				'user_id'		=> $this->input->post('user_id',true),
				'username' 		=> str_replace(";","",$this->input->post('username1',true)),
				// 'user_group_id' => $this->input->post('user_group_id',true),
				'password' 		=> $this->input->post('password',true),
				'log_stat'		=> $this->input->post('log_stat',true)
			);
			
			$old_data	= $this->User_model->getDetail($data['user_id']);
			$this->form_validation->set_rules('username', 'username', 'required');
			// $this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->User_model->cekuserNameExist($data['username']) || $last_username==$data['username']){
					if($password!=""){
						$data['password'] = $password;
						if($data['password']!=$repassword){
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
									Password do not Match!
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User/Edit/'.$data['user_id']);
							// break;
						}else{
							$data['password'] = md5($data['password']);
						}
					}
						if($this->User_model->saveEdituser($data)){
							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
							// $this->fungsi->set_change_log($old_data,$data,$auth['username'],$data['username']);
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user Successfully
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User/Edit/'.$data['user_id']);
						}else{
							$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user UnSuccessful
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User/Edit/'.$data['user_id']);
						}
					
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								username already exist !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('User/Edit/'.$data['user_id']);
				}
			}else{
				$data['password']='';
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('User/Edit/'.$data['user_id']);
			}
		}
		
		function defaultpassword(){
			$result = $this->User_model->getDetail($this->uri->segment(3));
			$pass = $this->User_model->getPassword();
			$old_avatar 	= $result['avatar'];
			$last_username 	= $result['username'];
			$password = $pass;
			$repassword = $pass;
			$data = array(
				'username' 		=> $result['username'],
				'user_group_id' => $result['user_group_id'],
				'branch_id' 	=> $result['branch_id'],
				// 'hak_akses' 	=> $result['hak_akses'],
				'log_stat'		=> $result['log_stat']
			);
			
			// if($this->form_validation->run()==true){
				// if($this->User_model->cekuserNameExist($data['username']) || $last_username==$data['username']){
					if($password!=""){
						$data['password'] = $password;
						if($data['password']!=$repassword){
							$msg = "<div class='alert alert-error'>                
									Password do not Match!
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User');
							// break;
						}else{
							$data['password'] = $data['password'];
						}
					}
					if($_FILES['user_avatar']['error'] == 0 && $_FILES['user_avatar']['size']>0){
						//upload and update the file
						$config['upload_path'] = './img/users/';
						$config['allowed_types'] = 'gif|jpg|png|jpeg';
						$config['overwrite'] = false;
						$config['remove_spaces'] = true;
						//$config['max_size']	= '100';// in KB

						$this->load->library('upload', $config);

						if ( ! $this->upload->do_upload('user_avatar')){
							$msg = "<div class='alert alert-error'>                
									".$this->upload->display_errors('', '')."
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User');
						} else {
							//Image Resizing
							$config['source_image'] = $this->upload->upload_path.$this->upload->file_name;
							$config['maintain_ratio'] = FALSE;
							$config['width'] = 50;
							$config['height'] = 50;

							$this->load->library('image_lib', $config);

							if ( ! $this->image_lib->resize()){
								$msg = "<div class='alert alert-error'>                
									".$this->upload->display_errors('', '')."
								</div> ";
								$this->session->set_userdata('message',$msg);
								redirect('User');
							} else {
									
									if(file_exists(FCPATH."img/users/".$old_avatar)){
									unlink(FCPATH."img/users/".$old_avatar);
								}
								$data['avatar'] = $this->upload->file_name;
								if($this->User_model->saveEdituser($data,$last_username)){
									$auth = $this->session->userdata('auth');
									$this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
									$msg = "<div class='alert alert-success'>                
												Reset Password Successfully
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('User');
								}else{
									$msg = "<div class='alert alert-error'>                
												Reset Password UnSuccessful
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('User');
								}
							}
						}
					} else {
						if($this->User_model->saveEdituser($data,$last_username)){
							$auth = $this->session->userdata('auth');
							$this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
							$msg = "<div class='alert alert-success'>                
										Reset Password Successfully
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User');
						}else{
							$msg = "<div class='alert alert-error'>                
										Reset Password UnSuccessful
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('User');
						}
					}
				// }else{
					// $msg = "<div class='alert alert-error'>                
								// username already exist !!!
							// </div> ";
					// $this->session->set_userdata('message',$msg);
					// redirect('user/Edit/'.$last_username);
				// }
			// }else{
				// $data['password']='';
				// $msg = validation_errors("<div class='alert alert-error'>", '</div>');
				// $this->session->set_userdata('message',$msg);
				// redirect('user/Edit/'.$last_username);
			// }
		}
		
		function delete(){
			if($this->User_model->delete($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1005','Application.user.delete',$auth['username'],'Delete user Account');
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data user Successfully
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('User');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data user UnSuccessful
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('User');
			}
		}
		
		function test(){
			echo $this->User_model->isThisMenuInGroup('1','99');
		}
	}
?>