<?php ob_start(); ?>
<?php
	Class ManagementDashboard extends CI_Controller{
		public function __construct(){
			parent::__construct();

			
			$this->load->model('MainPage_model');
			$this->load->model('ManagementDashboard_model');
			$this->load->helper('sistem');
			$this->load->library('configuration');
			$this->load->library('fungsi');
		}

		public function index(){
			$auth 						= $this->session->userdata('auth');
			$unique 					= $this->session->userdata('unique');

			$memberperiod				= $this->ManagementDashboard_model->getMemberPeriod();			

			foreach ($memberperiod  as $keyPeriod => $valPeriod){
				$start_date 			= $valPeriod['year_period'].'-'.$valPeriod['month_period'].'-01';

				$end_date 				= date("Y-m-t", strtotime($start_date));

				$corebranch 			= $this->ManagementDashboard_model->getCoreBranch();

				$query_count 			= "SELECT 'period_".$valPeriod['member_register_period']."' AS Period, ";

				foreach($corebranch as $keyBranch => $valBranch){
					$query_count .= "( SELECT COUNT(member_id) FROM core_member WHERE '".$start_date."' <= member_register_date AND member_register_date <= '".$end_date."' AND branch_id = ".$valBranch['branch_id']." ) AS ".$valBranch['branch_codename'].", ";
				}

				$query_count			= substr(trim($query_count), 0, strlen(trim($query_count)) -1);

				$count_member 			= $this->ManagementDashboard_model->getCountMember($query_count);

				foreach($count_member as $keyMember => $valMember){
					$data_countmember[$valMember['Period']] = array (
						'period'	=> $valMember['Period']
					);

					foreach($corebranch as $key => $val){
						$data_countmember[$valMember['Period']][$val['branch_codename']] = $valMember[$val['branch_codename']];
					}
				
				}
			}


			$totalmember_branch			= $this->ManagementDashboard_model->getTotalMember_Branch();

			$total_member 				= $this->ManagementDashboard_model->getTotalMember();

			$totalmandatorysavings		= $this->ManagementDashboard_model->getTotalMandatorySavings();
			$totalprincipalsavings		= $this->ManagementDashboard_model->getTotalPrincipalSavings();
			$totalspecialsavings		= $this->ManagementDashboard_model->getTotalSpecialSavings();

			$percentage_member_savings 	= $totalprincipalsavings['total_member_principal'] / $total_member * 100;

			$data_membersavings[0] 		= array(
				"savings_name"					=> "Simpanan Pokok",
				'total_member'					=> $total_member, 
				'total_member_savings'			=> $totalprincipalsavings['total_member_principal'],
				'total_amount_savings'			=> ($totalprincipalsavings['total_amount_principal'] / 100000),
				'percentage_member_savings'		=> number_format($percentage_member_savings, 2),
			);


			$percentage_member_savings 	= $totalmandatorysavings['total_member_mandatory'] / $total_member * 100;

			$data_membersavings[1] 		= array(
				"savings_name"					=> "Simpanan Wajib",
				'total_member'					=> $total_member, 
				'total_member_savings'			=> $totalmandatorysavings['total_member_mandatory'],
				'total_amount_savings'			=> ($totalmandatorysavings['total_amount_mandatory'] / 100000),
				'percentage_member_savings'		=> number_format($percentage_member_savings, 2),
			);

			$percentage_member_savings 	= $totalspecialsavings['total_member_special'] / $total_member * 100;

			$data_membersavings[2] 		= array(
				"savings_name"					=> "Simpanan Khusus",
				'total_member'					=> $total_member, 
				'total_member_savings'			=> $totalspecialsavings['total_member_special'],
				'total_amount_savings'			=> ($totalspecialsavings['total_amount_special'] / 100000),
				'percentage_member_savings'		=> number_format($percentage_member_savings, 2),
			);


			/* Savings Account */
			$savingsperiod				= $this->ManagementDashboard_model->getSavingsPeriod();		
			
			/* print_r("savingsperiod ");
			print_r($savingsperiod);
			print_r("<BR> ");
			print_r("<BR> "); */

			foreach ($savingsperiod  as $keyPeriod => $valPeriod){
				$start_date 			= $valPeriod['year_period'].'-'.$valPeriod['month_period'].'-01';

				$end_date 				= date("Y-m-t", strtotime($start_date));

				$acctsavings 			= $this->ManagementDashboard_model->getAcctSavings();

				$query_count 			= "SELECT 'period_".$valPeriod['savings_account_period']."' AS Period, ";

				foreach($acctsavings as $keySavings => $valSavings){
					$savings_name 		= str_replace(" ", "", $valSavings['savings_name']);

					$query_count .= "( SELECT COUNT(savings_account_id) FROM acct_savings_account WHERE '".$start_date."' <= savings_account_date AND savings_account_date <= '".$end_date."' AND savings_id = ".$valSavings['savings_id']." ) AS ".$savings_name.", ";
				}

				$query_count			= substr(trim($query_count), 0, strlen(trim($query_count)) -1);

				/* print_r("query_count ");
				print_r($query_count);
				print_r("<BR> ");
				print_r("<BR> "); */

				$count_account 			= $this->ManagementDashboard_model->getCountSavingsAccount($query_count);

				/* print_r("count_account ");
				print_r($count_account);
				print_r("<BR> ");
				print_r("<BR> "); */

				foreach($count_account as $keyAccount => $valAccount){
					$data_countaccount[$valAccount['Period']] = array (
						'period'	=> $valAccount['Period']
					);

					foreach($acctsavings as $key => $val){
						$savings_name 		= str_replace(" ", "", $val['savings_name']);
						
						$data_countaccount[$valAccount['Period']][$savings_name] = $valAccount[$savings_name];
					}
					/* print_r("data_countaccount ");
					print_r($data_countaccount);
					print_r("<BR> ");
					print_r("<BR> "); */
				}
			}

			/* print_r("data_countaccount ");
			print_r($data_countaccount);
			exit; */
			
			$data['main_view']['data_countmember']				= $data_countmember;		
			$data['main_view']['totalmember_branch']			= $totalmember_branch;
			$data['main_view']['corebranch']					= $corebranch;		
			$data['main_view']['data_membersavings']			= $data_membersavings;		
			$data['main_view']['data_countaccount']				= $data_countaccount;		
			$data['main_view']['acctsavings']					= $acctsavings;
			$data['main_view']['content']						= 'ManagementDashboard/ManagementDashboard_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('ManagementDashboardState-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('ManagementDashboardState-'.$unique['unique'],$sessions);
		}
	}
?>