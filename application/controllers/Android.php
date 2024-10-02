<?php
	Class Android extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('Android_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('AcctCashPayment_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->database('cipta');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			
		}

		public function getCoreProgram(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'				=> FALSE,
				'error_msg'			=> "",
				'error_msg_title'	=> "",
				'coreprogram'		=> "",
			);

			$data = array(
				'user_id'		=> $this->input->post('user_id',true),
			);


			if($response["error"] == FALSE){

				$coreprogramlist 	= $this->Android_model->getCoreProgram();

				if(!$coreprogramlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($coreprogramlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($coreprogramlist as $key => $val) {

							$coreprogram[$key]['program_id']				= $val['program_id'];
							$coreprogram[$key]['program_name']				= $val['program_name'];
							$coreprogram[$key]['program_photo']				= $base_url.'Android/getItemPicture/'.$val['program_id'];
							$coreprogram[$key]['program_remark']			= $val['program_remark'];
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['coreprogram'] 			= $coreprogram;
					}
				}
			}

			echo json_encode($response);
		}

		public function getItemPicture(){
			$program_id 	= $this->uri->segment(3);

			$item_picture 	= $this->Android_model->getItemPicture($program_id);

			$this->output->set_header('Content-type: image/jpg');
			$this->output->set_output($item_picture);			
		}





		//--------------------------- MEMBER ------------------------------------//

		public function getCoreMember(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'				=> FALSE,
				'error_msg'			=> "",
				'error_msg_title'	=> "",
				'coremember'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'user_id'		=> $this->input->post('user_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$systemuserdusun	= $this->Android_model->getSystemUserDusun($data['user_id']);

					$corememberlist 	= $this->Android_model->getCoreMember($data['branch_id'], $systemuserdusun);

					if(!$corememberlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							foreach ($corememberlist as $key => $val) {
								$memberidentity = $this->configuration->MemberIdentity();

								$member_address 	= $val['member_address'].' '.$val['province_name'].' '.$val['city_name'].' '.$val['kecamatan_name'];

								$member_identity_no = '( '.$memberidentity[$val['member_identity']].' ) '.$val['member_identity_no'];

								$coremember[$key]['branch_id']				= $val['branch_id'];
								$coremember[$key]['branch_name']			= $val['branch_name'];
								$coremember[$key]['member_id']				= $val['member_id'];
								$coremember[$key]['member_no']				= $val['member_no'];
								$coremember[$key]['member_name']			= $val['member_name'];
								$coremember[$key]['member_address']			= $member_address;
								$coremember[$key]['member_identity_no']		= $member_identity_no;
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['coremember'] 			= $coremember;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}

		// DAFTAR SIMPANAN SAHAM MEMBER

		public function getCoreMemberSavings(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'coremembersavings'		=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			if($response["error"] == FALSE){
				$branch_id 			= "";
				$data_array 		= "";				
				$corememberdata 	= $this->Android_model->getCoreMember_Detail($data['member_id'], $branch_id, $data_array);
	
				
				$lasttransactionprincipal = $this->Android_model->getLastTransactionPrincipal($data['member_id']);
				if(empty($lasttransactionprincipal)){
					$principal_description	= 'Belum Ada Transaksi';
					$principal_date			= '-';
				} else {
					$principal_description	= $lasttransactionprincipal['mutation_name'].' Rp. '.number_format($lasttransactionprincipal['principal_savings_amount'], 2,',','.');
					$principal_date			= date('d M Y', strtotime($lasttransactionprincipal['transaction_date']));
				}

				$lasttransactionmandatory = $this->Android_model->getLastTransactionMandatory($data['member_id']);
				if(empty($lasttransactionmandatory)){
					$mandatory_description	= 'Belum Ada Transaksi';
					$mandatory_date			= '-';
				} else {
					$mandatory_description	= $lasttransactionmandatory['mutation_name'].' Rp. '.number_format($lasttransactionmandatory['mandatory_savings_amount'], 2,',','.');
					$mandatory_date			= date('d M Y', strtotime($lasttransactionmandatory['transaction_date']));
				}

				$lasttransactionspecial 	= $this->Android_model->getLastTransactionSpecial($data['member_id']);
				if(empty($lasttransactionspecial)){
					$special_description	= 'Belum Ada Transaksi';
					$special_date			= '-';
				} else {
					$special_description	= $lasttransactionspecial['mutation_name'].' Rp. '.number_format($lasttransactionspecial['special_savings_amount'], 2,',','.');
					$special_date			= date('d M Y', strtotime($lasttransactionspecial['transaction_date']));
				}

				if($corememberdata['member_principal_savings_last_balance'] == null){
					$corememberdata['member_principal_savings_last_balance'] 		= 0;
				}

				if($corememberdata['member_special_savings_last_balance'] == null){
					$corememberdata['member_special_savings_last_balance'] 		= 0;
				}

				if($corememberdata['member_mandatory_savings_last_balance'] == null){
					$corememberdata['member_mandatory_savings_last_balance'] 		= 0;
				}


				$coremembersavings[0]['principal_savings_last_balance']	= $corememberdata['member_principal_savings_last_balance'];
				$coremembersavings[0]['special_savings_last_balance']	= $corememberdata['member_special_savings_last_balance'];
				$coremembersavings[0]['mandatory_savings_last_balance']	= $corememberdata['member_mandatory_savings_last_balance'];
				$coremembersavings[0]['principal_date']					= $principal_date;
				$coremembersavings[0]['principal_description']			= $principal_description;
				$coremembersavings[0]['mandatory_date']					= $mandatory_date;
				$coremembersavings[0]['mandatory_description']			= $mandatory_description;
				$coremembersavings[0]['special_date']					= $special_date;
				$coremembersavings[0]['special_description']			= $special_description;

				$response['error'] 					= FALSE;
				$response['error_msg_title'] 		= "Success";
				$response['error_msg'] 				= "Data Exist";
				$response['coremembersavings'] 		= $coremembersavings;
			}

			echo json_encode($response);
		}

		// END DAFTAR SIMPANAN SAHAM MEMBER

		// HISTORI TRANSAKSI SIMPANAN POKOK MEMBER

		public function getCoreMemberPrincipalHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'corememberprincipalsavings'	=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			if($response["error"] == FALSE){
				$corememberhistorylist 		= $this->Android_model->getAcctSavingsPrincipal($data['member_id']);
	
				
				if(!$corememberhistorylist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($corememberhistorylist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						
						foreach ($corememberhistorylist as $key => $val){

							/* if($val['mutation_in'] > 0){
								$savings_account_mutation	= $val['mutation_in'];
							} else {
								$savings_account_mutation	= $val['mutation_out'];
							} */

							$corememberprincipalsavings[$key]['principal_transaction_title']			= $val['mutation_name'];
							$corememberprincipalsavings[$key]['principal_transaction_date']			= date('d M Y H:i:s', strtotime($val['last_update']));
							$corememberprincipalsavings[$key]['principal_transaction_description']	= $val['mutation_name'].' Simpanan Pokok';
							$corememberprincipalsavings[$key]['principal_transaction_amount']			= $val['principal_savings_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['corememberprincipalsavings'] 		= $corememberprincipalsavings;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI SIMPANAN POKOK MEMBER


		// HISTORI TRANSAKSI SIMPANAN WAJIB MEMBER

		public function getCoreMemberMandatoryHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'coremembermandatorysavings'	=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			if($response["error"] == FALSE){
				$corememberhistorylist 		= $this->Android_model->getAcctSavingsMandatory($data['member_id']);
	
				
				if(!$corememberhistorylist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($corememberhistorylist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						
						foreach ($corememberhistorylist as $key => $val){

							/* if($val['mutation_in'] > 0){
								$savings_account_mutation	= $val['mutation_in'];
							} else {
								$savings_account_mutation	= $val['mutation_out'];
							} */

							$coremembermandatorysavings[$key]['principal_transaction_title']			= $val['mutation_name'];
							$coremembermandatorysavings[$key]['principal_transaction_date']			= date('d M Y H:i:s', strtotime($val['last_update']));
							$coremembermandatorysavings[$key]['principal_transaction_description']	= $val['mutation_name'].' Simpanan Wajib';
							$coremembermandatorysavings[$key]['principal_transaction_amount']			= $val['mandatory_savings_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['coremembermandatorysavings'] 		= $coremembermandatorysavings;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI SIMPANAN WAJIB MEMBER


		// HISTORI TRANSAKSI SIMPANAN KHUSUS MEMBER

		public function getCoreMemberSpecialHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'corememberspecialsavings'		=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			if($response["error"] == FALSE){
				$corememberhistorylist 		= $this->Android_model->getAcctSavingsSpecial($data['member_id']);
	
				
				if(!$corememberhistorylist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($corememberhistorylist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						
						foreach ($corememberhistorylist as $key => $val){

							/* if($val['mutation_in'] > 0){
								$savings_account_mutation	= $val['mutation_in'];
							} else {
								$savings_account_mutation	= $val['mutation_out'];
							} */

							$corememberspecialsavings[$key]['principal_transaction_title']			= $val['mutation_name'];
							$corememberspecialsavings[$key]['principal_transaction_date']			= date('d M Y H:i:s', strtotime($val['last_update']));
							$corememberspecialsavings[$key]['principal_transaction_description']	= $val['mutation_name'].' Simpanan Wajib';
							$corememberspecialsavings[$key]['principal_transaction_amount']			= $val['special_savings_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['corememberspecialsavings'] 			= $corememberspecialsavings;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI SIMPANAN WAJIB MEMBER

		public function getCoreMember_Detail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberdetail'		=> "",
			);

			$data = array(
				'member_id'		=> $this->input->post('member_id',true),
				'user_id'		=> $this->input->post('user_id',true),
				'branch_id'		=> $this->input->post('branch_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$systemuserdusun	= $this->Android_model->getSystemUserDusun($data['user_id']);

					$corememberdetaillist = $this->Android_model->getCoreMember_Detail($data['member_id'], $data['branch_id'], $systemuserdusun);
					

					if(!$corememberdetaillist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberdetaillist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity = $this->configuration->MemberIdentity();

							/*print_r("memberidentity ");
							print_r($memberidentity);*/

							$member_address 	= $corememberdetaillist['member_address'].' '.$corememberdetaillist['province_name'].' '.$corememberdetaillist['city_name'].' '.$corememberdetaillist['kecamatan_name'];

							$member_identity_no = '( '.$memberidentity[$corememberdetaillist['member_identity']].' ) '.$corememberdetaillist['member_identity_no'];

							$corememberdetail[0]['branch_id']				= $corememberdetaillist['branch_id'];
							$corememberdetail[0]['branch_name']				= $corememberdetaillist['branch_name'];
							$corememberdetail[0]['member_id']				= $corememberdetaillist['member_id'];
							$corememberdetail[0]['member_no']				= $corememberdetaillist['member_no'];
							$corememberdetail[0]['member_name']				= $corememberdetaillist['member_name'];
							$corememberdetail[0]['member_address']			= $member_address;
							$corememberdetail[0]['member_identity_no']		= $member_identity_no;
								
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['corememberdetail'] 		= $corememberdetail;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}

		public function getCoreMember_Login(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberlogin'			=> "",
			);

			$data = array(
				'member_no' 			=> $this->input->post('member_no',true),
				'password' 				=> $this->input->post('password',true),
				'member_password' 		=> md5($this->input->post('password',true))
			);

			/* $data = array(
				'member_no' 			=> '00000001',
				'password' 				=> '123456',
				'member_password' 		=> md5('123456')
			); */

			/*print_r("data ");
			print_r($data);*/
			
			if (empty($data)){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Data Login is Empty";
			} else {
				if($response["error"] == FALSE){
					$verify 	= $this->Android_model->getCoreMember_Login($data['member_no'], $data['member_password']);

					if($verify == false){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($verify)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							
							$corememberlogin[0]['member_id'] 			= $verify['member_id'];
							$corememberlogin[0]['member_no'] 			= $verify['member_no'];
							$corememberlogin[0]['member_name'] 			= $verify['member_name'];
							$corememberlogin[0]['branch_id'] 			= $verify['branch_id'];
							$corememberlogin[0]['savings_account_id'] 	= $verify['savings_account_id'];
							

							$response['error'] 				= FALSE;
							$response['error_msg_title'] 	= "Success";
							$response['error_msg'] 			= "Data Exist";
							$response['corememberlogin'] 	= $corememberlogin;
						}
					}
				}
			}

			echo json_encode($response);

		}
		


		//--------------------------- END MEMBER ------------------------------------//






		//--------------------------- SIMPANAN --------------------------------------//

		//------- List Simpanan Sukarela

		public function getAcctSavingsAccount(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);
			

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			// $data = array(
			// 	'branch_id'		=> 2,
			// 	'member_id'		=> 32888,
			// );

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			/*print_r("now ");
			print_r($now);
			print_r("<BR>");

			print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id']);

					if(!$acctsavingsaccountlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctsavingsaccountlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							foreach ($acctsavingsaccountlist as $key => $val) {
								$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
								$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
								$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
								$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
								$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
								$acctsavingsaccount[$key]['savings_account_first_deposit_amount']	= $val['savings_account_first_deposit_amount'];
								$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['acctsavingsaccount'] 	= $acctsavingsaccount;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}

		//---------- End List Simpanan Sukarela

		//---------- List Simpanan Sukarela Icon

		public function getAcctSavingsAccountMemberList(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccountmember'	=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			// $data['member_id']	= 35415;

			if($response["error"] == FALSE){
				$acctsavings = $this->Android_model->getAcctSavings();

				$no = 0;
				foreach ($acctsavings as $ks => $vs){
					$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccountMember($data['member_id'], $vs['savings_id']);
					

					if (!empty($acctsavingsaccountlist)){	
						foreach ($acctsavingsaccountlist as $key => $val) {

							$lasttransactionsavings 				= $this->Android_model->getLastTransactionSavings($val['savings_account_id']);
							if(empty($lasttransactionsavings)){
								$savings_account_description		= 'Belum Ada Transaksi';
								$savings_account_last_mutation		= 'Rp. 0,00';
								$savings_account_last_date			= '-';
							} else {
								if($lasttransactionsavings['mutation_in'] > 0){
									$savings_account_description	= $lasttransactionsavings['mutation_name'];
									
									$savings_account_last_mutation	= 'Rp. '.number_format($lasttransactionsavings['mutation_in'], 2,',','.');
								} else {
									$savings_account_description	= $lasttransactionsavings['mutation_name'];

									$savings_account_last_mutation	= 'Rp. '.number_format($lasttransactionsavings['mutation_out'], 2,',','.');
								}
								
								$savings_account_last_date			= date('d M Y', strtotime($lasttransactionsavings['today_transaction_date']));
							}
							
							$acctsavingsaccountmember[$no]['savings_account_id'] 					= $val['savings_account_id'];
							$acctsavingsaccountmember[$no]['savings_id']							= $val['savings_id'];
							$acctsavingsaccountmember[$no]['savings_code']							= $val['savings_code'];
							$acctsavingsaccountmember[$no]['savings_name']							= $val['savings_name'];
							$acctsavingsaccountmember[$no]['savings_account_no']					= $val['savings_account_no'];
							$acctsavingsaccountmember[$no]['savings_account_first_deposit_amount']	= $val['savings_account_first_deposit_amount'];
							$acctsavingsaccountmember[$no]['savings_account_last_balance']			= $val['savings_account_last_balance'];
							$acctsavingsaccountmember[$no]['savings_account_description']			= $savings_account_description;
							$acctsavingsaccountmember[$no]['savings_account_last_mutation']			= $savings_account_last_mutation;
							$acctsavingsaccountmember[$no]['savings_account_last_date']				= $savings_account_last_date;
							$acctsavingsaccountmember[$no]['savings_logo_url']						= $base_url.'Android/getSavingsLogo/'.$val['savings_id'];
							$acctsavingsaccountmember[$no]['savings_icon_url']						= $base_url.'Android/getSavingsIcon/'.$val['savings_id'];
							$acctsavingsaccountmember[$no]['savings_card_url']						= $base_url.'Android/getSavingsCard/'.$val['savings_id'];

							$no++;
						}
					}
					
				}

				if(!is_array($acctsavingsaccountmember)){
					$response['error'] 						= TRUE;
					$response['error_msg_title'] 			= "Kosong";
					$response['error_msg'] 					= "Anggota Belum Memiliki Rekening Simpanan";
					$response['acctsavingsaccountmember'] 	= $acctsavingsaccountmember;
				} else {
					$response['error'] 						= FALSE;
					$response['error_msg_title'] 			= "Success";
					$response['error_msg'] 					= "Data Exist";
					$response['acctsavingsaccountmember'] 	= $acctsavingsaccountmember;
				}
				
			}

			echo json_encode($response);
		}

		// End List Simpanan Sukarela Icon

		// Logo Simpanan

		public function getSavingsLogo(){
			$savings_id 	= $this->uri->segment(3);

			$savings_logo 	= $this->Android_model->getSavingsLogo($savings_id);

			$this->output->set_header('Content-type: image/jpg');
			$this->output->set_output($savings_logo);			
		}

		// End Logo

		// Icon Simpanan

		public function getSavingsIcon(){
			$savings_id 	= $this->uri->segment(3);

			$savings_icon 	= $this->Android_model->getSavingsIcon($savings_id);

			$this->output->set_header('Content-type: image/jpg');
			$this->output->set_output($savings_icon);			
		}

		// End Icon

		// Card Simpanan

		public function getSavingsCard(){
			$savings_id 	= $this->uri->segment(3);

			$savings_card 	= $this->Android_model->getSavingsCard($savings_id);

			$this->output->set_header('Content-type: image/jpg');
			$this->output->set_output($savings_card);			
		}

		// End Card

		// HISTORI TRANSAKSI SIMPANAN SUKARELA MEMBER

		public function getAcctSavingsAccountHistory(){
			/* $base_url 	= base_url();
			$auth 		= $this->session->userdata('auth'); */

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingscashmutationhistory'	=> "",
			);

			$data = array(
				'member_id'				=> $this->input->post('member_id',true),
				'savings_account_id'	=> $this->input->post('savings_account_id',true),
			);

			/* $data['member_id']		= 32890; */

			/* print_r("data ");
			print_r($data);
			exit; */

			if($response["error"] == FALSE){

				$preferencecompany 				= $this->Android_model->getPreferenceCompany();

				$data_mutation 					= array ($preferencecompany['cash_deposit_id'], $preferencecompany['cash_withdrawal_id']);

				/* print_r("data_mutation ");
				print_r($data_mutation);
				exit; */

				$acctsavingscashmutationlist	= $this->Android_model->getAcctSavingsCashMutation_History($data['member_id'], $data_mutation, $data['savings_account_id']);

				/* print_r("data_mutation ");
				print_r($data_mutation);
				exit; */
	
				
				if(!$acctsavingscashmutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$savingscashmutationstatus = $this->configuration->SavingsCashMutationStatus();

						foreach ($acctsavingscashmutationlist as $key => $val){
							if($val['mutation_id'] == $preferencecompany['cash_deposit_id']){
								$savings_transaction_description = $val['mutation_name'].' ke simpanan '.$val['savings_name'].' No. Rek. '.$val['savings_account_no'].' melalui '.$savingscashmutationstatus[$val['savings_cash_mutation_status']];
							} else {
								$savings_transaction_description = $val['mutation_name'].' dari simpanan '.$val['savings_name'].' No. Rek. '.$val['savings_account_no'].' melalui '.$savingscashmutationstatus[$val['savings_cash_mutation_status']];
							}

							$acctsavingscashmutationhistory[$key]['savings_transaction_title']			= $val['mutation_name'];
							$acctsavingscashmutationhistory[$key]['savings_transaction_date']			= date('d M Y H:i:s', strtotime($val['created_on']));
							$acctsavingscashmutationhistory[$key]['savings_transaction_description']	= $savings_transaction_description;
							$acctsavingscashmutationhistory[$key]['savings_transaction_amount']			= $val['savings_cash_mutation_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingscashmutationhistory'] 	= $acctsavingscashmutationhistory;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI SIMPANAN SUKARELA MEMBER

		public function getAcctSavingsAccountMBayar(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			
			/*print_r("now ");
			print_r($now);
			print_r("<BR>");

			print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			
			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id'], $data['branch_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($acctsavingsaccountlist as $key => $val) {
							$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
							$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
							$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
							$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
							$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingsaccount[$key]['savings_account_first_deposit_amount']	= $val['savings_account_first_deposit_amount'];
							$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccount'] 	= $acctsavingsaccount;
					}
				}
			}
			

			echo json_encode($response);
		}

		// HISTORI TRANSAKSI MBAYAR MASUK

		public function getAcctSavingsAccountMBayarInHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingsaccountmbayarinhistory'	=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			// $data['member_id']		= 32890;

			if($response["error"] == FALSE){

				$preferencecompany 				= $this->Android_model->getPreferenceCompany();

				$acctsavingsaccountlist			= $this->Android_model->getAcctSavingsTransferMutationTo_History($data['member_id']);
	
				
				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {

						foreach ($acctsavingsaccountlist as $key => $val){

							$acctsavingsaccountdetailfrom 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_from_id']);
							$acctsavingsaccountdetailto 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_to_id']);

							$acctsavingsaccountmbayarinhistory[$key]['mbayar_transaction_title']		= 'mBayar Masuk';
							$acctsavingsaccountmbayarinhistory[$key]['mbayar_transaction_date']			= date('d M Y H:i:s', strtotime($val['created_on']));
							$acctsavingsaccountmbayarinhistory[$key]['mbayar_transaction_description']	= 'Transfer dari Rekening '.$acctsavingsaccountdetailfrom['savings_account_no'].' Ke Rekening '.$acctsavingsaccountdetailto['savings_account_no'].' a/n '.$acctsavingsaccountdetailto['member_name'];
							$acctsavingsaccountmbayarinhistory[$key]['mbayar_transaction_amount']		= $val['savings_transfer_mutation_to_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingsaccountmbayarinhistory'] 	= $acctsavingsaccountmbayarinhistory;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI MBAYAR MASUK

		// HISTORI TRANSAKSI MBAYAR KELUAR

		public function getAcctSavingsAccountMBayarOutHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'									=> FALSE,
				'error_msg'								=> "",
				'error_msg_title'						=> "",
				'acctsavingsaccountmbayarouthistory'	=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			/* $data['member_id']		= 32887; */

			if($response["error"] == FALSE){

				$preferencecompany 				= $this->Android_model->getPreferenceCompany();

				$acctsavingsaccountlist			= $this->Android_model->getAcctSavingsTransferMutationFrom_History($data['member_id']);
	
				
				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {

						foreach ($acctsavingsaccountlist as $key => $val){

							$acctsavingsaccountdetailfrom 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_from_id']);
							$acctsavingsaccountdetailto 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_to_id']);

							$acctsavingsaccountmbayarouthistory[$key]['mbayar_transaction_title']		= 'mBayar Keluar';
							$acctsavingsaccountmbayarouthistory[$key]['mbayar_transaction_date']			= date('d M Y H:i:s', strtotime($val['created_on']));
							$acctsavingsaccountmbayarouthistory[$key]['mbayar_transaction_description']	= 'Transfer dari Rekening '.$acctsavingsaccountdetailfrom['savings_account_no'].' Ke Rekening '.$acctsavingsaccountdetailto['savings_account_no'].' a/n '.$acctsavingsaccountdetailto['member_name'];
							$acctsavingsaccountmbayarouthistory[$key]['mbayar_transaction_amount']		= $val['savings_transfer_mutation_from_amount'];
						}
							
						$response['error'] 									= FALSE;
						$response['error_msg_title'] 						= "Success";
						$response['error_msg'] 								= "Data Exist";
						$response['acctsavingsaccountmbayarouthistory'] 	= $acctsavingsaccountmbayarouthistory;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI MBAYAR KELUAR

		public function processAddAcctSavingsCashMutation(){
			$status = $this->input->post('status',true);

			/*print_r("status ");
			print_r("$status");*/

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if ($status == 1){
				$mutation_id = $preferencecompany['cash_deposit_id'];
			} else if ($status == 2){
				$mutation_id = $preferencecompany['cash_withdrawal_id'];
			}

			$password										= md5($this->input->post('password', true));

			$data = array(
				'savings_account_id'						=> $this->input->post('savings_account_id', true),
				'mutation_id'								=> $mutation_id,
				'member_id'									=> $this->input->post('member_id', true),
				'branch_id'									=> $this->input->post('branch_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'savings_cash_mutation_date'				=> date('Y-m-d'),
				'savings_cash_mutation_opening_balance'		=> $this->input->post('savings_cash_mutation_opening_balance', true),
				'savings_cash_mutation_last_balance'		=> $this->input->post('savings_cash_mutation_last_balance', true),
				'savings_cash_mutation_amount'				=> $this->input->post('savings_cash_mutation_amount', true),
				'savings_cash_mutation_remark'				=> $this->input->post('savings_cash_mutation_remark', true),
				'savings_cash_mutation_status'				=> 1,
				'operated_name'								=> $this->input->post('username', true),
				'created_id'								=> $this->input->post('user_id', true),
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$response = array(
				'error'										=> FALSE,
				'error_acctsavingscashmutation'				=> FALSE,
				'error_msg_title_acctsavingscashmutation'	=> "",
				'error_msg_acctsavingscashmutation'			=> "",
			);

			if($response["error_acctsavingscashmutation"] == FALSE){
				if(!empty($data)){					
					if($this->Android_model->getSystemUser($data['created_id'], $password)){
						if ($this->Android_model->insertAcctSavingsCashMutation($data)){

							$transaction_module_code = "TTAB";

							$transaction_module_id 	= $this->AcctSavingsCashMutation_model->getTransactionModuleID($transaction_module_code);
							$acctsavingscash_last 	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Last($data['created_id']);

							$savings_cash_mutation_id = $acctsavingscash_last['savings_cash_mutation_id'];

								
							$journal_voucher_period = date("Ym", strtotime($data['savings_cash_mutation_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
								'journal_voucher_description'	=> 'MUTASI TUNAI '.$acctsavingscash_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctsavingscash_last['savings_cash_mutation_id'],
								'transaction_journal_no' 		=> $acctsavingscash_last['savings_account_no'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);
							
							$this->AcctSavingsCashMutation_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctSavingsCashMutation_model->getJournalVoucherID($data['created_id']);

							$preferencecompany = $this->AcctSavingsCashMutation_model->getPreferenceCompany();


							if($data['mutation_id'] == $preferencecompany['cash_deposit_id']){
								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debet = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_debet);

								$account_id = $this->AcctSavingsCashMutation_model->getAccountID($data['savings_id']);

								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_credit);
							} else {
								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
					
								$account_id = $this->AcctSavingsCashMutation_model->getAccountID($data['savings_id']);

								$account_id_default_status = $this->AcctSavingsCashMutation_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_debit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_debit);

								$data_credit = array (
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$acctsavingscash_last['member_name'],
									'journal_voucher_amount'		=> $data['savings_cash_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_cash_mutation_amount'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
								);

								$this->AcctSavingsCashMutation_model->insertAcctJournalVoucherItem($data_credit);
							}

							$response['error_acctsavingscashmutation'] 	= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['savings_cash_mutation_id'] 		= $savings_cash_mutation_id;
						} else {
							$response['error_acctsavingscashmutation'] 	= TRUE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['savings_cash_mutation_id'] 		= "";
						}
					} else {
						$response['error_acctsavingscashmutation'] 	= TRUE;
						$response['error_msg_title'] 				= "Gagal";
						$response['error_msg'] 						= "Password Salah";
						$response['savings_cash_mutation_id'] 		= "";
					}
				}

			} 
			
			echo json_encode($response);
		}

		public function getAcctCreditsAccount(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			/*print_r("data ");
			print_r($data);*/

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$acctcreditsaccountlist = $this->Android_model->getAcctCreditsAccount($data['member_id'], $data['branch_id']);

					if(!$acctcreditsaccountlist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctcreditsaccountlist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							foreach ($acctcreditsaccountlist as $key => $val) {
								$credits_account_last_balance_principal = $val['credits_account_last_balance_principal'];
								$credits_account_last_balance_margin	= $val['credits_account_last_balance_margin'];

								$total_last_balance = $credits_account_last_balance_principal + $credits_account_last_balance_margin;

								$credits_account_id = $val['credits_account_id'];

								$detailpayment		= $this->AcctCashPayment_model->getDataByIDCredit($credits_account_id);

								$credits_payment_to = count($detailpayment) + 1;

								$acctcreditsaccount[$key]['credits_account_id'] 					= $val['credits_account_id'];
								$acctcreditsaccount[$key]['credits_id']								= $val['credits_id'];
								$acctcreditsaccount[$key]['credits_code']							= $val['credits_code'];
								$acctcreditsaccount[$key]['credits_name']							= $val['credits_name'];
								$acctcreditsaccount[$key]['credits_account_serial']					= $val['credits_account_serial'];
								$acctcreditsaccount[$key]['credits_account_period']					= $val['credits_account_period'];
								$acctcreditsaccount[$key]['credits_payment_to']						= $credits_payment_to;
								$acctcreditsaccount[$key]['credits_account_last_balance_principal']	= $val['credits_account_last_balance_principal'];
								$acctcreditsaccount[$key]['credits_account_last_balance_margin']	= $val['credits_account_last_balance_margin'];
							}
							
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['acctcreditsaccount'] 	= $acctcreditsaccount;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}

		public function processAddAcctCreditsPayment(){
			$password 							= md5($this->input->post('password',true));
			$credits_principal_opening_balance 	= $this->input->post('credits_principal_opening_balance',true);
			$credits_margin_opening_balance 	= $this->input->post('credits_margin_opening_balance',true);
			$credits_payment_principal 			= $this->input->post('credits_payment_principal',true);
			$credits_payment_margin 			= $this->input->post('credits_payment_margin',true);

			$credits_principal_last_balance		= $credits_principal_opening_balance - $credits_payment_principal;
			$credits_margin_last_balance		= $credits_margin_opening_balance - $credits_payment_margin;
			
			$response = array(
				'error'										=> FALSE,
				'error_acctcreditspayment'					=> FALSE,
				'error_msg_title_acctcreditspayment'		=> "",
				'error_msg_acctcreditspayment'				=> "",
			);



			$total_angsuran = $this->input->post('credits_account_period', true);
			$angsuran_ke 	= $this->input->post('credits_payment_to', true);

			/*print_r("angsuran_ke ");
			print_r($angsuran_ke);*/


			$credits_account_id 					= $this->input->post('credits_account_id', true);

			$credits_account_payment_date 			= $this->Android_model->getCreditsAccountPaymentDate($credits_account_id);

			if($angsuran_ke < $total_angsuran){
				$credits_account_payment_date_old 	= $credits_account_payment_date;
				$credits_account_payment_date 		= date('Y-m-d', strtotime("+1 months", strtotime($credits_account_payment_date_old)));
			}

			$credits_payment_date 					= date('Y-m-d');

			$date1 									= date_create($credits_payment_date);
			$date2 									= date_create($credits_account_payment_date);

			$credits_payment_day_of_delay 			= date_diff($date1, $date2)->format('%d');

			$data = array(
				'branch_id'							=> $this->input->post('branch_id', true),
				'member_id'							=> $this->input->post('member_id', true),
				'credits_id'						=> $this->input->post('credits_id', true),
				'credits_account_id'				=> $this->input->post('credits_account_id', true),
				'credits_payment_date'				=> date('Y-m-d'),
				'credits_principal_opening_balance'	=> $this->input->post('credits_principal_opening_balance',true),
				'credits_margin_opening_balance'	=> $this->input->post('credits_margin_opening_balance',true),
				'credits_payment_principal'			=> $this->input->post('credits_payment_principal',true),
				'credits_payment_margin'			=> $this->input->post('credits_payment_margin',true),
				'credits_payment_amount'			=> $this->input->post('credits_payment_amount',true),
				'credits_principal_last_balance'	=> $credits_principal_last_balance,
				'credits_margin_last_balance'		=> $credits_margin_last_balance,
				'credits_account_payment_date'		=> $credits_account_payment_date,
				'credits_payment_to'				=> $this->input->post('credits_payment_to', true),
				'credits_payment_day_of_delay'		=> $credits_payment_day_of_delay,
				'credits_payment_status'			=> 1,
				'created_id'						=> $this->input->post('user_id', true),
				'created_on'						=> date('Y-m-d H:i:s'),
			);
			

			$transaction_module_code 	= 'ANGS';
			$transaction_module_id 		= $this->AcctCreditAccount_model->getTransactionModuleID($transaction_module_code);
			$preferencecompany 			= $this->AcctCreditAccount_model->getPreferenceCompany();





			if($response["error_acctcreditspayment"] == FALSE){
				if(!empty($data)){					
					if ($this->Android_model->getSystemUser($data['created_id'], $password)){
						if($this->AcctCashPayment_model->insert($data)){
							$updatedata=array(
								"credits_account_last_balance_principal" 	=>$data['credits_principal_last_balance'],
								"credits_account_last_balance_margin" 		=>$data['credits_margin_last_balance'],
								"credits_account_last_payment_date"			=>$data['credits_payment_date'],
								"credits_account_payment_date"				=>$credits_account_payment_date,
								"credits_account_payment_to"				=>$data['credits_payment_to'],
							);
							$this->AcctCreditAccount_model->updatedata($updatedata,$data['credits_account_id']);

							$acctcashpayment_last 	= $this->AcctCashPayment_model->AcctCashPaymentLast($data['created_id']);

							$credits_payment_id 	= $acctcashpayment_last['credits_payment_id'];
								
							$journal_voucher_period = date("Ym", strtotime($data['credits_payment_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'journal_voucher_description'	=> 'ANGSURAN TUNAI '.$acctcashpayment_last['credits_name'].' '.$acctcashpayment_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctcashpayment_last['credits_payment_id'],
								'transaction_journal_no' 		=> $acctcashpayment_last['credits_account_serial'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);

							// print_r($acctcashpayment_last);exit;
							
							$this->AcctCreditAccount_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id = $this->AcctCreditAccount_model->getJournalVoucherID($data['created_id']);

							$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_principal'],
								'journal_voucher_debit_amount'	=> $data['credits_payment_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);

							$receivable_account_id = $this->AcctCreditAccount_model->getReceivableAccountID($data['credits_id']);

							$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($receivable_account_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $receivable_account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_principal'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_principal'],
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

							if($data['credits_id'] == $preferencecompany['deferred_margin_income']){

								$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_deferred_margin_income']);

								$data_debet =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_deferred_margin_income'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_margin'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
									'account_id_status'				=> 0,
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
							} else {
								$account_id_default_status = $this->AcctCreditAccount_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

								$data_debet =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $preferencecompany['account_cash_id'],
									'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
									'journal_voucher_amount'		=> $data['credits_payment_margin'],
									'journal_voucher_debit_amount'	=> $data['credits_payment_margin'],
									'account_id_status'				=> 0,
								);

								$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_debet);
							}

							$income_account_id 			= $this->AcctCreditAccount_model->getIncomeAccountID($data['credits_id']);

							$account_id_default_status 	= $this->AcctCreditAccount_model->getAccountIDDefaultStatus($income_account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $income_account_id,
								'journal_voucher_description'	=> $data_journal['journal_voucher_title'],
								'journal_voucher_amount'		=> $data['credits_payment_margin'],
								'journal_voucher_credit_amount'	=> $data['credits_payment_margin'],
								'account_id_status'				=> 1,
							);

							$this->AcctCreditAccount_model->insertAcctJournalVoucherItem($data_credit);

							$response['error_acctcreditspayment'] 		= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['credits_payment_id']				= $credits_payment_id;
						} else {
							$response['error_acctcreditspayment'] 		= TRUE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['credits_payment_id']				= $credits_payment_id;
						}
					} else {
						$response['error_acctcreditspayment'] 		= TRUE;
						$response['error_msg_title'] 				= "Gagal";
						$response['error_msg'] 						= "Password Salah";
						$response['credits_payment_id']				= $credits_payment_id;
					}


				}

			} 
			
			echo json_encode($response);
		}

		public function printNoteAcctSavingsCashMutation(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingscashmutation'		=> "",
			);

			$data = array(
				'savings_cash_mutation_id'		=> $this->input->post('savings_cash_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingscashmutationlist	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Detail($data['savings_cash_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingscashmutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingscashmutation[0]['company_name'] 					= $preferencecompany['company_name'];
						$acctsavingscashmutation[0]['member_name'] 						= $acctsavingscashmutationlist['member_name'];
						$acctsavingscashmutation[0]['savings_account_no']				= $acctsavingscashmutationlist['savings_account_no'];
						$acctsavingscashmutation[0]['member_address']					= $acctsavingscashmutationlist['member_address'];
						$acctsavingscashmutation[0]['savings_cash_mutation_amount']		= "Rp. ".number_format($acctsavingscashmutationlist['savings_cash_mutation_amount'], 2);
						$acctsavingscashmutation[0]['savings_cash_mutation_amount_str']	= numtotxt($acctsavingscashmutationlist['savings_cash_mutation_amount']);
						$acctsavingscashmutation[0]['branch_city']						= $acctsavingscashmutationlist['branch_city'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingscashmutation'] 	= $acctsavingscashmutation;
					}
				}
			}
			echo json_encode($response);
		}


		public function printNoteAcctCreditsPayment(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditspayment'		=> "",
			);

			$data = array(
				'credits_payment_id'		=> $this->input->post('credits_payment_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctcreditspaymentlist	= $this->Android_model->getAcctCreditsPayment_Detail($data['credits_payment_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctcreditspaymentlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctcreditspayment[0]['company_name'] 						= $preferencecompany['company_name'];
						$acctcreditspayment[0]['member_name'] 						= $acctcreditspaymentlist['member_name'];
						$acctcreditspayment[0]['credits_account_serial']			= $acctcreditspaymentlist['credits_account_serial'];
						$acctcreditspayment[0]['member_address']					= $acctcreditspaymentlist['member_address'];
						$acctcreditspayment[0]['credits_payment_amount']			= "Rp. ".number_format($acctcreditspaymentlist['credits_payment_amount'], 2);
						$acctcreditspayment[0]['credits_payment_amount_str']		= numtotxt($acctcreditspaymentlist['credits_payment_amount']);
						$acctcreditspayment[0]['branch_city']						= $acctcreditspaymentlist['branch_city'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctcreditspayment'] 		= $acctcreditspayment;
					}
				}
			}
			echo json_encode($response);
		}

		public function getDailyDashboard(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'dailydashboard'			=> "",
			);

			$data = array(
				'user_id'					=> $this->input->post('user_id',true),
				'daily_dashboard_date'		=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$savings_cash_deposit_amount 		= $this->Android_model->getSavingsCashDepositAmount($data['user_id'], $data['daily_dashboard_date'], $cash_deposit_id);

				$savings_cash_withdrawal_amount 	= $this->Android_model->getSavingsCashDepositAmount($data['user_id'], $data['daily_dashboard_date'], $cash_withdrawal_id);

				$creditspaymentamount 				= $this->Android_model->getCreditsPaymentAmount($data['user_id'], $data['daily_dashboard_date']);

				$credits_payment_principal 			= $creditspaymentamount['credits_payment_principal'];

				$credits_payment_margin 			= $creditspaymentamount['credits_payment_margin'];

				if (empty($savings_cash_deposit_amount)){
					$savings_cash_deposit_amount = 0;
				}

				if (empty($savings_cash_withdrawal_amount)){
					$savings_cash_withdrawal_amount = 0;
				}

				if (empty($credits_payment_principal)){
					$credits_payment_principal = 0;
				}

				if (empty($credits_payment_margin)){
					$credits_payment_margin = 0;
				}
				
				$credits_payment_amount 			= $credits_payment_principal + $credits_payment_margin;

						
				$dailydashboard[0]['dashboard_setor_tunai'] 	= number_format($savings_cash_deposit_amount, 2);
				$dailydashboard[0]['dashboard_tarik_tunai']		= number_format($savings_cash_withdrawal_amount, 2);
				$dailydashboard[0]['dashboard_angsuran_tunai']	= number_format($credits_payment_amount, 2);
						
				$response['error'] 					= FALSE;
				$response['error_msg_title'] 		= "Success";
				$response['error_msg'] 				= "Data Exist";
				$response['dailydashboard'] 		= $dailydashboard;
				
			}
			echo json_encode($response);
		}

		public function getAcctSavingsCashMutation(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingscashmutation'		=> "",
			);

			$data = array(
				'member_id'						=> $this->input->post('member_id',true),
				'cash_savings_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['cash_deposit_id'], $preferencecompany['cash_withdrawal_id']);

				$acctsavingscashmutationlist	= $this->AcctSavingsCashMutation_model->getAcctSavingsCashMutation_Member($data['member_id'], $data['cash_savings_mutation_date'], $data_mutation);


				if(!$acctsavingscashmutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationlist as $key => $val) {
							$acctsavingscashmutation[$key]['savings_code']					= $val['savings_code'];
							$acctsavingscashmutation[$key]['savings_name']					= $val['savings_name'];
							$acctsavingscashmutation[$key]['mutation_name']					= $val['mutation_name'];
							$acctsavingscashmutation[$key]['savings_account_no']			= $val['savings_account_no'];
							$acctsavingscashmutation[$key]['savings_cash_mutation_date']	= tgltoview($val['savings_cash_mutation_date']);
							$acctsavingscashmutation[$key]['savings_cash_mutation_amount']	= "Rp. ".number_format($val['savings_cash_mutation_amount'], 2);
							$acctsavingscashmutation[$key]['savings_account_last_balance']	= "Rp. ".number_format($val['savings_account_last_balance'], 2);
						}
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingscashmutation'] 	= $acctsavingscashmutation;
					}
				}
			}
			echo json_encode($response);
		}

		public function getAcctCreditsPayment(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctcreditspayment'		=> "",
			);

			$data = array(
				'member_id'					=> $this->input->post('member_id',true),
				'credits_payment_date'		=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){
				$acctcreditspaymentlist	= $this->Android_model->getAcctCreditsPayment_Member($data['member_id'], $data['credits_payment_date']);

				if(!$acctcreditspaymentlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {	
						foreach ($acctcreditspaymentlist as $key => $val) {
							$credits_payment_amount = $val['credits_payment_principal'] + $val['credits_payment_margin'];

							$acctcreditspayment[$key]['credits_id']					= $val['credits_id'];
							$acctcreditspayment[$key]['credits_code']				= $val['credits_code'];
							$acctcreditspayment[$key]['credits_name']				= $val['credits_name'];
							$acctcreditspayment[$key]['credits_account_id']			= $val['credits_account_id'];
							$acctcreditspayment[$key]['credits_account_serial']		= $val['credits_account_serial'];
							$acctcreditspayment[$key]['credits_payment_amount']		= $credits_payment_amount;
							$acctcreditspayment[$key]['credits_payment_date']		= tgltoview($val['credits_payment_date']);
						}					

						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctcreditspayment'] 		= $acctcreditspayment;
					}
				}
			}
			echo json_encode($response);
		}

		public function getAcctSavingsAccountFromDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccountfrom'		=> "",
			);

			$data = array(
				'savings_account_id'		=> $this->input->post('savings_account_id',true),
			);


			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount_DetailAccount($data['savings_account_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$acctsavingsaccountfrom[0]['savings_account_id'] 			= $acctsavingsaccountlist['savings_account_id'];
						$acctsavingsaccountfrom[0]['savings_id']					= $acctsavingsaccountlist['savings_id'];
						$acctsavingsaccountfrom[0]['savings_code']					= $acctsavingsaccountlist['savings_code'];
						$acctsavingsaccountfrom[0]['savings_name']					= $acctsavingsaccountlist['savings_name'];
						$acctsavingsaccountfrom[0]['branch_id']						= $acctsavingsaccountlist['branch_id'];
						$acctsavingsaccountfrom[0]['savings_account_no']			= $acctsavingsaccountlist['savings_account_no'];
						$acctsavingsaccountfrom[0]['savings_account_last_balance']	= $acctsavingsaccountlist['savings_account_last_balance'];
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingsaccountfrom'] 	= $acctsavingsaccountfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsAccountToDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccountto'		=> "",
			);

			$data = array(
				'savings_account_id'		=> $this->input->post('savings_account_id',true),
			);



			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount_DetailAccount($data['savings_account_id']);

				
				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$acctsavingsaccountto[0]['savings_account_id'] 				= $acctsavingsaccountlist['savings_account_id'];
						$acctsavingsaccountto[0]['savings_id']						= $acctsavingsaccountlist['savings_id'];
						$acctsavingsaccountto[0]['savings_code']					= $acctsavingsaccountlist['savings_code'];
						$acctsavingsaccountto[0]['savings_name']					= $acctsavingsaccountlist['savings_name'];
						$acctsavingsaccountto[0]['branch_id']						= $acctsavingsaccountlist['branch_id'];
						$acctsavingsaccountto[0]['member_id']						= $acctsavingsaccountlist['member_id'];
						$acctsavingsaccountto[0]['member_no']						= $acctsavingsaccountlist['member_no'];
						$acctsavingsaccountto[0]['member_name']						= $acctsavingsaccountlist['member_name'];
						$acctsavingsaccountto[0]['savings_account_no']				= $acctsavingsaccountlist['savings_account_no'];
						$acctsavingsaccountto[0]['savings_account_last_balance']	= $acctsavingsaccountlist['savings_account_last_balance'];
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccountto'] 	= $acctsavingsaccountto;
					}
				}
			}
			echo json_encode($response);
		}

		public function processAddAcctSavingsTransferMutation(){
			$auth = $this->session->userdata('auth');

			

			$data = array(
				'branch_id'								=> $this->input->post('branch_from_id', true),
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_status'		=> 1,
				'operated_name'							=> $this->input->post('username', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			/* $data = array(
				'branch_id'								=> 2,
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> 5000,
				'savings_transfer_mutation_status'		=> 1,
				'operated_name'							=> "NURKHOLISON",
				'created_id'							=> 32887,
				'created_on'							=> date('Y-m-d H:i:s'),
			); */

			$response = array(
				'error'											=> FALSE,
				'error_acctsavingstransfermutation'				=> FALSE,
				'error_msg_title_acctsavingstransfermutation'	=> "",
				'error_msg_acctsavingstransfermutation'			=> "",
			);

			if($response["error_acctsavingstransfermutation"] == FALSE){
				if(!empty($data)){	

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data)){
						$transaction_module_code 	= "MbAYAR";

						$transaction_module_id 		= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);

						$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_id']);
							
						$journal_voucher_period 	= date("Ym", strtotime($data['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			= $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);

						$savings_transfer_mutation_id 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);

						$preferencecompany 				= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();
						
						$preferenceppob 				= $this->Android_model->getPreferencePPOB();

						

						$datafrom = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $this->input->post('savings_account_from_id', true),
							'savings_id'								=> $this->input->post('savings_from_id', true),
							'member_id'									=> $this->input->post('member_from_id', true),
							'branch_id'									=> $this->input->post('branch_from_id', true),
							'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
							'savings_account_opening_balance'			=> $this->input->post('savings_account_from_opening_balance', true),
							'savings_transfer_mutation_from_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
							'savings_account_last_balance'				=> $this->input->post('savings_account_from_last_balance', true),
						);

						/* $datafrom = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> 66127,
							'savings_id'								=> 15,
							'member_id'									=> 32887,
							'branch_id'									=> 2,
							'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
							'savings_account_opening_balance'			=> 25000.00,
							'savings_transfer_mutation_from_amount'		=> 5000,
							'savings_account_last_balance'				=> 20000.00,
						); */

						$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datafrom['member_id']);

						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
							$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

							$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'NOTA DEBET '.$member_name,
								'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_transfer_mutation_amount'],
								'account_id_status'				=> 1,
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
						}
						
						$data_admin = array(
							'savings_account_id'			=> $datafrom['savings_account_id'],
							'savings_account_last_balance'	=> $datafrom['savings_account_last_balance'] - $preferenceppob['ppob_mbayar_admin']
						);

						$datasavingsdetail = array(
							'branch_id'					=> $datafrom['branch_id'],
							'savings_account_id'		=> $datafrom['savings_account_id'],
							'savings_id'				=> $datafrom['savings_id'],
							'member_id'					=> $datafrom['member_id'],
							'mutation_id'				=> $preferenceppob['ppob_adm_mutation_id'],
							'today_transaction_date'	=> date('Y-m-d'),
							'yesterday_transaction_date'=> date('Y-m-d'),
							'transaction_code'			=> 'Admin Mbayar',
							'opening_balance'			=> $datafrom['savings_account_last_balance'],
							'mutation_out'				=> $preferenceppob['ppob_mbayar_admin'],
							'last_balance'				=> $datafrom['savings_account_last_balance'] - $preferenceppob['ppob_mbayar_admin'],
							'operated_name'				=> 'SYSTEM'
						);

						if($this->Android_model->updateAcctSavingsAccount($data_admin, $datasavingsdetail)){
							$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

							$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'Admin mbayar '.$member_name,
								'journal_voucher_amount'		=> $preferenceppob['ppob_mbayar_admin'],
								'journal_voucher_debit_amount'	=> $preferenceppob['ppob_mbayar_admin'],
								'account_id_status'				=> 1,
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);

							$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income_mbayar']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferenceppob['ppob_account_income_mbayar'],
								'journal_voucher_description'	=> 'Admin Mbayar '.$member_name,
								'journal_voucher_amount'		=> $preferenceppob['ppob_mbayar_admin'],
								'journal_voucher_credit_amount'	=> $preferenceppob['ppob_mbayar_admin'],
								'account_id_status'				=> 0,
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);

						}

						/* savings_account_from_id=66127&savings_from_id=15&member_from_id=32887&branch_from_id=2&savings_account_from_opening_balance=25000.00&savings_account_from_last_balance=20000.0&user_id=32887&username=NURKHOLISON%2C%20SE&savings_transfer_mutation_amount=5000&savings_account_to_id=31011&savings_to_id=4&member_to_id=32887&branch_to_id=2&member_password=123456&savings_account_to_opening_balance=14506.68&savings_account_to_last_balance=19506.68 */


						$datato = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $this->input->post('savings_account_to_id', true),
							'savings_id'								=> $this->input->post('savings_to_id', true),
							'member_id'									=> $this->input->post('member_to_id', true),
							'branch_id'									=> $this->input->post('branch_to_id', true),
							'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
							'savings_account_opening_balance'			=> $this->input->post('savings_account_to_opening_balance', true),
							'savings_transfer_mutation_to_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
							'savings_account_last_balance'				=> $this->input->post('savings_account_to_last_balance', true),
						);

						/* $datato = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> 31011,
							'savings_id'								=> 4,
							'member_id'									=> 32887,
							'branch_id'									=> 2,
							'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
							'savings_account_opening_balance'			=> 14506.68,
							'savings_transfer_mutation_to_amount'		=> 5000,
							'savings_account_last_balance'				=> 19506.68,
						); */

						$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datato['member_id']);

						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){
							$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

							$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'NOTA KREDIT '.$member_name,
								'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
								'journal_voucher_credit_amount'	=> $data['savings_transfer_mutation_amount'],
								'account_id_status'				=> 0,
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
						}

						$response['error_acctsavingstransfermutation'] 	= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
					}else{
						$response['error_acctsavingstransfermutation'] 	= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
					}
				}
			}

			echo json_encode($response);

		}

		public function printAcctSavingsTransferMutationFrom(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'									=> FALSE,
				'error_msg'								=> "",
				'error_msg_title'						=> "",
				'acctsavingstransfermutationfrom'		=> "",
			);

			$data = array(
				'savings_transfer_mutation_id'		=> $this->input->post('savings_transfer_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom_DetailPrint($data['savings_transfer_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingstransfermutationfrom[0]['company_name'] 							= $preferencecompany['company_name'];
						$acctsavingstransfermutationfrom[0]['member_name'] 								= $acctsavingstransfermutationlist['member_name'];
						$acctsavingstransfermutationfrom[0]['savings_account_no']						= $acctsavingstransfermutationlist['savings_account_no'];
						$acctsavingstransfermutationfrom[0]['member_address']							= $acctsavingstransfermutationlist['member_address'];
						$acctsavingstransfermutationfrom[0]['savings_transfer_mutation_amount']			= "Rp. ".number_format($acctsavingstransfermutationlist['savings_transfer_mutation_from_amount'], 2);
						$acctsavingstransfermutationfrom[0]['savings_transfer_mutation_amount_str']		= numtotxt($acctsavingstransfermutationlist['savings_transfer_mutation_from_amount']);

						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingstransfermutationfrom'] 	= $acctsavingstransfermutationfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function printAcctSavingsTransferMutationTo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationto'		=> "",
			);

			$data = array(
				'savings_transfer_mutation_id'		=> $this->input->post('savings_transfer_mutation_id',true),
			);

			$preferencecompany = $this->Android_model->getPreferenceCompany();

			if($response["error"] == FALSE){
				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationTo_DetailPrint($data['savings_transfer_mutation_id']);

				/*print_r("acctsavingscashmutationlist ");
				print_r($acctsavingscashmutationlist);*/

				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {						
						$acctsavingstransfermutationto[0]['company_name'] 								= $preferencecompany['company_name'];
						$acctsavingstransfermutationto[0]['member_name'] 								= $acctsavingstransfermutationlist['member_name'];
						$acctsavingstransfermutationto[0]['savings_account_no']							= $acctsavingstransfermutationlist['savings_account_no'];
						$acctsavingstransfermutationto[0]['member_address']								= $acctsavingstransfermutationlist['member_address'];
						$acctsavingstransfermutationto[0]['savings_transfer_mutation_amount']			= "Rp. ".number_format($acctsavingstransfermutationlist['savings_transfer_mutation_to_amount'], 2);
						$acctsavingstransfermutationto[0]['savings_transfer_mutation_amount_str']		= numtotxt($acctsavingstransfermutationlist['savings_transfer_mutation_to_amount']);

						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctsavingstransfermutationto'] 	= $acctsavingstransfermutationto;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsTransferMutationFrom(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationfrom'	=> "",
			);

			$data = array(
				'member_id'							=> $this->input->post('member_id',true),
				'savings_transfer_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['account_savings_transfer_from_id'], $preferencecompany['account_savings_transfer_to_id']);

				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationFrom_Member($data['member_id'], $data['savings_transfer_mutation_date'], $data_mutation);


				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingstransfermutationlist as $key => $val) {
							$acctsavingstransfermutationfrom[$key]['savings_code']						= $val['savings_code'];
							$acctsavingstransfermutationfrom[$key]['savings_name']						= $val['savings_name'];
							$acctsavingstransfermutationfrom[$key]['mutation_name']						= $val['mutation_name'];
							$acctsavingstransfermutationfrom[$key]['savings_account_no']				= $val['savings_account_no'];
							$acctsavingstransfermutationfrom[$key]['savings_transfer_mutation_date']	= tgltoview($val['savings_transfer_mutation_date']);
							$acctsavingstransfermutationfrom[$key]['savings_transfer_mutation_amount']	= $val['savings_transfer_mutation_from_amount'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingstransfermutationfrom'] 	= $acctsavingstransfermutationfrom;
					}
				}
			}
			echo json_encode($response);
		}


		public function getAcctSavingsTransferMutationTo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingstransfermutationto'		=> "",
			);

			$data = array(
				'member_id'							=> $this->input->post('member_id',true),
				'savings_transfer_mutation_date'	=> date("Y-m-d"),
			);


			if($response["error"] == FALSE){
				$preferencecompany = $this->Android_model->getPreferenceCompany();

				$data_mutation = array ($preferencecompany['account_savings_transfer_from_id'], $preferencecompany['account_savings_transfer_to_id']);

				$acctsavingstransfermutationlist	= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutationTo_Member($data['member_id'], $data['savings_transfer_mutation_date'], $data_mutation);


				if(!$acctsavingstransfermutationlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingstransfermutationlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingstransfermutationlist as $key => $val) {
							$acctsavingstransfermutationto[$key]['savings_code']						= $val['savings_code'];
							$acctsavingstransfermutationto[$key]['savings_name']						= $val['savings_name'];
							$acctsavingstransfermutationto[$key]['mutation_name']						= $val['mutation_name'];
							$acctsavingstransfermutationto[$key]['savings_account_no']					= $val['savings_account_no'];
							$acctsavingstransfermutationto[$key]['savings_transfer_mutation_date']		= tgltoview($val['savings_transfer_mutation_date']);	
							$acctsavingstransfermutationto[$key]['savings_transfer_mutation_amount']	= $val['savings_transfer_mutation_to_amount'];
						}
						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctsavingstransfermutationto'] 	= $acctsavingstransfermutationto;
					}
				}
			}
			echo json_encode($response);
		}

		public function processEditSystemUserPassword(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'user_id'			=> $this->input->post('user_id', true),
				'password'			=> md5($this->input->post('password', true)),
				'new_password'		=> md5($this->input->post('new_password', true)),
			);


			$response = array(
				'error'									=> FALSE,
				'error_systemuserpassword'				=> FALSE,
				'error_msg_title_systemuserpassword'	=> "",
				'error_msg_systemuserpassword'			=> "",
			);

			if($response["error_systemuserpassword"] == FALSE){
				if ($data_systemuser = $this->Android_model->getSystemUserPassword($data['user_id'], $data['password'])){
					$dataupdate = array(
						'user_id'			=>	$data['user_id'],
						'password'			=>	$data['new_password'],
					);

					if ($this->Android_model->updateSystemUser($dataupdate)){

						$response['error'] 				= FALSE;
						$response['error_msg_title'] 	= "Ganti Password Berhasil";
						$response['error_msg'] 			= "Data Berhasil";		
					} else {
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "Ganti Password Gagal";
						$response['error_msg'] 			= "Data Gagal";		
					}
				} else {
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "Ganti Password Gagal";
					$response['error_msg'] 			= "Password User Salah";		
				}
				

			} 

			echo json_encode($response);

		}


		public function processEditCoreMemberPassword(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'member_id'					=> $this->input->post('member_id', true),
				'member_password'			=> md5($this->input->post('member_password', true)),
				'member_new_password'		=> md5($this->input->post('member_new_password', true)),
			);

			/*print_r("data ");
			print_r($data);*/

			$response = array(
				'error'									=> FALSE,
				'error_corememberpassword'				=> FALSE,
				'error_msg_title_corememberpassword'	=> "",
				'error_msg_corememberpassword'			=> "",
			);

			if($response["error_corememberpassword"] == FALSE){
				if ($data_coremember = $this->Android_model->getCoreMemberPassword($data['member_id'], $data['member_password'])){
					$dataupdate = array(
						'member_id'				=>	$data['member_id'],
						'member_password'		=>	$data['member_new_password'],
					);

					if ($this->Android_model->updateCoreMember($dataupdate)){

						$response['error'] 				= FALSE;
						$response['error_msg_title'] 	= "Ganti Password Berhasil";
						$response['error_msg'] 			= "Data Berhasil";		
					} else {
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "Ganti Password Gagal";
						$response['error_msg'] 			= "Data Gagal";		
					}
				} else {
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "Ganti Password Gagal";
					$response['error_msg'] 			= "Password Member Salah";		
				}
				

			} 

			echo json_encode($response);

		}

		public function getCoreMemberNo_Detail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'corememberdetail'		=> "",
			);

			$data = array(
				'member_no'		=> $this->input->post('member_no',true),
				'user_id'		=> $this->input->post('user_id',true),
				'branch_id'		=> $this->input->post('branch_id',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$systemuserdusun		= $this->Android_model->getSystemUserDusun($data['user_id']);

					$corememberdetaillist 	= $this->Android_model->getCoreMemberNo_Detail($data['member_no'], $data['branch_id'], $systemuserdusun);

					if(!$corememberdetaillist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($corememberdetaillist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity = $this->configuration->MemberIdentity();

							/*print_r("memberidentity ");
							print_r($memberidentity);*/

							$member_address 	= $corememberdetaillist['member_address'].' '.$corememberdetaillist['province_name'].' '.$corememberdetaillist['city_name'].' '.$corememberdetaillist['kecamatan_name'];

							$member_identity_no = '( '.$memberidentity[$corememberdetaillist['member_identity']].' ) '.$corememberdetaillist['member_identity_no'];

							$corememberdetail[0]['branch_id']				= $corememberdetaillist['branch_id'];
							$corememberdetail[0]['branch_name']				= $corememberdetaillist['branch_name'];
							$corememberdetail[0]['member_id']				= $corememberdetaillist['member_id'];
							$corememberdetail[0]['member_no']				= $corememberdetaillist['member_no'];
							$corememberdetail[0]['member_name']				= $corememberdetaillist['member_name'];
							$corememberdetail[0]['member_address']			= $member_address;
							$corememberdetail[0]['member_identity_no']		= $member_identity_no;
								
							$response['error'] 					= FALSE;
							$response['error_msg_title'] 		= "Success";
							$response['error_msg'] 				= "Data Exist";
							$response['corememberdetailno'] 	= $corememberdetail;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}


		public function getAcctSavingsAccountDetailNo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingsaccountdetailno'		=> "",
			);

			$data = array(
				'branch_id'				=> $this->input->post('branch_id',true),
				'member_id'				=> $this->input->post('member_id',true),
				'savings_account_no'	=> $this->input->post('savings_account_no',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			/*print_r("now ");
			print_r($now);
			print_r("<BR>");

			print_r("user_time_limit ");
			print_r($user_time_limit);
			print_r("<BR>");*/

			if ($now <= $user_time_limit){
				if($response["error"] == FALSE){
					$acctsavingsaccountdetailnolist = $this->Android_model->getAcctSavingsAccountDetailNo($data['savings_account_no']);

					if(!$acctsavingsaccountdetailnolist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctsavingsaccountdetailnolist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							
                            $memberidentity 	= $this->configuration->MemberIdentity();

							$member_address 	= $acctsavingsaccountdetailnolist['member_address'].' '.$acctsavingsaccountdetailnolist['province_name'].' '.$acctsavingsaccountdetailnolist['city_name'].' '.$acctsavingsaccountdetailnolist['kecamatan_name'];

							$member_identity_no = '( '.$memberidentity[$acctsavingsaccountdetailnolist['member_identity']].' ) '.$acctsavingsaccountdetailnolist['member_identity_no'];

							$acctsavingsaccountdetailno[0]['member_id'] 							= $acctsavingsaccountdetailnolist['member_id'];
							$acctsavingsaccountdetailno[0]['member_no'] 							= $acctsavingsaccountdetailnolist['member_no'];
							$acctsavingsaccountdetailno[0]['member_name'] 							= $acctsavingsaccountdetailnolist['member_name'];
							$acctsavingsaccountdetailno[0]['member_address'] 						= $member_address;
							$acctsavingsaccountdetailno[0]['member_identity_no'] 					= $member_identity_no;
							$acctsavingsaccountdetailno[0]['savings_account_id'] 					= $acctsavingsaccountdetailnolist['savings_account_id'];
							$acctsavingsaccountdetailno[0]['savings_id']							= $acctsavingsaccountdetailnolist['savings_id'];
							$acctsavingsaccountdetailno[0]['savings_code']							= $acctsavingsaccountdetailnolist['savings_code'];
							$acctsavingsaccountdetailno[0]['savings_name']							= $acctsavingsaccountdetailnolist['savings_name'];
							$acctsavingsaccountdetailno[0]['savings_account_no']					= $acctsavingsaccountdetailnolist['savings_account_no'];
							$acctsavingsaccountdetailno[0]['savings_account_first_deposit_amount']	= $acctsavingsaccountdetailnolist['savings_account_first_deposit_amount'];
							$acctsavingsaccountdetailno[0]['savings_account_last_balance']			= $acctsavingsaccountdetailnolist['savings_account_last_balance'];
							
							
							$response['error'] 							= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['acctsavingsaccountdetailno'] 	= $acctsavingsaccountdetailno;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}

			echo json_encode($response);
		}


		public function getAcctCreditsAccountDetailNo(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctcreditsaccountdetailno'	=> "",
			);

			$data = array(
				'branch_id'					=> $this->input->post('branch_id',true),
				'member_id'					=> $this->input->post('member_id',true),
				'credits_account_serial'	=> $this->input->post('credits_account_no',true),
			);

			$preferencecompany 		= $this->Android_model->getPreferenceCompany();

			$user_time_limit 		= strtotime(date("Y-m-d")." ".$preferencecompany['user_time_limit']);

			$now 					= strtotime(date("Y-m-d H:i:s"));

			if ($now <= $user_time_limit){

				if($response["error"] == FALSE){
					$acctcreditsaccountseriallist = $this->Android_model->getAcctCreditsAccountDetailSerial($data['credits_account_serial']);

					/*print_r("acctcreditsaccountseriallist ");
					print_r($acctcreditsaccountseriallist);
*/
					if(!$acctcreditsaccountseriallist){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Error Query Data";
					}else{
						if (empty($acctcreditsaccountseriallist)){
							$response['error'] 				= TRUE;
							$response['error_msg_title'] 	= "No Data";
							$response['error_msg'] 			= "Data Does Not Exist";
						} else {
							$memberidentity 						= $this->configuration->MemberIdentity();

							$member_address 						= $acctcreditsaccountseriallist['member_address'].' '.$acctcreditsaccountseriallist['province_name'].' '.$acctcreditsaccountseriallist['city_name'].' '.$acctcreditsaccountseriallist['kecamatan_name'];

							$member_identity_no 					= '( '.$memberidentity[$acctcreditsaccountseriallist['member_identity']].' ) '.$acctcreditsaccountseriallist['member_identity_no'];
							
							$credits_account_last_balance_principal = $acctcreditsaccountseriallist['credits_account_last_balance_principal'];
							$credits_account_last_balance_margin	= $acctcreditsaccountseriallist['credits_account_last_balance_margin'];

							$total_last_balance = $credits_account_last_balance_principal + $credits_account_last_balance_margin;

							$credits_account_id = $acctcreditsaccountseriallist['credits_account_id'];

							$detailpayment		= $this->AcctCashPayment_model->getDataByIDCredit($credits_account_id);

							$credits_payment_to = count($detailpayment) + 1;

							$acctcreditsaccountdetailno[0]['member_id'] 								= $acctcreditsaccountseriallist['member_id'];
							$acctcreditsaccountdetailno[0]['member_no'] 								= $acctcreditsaccountseriallist['member_no'];
							$acctcreditsaccountdetailno[0]['member_name'] 								= $acctcreditsaccountseriallist['member_name'];
							$acctcreditsaccountdetailno[0]['member_address'] 							= $member_address;
							$acctcreditsaccountdetailno[0]['member_identity_no'] 						= $member_identity_no;

							$acctcreditsaccountdetailno[0]['credits_account_id'] 						= $acctcreditsaccountseriallist['credits_account_id'];
							$acctcreditsaccountdetailno[0]['credits_account_no'] 						= $acctcreditsaccountseriallist['credits_account_no'];
							$acctcreditsaccountdetailno[0]['credits_id']								= $acctcreditsaccountseriallist['credits_id'];
							$acctcreditsaccountdetailno[0]['credits_code']								= $acctcreditsaccountseriallist['credits_code'];
							$acctcreditsaccountdetailno[0]['credits_name']								= $acctcreditsaccountseriallist['credits_name'];
							$acctcreditsaccountdetailno[0]['credits_account_serial']					= $acctcreditsaccountseriallist['credits_account_serial'];
							$acctcreditsaccountdetailno[0]['credits_account_period']					= $acctcreditsaccountseriallist['credits_account_period'];
							$acctcreditsaccountdetailno[0]['credits_payment_to']						= $credits_payment_to;
							$acctcreditsaccountdetailno[0]['credits_account_last_balance_principal']	= $acctcreditsaccountseriallist['credits_account_last_balance_principal'];
							$acctcreditsaccountdetailno[0]['credits_account_last_balance_margin']		= $acctcreditsaccountseriallist['credits_account_last_balance_margin'];
							
							
							$response['error'] 							= FALSE;
							$response['error_msg_title'] 				= "Success";
							$response['error_msg'] 						= "Data Exist";
							$response['acctcreditsaccountdetailno'] 	= $acctcreditsaccountdetailno;
						}
					}
				}
			} else {
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Waktu Transaksi Sudah Habis";
			}
			echo json_encode($response);
		}


		public function getAcctSavingsCashMutationDeposit(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingscashmutationdeposit'	=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'savings_cash_mutation_date'	=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$acctsavingscashmutationdepositlist	= $this->Android_model->getAcctSavingsCashMutation($data['user_id'], $data['savings_cash_mutation_date'], $cash_deposit_id);

				if(!$acctsavingscashmutationdepositlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationdepositlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationdepositlist as $key => $val) {
							$acctsavingscashmutationdeposit[$key]['member_id']								= $val['member_id'];
							$acctsavingscashmutationdeposit[$key]['member_no']								= $val['member_no'];
							$acctsavingscashmutationdeposit[$key]['member_name']							= $val['member_name'];
							$acctsavingscashmutationdeposit[$key]['savings_account_id']						= $val['savings_account_id'];
							$acctsavingscashmutationdeposit[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingscashmutationdeposit[$key]['savings_id']								= $val['savings_id'];
							$acctsavingscashmutationdeposit[$key]['savings_code']							= $val['savings_code'];
							$acctsavingscashmutationdeposit[$key]['savings_name']							= $val['savings_name'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_date']				= tgltoview($val['savings_cash_mutation_date']);	
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_opening_balance']	= $val['savings_cash_mutation_opening_balance'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_amount']			= $val['savings_cash_mutation_amount'];
							$acctsavingscashmutationdeposit[$key]['savings_cash_mutation_last_balance']		= $val['savings_cash_mutation_last_balance'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingscashmutationdeposit'] 	= $acctsavingscashmutationdeposit;
					}
				}
				
			}
			echo json_encode($response);
		}


		public function getAcctSavingsCashMutationWithdraw(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctsavingscashmutationwithdraw'	=> "",
			);

			$data = array(
				'user_id'						=> $this->input->post('user_id',true),
				'savings_cash_mutation_date'	=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){

				$preferencecompany 		= $this->Android_model->getPreferenceCompany();
				$cash_deposit_id 		= $preferencecompany['cash_deposit_id'];
				$cash_withdrawal_id 	= $preferencecompany['cash_withdrawal_id'];

				$acctsavingscashmutationwithdrawlist	= $this->Android_model->getAcctSavingsCashMutation($data['user_id'], $data['savings_cash_mutation_date'], $cash_withdrawal_id);

				if(!$acctsavingscashmutationwithdrawlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingscashmutationwithdrawlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctsavingscashmutationwithdrawlist as $key => $val) {
							$acctsavingscashmutationwithdraw[$key]['member_id']								= $val['member_id'];
							$acctsavingscashmutationwithdraw[$key]['member_no']								= $val['member_no'];
							$acctsavingscashmutationwithdraw[$key]['member_name']							= $val['member_name'];
							$acctsavingscashmutationwithdraw[$key]['savings_account_id']						= $val['savings_account_id'];
							$acctsavingscashmutationwithdraw[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingscashmutationwithdraw[$key]['savings_id']								= $val['savings_id'];
							$acctsavingscashmutationwithdraw[$key]['savings_code']							= $val['savings_code'];
							$acctsavingscashmutationwithdraw[$key]['savings_name']							= $val['savings_name'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_date']				= tgltoview($val['savings_cash_mutation_date']);	
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_opening_balance']	= $val['savings_cash_mutation_opening_balance'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_amount']			= $val['savings_cash_mutation_amount'];
							$acctsavingscashmutationwithdraw[$key]['savings_cash_mutation_last_balance']		= $val['savings_cash_mutation_last_balance'];
						}
						
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctsavingscashmutationwithdraw'] 	= $acctsavingscashmutationwithdraw;
					}
				}
				
			}
			echo json_encode($response);
		}


		public function getAcctCreditsPaymentDashboard(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctcreditspaymentdashboard'		=> "",
			);

			$data = array(
				'user_id'					=> $this->input->post('user_id',true),
				'credits_payment_date'		=> date("Y-m-d"),
			);



			if($response["error"] == FALSE){

				$acctcreditspaymentdashboardlist	= $this->Android_model->getAcctCreditsPayment($data['user_id'], $data['credits_payment_date']);

				if(!$acctcreditspaymentdashboardlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentdashboardlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {				
						foreach ($acctcreditspaymentdashboardlist as $key => $val) {
							$credits_principal_opening_balance 	= $val['credits_principal_opening_balance'];
							$credits_margin_opening_balance 	= $val['credits_margin_opening_balance'];

							$credits_payment_opening_balance 	= $credits_principal_opening_balance + $credits_margin_opening_balance;

							$credits_principal_last_balance 	= $val['credits_principal_last_balance'];
							$credits_margin_last_balance 		= $val['credits_margin_last_balance'];

							$credits_payment_last_balance 		= $credits_principal_last_balance + $credits_margin_last_balance;


							$acctcreditspaymentdashboard[$key]['member_id']							= $val['member_id'];
							$acctcreditspaymentdashboard[$key]['member_no']							= $val['member_no'];
							$acctcreditspaymentdashboard[$key]['member_name']						= $val['member_name'];
							$acctcreditspaymentdashboard[$key]['credits_account_id']				= $val['credits_account_id'];
							$acctcreditspaymentdashboard[$key]['credits_account_serial']			= $val['credits_account_serial'];
							$acctcreditspaymentdashboard[$key]['credits_id']						= $val['credits_id'];
							$acctcreditspaymentdashboard[$key]['credits_name']						= $val['credits_name'];
							$acctcreditspaymentdashboard[$key]['credits_payment_date']				= tgltoview($val['credits_payment_date']);	
							$acctcreditspaymentdashboard[$key]['credits_payment_opening_balance']	= $credits_payment_opening_balance;
							$acctcreditspaymentdashboard[$key]['credits_payment_amount']			= $val['credits_payment_amount'];
							$acctcreditspaymentdashboard[$key]['credits_payment_last_balance']		= $credits_payment_last_balance;
						}
						
						$response['error'] 							= FALSE;
						$response['error_msg_title'] 				= "Success";
						$response['error_msg'] 						= "Data Exist";
						$response['acctcreditspaymentdashboard'] 	= $acctcreditspaymentdashboard;
					}
				}
				
			}
			echo json_encode($response);
		}

		public function getAcctSavingsAccountMember(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'acctsavingsaccount'		=> "",
			);

			$data = array(
				'branch_id'		=> $this->input->post('branch_id',true),
				'member_id'		=> $this->input->post('member_id',true),
			);

			
			if($response["error"] == FALSE){
				$acctsavingsaccountlist = $this->Android_model->getAcctSavingsAccount($data['member_id']);

				if(!$acctsavingsaccountlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						foreach ($acctsavingsaccountlist as $key => $val) {
							$acctsavingsaccount[$key]['savings_account_id'] 					= $val['savings_account_id'];
							$acctsavingsaccount[$key]['savings_id']								= $val['savings_id'];
							$acctsavingsaccount[$key]['savings_code']							= $val['savings_code'];
							$acctsavingsaccount[$key]['savings_name']							= $val['savings_name'];
							$acctsavingsaccount[$key]['savings_account_no']						= $val['savings_account_no'];
							$acctsavingsaccount[$key]['savings_account_last_balance']			= $val['savings_account_last_balance'];
						}
						
						$response['error'] 					= FALSE;
						$response['error_msg_title'] 		= "Success";
						$response['error_msg'] 				= "Data Exist";
						$response['acctsavingsaccount'] 	= $acctsavingsaccount;
					}
				}
			}
			

			echo json_encode($response);
		}


		public function getAcctSavingsAccountDetail(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(	
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'acctsavingsaccountdetail'		=> "",
			);

			$data = array(
				'savings_account_id'		=> $this->input->post('savings_account_id',true),
			);

			
			if($response["error"] == FALSE){
				$acctsavingsaccountdetaillist = $this->Android_model->getAcctSavingsAccountDetail($data['savings_account_id']);

				if(!$acctsavingsaccountdetaillist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctsavingsaccountdetaillist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						/*krsort($acctsavingsaccountdetaillist);*/

						/*print_r("acctsavingsaccountdetaillist ");
						print_r($acctsavingsaccountdetaillist);*/

						foreach ($acctsavingsaccountdetaillist as $key => $val) {
							$acctsavingsaccountdetail[$key]['savings_account_id'] 		= $val['savings_account_id'];
							$acctsavingsaccountdetail[$key]['mutation_id']				= $val['mutation_id'];
							$acctsavingsaccountdetail[$key]['mutation_code']			= $val['mutation_code'];
							$acctsavingsaccountdetail[$key]['mutation_name']			= $val['mutation_name'];
							$acctsavingsaccountdetail[$key]['today_transaction_date']	= tgltoview($val['today_transaction_date']);
							$acctsavingsaccountdetail[$key]['mutation_in']				= $val['mutation_in'];
							$acctsavingsaccountdetail[$key]['mutation_out']				= $val['mutation_out'];
							$acctsavingsaccountdetail[$key]['last_balance']				= $val['last_balance'];
						}

						/*print_r("acctsavingsaccountdetail ");
						print_r($acctsavingsaccountdetail);*/
						
						$response['error'] 						= FALSE;
						$response['error_msg_title'] 			= "Success";
						$response['error_msg'] 					= "Data Exist";
						$response['acctsavingsaccountdetail'] 	= $acctsavingsaccountdetail;
					}
				}
			}
			

			echo json_encode($response);
		}

		// HISTORI TRANSAKSI ANGSURAN

		public function getAcctCreditsPaymentHistory(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'								=> FALSE,
				'error_msg'							=> "",
				'error_msg_title'					=> "",
				'acctcreditspaymenthistory'			=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			/* $data['member_id']		= 32890; */

			if($response["error"] == FALSE){

				$preferencecompany 				= $this->Android_model->getPreferenceCompany();

				$acctcreditspaymentlist			= $this->Android_model->getAcctCreditsPayment_History($data['member_id']);
	
				
				if(!$acctcreditspaymentlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($acctcreditspaymentlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {
						$creditspaymentstatus = $this->configuration->CreditsPaymentStatus();

						foreach ($acctcreditspaymentlist as $key => $val){
							$payment_transaction_description = 'Angsuran atas pembiayaan dengan No. Akad '.$val['credits_account_serial'].' melalui '.$creditspaymentstatus[$val['credits_payment_status']];
							

							$acctcreditspaymenthistory[$key]['payment_transaction_title']			= 'Angsuran';
							$acctcreditspaymenthistory[$key]['payment_transaction_date']			= date('d M Y H:i:s', strtotime($val['created_on']));
							$acctcreditspaymenthistory[$key]['payment_transaction_description']		= $payment_transaction_description;
							$acctcreditspaymenthistory[$key]['payment_transaction_amount']			= $val['credits_payment_amount'];
						}
							
						$response['error'] 								= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['acctcreditspaymenthistory'] 	= $acctcreditspaymenthistory;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI ANGSURAN

		//------------------------------- Z I S W A F -------------------------------------

		// ADD ZISWAF 

		public function processAddAcctSavingsZiswafMutation(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'branch_id'								=> $this->input->post('branch_from_id', true),
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
				'savings_transfer_mutation_status'		=> 2,
				'operated_name'							=> $this->input->post('username', true),
				'created_id'							=> $this->input->post('user_id', true),
				'created_on'							=> date('Y-m-d H:i:s'),
			);

			

			/* $data = array(
				'branch_id'								=> 2,
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> 1000,
				'savings_transfer_mutation_status'		=> 2,
				'operated_name'							=> '00000001',
				'created_id'							=> 32887,
				'created_on'							=> date('Y-m-d H:i:s'),
			); */

		
			
			$response = array(
				'error'										=> FALSE,
				'error_insertacctsavingsziswaf'				=> FALSE,
				'error_msg_title_insertacctsavingsziswaf'	=> "",
				'error_msg_insertacctsavingsziswaf'			=> "",
			);

			if($response["error_insertacctsavingsziswaf"] == FALSE){
				if(!empty($data)){	

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data)){
						$transaction_module_code 		= "ZISWAF";

						$transaction_module_id 			= $this->AcctSavingsTransferMutation_model->getTransactionModuleID($transaction_module_code);

						$savings_transfer_mutation_id 	= $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);



						$preferencecompany 				= $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

						//----- Simpan data transfer from

						

						$datafrom = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> $this->input->post('savings_account_from_id', true),
							'savings_id'								=> $this->input->post('savings_from_id', true),
							'member_id'									=> $this->input->post('member_from_id', true),
							'branch_id'									=> $this->input->post('branch_from_id', true),
							'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
							'savings_account_opening_balance'			=> $this->input->post('savings_account_from_opening_balance', true),
							'savings_transfer_mutation_from_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
							'savings_account_last_balance'				=> $this->input->post('savings_account_from_opening_balance', true) - $this->input->post('savings_transfer_mutation_amount', true),
						);

						/* $datafrom = array (
							'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
							'savings_account_id'						=> 31014,
							'savings_id'								=> 14,
							'member_id'									=> 32887,
							'branch_id'									=> 2,
							'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
							'savings_account_opening_balance'			=> 141103.56,
							'savings_transfer_mutation_from_amount'		=> 1000,
							'savings_account_last_balance'				=> 141103.56 - 1000,
						); */

						$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datafrom['member_id']);

						if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){

							//----- Simpan data jurnal
							$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data['created_id']);
							
							$journal_voucher_period 	= date("Ym", strtotime($data['savings_transfer_mutation_date']));
							
							$data_journal = array(
								'branch_id'						=> $data['branch_id'],
								'journal_voucher_period' 		=> $journal_voucher_period,
								'journal_voucher_date'			=> date('Y-m-d'),
								'journal_voucher_title'			=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
								'journal_voucher_description'	=> 'TRANSFER ANTAR REKENING '.$acctsavingstr_last['member_name'],
								'transaction_module_id'			=> $transaction_module_id,
								'transaction_module_code'		=> $transaction_module_code,
								'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
								'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
								'created_id' 					=> $data['created_id'],
								'created_on' 					=> $data['created_on'],
							);
							
							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

							$journal_voucher_id 			= $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data['created_id']);

							
							//----- Simpan data jurnal debit
							$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

							$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'NOTA DEBET '.$member_name,
								'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
								'journal_voucher_debit_amount'	=> $data['savings_transfer_mutation_amount'],
								'account_id_status'				=> 1,
							);

							$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


							//----- Simpan data transfer to
							$savingsaccountto 	= $this->AcctSavingsTransferMutation_model->getAcctSavingsAccount_Detail($preferencecompany['savings_account_ziswaf_id']);

							/* parameter: savings_from_id=14&savings_account_from_id=31014&savings_transfer_mutation_amount=1000&savings_account_from_opening_balance=141103.56&username=00000001&member_from_id=32887&user_id=32887&branch_from_id=2 */

							$datato = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $preferencecompany['savings_account_ziswaf_id'],
								'savings_id'								=> $savingsaccountto['savings_id'],
								'member_id'									=> $savingsaccountto['member_id'],
								'branch_id'									=> $savingsaccountto['branch_id'],
								'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
								'savings_account_opening_balance'			=> $savingsaccountto['savings_account_last_balance'],
								'savings_transfer_mutation_to_amount'		=> $this->input->post('savings_transfer_mutation_amount', true),
								'savings_account_last_balance'				=> $savingsaccountto['savings_account_last_balance'] + $this->input->post('savings_transfer_mutation_amount', true),
							);

							/* $datato = array (
								'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
								'savings_account_id'						=> $preferencecompany['savings_account_ziswaf_id'],
								'savings_id'								=> $savingsaccountto['savings_id'],
								'member_id'									=> $savingsaccountto['member_id'],
								'branch_id'									=> $savingsaccountto['branch_id'],
								'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
								'savings_account_opening_balance'			=> $savingsaccountto['savings_account_last_balance'],
								'savings_transfer_mutation_to_amount'		=> 1000,
								'savings_account_last_balance'				=> $savingsaccountto['savings_account_last_balance'] + 1000,
							); */

							$member_name = $this->AcctSavingsTransferMutation_model->getMemberName($datato['member_id']);

							if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){

								//----- Simpan data jurnal kredit
								$account_id = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

								$account_id_default_status = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'NOTA KREDIT '.$member_name,
									'journal_voucher_amount'		=> $data['savings_transfer_mutation_amount'],
									'journal_voucher_credit_amount'	=> $data['savings_transfer_mutation_amount'],
									'account_id_status'				=> 0,
								);

								$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
							}
						}

						

						$response['error_insertacctsavingsziswaf'] 		= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
					}else{
						$response['error_insertacctsavingsziswaf'] 		= FALSE;
						$response['error_msg_title'] 					= "Success";
						$response['error_msg'] 							= "Data Exist";
						$response['savings_transfer_mutation_id'] 		= $savings_transfer_mutation_id;
					}
				}
			}

			echo json_encode($response);

		}

		// END ADD ZISWAF

		// HISTORI TRANSAKSI ZISWAF

		public function getHistoryZISWAF(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'historyziswaf'					=> "",
			);

			$data = array(
				'member_id'			=> $this->input->post('member_id',true),
			);

			/* $data['member_id']		= 32887; */

			if($response["error"] == FALSE){

				$ziswaftransactionlist	= $this->Android_model->getAcctSavingsTransferMutation_ZISWAF($data['member_id']);
	
				
				if(!$ziswaftransactionlist){
					$response['error'] 				= TRUE;
					$response['error_msg_title'] 	= "No Data";
					$response['error_msg'] 			= "Error Query Data";
				}else{
					if (empty($ziswaftransactionlist)){
						$response['error'] 				= TRUE;
						$response['error_msg_title'] 	= "No Data";
						$response['error_msg'] 			= "Data Does Not Exist";
					} else {

						foreach ($ziswaftransactionlist as $key => $val){
							$acctsavingsaccountdetailfrom 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_from_id']);
							$acctsavingsaccountdetailto 	= $this->Android_model->getAcctSavingsAccount_DetailAccount($val['savings_account_to_id']);

							$historyziswaf[$key]['ziswaf_transaction_title']			= 'ZISWAF';
							$historyziswaf[$key]['ziswaf_transaction_date']				= date('d M Y H:i:s', strtotime($val['created_on']));
							$historyziswaf[$key]['ziswaf_transaction_description']		= 'Transfer dari Rekening '.$acctsavingsaccountdetailfrom['savings_account_no'].' Ke Rekening '.$acctsavingsaccountdetailto['savings_account_no'].' a/n '.$acctsavingsaccountdetailto['member_name'];
							$historyziswaf[$key]['ziswaf_transaction_amount']			= $val['savings_transfer_mutation_from_amount'];
						}
							
						$response['error'] 				= FALSE;
						$response['error_msg_title'] 	= "Success";
						$response['error_msg'] 			= "Data Exist";
						$response['historyziswaf'] 		= $historyziswaf;
					}
				}
			}

			echo json_encode($response);
		}

		// END HISTORI TRANSAKSI ZISWAF

		//------------------------------- E N D Z I S W A F -------------------------------------


		//---------------------------------------PENGURUS---------------------------------------//

		// MENGHITUNG JUMLAH PENAMBAHAN / PENGURANGAN MEMBER
		public function getTest(){

			$month 		= date('m');
			$year 		= date('Y');
			$memberthismonth	= $this->Android_model->getCoreMemberTotal_ThisMonth($month, $year);

			if(empty($memberthismonth)){
				$memberthismonth	= 0;
			}

			print_r($memberthismonth);
		}

		public function getDashboardPengurus(){
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');
			$date 		= date('Y-m-d');
			$day 		= date('d');
			$month 		= date('m');
			$year 		= date('Y');

			if($month == 01){
				$last_month = 12;
				$last_year 	= $year - 1;
			} else {
				$last_month = $month - 1;
				$last_year	= $year;
			}
			
			$last_date 		= date('t', strtotime($last_month));
			$last_period	= $last_year.'-'.$last_month.'-'.$last_date;

			$awal_tahun 	= '01-01-'.$year;

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'dashboardpengurus'				=> "",
				'dashboardanggota'				=> "",
			);

			if($response["error"] == FALSE){

				$membertotal		= $this->Android_model->getCoreMemberTotal();

				if(empty($membertotal)){
					$membertotal	= 0;
				}

				$memberlastmonth		= $this->Android_model->getCoreMemberTotal_LastMonth($last_month, $year);

				if(empty($memberlastmonth)){
					$memberlastmonth	= 0;
				}

				$memberthismonth	= $this->Android_model->getCoreMemberTotal_ThisMonth($month, $year);

				if(empty($memberthismonth)){
					$memberthismonth	= 0;
				}

				$specialsavings 	= $this->Android_model->getSpecialSavingsTotal($awal_tahun, $date);

				if(empty($specialsavings)){
					$specialsavings	= 0;
				}

				$mandatorysavings 	= $this->Android_model->getMandatorySavingsTotal($awal_tahun, $date);

				if(empty($mandatorysavings)){
					$mandatorysavings	= 0;
				}

				$acctsavings		= $this->Android_model->getAcctSavings();

				$no = 0;
				foreach($acctsavings as $key => $val){
					$savingsdeposittotal 	= $this->Android_model->getSavingsDepositTotal($val['savings_id'], $awal_tahun, $date);

					if(empty($savingsdeposittotal)){
						$savingsdeposittotal = 0;
					}

					if($savingsdeposittotal > 0){
						$dataacctsavingsdeposit[$no]['savings_name']				= $val['savings_name'];
						$dataacctsavingsdeposit[$no]['savings_deposit_total']		= $savingsdeposittotal;

						$no++;
					}	
				}

				$no = 0;
				foreach($acctsavings as $key => $val){
					$savingswithdrawaltotal = $this->Android_model->getSavingsWithdrawalTotal($val['savings_id'], $awal_tahun, $date);

					if(empty($savingswithdrawaltotal)){
						$savingswithdrawaltotal = 0;
					}

					if($savingswithdrawaltotal > 0){
						$dataacctsavingswithdrawal[$no]['savings_name']				= $val['savings_name'];
						$dataacctsavingswithdrawal[$no]['savings_withdrawal_total']	= $savingswithdrawaltotal;

						$no++;
					}	
				}

				$acctcredits 		= $this->Android_model->getAcctCredits();

				$nc = 0;
				foreach($acctcredits as $k => $v){
					$creditsaccounttotal 	= $this->Android_model->getCreditsAccountTotal($v['credits_id'], $awal_tahun, $date);

					if(empty($creditsaccounttotal)){
						$creditsaccounttotal = 0;
					}

					if($creditsaccounttotal > 0){
						$dataacctcreditsaccount[$nc]['credits_name']				= $v['credits_name'];
						$dataacctcreditsaccount[$nc]['credits_account_total']		= $creditsaccounttotal;

						$nc++;
					}	
				}

				$nc = 0;
				foreach($acctcredits as $k => $v){
					$creditspaymenttotal 	= $this->Android_model->getCreditsPaymentTotal($v['credits_id'], $awal_tahun, $date);

					if(empty($creditspaymenttotal)){
						$creditspaymenttotal = 0;
					}

					if($creditspaymenttotal > 0){
						$dataacctcreditspayment[$nc]['credits_name']				= $v['credits_name'];
						$dataacctcreditspayment[$nc]['credits_payment_total']		= $creditspaymenttotal;

						$nc++;
					}	
				}

				$acctdeposito 		= $this->Android_model->getAcctDeposito();

				$nd = 0;
				foreach($acctdeposito as $kd => $vd){
					$depositoaccounttotal 	= $this->Android_model->getDepositoAccountTotal($vd['deposito_id'], $awal_tahun, $date);
					
					if(empty($depositoaccounttotal)){
						$depositoaccounttotal = 0;
					}
					if($depositoaccounttotal > 0){
						$dataacctdeposito[$nd]['deposito_name']				= substr($vd['deposito_name'], 20);
						$dataacctdeposito[$nd]['deposito_account_total']	= $depositoaccounttotal;

						$nd++;
					}	
				}

				$no = 0;
				for($i=$month;$i>=1;$i--){
					$memberpermonth 	= $this->Android_model->getCoreMemberTotal_ThisMonth($i, $year);

					if(empty($memberpermonth)){
						$memberpermonth = 0;
					}
					$datamembers[$no]['month_name']				= date('F', mktime(0, 0, 0, $i, 10));
					$datamembers[$no]['member_total']			= $memberpermonth;

					$no++;
				}

				$no = 0;
				for($i=$day-0;$i>=1;$i--){
					$memberperday 	= $this->Android_model->getCoreMemberTotal_ThisDay($i, $month, $year);

					if(empty($memberperday)){
						$memberperday = 0;
					}
					$datamembersday[$no]['day']					= $i;
					$datamembersday[$no]['member_total']		= $memberperday;

					$no++;
				}

				$branch = $this->Android_model->getCoreBranch();
				
				$dashboardanggota[0]['member_total']				= $membertotal;
				$dashboardanggota[0]['member_this_month']			= $memberthismonth;
				$dashboardanggota[0]['member_last_month']			= $memberlastmonth;
				
				$dashboardpengurus[0]['member_total']				= $memberlastmonth;
				$dashboardpengurus[0]['member_this_month']			= $memberthismonth;
				$dashboardpengurus[0]['special_savings_total']		= $specialsavings;
				$dashboardpengurus[0]['mandatory_savings_total']	= $mandatorysavings;

				$response['error'] 									= FALSE;
				$response['error_msg_title'] 						= "Success";
				$response['error_msg'] 								= "Data Exist";
				$response['dashboardpengurus'] 						= $dashboardpengurus;
				$response['dashboardanggota'] 						= $dashboardanggota;
				$response['datamemberspermonth'] 					= $datamembers;
				$response['datamembersperday'] 						= $datamembersday;
				$response['branch'] 								= $branch;
				$response['dataacctsavingsdeposit'] 				= $dataacctsavingsdeposit;
				$response['dataacctsavingswithdrawal'] 				= $dataacctsavingswithdrawal;
				$response['dataacctcreditsaccount'] 				= $dataacctcreditsaccount;
				$response['dataacctcreditspayment'] 				= $dataacctcreditspayment;
				$response['dataacctdeposito'] 						= $dataacctdeposito;
			}

			echo json_encode($response);
		}

		// END MENGHITUNG JUMLAH PENAMBAHAN / PENGURANGAN MEMBER
		
		public function getAnggotaCabang(){
			$branch 	= $this->uri->segment(3);
			$base_url 	= base_url();
			$auth 		= $this->session->userdata('auth');
			$date 		= date('Y-m-d');
			$day 		= date('d');
			$month 		= date('m');
			$year 		= date('Y');

			if($month == 01){
				$last_month = 12;
				$last_year 	= $year - 1;
			} else {
				$last_month = $month - 1;
				$last_year	= $year;
			}
			
			$last_date 		= date('t', strtotime($last_month));
			$last_period	= $last_year.'-'.$last_month.'-'.$last_date;

			$awal_tahun 	= '01-01-'.$year;

			$response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
			);

			if($response["error"] == FALSE){

				$no = 0;
				for($i=$month;$i>=1;$i--){
					$memberpermonth 	= $this->Android_model->getCoreMemberTotal_ThisMonthBranch($i, $year, $branch);

					if(empty($memberpermonth)){
						$memberpermonth = 0;
					}
					$datamembers[$no]['month_name']				= date('F', mktime(0, 0, 0, $i, 10));
					$datamembers[$no]['member_total']			= $memberpermonth;

					$no++;
				}

				$no = 0;
				for($i=$day-0;$i>=1;$i--){
					$memberperday 	= $this->Android_model->getCoreMemberTotal_ThisDayBranch($i, $month, $year, $branch);

					if(empty($memberperday)){
						$memberperday = 0;
					}
					$datamembersday[$no]['day']					= $i;
					$datamembersday[$no]['member_total']		= $memberperday;

					$no++;
				}

				$response['branchmembermonth'] = $datamembers;
				$response['branchmemberday'] = $datamembersday;
			}
			echo json_encode($response);
		}

		//------------------------------------ END PENGURUS ------------------------------------//
	}
?>