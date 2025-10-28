<?php
namespace eBizIndia;
class Token{
	const life_time = 600; // in sec - 10 minutes	

	public static function generate(){
		// SECURITY IMPROVEMENT: Use bin2hex instead of md5 for token generation
		// This creates a 64-byte (128-character hex) token from cryptographically secure random bytes
		$token = bin2hex(random_bytes(32));
		$_SESSION['token'] = [];
		$_SESSION['token'][$token] = time()+self::life_time; // valid for next 10 minutes
		return $token;
	}

	public static function get(){
		return key($_SESSION['token']??[]);
	}

	public static function verifyFromHeader(){
		$token = filter_input(INPUT_SERVER, 'HTTP_TOKEN');
		return self::verify($token);
	}

	public static function verifyFromPayload(){
		$token = filter_input(INPUT_POST, 'csrf');
		return self::verify($token);
	}

	private static function verify($token){
		if(empty($token))
			return false;
		if(isset($_SESSION['token'][$token])){
			$token_val = $_SESSION['token'][$token];
			unset($_SESSION['token'][$token]);
			if($token_val>time())
				return true;
		}
		return false;	
	}

}