<?php 
include('./classes/DB.php');
include('./classes/Login.php');
include('./classes/Notify.php');

if(Login::isLoggedIn()){
    $userid=Login::isLoggedIn(); //que userid esta iniciando sesión

} else{
    die('Sesión no iniciada');
}

if(isset($_GET['mid'])){
    $message = DB::query('SELECT * FROM messages WHERE id = :mid AND(receiver=:receiver OR sender=:sender)', array(':mid'=>$_GET['mid'], ':receiver'=>$userid, ':sender'=>$userid))[0];
    echo '<h1>Ver mensaje</h1>';
    echo htmlspecialchars($message['body']);
    echo '<hr />';

    if($message['sender'] == $userid){
        $id = $message['receiver'];
    } else {
        $id = $message ['sender'];
    }
    DB::query('UPDATE messages SET `read` = 1 WHERE id=:mid ', array(':mid'=>$_GET['mid']));
    ?>
    <form action="enviar-mensaje.php?receiver=<?php echo $id;?>" method="post">
        <textarea name="body" rows="8" cols="80"></textarea>
	    <input type="submit" name="send" value="Enviar mensaje">
    </form>
    <?php
} else {

?>

<h1>Mis mensajes</h1>

<?php 

$messages = DB::query('SELECT messages.*, users.USERNAME FROM messages, users 
                    WHERE receiver=:receiver OR sender=:sender
                    AND users.ID = messages.sender', array(':receiver'=>$userid, ':sender'=>$userid));

foreach($messages as $message){

    if(strlen($message['body']) > 10){
        $m = substr($message['body'], 0, 10)." ...";
    }else {
        $m = $message['body'];
    }

    if($message['read']==0){
        echo "<a href='mis-mensajes.php?mid=".$message['id']."'><strong>".$m."</strong><a/> - enviado por ".$message['USERNAME']."<hr />";
    } else {
        echo "<a href='mis-mensajes.php?mid=".$message['id']."'>".$m."<a/> - enviado por ".$message['USERNAME']."<hr />";
    }
}
}
?>
