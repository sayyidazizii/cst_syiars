<?php
	class RelationCustomerSatisfactionReport_model extends CI_Model {
		var $table = "invt_warehouse_in";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getRelationCustomerSatisfactionReport($branch_id, $query_count){
			$query = "SELECT core_branch.branch_name AS 'Nama Layanan', ".$query_count." 
				FROM relation_customer_satisfaction, core_branch
				WHERE relation_customer_satisfaction.branch_id = core_branch.branch_id";

			if ($branch_id != ''){
				$query .= "WHERE relation_customer_satisfaction.branch_id = ".$branch_id." ";
			}

			$query .= " GROUP BY relation_customer_satisfaction.branch_id";

			$result = $this->db->query($query)->result_array();

			/* print_r($this->db->last_query());
			exit; */

			if(!empty($result)){
				return $result;
			}else{
				return false;
			}
		}

		public function getPreferenceCompany(){
			$this->db->select('preference_company.company_name, preference_company.company_slogan, preference_company.company_footer');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getBranchName($branch_id){
			$this->db->select('core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}

		
		
	}
?>