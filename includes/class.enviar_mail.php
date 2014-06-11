<?php


class Enviar_mail
{
	
	public $error;
	public $error_info;
	
	/*
	* A continuación se ponen los datos a cambiar en función del tipo de servidor
	*/
	private $_mail = object;	
	
	/* Donde colocamos las clases*/
	private $_directorio_clases = 'includes/';
	
	/*método de envio por defecto*/
	private $_metodo_envio = 'smtp';
	private $_caracteres = 'utf-8'; //puede ser iso-8859-1
	private $_tipo_cuerpo_mensaje= 'text/html'; //puede ser text/plain -> es como se manda AltBody
	
	/*Debemos de poner la autorizacion y sus datos*/
	private $_smtp_auth = true;
	private $_usuario='web@dizma.es';
	private $_pass='Dizma123456';
	
	/*Datos referentes al servidor y dominio*/
	private $_servidor='dizma.es';
	private $_servidor_salida='mail.dizma.es';
	
	/* Variables recogidas */
	public $remitente;
	public $destinatarios;
	public $asunto;
	public $mensaje;
	public $otros_datos;
	public $adjuntos;
	
	/* En el caso de realizar un mailing por la web
	se produce que un destinatario escribe a una lista (array)
	contemplando este caso para su elaboración futura, disgregando
	la matriz de envio en grupos grandes
	*/
	public $mailing;
	
	public function __construct($destinatarios, $asunto, $mensaje, $remitente = null, $otros_datos = null,  $adjuntos = null, $mailing = false) 
	{
		
		//include_once 'class.comprobar_datos.php';
		include_once 'class.phpmailer.php';
		
		$this->error = false;
		
		$this->_mail = new phpmailer();
		$this->_mail->PluginDir = $this->_directorio_clases;
		$this->_mail->Mailer = $this->_metodo_envio;
		$this->_mail->HostName = $this->_servidor;
		$this->_mail->Host = $this->_servidor_salida;
		$this->_mail->SMTPAuth = $this->_smtp_auth;
		$this->_mail->Username= $this->_usuario;
		$this->_mail->Password = $this->_pass;
		
		$this->destinatarios = $destinatarios;
		$this->asunto = $asunto;
		$this->mensaje = $mensaje;
		$this->remitente = $remitente;
		$this->otros_datos = $otros_datos;
		$this->adjuntos = $adjuntos;
		$this->mailing = $mailing;
		
		$this->_poner_otros_datos();
		$this->_poner_remitentes();
		$this->_poner_destinos();
		$this->_poner_asunto();
		$this->_poner_mensaje();
		$this->_poner_adjuntos();
		
		$this->_enviar_mail();
		
	}
		
	private function _poner_destinos() 
	{	
		
		if(!is_array($this->_destinatarios)) {
			$validar_mail = new Validar_datos('email', $this->destinatarios);
			if($validar_mail->error) {
				$this->error = true;
			}
			$destinatarios = $validar_mail->formateado;
			$validar_mail->__destruct();
		} else {
			foreach($this->_destinatarios as $direccion_mail) {
				$validar_mail = new Validar_datos('email',$direccion_mail);
				if($validar_mail->error) {
					$this->error = true;
				}
			}
			$destinatarios = $validar_mail->formateado;
			$validar_mail->__destruct();
		} 
		
		if(!$this->error) {
			//var_dump($destinatarios);
			foreach($destinatarios as $email) {		
				$this->_mail->AddAddress($email);
			}
		} else {
			$this->error_info = 'Error en la/s direccion/es de el/los destinatario/s.';
		}
		
	}
	
	private function _poner_remitentes() 
	{	
		/*
		$this->_mail->From = $_POST['mail'];
		$this->_mail->FromName = $_POST["contacto"];
		*/
		
		if(!$this->remitente) {
			$this->_mail->From = $this->_usuario;
		} else {
			$validar_remitente = new Validar_datos('email',$this->remitente);
			if($validar_remitente->error) {
				$this->error=true;
				$this->error_info = 'Error en la dirección del remitente';
			} else {
				$this->_mail->From = $validar_remitente->formateado[0];//solo es uno, el primero de la matriz devuelta
				if(!$this->_mail->FromName) $this->_mail->FromName=$validar_remitente->formateado[0];
			}
			$validar_remitente->__destruct();		
		}
	}
	
	private function _poner_adjuntos()
	{
		/***********************************************
		Me queda esta parte eb la que tenemos que hacer elç
		analisis de los adjuntos y enviarlos, como no lo necesito
		ahora lo dejo
		if(is_array($adjuntos)) {
			$this->_mail->AddAttachment("images/foto.jpg", "foto.jpg");
			$this->_mail->AddAttachment("files/demo.zip", "demo.zip");
		***********************************************/
	}
	
	private function _poner_otros_datos()
	{
		//var_dump($this->otros_datos);
		foreach($this->otros_datos as $variable => $valor) {
			if(preg_match("#(nombre|remitente|from)#", $variable)) $this->_mail->FromName = $valor;
			if(preg_match("#(caracteres|codificacion)#", $variable)) $this->_caracteres = $valor;
			if(preg_match("#(tipo_cuerpo|tipo_texto|tipo_mensaje)#", $variable)) $this->_tipo_cuerpo_mensaje = $valor;
			//se piueden colocar cualquier tipo de datos
		}
		
	}
	
	private function _poner_asunto() 
	{
		if(!$this->asunto) {
			$this->_mail->Subject = "E-mail enviado desde la página web";
			if($this->mailing) {
				$this->error = true;
				$this->error_info = 'No se puede hacer un mailing sin asunto.';
			}
		} else {
			$this->_mail->Subject = $this->asunto;
		}
	}
	
	private function _poner_mensaje() 
	{
		/*******************************************************
		/ si enviamos un mailing hemos de dar un buen formato
		/ incluyendo la posibilidad de poner imágenes que se 
		/ descargen desde la web, es publicitario
		********************************************************/
		
		$this->_mail->CharSet = $this->_caracteres;
		$this->_mail->ContentType = $this->_tipo_cuerpo_mensaje;
		
		if($this->mensaje) {
			if($this->_tipo_cuerpo_mensaje == 'text/plain') { 
				$this->_mail->Body = $this->mensaje;
				$this->_mail->AltBody = $this->mensaje;
			} else {
				$this->_mail->Body = $this->mensaje;
				$this->_mail->AltBody = 'Actualize su gestor de correo porque no puede ver este mensaje.';
			}
		} else {
			$this->error = true;
			$this->error_info = 'Se trata de enviar un mensaje sin cuerpo.';
		}
		
	}
			
	private function _enviar_mail()
	{

		if(!$this->error) {
			
			$exito = $this->_mail->Send();
		
		   $intentos=1; 
		   while ((!$exito) && ($intentos < 5)) 
		   {
		   	sleep(5);
		      $exito = $this->_mail->Send();
		      $intentos=$intentos+1;
		   }
		   
		   if(!$exito) {
		   	$this->error=true;
		   	$this->error_info = $this->_mail->ErrorInfo;
		   } 
		   
		}
			
	}	
	
	//consultamos si hay error y mostranmos el resultado desde la llamada,
	//sino generamos el éxito desde la misma llamada, no como respuesta
	
	
}