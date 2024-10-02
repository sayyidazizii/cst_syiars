<?php
	class AcctDepositoProfitSharingReport_model extends CI_Model {
		var $table = "acct_deposito_profit_sharing_new";

		var $column_order = array(null, 'acct_deposito_account.deposito_account_no','acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address'); //field yang ada di table user
		var $column_search = array('core_member.member_name','acct_savings_account.savings_account_no','core_member.member_address', 'acct_deposito_account.deposito_account_no'); //field yang diizin untuk pencarian 
		var $order = array('acct_deposito_account.deposito_account_no' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctDepositoProfitSharing($start_date, $end_date){
			$this->db->select('acct_deposito_profit_sharing_new.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_profit_sharing_new.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_profit_sharing_new.member_id, core_member.member_name, acct_deposito_profit_sharing_new.deposito_profit_sharing_amount, acct_deposito_profit_sharing_new.deposito_account_last_balance, acct_deposito_profit_sharing_new.deposito_profit_sharing_due_date, acct_deposito_profit_sharing_new.deposito_profit_sharing_date');
			$this->db->from('acct_deposito_profit_sharing_new');
			$this->db->join('core_member', 'acct_deposito_profit_sharing_new.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing_new.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing_new.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing_new.deposito_profit_sharing_due_date >=', $start_date);
			$this->db->where('acct_deposito_profit_sharing_new.deposito_profit_sharing_due_date <=', $end_date);
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
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
	    	$this->db->select('acct_deposito_profit_sharing_new.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_profit_sharing_new.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_profit_sharing_new.member_id, core_member.member_name, acct_deposito_profit_sharing_new.deposito_profit_sharing_amount, acct_deposito_profit_sharing_new.deposito_account_last_balance, acct_deposito_profit_sharing_new.deposito_profit_sharing_due_date, acct_deposito_profit_sharing_new.deposito_profit_sharing_date');
			$this->db->from('acct_deposito_profit_sharing_new');
			$this->db->join('core_member', 'acct_deposito_profit_sharing_new.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing_new.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing_new.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing_new.deposito_profit_sharing_period', $period);
			if($branch_id != 0){
				$this->db->where('acct_deposito_profit_sharing_new.branch_id', $branch_id);
			}
			
	        return $this->db->count_all_results();
	    }

		private function _get_datatables_query($period, $branch_id)
	    {
	       	$this->db->select('acct_deposito_profit_sharing_new.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_profit_sharing_new.savings_account_id, acct_savings_account.savings_account_no, acct_deposito_profit_sharing_new.member_id, core_member.member_name, core_member.member_address, acct_deposito_profit_sharing_new.deposito_profit_sharing_amount, acct_deposito_profit_sharing_new.deposito_daily_average_balance, acct_deposito_profit_sharing_new.deposito_profit_sharing_due_date, acct_deposito_profit_sharing_new.deposito_profit_sharing_date');
			$this->db->from('acct_deposito_profit_sharing_new');
			$this->db->join('core_member', 'acct_deposito_profit_sharing_new.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_deposito_profit_sharing_new.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing_new.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_account.deposito_account_status', 0);
			$this->db->where('acct_deposito_profit_sharing_new.deposito_profit_sharing_period', $period);
			if($branch_id != 0){
				$this->db->where('acct_deposito_profit_sharing_new.branch_id', $branch_id);
			}
			$this->db->order_by('acct_deposito_account.deposito_account_no', 'ASC');
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