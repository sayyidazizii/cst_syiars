<?php

require_once ("lib/nusoap.php");

class PpobPulsaPrabayar_model extends CI_Model 
{
	/* const BBUZ_PP_HOST	= 'http://117.102.64.238:1212/pulsaPrabayar.php?wsdl'; */
	const BBUZ_PP_HOST	= 'https://pp.bosbiller.com/pp/index.php?wsdl';
	const SOAP_TIMEOUT	= 60;
	const SOAP_RESPONSE_TIMEOUT = 180;
	
	// key and secret for development
	private $apiKey		= 'bd8446eaa574ab00ab72249f8b06a759';	
	private $secretKey	= 'a3355920444fae8eb84b1f6304bbe1d9';	

	// key and secret for production
	//private $apikey 		= 'bc051a1679dedaa826519ea535ac24556d24bd10';
	//private $secretkey      = 'fa2b2be489e5796abd2e50078f076e7cb4401885';

	//private $apiKey = 'bc051a1679dedaa826519ea535ac24556d24bd10';
	//private $secretKey = 'fa2b2be489e5796abd2e50078f076e7cb4401885';
	private $soap;
	
	public function __construct() 
	{
		$this->soap = new nusoap_client(
			self::BBUZ_PP_HOST,
			$wsdl = true, 
			$proxyhost = false, 
			$proxyport = false, 
			$proxyusername = false, 
			$proxypassword = false, 
			$timeout = self::SOAP_TIMEOUT, 
			$response_timeout = self::SOAP_RESPONSE_TIMEOUT, 
			$portName = ''
		);
		
		$this->soap->setCredentials($this->apiKey, $this->secretKey, 'basic');
	}
	
	public function info($data = []) 
	{		
		return $this->soap->call('mitraInfo', $data);
	}


	public function produk($data = [])
	{
		$parts = [
			'product_id' => $data['id']
		];

		return $this->soap->call('productList', $parts);
	}
	
	public function inquiry($data)
	{		
		// $parts = [
		// 	'msisdn' => $data['phone']
		// ];

		return $this->soap->call('inquiry', $data);
	}
	
	public function payment($data) 
	{
		// $parts = [
		// 	'product_id' => $data['id'],
		// 	'msisdn' => $data['phone'],
		// 	'purchase_amount' => $data['harga']
		// ];

		return $this->soap->call('payment', $data);
	}

	public function status($data) 
	{
		// $parts = [
		// 	'msisdn' => $data['phone'],
		// 	'trxID' => $data['id_transaksi']
		// ];
		
		return $this->soap->call('checkStatus', $data);
	}
}

?>