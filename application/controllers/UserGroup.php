<?php
	Class UserGroup extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('UserGroup_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('fungsi');
		}
		
		public function index(){
			$this->lists();
		}
		
		public function lists(){
			$data['main_view']['usergroup']		= $this->UserGroup_model->get_list();
			$data['main_view']['content']	= 'UserGroup/ListUserGroup_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function Add(){
			$data['main_view']['result']	= '';
			$data['main_view']['content']	= 'UserGroup/FormAddUserGroup_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processAddUserGroup(){
			$data = array(
				'user_group_name' 		=> str_replace(";","",$this->input->post('user_group_name',true))
			);
			$this->form_validation->set_rules('user_group_name', 'Group Name', 'required|is_unique[system_user_group.user_group_name]|max_length[50]|filterspecialchar');
			if($this->form_validation->run()==true){
				if($this->UserGroup_model->saveNewGroup($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1006','Application.UserGroup.processAddUserGroup',$auth['username'],'Add New User Group');
					$level = $this->UserGroup_model->getMenuID($data['user_group_name']);
					foreach($_POST as $key=>$val){
						$tmp = explode("_",$key);
						if($tmp[1]=="FT"){
							$MM[$tmp[0]] = 1;
						}
					}
					
					if(count($MM)>0){
						$this->UserGroup_model->deleteMapping($level);
						foreach($MM as $key=>$val){
							$data2 = array(
								'user_group_level' 	=> $level,
								'id_menu'			=> $key
							);
							$this->UserGroup_model->saveMapping($data2);
						}
					
						$msg = "<div class='alert alert-success alert-dismissable'>                  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	
									Add Data User Group Successfully
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('UserGroup/Add');
					} else {
						$msg = "<div class='alert alert-danger alert-dismissable'>    
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>						
									New Group Created successfully, but menu privileges doesn't set. Please set menu privileges !!!
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('UserGroup/Edit/'.$level);
					}
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Add Data User Group UnSuccessful
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('UserGroup/Add');
				}
			}else{
				$this->session->set_userdata('AddUserGroup',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('UserGroup/Add');
			}
		}
		
		function Edit(){
			$data['main_view']['result']	= $this->UserGroup_model->getDetail($this->uri->segment(3));
			$data['main_view']['content']	= 'UserGroup/FormEditUserGroup_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processEditUserGroup(){
			$old_user_group_name = $this->input->post('old_user_group_name',true);
			$data = array(
				'user_group_id' 		=> $this->input->post('user_group_id',true),
				'user_group_name' 		=> str_replace(";","",$this->input->post('user_group_name',true))
			);
			$old_data = $this->UserGroup_model->getDetail($data['user_group_id']);
			$this->form_validation->set_rules('user_group_id', 'Group ID', 'required');
			$this->form_validation->set_rules('user_group_name', 'Group Name', 'required');
			if($this->form_validation->run()==true){
				if($this->UserGroup_model->cekGroupName($data['user_group_name']) || $old_user_group_name==$data['user_group_name']){
					foreach($_POST as $key=>$val){
						$tmp = explode("_",$key);
						if($tmp[1]=="FT"){
							$MM[$tmp[0]] = 1;
						}
					}
					if(count($MM)>0 && $this->UserGroup_model->UpdateGroup($data)){
						$this->UserGroup_model->deleteMapping($data['user_group_id']);
						foreach($MM as $key=>$val){
							$data2 = array(
								'user_group_level' 	=> $data['user_group_id'],
								'id_menu'			=> $key
							);
							$this->UserGroup_model->saveMapping($data2);
						}
						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['username'],'1007','Application.UserGroup.processEditUserGroup',$auth['username'],'Edit User Group');
						$this->fungsi->set_change_log($old_data,$data,$auth['username'],$data['user_group_id']);
						$msg = "<div class='alert alert-success alert-dismissable'>                  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	
									Edited Data User Group Successfully
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('UserGroup/Edit/'.$data['user_group_id']);
					} else {
						$msg = "<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	
									Failed to set menu privileges, because menu privileges doesn't set. Please set menu privileges !!!
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('UserGroup/Edit/'.$old_user_group_name);
					}
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>                
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	
								Maaf Group Name dengan Nama $data[user_group_name] sudah ada !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('UserGroup/Edit/'.$old_user_group_name);
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('UserGroup/Edit/'.$old_user_group_name);
			}
		}
		
		function delete(){
			if($this->UserGroup_model->delete($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['username'],'1008','Application.UserGroup.delete',$auth['username'],'Delete User Group');
				$msg = "<div class='alert alert-success alert-dismissable'>                  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	
							Delete Data User Group Successfully
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('UserGroup');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data User Group UnSuccessful
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('UserGroup');
			}
		}
		
		function test(){
			echo $this->UserGroup_model->isThisMenuInGroup('1','99');
		}
	}
?>