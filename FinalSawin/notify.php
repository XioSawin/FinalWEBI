<?php

include('./classes/DB.php');
include('./classes/Login.php');

if(Login::isLoggedIn()){
	$userid = Login::isLoggedIn();
} else {
	echo 'Sesión no iniciada';
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Final WEB I</title>
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
                        <li role="presentation"><a href="perfil.php?username=<?php echo $username;?>">Mi Perfil</a></li>
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
                        <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" href="#">User <span class="caret"></span></a>
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
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">User <span class="caret"></span></a>
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
        <h1>Notificaciones </h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group">
                      <?php
						if(DB::query('SELECT * FROM notificaciones WHERE receiver=:userid', array(':userid'=>$userid))){

							$notifications = DB::query('SELECT * FROM notificaciones WHERE receiver=:userid', array(':userid'=>$userid));

							foreach ($notifications as $n) {
								
								if($n['TYPE'] == 1){
									$senderName = DB::query('SELECT username FROM users WHERE id=:senderid', array(':senderid'=>$n['SENDER']))[0]['username'];
									
									if($n['extra']==""){
									
									} else {
										$extra = json_decode($n['extra']);
									
										echo '<li class="list-group-item"><span>'.$senderName." te ha mencionado en un <a href='perfil.php?username=$senderName'>post</a>! - ".$extra->postbody.'</span></li>';
									}
									
									
								}else if($n['TYPE'] == 2){
                                    $senderName = DB::query('SELECT username FROM users WHERE id=:senderid ORDER BY id DESC', array(':senderid'=>$n['SENDER']))[0]['username'];
                                    $receiverName = DB::query('SELECT username FROM users WHERE id=:receiverid ORDER BY id DESC', array(':receiverid'=>$n['RECEIVER']))[0]['username'];
									echo '<li class="list-group-item"><span>'.$senderName.' le ha dado ME GUSTA a tu <a href="perfil.php?username='.$receiverName.'">post</a>. </span></li>';
								}
							}

						}

						?>
                    </ul>
                </div>
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
    <script>
        $(document).ready(function(){
            $('.sbox').keyup(function() {
                                $('.autocomplete').html("")
                                $.ajax({
                                        type: "GET",
                                        url: "api/search?query=" + $(this).val(),
                                        processData: false,
                                        contentType: "application/json",
                                        data: '',
                                        success: function(r) {
                                                r = JSON.parse(r)
                                                for (var i = 0; i < r.length; i++) {
                                                        console.log(r[i].BODY)
                                                        $('.autocomplete').html(
                                                                $('.autocomplete').html() +
                                                                '<a href="perfil.php?username='+r[i].USERNAME+'#'+r[i].ID+'"><li class="list-group-item"><span>'+r[i].BODY+'</span></li></a>'
                                                        )
                                                }
                                        },
                                        error: function(r) {
                                                console.log(r)
                                        }
                                })
                        })
        })
    </script>
</body>

</html>