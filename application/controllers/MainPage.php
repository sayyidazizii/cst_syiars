<?php
	Class MainPage extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
		}
		
		public function index(){
			$data['main_view']['content']	= 'Home';
			$this->load->view('MainPage_view',$data);
		}
	}
?>