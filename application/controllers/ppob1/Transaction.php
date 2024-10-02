<?php
	Class Transaction extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('Android_model');
			$this->load->model('AndroidPPOB_model');
			$this->load->model('TopupPPOB_model');
			$this->load->model('SettingPrice_model');
            $this->load->model('AcctSavingsTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->helper('api_helper');
			$this->load->database('default');
			$this->load->database('cipta');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			
        }


        public function getPPOBPulsaPrePaid(){
            $response = array(
                'error'                         => FALSE,
                'error_msg'                     => "",
                'error_msg_title'               => "",
                'ppobpulsaprepaidproduct'       => "",
            );

            $ppob_agen_id       = $this->input->post('user_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('phone_number', true),
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-prabayar/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            $settingPrice       = $this->SettingPrice_model->getPPOBSettingPrice_Code('PREPAIDDB');

            if($inquiry_data['code'] == 200){
                $no = 0;
                foreach ($inquiry_data['data'] as $key => $val){
                    $price              = ceil($val['price'] + $settingPrice['setting_price_fee']);

                    $ppob_product_price = ceil($val['price'] + $settingPrice['setting_price_fee'] + $settingPrice['setting_price_commission']);

                    $ppobpulsaprepaidproduct[$no]['ppob_product_code']             = $val['product_id'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_name']             = $val['voucher'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_type']             = $val['voucher'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_cost']             = $val['nominal'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_price']            = $ppob_product_price;
                    $ppobpulsaprepaidproduct[$no]['ppob_product_fee']              = $settingPrice['setting_price_fee'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_commission']       = $settingPrice['setting_price_commission'];
                    $ppobpulsaprepaidproduct[$no]['ppob_product_default_price']    = $val['price'];

                    $no++;
                }

                $response['error']                      = FALSE;
                $response['error_msg_title']            = "Success";
                $response['error_title']                = "Data Exist";
                $response['ppob_balance']               = $ppob_balance;
                $response['ppobpulsaprepaidproduct']    = $ppobpulsaprepaidproduct;
                $response['id_transaksi']               = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);

        }

        public function paymentPPOBPulsaPrePaid(){
            $auth = $this->session->userdata('auth');
            
            $response = array(
				'error'                             => FALSE,
				'error_paymentppobpulsaprepaid'	    => FALSE,
				'error_msg_title'		            => "",
				'error_msg'			                => "",
			);


            $data_post = array (
                'productID'                         => $this->input->post('productID',true), 
                'productPrice'                      => $this->input->post('productPrice',true),
                'productDefaultPrice'               => $this->input->post('productDefaultPrice',true),
                'ppob_product_fee'                  => $this->input->post('ppob_product_fee',true),
                'ppob_product_commission'           => $this->input->post('ppob_product_commission',true),
                'member_id'                         => $this->input->post('member_id',true),
                'member_name'                       => $this->input->post('member_name',true),
                'phone_number'                      => $this->input->post('phone_number',true),
                'id_transaksi'                      => $this->input->post('id_transaksi',true),
                'branch_id'                         => $this->input->post('branch_id',true),
                'savings_account_id'                => $this->input->post('savings_account_id',true),
                'savings_id'                        => $this->input->post('savings_id',true),
            );

            $ppob_product_code 			            = $data_post['productID'];

			$ppob_agen_id				            = $data_post['member_id'];

			$ppobproduct 				            = $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

            $ppob_product_price 		            = $data_post['productPrice'];
            
			$ppob_product_default_price             = $data_post['productDefaultPrice'];

            $ppob_product_fee                       = $data_post['ppob_product_fee'];

            $ppob_product_commission                = $data_post['ppob_product_commission'];

            $savings_account_id                     = $data_post['savings_account_id'];

            $savings_id                             = $data_post['savings_id'];

			if($ppob_agen_id == null){
				$ppob_agen_id 			            = 0;
            }

            /* Saldo Simpanan Dana PPOB madani */
            $database 					            = $this->db->database;
			$ppob_company_id			            = $this->AndroidPPOB_model->getPPOBCompanyID($database);
            $ppob_balance_company                   = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);

            if (empty($ppob_balance_company)){
                $ppob_balance_company = 0;
            }

            /* Saldo Simpanan Anggota */
            $ppobbalance                            = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);
            $ppob_balance                           = $ppobbalance['savings_account_last_balance'];
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            /* Saldo Dana PPOB Cabang */
            $topup_branch_balance                   = $this->AndroidPPOB_model->getTopupBranchBalance($data_post['branch_id']);
            
            if(empty($topup_branch_balance)){
                $topup_branch_balance   = 0;
            }

			if($ppob_balance < $ppob_product_price){
				$response['error_paymentppobpulsaprepaid'] 	    = TRUE;
				$response['error_msg_title'] 					= "Transaksi Gagal";
				$response['ppob_transaction_remark'] 			= "Saldo Anda tidak mencukupi";
			} else {
                if ($topup_branch_balance < $ppob_product_price){
                    $response['error_paymentppobpulsaprepaid'] 	    = TRUE;
                    $response['error_msg_title'] 					= "Transaksi Gagal";
                    $response['ppob_transaction_remark'] 		    = "Dana PPOB Cabang tidak mencukupi";
                } else {
                    if($ppob_balance_company < $ppob_product_price){
                        $response['error_paymentppobpulsaprepaid'] 	    = TRUE;
                        $response['error_msg_title'] 					= "Transaksi Gagal";
                        $response['ppob_transaction_remark'] 			= "Dana PPOB tidak mencukupi";
                    } else {
                        $data_inquiry[0] = array (
                            'product_id'        => $ppob_product_code,
                            'nova'              => $data_post['phone_number'],
                            'id_transaksi'      => $data_post['id_transaksi']
                        );
                        
                        $data = array();

                        $data['url']            = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-prabayar/payment';
                        $data['apikey']         = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                        $data['secretkey']      = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                        $data['content']        = json_encode($data_inquiry);

                        $inquiry_data           = json_decode(apiTrans($data), true);

                        if($inquiry_data['code'] == 200){
                            $ppob_transaction_status = 1;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['data']['trxID'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'          	=> $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $savings_account_id,
                                'savings_id'			                => $savings_id,
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $data_post['productPrice'],
                                'ppob_transaction_default_amount'	    => $data_post['productDefaultPrice'],
                                'ppob_transaction_fee_amount'	        => $data_post['ppob_product_fee'],
                                'ppob_transaction_commission_amount'	=> $data_post['ppob_product_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'created_id'				            => $data_post['member_id'],
                                'ppob_transaction_remark'	            => 'trxID : '.$inquiry_data['data']['trxID'].' - VoucherSN : '.$inquiry_data['data']['token'].' - Nomor HP : '.$inquiry_data['data']['nova'].' - '.$ppobproduct['ppob_product_name'].' - '.$ppobproduct['ppob_product_title'].' - ID Transaksi : '.$data_post['id_transaksi'], 
                                'created_on'				            => date('Y-m-d H:i:s')
                            );

                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);

                                $data_profitshare = array (
                                    'member_id'                     => $data_post['member_id'],
                                    'savings_account_id'            => $savings_account_id,
                                    'savings_id'                    => $savings_id, 
                                    'branch_id'                     => $data_post['branch_id'],
                                    'ppob_profit_share_date'        => date("Y-m-d"),
                                    'ppob_profit_share_amount'      => $ppob_product_commission,
                                    'data_state'                    => 0,
                                    'created_id'                    => $auth['user_id'],
                                    'created_on'                    => date("Y-m-d H:i:s"),
                                );      

                                if($this->AndroidPPOB_model->insertPPOBProfitShare_Company($data_profitshare)){
                                    $data_jurnal = array (
                                        'branch_id'                 => $data_post['branch_id'],
                                        'ppob_company_id'           => $ppob_company_id,
                                        'member_id'                 => $data_post['member_id'],
                                        'member_name'               => $data_post['member_name'],
                                        'product_name'              => $ppobproduct['ppob_product_name'],
                                        'ppob_agen_price'           => $ppob_product_price,
                                        'ppob_company_price'        => $ppob_product_default_price,
                                        'ppob_admin'                => 0,
                                        'ppob_fee'                  => $ppob_product_fee,
                                        'ppob_commission'           => $ppob_product_commission,
                                        'savings_account_id'        => $savings_account_id,
                                        'savings_id'                => $savings_id,
                                        'journal_status'            => 1,
                                    );

                                    $this->journalPPOB($data_jurnal);
                                }                            
                            }
                
                            $response['error_paymentppobpulsaprepaid'] 	    = FALSE;
                            $response['error_msg_title'] 					= "Transaksi Berhasil";
                            $response['ppob_transaction_remark'] 			= $datappob_transaction['ppob_transaction_remark'];

                        } else {
                            $ppob_transaction_status = 2;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['code'].' - '.$data_inquiry[0]['id_transaksi'].' - '.$data_inquiry[0]['phone_number'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $data_post['productPrice'],
                                'ppob_transaction_default_amount'	    => $data_post['productPrice'],
                                'ppob_transaction_fee_amount'	        => $data_post['productPrice'],
                                'ppob_transaction_commission_amount'	=> $data_post['productPrice'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'created_id'				            => $data_post['member_id'],
                                'ppob_transaction_remark'	            => 'trxID : '.$data_inquiry[0]['id_transaksi'].' - VoucherSN : VOUCHERSN Nomor HP '.$data_inquiry[0]['phone_number'].' - '.$data_inquiry[0]['product_id'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );

                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);
                            }
                
                            $response['error_paymentppobpulsaprepaid'] 	    = FALSE;
                            $response['error_msg_title'] 					= "Transaksi Gagal";
                            $response['ppob_transaction_remark'] 			= $datappob_transaction['ppob_transaction_remark'];
                        }
                    }
                }
			}

			echo json_encode($response);
        }


        //TOPUP EMONEY----------------------------------------------------------------------------
        public function getPPOBTopUpEMoneyCategory(){
            $response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobtopupemoney'			=> "",
            );
            
			$ppobtopupemoneycategory[0]['ppob_product_category_id']	    = 28;
			$ppobtopupemoneycategory[0]['ppob_product_category_name']	= 'Topup Dana';
			$ppobtopupemoneycategory[0]['ppob_product_category_code']	= 'DANA';
			$ppobtopupemoneycategory[1]['ppob_product_category_id']	    = 29;
			$ppobtopupemoneycategory[1]['ppob_product_category_name']	= 'Topup OVO';
			$ppobtopupemoneycategory[1]['ppob_product_category_code']   = 'GRAB';
			$ppobtopupemoneycategory[2]['ppob_product_category_id']	    = 30;
			$ppobtopupemoneycategory[2]['ppob_product_category_name']	= 'Topup GoPay';
			$ppobtopupemoneycategory[2]['ppob_product_category_code']	= 'GOJEK';
            $ppobtopupemoneycategory[3]['ppob_product_category_id']	    = 72;
			$ppobtopupemoneycategory[3]['ppob_product_category_name']	= 'Topup Shopee';
			$ppobtopupemoneycategory[3]['ppob_product_category_code']	= 'SHOPEE';
			/* $ppobtopupemoney[3]['ppob_product_category_id']	    = 31;
			$ppobtopupemoney[3]['ppob_product_category_name']	= 'Topup E-Toll';
			$ppobtopupemoney[3]['ppob_product_category_code']   = 'ETOLL'; */
		
		
			
			$response['error'] 						= FALSE;
			$response['error_msg_title'] 			= "Success";
			$response['error_msg'] 					= "Data Exist";
			$response['ppobtopupemoneycategory']    = $ppobtopupemoneycategory;

			echo json_encode($response);
        }

        public function getPPOBTopUpEMoneyProduct(){
            $response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobtopupemoneyproduct'	=> "",
            );

            $ppob_agen_id           = $this->input->post('member_id');

            $ppob_balance           = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance       = 0;
            }

            $data_inquiry[0]        = array (
                'productCode'          => $this->input->post('productCode', true),
            );

            $data = array();

            $data['url']            = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-topup/inquiry';
            $data['apikey']         = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']      = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']        = json_encode($data_inquiry);


            $inquiry_data           = json_decode(apiTrans($data), true);

            $settingPrice           = $this->SettingPrice_model->getPPOBSettingPrice_Code('EMONEY');

            if($inquiry_data['code'] == 200){
                $no = 0;
                foreach ($inquiry_data['data'] as $key => $val){
                    $price                  = ceil($val['price'] + $settingPrice['setting_price_fee']);
                    $price_commission       = ceil($price + $settingPrice['setting_price_commission']);

                    $ppob_product_price     = ceil($val['price'] + $settingPrice['setting_price_fee'] + $settingPrice['setting_price_commission']);

                    $ppobtopupemoneyproduct[$no]['ppob_product_code']               = $val['productCode'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_name']               = $val['productDesc'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_price']              = $ppob_product_price;
                    $ppobtopupemoneyproduct[$no]['ppob_product_fee']                = $settingPrice['setting_price_fee'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_commission']         = $settingPrice['setting_price_commission'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_default_price']      = $val['price'];

                    $no++;
                }

                $response['error']                      = FALSE;
                $response['error_msg_title']            = "Success";
                $response['error_title']                = "Data Exist";
                $response['ppob_balance']               = $ppob_balance;
                $response['ppobtopupemoneyproduct']     = $ppobtopupemoneyproduct;
                $response['id_transaksi']               = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

			echo json_encode($response);
        }

        public function paymentPPOBTopUpEMoney(){
            $auth = $this->session->userdata('auth');

            $response = array(
				'error'									=> FALSE,
				'error_paymentppobtopupemoney'			=> FALSE,
				'error_msg_title'	                    => "",
				'error_msg'                 			=> "",
			);

            $data_post                  = array(
                'ppob_product_code' 		    => $this->input->post('productCode',true),
                'ppob_agen_id'				    => $this->input->post('member_id', true),
                'ppob_product_price' 		    => $this->input->post('productPrice',true),
                'ppob_product_default_price'    => $this->input->post('productDefaultPrice',true),
                'ppob_product_fee'              => $this->input->post('ppob_product_fee', true),
                'ppob_product_commission'       => $this->input->post('ppob_product_commission', true),
                'member_id'				        => $this->input->post('member_id',true),
                'member_name'		        	=> $this->input->post('member_name',true),
                'branch_id'                     => $this->input->post('branch_id', true),
                'savings_account_id'            => $this->input->post('savings_account_id', true),
                'savings_id'                    => $this->input->post('savings_id', true),
                'id_pelanggan'                  => $this->input->post('id_pelanggan', true),
                'productCode'                   => $this->input->post('productCode', true),
                'id_transaksi'                  => $this->input->post('id_transaksi', true),
            );

			$ppob_product_code 			    = $data_post['productCode'];

            $ppob_agen_id				    = $data_post['member_id'];

            $ppobproduct 				    = $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

            $ppob_product_price 		    = $data_post['ppob_product_price'];
            
			$ppob_product_default_price     = $data_post['productDefaultPrice'];

            $ppob_product_fee               = $data_post['ppob_product_fee'];

            $ppob_product_commission        = $data_post['ppob_product_commission'];

            $savings_account_id             = $data_post['savings_account_id'];

            $savings_id                     = $data_post['savings_id'];

            if($ppob_agen_id == null){
				$ppob_agen_id 			    = 0;
            }

            /* Saldo Simpanan Dana PPOB madani */
            $database 					    = $this->db->database;
            $ppob_company_id			    = $this->AndroidPPOB_model->getPPOBCompanyID($database);
            $ppob_balance_company           = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);

            if (empty($ppob_balance_company)){
                $ppob_balance_company = 0;
            }

            /* Saldo Simpanan Anggota */
            $ppobbalance                    = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);
            $ppob_balance                   = $ppobbalance['savings_account_last_balance'];
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            /* Saldo Dana PPOB Cabang */
            $topup_branch_balance           = $this->AndroidPPOB_model->getTopupBranchBalance($data_post['branch_id']);
            
            if(empty($topup_branch_balance)){
                $topup_branch_balance   = 0;
            }

			if($ppob_balance < $ppob_product_price){
				$response['error_paymentppobtopupemoney'] 	    = TRUE;
				$response['error_msg_title'] 					= "Transaksi Gagal";
				$response['ppob_transaction_remark']    		= "Saldo Anda tidak mencukupi";
			} else {
                if ($topup_branch_balance < $ppob_product_price){
                    $response['error_paymentppobtopupemoney'] 	    = TRUE;
                    $response['error_msg_title'] 					= "Transaksi Gagal";
                    $response['ppob_transaction_remark'] 			= "Dana PPOB Cabang tidak mencukupi";
                } else {
                    if($ppob_balance_company < $ppob_product_price){
                        $response['error_paymentppobtopupemoney'] 	    = TRUE;
                        $response['error_msg_title'] 					= "Transaksi Gagal";
                        $response['ppob_transaction_remark'] 			= "Dana PPOB tidak mencukupi";
                    } else {
                        $data_inquiry[0] = array (
                            'nova'          => $data_post['id_pelanggan'],
                            'productCode'   => $data_post['productCode'],
                            'id_transaksi'  => $data_post['id_transaksi'],
                        );
                        
                        $data = array();

                        $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-topup/payment';
                        $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                        $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                        $data['content']    = json_encode($data_inquiry);

                        $inquiry_data       = json_decode(apiTrans($data), true);

                        if($inquiry_data['code'] == 200){
                            $ppob_transaction_status = 1;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['data']['trxID'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $savings_account_id,
                                'savings_id'			                => $savings_id,
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $data_post['ppob_product_price'],
                                'ppob_transaction_default_amount'	    => $data_post['ppob_product_default_price'],
                                'ppob_transaction_fee_amount'	        => $data_post['ppob_product_fee'],
                                'ppob_transaction_commission_amount'	=> $data_post['ppob_product_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'created_id'				            => $this->input->post('member_id',true),
                                'ppob_transaction_remark'	            => 'trxID : '.$inquiry_data['data']['trxID'].' - Voucher Code : '.$inquiry_data['data']['voucher'].' - ID Pelanggan : '.$inquiry_data['data']['nova'].' - '.$ppobproduct['ppob_product_name'].' - '.$ppobproduct['ppob_product_title'].' - No. Referensi : '.$inquiry_data['data']['ref'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );
                
                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);


                                $data_profitshare = array (
                                    'member_id'                         => $data_post['member_id'],
                                    'savings_account_id'                => $savings_account_id,
                                    'savings_id'                        => $savings_id, 
                                    'branch_id'                         => $data_post['branch_id'],
                                    'ppob_profit_share_date'            => date("Y-m-d"),
                                    'ppob_profit_share_amount'          => $ppob_product_commission,
                                    'data_state'                        => 0,
                                    'created_id'                        => $auth['user_id'],
                                    'created_on'                        => date("Y-m-d H:i:s"),
                                );      

                                if($this->AndroidPPOB_model->insertPPOBProfitShare_Company($data_profitshare)){
                                    $data_jurnal = array (
                                        'branch_id'                     => $data_post['branch_id'],
                                        'ppob_company_id'               => $ppob_company_id,
                                        'member_id'                     => $data_post['member_id'],
                                        'member_name'                   => $data_post['member_name'],
                                        'product_name'                  => $ppobproduct['ppob_product_name'],
                                        'ppob_agen_price'               => $data_post['ppob_product_price'],
                                        'ppob_company_price'            => $data_post['ppob_product_default_price'],
                                        'ppob_admin'                    => 0,
                                        'ppob_fee'                      => $ppob_product_fee,
                                        'ppob_commission'               => $ppob_product_commission,
                                        'savings_account_id'            => $savings_account_id,
                                        'savings_id'                    => $savings_id,
                                        'journal_status'                => 1,
                                    );

                                    $this->journalPPOB($data_jurnal);
                                } 
                            }
                
                            $response['error_paymentppobtopupemoney'] 	    = FALSE;
                            $response['error_msg_title'] 					= "Transaksi Berhasil";
                            $response['ppob_transaction_remark'] 			= $datappob_transaction['ppob_transaction_remark'];

                        } else {
                            $ppob_transaction_status = 2;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['data']['trxID'].$inquiry_data['code'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $this->input->post('member_id',true),
                                'ppob_agen_name'			            => $this->input->post('member_name',true),
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $inquiry_data['data']['harga'],
                                'ppob_transaction_default_amount'	    => $data_post['ppob_product_price'],
                                'ppob_transaction_fee_amount'	        => $data_post['ppob_product_price'],
                                'ppob_transaction_commission_amount'	=> $data_post['ppob_product_price'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'created_id'				            => $this->input->post('member_id',true),
                                'ppob_transaction_remark'	            => 'trxID : '.$data_inquiry[0]['id_transaksi'].' - Voucher Code VOUCHERCODE ID Pelanggan : '.$data_inquiry[0]['nova'].' - '.$ppobproduct['ppob_product_name'].' - '.$ppobproduct['ppob_product_title'].' - Product Code : '.$data_inquiry[0]['productCode'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_on'				=> date('Y-m-d H:i:s')
                            );
                
                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);
                            }
                
                            $response['error_paymentppobtopupemoney'] 	    = FALSE;
                            $response['error_msg_title'] 					= "Transaksi Gagal";
                            $response['ppob_transaction_remark'] 			= $datappob_transaction['ppob_transaction_remark'];
                        }
                    }
                }
			}

			echo json_encode($response);
        }





        //PLN POSTPAID---------------------------------------------------------------------
        public function getPPOBPLNPostPaid(){
            $response = array(
                'error'                      => FALSE,
                'error_msg'                  => "",
                'error_msg_title'            => "",
                'ppobplnpostpaidproduct'     => "",
            );

            $member_id          = $this->input->post('member_id');

            $ppobsavingsaccount = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($member_id);

            if (empty($ppobsavingsaccount)){
                $ppob_balance   = 0;
            } else {
                $ppob_balance   = $ppobsavingsaccount['savings_account_last_balance'];
            }

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('id_pelanggan_pln', true),
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/postpaid/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);

            $inquiry_data       = json_decode(apiTrans($data), true);

            $settingPrice       = $this->SettingPrice_model->getPPOBSettingPrice_Code('PLNPOSTPAIDDB');

            if($inquiry_data['code'] == 200){
                if ($inquiry_data['data']['responseCode'] == '00'){
                    $ppobplnpostpaidproduct[0]['refID']			                = $inquiry_data['data']['refID'];
                    $ppobplnpostpaidproduct[0]['id_pelanggan']	                = $inquiry_data['data']['subscriberID'];
                    $ppobplnpostpaidproduct[0]['tarif']			                = $inquiry_data['data']['tarif'];
                    $ppobplnpostpaidproduct[0]['daya']			                = $inquiry_data['data']['daya'];
                    $ppobplnpostpaidproduct[0]['nama']			                = $inquiry_data['data']['nama'];
                    $ppobplnpostpaidproduct[0]['totalTagihan']			        = $inquiry_data['data']['totalTagihan'];
                    $ppobplnpostpaidproduct[0]['lembarTagihanTotal']	        = $inquiry_data['data']['lembarTagihanTotal'];
                    $ppobplnpostpaidproduct[0]['responseCode']	                = '0000';
                    $ppobplnpostpaidproduct[0]['message']	                    = $inquiry_data['data']['message'];
                    
                    $detilTagihan = $inquiry_data['data']['detilTagihan'];
                    
                    foreach($detilTagihan as $key => $val){
                        $ppob_product_fee           = $settingPrice['setting_price_fee'];
                        $ppob_product_commission    = $settingPrice['setting_price_commission'];
                        $ppob_product_admin         = $val['admin'] - $ppob_product_fee - $ppob_product_commission;


                        $ppobplnpostpaidbill[$key]['periodeTagihan']	        = $val['periode'];
                        $ppobplnpostpaidbill[$key]['nilaiTagihan']		        = $val['nilaiTagihan'];
                        $ppobplnpostpaidbill[$key]['dendaTagihan']		        = $val['denda'];
                        $ppobplnpostpaidbill[$key]['adminTagihan']		        = $val['admin'];
                        $ppobplnpostpaidbill[$key]['jumlahTagihan']		        = $val['total'];
                    }
        
                    $response['error'] 							                = FALSE;
                    $response['error_msg_title'] 				                = "Success";
                    $response['error_msg'] 						                = "Data Exist";
                    $response['ppob_balance'] 					                = $ppob_balance;
                    $response['ppobplnpostpaidproduct'] 		                = $ppobplnpostpaidproduct;
                    $response['ppobplnpostpaidbill'] 			                = $ppobplnpostpaidbill;
                    $response['id_transaksi']                                   = $inquiry_data['id_transaksi'];
                } else {
                    $ppobplnpostpaidproduct[0]['responseCode']	                = $inquiry_data['data']['responseCode'];
                    $ppobplnpostpaidproduct[0]['message']	                    = $inquiry_data['data']['message'];
                    
                    $ppobplnpostpaidbill[0]['periodeTagihan']	                = "";
                    $ppobplnpostpaidbill[0]['nilaiTagihan']		                = "";
                    $ppobplnpostpaidbill[0]['dendaTagihan']		                = "";
                    $ppobplnpostpaidbill[0]['adminTagihan']		                = "";
                    $ppobplnpostpaidbill[0]['jumlahTagihan']		            = "";
                    
                    
                    $response['error'] 							                = FALSE;
                    $response['error_msg_title'] 				                = "Success";
                    $response['error_msg'] 						                = "Data Exist";
                    $response['ppob_balance'] 					                = $ppob_balance;
                    $response['ppobplnpostpaidproduct'] 		                = $ppobplnpostpaidproduct;
                    $response['ppobplnpostpaidbill'] 			                = $ppobplnpostpaidbill;
                    $response['id_transaksi']                                   = 0;
                }
				
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);
        }

        public function paymentPPOBPLNPostPaid(){
            $auth = $this->session->userdata('auth');
            
            $response = array(
				'error'								=> FALSE,
				'error_paymentppobplnpostpaid'		=> FALSE,
				'error_msg_title'	                => "",
				'error_msg'			                => "",
			);

            $data_post = array(
                'member_id'                         => $this->input->post('member_id', true),
                'member_name'                       => $this->input->post('member_name', true),
                'id_pelanggan_pln'                  => $this->input->post('id_pelanggan_pln', true),
                'totalTagihan'                      => $this->input->post('totalTagihan', true),
                'refID'                             => $this->input->post('refID', true),
                'id_transaksi'                      => $this->input->post('id_transaksi', true),
                'branch_id'                         => $this->input->post('branch_id', true),
                'savings_account_id'                => $this->input->post('savings_account_id', true),
                'savings_id'                        => $this->input->post('savings_id', true),
            );

			$ppobresponstatus 			    = $this->configuration->PpobResponeCode();

			$ppob_product_code 			    = 'PLNPOSTPAIDB';

			$ppob_agen_id				    = $data_post['member_id'];
			
			$ppobproduct 				    = $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$totaltagihan 				    = $data_post['totalTagihan'];

            $savings_account_id             = $data_post['savings_account_id'];

            $savings_id                     = $data_post['savings_id'];

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }

            $database 					    = $this->db->database;
			$ppob_company_id			    = $this->AndroidPPOB_model->getPPOBCompanyID($database);
            $ppob_balance_company           = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);

            if (empty($ppob_balance_company)){
                $ppob_balance_company = 0;
            }

            $ppobbalance                    = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);
            $ppob_balance                   = $ppobbalance['savings_account_last_balance'];
            
            if(empty($ppob_balance)){
                $ppob_balance           = 0;
            }

            /* Saldo Dana PPOB Cabang */
            $topup_branch_balance           = $this->AndroidPPOB_model->getTopupBranchBalance($data_post['branch_id']);
            
            if(empty($topup_branch_balance)){
                $topup_branch_balance   = 0;
            }

			if($ppob_balance < $totaltagihan){
				$response['error_paymentppobplnpostpaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Transaksi Gagal";
				$response['ppob_transaction_remark'] 		= "Saldo Anda tidak mencukupi";
			} else {
                if ($topup_branch_balance < $totaltagihan){
                    $response['error_paymentppobplnpostpaid'] 	    = TRUE;
                    $response['error_msg_title'] 					= "Transaksi Gagal";
                    $response['ppob_transaction_remark'] 			= "Dana PPOB Cabang tidak mencukupi";
                } else{
                    if($ppob_balance_company < $totaltagihan){
                        $response['error_paymentppobplnpostpaid'] 	    = TRUE;
                        $response['error_msg_title'] 					= "Transaksi Gagal";
                        $response['ppob_transaction_remark'] 			= "Dana PPOB tidak mencukupi";
                    } else {
                    
                        $data_inquiry[0] = array (
                            'nova' 		    => $data_post['id_pelanggan_pln'],
                            'id_transaksi' 	=> $data_post['id_transaksi']
                        );
                        
                        $data = array();

                        $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/postpaid/payment';
                        $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                        $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                        $data['content']    = json_encode($data_inquiry);

                        $inquiry_data       = json_decode(apiTrans($data), true);

                        $settingPrice       = $this->SettingPrice_model->getPPOBSettingPrice_Code('PLNPOSTPAIDDB');
            
                        if($inquiry_data['code'] == 200){
                            $detilTagihan = $inquiry_data['data']['detilTagihan'];
                        
                            foreach($detilTagihan as $key => $val){
                                $ppob_transaction_status = 1;

                                $nilaiTagihan   = $val['nilaiTagihan'];
                                $denda          = $val['denda'];
                                $admin          = $val['admin'];

                                $ppob_transaction_admin_amount              = $admin - ($settingPrice['setting_price_fee' ] + $settingPrice['setting_price_commission']);
                                $ppob_transaction_company_amount            = $ppob_transaction_admin_amount;

                                $ppob_transaction_amount                    = $nilaiTagihan + $ppob_transaction_admin_amount;

                                $datappob_transaction = array (
                                    'ppob_unique_code'			            => $inquiry_data['data']['noReferensi'],
                                    'ppob_company_id'			            => $ppob_company_id,
                                    'ppob_agen_id'				            => $data_post['member_id'],
                                    'ppob_agen_name'			            => $data_post['member_name'],
                                    'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                    'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                    'member_id'				                => $data_post['member_id'],
                                    'savings_account_id'		            => $savings_account_id,
                                    'savings_id'			                => $savings_id,
                                    'branch_id'			                    => $data_post['branch_id'],
                                    'transaction_id'			            => $data_post['id_transaksi'],
                                    'ppob_transaction_amount'	            => $ppob_transaction_amount,
                                    'ppob_transaction_default_amount'	    => $val['total'],
                                    'ppob_transaction_admin_amount'	        => $ppob_transaction_admin_amount,
                                    'ppob_transaction_company_amount'	    => $ppob_transaction_admin_amount,
                                    'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                    'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                    'ppob_transaction_date'		            => date('Y-m-d'),
                                    'ppob_transaction_status'	            => $ppob_transaction_status,
                                    'ppob_transaction_remark'	            => 'ID Pelanggan : '.$inquiry_data['data']['subscriberID'].' Nama '.$inquiry_data['data']['namaPengguna'].' - Tarif/Daya : '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' - No. Ref : '.$inquiry_data['data']['noReferensi'].' - Lembar Tagihan : '.$inquiry_data['data']['lembarTagihanTotal'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                    'created_id'				            => $data_post['member_id'],
                                    'created_on'				            => date('Y-m-d H:i:s')
                                );
                    
                                if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                    $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);

                                    $data_profitshare = array (
                                        'member_id'                 => $data_post['member_id'],
                                        'savings_account_id'        => $savings_account_id,
                                        'savings_id'                => $savings_id, 
                                        'branch_id'                 => $data_post['branch_id'],
                                        'ppob_profit_share_date'    => date("Y-m-d"),
                                        'ppob_profit_share_amount'  => $settingPrice['setting_price_commission'],
                                        'data_state'                => 0,
                                        'created_id'                => $auth['user_id'],
                                        'created_on'                => date("Y-m-d H:i:s"),
                                    );      

                                    if($this->AndroidPPOB_model->insertPPOBProfitShare_Company($data_profitshare)){
                                        $data_jurnal = array (
                                            'branch_id'                 => $data_post['branch_id'],
                                            'ppob_company_id'           => $ppob_company_id,
                                            'member_id'                 => $data_post['member_id'],
                                            'member_name'               => $data_post['member_name'],
                                            'product_name'              => $ppobproduct['ppob_product_name'],
                                            'ppob_agen_price'           => $datappob_transaction['ppob_transaction_amount'],
                                            'ppob_company_price'        => $nilaiTagihan,
                                            'ppob_admin'                => $ppob_transaction_admin_amount,
                                            'ppob_fee'                  => $settingPrice['setting_price_fee'],
                                            'ppob_commission'           => $settingPrice['setting_price_commission'],
                                            'savings_account_id'        => $savings_account_id,
                                            'savings_id'                => $savings_id,
                                            'journal_status'            => 1,
                                        );
        
                                        $this->journalPPOB($data_jurnal);

                                    }
                                }
                            }
                            
                
                            $response['error_paymentppobplnpostpaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Berhasil";
                            $response['ppob_transaction_remark']        = $datappob_transaction['ppob_transaction_remark'];

                        } else {
                            $ppob_transaction_status = 2;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['code'].' - '.$data_inquiry[0]['id_transaksi'].' - '.$data_inquiry[0]['nova'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'			            => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $totaltagihan,
                                'ppob_transaction_default_amount'	    => $totaltagihan,
                                'ppob_transaction_admin_amount'	        => 0,
                                'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'ppob_transaction_remark'	            => 'ID Pelanggan 0000',
                                'ppob_transaction_remark'	            => 'ID Pelanggan : '.$data_inquiry[0]['nova'].' - Nama NAMA - Tarif/Daya TARIF/DAYA - ID. Transaksi : '.$data_inquiry[0]['id_transaksi'].' - Nominal : '.$data_inquiry[0]['nominal'].' - No. Ref NO. REF - : '.$inquiry_data['data']['message'].' - ID Transaksi : '. $data_post['id_transaksi'],
                                'created_id'				            => $data_post['member_id'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );
                
                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);
                            }
                
                            $response['error_paymentppobplnpostpaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Gagal";
                            $response['ppob_transaction_remark']        = $datappob_transaction['ppob_transaction_remark'];
                        }
                    }
                }
            }

			echo json_encode($response);
        }

         //PLN PPREPAID--------------------------------------------------------------------
        public function getPPOBPLNPrePaid(){
            $response = array(
                'error'                      => FALSE,
                'error_msg'                  => "",
                'error_msg_title'            => "",
                'ppobplnprepaidproduct'      => "",
            );

            $member_id          = $this->input->post('member_id');

            $ppobsavingsaccount = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($member_id);

            if (empty($ppobsavingsaccount)){
                $ppob_balance   = 0;
            } else {
                $ppob_balance   = $ppobsavingsaccount['savings_account_last_balance'];
            }

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('id_pelanggan_pln', true),
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/prepaid/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            if($inquiry_data['code'] == 200){
                $ppobplnprepaidproduct[0]['msn']			= $inquiry_data['data']['msn'];
				$ppobplnprepaidproduct[0]['id_pelanggan']	= $inquiry_data['data']['subscriberID'];
				$ppobplnprepaidproduct[0]['tarif']			= $inquiry_data['data']['tarif'];
				$ppobplnprepaidproduct[0]['daya']			= $inquiry_data['data']['daya'];
				$ppobplnprepaidproduct[0]['nama']			= $inquiry_data['data']['nama'];
				$ppobplnprepaidproduct[0]['admin']			= $inquiry_data['data']['admin'];
				$ppobplnprepaidproduct[0]['refID']			= $inquiry_data['data']['refID'];
				
				$nominalPLN = $inquiry_data['data']['powerPurchaseDenom'];
				
				foreach($nominalPLN as $key => $val){
					$ppobplnprepaidnominal[$key]['nominalPLN']	= $val;
				}
	
				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobplnprepaidproduct'] 			= $ppobplnprepaidproduct;
				$response['ppobplnprepaidnominal'] 			= $ppobplnprepaidnominal;
                $response['id_transaksi']                   = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);
        }

        public function ccMasking($data) {
            return substr($data, 0, 4)."-".substr($data, 4, 4)."-".substr($data,8, 4)."-".substr($data,12, 4)."-".substr($data,16, 4);
		}

        public function paymentPPOBPLNPrePaid(){
            
            $auth = $this->session->userdata('auth');

            $response = array(
				'error'							=> FALSE,
				'error_paymentppobplnprepaid'	=> FALSE,
				'error_msg_title'		        => "",
				'error_msg'			            => "",
			);

            $data_post = array(
                'member_id'                     => $this->input->post('member_id', true),
                'nominalPLN'                    => $this->input->post('nominalPLN', true),
                'adminPLN'                      => $this->input->post('adminPLN', true),
                'id_pelanggan_pln'              => $this->input->post('id_pelanggan_pln', true),
                'id_transaksi'                  => $this->input->post('id_transaksi', true),
                'member_name'                   => $this->input->post('member_name', true),
                'branch_id'                     => $this->input->post('branch_id', true),
                'savings_account_id'            => $this->input->post('savings_account_id', true),
                'savings_id'                    => $this->input->post('savings_id', true),
            );

			$ppob_product_code 			= 'PLNPREPAIDB';

			$ppob_agen_id				= $data_post['member_id'];
			
			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$nominal 					= $data_post['nominalPLN'];

			$by_admin 					= $data_post['adminPLN'];

            $savings_account_id         = $data_post['savings_account_id'];

            $savings_id                 = $data_post['savings_id'];

			$totalnominal				= $nominal + $by_admin;

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }

            $database 					= $this->db->database;
			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);
            $ppob_balance_company       = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);

            if (empty($ppob_balance_company)){
                $ppob_balance_company = 0;
            }

            $ppobbalance                = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);
            $ppob_balance               = $ppobbalance['savings_account_last_balance'];
            if(empty($ppob_balance)){
                $ppob_balance = 0;
            }

            /* Saldo Dana PPOB Cabang */
            $topup_branch_balance           = $this->AndroidPPOB_model->getTopupBranchBalance($data_post['branch_id']);
            
            if(empty($topup_branch_balance)){
                $topup_branch_balance   = 0;
            }


			if($ppob_balance < $totalnominal){
				$response['error_paymentppobplnprepaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['ppob_transaction_remark'] 		= "Saldo Anda tidak mencukupi";
			} else {
                if($topup_branch_balance < $totalnominal){
                    $response['error_paymentppobplnprepaid'] 	    = TRUE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['ppob_transaction_remark']            = "Dana PPOB Cabang tidak mencukupi";
                } else {
                    if($ppob_balance_company < $totalnominal){
                        $response['error_paymentppobplnprepaid'] 	    = TRUE;
                        $response['error_msg_title'] 					= "Confirm";
                        $response['ppob_transaction_remark'] 			= "Dana PPOB tidak mencukupi";
                    } else {
                        $data_inquiry[0] = array (
                            'nominal' 		=> $data_post['nominalPLN'],
                            'nova' 		    => $data_post['id_pelanggan_pln'],
                            'id_transaksi' 	=> $data_post['id_transaksi']
                        );

                        $data = array();

                        $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/prepaid/payment';
                        $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                        $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                        $data['content']    = json_encode($data_inquiry);

                        $inquiry_data       = json_decode(apiTrans($data), true);
            
                        $settingPrice       = $this->SettingPrice_model->getPPOBSettingPrice_Code('PLNPREPAIDDB');

                        if($inquiry_data['code'] == 200){
                            $ppob_transaction_status = 1;

                            $token 	= $this->ccMasking($inquiry_data['data']['tokenNumber']);

                            $ppob_transaction_admin_amount              = $by_admin - ($settingPrice['setting_price_fee' ] + $settingPrice['setting_price_commission']);
                            $ppob_transaction_company_amount            = $ppob_transaction_admin_amount;

                            $ppob_transaction_amount                    = $nominal + $ppob_transaction_admin_amount;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['data']['noReferensi'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $savings_account_id,
                                'savings_id'			                => $savings_id,
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'			            => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $ppob_transaction_amount,
                                'ppob_transaction_default_amount'	    => $totalnominal,
                                'ppob_transaction_admin_amount'	        => $ppob_transaction_admin_amount,
                                'ppob_transaction_company_amount'	    => $ppob_transaction_admin_amount,
                                'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'ppob_transaction_remark'	            => 'ID Pelanggan : '.$inquiry_data['data']['msn'].' - Nama : '.$inquiry_data['data']['namaPengguna'].' - Tarif : '.$inquiry_data['data']['tarif'].' - Daya : '.$inquiry_data['data']['daya'].' - No. Ref : '.$inquiry_data['data']['noReferensi'].' - Token : '.$token.' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_id'				            => $data_post['member_id'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );
                
                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $data_balance = array (
                                    'ppob_agen_id'          => $ppob_agen_id,
                                    'ppob_balance_amount'   => $ppob_balance - $inquiry_data['data']['totalTagihan']
                                );

                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);

                                $data_profitshare = array (
                                    'member_id'                 => $data_post['member_id'],
                                    'savings_account_id'        => $savings_account_id,
                                    'savings_id'                => $savings_id, 
                                    'branch_id'                 => $data_post['branch_id'],
                                    'ppob_profit_share_date'    => date("Y-m-d"),
                                    'ppob_profit_share_amount'  => $settingPrice['setting_price_commission'],
                                    'data_state'                => 0,
                                    'created_id'                => $auth['user_id'],
                                    'created_on'                => date("Y-m-d H:i:s"),
                                );      

                                if($this->AndroidPPOB_model->insertPPOBProfitShare_Company($data_profitshare)){
                                    $data_jurnal = array (
                                        'branch_id'                 => $data_post['branch_id'],
                                        'ppob_company_id'           => $ppob_company_id,
                                        'member_id'                 => $data_post['member_id'],
                                        'member_name'               => $data_post['member_name'],
                                        'product_name'              => $ppobproduct['ppob_product_name'],
                                        'ppob_agen_price'           => $datappob_transaction['ppob_transaction_amount'],
                                        'ppob_company_price'        => $nominal,
                                        'ppob_admin'                => $ppob_transaction_admin_amount,
                                        'ppob_fee'                  => $settingPrice['setting_price_fee'],
                                        'ppob_commission'           => $settingPrice['setting_price_commission'],
                                        'savings_account_id'        => $savings_account_id,
                                        'savings_id'                => $savings_id,
                                        'journal_status'            => 1,
                                    );

                                    $this->journalPPOB($data_jurnal);

                                }
                            }
                
                            $response['error_paymentppobplnprepaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Berhasil";
                            $response['ppob_transaction_remark'] 		= $datappob_transaction['ppob_transaction_remark'];

                        } else {
                            $ppob_transaction_status = 2;

                            $token 	= $this->ccMasking($inquiry_data['data']['tokenNumber']);

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['code'].' - '.$data_inquiry[0]['id_transaksi'].' - '.$data_inquiry[0]['nova'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'			            => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $totalnominal,
                                'ppob_transaction_default_amount'	    => $totalnominal,
                                'ppob_transaction_admin_amount'	        => 0,
                                'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'ppob_transaction_remark'	            => 'ID Pelanggan : '.$data_inquiry[0]['nova'].' - Nama NAMA - Tarif/Daya TARIF/DAYA - Nominal : '.$data_inquiry[0]['nominal'].' - Token TOKEN : '.$inquiry_data['data']['message'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_id'				            => $data_post['member_id'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );
                
                            if ($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);
                            }

                            
                
                            $response['error_paymentppobplnprepaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Gagal";
                            $response['error_msg'] 						= "Gagal";
                            $response['ppob_transaction_remark'] 		= $datappob_transaction['ppob_transaction_remark'];
                        } 
                    }
                }
			}

			echo json_encode($response);
        }


        //BPJS KESEHATAN---------------------------------------------------------------------
        public function getPPOBBPJS(){
            $response = array(
                'error'                         => FALSE,
                'error_msg'                     => "",
                'error_msg_title'               => "",
                'ppobbpjskesehatanproduct'      => "",
            );

            $ppob_agen_id       = $this->input->post('user_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('noVA', true),
                'jumlah_bulan'  => $this->input->post('jmlBulan', true),
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-bpjs/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);

            $inquiry_data       = json_decode(apiTrans($data), true);

            if($inquiry_data['code'] == 200){
				$ppobbpjskesehatanproduct[0]['noVA']			= $inquiry_data['data']['nova'];
				$ppobbpjskesehatanproduct[0]['nama']			= $inquiry_data['data']['nama'];
				$ppobbpjskesehatanproduct[0]['namaCabang']		= $inquiry_data['data']['namaCabang'];
				$ppobbpjskesehatanproduct[0]['jumlahPeriode']	= $inquiry_data['data']['jumlahPeriode'];
				$ppobbpjskesehatanproduct[0]['jumlahPeserta']	= $inquiry_data['data']['jumlahPeserta'];
				$ppobbpjskesehatanproduct[0]['nilaiTagihan']	= $inquiry_data['data']['tagihan'];
				$ppobbpjskesehatanproduct[0]['adminTagihan']	= $inquiry_data['data']['admin'];
				$ppobbpjskesehatanproduct[0]['totalTagihan']	= $inquiry_data['data']['total'];
				$ppobbpjskesehatanproduct[0]['refID']			= $inquiry_data['data']['refID'];
	
				$detailPeserta = $inquiry_data['data']['detailPeserta'];
				
				foreach($detailPeserta as $key => $val){
					$ppobbpjskesehatanpeserta[$key]['noPeserta']		= $val['noPeserta'];
					$ppobbpjskesehatanpeserta[$key]['namaPeserta']		= $val['nama'];
					$ppobbpjskesehatanpeserta[$key]['premiPeserta']		= $val['premi'];
					$ppobbpjskesehatanpeserta[$key]['saldoPeserta']		= $val['saldo'];
				}
	
				$response['error'] 							= FALSE;
				$response['error_msg_title'] 				= "Success";
				$response['error_msg'] 						= "Data Exist";
				$response['ppob_balance'] 					= $ppob_balance;
				$response['ppobbpjskesehatanproduct'] 		= $ppobbpjskesehatanproduct;
				$response['ppobbpjskesehatanpeserta'] 		= $ppobbpjskesehatanpeserta;
                $response['id_transaksi']                   = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);
        }

        public function paymentPPOBBPJS(){
            $auth = $this->session->userdata('auth');

            $response = array(
				'error'								=> FALSE,
				'error_paymentppobbpjskesehatan'	=> FALSE,
				'error_msg_title'		            => "",
				'error_msg'			                => "",
			);

            $data_post = array (
                'member_id'             => $this->input->post('member_id', true),
                'member_name'           => $this->input->post('member_name', true),
                'totalTagihan'          => $this->input->post('totalTagihan',true),
                'nova'                  => $this->input->post('noVA', true),
                'jumlah_bulan'          => $this->input->post('jmlBulan', true),
                'id_transaksi'          => $this->input->post('id_transaksi', true),
                'branch_id'             => $this->input->post('branch_id', true),
                'savings_id'            => $this->input->post('savings_id', true),
                'savings_account_id'    => $this->input->post('savings_account_id', true),
            );

			$ppob_product_code 			= 'BPJSKES';

			$ppob_agen_id				= $data_post['member_id'];

			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			

			$totalTagihan 				= $data_post['totalTagihan'];;

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }

            /* Saldo Dana PPOB madani */
            $database 					= $this->db->database;
			$ppob_company_id		    = $this->AndroidPPOB_model->getPPOBCompanyID($database);
            $ppob_balance_company       = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);
            if(empty($ppob_balance_company)){
                $ppob_balance_company   = 0;
            }
            
            /* Saldo Simpanan Anggota */
            $ppobbalance                = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);
            $ppob_balance               = $ppobbalance['savings_account_last_balance'];
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            /* Saldo Dana PPOB Cabang */
            $topup_branch_balance           = $this->AndroidPPOB_model->getTopupBranchBalance($data_post['branch_id']);
            
            if(empty($topup_branch_balance)){
                $topup_branch_balance   = 0;
            }

			if($ppob_balance < $totalTagihan){
				$response['error_paymentppobbpjskesehatan'] 	= TRUE;
				$response['error_msg_title'] 					= "Transaksi Gagal";
				$response['ppob_transaction_remark'] 			= "Saldo Anda tidak mencukupi";

			} else {
                if($topup_branch_balance < $totalTagihan){
                    $response['error_paymentppobbpjskesehatan'] 	= TRUE;
                    $response['error_msg_title'] 					= "Transaksi Gagal";
                    $response['ppob_transaction_remark'] 			= "Dana PPOB Cabang tidak mencukupi";
                } else {
                    if($ppob_balance_company < $totalTagihan){
                        $response['error_paymentppobbpjskesehatan'] 	= TRUE;
                        $response['error_msg_title'] 					= "Transaksi Gagal";
                        $response['ppob_transaction_remark'] 			= "Dana PPOB tidak mencukupi";
                    } else {
                        $data_inquiry[0] = array (
                            'nova'          => $data_post['nova'],
                            'jumlah_bulan'  => $data_post['jumlah_bulan'],
                            'id_transaksi'  => $data_post['id_transaksi'],
                        );
                        
                        $data               = array();

                        $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-bpjs/payment';
                        $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                        $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                        $data['content']    = json_encode($data_inquiry);

                        $inquiry_data       = json_decode(apiTrans($data), true);

                        $settingPrice       = $this->SettingPrice_model->getPPOBSettingPrice_Code('BPJSKES');

                        /* PPOB TRANSACTION BARU */

                        if($inquiry_data['code'] == 200){

                            $ppob_transaction_status = 1;

                            $totalTagihan   = $inquiry_data['data']['totalTagihan'];
                            $tagihan        = $inquiry_data['data']['tagihan'];
                            $admin          = $inquiry_data['data']['admin'];

                            $ppob_transaction_admin_amount              = $admin - ($settingPrice['setting_price_fee' ] + $settingPrice['setting_price_commission']);
                            $ppob_transaction_company_amount            = $ppob_transaction_admin_amount;

                            $ppob_transaction_amount                    = $tagihan + $ppob_transaction_admin_amount;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['data']['noReferensi'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $ppob_transaction_amount,
                                'ppob_transaction_default_amount'	    => $totalTagihan,
                                'ppob_transaction_admin_amount'	        => $ppob_transaction_admin_amount,
                                'ppob_transaction_company_amount'	    => $ppob_transaction_admin_amount,
                                'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'ppob_transaction_remark'	            => 'No. VA : '.$inquiry_data['data']['nova'].' - Nama : '.$inquiry_data['data']['namaPengguna'].' - Jumlah Peserta : '.$inquiry_data['data']['jumlahPeserta'].' - Jumlah Periode : '.$inquiry_data['data']['jumlahPeriode'].' - No. Referensi : '.$inquiry_data['data']['noReferensi'].' - ID Transaksi : '.$data_post['id_transaksi'],
                                'created_id'				            => $data_post['member_id'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );

                        
                    
                            if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                                $data_balance = array (
                                    'ppob_agen_id'          => $ppob_agen_id,
                                    'ppob_balance_amount'   => $ppob_balance - $inquiry_data['data']['totalTagihan']
                                );

                                $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);

                                $data_profitshare = array (
                                    'member_id'                 => $data_post['member_id'],
                                    'savings_account_id'        => $data_post['savings_account_id'],
                                    'savings_id'                => $data_post['savings_id'], 
                                    'branch_id'                 => $data_post['branch_id'],
                                    'ppob_profit_share_date'    => date("Y-m-d"),
                                    'ppob_profit_share_amount'  => $settingPrice['setting_price_commission'],
                                    'data_state'                => 0,
                                    'created_id'                => $auth['user_id'],
                                    'created_on'                => date("Y-m-d H:i:s"),
                                );      

                                if($this->AndroidPPOB_model->insertPPOBProfitShare_Company($data_profitshare)){
                                    $data_jurnal = array (
                                        'branch_id'                 => $data_post['branch_id'],
                                        'ppob_company_id'           => $ppob_company_id,
                                        'member_id'                 => $data_post['member_id'],
                                        'member_name'               => $data_post['member_name'],
                                        'product_name'              => $ppobproduct['ppob_product_name'],
                                        'ppob_agen_price'           => $datappob_transaction['ppob_transaction_amount'],
                                        'ppob_company_price'        => $tagihan,
                                        'ppob_admin'                => $ppob_transaction_admin_amount,
                                        'ppob_fee'                  => $settingPrice['setting_price_fee'],
                                        'ppob_commission'           => $settingPrice['setting_price_commission'],
                                        'savings_account_id'        => $data_post['savings_account_id'],
                                        'savings_id'                => $data_post['savings_id'],
                                        'journal_status'            => 1,
                                    );

                                    $this->journalPPOB($data_jurnal);

                                }
                            }

                
                            $response['error_paymentppobplnpostpaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Berhasil";
                            $response['ppob_transaction_remark'] 		= $datappob_transaction['ppob_transaction_remark'];
                        } else {
                            $ppob_transaction_status = 2;

                            $datappob_transaction = array (
                                'ppob_unique_code'			            => $inquiry_data['code'].' - '.$data_inquiry[0]['id_transaksi'].' - '.$data_inquiry[0]['nova'],
                                'ppob_company_id'			            => $ppob_company_id,
                                'ppob_agen_id'				            => $data_post['member_id'],
                                'ppob_agen_name'			            => $data_post['member_name'],
                                'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                                'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                                'member_id'				                => $data_post['member_id'],
                                'savings_account_id'		            => $data_post['savings_account_id'],
                                'savings_id'			                => $data_post['savings_id'],
                                'branch_id'			                    => $data_post['branch_id'],
                                'transaction_id'	                    => $data_post['id_transaksi'],
                                'ppob_transaction_amount'	            => $totalTagihan,
                                'ppob_transaction_default_amount'	    => $totalTagihan,
                                'ppob_transaction_admin_amount'	        => 0,
                                'ppob_transaction_fee_amount'	        => $settingPrice['setting_price_fee'],
                                'ppob_transaction_commission_amount'	=> $settingPrice['setting_price_commission'],
                                'ppob_transaction_date'		            => date('Y-m-d'),
                                'ppob_transaction_status'	            => $ppob_transaction_status,
                                'ppob_transaction_remark'	            => 'No. VA : '.$data_inquiry[0]['nova'].' - Nama NAMA Jumlah Peserta JUMLAH PESERTA Jumlah Periode : '.$data_inquiry[0]['jumlah_bulan'].' - ID Transaksi '.$data_post['id_transaksi'],
                                'created_id'				            => $data_post['member_id'],
                                'created_on'				            => date('Y-m-d H:i:s')
                            );

                            $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
                
                            $response['error_paymentppobplnpostpaid'] 	= FALSE;
                            $response['error_msg_title'] 				= "Transaksi Gagal";
                            $response['ppob_transaction_status'] 		= $datappob_transaction['ppob_transaction_status'];	
                        }


                        /* PPOB TRANSACTION LAMA */
                        
                    }
                }
			}

			echo json_encode($response);
        }




























        //PULSA PASCABAYAR---------------------------------------------------------------------------
        public function getPPOBPulsaPascabayarProduk(){
            $response = array(
				'error'							=> FALSE,
				'error_msg'						=> "",
				'error_msg_title'				=> "",
				'ppobpulsapascabayar'			=> "",
            );
            
			$ppobpulsapascabayar[0]['ppob_product_category_id']	    = 33;
			$ppobpulsapascabayar[0]['ppob_product_category_name']	= 'Matrix';
			$ppobpulsapascabayar[0]['ppob_product_category_code']	= 'MATRIX';
			$ppobpulsapascabayar[1]['ppob_product_category_id']	    = 33;
			$ppobpulsapascabayar[1]['ppob_product_category_name']	= 'Three Postpaid';
			$ppobpulsapascabayar[1]['ppob_product_category_code']   = 'THREEPOSTP';
			$ppobpulsapascabayar[2]['ppob_product_category_id']	    = 33;
			$ppobpulsapascabayar[2]['ppob_product_category_name']	= 'XL XPlor';
			$ppobpulsapascabayar[2]['ppob_product_category_code']	= 'XPLOR';
			$ppobpulsapascabayar[3]['ppob_product_category_id']	    = 33;
			$ppobpulsapascabayar[3]['ppob_product_category_name']	= 'Smartfren Postpaid';
			$ppobpulsapascabayar[3]['ppob_product_category_code']   = 'SMFPOSTP';
		
		
			
			$response['error'] 						= FALSE;
			$response['error_msg_title'] 			= "Success";
			$response['error_msg'] 					= "Data Exist";
			$response['ppobpulsapascabayar'] 		= $ppobpulsapascabayar;

			echo json_encode($response);
        }

        public function getPPOBPulsaPascabayar(){
            $response = array(
                'error'                         => FALSE,
                'error_msg'                     => "",
                'error_msg_title'               => "",
                'ppobpulsapascabayarbill'      => "",
            );

            $ppob_agen_id       = $this->input->post('member_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'product_id'    => $this->input->post('product_id', true),
                'nova'          => $this->input->post('phone_number', true),
            );

            // $data_inquiry[0]    = array (
            //     'product_id'    => 'MATRIX',
            //     'nova'          => '081601239001',
            // );


            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-pascabayar/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            if($inquiry_data['code'] == 200){
                $ppobpulsapascabayarbill[0]['refID']              = $inquiry_data['data']['refID'];
                $ppobpulsapascabayarbill[0]['id_pelanggan']       = $inquiry_data['data']['nova'];
                $ppobpulsapascabayarbill[0]['nama']               = $inquiry_data['data']['namaPengguna'];
                $ppobpulsapascabayarbill[0]['periode']            = $inquiry_data['data']['periode'];
                $ppobpulsapascabayarbill[0]['jumlahTagihan']      = $inquiry_data['data']['jumlahTagihan'];
                $ppobpulsapascabayarbill[0]['tagihan']            = $inquiry_data['data']['tagihan'];
                $ppobpulsapascabayarbill[0]['admin']              = $inquiry_data['data']['admin'];
                $ppobpulsapascabayarbill[0]['totalTagihan']       = $inquiry_data['data']['totalTagihan'];

                $response['error']                      = FALSE;
                $response['error_msg_title']            = "Success";
                $response['error_title']                = "Data Exist";
                $response['ppob_balance']               = $ppob_balance;
                $response['ppobpulsapascabayarbill']    = $ppobpulsapascabayarbill;
                $response['id_transaksi']               = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);
        }

        public function paymentPPOBPulsaPascabayar(){
            $response = array(
				'error'									=> FALSE,
				'error_paymentppobpulsapascabayar'	    => FALSE,
				'error_msg_title'	                    => "",
				'error_msg'			                    => "",
			);

			$ppob_product_code 			= $this->input->post('productCode', true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance               = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 	= 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobpulsapascabayar'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {

                $data_inquiry[0] = array (
                    'product_id' 	=> $this->input->post('productCode', true),
                    'nova' 		    => $this->input->post('phone_number', true),
                    'id_transaksi' 	=> $this->input->post('id_transaksi', true)
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-pascabayar/payment';
                $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);
	
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['refID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$inquiry_data['data']['nova'].' Nama '.$inquiry_data['data']['namaPengguna'].' Periode '.$inquiry_data['data']['periode'].' No. Ref '.$inquiry_data['data']['refID'].' Jumlah Tagihan '.$inquiry_data['data']['jumlahTagihan'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $totaltagihan
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $totaltagihan,
                                'ppob_company_price'    => $totaltagihan - $inquiry_data['data']['fee'],
                                'ppob_fee'              => $inquiry_data['data']['fee']
                            );

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
                    $response['error_paymentppobpulsapascabayar'] 	= FALSE;
                    $response['error_msg_title'] 					= "Success";
                    $response['error_msg'] 							= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['refID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$inquiry_data['data']['nova'].' Nama '.$inquiry_data['data']['namaPengguna'].' Periode '.$inquiry_data['data']['periode'].' No. Ref '.$inquiry_data['data']['refID'].' Jumlah Tagihan '.$inquiry_data['data']['jumlahTagihan'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymentppobpulsapascabayar'] 	= FALSE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['error_msg'] 							= "Gagal Transaksi";
                }
                 
			}

			echo json_encode($response);
        }





        //TELKOM---------------------------------------------------------------------------
        public function getPPOBTopUpTelkomProduk(){
            $response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobtopuptelkom'			=> "",
            );
            
			$ppobtopuptelkom[0]['ppob_product_category_id']	    = 35;
			$ppobtopuptelkom[0]['ppob_product_category_name']	= 'TELKOM PSTN';
			$ppobtopuptelkom[0]['ppob_product_category_code']	= 'TELKOMPSTN';
			$ppobtopuptelkom[1]['ppob_product_category_id']	    = 35;
			$ppobtopuptelkom[1]['ppob_product_category_name']	= 'Telkom Speedy';
			$ppobtopuptelkom[1]['ppob_product_category_code']   = 'TELKOMSPEEDY';
			$ppobtopuptelkom[2]['ppob_product_category_id']	    = 35;
			$ppobtopuptelkom[2]['ppob_product_category_name']	= 'Telkom Flexi';
			$ppobtopuptelkom[2]['ppob_product_category_code']	= 'TELKOMFLEXI';
			$ppobtopuptelkom[3]['ppob_product_category_id']	    = 35;
			$ppobtopuptelkom[3]['ppob_product_category_name']	= 'Telkomsel Halo';
			$ppobtopuptelkom[3]['ppob_product_category_code']   = 'TELKOMSELLHALO';
		
		
			
			$response['error'] 					= FALSE;
			$response['error_msg_title'] 		= "Success";
			$response['error_msg'] 				= "Data Exist";
			$response['ppobtopuptelkom'] 		= $ppobtopuptelkom;

			echo json_encode($response);
        }

        public function getPPOBTelkom(){
            $response = array(
                'error'                         => FALSE,
                'error_msg'                     => "",
                'error_msg_title'               => "",
                'ppobpulsatelkomdata'           => "",
            );

            $ppob_agen_id       = $this->input->post('user_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'productCode'   => $this->input->post('productCode', true),
                'nova'          => $this->input->post('phone_number', true),
            );

            // $data_inquiry[0]    = array (
            //     'product_id'    => 'MATRIX',
            //     'nova'          => '081601239001',
            // );


            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-telkom/inquiry';
            $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
            $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            if($inquiry_data['code'] == 200){
                $ppobpulsatelkomdata[0]['id_pelanggan']		= $inquiry_data['data']['idpel'];
				$ppobpulsatelkomdata[0]['nama']				= $inquiry_data['data']['nama'];
				$ppobpulsatelkomdata[0]['kodeArea']			= $inquiry_data['data']['kodeArea'];
				$ppobpulsatelkomdata[0]['jumlahTagihan']	= $inquiry_data['data']['jumlahTagihan'];
				$ppobpulsatelkomdata[0]['divre']			= $inquiry_data['data']['divre'];
				$ppobpulsatelkomdata[0]['totalTagihan']		= $inquiry_data['data']['totalTagihan'];
				$ppobpulsatelkomdata[0]['refID']			= $inquiry_data['data']['refID'];

				$detailtagihan = $inquiry_data['data']['tagihan'];

				foreach($detailtagihan as $k => $v){
					$ppobpulsatelkombill[$k]['periodeTagihanTelkom']	= $v['periode'];
					$ppobpulsatelkombill[$k]['nilaiTagihanTelkom']		= $v['nilaiTagihan'];
					$ppobpulsatelkombill[$k]['adminTagihanTelkom']		= $v['admin'];
					$ppobpulsatelkombill[$k]['totalTagihanTelkom']		= $v['total'];
					$ppobpulsatelkombill[$k]['feeTagihanTelkom']		= $v['fee'];
				}

                $response['error']                      = FALSE;
                $response['error_msg_title']            = "Success";
                $response['error_title']                = "Data Exist";
                $response['ppob_balance']               = $ppob_balance;
                $response['ppobpulsatelkomdata']        = $ppobpulsatelkomdata;
                $response['ppobpulsatelkombill']        = $ppobpulsatelkombill;
                $response['id_transaksi']               = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);
        }

        public function paymentPPOBTelkom(){
            $response = array(
				'error'									=> FALSE,
				'error_paymentppobpulsatelkom'	        => FALSE,
				'error_msg_title'	                    => "",
				'error_msg'			                    => "",
			);

			$ppob_product_code 			= $this->input->post('productCode', true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance               = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 	= 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobpulsatelkom'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {

                $data_inquiry[0] = array (
                    'productCode' 	=> $this->input->post('productCode', true),
                    'nova' 		    => $this->input->post('phone_number', true),
                    'id_transaksi' 	=> $this->input->post('id_transaksi', true)
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-telkom/payment';
                $data['apikey']     = '$2y$10$pIWLU8/X0m4GGTdkhWAaJOt/ivDhhmyH64kOq//0sbRCgbk8Gw71q';
                $data['secretkey']  = '$2y$10$vswAf9Tq78bbCCSf0Q99EuHzV5K67xGzGfJUS0Ld51XhJMKNMCvym';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);
	
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['refID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$inquiry_data['data']['idpel'].' Nama '.$inquiry_data['data']['nama'].' No. Ref '.$inquiry_data['data']['refID'].' Jumlah Tagihan '.$inquiry_data['data']['jumlahTagihan'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $totaltagihan
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $totaltagihan,
                                'ppob_company_price'    => $totaltagihan - $inquiry_data['data']['fee'],
                                'ppob_fee'              => $inquiry_data['data']['fee']
                            );

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
                    $response['error_paymentppobpulsatelkom'] 	= FALSE;
                    $response['error_msg_title'] 				= "Success";
                    $response['error_msg'] 						= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['refID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'Nomor Pelanggan '.$inquiry_data['data']['idpel'].' Nama '.$inquiry_data['data']['nama'].' No. Ref '.$inquiry_data['data']['refID'].' Jumlah Tagihan '.$inquiry_data['data']['jumlahTagihan'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymentppobpulsatelkom'] 	= FALSE;
                    $response['error_msg_title'] 				= "Confirm";
                    $response['error_msg'] 						= "Gagal Transaksi";
                }
                 
			}

			echo json_encode($response);
        }





       






        






        






        




        







        //JURNAL---------------------------------------------------------------------------
        public function journalPPOB($data){
            /* SAVINGS TRANSFER FROM */

            $preferenceppob 			= $this->AndroidPPOB_model->getPreferencePpob();

            $data_transfermutation = array(
				'branch_id'								=> $data['branch_id'],
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $data['ppob_agen_price'],
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $data['member_name'],
				'created_id'							=> $data['member_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

            if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutation)){
                $transaction_module_code 	        = "TRPPOB";
                $transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
                $savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutation['created_on']);
                $preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


                /* SIMPAN DATA TRANSFER FROM */

                $ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

                $savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

                $datafrom = array (
                    'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
                    'savings_account_id'						=> $data['savings_account_id'],
                    'savings_id'								=> $data['savings_id'],
                    'member_id'									=> $data['member_id'],
                    'branch_id'									=> $data['branch_id'],
                    'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
                    'savings_account_opening_balance'			=> $savings_account_opening_balance,
                    'savings_transfer_mutation_from_amount'		=> $data['ppob_agen_price'],
                    'savings_account_last_balance'				=> $savings_account_opening_balance - $data['ppob_agen_price'],
                );

                $member_name = $data['member_name'];

                if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){   
                    $acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutation['created_id']);
							
                    $journal_voucher_period 	= date("Ym", strtotime($data_transfermutation['savings_transfer_mutation_date']));
                    
                    $data_journal = array(
                        'branch_id'						=> $data_transfermutation['branch_id'],
                        'journal_voucher_period' 		=> $journal_voucher_period,
                        'journal_voucher_date'			=> date('Y-m-d'),
                        'journal_voucher_title'			=> 'TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
                        'journal_voucher_description'	=> 'TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
                        'transaction_module_id'			=> $transaction_module_id,
                        'transaction_module_code'		=> $transaction_module_code,
                        'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
                        'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
                        'created_id' 					=> $data_transfermutationfrom['created_id'],
                        'created_on' 					=> $data_transfermutationfrom['created_on'],
                    );
                    
                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

                    $journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfrom['created_id']);

                    
                    /* SIMPAN DATA JOURNAL DEBIT */
                    $account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

                    $account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

                    $data_debit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $account_id,
                        'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
                        'journal_voucher_debit_amount'	=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
                        'account_id_status'				=> 0,
                    );

                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


                    /* SIMPAN DATA JOURNAL CREDIT */
                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

                    if ($data['ppob_admin'] > 0){
                        $ppob_company_price             = $data['ppob_company_price'];
                        $ppob_admin                     = $data['ppob_admin'];
                        $journal_voucher_amount         = $ppob_company_price + $ppob_admin;
                    } else {
                        $journal_voucher_amount         = $data['ppob_company_price'];
                    }

                    $data_credit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_down_payment'],
                        'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $journal_voucher_amount,
                        'journal_voucher_credit_amount'	=> $journal_voucher_amount,
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);

                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

                    $data_credit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_income'],
                        'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data['ppob_fee'] + $data['ppob_commission'],
                        'journal_voucher_credit_amount'	=> $data['ppob_fee'] + $data['ppob_commission'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);

                    /* SERVER PPOB */
                    /* if ($data['ppob_admin'] > 0){
                        $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_cost']);

                        $data_credit = array(
                            'journal_voucher_id'			=> $journal_voucher_id,
                            'account_id'					=> $preferenceppob['ppob_account_cost'],
                            'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                            'journal_voucher_amount'		=> $data['ppob_admin'],
                            'journal_voucher_credit_amount'	=> $data['ppob_admin'],
                            'account_id_default_status'		=> $account_id_default_status,
                            'account_id_status'				=> 1,
                        );

                        $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);
                    } */
                }

            }


            /* SAVINGS TRANSFER TO */

            $data_transfermutationto = array(
				'branch_id'								=> $data['branch_id'],
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $data['ppob_commission'],
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $data['member_name'],
				'created_id'							=> $data['member_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

            if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationto)){
                $transaction_module_code 	        = "PSPPOB";
                $transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
                $savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationto['created_on']);
                $preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

                /* SIMPAN DATA TRANSFER TO */
                $ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

                $savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

                $datato = array (
                    'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
                    'savings_account_id'						=> $data['savings_account_id'],
                    'savings_id'								=> $data['savings_id'],
                    'member_id'									=> $data['member_id'],
                    'branch_id'									=> $data['branch_id'],
                    'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
                    'savings_account_opening_balance'			=> $savings_account_opening_balance,
                    'savings_transfer_mutation_to_amount'		=> $data['ppob_commission'],
                    'savings_account_last_balance'				=> $savings_account_opening_balance + $data['ppob_commission'],
                );

                $member_name = $data['member_name'];

                if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){
                    $acctsavingstr_last 		= $this->AndroidPPOB_model->getAcctSavingsTransferMutationTo_Last($data_transfermutationto['created_id']);
							
                    $journal_voucher_period 	= date("Ym", strtotime($data_transfermutationto['savings_transfer_mutation_date']));
                    
                    $data_journal = array(
                        'branch_id'						=> $data_transfermutationto['branch_id'],
                        'journal_voucher_period' 		=> $journal_voucher_period,
                        'journal_voucher_date'			=> date('Y-m-d'),
                        'journal_voucher_title'			=> 'BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
                        'journal_voucher_description'	=> 'BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
                        'transaction_module_id'			=> $transaction_module_id,
                        'transaction_module_code'		=> $transaction_module_code,
                        'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
                        'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
                        'created_id' 					=> $data_transfermutationto['created_id'],
                        'created_on' 					=> $data_transfermutationto['created_on'],
                    );
                    
                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

                    $journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationto['created_id']);


                    /* SIMPAN DATA JOURNAL DEBIT */

                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

                    $data_debit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_income'],
                        'journal_voucher_description'	=> 'Bagi Hasil PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'journal_voucher_debit_amount'	=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 0,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);


                    //----- Simpan data jurnal kredit
                    $account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

                    $account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

                    $data_credit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $account_id,
                        'journal_voucher_description'	=> 'Bagi Hasil PPOB '.$member_name,
                        'journal_voucher_amount'		=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'journal_voucher_credit_amount'	=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
                }          
            }



            /* SIMPAN TRANSFER FROM FEE BASE PPOB */
            $data_transfermutationfromfeebase = array(
				'branch_id'								=> $data['branch_id'],
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $preferenceppob['ppob_mbayar_admin'],
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $data['member_name'],
				'created_id'							=> $data['member_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

            if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfromfeebase)){
                $transaction_module_code 	        = "FBPPOB";
                $transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
                $savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationfromfeebase['created_on']);
                $preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


                /* SIMPAN DATA TRANSFER FROM */

                $ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

                $savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

                $datafrom = array (
                    'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
                    'savings_account_id'						=> $data['savings_account_id'],
                    'savings_id'								=> $data['savings_id'],
                    'member_id'									=> $data['member_id'],
                    'branch_id'									=> $data['branch_id'],
                    'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
                    'savings_account_opening_balance'			=> $savings_account_opening_balance,
                    'savings_transfer_mutation_from_amount'		=> $preferenceppob['ppob_mbayar_admin'],
                    'savings_account_last_balance'				=> $savings_account_opening_balance - $preferenceppob['ppob_mbayar_admin'],
                );

                $member_name = $data['member_name'];

                if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){   
                    $acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutationfromfeebase['created_id']);
							
                    $journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfromfeebase['savings_transfer_mutation_date']));
                    
                    $data_journal = array(
                        'branch_id'						=> $data_transfermutationfromfeebase['branch_id'],
                        'journal_voucher_period' 		=> $journal_voucher_period,
                        'journal_voucher_date'			=> date('Y-m-d'),
                        'journal_voucher_title'			=> 'FEE BASE PPOB '.$acctsavingstr_last['member_name'],
                        'journal_voucher_description'	=> 'FEE BASE PPOB '.$acctsavingstr_last['member_name'],
                        'transaction_module_id'			=> $transaction_module_id,
                        'transaction_module_code'		=> $transaction_module_code,
                        'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
                        'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
                        'created_id' 					=> $data_transfermutationfromfeebase['created_id'],
                        'created_on' 					=> $data_transfermutationfromfeebase['created_on'],
                    );
                    
                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

                    $journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfromfeebase['created_id']);

                    
                    /* SIMPAN DATA JOURNAL DEBIT */
                    $account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

                    $account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

                    $data_debit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $account_id,
                        'journal_voucher_description'	=> 'Fee Base PPOB '.$member_name,
                        'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
                        'journal_voucher_debit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 0,
                    );

                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


                    /* SIMPAN DATA JOURNAL DEBIT */

                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

                    $data_credit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_income'],
                        'journal_voucher_description'	=> 'Fee Base PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
                        'journal_voucher_credit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);
                }

            }

            return; 
        }

         //JURNAL---------------------------------------------------------------------------
         public function refundJournalPPOB(){
            /* SAVINGS TRANSFER TO */

				$member_name    = $this->AcctSavingsTransferMutation_model->getMemberName($this->input->post('member_id',true));
				$ppobproduct    = $this->AndroidPPOB_model->getPPOBProduct_DetailByID($this->input->post('ppob_product_id',true));

				if($ppobproduct['ppob_product_category_id'] == 33 || $ppobproduct['ppob_product_category_id'] == 35){
					//PULSA SAMA TElKOM
					$data = array (
						'branch_id'             => $this->input->post('branch_id', true),
						'ppob_company_id'       => $this->input->post('ppob_company_id',true),
						'member_id'             => $this->input->post('member_id', true),
						'member_name'           => $member_name,
						'product_name'          => $ppobproduct['ppob_product_name'],
						'ppob_agen_price'       => $this->input->post('ppob_transaction_amount',true),
						'ppob_company_price'    => $this->input->post('ppob_transaction_default_amount',true),
						'ppob_fee'              => $this->input->post('ppob_transaction_fee_amount',true),
					);
				}else{
					$data           = array (
						'branch_id'                     => $this->input->post('branch_id',true),
						'ppob_company_id'               => $this->input->post('ppob_company_id',true),
						'member_id'                     => $this->input->post('member_id',true),
						'member_name'                   => $member_name,
						'product_name'                  => $ppobproduct['ppob_product_name'],
						'ppob_agen_price'               => $this->input->post('ppob_transaction_amount',true),
						'ppob_company_price'            => $this->input->post('ppob_transaction_default_amount',true),
						'ppob_admin'                    => 0,
						'ppob_fee'                      => $this->input->post('ppob_transaction_fee_amount',true),
						'ppob_commission'               => $this->input->post('ppob_transaction_commission_amount',true),
						'savings_account_id'            => $this->input->post('savings_account_id',true),
						'savings_id'                    => $this->input->post('savings_id',true),
						'journal_status'                => 1,
					);
				}

				$preferenceppob 			= $this->AndroidPPOB_model->getPreferencePpob();

				$data_transfermutationto = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $data['ppob_agen_price'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationto)){
					$transaction_module_code 	        = "TRPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationto['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


					/* SIMPAN DATA TRANSFER TO */

					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datato = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_to_amount'		=> $data['ppob_agen_price'],
						'savings_account_last_balance'				=> $savings_account_opening_balance + $data['ppob_agen_price'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){   
						$acctsavingstr_last 		= $this->AndroidPPOB_model->getAcctSavingsTransferMutationTo_Last($data_transfermutationto['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationto['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationto['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND TRANSAKSI PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationto['created_id'],
							'created_on' 					=> $data_transfermutationto['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationto['created_id']);


						/* SIMPAN DATA JOURNAL DEBIT */
						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

						if ($data['ppob_admin'] > 0){
							$ppob_company_price             = $data['ppob_company_price'];
							$ppob_admin                     = $data['ppob_admin'];
							$journal_voucher_amount         = $ppob_company_price + $ppob_admin;
						} else {
							$journal_voucher_amount         = $data['ppob_company_price'];
						}

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_down_payment'],
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $journal_voucher_amount,
							'journal_voucher_debit_amount'	=> $journal_voucher_amount,
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data['ppob_fee'] + $data['ppob_commission'],
							'journal_voucher_debit_amount'	=> $data['ppob_fee'] + $data['ppob_commission'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						
						/* SIMPAN DATA JOURNAL CREDIT */
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationto['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationto['savings_transfer_mutation_amount'],
							'account_id_status'				=> 1,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);

					}

				}


				/* SAVINGS TRANSFER FROM */

				$data_transfermutationfrom = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $data['ppob_commission'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfrom)){
					$transaction_module_code 	        = "PSPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationfrom['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();

					/* SIMPAN DATA TRANSFER FROM */
					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datafrom = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_from_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_from_amount'		=> $data['ppob_commission'],
						'savings_account_last_balance'				=> $savings_account_opening_balance - $data['ppob_commission'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationFrom($datafrom)){
						$acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutationfrom['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfrom['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationfrom['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND BAGI HASIL PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationfrom['created_id'],
							'created_on' 					=> $data_transfermutationfrom['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfrom['created_id']);


						//----- Simpan data jurnal debit
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datafrom['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Bagi Hasil PPOB '.$member_name,
							'journal_voucher_amount'		=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


						/* SIMPAN DATA JOURNAL KREDIT */

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_kredit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Bagi Hasil PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationfrom['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_kredit);
					}          
				}



				/* SIMPAN TRANSFER FROM FEE BASE PPOB */
				$data_transfermutationfromfeebase = array(
					'branch_id'								=> $data['branch_id'],
					'savings_transfer_mutation_date'		=> date('Y-m-d'),
					'savings_transfer_mutation_amount'		=> $preferenceppob['ppob_mbayar_admin'],
					'savings_transfer_mutation_status'		=> 3,
					'operated_name'							=> $data['member_name'],
					'created_id'							=> $data['member_id'],
					'created_on'							=> date('Y-m-d H:i:s'),
				);

				if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfromfeebase)){
					$transaction_module_code 	        = "FBPPOB";
					$transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
					$savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data_transfermutationfromfeebase['created_on']);
					$preferencecompany 				    = $this->AcctSavingsTransferMutation_model->getPreferenceCompany();


					/* SIMPAN DATA TRANSFER TO */

					$ppobbalance                        = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($data['member_id']);

					$savings_account_opening_balance    = $ppobbalance['savings_account_last_balance'];

					$datato = array (
						'savings_transfer_mutation_id'				=> $savings_transfer_mutation_id,
						'savings_account_id'						=> $data['savings_account_id'],
						'savings_id'								=> $data['savings_id'],
						'member_id'									=> $data['member_id'],
						'branch_id'									=> $data['branch_id'],
						'mutation_id'								=> $preferencecompany['account_savings_transfer_to_id'],
						'savings_account_opening_balance'			=> $savings_account_opening_balance,
						'savings_transfer_mutation_to_amount'		=> $preferenceppob['ppob_mbayar_admin'],
						'savings_account_last_balance'				=> $savings_account_opening_balance + $preferenceppob['ppob_mbayar_admin'],
					);

					$member_name = $data['member_name'];

					if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){   
						$acctsavingstr_last 		= $this->AndroidPPOB_model->getAcctSavingsTransferMutationTo_Last($data_transfermutationfromfeebase['created_id']);
								
						$journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfromfeebase['savings_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $data_transfermutationfromfeebase['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'REFUND FEE BASE PPOB '.$acctsavingstr_last['member_name'],
							'journal_voucher_description'	=> 'REFUND FEE BASE PPOB '.$acctsavingstr_last['member_name'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $acctsavingstr_last['savings_transfer_mutation_id'],
							'transaction_journal_no' 		=> $acctsavingstr_last['savings_account_no'],
							'created_id' 					=> $data_transfermutationfromfeebase['created_id'],
							'created_on' 					=> $data_transfermutationfromfeebase['created_on'],
						);
						
						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfromfeebase['created_id']);


						/* SIMPAN DATA JOURNAL DEBIT */

						$account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $preferenceppob['ppob_account_income'],
							'journal_voucher_description'	=> 'REFUND Fee Base PPOB '.$data['product_name'].' '.$data['member_name'],
							'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'journal_voucher_debit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 0,
						);

						$this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debit);

						
						/* SIMPAN DATA JOURNAL KREDIT */
						$account_id                 = $this->AcctSavingsTransferMutation_model->getAccountID($datato['savings_id']);

						$account_id_default_status  = $this->AcctSavingsTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'REFUND Fee Base PPOB '.$member_name,
							'journal_voucher_amount'		=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'journal_voucher_credit_amount'	=> $data_transfermutationfromfeebase['savings_transfer_mutation_amount'],
							'account_id_default_status'		=> $account_id_default_status,
							'account_id_status'				=> 1,
						);

						$this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
					}

				}

				$ppob_company_last_balance = $this->AndroidPPOB_model->getPPOBCompanyBalance($data['ppob_company_id']);
				$ppob_company_balance = $ppob_company_last_balance + $data['ppob_agen_price'];

				$data_company = array(
					'ppob_company_id'				=> $data['ppob_company_id'],
					'ppob_company_balance'			=> $ppob_company_balance,
				);
				$this->AndroidPPOB_model->updatePPOBCompanyBalance($data_company);

            return; 
        }

    }
        
?>