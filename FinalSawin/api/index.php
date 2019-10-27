<?php

require_once("DB.php");

$db = new DB("127.0.0.1", "finalwebi", "root", "");

if($_SERVER['REQUEST_METHOD'] == "GET"){

    if($_GET['url'] == "musers"){
        //users a los que se le envia el mensaje
        $token = $_COOKIE['RSID'];
        $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

        $users = $db->query("SELECT s.username AS Sender, r.username AS Receiver, s.id AS SenderID, r.id AS ReceiverID FROM messages LEFT JOIN users s ON s.id=messages.sender LEFT JOIN users r ON r.id=messages.receiver WHERE (s.id=:userid OR r.id=:userid)", array(":userid"=>$userid));
        $us = array ();

        foreach($users as $user){
            if(!in_array(array('username'=>$user['Receiver'], 'id'=>$user['ReceiverID']), $us)){
                array_push($us, array('username'=>$user['Receiver'], 'id'=>$user['ReceiverID']));
            }
            if(!in_array(array('username'=>$user['Sender'], 'id'=>$user['SenderID']), $us)){
                array_push($us, array('username'=>$user['Sender'], 'id'=>$user['SenderID']));
            }
        }

        echo json_encode($us);
        
    } else if($_GET['url'] == "auth"){

    } else if ($_GET['url'] == "messages"){
        $sender = $_GET['sender'];

        $token = $_COOKIE['RSID'];
        $receiver = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

        $messages = $db->query('SELECT messages.id, messages.body, s.username AS sender, r.username AS receiver 
        FROM messages 
        LEFT JOIN users s ON messages.sender = s.id 
        LEFT JOIN users r ON messages.receiver = r.id 
        WHERE (r.id =:r AND s.id=:s) OR (r.id=:s AND s.id=:r)', array(':r'=>$receiver, ':s'=>$sender));

        
        echo json_encode($messages);

    } else if ($_GET['url'] == "search"){

        $tosearch = explode(" ", $_GET['query']);
		
		if(count($tosearch) == 1){
			$tosearch = str_split($tosearch[0], 2);
		}

		//buscar por post
		$whereclause = "";
		$paramsarray = array(':body'=>'%'.$_GET['query'].'%');

		for($i = 0; $i < count($tosearch); $i++){

			if($i % 2){
				$whereclause .= " OR body LIKE :p$i ";
				$paramsarray[":p$i"] = $tosearch[$i];
			}
		}
		$posts = $db->query('SELECT posts.ID, posts.BODY, users.USERNAME FROM posts, users WHERE users.ID = posts.USER_ID AND posts.BODY LIKE :body '.$whereclause.' LIMIT 10', $paramsarray);

        
		echo json_encode($posts);

    } else if ($_GET['url'] == "users"){

        $token = $_COOKIE['RSID'];
        $user_id = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
        $username = $db->query('SELECT username FROM users WHERE id=:usid', array(':usid'=>$user_id))[0]['username'];
        echo $username;

    } else if ($_GET['url'] == "comments" && isset($_GET['postid'])){
        $output = "";
        //mostrar comentarios de los posts
		$comments = $db->query('SELECT comentarios.COMMENT, users.USERNAME FROM comentarios, users WHERE post_id=:postid AND comentarios.USER_ID=users.ID', array(':postid'=>$_GET['postid']));
        $output = "[";
		foreach ($comments as $comment) {
			$output .= "{";
                $output .= '"Comment": "'.$comment['COMMENT'].'",';
                $output .= '"CommentedBy": "'.$comment['USERNAME'].'"';
            $output .= "},";
        }
        $output = substr($output, 0, strlen($output)-1);
        $output .= "]";

        echo $output;

    } else if ($_GET['url'] == "posts"){

        $token = $_COOKIE['RSID'];

        $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

        //crear timeline viendo solo a quienes sigue el usuario registrado
        $followingposts = $db->query('SELECT posts.ID, posts.BODY, posts.POSTED_AT, posts.LIKES, posts.POSTIMG, users.USERNAME FROM users, posts, seguidores
            WHERE (posts.USER_ID = seguidores.USER_ID
            OR posts.USER_ID = :userid)
            AND users.ID = posts.USER_ID
            AND seguidores.FOLLOWER_ID = :userid
            GROUP BY posts.ID DESC;', array(':userid'=>$userid), array(':userid'=>$userid));

            $response = "[";
            foreach($followingposts as $post) {
                    $response .= "{";
                            $response .= '"PostId": '.$post['ID'].',';
                            $response .= '"PostBody": "'.$post['BODY'].'",';
                            $response .= '"PostedBy": "'.$post['USERNAME'].'",';
                            $response .= '"PostDate": "'.$post['POSTED_AT'].'",';
                            $response .= '"PostImg": "'.$post['POSTIMG'].'",';
                            $response .= '"Likes": '.$post['LIKES'].'';
                    $response .= "},";
            }
            $response = substr($response, 0, strlen($response)-1);
            $response .= "]";
            http_response_code(200);
            echo $response;

    } else if ($_GET['url'] == "profilepost"){

        $userid = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

        //crear timeline viendo solo a quienes sigue el usuario registrado
        $followingposts = $db->query('SELECT posts.ID, posts.BODY, posts.POSTED_AT, posts.LIKES, posts.POSTIMG, users.USERNAME FROM users, posts
            WHERE users.ID = posts.USER_ID
            AND users.ID = :userid
            ORDER BY posts.POSTED_AT DESC;', array(':userid'=>$userid));

        $response = "[";
        foreach($followingposts as $post) {
                $response .= "{";
                        $response .= '"PostId": '.$post['ID'].',';
                        $response .= '"PostBody": "'.$post['BODY'].'",';
                        $response .= '"PostedBy": "'.$post['USERNAME'].'",';
                        $response .= '"PostDate": "'.$post['POSTED_AT'].'",';
                        $response .= '"PostImg": "'.$post['POSTIMG'].'",';
                        $response .= '"Likes": '.$post['LIKES'].'';
                $response .= "},";
        }
        $response = substr($response, 0, strlen($response)-1);
        $response .= "]";
        http_response_code(200);
        echo $response;

    }

} else if ($_SERVER['REQUEST_METHOD'] == "POST"){

    if(isset($_COOKIE['RSID'])){
        $token = $_COOKIE['RSID'];
    } else {
        die();
    }
        
        

    if($_GET['url'] == "message"){
        $userid = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
        
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $body = $postBody['body'];
        $receiver = $postBody['receiver'];

        if(strlen($Body)> 100) {
            echo " { 'Error': 'Mensaje demasiado largo'} ";
        } 

        if($body == null){
            $body = "";
        }

        if($receiver == null){
            die();
        }

        if($userid == null){
            die();
        }
        $db->query("INSERT INTO messages VALUES ('', :body, :sender, :receiver, '0')", array(':body'=>$body, ':sender'=>$userid, ':receiver'=>$receiver));
        echo " { 'Success': 'Mensaje enviado'} ";

    } else if($_GET['url'] == "users"){
        //crear cuenta 
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $username = $postBody->username;
        $email = $postBody->email;
        $password = $postBody->password;

        if(!$db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){

			if(strlen($username)>=3 && strlen($username)<=32){

				if(preg_match('/[a-zA-Z0-9_]+/', $username)){

					if(strlen($password)>=6 && strlen($password) <=60){
						if(filter_var($email, FILTER_VALIDATE_EMAIL)){

							//chequar que no haya dos emails iguales
							if(!$db->query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))){
								$db->query('INSERT INTO users VALUES(\'\', :username, :password, :email, \'\')', array('username'=>$username, 'password'=>password_hash($password, PASSWORD_BCRYPT), 'email'=>$email));

								echo '{ "Success": "¡Usuario creado!" }';
                                http_response_code(200);
							}else{
								echo '{ "Error": "Email ya se encuentra registrado." }';
                                http_response_code(409);
							}
							
						}else{
							echo '{ "Error": "Email inválido." }';
                            http_response_code(409);
						}
					}else{
						echo '{ "Error": "Longitud de contraseña inválida. Debe tener más de 6 caracteres" }';
                        http_response_code(409);
					}
	
				}else {
					echo '{ "Error": "Caracteres inválidos en la contraseña." }';
                    http_response_code(409);
				}
			}else {
                echo '{ "Error": "Longitud de usuario inválida." }';
                http_response_code(409);
			}

			
		}else{
            echo '{ "Error": "El usuario ya se encuentra registrado." }';
            http_response_code(409);
		}	
        
    }

    //iniciar sesión
    if($_GET['url'] == "auth"){
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $username = $postBody->username;
        $password = $postBody->password;

        if($db->query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {
            if(password_verify($password, $db->query('SELECT `password` FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])){
                $cstrong = True;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));

                $user_id = $db->query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];

                $db->query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

                //cookies para loggear al user. RSID=RedSocialID. token valid for 1 week. cookie valid everywhere. domain cookie is valid NULL. javascript cant access cookie.
				setcookie("RSID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);

				//set a kinda remember me cookie. cookie will expire every 3 days. whenever it does, a new RSID cookie needs to be created. security reasons mate x2.

				setcookie("RSID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

                echo '{ "Token": "'.$token.'"}';
            } else {
                echo '{ "Error": "Nombre de usuario o contraseña incorrecta."}';
                http_response_code(401);
            }
        } else {
            echo '{ "Error": "Nombre de usuario o contraseña incorrecta."}';
            http_response_code(401);
        }
    
    } else if($_GET['url'] == "logout"){

    }else if ($_GET['url'] == "likes"){

        $postId = $_GET['id'];
        $token = $_COOKIE['RSID'];
        $likerId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

        //checking if user already liked the post.
			if(!$db->query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId))){

				//updating number of likes for current post
				$db->query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$postId));

				//which user liked the post
				$db->query('INSERT INTO post_likes VALUES(\'\', :postid, :userid)', array(':postid'=>$postId, ':userid'=>$likerId));
			
				//Notify::createNotify("", $postid);
			
			}else {
				//unliking post

				//updating number of likes for current post
				$db->query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$postId));

				//which user liked the post
				$db->query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$postId, ':userid'=>$likerId));
            }
            
            echo "{";
                echo '"Likes":';
                echo $db->query('SELECT likes FROM posts WHERE id=:postid', array(':postid'=>$postId))[0]['likes'];
            echo "}";

        } else if ($_GET['url'] == "deletepost" && isset($_GET['postid'])){
            
            $token = $_COOKIE['RSID'];
            $userId = $db->query('SELECT user_id FROM login_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];

            if($db->query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$userId))){
                //TODO:hacer query de borrar
                $db->query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$userId));

                $db->query('DELETE FROM post_likes WHERE post_id = :postid', array(':postid'=>$_GET['postid']));
                
                echo "Post eliminado";
            }
        }


} else if ($_SERVER['REQUEST_METHOD' == "DELETE"]){
       if($_GET['url'] == "auth"){
            if(isset($_GET['token'])) {
                if($db->query("SELECT token FROM login_tokens WHERE token=:token", array(':token'=>sha1($_GET['token'])))) {
                    $db->query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_GET['token'])));

                    echo '{ "Status": "Éxito" }';
                    http_response_code(200);
                } else {
                    echo '{ "Error": "Token inválido" }';
                    http_response_code(400);
                }
            } else {
                echo '{ "Error": "Malformed request" }';
                http_response_code(400);
            }
        }
    } else {
        http_response_code(405);
    }



?>