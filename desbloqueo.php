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

if($_SESSION['intentos']<4) {
	
	if($_SERVER['REQUEST_METHOD']=='POST') {
		
		$_SESSION['intentos']--;
		//var_dump($_POST);
		
		if ($_POST['suma'] == intval($_SESSION['uno'] + $_SESSION['dos']) && $_POST['comp'] == $_POST['cp'] && $_POST['idcliente']) {
			$sql_desbloquear="UPDATE clientes SET desbloqueo=NULL WHERE idcliente = '{$_POST['idcliente']}';";	
			$desbloquerar = new Mysql;
			$desbloquerar->resultado_consulta($sql_desbloquear);
			$sql_datos="SELECT * FROM clientes WHERE idcliente = '{$_POST['idcliente']}';";	
			$desbloquerar->ejecutar_consulta($sql_datos);
			echo '<center><h4 style="font-family: arial;">Ya puede realizar sus compras en <a href="http://www.dizma.es/" style="color: #FF3399;">WWW.DIZMA.ES</a>.</h4></center>';
			$mensaje = '
			<html>
				<head>
				   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
				</head>
				<body>
					<p>Un cliente ha mandado la verificación de su cuenta. Debe entrar en su panel de control para asignarle un grupo al usuario.</p>
					<p>Usuario/email: '.$desbloquerar->registros[0]->usuario.'<br>
					Nombre: '.$desbloquerar->registros[0]->nombre.'<br>
					NIF: '.$desbloquerar->registros[0]->nif.'<br>
					Domicilio: '.$desbloquerar->registros[0]->domicilio.'<br>
					CP: '.$desbloquerar->registros[0]->cp.'<br>
					Población: '.$desbloquerar->registros[0]->poblacion.'<br>
					Provincia: '.$desbloquerar->registros[0]->provincia.'<br>
					Teléfono1: '.$desbloquerar->registros[0]->telefono1.'<br>
					Teléfono2: '.$desbloquerar->registros[0]->telefono2.'<br>
					Móvil: '.$desbloquerar->registros[0]->movil.'</p>
				</body>
			</html>
			';
			
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=utf-8";
			$headers[] = "From: {$_SERVER['SERVER_NAME']} <web@{$_SERVER['SERVER_NAME']}>";
			$headers[] = "X-Mailer: PHP/" . phpversion();

			$cabeceras=implode("\r\n", $headers);
			//$mail_desbloqueo = new Enviar_mail('nortemadrid@nortemadrid.com', 'Nuevo usuario registrado', $mensaje ,'comercial@dizma.es');
			mail('comercial@dizma.es', 'Nuevo usuario registrado', $mensaje, $cabeceras);
			$desbloquerar->__destruct();
		} else echo '<center><h4  style="font-family: arial;">Hay un error. Vuelva a intentralo.</h4><a style="color: #FF3399; font-weight: bold;" href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
				
	} else { //llamada por GET
		
		$desbloqueo = new Mysql;
		
		$sql_bloqueo_permanente = "SELECT idcliente FROM clientes WHERE eliminado=1 AND usuario = '{$_GET['usuario']}' AND desbloqueo=0;";
		$desbloqueo->ejecutar_consulta($sql_bloqueo_permanente);
		
		if($desbloqueo->numero_registros>0) 
			die ('¡¡CUENTA BLOQUEADA!!');
		
		$sql_desbloqueo = "SELECT idcliente, cp FROM clientes WHERE eliminado=0 AND usuario = '{$_GET['usuario']}' AND desbloqueo='{$_GET['desbloqueo']}';";
		
		
		$desbloqueo->ejecutar_consulta($sql_desbloqueo);
		
		if ($desbloqueo->numero_registros == 1) {
			
			$_SESSION['uno']= intval(rand(1, 20));
			$_SESSION['dos'] = intval(rand(1, 20));
			
			
		?>
				
		<center style="margin-top: 20px;">
		<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="idcliente" value="<?php echo $desbloqueo->registros[0]->idcliente; ?>"/>
		<input type="hidden" name="comp" value="<?php echo $desbloqueo->registros[0]->cp; ?>" />
		<label style="font-family: arial; font-weight: bold;">Código Postal: </label><input type="text" name="cp" value="" size="7" maxlength="5" pattern="[0-9]{5}" required  class="datos_cesta"/><br>
		<label style="font-family: arial;  font-weight: bold;">¿Cuánto es?: </label><input type="text" name="suma" value=""  class="datos_cesta" style="margin-left: 10px;" size="10" maxlength="2" pattern="[0-9]+" placeholder="<?php echo "{$_SESSION['uno']} más {$_SESSION['dos']}"; ?>" required /><br>
		<input type="submit" name="accion" value="Activar" class="cp" />
		</form>	
		</center>
			

		<?php	
		
		} else {
			
			$sql_debloqueado = "SELECT idcliente, cp FROM clientes WHERE eliminado=0 AND usuario = '{$_GET['usuario']}' AND desbloqueo is NULL;";
			$desbloqueo->ejecutar_consulta($sql_debloqueado);
			if($desbloqueo->numero_registros == 1)  echo '<p>Su cuenta ya ha sido desbloqueda y ya puede realizar sus compras.</h4><br><a href="http://www.muyerotika.com">Ir a www.muyerotika.com</a><p>'; 
			else echo 'Existen errores y no se puede activar su cuenta, póngase en contacto con el administrador del sistema para solucinarlo';
			
		}
		
		$desbloqueo->__destruct(); 
		
	}
	
} else {
	
	echo '¡Demasiados intentos para una pregunta tan sencilla!<br>';

	if($_GET['usuario']) {
		$sql_bloqueo="UPDATE clientes SET eliminado=1, desbloqueo=0 WHERE usuario = '{$_GET['usuario']}';";	
		$bloquerar = new Mysql;
		$bloquerar->resultado_consulta($sql_bloqueo);
		$bloquerar->__destruct();
		echo "¡SU CUENTA {$_GET['usuario']} HA SIDO BLOQUEADA Y DEBERÁ PONERSE EN CONTACTO CON EL ADMINISTRADOR DEL SISTEMA!";
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		$headers[] = "From: {$_SERVER['SERVER_NAME']} <web@{$_SERVER['SERVER_NAME']}>";
		$headers[] = "X-Mailer: PHP/" . phpversion();

		$cabeceras=implode("\r\n", $headers);
		//$mail_desbloqueo = new Enviar_mail('nortemadrid@nortemadrid.com', 'Nuevo usuario registrado', $mensaje ,'comercial@dizma.es');
		$mensaje = "Se han bloqueado la cuenta {$_GET['usuario']}.";
		mail('comercial@dizma.es', 'Usuario Bloqueado', $mensaje, $cabeceras);
	} else {
		echo '¿A qué esta usted jugando, estamos registrando y bloqueando su IP?';
	}
	
}

?>


</body>
</html>
