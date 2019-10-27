<?php

class Comment{
	public static function createComment($commentbody, $postId, $userId){

		//check if comment is the right lenght -> <=260

		if(strlen($commentbody)>260 || strlen($commentbody)<1){
			die('Incorrect lenght');

		}
		
		//check if post id es valido. if it is, inserta comentario relacionado en db.
		if(!DB::query('SELECT id FROM posts WHERE id=:postid', array(':postid'=>$postId))){
			echo 'Invalid post ID';
		}else{
			DB::query('INSERT INTO comentarios VALUES(\'\', :comment, :userid, NOW(), :postid)', array(':comment'=>$commentbody, ':userid'=>$userId, ':postid'=>$postId));
		}
	}

	public static function displayComments($postId){
		//mostrar comentarios de los posts

		$comments = DB::query('SELECT comentarios.COMMENT, users.USERNAME FROM comentarios, users WHERE post_id=:postid AND comentarios.USER_ID=users.ID', array(':postid'=>$postId));

		foreach ($comments as $comment) {
			
			echo $comment['COMMENT']." ~ ".$comment['USERNAME']."<hr />";

		}
	}
}

?>