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
			// $this->load->model('PpobTopupMember_model');
			
			// $this->load->model('PpobPulsaPrabayar_model');
			// $this->load->model('PpobPulsaPascabayar_model');
			// $this->load->model('PpobPaymentTelkomApi_model');
			// $this->load->model('PpobPlnPrepaid_model');
			// $this->load->model('PpobPlnPostpaid_model');
			// $this->load->model('PpobPaymentTopUpApi_model');
			// $this->load->model('PpobPaymentBpjs_model');
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


        //PULSA PRABAYAR---------------------------------------------------------------------------
        public function getPPOBPulsaPrePaid(){
            $response = array(
                'error'                         => FALSE,
                'error_msg'                     => "",
                'error_msg_title'               => "",
                'ppobpulsaprabayarproduct'      => "",
            );

            $ppob_agen_id       = $this->input->post('user_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('phone_number', true),
            );

            $data_inquiry[0]    = array (
                'nova'          => '087839122015',
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-prabayar/inquiry';
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            $settingPrice       = $this->SettingPrice_model->getSettingByType('1');

            /* print_r("inquiry_data ");
            print_r($inquiry_data); */

            if($inquiry_data['code'] == 200){
                $no = 0;
                foreach ($inquiry_data['data'] as $key => $val){
                    $price              = ceil($val['price'] + $settingPrice['setting_price_fee']);
                    $price_commission   = ceil($price + $settingPrice['setting_price_commission']);

                    $ppob_product_price = ceil($val['price'] + $settingPrice['setting_price_fee'] + $settingPrice['setting_price_commission']);

                    $ppobpulsaprabayarproduct[$no]['ppob_product_code']             = $val['product_id'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_name']             = $val['voucher'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_type']             = $val['voucher'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_cost']             = $val['nominal'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_price']            = $ppob_product_price;
                    $ppobpulsaprabayarproduct[$no]['ppob_product_fee']              = $settingPrice['setting_price_fee'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_commission']       = $settingPrice['setting_price_commission'];
                    $ppobpulsaprabayarproduct[$no]['ppob_product_default_price']    = $val['price'];

                    $no++;
                }

                $response['error']                      = FALSE;
                $response['error_msg_title']            = "Success";
                $response['error_title']                = "Data Exist";
                $response['ppob_balance']               = $ppob_balance;
                $response['ppobpulsaprabayarproduct']   = $ppobpulsaprabayarproduct;
                $response['id_transaksi']               = $inquiry_data['id_transaksi'];
            } else {
                $response['error']                      = TRUE;
                $response['error_msg_title']            = "Confrim";
                $response['error_title']                = "Data Kosong";
                $response['ppob_balance']               = $ppob_balance;
            }

            
			echo json_encode($response);

        }

        public function paymentPPOBPulsaPrabayar(){
            $response = array(
				'error'                             => FALSE,
				'error_paymentppobpulsaprabayar'	=> FALSE,
				'error_msg_title'		            => "",
				'error_msg'			                => "",
			);


            $data_post = array (
                'productID'                 => $this->input->post('productID',true), 
                'productPrice'              => $this->input->post('productPrice',true),
                'productDefaultPrice'       => $this->input->post('productDefaultPrice',true),
                'ppob_product_fee'          => $this->input->post('ppob_product_fee',true),
                'ppob_product_commission'   => $this->input->post('ppob_product_commission',true),
                'member_id'                 => $this->input->post('member_id',true),
                'member_name'               => $this->input->post('member_name',true),
                'phone_number'              => $this->input->post('phone_number',true),
                'id_transaksi'              => $this->input->post('id_transaksi',true),
                'branch_id'                 => $this->input->post('branch_id',true),
                'savings_account_id'        => $this->input->post('savings_account_id',true),
                'savings_id'                => $this->input->post('savings_id',true),
            );

            /* productID=P118325&productPrice=6160&productDefaultPrice=5910&ppob_product_fee=5910&ppob_product_commission=5910&member_id=32887&member_name=NURKHOLISON%2C%20SE&phone_number=087838921292&id_transaksi=393&branch_id=2&savings_account_id=31016&savings_id=3 */

            /* $data_post = array (
                'productID'                 => 'P118325', 
                'productPrice'              => 6160,
                'productDefaultPrice'       => 5910,
                'ppob_product_fee'          => 5910,
                'ppob_product_commission'   => 5910,
                'member_id'                 => 32887,
                'member_name'               => 'NURKHOLISON',
                'phone_number'              => '087838921292',
                'id_transaksi'              => 393,
                'branch_id'                 => 2,
                'savings_account_id'        => 31016,
                'savings_id'                => 3,
            ); */

            $ppob_product_code 			    = $data_post['productID'];
            
            $database 					    = $this->db->database;

			$ppob_company_id			    = $this->AndroidPPOB_model->getPPOBCompanyID($database);

            $ppob_balance_company           = $this->AndroidPPOB_model->getPPOBCompanyBalance($ppob_company_id);

			$ppob_agen_id				    = $data_post['member_id'];

			$ppobproduct 				    = $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppobbalance                    = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($ppob_agen_id);

            $ppob_balance                   = $ppobbalance['savings_account_last_balance'];

            $ppob_product_price 		    = $data_post['productPrice'];
            
			$ppob_product_default_price     = $data_post['productDefaultPrice'];

            $ppob_product_fee               = $data_post['ppob_product_fee'];

            $ppob_product_commission        = $data_post['ppob_product_commission'];

            $savings_account_id             = $data_post['savings_account_id'];

            $savings_id                     = $data_post['savings_id'];

			if($ppob_agen_id == null){
				$ppob_agen_id 			    = 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

			if($ppob_balance < $ppob_product_price){
				$response['error_paymentppobpulsaprabayar'] 	= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Saldo Anda tidak mencukupi";
			} else {
                if($ppob_balance_company < $ppob_product_price){
                    $response['error_paymentppobpulsaprabayar'] 	= TRUE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['error_msg'] 							= "Dana PPOB tidak mencukupi";
                } else {
                    $data_inquiry[0] = array (
                        'product_id'     => $ppob_product_code,
                        'nova'           => $data_post['phone_number'],
                        'id_transaksi'   => $data_post['id_transaksi']
                    );

                    /* print_r("data_inquiry ");
                    print_r($data_inquiry);
                    print_r("<BR> "); */
                    
                    $data = array();

                    $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/pulsa-prabayar/payment';
                    $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                    $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
                    $data['content']    = json_encode($data_inquiry);


                    $inquiry_data       = json_decode(apiTrans($data), true);

                    /* print_r("inquiry_data ");
                    print_r($inquiry_data);
                    print_r("<BR> ");
                    print_r("<BR> "); */
        
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
                            'ppob_transaction_amount'	            => $data_post['productPrice'],
                            'ppob_transaction_default_amount'	    => $data_post['productDefaultPrice'],
                            'ppob_transaction_fee_amount'	        => $data_post['ppob_product_fee'],
                            'ppob_transaction_commission_amount'	=> $data_post['ppob_product_commission'],
                            'ppob_transaction_date'		            => date('Y-m-d'),
                            'ppob_transaction_status'	            => $ppob_transaction_status,
                            'created_id'				            => $data_post['member_id'],
                            'ppob_transaction_remark'	            => 'trxID '.$inquiry_data['data']['trxID'].' VoucherSN '.$inquiry_data['data']['token'].' Nomor HP '.$inquiry_data['data']['nova'].' '.$ppobproduct['data']['ppob_product_name'].' '.$ppobproduct['data']['ppob_product_title'],
                            'created_on'				            => date('Y-m-d H:i:s')
                        );

                        /* print_r("datappob_transaction ");
                        print_r($datappob_transaction);
                        print_r("<BR> ");
                        print_r("<BR> "); */
            
                        if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                            $data_balance = array (
                                'ppob_company_id'           => $ppob_company_id,
                                'ppob_company_balance'      => $ppob_company_balance - $ppob_product_price
                            );

                            $this->AndroidPPOB_model->insertPPOBTransaction_Company($datappob_transaction);

                            $data_profitshare = array (
                                'member_id'                 => $data_post['member_id'],
                                'savings_account_id'        => $savings_account_id,
                                'savings_id'                => $savings_id, 
                                'branch_id'                 => $data_post['branch_id'],
                                'ppob_profit_share_date'    => date("Y-m-d"),
                                'ppob_profit_share_amount'  => $ppob_product_commission,
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
                                    'ppob_agen_price'           => $ppob_product_price,
                                    'ppob_company_price'        => $ppob_product_default_price,
                                    'ppob_fee'                  => $ppob_product_fee,
                                    'ppob_commission'           => $ppob_product_commission,
                                    'savings_account_id'        => $savings_account_id,
                                    'savings_id'                => $savings_id,
                                    'journal_status'            => 1,
                                );

                                $this->journalPPOB($data_jurnal);
                            }                            
                        }
            
                        $response['error_paymentppobpulsaprabayar'] 	= FALSE;
                        $response['error_msg_title'] 					= "Confirm";
                        $response['error_msg'] 							= "Success";

                    } else {
                        $ppob_transaction_status = 2;

                        $datappob_transaction = array (
                            'ppob_unique_code'			            => '0000',
                            'ppob_company_id'			            => $ppob_company_id,
                            'ppob_agen_id'				            => $data_post['member_id'],
                            'ppob_agen_name'			            => $data_post['member_name'],
                            'ppob_product_category_id'	            => $ppobproduct['ppob_product_category_id'],
                            'ppob_product_id'			            => $ppobproduct['ppob_product_id'],
                            'ppob_transaction_amount'	            => $data_post['productPrice'],
                            'ppob_transaction_default_amount'	    => $data_post['productPrice'],
                            'ppob_transaction_fee_amount'	        => $data_post['productPrice'],
                            'ppob_transaction_commission_amount'	=> $data_post['productPrice'],
                            'ppob_transaction_date'		            => date('Y-m-d'),
                            'ppob_transaction_status'	            => $ppob_transaction_status,
                            'created_id'				            => $data_post['member_id'],
                            'ppob_transaction_remark'	            => 'trxID 0000 VoucherSN 0000',
                            'created_on'				            => date('Y-m-d H:i:s')
                        );

                        /* print_r("datappob_transaction");
                        print_r($datappob_transaction); */
            
                        $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
            
                        $response['error_paymentppobpulsaprabayar'] 	= FALSE;
                        $response['error_msg_title'] 					= "Confirm";
                        $response['error_msg'] 							= "Gagal";
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
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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





        //PLN PPREPAID--------------------------------------------------------------------
        public function getPPOBPLNPrePaid(){
            $response = array(
                'error'                      => FALSE,
                'error_msg'                  => "",
                'error_msg_title'            => "",
                'ppobplnprepaidproduct'      => "",
            );

            /* $ppob_agen_id       = $this->input->post('user_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id); */

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
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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

        public function paymentPPOBPlnPrabayar(){
            $response = array(
				'error'							=> FALSE,
				'error_paymentppobplnprepaid'	=> FALSE,
				'error_msg_title'		        => "",
				'error_msg'			            => "",
			);

			$ppob_product_code 			= 'PLNPREPAIDB';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance               = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

			$nominal 					= $this->input->post('nominalPLN', true);
			$by_admin 					= $this->input->post('adminPLN', true);
			$totalnominal				= $nominal + $by_admin;

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance = 0;
            }


			if($ppob_balance < $totalnominal){

				$response['error_paymentppobplnprepaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {
                $data_inquiry[0] = array (
                    'nominal' 		=> $this->input->post('nominalPLN', true),
                    'nova' 		    => $this->input->post('id_pelanggan_pln', true),
                    'id_transaksi' 	=> $this->input->post('id_transaksi', true)
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/prepaid/payment';
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);
    
                
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $token 	= $this->ccMasking($inquiry_data['data']['tokenNumber']);

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name', true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $totalnominal,
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['msn'].' Nama '.$inquiry_data['data']['namaPengguna'].' Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].'  Token '.$token,
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $inquiry_data['data']['totalTagihan']
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $inquiry_data['data']['totalTagihan'],
                                'ppob_company_price'    => $inquiry_data['data']['totalTagihan'] - $inquiry_data['data']['fee'],
                                'ppob_fee'              => $inquiry_data['data']['fee']
                            );

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
        
                    $response['error_paymentppobplnprepaid'] 	= FALSE;
                    $response['error_msg_title'] 				= "Success";
                    $response['error_msg'] 						= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $token 	= $this->ccMasking($inquiry_data['data']['tokenNumber']);

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name', true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $totalnominal,
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['msn'].' Nama '.$inquiry_data['data']['namaPengguna'].'<br> Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].'  Token '.$token,
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymentppobplnprepaid'] 	= FALSE;
                    $response['error_msg_title'] 				= "Confirm";
                    $response['error_msg'] 						= "Gagal";
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

            /* $member_id          = '32887'; */

            /* $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id); */

            $ppobsavingsaccount = $this->AndroidPPOB_model->getPPOBBalanceSavingsAccount($member_id);

            if (empty($ppobsavingsaccount)){
                $ppob_balance   = 0;
            } else {
                $ppob_balance   = $ppobsavingsaccount['savings_account_last_balance'];
            }

            /* if(empty($ppob_balance)){
                $ppob_balance   = 0;
            } */

            $data_inquiry[0]    = array (
                'nova'          => $this->input->post('id_pelanggan_pln', true),
            );

            $data_inquiry[0]    = array (
                'nova'          => '520530322560',
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/postpaid/inquiry';
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            /* print_r("inquiry_data");
            print_r($inquiry_data); */

            if($inquiry_data['code'] == 200){
                if ($inquiry_data['data']['responseCode'] == '00'){
                    $ppobplnpostpaidproduct[0]['refID']			        = $inquiry_data['data']['refID'];
                    $ppobplnpostpaidproduct[0]['id_pelanggan']	        = $inquiry_data['data']['subscriberID'];
                    $ppobplnpostpaidproduct[0]['tarif']			        = $inquiry_data['data']['tarif'];
                    $ppobplnpostpaidproduct[0]['daya']			        = $inquiry_data['data']['daya'];
                    $ppobplnpostpaidproduct[0]['nama']			        = $inquiry_data['data']['nama'];
                    $ppobplnpostpaidproduct[0]['totalTagihan']			= $inquiry_data['data']['totalTagihan'];
                    $ppobplnpostpaidproduct[0]['lembarTagihanTotal']	= $inquiry_data['data']['lembarTagihanTotal'];
                    $ppobplnpostpaidproduct[0]['responseCode']	        = '0000';
                    $ppobplnpostpaidproduct[0]['message']	            = $inquiry_data['data']['message'];
                    
                    $detilTagihan = $inquiry_data['data']['detilTagihan'];
                    
                    foreach($detilTagihan as $key => $val){
                        $ppobplnpostpaidbill[$key]['periodeTagihan']	= $val['periode'];
                        $ppobplnpostpaidbill[$key]['nilaiTagihan']		= $val['nilaiTagihan'];
                        $ppobplnpostpaidbill[$key]['dendaTagihan']		= $val['denda'];
                        $ppobplnpostpaidbill[$key]['adminTagihan']		= $val['admin'];
                        $ppobplnpostpaidbill[$key]['jumlahTagihan']		= $val['total'];
                    }
        
                    $response['error'] 							        = FALSE;
                    $response['error_msg_title'] 				        = "Success";
                    $response['error_msg'] 						        = "Data Exist";
                    $response['ppob_balance'] 					        = $ppob_balance;
                    $response['ppobplnpostpaidproduct'] 		        = $ppobplnpostpaidproduct;
                    $response['ppobplnpostpaidbill'] 			        = $ppobplnpostpaidbill;
                    $response['id_transaksi']                           = $inquiry_data['id_transaksi'];
                } else {
                    $ppobplnpostpaidproduct[0]['responseCode']	        = $inquiry_data['data']['responseCode'];
                    $ppobplnpostpaidproduct[0]['message']	            = $inquiry_data['data']['message'];
                    
                    $ppobplnpostpaidbill[0]['periodeTagihan']	        = "";
                    $ppobplnpostpaidbill[0]['nilaiTagihan']		        = "";
                    $ppobplnpostpaidbill[0]['dendaTagihan']		        = "";
                    $ppobplnpostpaidbill[0]['adminTagihan']		        = "";
                    $ppobplnpostpaidbill[0]['jumlahTagihan']		    = "";
                    
                    $response['error'] 							        = FALSE;
                    $response['error_msg_title'] 				        = "Success";
                    $response['error_msg'] 						        = "Data Exist";
                    $response['ppob_balance'] 					        = $ppob_balance;
                    $response['ppobplnpostpaidproduct'] 		        = $ppobplnpostpaidproduct;
                    $response['ppobplnpostpaidbill'] 			        = $ppobplnpostpaidbill;
                    $response['id_transaksi']                           = 0;
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
            $response = array(
				'error'								=> FALSE,
				'error_paymentppobplnpostpaid'		=> FALSE,
				'error_msg_title'	                => "",
				'error_msg'			                => "",
			);

			$ppobresponstatus 			= $this->configuration->PpobResponeCode();

			$ppob_product_code 			= 'PLNPOSTPAIDB';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);
			
			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

			$totaltagihan 				= $this->input->post('totalTagihan', true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance           = 0;
            }


			if($ppob_balance < $totaltagihan){

				$response['error_paymentppobplnpostpaid'] 	= TRUE;
				$response['error_msg_title'] 				= "Confirm";
				$response['error_msg'] 						= "Saldo Anda tidak mencukupi";

			} else {

                /* member_id=32887&member_name=00000001&id_pelanggan_pln=530000000001&totalTagihan=302500&refID=44404057&id_transaksi=227&branch_id=2 */
                
                $data_inquiry[0] = array (
                    'nova' 		    => $this->input->post('id_pelanggan_pln', true),
                    'id_transaksi' 	=> $this->input->post('id_transaksi', true)
                );

                $data_inquiry[0] = array (
                    'nova' 		    => '530000000001',
                    'id_transaksi' 	=> '240'
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-pln/postpaid/payment';
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);

                print_r("inquiry_data ");
                print_r($inquiry_data);
                print_r("<BR> ");
                print_r("<BR> ");
                exit;
	
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['subscriberID'].' Nama '.$inquiry_data['data']['namaPengguna'].' Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].' Lembar Tagihan '.$inquiry_data['data']['lembarTagihanTotal'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );

                    /* $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> 32887,
                        'ppob_agen_name'			=> 00000001,
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['subscriberID'].' Nama '.$inquiry_data['data']['namaPengguna'].' Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].' Lembar Tagihan '.$inquiry_data['data']['lembarTagihanTotal'],
                        'created_id'				=> 32887,
                        'created_on'				=> date('Y-m-d H:i:s')
                    ); */
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $inquiry_data['data']['totalTagihan']
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $inquiry_data['data']['totalTagihan'],
                                'ppob_company_price'    => $inquiry_data['data']['totalTagihan'] - $inquiry_data['data']['sales_vendor_fee_agen'] - $inquiry_data['data']['sales_vendor_fee_member'],
                                'ppob_fee_agen'         => $inquiry_data['data']['sales_vendor_fee_agen'],
                                'ppob_fee_member'       => $inquiry_data['data']['sales_vendor_fee_member']
                            );

                            /* $data_jurnal = array (
                                'branch_id'             => 2,
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => 32887,
                                'member_name'           => 00000001,
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $inquiry_data['data']['totalTagihan'],
                                'ppob_company_price'    => $inquiry_data['data']['totalTagihan'] - $inquiry_data['data']['fee'],
                                'ppob_fee'              => $inquiry_data['data']['fee']
                            ); */

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
                    $response['error_paymentppobplnpostpaid'] 	= FALSE;
                    $response['error_msg_title'] 				= "Success";
                    $response['error_msg'] 						= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['subscriberID'].' Nama '.$inquiry_data['data']['namaPengguna'].' Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].' Lembar Tagihan '.$inquiry_data['data']['lembarTagihanTotal'],
                        'created_id'				=> $this->input->post('member_id',true),
                        'created_on'				=> date('Y-m-d H:i:s')
                    );


                    /* $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> 32887,
                        'ppob_agen_name'			=> '00000001',
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'ppob_transaction_remark'	=> 'ID Pelanggan '.$inquiry_data['data']['subscriberID'].' Nama '.$inquiry_data['data']['namaPengguna'].' Tarif/Daya '.$inquiry_data['data']['tarif'].'/'.$inquiry_data['data']['daya'].' No. Ref '.$inquiry_data['data']['noReferensi'].' Lembar Tagihan '.$inquiry_data['data']['lembarTagihanTotal'],
                        'created_id'				=> 32887,
                        'created_on'				=> date('Y-m-d H:i:s')
                    ); */
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymentppobplnpostpaid'] 	= FALSE;
                    $response['error_msg_title'] 				= "Confirm";
                    $response['error_msg'] 						= "Gagal";	
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
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
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
            $response = array(
				'error'								=> FALSE,
				'error_paymentppobbpjskesehatan'	=> FALSE,
				'error_msg_title'		            => "",
				'error_msg'			                => "",
			);

			$ppob_product_code 			= 'BPJSKES';

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance               = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

			$totalTagihan 				= $this->input->post('totalTagihan',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
            }
            
            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

			if($ppob_balance < $totalTagihan){

				$response['error_paymentppobbpjskesehatan'] 	= TRUE;
				$response['error_msg_title'] 					= "Confirm";
				$response['error_msg'] 							= "Saldo Anda tidak mencukupi";

			} else {
                $data_inquiry[0] = array (
                    'nova'          => $this->input->post('noVA', true),
                    'jumlah_bulan'  => $this->input->post('jmlBulan', true),
                    'id_transaksi'  => $this->input->post('id_transaksi', true),
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-bpjs/payment';
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);
				
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'created_id'				=> $this->input->post('member_id',true),
                        'ppob_transaction_remark'	=> 'No. VA '.$inquiry_data['data']['nova'].' Nama '.$inquiry_data['data']['namaPengguna'].' Jumlah Peserta'.$inquiry_data['data']['jumlahPeserta'].' Jumlah Periode '.$inquiry_data['data']['jumlahPeriode'].' No. Referensi '.$inquiry_data['data']['noReferensi'],
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $inquiry_data['data']['totalTagihan']
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $inquiry_data['data']['totalTagihan'],
                                'ppob_company_price'    => $inquiry_data['data']['totalTagihan'] - $inquiry_data['data']['fee'],
                                'ppob_fee'              => $inquiry_data['data']['fee']
                            );

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
                    $response['error_paymentppobbpjskesehatan'] 	= FALSE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['error_msg'] 							= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['noReferensi'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['totalTagihan'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'created_id'				=> $this->input->post('member_id',true),
                        'ppob_transaction_remark'	=> 'No. VA '.$inquiry_data['data']['nova'].' Nama '.$inquiry_data['data']['namaPengguna'].' Jumlah Peserta'.$inquiry_data['data']['jumlahPeserta'].' Jumlah Periode '.$inquiry_data['data']['jumlahPeriode'].' No. Referensi '.$inquiry_data['data']['noReferensi'],
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymentppobbpjskesehatan'] 	= FALSE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['error_msg'] 							= "Gagal";

				}
			}

			echo json_encode($response);
        }






        //TOPUP EMONEY----------------------------------------------------------------------------
        public function getPPOBTopUpEmoneyProduk(){
            $response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobtopupemoney'			=> "",
            );
            
			$ppobtopupemoney[0]['ppob_product_category_id']	    = 28;
			$ppobtopupemoney[0]['ppob_product_category_name']	= 'Topup Dana';
			$ppobtopupemoney[0]['ppob_product_category_code']	= 'DANA';
			$ppobtopupemoney[1]['ppob_product_category_id']	    = 29;
			$ppobtopupemoney[1]['ppob_product_category_name']	= 'Topup OVO';
			$ppobtopupemoney[1]['ppob_product_category_code']   = 'GRAB';
			$ppobtopupemoney[2]['ppob_product_category_id']	    = 30;
			$ppobtopupemoney[2]['ppob_product_category_name']	= 'Topup GoPay';
			$ppobtopupemoney[2]['ppob_product_category_code']	= 'GOJEK';
			$ppobtopupemoney[3]['ppob_product_category_id']	    = 31;
			$ppobtopupemoney[3]['ppob_product_category_name']	= 'Topup E-Toll';
			$ppobtopupemoney[3]['ppob_product_category_code']   = 'ETOLL';
		
		
			
			$response['error'] 						= FALSE;
			$response['error_msg_title'] 			= "Success";
			$response['error_msg'] 					= "Data Exist";
			$response['ppobtopupemoney']            = $ppobtopupemoney;

			echo json_encode($response);
        }

        public function getPPOBTopUp(){
            $response = array(
				'error'						=> FALSE,
				'error_msg'					=> "",
				'error_msg_title'			=> "",
				'ppobtopupemoneyproduct'	=> "",
            );

            $ppob_agen_id       = $this->input->post('member_id');

            $ppob_balance       = $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            if(empty($ppob_balance)){
                $ppob_balance   = 0;
            }

            $data_inquiry[0]    = array (
                'productCode'          => $this->input->post('productCode', true),
            );

            $data = array();

            $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-topup/inquiry';
            $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
            $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
            $data['content']    = json_encode($data_inquiry);


            $inquiry_data       = json_decode(apiTrans($data), true);

            $settingPrice       = $this->SettingPrice_model->getSettingByType('1');

            if($inquiry_data['code'] == 200){
                $no = 0;
                foreach ($inquiry_data['data'] as $key => $val){
                    $price = ceil($val['price'] + ($val['price'] * ($settingPrice->setting_price_fee / 100)));

                    $ppobtopupemoneyproduct[$no]['ppob_product_code']             = $val['productCode'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_name']             = $val['productDesc'];
                    $ppobtopupemoneyproduct[$no]['ppob_product_price']            = $price;
                    $ppobtopupemoneyproduct[$no]['ppob_product_default_price']    = $val['price'];

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

        public function paymentPPOBTopup(){
            $response = array(
				'error'									=> FALSE,
				'error_paymenttopupemoney'				=> FALSE,
				'error_msg_title'	                    => "",
				'error_msg'                 			=> "",
			);

			$ppob_product_code 			= $this->input->post('productCode',true);

			$database 					= $this->db->database;

			$ppob_company_id			= $this->AndroidPPOB_model->getPPOBCompanyID($database);

			$ppob_agen_id				= $this->input->post('member_id', true);

			$ppobproduct 				= $this->AndroidPPOB_model->getPPOBProduct_Detail($ppob_product_code);

			$ppob_balance 				= $this->TopupPPOB_model->getPPOBBalanceAgen($ppob_agen_id);

            $ppob_product_price 		= $this->input->post('productPrice',true);
            
			$ppob_product_default_price = $this->input->post('productDefaultPrice',true);

			if($ppob_agen_id == null){
				$ppob_agen_id 			= 0;
			}

			if($ppob_balance < $ppob_product_price){

				$response['error_paymenttopupemoney'] 	= TRUE;
				$response['error_msg_title'] 			= "Confirm";
				$response['error_msg'] 					= "Saldo Anda tidak mencukupi";

			} else {
	
				$data_inquiry[0] = array (
                    'nova'          => $this->input->post('id_pelanggan', true),
                    'productCode'   => $this->input->post('productCode', true),
                    'id_transaksi'  => $this->input->post('id_transaksi', true),
                );
                
                $data = array();

                $data['url']        = 'https://ciptapro.com/cst_ciptasolutindo/api/ppob/payment-topup/payment';
                $data['apikey']     = '$2y$10$RM31rL6NuQjiXHrEv3jewe95KtXnXsoVaVZxAdpRzppegAV7AqiKe';
                $data['secretkey']  = '$2y$10$/FVsrF6I1m.VtFiWh5gHVe7MdEHV/JIsbyV7u5Qiwfw1XBQ.MtDUS';
                $data['content']    = json_encode($data_inquiry);

                $inquiry_data       = json_decode(apiTrans($data), true);
	
                if($inquiry_data['code'] == 200){
                    $ppob_transaction_status = 1;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['trxID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $ppob_product_price,
                        'ppob_transaction_cipta_amount'	    => $ppob_product_default_price,
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'created_id'				=> $this->input->post('member_id',true),
                        'ppob_transaction_remark'	=> 'trxID '.$inquiry_data['data']['trxID'].' Voucher Code '.$inquiry_data['data']['voucher'].' ID Pelanggan '.$inquiry_data['data']['nova'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$inquiry_data['data']['ref'],
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    if($this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction)){
                        $data_balance = array (
                            'ppob_agen_id'          => $ppob_agen_id,
                            'ppob_balance_amount'   => $ppob_balance - $ppob_product_price
                        );

                        if($this->TopupPPOB_model->updatePPOBBalance($ppob_agen_id, $data_balance)){
                            $data_jurnal = array (
                                'branch_id'             => $this->input->post('branch_id', true),
                                'ppob_company_id'       => $ppob_company_id,
                                'member_id'             => $this->input->post('member_id', true),
                                'member_name'           => $this->input->post('member_name', true),
                                'product_name'          => $ppobproduct['ppob_product_name'],
                                'ppob_agen_price'       => $ppob_product_price,
                                'ppob_company_price'    => $ppob_product_default_price,
                                'ppob_fee'              => $ppob_product_price - $ppob_product_default_price
                            );

                            $this->journalPPOB($data_jurnal);
                        }
                    }
        
                    $response['error_paymenttopupemoney'] 	= FALSE;
                    $response['error_msg_title'] 					= "Confirm";
                    $response['error_msg'] 							= "Success";

                } else {
                    $ppob_transaction_status = 2;

                    $datappob_transaction = array (
                        'ppob_unique_code'			=> $inquiry_data['data']['trxID'],
                        'ppob_company_id'			=> $ppob_company_id,
                        'ppob_agen_id'				=> $this->input->post('member_id',true),
                        'ppob_agen_name'			=> $this->input->post('member_name',true),
                        'ppob_product_category_id'	=> $ppobproduct['ppob_product_category_id'],
                        'ppob_product_id'			=> $ppobproduct['ppob_product_id'],
                        'ppob_transaction_amount'	=> $inquiry_data['data']['harga'],
                        'ppob_transaction_date'		=> date('Y-m-d'),
                        'ppob_transaction_status'	=> $ppob_transaction_status,
                        'created_id'				=> $this->input->post('member_id',true),
                        'ppob_transaction_remark'	=> 'trxID '.$inquiry_data['data']['trxID'].' Voucher Code '.$inquiry_data['data']['voucher'].' ID Pelanggan '.$inquiry_data['data']['nova'].' '.$ppobproduct['ppob_product_name'].' '.$ppobproduct['ppob_product_title'].'No. Referensi '.$inquiry_data['data']['ref'],
                        'created_on'				=> date('Y-m-d H:i:s')
                    );
        
                    $this->AndroidPPOB_model->insertPPOBTransaction($datappob_transaction);
        
                    $response['error_paymenttopupemoney'] 	= FALSE;
                    $response['error_msg_title'] 			= "Confirm";
                    $response['error_msg'] 					= "Gagal";
                }
				
			}

			echo json_encode($response);
        }




        







        //JURNAL---------------------------------------------------------------------------
        public function journalPPOB($data){
            /* SAVINGS TRANSFER FROM */

            $data_transfermutationfrom = array(
				'branch_id'								=> $data['branch_id'],
				'savings_transfer_mutation_date'		=> date('Y-m-d'),
				'savings_transfer_mutation_amount'		=> $data['ppob_agen_price'],
				'savings_transfer_mutation_status'		=> 3,
				'operated_name'							=> $data['member_name'],
				'created_id'							=> $data['member_id'],
				'created_on'							=> date('Y-m-d H:i:s'),
			);

            if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutation($data_transfermutationfrom)){
                $transaction_module_code 	        = "TRPPOB";
                $transaction_module_id 		        = $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
                $savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);
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
                    $acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutationfrom['created_id']);
							
                    $journal_voucher_period 	= date("Ym", strtotime($data_transfermutationfrom['savings_transfer_mutation_date']));
                    
                    $data_journal = array(
                        'branch_id'						=> $data_transfermutationfrom['branch_id'],
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
                        'account_id_status'				=> 1,
                    );

                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_debit);


                    /* SIMPAN DATA JOURNAL CREDIT */
                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_down_payment']);

                    $data_credit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_down_payment'],
                        'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data['ppob_company_price'],
                        'journal_voucher_credit_amount'	=> $data['ppob_company_price'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);

                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

                    $data_credit =array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_income'],
                        'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data['ppob_fee'],
                        'journal_voucher_credit_amount'	=> $data['ppob_fee'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
                    );

                    $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);
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
                $savings_transfer_mutation_id 	    = $this->AcctSavingsTransferMutation_model->getSavingsTransferMutationID($data['created_on']);
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
                    'savings_transfer_mutation_from_amount'		=> $data['ppob_commission'],
                    'savings_account_last_balance'				=> $savings_account_opening_balance + $data['ppob_commission'],
                );

                $member_name = $data['member_name'];

                if($this->AcctSavingsTransferMutation_model->insertAcctSavingsTransferMutationTo($datato)){
                    $acctsavingstr_last 		= $this->AcctSavingsTransferMutation_model->getAcctSavingsTransferMutation_Last($data_transfermutationto['created_id']);
							
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

                    $journal_voucher_id 			    = $this->AcctSavingsTransferMutation_model->getJournalVoucherID($data_transfermutationfrom['created_id']);


                    /* SIMPAN DATA JOURNAL DEBIT */

                    $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

                    $data_debit = array(
                        'journal_voucher_id'			=> $journal_voucher_id,
                        'account_id'					=> $preferenceppob['ppob_account_income'],
                        'journal_voucher_description'	=> 'Bagi Hasil PPOB '.$data['product_name'].' '.$data['member_name'],
                        'journal_voucher_amount'		=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'journal_voucher_debit_amount'	=> $data_transfermutationto['savings_transfer_mutation_amount'],
                        'account_id_default_status'		=> $account_id_default_status,
                        'account_id_status'				=> 1,
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
                        'account_id_status'				=> 0,
                    );

                    $this->AcctSavingsTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
                }          
            }







            $transaction_module_code 	= "TRPPOB";
            $transaction_module_id 		= $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
            
            $journal_voucher_period 	= date("Ym");

            $ppobtransaction_last 		= $this->AndroidPPOB_model->getPpobTransactionMember_Last($data['member_id'], $data['ppob_company_id']);

            $data_journal = array(
                'branch_id'							=> $data['branch_id'],
                'journal_voucher_period' 			=> $journal_voucher_period,
                'journal_voucher_date'				=> date('Y-m-d'),
                'journal_voucher_title'				=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                'journal_voucher_description'		=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                'transaction_module_id'				=> $transaction_module_id,
                'transaction_module_code'			=> $transaction_module_code,
                'transaction_journal_id' 			=> $ppobtransaction_last['ppob_transaction_id'],
                'transaction_journal_no' 			=> $ppobtransaction_last['ppob_transaction_no'],
                'created_id' 						=> $data['member_id'],
                'created_on' 						=> date('Y-m-d H:i:s'),
            );

            $this->AndroidPPOB_model->insertAcctJournalVoucher($data_journal);

            $journal_voucher_id 		= $this->AndroidPPOB_model->getJournalVoucherID($data_journal['created_id']);

            $preferenceppob 			= $this->AndroidPPOB_model->getPreferencePpob();

            $account_id 				= $this->AndroidPPOB_model->getAccountID($data['savings_id']);

            //DEBET
            $account_id_default_status 	= $this->AndroidPPOB_model->getAccountIDDefaultStatus($account_id);

            $data_debet = array (
                'journal_voucher_id'			=> $journal_voucher_id,
                'account_id'					=> $account_id,
                'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                'journal_voucher_amount'		=> $data['ppob_agen_price'],
                'journal_voucher_debit_amount'	=> $data['ppob_agen_price'],
                'account_id_default_status'		=> $account_id_default_status,
                'account_id_status'				=> 0,
            );

            $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debet);

            





            /* PROFIT SHARE */

            $transaction_module_code 	= "PSPPOB";
            $transaction_module_id 		= $this->AndroidPPOB_model->getTransactionModuleID($transaction_module_code);
            
            $journal_voucher_period 	= date("Ym");

            $ppobtransaction_last 		= $this->AndroidPPOB_model->getPpobTransactionMember_Last($data['member_id'], $data['ppob_company_id']);

            $data_journal = array(
                'branch_id'							=> $data['branch_id'],
                'journal_voucher_period' 			=> $journal_voucher_period,
                'journal_voucher_date'				=> date('Y-m-d'),
                'journal_voucher_title'				=> 'Transaksi Share PPOB '.$data['product_name'].' '.$data['member_name'],
                'journal_voucher_description'		=> 'Transaksi Share PPOB '.$data['product_name'].' '.$data['member_name'],
                'transaction_module_id'				=> $transaction_module_id,
                'transaction_module_code'			=> $transaction_module_code,
                'transaction_journal_id' 			=> $ppobtransaction_last['ppob_transaction_id'],
                'transaction_journal_no' 			=> $ppobtransaction_last['ppob_transaction_no'],
                'created_id' 						=> $data['member_id'],
                'created_on' 						=> date('Y-m-d H:i:s'),
            );

            $this->AndroidPPOB_model->insertAcctJournalVoucher($data_journal);

            $journal_voucher_id 		= $this->AndroidPPOB_model->getJournalVoucherID($data_journal['created_id']);

            $preferenceppob 			= $this->AndroidPPOB_model->getPreferencePpob();

            

            //DEBET

            $account_id_default_status 			= $this->AndroidPPOB_model->getAccountIDDefaultStatus($preferenceppob['ppob_account_income']);

            $data_credit = array(
                'journal_voucher_id'			=> $journal_voucher_id,
                'account_id'					=> $preferenceppob['ppob_account_income'],
                'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                'journal_voucher_amount'		=> $data['ppob_commission'],
                'journal_voucher_debit_amount'	=> $data['ppob_commission'],
                'account_id_default_status'		=> $account_id_default_status,
                'account_id_status'				=> 1,
            );

            $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_credit);

            //KREDIT
            $account_id 				= $this->AndroidPPOB_model->getAccountID($data['savings_id']);

            $account_id_default_status 	= $this->AndroidPPOB_model->getAccountIDDefaultStatus($account_id);

            $data_debet = array (
                'journal_voucher_id'			=> $journal_voucher_id,
                'account_id'					=> $account_id,
                'journal_voucher_description'	=> 'Transaksi PPOB '.$data['product_name'].' '.$data['member_name'],
                'journal_voucher_amount'		=> $data['ppob_commission'],
                'journal_voucher_credit_amount'	=> $data['ppob_commission'],
                'account_id_default_status'		=> $account_id_default_status,
                'account_id_status'				=> 0,
            );

            $this->AndroidPPOB_model->insertAcctJournalVoucherItem($data_debet);
            

            return; 
        }

    }
        
?>