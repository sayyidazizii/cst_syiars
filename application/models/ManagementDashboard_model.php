<?php
	class ManagementDashboard_model extends CI_Model {
		var $table = "sales_invoice";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function getMemberPeriod(){
			$query = $this->db->query("SELECT CONCAT(YEAR(member_register_date), RIGHT(CONCAT('0', MONTH(member_register_date)), 2)) AS member_register_period, 
				YEAR(member_register_date) AS year_period, RIGHT(CONCAT('0', MONTH(member_register_date)), 2) AS month_period
			FROM core_member
			WHERE DATE_ADD(CURDATE(), INTERVAL -1 YEAR) <= member_register_date
			AND member_register_date <= CURDATE()
			AND data_state = 0
			GROUP BY member_register_period")->result_array();
			return $query;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_codename');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_codename <> "" ');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCountMember($query_count){
			$query = $this->db->query($query_count)->result_array();
			return $query;
		}

		public function getTotalMember_Branch(){
			$query = $this->db->query("SELECT branch_id, branch_codename,
				(SELECT COUNT(member_id) FROM core_member member WHERE branch_id = core_branch.branch_id AND data_state = 0)  AS total_member,
				(SELECT COUNT(member_id) FROM core_member member WHERE branch_id = core_branch.branch_id AND (member_mandatory_savings_last_balance + member_principal_savings_last_balance) <> 0 AND data_state = 0) AS total_active_member,
				(SELECT COUNT(member_id) FROM core_member member WHERE branch_id = core_branch.branch_id AND (member_mandatory_savings_last_balance + member_principal_savings_last_balance) = 0 AND data_state = 0) AS total_passive_member
				FROM core_branch
				WHERE data_state = 0
				AND branch_codename <> '' ")->result_array();
			return $query;
		}

		public function getTotalMember(){
			$this->db->select('COUNT(core_member.member_id) AS total_member');
			$this->db->from('core_member');
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['total_member'];
		}

		public function getTotalMandatorySavings(){
			$this->db->select('COUNT(core_member.member_id) AS total_member_mandatory, SUM(core_member.member_mandatory_savings_last_balance) AS total_amount_mandatory');
			$this->db->from('core_member');
			$this->db->where('core_member.member_mandatory_savings_last_balance > ', 0);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getTotalPrincipalSavings(){
			$this->db->select('COUNT(core_member.member_id) AS total_member_principal, SUM(core_member.member_principal_savings_last_balance) AS total_amount_principal');
			$this->db->from('core_member');
			$this->db->where('core_member.member_principal_savings_last_balance > ', 0);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getTotalSpecialSavings(){
			$this->db->select('COUNT(core_member.member_id) AS total_member_special, SUM(core_member.member_special_savings_last_balance) AS total_amount_special');
			$this->db->from('core_member');
			$this->db->where('core_member.member_special_savings_last_balance > ', 0);
			$this->db->where('core_member.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getSavingsPeriod(){
			$query = $this->db->query("SELECT CONCAT(YEAR(savings_account_date), RIGHT(CONCAT('0', MONTH(savings_account_date)), 2)) AS savings_account_period, 
				YEAR(savings_account_date) AS year_period, RIGHT(CONCAT('0', MONTH(savings_account_date)), 2) AS month_period
			FROM acct_savings_account
			WHERE DATE_ADD(CURDATE(), INTERVAL -1 YEAR) <= savings_account_date
			AND savings_account_date <= CURDATE()
			AND data_state = 0
			GROUP BY savings_account_period")->result_array();
			return $query;
		}

		public function getAcctSavings(){
			$this->db->select('acct_savings.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->where('acct_savings.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCountSavingsAccount($query_count){
			$query = $this->db->query($query_count)->result_array();
			return $query;
		}








		public function getTotalAmount($start_date, $end_date, $section_id){
			$this->db->select('SUM(sales_invoice.total_amount) AS total_amount');
			$this->db->from('sales_invoice');
			$this->db->where('sales_invoice.sales_invoice_date >=', $start_date);
			$this->db->where('sales_invoice.sales_invoice_date <=', $end_date);
			$this->db->where('sales_invoice.section_id', $section_id);
			$this->db->where('sales_invoice.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['total_amount'];
		}

		public function getSalesInvoiceTotal($year){
			$query = $this->db->query("SELECT CONCAT(YEAR(sales_invoice.sales_invoice_date), RIGHT(CONCAT('0', MONTH(sales_invoice.sales_invoice_date)), 2)) AS sales_invoice_period,
			(SELECT SUM(total_amount) AS total_amount FROM sales_invoice WHERE section_id = 1 AND CONCAT(YEAR(sales_invoice.sales_invoice_date), RIGHT(CONCAT('0', MONTH(sales_invoice.sales_invoice_date)), 2)) = sales_invoice_period) AS total_amount_lem,
			(SELECT SUM(total_amount) AS total_amount FROM sales_invoice WHERE section_id = 2 AND CONCAT(YEAR(sales_invoice.sales_invoice_date), RIGHT(CONCAT('0', MONTH(sales_invoice.sales_invoice_date)), 2)) = sales_invoice_period) AS total_amount_plastik
			FROM sales_invoice
			WHERE YEAR(sales_invoice_date) = '$year'
			GROUP BY sales_invoice_period
			ORDER BY sales_invoice_period")->result_array();
			return $query;
		}

		public function getSalesInvoiceItemTotal($year){
			$query = $this->db->query("SELECT invt_item.section_id, core_section.section_name, SUM(quantity) AS total_quantity, sales_invoice_item.item_unit_id
			FROM sales_invoice, sales_invoice_item, invt_item, core_section
			WHERE sales_invoice.sales_invoice_id = sales_invoice_item.sales_invoice_id
			AND sales_invoice_item.item_id = invt_item.item_id
			AND invt_item.section_id = core_section.section_id
			AND YEAR(sales_invoice.sales_invoice_date) = '$year'
			AND invt_item.item_category_id = 1
			AND sales_invoice.data_state = 0
			GROUP BY invt_item.section_id")->result_array();
			return $query;
		}
		
		public function getSalesInvoiceCustomerTotal($year){
			$query = $this->db->query("SELECT core_section.section_name, COUNT(sales_invoice.customer_id) AS total_customer
			FROM sales_invoice, core_section
			WHERE sales_invoice.section_id = core_section.section_id
			AND YEAR(sales_invoice.sales_invoice_date) = '$year'
			AND sales_invoice.data_state = 0
			GROUP BY sales_invoice.section_id")->result_array();
			return $query;
		}

		public function getSalesSalesmanTargetInvoice($year){
			$query = $this->db->query("SELECT sales_salesman.salesman_name, 
			(SELECT SUM(total_amount) FROM sales_invoice 
				WHERE salesman_id = sales_salesman.salesman_id
				AND YEAR(sales_invoice_date) = '$year') AS total_invoice,
			(SELECT salesman_target_total_amount FROM sales_salesman_target 
				WHERE salesman_id = sales_salesman.salesman_id 
				AND salesman_target_period = '$year') AS total_target
			FROM sales_salesman
			WHERE data_state = 0")->result_array();
			return $query;
		}

		

		public function getTotalTargetPeriod($salesman_target_item_period){
			$this->db->select('SUM(sales_salesman_target_item.salesman_target_item_amount) AS total_target_period');
			$this->db->from('sales_salesman_target_item');
			$this->db->where('sales_salesman_target_item.salesman_target_item_period', $salesman_target_item_period);
			$result = $this->db->get()->row_array();
			return $result['total_target_period'];
		}

		public function getSalesSalesman(){
			$this->db->select('sales_salesman.salesman_id, sales_salesman.salesman_name');
			$this->db->from('sales_salesman');
			$this->db->where('sales_salesman.salesman_status', 1);
			$this->db->where('sales_salesman.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSalesInvoiceYearSalesman($today, $salesman_id){
			$query = $this->db->query("SELECT CONCAT(YEAR(sales_invoice_date), RIGHT(CONCAT('0', MONTH(sales_invoice_date)), 2)) AS sales_invoice_period,
			SUM(total_amount) AS total_amount_period
			FROM sales_invoice
			WHERE DATE_ADD('$today', INTERVAL -1 YEAR) <= sales_invoice_date
			AND sales_invoice_date <= '$today' 
			AND salesman_id = '$salesman_id'
			AND data_state = 0
			GROUP BY sales_invoice_period")->result_array();
			return $query;
		}

		public function getTotalTargetPeriodSalesman($salesman_target_item_period, $salesman_id){
			$this->db->select('SUM(sales_salesman_target_item.salesman_target_item_amount) AS total_target_period');
			$this->db->from('sales_salesman_target_item');
			$this->db->where('sales_salesman_target_item.salesman_target_item_period', $salesman_target_item_period);
			$this->db->where('sales_salesman_target_item.salesman_id', $salesman_id);
			$result = $this->db->get()->row_array();
			return $result['total_target_period'];
		}

		public function getTotalAmount_Salesman($start_date, $end_date, $salesman_id){
			$this->db->select('SUM(sales_invoice.total_amount) AS total_amount');
			$this->db->from('sales_invoice');
			$this->db->where('sales_invoice.sales_invoice_date >=', $start_date);
			$this->db->where('sales_invoice.sales_invoice_date <=', $end_date);
			$this->db->where('sales_invoice.salesman_id', $salesman_id);
			$this->db->where('sales_invoice.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['total_amount'];
		}

		public function getTotalTargetPeriod_Salesman($salesman_target_item_period, $salesman_id){
			$this->db->select('SUM(sales_salesman_target_item.salesman_target_item_amount) AS total_target_period');
			$this->db->from('sales_salesman_target_item');
			$this->db->where('sales_salesman_target_item.salesman_target_item_period', $salesman_target_item_period);
			$this->db->where('sales_salesman_target_item.salesman_id', $salesman_id);
			$result = $this->db->get()->row_array();
			return $result['total_target_period'];
		}

		public function getTotalCustomerEC($start_date, $end_date, $salesman_id){
			$this->db->select('sales_salesman_itinerary.salesman_itinerary_id');
			$this->db->from('sales_salesman_itinerary');
			$this->db->where('sales_salesman_itinerary.salesman_id', $salesman_id);
			$this->db->where('sales_salesman_itinerary.salesman_itinerary_date >=', $start_date);
			$this->db->where('sales_salesman_itinerary.salesman_itinerary_date <=', $end_date);
			$this->db->where('sales_salesman_itinerary.data_state', 0);
			$result = $this->db->get()->num_rows();
			return $result;	
		}

		public function getTotalCustomerEA($start_date, $end_date, $salesman_id){
			$query = $this->db->query("SELECT DISTINCT sales_salesman_itinerary.customer_id FROM sales_salesman_itinerary WHERE sales_salesman_itinerary.salesman_itinerary_date >= '$start_date' AND sales_salesman_itinerary.salesman_itinerary_date <= '$end_date' AND sales_salesman_itinerary.salesman_id = '$salesman_id' AND sales_salesman_itinerary.customer_id IN (SELECT sales_invoice.customer_id FROM sales_invoice WHERE sales_invoice_date >= '$start_date' AND sales_invoice_date <= '$end_date');")->num_rows();
			return $query;
		}
	}
?>