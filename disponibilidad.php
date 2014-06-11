<?php

class Disponibilidad
{
	private $_producto = object;
	
	public function __construct($producto)
	{
		$this->_producto=$producto;
	}
	
	public function formulario ()
	{	
		if($_POST['accion']=='enviar_disponibilidad' && $_POST['idproducto']==$this->_producto->idproducto) {	
			if($this->_mail()) 	$respuesta_mail = "Se ha enviado el aviso y será avisado cuando lo tengamos.";
			else $respuesta_mail = "Ha ocurrido un error. Inténtelo mas tarde.";
			require 'respuesta_disponibilidad.php';
		} else require 'formulario_disponibilidad.php';
		
		//require 'formulario_disponibilidad.php';
	}	
	
	private function _mail() 
	{
		$mensaje = "
			Idproducto:		{$_POST['idproducto_diakros']} \r\n
			Producto:		{$_POST['producto']} \r\n
			Nombre:		{$_POST['nombre']} \r\n
			E-mail:		{$_POST['mail']} \r\n		
		";
		$cabeceras = 'From: Web Dizma.es <web@dizma.es>' . "\r\n";
		return mail('comercial@dizma.es', "Aviso de disponibilidad", $mensaje,$cabeceras);

	}
	
	
}

