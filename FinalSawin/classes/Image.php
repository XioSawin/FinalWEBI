<?php 

class Image{

	public static function uploadImage($formname, $query, $params){
			//obtener la imagen
		$image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));

		$options = array('http'=>array(

			'method'=>"POST",
			'header'=>"Authorization: Bearer d3444866bb3d93d142cceb5f5a60ea3ec2a97230\n".
					"Content-type: application/x-www-form-urlencoded",
			'content'=>$image
		));

		$context = stream_context_create($options);

		$imgurURL = "https://api.imgur.com/3/image";

		//chequear que archivo img no sea más grande que 10MB
		if($_FILES[$formname]['size'] > 1024000){

			die('Imagen muy grande. El tamaño debe ser de 10MB o menos.');

		}

		//cargar imagen al hosting: imgur
		$response = file_get_contents($imgurURL, false, $context);

		$response = json_decode($response);

		//hacer el upload de las imagenes de manera dinámica.

		$preparams = array($formname=>$response->data->link);

		$params = $preparams + $params;

		//linkear profile img con el usuario en DB
		DB::query($query, $params);
		}
	}


?>
