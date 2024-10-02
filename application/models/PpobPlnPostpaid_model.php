<?php
require_once ("lib/nusoap.php");
	class PpobPlnPostpaid_model extends CI_Model {
		var $table = "acct_account";
		
	/* const BBUZ_PP_HOST	= 'http://117.102.64.238:1212/pp/index.php?wsdl'; */
	const BBUZ_PP_HOST	= 'https://pp.bosbiller.com/pp/index.php?wsdl';
	const SOAP_TIMEOUT	= 60;
	const SOAP_RESPONSE_TIMEOUT = 360;
	
	// key and secret for development
	private $apiKey		= 'bd8446eaa574ab00ab72249f8b06a759';	
	private $secretKey	= 'a3355920444fae8eb84b1f6304bbe1d9';	

	//key and secret for production
	//private $apikey 	= 'bc051a1679dedaa826519ea535ac24556d24bd10';
	//private $secretkey  = 'fa2b2be489e5796abd2e50078f076e7cb4401885';
	private $soap;
	
	public function __construct() 
	{
		$this->soap = new nusoap_client(
			self::BBUZ_PP_HOST, 
			true, 
			false, 
			false, 
			false, 
			false, 
			self::SOAP_TIMEOUT, 
			self::SOAP_RESPONSE_TIMEOUT, 
			''
		);
		
		$this->soap->setCredentials($this->apiKey, $this->secretKey, 'basic');
		} 

	public function inquiry($data) 
	{		
		// $params = array(
		// 	'productCode' => $productCode,
		// 	'idPel'		  => $idPel,
		// 	'idPel2'	  => $denom,
		// 	'miscData'	  => $miscData
		// );
		
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
			'cmd' => $cmd,
			'data' => $data
		);
		
		return $this->soap->call('ppOptions', $params);
	}
		
}
?>