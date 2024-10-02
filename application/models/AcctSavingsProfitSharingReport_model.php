<?php
	class AcctSavingsProfitSharingReport_model extends CI_Model {
		var $table = "acct_savings_profit_sharing";

		var $column_order = array(null, 'acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address'); //field yang ada di table user
		var $column_search = array('core_member.member_name','acct_savings_account.savings_account_no','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_savings_account.savings_account_no' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavingsProfitSharing($period){
			$this->db->select('acct_savings_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing.member_id, core_member.member_name, acct_savings_profit_sharing.savings_profit_sharing_amount, acct_savings_profit_sharing.savings_account_last_balance');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->join('core_member', 'acct_savings_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('savings_profit_sharing_period', $period);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getMutationCode($mutation_id){
			$this->db->select('mutation_code');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_code'];
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}


		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getSavingsCode($savings_id){
			$this->db->select('savings_code');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_code'];
		}

		public function getSavingsNisbah($savings_id){
			$this->db->select('savings_nisbah');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_nisbah'];
		}

		public function getCityName($city_id){
			$this->db->select('city_name');
			$this->db->from('core_city');
			$this->db->where('city_id', $city_id);
			$result = $this->db->get()->row_array();
			return $result['city_name'];
		}

		public function getKecamatanName($kecamatan_id){
			$this->db->select('kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('kecamatan_id', $kecamatan_id);
			$result = $this->db->get()->row_array();
			return $result['kecamatan_name'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		function get_datatables($period, $branch_id)
	    {
	        $this->_get_datatables_query($period, $branch_id);
			// $this->db->join('core_member','acct_credits_account.member_id=core_member.member_id');
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
 
	    function count_filtered($period, $branch_id)
	    {
	        $this->_get_datatables_query($period, $branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
 
	    public function count_all($period, $branch_id)
	    {	
	    	$this->db->select('acct_savings_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing.member_id, core_member.member_name, acct_savings_profit_sharing.savings_profit_sharing_amount, acct_savings_profit_sharing.savings_account_last_balance');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->join('core_member', 'acct_savings_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('savings_profit_sharing_period', $period);
			if($branch_id != 0){
				$this->db->where('acct_savings_profit_sharing.branch_id', $branch_id);
			}
			
	        return $this->db->count_all_results();
	    }

		private function _get_datatables_query($period, $branch_id)
	    {
	       	$this->db->select('acct_savings_profit_sharing.savings_account_id, acct_savings_account.savings_account_no, acct_savings_profit_sharing.member_id, core_member.member_name, core_member.member_address, acct_savings_profit_sharing.savings_profit_sharing_amount, acct_savings_profit_sharing.savings_account_last_balance, acct_savings_profit_sharing.savings_daily_average_balance');
			$this->db->from('acct_savings_profit_sharing');
			$this->db->join('core_member', 'acct_savings_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_profit_sharing.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_profit_sharing.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings.savings_nisbah > 0');
			$this->db->where('acct_savings_profit_sharing.savings_profit_sharing_period', $period);
			if($branch_id != 0){
				$this->db->where('acct_savings_profit_sharing.branch_id', $branch_id);
			}
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			// $this->db->limit(50);
	 
	        $i = 0;
	     
	        foreach ($this->column_search as $item) // looping awal
	        {
	            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
	            {
	                 
	                if($i===0) // looping awal
	                {
	                    $this->db->group_start(); 
	                    $this->db->like($item, $_POST['search']['value']);
	                }
	                else
	                {
	                    $this->db->or_like($item, $_POST['search']['value']);
	                }
	 
	                if(count($this->column_search) - 1 == $i) 
	                    $this->db->group_end(); 
	            }
	            $i++;
	        }
	         
	        if(isset($_POST['order'])) 
	        {
	            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	        } 
	        else if(isset($this->order))
	        {
	            $order = $this->order;
	            $this->db->order_by(key($order), $order[key($order)]);
	        }
	    }
	}
?>