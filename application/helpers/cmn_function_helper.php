<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function response($msg = [], $code = 400, $extra = '')
{
	$param['responseCode'] = (int)$code;
	$param['responseMessage'] = $msg;
	if (!empty($extra)) {
		$param = array_merge($param,$extra);
	}
	echo (@$param)?json_encode($param):""; exit;
}

function checkAuthToken()
{
	$headers = apache_request_headers();
    //echo my_decrypt('123'); exit;
	if(empty(my_encrypt(@$headers['token'])))
	{
		//response('Invalid Token', 400);
	}
}

function my_decrypt($data) 
{
    if ($data != '') {

        $key = ENC_KEY;

        $encryption_key = base64_decode($key);

        //$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // $iv = "1234567890123456";

        $iv = '';

        for ($i = 1; $i <= 8; $i++) {

            $iv .= rand(55, 22) + 7;

        }

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);

        return base64_encode($encrypted . '::' . $iv);

    }
}

function my_encrypt($data) 
{
    $key = ENC_KEY;
	$encrypted_data = '';
	$iv = '';
	
	$encryption_key = base64_decode($key);
	$base64 = explode('::', base64_decode($data));
	
	if(isset($base64[1])){ list($encrypted_data, $iv) = explode('::', base64_decode($data), 2); }
	if(strlen($iv) == 16)
	{
		$decrypt = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
		if($decrypt)
		{
			return $decrypt;
		}
	}else
	{
		return false;
	}

}

function img_file_cinfig()
{
     return array(
               'upload_path' => "./assets/uploads",
               'allowed_types' => "gif|jpg|png|jpeg|pdf|docx",
               'overwrite' => TRUE,
               'max_size' => "2000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
               );
}


?>
