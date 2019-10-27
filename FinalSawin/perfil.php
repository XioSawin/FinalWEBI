<?php
	include('./classes/DB.php');
	include('./classes/Login.php');
	include('./classes/Post.php');
	include('./classes/Image.php');
	include('./classes/Notify.php');

	$username = "";
	$isFollowing = false;

	if(isset($_GET['username'])){
		if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){

			$username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];

			//get userid == al que aparece en la página & follower's id == a quién esta logueado.

			$userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];

			$followerid = Login::isLoggedIn();



			//check if form is being submitted
			if(isset($_POST['follow'])){

				//para que usuario no se pueda seguir a si mismo
				if($userid != $followerid){
					//check if it is not a follower
					if(!DB::query('SELECT follower_id FROM seguidores WHERE `user_id` =:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))){
						 //add follower relationship
						DB::query('INSERT INTO seguidores VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
					} else {
						echo 'Ya sigues a este usuario.';
					}

					$isFollowing = True;
					}
				
			}

			//check if it is a follower
				if(DB::query('SELECT follower_id FROM seguidores WHERE `user_id`=:userid', array(':userid'=>$userid))){
					
					//echo 'Ya sigues a este usuario.';
					$isFollowing = True;
				}

			//si ya sigue al usuario, dar opción de unfollow
			if(isset($_POST['unfollow'])){

				if($userid != $followerid){
					//check if it is a follower
					if(DB::query('SELECT follower_id FROM seguidores WHERE `user_id`=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))){

						 //delete follower relationship
						DB::query('DELETE FROM seguidores WHERE `user_id`=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
					} 

					$isFollowing = False;
					}
				
			}

			if(isset($_POST['deletepost'])){
				
				if(DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))){

					DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));

					DB::query('DELETE FROM post_likes WHERE post_id = :postid', array(':postid'=>$_GET['postid']));

					echo 'Post deleted';
				}				

			}
				
			if(isset($_POST['post'])){

				if($_FILES['postimg']['size'] == 0){
					//posting
					Post::createPost($_POST['postbody'], Login::isLoggedIn(), $userid);
				}else {

					//latest post id
					$postid = Post::createImgPost($_POST['postbody'], Login::isLoggedIn(), $userid);

					Image::uploadImage('postimg',"UPDATE posts SET postimg =:postimg WHERE id=:postid", array(':postid'=>$postid));
				}

				
				
			}

			if(isset($_GET['postid']) && !isset($_POST['deletepost']) ){
				//liking posts
				Post::likePost($_GET['postid'], $followerid);

			}

			$posts = Post::displayPosts($userid, $username, $followerid);

		}

	}else{
	
		die('Usuario no encontrado');
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username; ?> - Final WEB I</title>
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
                        <li role="presentation"><a href="mensajes.html">Mensajes</a></li>
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
        <h1>@<?php echo $username; ?> </h1></div>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item"><span><strong>Bienvenido a mi perfil</strong></span>
                            <p>¡Hola! Soy @<?php echo $username; ?></p>
                            <form action="perfil.php?username=<?php echo $username; ?>" method="post">
                                    <?php
                                    if ($userid != $followerid) {
                                            if ($isFollowing) {
                                                    echo '<input type="submit" name="unfollow" value="Dejar de seguir">';
                                            } else {
                                                    echo '<input type="submit" name="follow" value="Seguir">';
                                            }
                                    }
                                    ?>
                            </form>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
						<div class="timelineposts">

						</div>  
                    </ul>
				</div>
				<div class="modal fade" id="commentsmodal" role="dialog" tabindex="-1" style="padding-top:100px;" data-aos="fade-up">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title">Comentarios</h4></div>
                            <div class="modal-body" style="max-height: 400px; overflow-y:auto">
                            <form></form>
								<textarea name="comentario"></textarea>
							</div>
							<div class="modal-footer">
                                <input type="comentario" name="comment" value="Comentar" class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">

								<button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
								
							</div>
						</div>
					</div>
                </div>
                <div class="modal fade" id="newpost" role="dialog" tabindex="-1" style="padding-top:100px;" data-aos="fade-up">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title">Nuevo post</h4></div>
							<div style="max-height: 400px; overflow-y:auto">
								<!--form para subir posts c/ imágenes -->
                                <form action="perfil.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                                    <textarea name="postbody" rows="8" cols="80"></textarea>
                                    <br /> Añadir una imagen:
                                    
                                    <input type="file" name="postimg">	         
							</div>
							<div class="modal-footer">
                                <input type="submit" name="post" value="Postear" class="btn btn-default" type="button" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">
								<button class="btn btn-default" type="button" data-dismiss="modal" style="background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;">Cerrar</button>
								<!--<button class="btn btn-primary" type="button">Enviar</button>-->
                                </form>
                            </div>
						</div>
					</div>
				</div>
                <div class="col-md-3">
                    <button class="btn btn-default" type="button" style="width:100%;background-image:url(&quot;none&quot;);background-color:#da052b;color:#fff;padding:16px 32px;margin:0px 0px 6px;border:none;box-shadow:none;text-shadow:none;opacity:0.9;text-transform:uppercase;font-weight:bold;font-size:13px;letter-spacing:0.4px;line-height:1;outline:none;" onclick="showNewPost()">Nuevo Post</button>
                    <ul class="list-group"></ul>
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

    <script type="text/javascript">

    //para hacer foco en el post rtdo de la búsqueda.
    function scrollToAnchor(aid){
        var aTag = $(aid);
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
    }
            $(document).ready(function() {

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

                    $.ajax({
                            type: "GET",
                            url: "api/profilepost?username=<?php echo $username; ?>",
                            processData: false,
                            contentType: "application/json",
                            data: '',
                            success: function(r) {
                                    var posts = JSON.parse(r)
                                    $.each(posts, function(index) {
                                            if (posts[index].PostImage == "") {
                                                    $('.timelineposts').html(
                                                            $('.timelineposts').html() +
                                                            '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><footer>Posteado por '+posts[index].PostedBy+' el '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id="'+posts[index].PostId+'"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid="'+posts[index].PostId+'" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" onclick="borrar('+posts[index].PostId+')"><i class="glyphicon glyphicon-trash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Borrar</span></button></footer></blockquote></li>'
                                                    )
                                            } else {
                                                    $('.timelineposts').html(
                                                            $('.timelineposts').html() +
                                                            '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImg+'" class="postimg" id="img'+posts[index].PostId+'"><footer>Posteado por '+posts[index].PostedBy+' el '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id="'+posts[index].PostId+'"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" onclick="borrarImagen('+posts[index].PostId+')" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-trash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Borrar</span></button></footer></blockquote></li>'
                                                    )
                                            }
                                            
                                            
                                            
                                            $('[data-id]').click(function() {
                                                    var buttonid = $(this).attr('data-id');
                                                    $.ajax({
                                                            type: "POST",
                                                            url: "api/likes?id=" + $(this).attr('data-id'),
                                                            processData: false,
                                                            contentType: "application/json",
                                                            data: '',
                                                            success: function(r) {
                                                                    var res = JSON.parse(r)
                                                                    $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+res.Likes+' Likes</span>')
                                                            },
                                                            error: function(r) {
                                                                    console.log(r)
                                                            }
                                                    });
                                            })
                                    })
                                    $('.postimg').each(function() {
                                            this.src=$(this).attr('data-tempsrc')
                                            this.onload = function() {
                                                    this.style.opacity = '1';
                                                    this.style.width = '100%';
                                            }
                                    })
                                    scrollToAnchor(location.hash)
                            },
                            error: function(r) {
                                    console.log(r)
                            }
                    });
            });
            function borrar(pid) {
                var txt;
                var r = confirm("¿Está seguro que quiere eliminar este post?");                
                if (r == true) {
                    $.ajax({
                                                        type:"POST",
                                                        url:"api/deletepost?postid=" +pid,
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r){
                                                            location.reload()
                                                        },
                                                        error: function(r){
                                                            console.log(r)
                                                        }
                                                    })
                } else {
                    txt = "You pressed Cancel!";
                }
                //document.getElementById("demo").innerHTML = txt;
            }
            function borrarImagen(pid) {
                var txt;
                var r = confirm("¿Está seguro que quiere eliminar este post?");
                if (r == true) {
                    $.ajax({
                                                        type:"POST",
                                                        url:"api/deletepost?postid=" +pid,
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r){
                                                            location.reload()
                                                        },
                                                        error: function(r){
                                                            console.log(r)
                                                        }
                                                    })
                } else {
                    txt = "You pressed Cancel!";
                }
                //document.getElementById("demo").innerHTML = txt;
            }
            function showNewPost() {
                    $('#newpost').modal('show')
            }
            function showCommentsModal(res) {
                    $('#commentsmodal').modal('show')
                    var output = "";
                    for (var i = 0; i < res.length; i++) {
                            output += res[i].Comment;
                            output += " ~ ";
                            output += res[i].CommentedBy;
                            output += "<hr />";
                    }
                    $('.modal-body').html(output)
            }

    </script>

</body>
</html>