<?php

class Login{
		public static function isLoggedIn(){

		//check if cookie has been set
		if(isset($_COOKIE['RSID'])){
			//check login in database && if token is valid
			if(DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['RSID'])))){

				$userid= DB::query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['RSID'])))[0]['user_id'];

				//check if 2nd cookie is still set.
				if(isset($_COOKIE['RSID_'])){
					return $userid;
				}else {
					$cstrong = True; //clave es cryptographically strong
				//token security
					$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong)); //convertir binario a hexa
					
					//insert new token into tokens table and delete expired one.
					DB::query('INSERT INTO login_tokens VALUES(\'\', :token, :userid)', array(':token'=>sha1($token), ':user_id'=>$user_id));

					DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['RSID'])));

					//replace old RSID token with RSID_. keepin it fresh yall.

					//same as in login.php. it does the same thing.
					setcookie("RSID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);

					setcookie("RSID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

					return $userid;

				}

				
			}
		}

		return false;
	}
}
	
?>