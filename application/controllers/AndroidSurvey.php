<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	Class AndroidSurvey extends CI_Controller {
		public function __construct(){
			parent::__construct();
			$this->load->model('AndroidSurvey_model');
			$this->load->library('configuration');
			$this->load->helper('sistem');
			$this->load->database("default");
			$this->load->helper('url');
		}
		
		public function processAddRelationCustomerSatisfaction(){
			$data = array (
				'branch_id'						=> $this->input->post('branch_id',true),
				'customer_satisfaction_status'	=> $this->input->post('customer_satisfaction_status',true),
				'customer_satisfaction_date'	=> date("Y-m-d"),
			);

			$response = array(
				'error'					=> FALSE,
				'error_msg_title'		=> "",
				'error_msg'				=> "",
			);
			
			if($response["error"] == FALSE){
				if ($this->AndroidSurvey_model->insertRelationCustomerSatisfaction($data)){
					$response['error']						 	= FALSE;
					$response['error_msg_title'] 				= "Success";
					$response['error_msg'] 						= "Data Saved";
				} else {
					$response['error']		 					= TRUE;
					$response['error_msg_title'] 				= "Fail";
					$response['error_msg'] 						= "Data Failed";
				}
			}
			
			echo json_encode($response);
		}	

		public function getTotalVisitor(){
			$response = array(
				'error'					=> FALSE,
				'error_msg'				=> "",
				'error_msg_title'		=> "",
				'totalvisitor'			=> "",
			);

			$data = array(
				'branch_id' 	=> $this->input->post('branch_id',true),
				'today'		 	=> date("Y-m-d"),
			);

			/* $data = array(
				'branch_id' 	=> 1,
				'today'		 	=> date("Y-m-d"),
			); */
			
			if (empty($data)){
				$response['error'] 				= TRUE;
				$response['error_msg_title'] 	= "No Data";
				$response['error_msg'] 			= "Data Login is Empty";
			} else {
				if($response["error"] == FALSE){
					$total_visitor 	= $this->AndroidSurvey_model->getTotalVisitor($data);

					/* print_r("total_visitor ");
					print_r($total_visitor); */

					$totalvisitor[0]['total_visitor']		= $total_visitor;
					

					$response['error'] 				= FALSE;
					$response['error_msg_title'] 	= "Success";
					$response['error_msg'] 			= "Data Exist";
					$response['totalvisitor'] 		= $totalvisitor;
				}
			}

			echo json_encode($response);

		}
	}
?>