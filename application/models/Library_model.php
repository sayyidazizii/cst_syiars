<?php
	class library_model extends CI_Model {
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function getmachine(){
			$this->db->select('machine_id,machine_name')->from('core_machine');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getbank(){
			$this->db->select('bank_id,bank_name')->from('core_bank');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getitem(){
			$this->db->select('item_id,item_name')->from('invt_item');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getcurrency(){
			$this->db->select('currency_id,currency_code')->from('acct_currency');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getaccount(){
			$this->db->select('account_id, account_name');
			$this->db->from('acct_account');
			$this->db->where('account_has_child','0');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getbrand(){
			$this->db->select('brand_id, brand_name')->from('core_brand');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}

		public function getconversioncostquantity($id){
			$this->db->select('item_conversion_quantity')->from('invt_item_conversion_cost');
			$this->db->where('item_conversion_cost_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getconsignmentinstock($item_id,$warehouse_id,$supplier_id){
			$this->db->select('last_balance')->from('invt_item_stock_konsinyasi');
			$this->db->where('item_id',$item_id);
			$this->db->where('warehouse_id',$warehouse_id);
			$this->db->where('supplier_id',$supplier_id);
			$result = $this->db->get()->row_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}
		
		public function getnewconsignmentincode(){
			$query = $this->db->query("SELECT getNewCodeConsigmentIn() as konsinyasi_in_code")->row_array();
			return $query['konsinyasi_in_code'];
		}
		
		public function getconversionquantity($item_id, $item_unit_id){
			$this->db->select('item_conversion_quantity')->from('invt_item_conversion_cost');
			$this->db->where('item_id',$item_id);
			$this->db->where('item_unit_id',$item_unit_id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_conversion_quantity'])){
				return '-';
			}else{
				return $result['item_conversion_quantity'];
			}
		}
		
		public function countstock($item_id){
			$result = $this->db->query('SELECT SUM(last_balance) AS last_balance FROM invt_item_stock WHERE item_id="'.$item_id.'"')->row_array();
			return $result['last_balance'];
		}
		
		public function getcategory(){
			$this->db->select('item_category_id, item_category_name')->from('invt_item_category');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}
		
		public function getitemunit(){
			$this->db->select('item_unit_id, item_unit_symbol')->from('invt_item_unit');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}
		
		public function getwarehouse(){
			$this->db->select('warehouse_id, warehouse_name')->from('invt_warehouse');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}		
  
		public function getsupplier(){
			$this->db->select('supplier_id, supplier_name')->from('core_supplier');
			$this->db->where('data_state','0');
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}		
  
		public function getunitsymbol($id){
			$this->db->select('item_unit_symbol')->from('invt_item_unit');
			$this->db->where('item_unit_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_unit_symbol'])){
				return '-';
			}else{
				return $result['item_unit_symbol'];
			}
		}		
		
		public function getitemcategoryname($id){
			$this->db->select('item_category_name')->from('invt_item_category');
			$this->db->where('item_category_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_category_name'])){
				return '-';
			}else{
				return $result['item_category_name'];
			}
		}		
		
		public function getmachinename($id){
			$this->db->select('machine_name')->from('core_machine');
			$this->db->where('machine_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['machine_name'])){
				return '-';
			}else{
				return $result['machine_name'];
			}
		}		
		
		public function getbrandname($id){
			$this->db->select('brand_name')->from('core_brand');
			$this->db->where('brand_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['brand_name'])){
				return '-';
			}else{
				return $result['brand_name'];
			}
		}

		public function getwarehousename($id){
			$this->db->select('warehouse_name')->from('invt_warehouse');
			$this->db->where('warehouse_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['warehouse_name'])){
				return '-';
			}else{
				return $result['warehouse_name'];
			}
		}		
		
		public function getaccountname($id){
			$this->db->select('account_name')->from('acct_account');
			$this->db->where('account_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['account_name'])){
				return '-';
			}else{
				return $result['account_name'];
			}
		}
  
		public function getsuppliername($id){
			$this->db->select('supplier_code, supplier_name')->from('core_supplier');
			$this->db->where('supplier_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['supplier_name'])){
				return '-';
			}else{
				return $result['supplier_name'];
			}
		}

		public function getitemname($id){
			$this->db->select('item_name')->from('invt_item');
			$this->db->where('item_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_name'])){
				return '-';
			}else{
				return $result['item_name'];
			}
		}

		public function getbarcodepaper(){
			$this->db->select('barcode_paper_id, barcode_paper_name')->from('core_barcode_paper');
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getbarcodesticker(){
			$this->db->select('barcode_sticker_id, barcode_sticker_name')->from('core_barcode_sticker');
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getbarcodetype(){
			$this->db->select('barcode_type_id, barcode_type_name')->from('core_barcode_type');
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}
		
		public function getbarcodepapername($id){
			$this->db->select('barcode_paper_name')->from('core_barcode_paper');
			$this->db->where('barcode_paper_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['barcode_paper_name'])){
				return '-';
			}else{
				return $result['barcode_paper_name'];
			}
		}

		public function getbarcodestickername($id){
			$this->db->select('barcode_sticker_name')->from('core_barcode_sticker');
			$this->db->where('barcode_sticker_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['barcode_sticker_name'])){
				return '-';
			}else{
				return $result['barcode_sticker_name'];
			}
		}

		public function getbarcodetypename($id){
			$this->db->select('barcode_type_name')->from('core_barcode_type');
			$this->db->where('barcode_type_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['barcode_type_name'])){
				return '-';
			}else{
				return $result['barcode_type_name'];
			}
		}
  
		public function getitemcode($id){
			$this->db->select('item_code')->from('invt_item');
			$this->db->where('item_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_code'])){
				return '-';
			}else{
				return $result['item_code'];
			}
		}
		
		public function getzone(){
			$this->db->select('zone_id, zone_name')->from('core_zone');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}
		
		public function getzonename($id){
			$this->db->select('zone_name')->from('core_zone');
			$this->db->where('zone_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['zone_name'])){
				return '-';
			}else{
				return $result['zone_name'];
			}
		}

		public function getexpedition(){
			$this->db->select('expedition_id, expedition_name')->from('core_expedition');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}
		
		public function getexpeditionname($id){
			$this->db->select('expedition_name')->from('core_expedition');
			$this->db->where('expedition_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['expedition_name'])){
				return '-';
			}else{
				return $result['expedition_name'];
			}
		}

		public function getstockistgroup(){
			$this->db->select('stockist_group_id, stockist_group_name')->from('sales_stockist_group');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			if ($result->num_rows() > 0 ){
				return $result->result_array();	
			}
			else{
				return array();	
			}
		}
		
		public function getstockistgroupname($id){
			$this->db->select('stockist_group_name')->from('sales_stockist_group');
			$this->db->where('stockist_group_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['stockist_group_name'])){
				return '-';
			}else{
				return $result['stockist_group_name'];
			}
		}

		public function getstockist(){
			$query = $this->db->query("select stockist_id,concat(stockist_code,'  -  ',stockist_name)stockist_name from sales_stockist where data_state='0' AND stockist_status='1'");
			$result = $query->result_array();
			return $result;
		}
		
		public function getstockistname($id){
			$this->db->select('stockist_name')->from('sales_stockist');
			$this->db->where('stockist_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['stockist_name'])){
				return '-';
			}else{
				return $result['stockist_name'];
			}
		}		
		
		public function getstockistcode($id){
			$this->db->select('stockist_code')->from('sales_stockist');
			$this->db->where('stockist_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['stockist_code'])){
				return '-';
			}else{
				return $result['stockist_code'];
			}
		}
	
		public function getunitprice($id){
			$this->db->select('item_unit_price')->from('invt_item');
			$this->db->where('item_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result['item_unit_price'])){
				return '0';
			}else{
				return $result['item_unit_price'];
			}
		}
		
		public function getcompanyaddress(){
			$this->db->select('company_address');
			$this->db->from('preference_company');
			$this->db->limit(1,0);
			$result = $this->db->get()->row_array();
			return $result['comapny_address'];
		}
		
		public function getcompanylogo(){
			$this->db->select('company_logo');
			$this->db->from('preference_company');
			$this->db->limit(1,0);
			$result = $this->db->get()->row_array();
			return $result['company_logo'];
		}
		
		public function getsalesorder($id){
			$this->db->select('*')->from('sales_order');
			$this->db->where('sales_order_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getsalesorderitem($id){
			$this->db->select('*')->from('sales_order_item');
			$this->db->where('sales_order_id',$id);
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getsalesinvoice($id){
			$this->db->select('*')->from('sales_invoice');
			$this->db->where('sales_invoice_id',$id);
			$result = $this->db->get()->row_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getsalesinvoiceitem($id){
			$this->db->select('*')->from('sales_invoice_item');
			$this->db->where('sales_invoice_id',$id);
			$result = $this->db->get()->result_array();
			if(!isset($result)){
				return '-';
			}else{
				return $result;
			}
		}

		public function getIDMenu($id){
			$this->db->select('system_menu.id_menu');
			$this->db->from('system_menu');
			$this->db->where('system_menu.id', $id);
			$result = $this->db->get()->row_array();
			return $result['id_menu'];
		}
		
		public function getUserGroupLevel($id){
			$this->db->select('system_user_group.user_group_level');
			$this->db->from('system_user_group');
			$this->db->where('system_user_group.user_group_id', $id);
			$result = $this->db->get()->row_array();
			return $result['user_group_level'];
		}
		
		public function getIDMenuOnSystemMapping($id){
			$auth 	= $this->session->userdata('auth');
			$level 	= $this->getUserGroupLevel($auth['user_group_level']);
			
			$this->db->select('system_menu_mapping.id_menu');
			$this->db->from('system_menu_mapping');
			$this->db->where('system_menu_mapping.id_menu', $id);
			$this->db->where('system_menu_mapping.user_group_level', $level);
			$result = $this->db->get()->row_array();
			if($result['id_menu']!=''){
				$return = '1';
			}else{
				$return = '0';
			}
			return $return;
		}

	}
?>