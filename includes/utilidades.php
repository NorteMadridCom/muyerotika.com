<?php

class utilidades
{

public function devuelve_fecha() 
	{
		date_default_timezone_set('UTC');
		$dia = date ("d");
		$mes = date ("m");
		$ano = date ("Y");
		if ($mes == 1) {
		$mes = "Enero";
		}
		if ($mes == 2) {
		$mes = "Febrero";
		}
		if ($mes == 3) {
		$mes = "Marzo";
		}
		if ($mes == 4) {
		$mes = "Abril";
		}
		if ($mes == 5) {
		$mes = "Mayo";
		}
		if ($mes == 6) {
		$mes = "Junio";
		}
		if ($mes == 7) {
		$mes = "Julio";
		}
		if ($mes == 8) {
		$mes = "Agosto";
		}
		if ($mes == 9) {
		$mes = "Septiembre";
		}
		if ($mes == 10) {
		$mes = "Octubre";
		}
		if ($mes == 11) {
		$mes = "Noviembre";
		}
		if ($mes == 12) {
		$mes = "Diciembre";
		}
		//echo "Hoy es ".$dia." de ".$mes." de ".$ano;
		//$fecha = $dia." de ".$mes." de ".$ano;
		//return $fecha;
		return $dia." de ".$mes." de ".$ano;
		
	}//devuelve_fecha
	
}//de la clase

?>