<?php

include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Image.php');


if(Login::isLoggedIn()){
	//echo 'Sesión iniciada';
	$userid=Login::isLoggedIn(); //que userid esta iniciando sesión	

} else{
	die('Sesión no iniciada');
}

if(isset($_POST['uploadprofileimg'])){

	Image::uploadImage('profileimg',"UPDATE users SET profileimg = :profileimg WHERE id=:userid", array(':userid'=>$userid));

?>


<h1>Mi cuenta</h1>

<form action="mi-cuenta.php" method="post" enctype="multipart/form-data">
	Actualizar imagen de perfil:
	
	<input type="file" name="profileimg">	
	<input type="submit" name="uploadprofileimg" value="Actualizar imagen">

</form>