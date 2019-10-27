<?php
	include('./classes/DB.php');

	if(isset($_POST['createAccount'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$email = $_POST['email'];

		if(!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){

			if(strlen($username)>=3 && strlen($username)<=32){

				if(preg_match('/[a-zA-Z0-9_]+/', $username)){

					if(strlen($password)>=6 && strlen($password) <=60){
						if(filter_var($email, FILTER_VALIDATE_EMAIL)){

							//chequar que no haya dos emails iguales
							if(!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))){
								DB::query('INSERT INTO users VALUES(\'\', :username, :password, :email, \'\')', array('username'=>$username, 'password'=>password_hash($password, PASSWORD_BCRYPT), 'email'=>$email));

								echo "¡Registración exitosa!";
							}else{
								echo 'Email ya se encuentra registrado en la base de datos.';
							}
							
						}else{
							echo 'Email inválido';
						}
					}else{
						echo 'Longitud de contraseña inválida';
					}
	
				}else {
					echo 'Caracteres inválidos';
				}
			}else {
				echo 'Longitud de usuario inválida.';
			}

			
		}else{
			echo 'El usuario ya existe!';
		}	
	}
?>

<h1>Registrarse</h1>
<form action="crear-cuenta.php" method="post">
	<input type="text" name="username" value="" placeholder="Nombre de usuario... "> <br />
	<input type="password" name="password" value="" placeholder="Contraseña... "> <br />
	<input type="email" name="email" value="" placeholder="usted@suemail.com"> <br />
	<input type="submit" name="createAccount" value="Crear cuenta">
</form>