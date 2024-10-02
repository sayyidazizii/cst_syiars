<?php

require_once ("lib/nusoap.php");

class PpobPaymentBpjs_model extends CI_Model {

	/* const BBUZ_PP_HOST	= 'http://117.102.64.238:1212/pp/index.php?wsdl'; */
	const BBUZ_PP_HOST	= 'https://pp.bosbiller.com/pp/index.php?wsdl';
	const SOAP_TIMEOUT	= 60;
	const SOAP_RESPONSE_TIMEOUT = 180;
	
	// key and secret for development
	private $apiKey		= 'bd8446eaa574ab00ab72249f8b06a759';	
	private $secretKey	= 'a3355920444fae8eb84b1f6304bbe1d9';	

	//key and secret for production
	//private $apiKey 	  = 'bc051a1679dedaa826519ea535ac24556d24bd10';
	//private $secretKey 	  = 'fa2b2be489e5796abd2e50078f076e7cb4401885';
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
	
	public function inquiry($data) 
	{		
		// $params = array(
		// 	'productCode' => $productCode,
		// 	'idPel'		  => $idPel,
		// 	'idPel2'	  => $idPel2,
		// 	'miscData'	  => $miscData
		// );

		//print_r($data);exit;
		
		return $this->soap->call('ppInquiry', $data);
	}
	
	// public function payment($productCode, $refID, $nominal, $miscData= '')
	// {		
	// 	$params = array(
	// 		'productCode' => $productCode,
	// 		'refID'		  => $refID,
	// 		'nominal'	  => $nominal,
	// 		'miscData'	  => $miscData
	// 	);
		
	// 	return $this->soap->call('ppPayment', $params);
	// }

	public function payment($data)
	{		
		// $params = array(
		// 	'productCode' => $productCode,
		// 	'refID'		  => $refID,
		// 	'nominal'	  => $nominal,
		// 	'miscData'	  => $miscData
		// );
		
		return $this->soap->call('ppPayment', $data);
	}
	
	public function mitraInfo() 
	{
		return $this->soap->call('ppMitraInfo', array());
	}

	public function options($cmd, $data) {
		$params = array(
			
			'cmd'  => $cmd,
			'data' => $data
		);
		
		return $this->soap->call('ppOptions', $params);
	}

	public function topup($productCode, $idPel, $idPel2 = '', $miscData = '') 
	{		
		$params = array(
			'productCode' => $productCode,
			'idPel'		  => $idPel,
			'idPel2'	  => $idPel2,
			'miscData'	  => $miscData
		);
		
		return $this->soap->call('ppTopup', $params);
	}
}