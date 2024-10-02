<?php
	class AndroidSurvey_model extends CI_Model {
		var $table = "acct_account";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function insertRelationCustomerSatisfaction($data){
			if($this->db->insert('relation_customer_satisfaction', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getTotalVisitor($data){
			$this->db->select('relation_customer_satisfaction.customer_satisfaction_id');
			$this->db->from('relation_customer_satisfaction');
			$this->db->where('relation_customer_satisfaction.branch_id', $data['branch_id']);
			$this->db->where('relation_customer_satisfaction.customer_satisfaction_date', $data['today']);
			$result = $this->db->get()->num_rows();
			return $result;
		}
	}
?>