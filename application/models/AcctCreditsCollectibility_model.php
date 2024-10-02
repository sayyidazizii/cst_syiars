<?php
	class AcctCreditsCollectibility_model extends CI_Model {
		var $table = "acct_credits_account";
		var $column_order = array(null, 'acct_credits_account.credits_account_serial','core_member.member_name'); //field yang ada di table user
		var $column_search = array('acct_credits_account.credits_account_serial','core_member.member_name'); //field yang diizin untuk pencarian 
		var $order = array('acct_credits_account.credits_account_serial' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getCoreBranch(){
            $this->db->select('core_branch.branch_id, core_branch.branch_name');
            $this->db->from('core_branch');
            $this->db->where('core_branch.branch_id <>', 1);
            $this->db->where('core_branch.data_state', 0);
            $result = $this->db->get()->result_array();
            return $result;
        }
		
		public function getCreditsAccount(){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_margin,  acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_payment_date');
			$this->db->from('acct_credits_account');
			$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
			$this->db->where('acct_credits_account.data_state ', 0);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}

		public function getPreferenceCollectibility(){
			$this->db->select('*');
			$this->db->from('preference_collectibility');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctCreditsPayment($credits_account_id){
			$this->db->select('acct_credits_payment.credits_payment_date, acct_credits_payment.credits_principal_last_balance');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.data_state ', 0);
			$this->db->where('acct_credits_payment.credits_account_id', $credits_account_id);
			$this->db->order_by('acct_credits_payment.credits_payment_date', 'DESC');	
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			// print_r($result);exit;
			return $result;
		}

		public function getAcctCredits(){
			$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits');
			$this->db->where('acct_credits.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSourceFund(){
			$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_name');
			$this->db->from('acct_source_fund');
			$this->db->where('acct_source_fund.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		private function _get_datatables_query($branch_id)
    {
    	$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_net_price, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_margin_amount, acct_credits_account.credits_account_last_balance_margin,  acct_credits_account.credits_account_last_balance_principal, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_payment_date');
        $this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.branch_id ', $branch_id);
		$this->db->where('acct_credits_account.credits_account_last_balance_principal > ', 0);
		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
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
 
    function get_datatables($branch_id)
    {
        $this->_get_datatables_query($branch_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered($branch_id)
    {
        $this->_get_datatables_query($branch_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all($branch_id)
    {
        $this->db->from('acct_credits_account');
		$this->db->where('acct_credits_account.branch_id ', $branch_id);
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.credits_account_last_balance_principal > ', 0);
        return $this->db->count_all_results();
    }
	}
?>