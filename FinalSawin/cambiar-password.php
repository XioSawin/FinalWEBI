<?php
	//allow to change password by entering old pswrd 1st. not a 'forgot password'.
	include('./classes/DB.php');
	include('./classes/Login.php');
	$tokenIsValid = False;

	if(Login::isLoggedIn()){
		//check if form was submitted
		if(isset($_POST['changepassword'])){
			//comparar valor oldpswrd c/ valor en bdd.

			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$newpasswordre = $_POST['newpasswordre'];
			$userid = Login::isLoggedIn();

			if(password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])){

				if($newpassword == $newpasswordre){

					if(strlen($newpassword)>=6 && strlen($newpassword)<=60){

						DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_DEFAULT), ':userid'=>$userid));

						echo "Contraseña modificada";
					}

				}else {
					echo 'Las nuevas contraseñas no coinciden';
				}


			}else{
				echo 'Contraseña ingresada es incorrecta';
			}
		}
	} else {
		if(isset($_GET['token'])){
			$token = $_GET['token'];

			if(DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))){
				$user_id = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
				$tokenIsValid = true;

				if(isset($_POST['changepassword'])){
				//comparar valor oldpswrd c/ valor en bdd.

					$newpassword = $_POST['newpassword'];
					$newpasswordre = $_POST['newpasswordre'];


						if($newpassword == $newpasswordre){

							if(strlen($newpassword)>=6 && strlen($newpassword)<=60){

								DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_DEFAULT), ':userid'=>$user_id));

								echo "Contraseña modificada";

								DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$user_id));
							}

						}else {
							echo 'Las nuevas contraseñas no coinciden';
						}
				}
			} else{
				die('Token invalid');
			}
	}else {
		die('Sesión no iniciada');
	}
}
?>

<!--<h1>Cambiar contraseña</h1>
<form action="php if(!$tokenIsValid) { echo 'cambiar-password.php'; } else {echo 'cambiar-password.php?token='.$token.'';}?>" method="post">
	php if(!$tokenIsValid) {
		echo '<input type="password" name="oldpassword" value="" placeholder="Contraseña actual"><p />';
	//} 
	<input type="password" name="newpassword" value="" placeholder="Nueva contraseña"><p />
	<input type="password" name="newpasswordre" value="" placeholder="Repetir nueva contraseña"><p />
	<input type="submit" name="changepassword" value="Cambiar contraseña">
</form>-->

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar contraseña - Final WEB I</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="assets/css/Highlight-Clean.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body>
    <header class="hidden-sm hidden-md hidden-lg">
        <div class="searchbox">
            <form>
                <h1 class="text-left">Final WEB I</h1>
                <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                                        <input class="form-control sbox" type="text">
                                        <ul class="list-group autocomplete" style="position:absolute;width:100%; z-index: 100">
                                        </ul>
                        </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">MENU <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li role="presentation"><a href="#">Mi Perfil</a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="index.html">Inicio </a></li>
                        <li role="presentation"><a href="notify.php">Notificaciones </a></li>
                        <li role="presentation"><a href="cambiar-password.php">Mi Cuenta</a></li>
                        <li role="presentation"><a href="logout.php">Cerrar Sesión </a></li>
                    </ul>
                </div>
            </form>
        </div>
        <hr>
    </header>
    <div>
        <nav class="navbar navbar-default hidden-xs navigation-clean">
            <div class="container">
                <div class="navbar-header"><a class="navbar-brand navbar-link" href="#"><i class="icon ion-ios-navigate"></i></a>
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
                </div>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <form class="navbar-form navbar-left">
                        <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                                        <input class="form-control sbox" type="text">
                                        <ul class="list-group autocomplete" style="position:absolute;width:100%; z-index: 100">
                                        </ul>
                        </div>
                    </form>
                    <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                        <li role="presentation"><a href="index.html">Inicio </a></li>
                        <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" href="#">Usuario <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li role="presentation"><a href="perfil.php?username=<?php echo $username;?>">Mi Perfil</a></li>
                            <li class="divider" role="presentation"></li>
                            <li role="presentation"><a href="index.html">Inicio </a></li>
                            <li role="presentation"><a href="notify.php">Notificaciones </a></li>
                            <li role="presentation"><a href="cambiar-password.php">Mi Cuenta</a></li>
                            <li role="presentation"><a href="logout.php">Cerrar Sesión </a></li>
                        </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                        <li class="active" role="presentation"><a href="index.html">Inicio</a></li>
                        <li role="presentation"><a href="notify.php">Notificaciones</a></li>
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">Usuario <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li role="presentation"><a href="perfil.php?username=<?php echo $username;?>">Mi Perfil</a></li>
                            <li class="divider" role="presentation"></li>
                            <li role="presentation"><a href="index.html">Inicio </a></li>
                            <li role="presentation"><a href="notify.php">Notificaciones </a></li>
                            <li role="presentation"><a href="cambiar-password.php">Mi Cuenta</a></li>
                            <li role="presentation"><a href="logout.php">Cerrar Sesión </a></li>
                        </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <h1> </h1></div>
    <div>
        <div class="container">
			<div class="login-clean">
				<form action="<?php if(!$tokenIsValid) { echo 'cambiar-password.php'; } else {echo 'cambiar-password.php?token='.$token.'';}?>" method="post">
					<h2 class="sr-only">Cambiar contraseña</h2>
					<div class="form-group">
						<?php if(!$tokenIsValid) {
								echo '<input class="form-control" type="password" name="oldpassword" placeholder="Contraseña actual">';
								
						} ?>
					</div>
					<div class="form-group">
						<input class="form-control" type="password" name="newpassword" placeholder="Nueva contraseña">
					</div>
					<div class="form-group">
						<input class="form-control" type="password" name="newpasswordre" placeholder="Repita contraseña">
					</div>
					<div class="form-group">
						<input class="btn btn-primary btn-block" type="submit" name="changepassword" value="Cambiar contraseña">
					</div>
				</form>
			</div>
        </div>
    </div>
    <div class="footer-dark" style="position: relative">
        <footer>
            <div class="container">
                <p class="copyright">Final WEB I - Xiomara Sawin - UCES 1° cuatrimestre 2019</p>
            </div>
        </footer>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>

</body>
</html>