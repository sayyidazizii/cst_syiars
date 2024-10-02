<?php
	class TopupPPOB_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			$this->dbcipta = $this->load->database('cipta', true);
		} 


		public function insertPPOBTopUP($data){
			$query = $this->db->insert('ppob_topup', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getPPOBTopUP_Detail($ppob_topup_id, $ppob_topup_status){
			$this->dbcipta->select('ppob_topup_id, ppob_topup_no, ppob_topup_date, created_on, ppob_topup_amount, ppob_topup_status');
			$this->dbcipta->from('ppob_topup');
			$this->dbcipta->where('ppob_topup_id', $ppob_topup_id);
			$this->dbcipta->where('ppob_topup_status', $ppob_topup_status);
			return $this->dbcipta->get()->row_array();
		}
		
		public function insertPPOBBalance($data){
			$query = $this->db->insert('ppob_balance', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function updatePPOBBalance($id, $data){
			$this->db->where('ppob_agen_id', $id);
			$query = $this->db->update('ppob_balance', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getPPOBBalanceAgen($ppob_agen_id){
			$this->db->select('ppob_balance_amount');
			$this->db->from('ppob_balance');
			$this->db->where('ppob_agen_id', $ppob_agen_id);
			$result = $this->db->get()->row_array();
			return $result['ppob_balance_amount'];
		}

		public function getPPOBTransactionLog($ppob_company_id, $ppob_agen_id){
			$this->dbcipta->select('ppob_transaction_id, ppob_unique_code, ppob_transaction_code');
			$this->dbcipta->from('ppob_transaction_log');
			$this->dbcipta->where('ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->group_by('ppob_transaction_id');
			$this->dbcipta->group_by('ppob_unique_code');
			$this->dbcipta->group_by('ppob_transaction_code');
			$this->dbcipta->order_by('ppob_transaction_id', 'DESC');
			$result = $this->dbcipta->get()->result_array();
			return $result;
		}

		public function getPPOBProduct($ppob_product_category_id){
			$this->dbcipta->select('ppob_product_id, ppob_product_category_id, ppob_product_code, ppob_product_name, ppob_product_price');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_category_id', $ppob_product_category_id);
			$this->dbcipta->where('data_state', 0);
			$this->dbcipta->order_by('ppob_product_id','ASC');
			return $this->dbcipta->get()->result_array();
		}

		public function getPPOBProduct_Detail($ppob_product_code){
			$this->dbcipta->select('ppob_product_id, ppob_product_category_id, ppob_product_code, ppob_product_name, ppob_product_title, ppob_product_cost, ppob_product_price');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_code', $ppob_product_code);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result;
		}

		public function getPPOBProductCategoryID($ppob_product_id){
			$this->dbcipta->select('ppob_product_category_id');
			$this->dbcipta->from('ppob_product');
			$this->dbcipta->where('ppob_product_id', $ppob_product_id);
			$this->dbcipta->where('data_state', 0);
			$result = $this->dbcipta->get()->row_array();
			return $result['ppob_product_category_id'];
		}

		public function insertPPOBTransaction($data){
			$query = $this->dbcipta->insert('ppob_transaction', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getPPOBTransaction_Detail($ppob_transaction_id, $ppob_transaction_status){
			$this->dbcipta->select('ppob_transaction.ppob_transaction_id, ppob_transaction.ppob_transaction_no, ppob_transaction.ppob_transaction_date, ppob_transaction.created_on, ppob_transaction.ppob_transaction_amount, ppob_transaction.ppob_transaction_status, ppob_product.ppob_product_name, ppob_product_category.ppob_product_category_name');
			$this->dbcipta->from('ppob_transaction');
			$this->dbcipta->join('ppob_product', 'ppob_transaction.ppob_product_id = ppob_product.ppob_product_id');
			$this->dbcipta->join('ppob_product_category', 'ppob_transaction.ppob_product_category_id = ppob_product_category.ppob_product_category_id');
			$this->dbcipta->where('ppob_transaction.ppob_transaction_id', $ppob_transaction_id);
			$this->dbcipta->where('ppob_transaction.ppob_transaction_status', $ppob_transaction_status);
			return $this->dbcipta->get()->row_array();
		}

		public function getPPOBTransaction_Product($ppob_company_id, $ppob_agen_id, $ppob_product_id){
			$this->dbcipta->select('ppob_transaction.ppob_transaction_id, ppob_transaction.ppob_transaction_no, ppob_transaction.ppob_transaction_date, ppob_transaction.created_on, ppob_transaction.ppob_transaction_amount, ppob_transaction.ppob_transaction_status, ppob_product.ppob_product_name, ppob_product_category.ppob_product_category_name, ppob_transaction.ppob_transaction_remark');
			$this->dbcipta->from('ppob_transaction');
			$this->dbcipta->join('ppob_product', 'ppob_transaction.ppob_product_id = ppob_product.ppob_product_id');
			$this->dbcipta->join('ppob_product_category', 'ppob_transaction.ppob_product_category_id = ppob_product_category.ppob_product_category_id');
			$this->dbcipta->where('ppob_transaction.ppob_company_id', $ppob_company_id);
			$this->dbcipta->where('ppob_transaction.ppob_agen_id', $ppob_agen_id);
			$this->dbcipta->where('ppob_transaction.ppob_product_id', $ppob_product_id);
			$this->dbcipta->where('ppob_transaction.ppob_transaction_status', 1);
			$this->dbcipta->order_by('ppob_transaction.created_on','DESC');
			$this->dbcipta->limit(10);
			return $this->dbcipta->get()->result_array();
			// print_r($this->dbcipta->last_query());exit;
		}

		

		//END PPOB//
	}
?>