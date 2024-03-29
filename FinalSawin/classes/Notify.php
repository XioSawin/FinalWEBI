<?php
class Notify {
    public static function createNotify($text = "", $postid = 0){
        $text = explode(" ", $text);
        $notify = array();

        foreach($text as $word){
            
            if(substr($word, 0, 1) == "@"){

                $notify[substr($word, 1)] = array("type"=>1, "extra"=>' {"postbody": "'.htmlentities(implode(" ", $text)).'"} ');
                
            }	
        }

        if(count($text)== 1 && $postid != 0 ){
            
            $temp = DB::query('SELECT posts.USER_ID AS receiver, post_likes.USER_ID AS sender FROM posts, post_likes WHERE posts.ID = post_likes.POST_ID AND posts.ID=:postid', array(':postid'=>$postid));

            $r = $temp[0]["receiver"];
            $s = $temp[0]["sender"];

            DB::query('INSERT INTO notificaciones VALUES (\'\', :type, :receiver, :sender, :extra)', array(':type'=>2, ':receiver'=>$r, ':sender'=>$s, ':extra'=>""));
        }
        return $notify;	
    }
}
?>