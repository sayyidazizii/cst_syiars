<?php
class CronSystemUser extends CI_Controller{
	public function __construct(){
		parent::__construct();
		// error_reporting(0);
		$this->load->model('CronSystemUser_model');
		$this->load->model('MainPage_model');
		$this->load->helper('sistem');
		$this->load->library('configuration');
		$this->load->library('fungsi');
		$this->load->helper('url');
		$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
	}
	
	public function index(){

		echo "access is denied index";
	}

	public function getSystemUser($password){
		if($password!="m4d4n1j4t1m"){
			echo "access is denied";
		} else {
			$today = date("Y-m-d H:i:s");
			$systemuser = $this->CronSystemUser_model->getSystemUser($today);

			foreach ($systemuser as $key => $val)	{
				$data_delete  = array(
					'user_id'		=> $val['user_id'],
					'member_id'		=> $val['member_id']
				);

				$this->CronSystemUser_model->deleteSystemUser($data_delete);

				$data_update = array(
					'member_id'		=> $val['member_id'],
					'ppob_status'	=> 0
				);

				$this->CronSystemUser_model->updateCoreMember($data_update);
			}
		} 
	}
}