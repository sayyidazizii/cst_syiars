<?php
	class AcctCreditsPPOB_model extends CI_Model {
		
		var $table = "acct_credits_ppob";
		var $column_order = array(null, 'credits_ppob_no','credits_ppob_date'); //field yang ada di table user
		var $column_search = array('credits_ppob_no','credits_ppob_date'); //field yang diizin untuk pencarian 
		var $order = array('credits_ppob_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 
		
		public function getData($start_date, $end_date){
			$this->db->from($this->table);
			$this->db->where('credits_ppob_date >=', $start_date);
			$this->db->where('credits_ppob_date <=', $end_date);
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		function get_datatables($start_date, $end_date)
	    {
	        $this->_get_datatables_query($start_date, $end_date);
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered($start_date, $end_date)
	    {
	        $this->_get_datatables_query($start_date, $end_date);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all($start_date, $end_date)
	    {
	        $this->db->from($this->table);
	        $this->db->where('credits_ppob_date >=', $start_date);
			$this->db->where('credits_ppob_date <=', $end_date);
			$this->db->where('data_state', 0);
	        return $this->db->count_all_results();
	    }

	    private function _get_datatables_query($start_date, $end_date)
	    {
	    	$this->db->from($this->table);
			$this->db->where('credits_ppob_date >=', $start_date);
			$this->db->where('credits_ppob_date <=', $end_date);
			$this->db->where('data_state', 0);
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
		
		public function insertData($data){
			return $query = $this->db->insert($this->table,$data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherID($created_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.created_id', $created_id);
			$this->db->order_by('acct_journal_voucher.journal_voucher_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_id'];
		}
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}
		
	}
?>