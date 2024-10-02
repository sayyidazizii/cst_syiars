<?php
	Class ValidationProcess_model extends CI_Model{
		var $table = "system_user";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function getSystemUser($data){
			$this->db->select('user_id, username, password, user_group_id, log_stat, user_level, database, branch_id, branch_status, user_name');
			$this->db->from('system_user');
			$this->db->where('username', $data['username']);
			$this->db->where('password', $data['password']);
			$this->db->where('system_user.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		function getLogin($data){
			$hasil = $this->db->query("UPDATE system_user SET log_stat='on' WHERE username='$data[username]' AND password='$data[password]'");
			if($hasil){
				return true;
			}else{
				return false;
			}
		}
		
		function getLogout($data){
			$hasil = $this->db->query("UPDATE system_user SET log_stat='off' WHERE username='$data[username]' AND password='$data[password]'");
			if($hasil){
				return true;
			}else{
				return false;
			}
		}
		
		function getName($id){
		
		}

		public function getCoreBranch_Detail($branch_id){
			$this->db->select('core_branch.branch_id, core_branch.branch_code, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$this->db->where('core_branch.data_state', 0);
			return $this->db->get()->row_array();
		}

		public function getPreferenceCompany(){
			$this->db->select('preference_company.company_name, preference_company.company_slogan, preference_company.company_footer');
			$this->db->from('preference_company');
			$result = $this->db->get()->row_array();
			return $result;
		}
	}
?>