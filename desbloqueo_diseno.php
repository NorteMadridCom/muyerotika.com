<?php

session_start();

require_once 'includes/mysql.php';
require_once 'includes/class.enviar_mail.php';

if(!$_SESSION['intentos']) $_SESSION['intentos']=1; 
else $_SESSION['intentos']++;

//echo $_SESSION['intentos'];

?>


<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
<link href="estilo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="centrador_web">
		<a href="index.php"><img src="img/logo.jpg" class="logo" alt="logo comercial Dizma S.L" width="220" border="0" style="margin-left: 380px;"></a>
		
		<!-- RECUADRO IZQ - BUSCADOR Y DUDAS -->
		<div id="recuadro_izq" style="float: right; margin-right: 50px;">
			
			
			<!-- BOCADILLO DUDAS -->
			<!-- <div id="duda">
				<p class="txt_duda">¿Tienes alguna duda? <a href="#" class="duda">haz clik aquí</a></p>
			</div>-->
		</div>
<img src="img/linea.jpg" class="linea" alt="linea separadora Dizma S.L" border="0" width="900">		
<?php

if($_GET['a']==1) {
	
	if($_GET['b']==1) {
		
		
		if ($_GET['c']==1) {
			
			echo '<center><h4 style="font-family: arial;">Ya puede realizar sus compras en <a href="http://www.dizma.es/" style="color: #FF3399;">DIZMA</a>.</h4></center>';
			
		} else echo '<center><h4  style="font-family: arial;">Hay un error. Vuelva a intentralo.</h4><a style="color: #FF3399; font-weight: bold;" href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
				
	} else {

		?>
				
		<center style="margin-top: 20px;">
		<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="idcliente" value="<?php echo $desbloqueo->registros[0]->idcliente; ?>"/>
		<input type="hidden" name="comp" value="<?php echo $desbloqueo->registros[0]->cp; ?>" />
		<label style="font-family: arial; font-weight: bold;">Código Postal: </label><input type="text" name="cp" value="" size="7" maxlength="5" pattern="[0-9]{5}" required  class="datos_cesta"/><br>
		<label style="font-family: arial;  font-weight: bold;">¿Cuánto es?: </label><input type="text" name="suma" value=""  class="datos_cesta" style="margin-left: 10px;" size="10" maxlength="2" pattern="[0-9]+" placeholder="<?php echo "{$_SESSION['uno']} más {$_SESSION['dos']}"; ?>" required/><br>
		<input type="submit" name="accion" value="Activar" class="cp" />
		</form>	
		</center>

		<?php	
		

		
	}
	
} else {
	
	echo '<center><h4  style="font-family: arial;">¡Demasiados intentos para una pregunta tan sencilla!<br>';
	if($_GET['b']==1) {
		echo "¡SU CUENTA {$_GET['usuario']} HA SIDO BLOQUEADA Y DEBERÁ PONERSE EN CONTACTO CON EL ADMINISTRADOR DEL SISTEMA!";
		//enviar un mail a david
	} else {
		echo '¿A qué esta usted jugando, estamos registrando y bloqueando su IP?</h4></center>';
	}
	
}

?>


</body>
</html>
