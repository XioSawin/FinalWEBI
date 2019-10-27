<?php
	include('./classes/DB.php');

	if(isset($_POST['resetpassword'])){

		$email = $_POST['email'];
		$cstrong = True; //clave es cryptographically strong
				//token security
		$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong)); //convertir binario a hexa
		//get userID
		$user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
		//guardar token en DB. sha1 to hash the token. security reasons mate.
		DB::query('INSERT INTO password_tokens VALUES(\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

		echo 'Email enviado';
		echo '<br />';
		echo $token;
	}
?>

<h1>¿Olvidaste tu contraseña?</h1>
<form action="password-olvidada.php" method="post">
	<input type="text" name="email" value="" placeholder="Email"><p />
	<input type="submit" name="resetpassword" value="Resetear contraseña">
</form>