<?php


class Contacto
{
	
	private $_email;
	//private $_asunto;
	private $_cabeceras;
	private $_mensaje;
	private $_remitente;
	private $_seccion;
	//private $_visibilidad;
	private $_respuesta;
	
	public function __construct($config,$seccion) 
	{
		
		$visibilidad = "hidden";
		$this->_email = $config->conf['email_contacto'];
		$this->_seccion=$seccion;
		if($_POST['accion']=='enviar') { 
			$this->_mensaje();
			$this->_cabeceras();
			$this->_enviar_mail();
			$visibilidad="visible";
		} 
		include "mensaje_correo.php"; 
		include "formulario_$seccion.php"; 
		
	}

	private function _enviar_mail()
	{		
		$asunto = "E-mail enviado desde el formulario de la sección {$this->_seccion} de la página web de {$_SERVER['SERVER_NAME']}";
		if(mail($this->_email, $asunto, $this->_mensaje,$this->_cabeceras)) $this->_respuesta = "Mensaje enviado con éxito";
		else $this->_respuesta = "No se ha podido enviar el mensaje.";		
	}	
	
	private function _cabeceras()
	{
		
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		$headers[] = "From: {$_SERVER['SERVER_NAME']} <web@{$_SERVER['SERVER_NAME']}>";
		$headers[] = "Reply-To: {$this->_remitente}";
		$headers[] = "X-Mailer: PHP/".phpversion();
		
		$this->_cabeceras=implode("\r\n", $headers);
		
	}
	
	private function _mensaje()
	{
		
		$header='
			<html>
			<head>
			   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			</head>
			<body>
		';
		
		$body = "<p>Los datos del formulario de {$this->_seccion} son los siguientes:</p>";
		foreach($_POST as $var => $val) {
			if($var!='accion')  $body .= "<p>$var: $val</p>";
			if	($var=="e-mail" || $var=="mail" || $var=="email") echo $this->_remitente=$val;
		}	
		$body .= "<p>Este mensaje esta creado desde el formulario de contacto de su página web. No olvide contestar a la dirección de e-mail que aparece.";
			   
		$footer='
			</body>
			</html>
		';
		
		$this->_mensaje=$header.$body.$footer;
		
	}
	
	
	
	
}

