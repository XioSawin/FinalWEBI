<?php
	include('./classes/DB.php');
	include('./classes/Login.php');
	include('./classes/Post.php');
	include('./classes/Comment.php');
	include('./classes/Notify.php');

	$showTimeline = False;

	if(Login::isLoggedIn()){
		//echo 'Sesión iniciada';
		$userid=Login::isLoggedIn(); //que userid esta iniciando sesión

		$showTimeline = True;

	} else{
		die('Sesión no iniciada');
	}

	if(isset($_GET['postid'])){
				//liking posts
		Post::likePost($_GET['postid'], $userid);

	}

	//verif if se postearon comentarios
	if(isset($_POST['comment'])){
		Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
	}

	//logica del buscador. para que no sea necesario buscar la palabra entera, o que la palabra entera esté bien.
	if(isset($_POST['searchbox'])){
		$tosearch = explode(" ", $_POST['searchbox']);
		
		if(count($tosearch) == 1){
			$tosearch = str_split($tosearch[0], 2);
		}

		//buscar por username
		$whereclause = "";
		$paramsarray = array(':username'=>'%'.$_POST['searchbox'].'%');

		for($i = 0; $i <count($tosearch); $i++){
			$whereclause .= " OR username LIKE :u$i ";
			$paramsarray[":u$i"] = $tosearch[$i];
		}

		$users = DB::query('SELECT users.USERNAME FROM users WHERE users.USERNAME LIKE :username '.$whereclause.'', $paramsarray);

		print_r($users);

		//buscar por post
		$whereclause = "";
		$paramsarray = array(':body'=>'%'.$_POST['searchbox'].'%');

		for($i = 0; $i <count($tosearch); $i++){

			if($i % 2){
				$whereclause .= " OR body LIKE :p$i ";
				$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
		$posts = DB::query('SELECT posts.BODY FROM posts WHERE posts.BODY LIKE :body '.$whereclause.'', $paramsarray);

		echo '<pre>';
		print_r($posts);
		echo '</pre>';
	}
?>

<!--search bar-->
<form action="index.php" method="post">
	<input type="text" name="searchbox" value="">
	<input type="submit" name="search" value="Buscar">

</form>

<?php

	//crear timeline viendo solo a quienes sigue el usuario registrado
	$followingposts = DB::query('SELECT posts.ID, posts.BODY, posts.LIKES, users.USERNAME FROM users, posts, seguidores
		WHERE posts.USER_ID = seguidores.USER_ID
		AND users.ID = posts.USER_ID
		AND FOLLOWER_ID = :userid
		ORDER BY posts.LIKES DESC;', array(':userid'=>$userid));

	foreach ($followingposts as $post) {

		echo $post['BODY']." ~ ".$post['USERNAME'];
		echo "<form action='index.php?postid=".$post['ID']."' method='post'>";

		if(!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$post['ID'], ':userid'=>$userid))){

           	echo"<input type='submit' name='like' value='Like'>";

		} else {
			echo"<input type='submit' name='unlike' value='Unlike'>";
		}  

		//form comentarios

		echo "<span>".$post['LIKES']." likes</span>
     		</form>

    		

 			<form action='index.php?postid=".$post['ID']."' method='post'>
    		<textarea name='commentbody' rows='3' cols='50'></textarea>
			<input type='submit' name='comment' value='Comentar'>

          	</form>
          	";

          	Comment::displayComments($post['ID']);

		echo "<hr /></br />";          
		
	}
?>