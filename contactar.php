<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

		<title>Comercial Dizma S.L. - Quienes somos</title>
		<meta name="description" content="" />
		<meta name="author" content="Usuario" />

		<meta name="viewport" content="width=device-width; initial-scale=1.0" />

		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
		
		<link href="somos.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="centrador">
			<header>
				
				<a href="index.php"><img src="img/logo.jpg" class="logo" alt="logo comercial Dizma S.L" width="220" border="0"></a> 
				<h1>Contacto</h1>
				
			</header>

		
			<div id="contenido">		
		
				<h3>Contacta con nosotros:</h3>

<?php
require_once 'includes/class.html_mail.php';
require_once 'includes/mysql.php';
if($_POST['accion']=='enviar' && $_POST['email']) {
	
	$mes="";
	$mes .= "<p>Nombre: {$_POST['nombre']}</p>
			<p>E-mail: {$_POST['email']}</p>
			<p>Teléfono: {$_POST['telefono']}</p>
			<p>Mensaje:<br>
			{$_POST['nombre']}</p>
	";
	
	$mail_cliente = new Html_mail($mes,"Formulario de contato",false, false, $_POST['email']);
	if($mail_cliente->error) echo $mail_cliente->error;
	else echo "Mensaje enviado correctamente. Pronto nos pondremos en contacto con ustedes.";
	
} else {
	include 'form_contactar.php';
}

?>

<div style="clear: both;"></div>
				</div>
				
				
				<p>
					Si quieres contactar con nosotros, resolver cualquier duda, conocer el estado de tu pedido…<br> la forma más rápida es por correo electrónico: <a href="mailto:comercial@dizma.es" style="color: #000;">comercial@dizma.es</a><br><br>
					Si prefieres hablar con nosotros, puedes llamarnos directamente al teléfono 91 319 03 37 de lunes a viernes de 10:00 a 19:00 h.

				</p>
				
			</div>
		</div>
		
	</body>
</html>
