<?php
	Class ValidationProcess extends CI_Controller{
		public function __construct(){
			parent::__construct();
			// $this->load->model('MainPage_model');
			$this->load->model('ValidationProcess_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->library('fungsi');
			/*$this->load->library('session');*/
			$this->load->library('configuration');
			$this->load->database('default');

		}
		
		public function index(){
			$posisition = str_replace('\'', '/', realpath(dirname(__FILE__))) . '/';
			$root		= str_replace('\'', '/', realpath($posisition . '../../')) . '/';
			$path		= $root."application/logs";
			if ($nuxdir = opendir($path)){     //buka direktory yang diperkenalkan
				while ($isi = readdir($nuxdir)) {
					if(is_numeric(strpos($isi, "-"))){
						$pos = explode('-',$isi);
						if(count($pos)==4){
							if($pos[2]==date('m')){
								continue;
							} else {
								unlink($path."/".$isi);
							}
						}else{
							continue;
						}
					}else{
						continue;
					}
				}
				closedir($nuxdir);
			}
			
			$now = strtotime(date("Y-m-d"));
			$filename = $root.'parameter.par';
			if (file_exists($filename)) {
				$last = strtotime(date("Y-m-d", filectime($filename)));
				if($now>$last){
					$content ='';
					for($i=0;$i<5000;$i++){
						if ($i==2500){
							$content .= "?".get_unique().";";
						} else {
							$content .= chr(rand(128,248));
						}
					}
					$file = fopen($filename, 'w');		
					fwrite($file, $content);
					fclose($file);
				}
			} else {
				$content ='';
					for($i=0;$i<5000;$i++){
						if ($i==2500){
							$content .= "?".get_unique().";";
						} else {
							$content .= chr(rand(128,248));
						}
					}
					$file = fopen($filename, 'w');		
					fwrite($file, $content);
					fclose($file);
			}
			
			$this->load->view('LoginForm');
		}
		
		public function loginValidate(){
			$data = array(
				'username' => $this->input->post('username',true),
				'password' => md5($this->input->post('password',true))
			);
			
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('username', 'Username', 'required');
			
			if($this->form_validation->run()==true){
				$verify 	= $this->ValidationProcess_model->getSystemUser($data);
				if(count($verify)>1){
					/* print_r("verify");
					print_r($verify); */
					/* exit; */

					/* $this->fungsi->set_log($verify['user_id'], $verify['username'],'1001','Application.validationprocess.verifikasi',$verify['username'],'Login System'); */
					$this->session->set_userdata('auth', array(
									'user_id'			=> $verify['user_id'],
									'username'			=> $verify['username'],
									'database'			=> $verify['database'],
									'branch_id'			=> $verify['branch_id'],
									'branch_status'		=> $verify['branch_status'],
									'user_group_level'	=> $verify['user_group_id'],
									'user_level'		=> $verify['user_level']
								)
							);

							/* print_r("verify");
				print_r($verify);
				exit; */
					redirect('MainPage');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Username dan Password tidak cocok !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('ValidationProcess');
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('ValidationProcess');
			}
		}
		
		public function logout(){
			$auth = $this->session->userdata('auth');
			$this->ValidationProcess_model->getLogout($auth);

			$this->fungsi->set_log($auth['user_id'], $auth['username'],'1002','Application.validationprocess.logout',$auth['username'],'Logout System');
			$this->session->unset_userdata('auth');
			$this->session->sess_destroy();
			redirect('ValidationProcess');
		}
		
		public function warning(){
			$this->load->view('warning');
		}

		public function loginValidateUser(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'systemuser'			=> "",
			);

			$data = array(
				'username' 	=> $this->input->post('username',true),
				'password' 	=> md5($this->input->post('password',true))
			);

			/* $data = array(
				'username' 	=> 'administrator',
				'password' 	=> md5("567482")
			); */
			
			if (empty($data)){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Data Login is Empty";
			} else {
				if($response["error"] == FALSE){
					$systemuserlist 	= $this->ValidationProcess_model->getSystemUser($data);

					/* print_r("systemuserlist ");
					print_r($systemuserlist);
					print_r("<BR> ");
					print_r("<BR> "); */

					if($systemuserlist == false){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($systemuserlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$corebranch								= $this->ValidationProcess_model->getCoreBranch_Detail($systemuserlist['branch_id']);
							$preferencecompany						= $this->ValidationProcess_model->getPreferenceCompany();

							/* print_r("corebranch ");
							print_r($corebranch);
							print_r("<BR> ");
							print_r("<BR> ");

							print_r("preferencecompany ");
							print_r($preferencecompany);
							print_r("<BR> ");
							print_r("<BR> "); */
							
							$systemuser[0]['user_id'] 				= $systemuserlist['user_id'];
							$systemuser[0]['username'] 				= $systemuserlist['username'];
							$systemuser[0]['user_name'] 			= $systemuserlist['user_name'];
							$systemuser[0]['branch_id'] 			= $systemuserlist['branch_id'];
							$systemuser[0]['branch_code'] 			= $corebranch['branch_code'];
							$systemuser[0]['branch_name'] 			= $corebranch['branch_name'];
							$systemuser[0]['company_name'] 			= $preferencecompany['company_name'];
							$systemuser[0]['company_slogan'] 		= $preferencecompany['company_slogan'];
							$systemuser[0]['company_footer'] 		= $preferencecompany['company_footer'];
							

							$response['error'] 				= FALSE;
							$response['error_msg_title'] 	= "Success";
							$response['error_msg'] 			= "Data Exist";
							$response['systemuser'] 		= $systemuser;
						}
					}
				}
			}

			echo json_encode($response);

		}

	}
?>