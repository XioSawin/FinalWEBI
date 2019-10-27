<?php 

//uso de sesiones para CSRF protection
session_start();
$cstrong = True;
$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

if(!isset($_SESSION['token'])){
    $_SESSION['token']= $token;
}

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Notify.php');

if(Login::isLoggedIn()){
    $userid=Login::isLoggedIn(); //que userid esta iniciando sesión

} else{
    die('Sesión no iniciada');
}

if(isset($_POST['send'])){

    //security mate
    if(!isset($_POST['nocsrf'])){
        die('Token inválido');
    }
    
    if($_POST['nocsrf'] != $_SESSION['token']){
        die("Token inválido");
    }

    //check que receptor del mensaje existe en la base de datos
    if(DB::query('SELECT id FROM users WHERE id=:receiver', array(':receiver'=>$_GET['receiver']))){
        DB::query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, 0)", array(':body'=>$_POST['body'], ':sender'=>$userid, ':receiver'=>htmlspecialchars($_GET['receiver'])));
        echo "Mensaje enviado.";
    } else {
        die('ID no es válido.');
    }
    
    session_destroy();
}

?>


<h1>Enviar un mensaje</h1>
<form action="enviar-mensaje.php?receiver=<?php echo htmlspecialchars($_GET['receiver']);?>" method="post">
    <textarea name="body" rows="8" cols="80"></textarea>
    <input type="hidden" name="nocsrf" value="<?php echo $_SESSION['token'] ?>">
	<input type="submit" name="send" value="Enviar mensaje">
</form>
