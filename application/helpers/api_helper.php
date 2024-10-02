<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function apiAuth()
{
	$CI = get_instance();
	$CI->load->model('ppob/CoreCompany_model');
	$headers = array();
		foreach($_SERVER as $key => $value)
		{
			if (substr($key, 0, 5) <> 'HTTP_') 
			{
				continue;
			}
			$header = str_replace(' ', '-', str_replace('_', ' ', strtolower(substr($key, 5))));
			$headers[$header] = $value;
		}
	// $headers['apikey'];
	$CI->load->model('ppob/CoreCompany_model');
	$company = $CI->CoreCompany_model->getComanyByKey($headers['apikey'],$headers['secretkey']);
	return $company;
}

function apiTrans($data)
{

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$data['url']);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data['content']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$headers = [
		'apikey:'.$data['apikey'],
		'secretkey:'.$data['secretkey'],
		'Content-Type:application/json'
	];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$data = curl_exec ($ch);
	curl_close ($ch);
	return $data;
	
}
