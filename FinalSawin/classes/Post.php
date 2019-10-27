<?php 
	class Post{

		public static function createPost($postbody, $loggedinUserId, $perfilUserId){

			//check if post is the right lenght -> <=260

			if(strlen($postbody)>260 || strlen($postbody)<1){

				die('Incorrect lenght');

			}

			$topics = self::getTopics($postbody);


			if($loggedinUserId == $perfilUserId){

				//enviar notificaciones a otros usuarios
				if(count(Notify::createNotify($postbody)) !=0){

					foreach (Notify::createNotify($postbody) as $key => $n) {

						$s = $loggedinUserId;					
						$r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];

						if($r != 0){

							DB::query('INSERT INTO notificaciones VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));

						}
					}
				}


				//pasar post a db con timestamp de posteo
				DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0, \'\', :topics)', array(':postbody'=>$postbody, ':userid'=>$perfilUserId, ':topics'=>$topics));
			} else {
				die('Incorrect user');
			}
		}

		public static function createImgPost($postbody, $loggedinUserId, $perfilUserId){

			//check if post is the right lenght -> <=260

			if(strlen($postbody)>260){

				die('Incorrect lenght');

			}

			$topics = self::getTopics($postbody);

			if($loggedinUserId == $perfilUserId){

				//enviar notificaciones a otros usuarios
				if(count(Notify::createNotify($postbody)) !=0){

					foreach (Notify::createNotify($postbody) as $key => $n) {

						$s = $loggedinUserId;					
						$r = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$key))[0]['id'];

						if($r != 0){

							DB::query('INSERT INTO notificaciones VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>$n["type"], ':receiver'=>$r, ':sender'=>$s, ':extra'=>$n["extra"]));

						}
					}
				}

				//pasar post a db con timestamp de posteo
				DB::query('INSERT INTO posts VALUES(\'\', :postbody, NOW(), :userid, 0, \'\', \'\')', array(':postbody'=>$postbody, ':userid'=>$perfilUserId));
				$postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY id DESC LIMIT 1;', array(':userid'=>$loggedinUserId))[0]['id'];

				return $postid;

			} else {
				die('Incorrect user');
			}
		}

		public static function likePost($postid, $likerId){
			//checking if user already liked the post.
			if(!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$likerId))){

				//updating number of likes for current post
				DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postid));

				//which user liked the post
				DB::query('INSERT INTO post_likes VALUES(\'\', :postid, :userid)', array(':postid'=>$postid, ':userid'=>$likerId));
			
				Notify::createNotify("", $postid);
			
			}else {
				//unliking post

				//updating number of likes for current post
				DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postid));

				//which user liked the post
				DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postid, ':userid'=>$likerId));
			}
		
		}

		public static function getTopics($text){

			$text = explode(" ", $text);

			$topics = "";

			foreach($text as $word){
			
				if(substr($word, 0, 1) == "#"){
					//para chequear hashtags con #.
					$topics .= substr($word, 1).",";	
				}
			}
			return $topics;
		}

		
		public static function link_add($text){

			$text = explode(" ", $text);
			$newstring = "";

			foreach($text as $word){
				
				if(substr($word, 0, 1) == "@"){
					//para taggear otros usuarios en los comentarios con @.
					$newstring .= "<a href='perfil.php?username=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";
				}else if(substr($word, 0, 1) == "#"){
					//para chequear hashtags con #.
					$newstring .= "<a href='topics.php?topic=".substr($word, 1)."'>".htmlspecialchars($word)." </a>";
				}else {
					$newstring .= htmlspecialchars($word)." ";
				}			
			}

			return $newstring;
		}



		public static function displayPosts($userid, $username, $loggedinUserId){
			$dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
			$posts = "";

			foreach ($dbposts as $pst) {

				//if post not liked - show like button
				if(!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$pst['ID'], ':userid'=>$loggedinUserId))){

					$posts .= "<img src='".$pst['POSTIMG']."'>".self::link_add($pst['BODY'])."
					<form action='perfil.php?username=$username&postid=".$pst['ID']."' method='post'>
                                <input type='submit' name='like' value='Like'>
                                <span>".$pst['LIKES']." likes</span>";
                	if ($userid == $loggedinUserId){
                		$posts.= "<input type='submit' name='deletepost' value='x' />";
                	}

                	$posts .= "
							</form><hr /></br />";
				}else {
					$posts .= "<img src='".$pst['POSTIMG']."'>".self::link_add($pst['BODY'])."
					<form action='perfil.php?username=$username&postid=".$pst['ID']."' method='post'>
                                <input type='submit' name='unlike' value='Unlike'>
                                <span>".$pst['LIKES']." likes</span>
                                ";
                    if ($userid == $loggedinUserId){
                		$posts.= "<input type='submit' name='deletepost' value='x' />";
                	}

                	$posts .="
							</form><hr /></br />";
				}
			}

			return $posts;
		}

		
		
	}

?>