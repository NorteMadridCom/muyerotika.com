<?php

class Validar_datos
{
	
	private $_patron;
	private $_obligatorio;
	private $_tipo;
	private $_valor;
	public $error;
	public $formateado;
	public $fechaunix;
	public $fechasql;
	
	public function __construct($tipo, $valor, $obligatorio=true, $patron=false) 
	{

		/***************************************************************************************************
		//Por cada campo de formulario, que se desee comprobar, se llama a esta clase y se comprueba con 
		//los patrones estandar, pero no es necesario que sea obligatorio, ya que se da el caso de que uno
		//no sea obligatio, pero en el caso de que se rellene ha de tener un formato correcto (cumpleaños)
		//en el caso de los especiales, se puede plantear algo como numericos y/o longitud determinada
		//esta clase no solo valida sino que formatea los datos y los devuelve
		****************************************************************************************************/		
		
		$this->_tipo = $tipo;
		$this->_valor = $valor;
		$this->_obligatorio = $obligatorio;
		$this->_patron = $patron;
		$this->error = false;
		
		if(preg_match('/mail/', $this->_tipo)) {
			$this->_email();
		} elseif(preg_match('/fecha/', $this->_tipo)) {
			$this->_fecha();
		} elseif(preg_match('/hora/', $this->_tipo)) {
			$this->_hora();
		} elseif(preg_match('/date/', $this->_tipo)) {
			$this->_date();
		} elseif(preg_match('/tiempo/', $this->_tipo)) {
			$this->_hora(true);
		} elseif(preg_match('/(nie|nif|dni|cif)/', $this->_tipo)) {
			$this->_nif();
		} elseif(preg_match('/(nass|social)/', $this->_tipo)) {
			$this->_seg_social();
		} elseif(preg_match('/(tel|fax|movil)/', $this->_tipo)) {
			$this->_telefono();
		} elseif(preg_match('/pass/', $this->_tipo)) {
			$this->_pass();
		} elseif(preg_match('/(ccc|banc|cuenta)/', $this->_tipo)) {
			$this->_ccc();
		} elseif(preg_match('/(vacio)/', $this->_tipo)) {
			//no es de ningun tipo y no tiene patron y es obligatorio entendemos que ha de no ser vacío
			//pero no ha de devolver nada, simplemente devolver error si esta vacío
			$this->_vacio();
		//} elseif(preg_match($this->_patron, $this->_tipo)) {
			//hacer algo especial
			//echo 'En obras';
		} else {
			$this->formateado = $this->_valor;
		}
		
		//echo $tipo;
	}
	
	private function _vacio() 
	{	
		if(!$this->_valor) {
			$this->error = true;
		}
	}
	
	private function _email()
	{
		/*************************************************************************************
		// Aunque los mails han de estar en minúsculas, en la cadena de verificación
		// hemos dejado las mayúsculas por si se desea usar, eso si, todo se pasa a 
		// minúsculas y se alamcena es este formato. Se admiten y se filtran los sepradores
		// coma y punto y coma, por si se pusieran múltiples.
		// no contemplado el caso "Usuario <usuario@dominio.ext>" y su múltiple ->pendiente
		**************************************************************************************/
		
		$patron='#(([A-Za-z0-9]+[a-zA-Z0-9_.-]*[A-Za-z0-9]+)@([A-Za-z0-9]+[a-zA-Z0-9.-]*[A-Za-z0-9]+).([a-zA-Z]{2,6}))#';
		$this->_valor=str_replace(';', ',', $this->_valor);//reemplazo de ; por ,
		$this->_valor=strtolower($this->_valor); //los correos siempre van en minusculas
		$emails=array_map('trim',explode(",",$this->_valor)); //separamos y quitamos los espacios
		foreach ($emails as $email) {
			//echo "<p>$email</p>";
			if(!preg_match($patron, $email)) {
				$this->error=true;
			}
		}
		if($this->error === false) {
			$this->formateado = $emails;//devolvamos una matriz
			//var_dump($this->formateado);
		}
		
	}
	
	private function _nif()
	{
	
		$this->_valor=strtoupper($this->_valor);
		$this->formateado = $this->_valor;
		$var_letras='TRWAGMYFPDXBNJZSQVHLCKE';
		
		//si es un nif con menos de 9 cifras rellenamos hasta  las 9 con ceros.
		if(strlen($this->_valor)<9) {//si es nif, funcionara, si es otro, dara error
			//miro si lo ultimo es una letra
			if(preg_match('/[A-Z]/',substr($this->_valor,-1))){
				$ceros=9-strlen($this->_valor);
				for($i=0;$i<$ceros;$i++) {
					$this->_valor='0'.$this->_valor;
				}
			}
		} elseif(strlen($this->_valor)>9) {
			$this->error = true;
		}
		
		
		if(preg_match('/^([0-9]{8})([A-Z]{1})$/',$this->_valor)) {//esto es un nif
			$valor=intval(substr($this->_valor,0,8));
			$resto= $valor % 23;
			$letra=substr($var_letras,$resto,1);
			if($letra!=substr($this->_valor,-1)){
				$this->error = true;
			}
		} elseif(preg_match('/^[XY]{1}([0-9]{7})([A-Z]{1})$/',$this->_valor)) {//esto es un nie
			if(substr($this->_valor, 0, 1)=='Y') {
				$valor=intval('1'.substr($this->_valor,1,7));
			} else {	
				$valor=intval(substr($this->_valor,1,7));
			}
			$resto= $valor % 23;
			$letra=substr($var_letras,$resto,1);
			if($letra!=substr($this->_valor,-1)){
				$this->error = true;
			}
		} elseif(preg_match('/^[A-H]{1}([0-9]{8})$/',$this->_valor)) {//esto es un cif
			$valor=substr($this->_valor,1,7);
			$valor_par=intval(substr($valor,1,1))+intval(substr($valor,3,1))+intval(substr($valor,5,1));
			$valor_impar[0]=intval(substr($valor,0,1))*2;
			$valor_impar[1]=intval(substr($valor,2,1))*2;
			$valor_impar[2]=intval(substr($valor,4,1))*2;
			$valor_impar[3]=intval(substr($valor,6,1))*2;
			unset($valor_impares);
			for($i=0;$i<4;$i++){
				if(strlen($valor_impar[$i])>1) {
					$valor_impar[$i]=intval(substr($valor_impar[$i], 0, 1))+intval(substr($valor_impar[$i], 1, 1));
				} else {
					$valor_impar[$i]=substr($valor_impar[$i], 0, 1);
				}
				$valor_impares = $valor_impares+$valor_impar[$i];
			}
			$valor_total=$valor_impares+$valor_par;
			$control=10-($valor_total % 10);
			if(substr($this->_valor,-1)!=$control) {
				$this->error = true;
			}
		} elseif(preg_match('/^([PQS]{1})([0-9]{7})([A-Z]{1})$/',$this->_valor)) {//esto es un cif estatal
			$valor=substr($this->_valor,1,7);
			$valor_par=intval(substr($valor,1,1))+intval(substr($valor,3,1))+intval(substr($valor,5,1));
			$valor_impar[0]=intval(substr($valor,0,1))*2;
			$valor_impar[1]=intval(substr($valor,2,1))*2;
			$valor_impar[2]=intval(substr($valor,4,1))*2;
			$valor_impar[3]=intval(substr($valor,6,1))*2;
			unset($valor_impares);
			for($i=0;$i<4;$i++){
				if(strlen($valor_impar[$i])>1) {
					$valor_impar[$i]=intval(substr($valor_impar[$i], 0, 1))+intval(substr($valor_impar[$i], 1, 1));
				} else {
					$valor_impar[$i]=substr($valor_impar[$i], 0, 1);
				}
				$valor_impares = $valor_impares+$valor_impar[$i];
			}
			$valor_total=$valor_impares+$valor_par;
			$letra=chr(64+(10-($valor_total % 10)));
			if(substr($this->_valor,-1)!=$letra){
				$this->error = true;
			}
		} else {
			$this->error = true;	//no coincide con ningun patron
		}
		
	}
	
	private function _seg_social() 
	{
		if(preg_match("/^[0-9]{10,12}$/",$this->_valor)) {
			$rango=10000000;
			$primo=97;
			$aux=10;
			$dc=intval(substr($this->_valor, -2));
			$cp=intval(substr($this->_valor, 0,2));
			$num=intval(substr($this->_valor, 2, -2));
			
			if($num<$rango) {
				$aux=1;
			}
				
			$dc_aux=(($cp*$rango*$aux)+$num)-(floor((($cp*$rango*$aux)+$num)/$primo)*97);
			
			if($dc_aux!=$dc) {
				$this->error = true;
			}
		} else {
			$this->error = true;
		}
	}
	
	private function _pass() 
	{
		/***********************************************************************
		//podemos modificar el nivel de seguridad modificando los parámetros
		// en html5: pattern = "(?=^.{6,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"
		// esto permite caracteres especiales, de 6 a 20 long con May. min y numero obligatorio
		***********************************************************************/

		$long_min = 6;
		$long_max = 20;
		$may = true;
		//$especiales = true;
		
		if(strlen($this->_valor)<$long_min || strlen($this->_valor)>$long_max) {
				$this->error = true;
		}
		if(!preg_match('/[A-Z]/', $this->_valor) && $may) {
			$this->error = true;
		}
		/*
		if(!preg_match('/[a-zA-Z0-9]+/',$this->_valor)) {
			$this->error = true;
		}
		*/
		$this->formateado=$this->_valor;
	}
	
	private function _fecha() 
	{
		$divisor='/';
		$matriz_fecha_completa = explode(' ', $this->_valor);
		$fecha = $matriz_fecha_completa[0];
		$hora = $matriz_fecha_completa[1];
		
		if($hora) {
			_hora();
		}
		
		if(preg_match('#^[0-9]{6,8}$#', $fecha)) { //formato seguido
			if(strlen($fecha)==6) {
				$ano = '20' . substr($fecha, -2);
			} elseif(strlen($fecha)==8) {
				$ano = substr($fecha, -4);
			} else {
				$this->error = true;
			}
			$dia = substr($fecha, 0, 2);
			$mes = substr($fecha, 2, 2);
			
		} elseif(preg_match('#^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$#', $fecha)) { //formato fecha sql
			$separador = "-";
			$matriz_fecha = explode($separador, $fecha);
			if(count($matriz_fecha)!=3 || strlen($matriz_fecha[2])==3) {
				$this->error = true;
			} else {
				$dia = $matriz_fecha[2];
				$mes = $matriz_fecha[1];
				$ano = $matriz_fecha[0];
			}
		} elseif(preg_match('#^[0-9]{1,2}.[0-9]{1,2}.[0-9]{2,4}$#', $fecha)) { //formato fecha española
			$separador = $fecha[2];
			$matriz_fecha = explode($separador, $fecha);
			if(count($matriz_fecha)!=3 || strlen($matriz_fecha[2])==3) {
				$this->error = true;
			} else {
				$dia = $matriz_fecha[0];
				$mes = $matriz_fecha[1];
				$ano = $matriz_fecha[2];
			}
		} else {
			$this->error = true;
		}
		
		if(!$this->error) {
			if(checkdate($mes, $dia, $ano)) {
				$this->formateado = $dia.$divisor.$mes.$divisor.$ano;
				$this->fecha_unix = mktime($hour = null, $minute = null, $second = null, $mes, $dia, $ano);
			} else {
				$this->error = true;
			}
		}
	}
	
	private function _date() 
	{
		$divisor='/';
		$matriz_fecha_completa = explode(' ', $this->_valor);
		$fecha = $matriz_fecha_completa[0];
		$hora = $matriz_fecha_completa[1];
		
		if($hora) {
			_hora();
		}
		
		if(preg_match('#^[0-9]{6,8}$#', $fecha)) {
			if(strlen($fecha)==6) {
				$ano = '20' . substr($fecha, -2);
			} elseif(strlen($fecha)==8) {
				$ano = substr($fecha, -4);
			} else {
				$this->error = true;
			}
			$dia = substr($fecha, 0, 2);
			$mes = substr($fecha, 2, 2);
			
		} elseif(preg_match('#^[0-9]{1,2}.[0-9]{1,2}.[0-9]{2,4}$#', $fecha)) {
			$separador = $fecha[2];
			$matriz_fecha = explode($separador, $fecha);
			if(count($matriz_fecha)!=3 || strlen($matriz_fecha[2])==3) {
				$this->error = true;
			} else {
				$dia = $matriz_fecha[0];
				$mes = $matriz_fecha[1];
				$ano = $matriz_fecha[2];
			}
		} else {
			$this->error = true;
		}
		
		if(!$this->error) {
			if(checkdate($mes, $dia, $ano)) {
				$this->formateado = $mes.$divisor.$dia.$divisor.$ano;
				$this->fechaunix = mktime($hour = null, $minute = null, $second = null, $mes, $dia, $ano);
				$this->fechasql = "$ano-$mes-$dia $hora";
			} else {
				$this->error = true;
			}
		}
	}
	
	private function _hora($tiempo=null) 
	{
		$matriz_hora=explode(':', $this->_valor);
		$hora=$matriz_hora[0];
		$min=$matriz_hora[1];
		$seg=$matriz_hora[2];
		if($tiempo) {
			$this->horaunix = mktime($hora, $min, $seg);
		} else {
			if($hora<24 && $min<60 && $seg<60) {
				$this->horaunix = mktime($hora, $min, $seg);
			} else {
				$this->error = true;
			}
		}
	}
	
	private function _telefono() 
	{

		/************************************************************************************************************
		//La forma de usar la variable formateada a un múmero es (si es correcto):
		//$objeto->formateado['internacional']['divisor_internacional']['grupo_nacional']['seprador_nacional']
		//$objeto->formateado['nacional']['dos']['punto'] = 629.20.08.87
		//$objeto->formateado['nacional']['dos']['no'] = 629200887
		//$objeto->formateado['internacional']['punto']['dos']['guion'] = +34.629-20-08-87
		//$objeto->formateado['internacional']['punto']['tres']['espacio'] = +34.629 200 887
		//$objeto->formateado['internacional']['no']['dos']['no'] = +34629200887
		//solo se hace si el numero de telefono es correcto, no se admiten paréntesis
		************************************************************************************************************/

		
		$this->_valor = trim($this->_valor);
		$prefijo_internacional='+34';//conste para españa, se debe variar segun país

		$this->_valor = preg_replace('#^00(\.|\-|\/| )?#', '+',$this->_valor);//cambiamos el 00* por el +
		$partes = preg_split('#(^\+[\.|\-|\/| ]?[0-9]{1,3}[\.|\-|\/| ]{1})#', $this->_valor, 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);//sacamos el prefijo si exite
		if(count($partes)==2 && $partes[1] && $partes[0]) {
			$prefijo_internacional = $partes[0];
			$num_nacional = $partes[1];
		} elseif(preg_match('#^\+[0-9]{8,}$#', $this->_valor)) { 
			$prefijo_internacional='+';
			$num_nacional = substr($this->_valor, 1);
		} else {
			$num_nacional = $this->_valor;
			$prefijo_internacional="";
		}
		
		$separadores = array(' ','.','-','/');
		foreach($separadores as $separador) {
			$trozos_nacional = explode($separador, $num_nacional);
			$num_nacional = implode('', $trozos_nacional);
			$trozos_internacional = explode($separador, $prefijo_internacional);
			$prefijo_internacional = implode('', $trozos_internacional);
		}
		
		if(($prefijo_internacional == '+34' || !$prefijo_internacional) && preg_match('#^(6|9)[0-9]{8}$#', $num_nacional)) {
			if(!$prefijo_internacional) $prefijo_internacional='+34'; //hemos pasado el numero nacional correcto
			$prefijo_nacional = substr($num_nacional, 0, 3);
			$tel_nacional = substr($num_nacional,3);
		} elseif(preg_match('#^\+[0-9]?[0-9]?[0-9]?$#',$prefijo_internacional) && preg_match('#^[0-9]{7,}$#', $num_nacional)) { 
			$tel_nacional = $num_nacional;
		} else {
			$this->error = true;
		}
		
		if($this->error === false) {
			$grupos['dos'] = str_split($tel_nacional, 2);
			$grupos['tres'] = str_split($tel_nacional, 3);
			$divisor = array(
				'punto'=>'.',
				'espacio'=>' ',
				'guion'=>'-',
				'no'=>''
			);
			foreach($divisor as $nom_div => $div) {
				foreach($grupos as $nom_grupo => $grupo) {
					if($prefijo_nacional) {
						$num_format['nacional'][$nom_grupo][$nom_div] = $prefijo_nacional.$div.implode($div, $grupo);
					} else {
						$num_format['nacional'][$nom_grupo][$nom_div] = implode($div, $grupo);
					}
					foreach($divisor as $nom_div_pref => $div_pref) {
						if(strlen($prefijo_internacional)>1) {
							if($prefijo_nacional) {
								$num_format['internacional'][$nom_div_pref][$nom_grupo][$nom_div] = $prefijo_internacional.$div_pref.$prefijo_nacional.$div.implode($div, $grupo);
							} else {
								$num_format['internacional'][$nom_div_pref][$nom_grupo][$nom_div] = $prefijo_internacional.$div_pref.implode($div, $grupo);
							}
						} else {
								$num_format['internacional'][$nom_div_pref][$nom_grupo][$nom_div] = $prefijo_internacional.implode($div, $grupo);
						}
					}
				}
			}
			
			$this->formateado = $num_format;
			
		}
		
	}
	
	private function _ccc() 
	{
		
		/*********************************************************************************
		//Comprobamos la cuenta bancaria, requiere de la función adicional _dc
		//$objeto->formateado['separador'];
		*********************************************************************************/

		$this->_valor = trim($this->_valor);
		
		$separadores = array(' ','.','-','/');
		foreach($separadores as $separador) {
			$trozos = explode($separador, $this->_valor);
			$this->_valor = implode('', $trozos);
		}
		
		if(preg_match('#^[0-9]{20}$#', $this->_valor)) {
			$partes = str_split($this->_valor, 10);
			$numero_cc = $partes[1];
			$banco_sucur_dc = str_split($partes[0],4);
			$banco = $banco_sucur_dc[0];
			$sucursal = $banco_sucur_dc[1];
			$dc = $banco_sucur_dc[2];
			if($dc != $this->_dc('00'.$banco.$sucursal) . $this->_dc($numero_cc)) {
				$this->error = true;
			}
		} else {
			$this->error = true;
		}
		
		if($this->error === false) {
			$divisores = array(
				'punto'=>'.',
				'espacio'=>' ',
				'guion'=>'-',
				'no'=>''
			);
			foreach($divisores as $divisor=>$div) {
				$this->formateado[$divisor] = $banco.$div.$sucursal.$div.$dc.$div.$numero_cc;
			}
		}

	}
	
	private function _dc($control) 
	{
		/***********************************************
		//Función necesaria para calcular los digitos
		//de control para las cuentas bancarias
		***********************************************/
		
		$cifras = Array(1,2,4,8,5,10,9,7,3,6);
		
		$chequeo = 0;
		for ($i=0; $i < count($cifras); $i++) {
			$chequeo += intval($control[$i])*$cifras[$i];
		}
		$chequeo = 11 - ($chequeo % 11);
		if ($chequeo == 11) {$chequeo = 0;}
		if ($chequeo == 10) {$chequeo = 1;}
		
		return $chequeo;	
						
	}
	
	public function __destruct() {}
	
}
