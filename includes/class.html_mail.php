<?php

class Html_mail
{
	/*******************************************************************
	 * Función en envio de mail HTML sin adjuntos. 
	 * No requiere de autenticacion ya que usa la función mail.
	 * 
	 * Envios directos: Servidor -> clientes 
	 * $config['mail_envio'], $config['mail_respuesta'] requeridos
	 * 
	 * envíos inversos: cliente->adminstrador (formulario contacto)
	 * $config['mail_respuesta'] requerido
	 * 
	 ******************************************************************/

	private $_hash;
	private $directo;
	
	public $error;
	
	public function __construct($mensaje_html, $asunto, $directo=true, $destinatario=false, $remitente=false, $responder_a=false)
	{
		$this->error=false;
		$this->_hash = md5(date('r', time()));
		
		//require_once 'includes/mysql.php';
		$config=new Config();
		
		if($directo) {
			if($destinatario) {//sel sistema a cliente
				$dest=$destinatario;
				$remite=$config->conf['mail_envio'];
				$resp_a=$config->conf['mail_respuesta'];
				if($remitente) $remite=$remitente;
				if($responder_a) $resp_a=$responder_a;
			} else $this->error="No hay destinatario del mensaje.";
		} else {
			if($remitente) {//del form a admins
				$remite=$remitente;
				$resp_a=$remitente;
				$dest=$config->conf['mail_respuesta'];
				if($destinatario) $dest=$destinatario;
			} else $this->error="No hay remitente del mensaje.";
		}
		
		if(!$this->error) {
			if( !mail($dest, $asunto, $this->_mensaje($mensaje_html), $this->_cabeceras($remite,$resp_a)) ) $this->error="No se ha podido enviar el correo electrónico.";
		}
		
		return $this->error;
		
	}
	
	private function _cabeceras($remite,$resp_a)
	{		
		$headers = "From: $remite\r\nReply-To: $resp_a";
		$headers .= "\r\nContent-Type: multipart/alternative; boundary=\"PHP-alt-".$this->_hash."\"";
		return $headers;
	}
	
	private function _mensaje($cuerpo)
	{
		
		ob_start(); //Turn on output buffering
		?>

--PHP-alt-<?php echo $this->_hash; ?> 
Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: 7bit

<?php echo $cuerpo; ?>

--PHP-alt-<?php echo $this->_hash; ?>--

		<?
		//es intocable, no tabular ni nada porque sino no sale correcto
		$mensaje = ob_get_clean();
		
		return $mensaje;
	}

}
