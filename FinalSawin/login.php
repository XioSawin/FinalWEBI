<?php
	include('classes/DB.php');

	//check if form has been submitted

	if(isset($_POST['login'])) {
		//'grab' username and password values from inpout text
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){

			if(password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])){

				echo 'Sesión iniciada!';

				$cstrong = True; //clave es cryptographically strong
				//token security
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong)); //convertir binario a hexa
				
				//get userID
				$user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
				
				//guardar token en DB. sha1 to hash the token. security reasons mate.
				DB::query('INSERT INTO login_tokens VALUES(\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

				//cookies para loggear al user. RSID=RedSocialID. token valid for 1 week. cookie valid everywhere. domain cookie is valid NULL. javascript cant access cookie.
				setcookie("RSID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);

				//set a kinda remember me cookie. cookie will expire every 3 days. whenever it does, a new RSID cookie needs to be created. security reasons mate x2.

				setcookie("RSID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
				
			}else {
				echo 'Contraseña incorrecta.';
			}

		}else{
			echo 'Usuario inexistente.';
		}
	}
?>

<h1>Inicia sesión</h1>
<form action="login.php" method="post">
	<input type="text" name="username" value="" placeholder="Usuario"><p />
	<input type="password" name="password" value="" placeholder="Contraseña"><p />
	<input type="submit" name="login" value="Iniciar Sesión">
</form>
