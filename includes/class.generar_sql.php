<?php

// queda mejorar esta clase haciendo un analisis de where por ejemplo
//hay que mirar la posibilidad de eliminado con forein keys y otros

class Generar_sql
{
	
	//es la sentecia devuelta lista para usar
	public $sql;
	
	//cambia a true en caso de haber error
	public $error = false;
	
	//campos que necesitamos si o si
	private $_campos_obligatorios = array();
	
	//tabla en la que modificamos los datos
	private $_tabla;
	
	//es la matriz que contiene los valores que debemos meter
	//normalemnte es el $_POST[campo] = valor, pero vale
	//cualquier matriz tal que $array[clave]=valor
	private $_valores = array();
	
	//generamos el par clave valor
	//formato de $par[n] = 'campo = valor'
	//para usar como implode
	private $_par = array();
	
	//si no lo especificamos se busca para no cometer
	//error de ponerlo en valores o bien si no viene en 
	//el where pero en valores si, y es un update, lo coloca
	//en la cadena where si es que ya no esta especificado
	private $_campo_id;
	
	//matriz donde where[campo]=valor;
	//es posible llevarse el campo_id aqui pero
	//solo hace combinaciones AND, para no usarla
	//se puede traer una cadena de texto que se mantiene
	//intacta y se añade
	private $_where;
	
	//si hay algun campo del form que no corresponde en base de 
	//datos, con el formato array[campo_sql]=campo_form
	//realizamos los cambios para que funcione
	private $_equivalencias = array();
	
	//si es true, insertamos, en caso
	//contrario update
	private $_insert;
	
	private $_campos_tabla = array();
	
	public function __construct($insert=true, $tabla, $campo_id = null, $campos_obligatorios=array(), $valores = null, $where =  null, $equivalencias=array()) 
	{

		if($tabla) {
		
			$this->_tabla = $tabla;
			$this->_campos_obligatorios = $campos_obligatorios;
			$this->_equivalencias = $equivalencias;
			$this->_where = $where;
			$this->_insert = $insert;
			$this->_campo_id=$campo_id;
			if(is_array($valores)) {
				$this->_valores = $valores;
			} else {
				$this->_valores = $_POST;
			}
			
			$sql = "DESCRIBE {$this->_tabla};";
			$campos_tabla = new Mysql;
			//$campos_tabla->ejecutar_consulta($sql);
			foreach($campos_tabla->ejecutar_consulta($sql) as $reg)
			{
				if(is_object($reg)) {
					//var_dump($reg);
					//echo '<br>';
					$this->_campos_tabla[] = $reg;
					//var_dump($this->_campos_tabla);
				}
			}

			$this->_campo_id();

			$this->_equivalencias();
			$this->_campos_obligatorios();
			$this->_campos_opcionales();
			$this->_construccion_sql();
			
			$campos_tabla->__destruct();
		
		} else {
			$this->error;
		}
	
	}
	
	private function _campo_id() 
	{
		if(!$this->_campo_id) {
			foreach($this->_campos_tabla as $registro) {
				if(
					(stripos($registro->Field,'id') !== false) &&
					(stripos($registro->Type,'int') !== false) &&
					(stripos($registro->Null,'no') !== false) &&
					(stripos($registro->Key,'pri') !== false) &&
					(stripos($registro->Extra,'auto_increment') !== false) 				
				) {
					$this->_campo_id = $registro->Field;
				}
			}
		}
		
		if($this->_campo_id) {	
			if(
				$this->_insert === false && 
				!$this->_where[$this->_campo_id] && 
				$this->_valores[$this->_campo_id] &&
				!is_array($this->_where)
			) {
				$this->_where[$this->_campo_id] = $this->_valores[$this->_campo_id];
			} 
			//exista o no ni lo pienso, lo elimino
			unset($this->_valores[$this->_campo_id]);
		}
	
	}
	
	
	private function _campos_opcionales() 
	{
		
		/**********************************************************
		* Buscamos los campos no obligatorios como aquellos que
		* no son obligatorios. Hay un problema si la referencia del
		* nombre del campo del form no coincide con el campo mysql
		***********************************************************/
		
		foreach($this->_campos_tabla as $registro) {
			if(!in_array($registro->Field, $this->_campos_obligatorios)) {		
				if(array_key_exists($registro->Field, $this->_valores)) {
					if(!(!$this->_valores[$registro->Field] && $this->_insert === true)) $this->_par[] = $registro->Field . " = '" . $this->_valores[$registro->Field] ."'";
				}
			}
		}
		
	}
	
	private function _campos_obligatorios() 
	{
		
		$this->_par = array();
		foreach($this->_campos_obligatorios as $campo) {
			if(array_key_exists($campo, $this->_valores) && $this->_valores[$campo] !== null) {
				$this->_par[] = "$campo = '" . $this->_valores[$campo] . "'";
			} else {	
				$this->error = true;
			}			
		}
		
	}
	
	private function _equivalencias()
	{

		/****************************************************
		* el formato ha de ser array[campo_sql]=campo_form
		* buscamos donde pone $this->_valores[campo_form] y
		* covertimos a $this->_valores[campo_sql], eliminado
		* la clave por si es el caso idart por art, en el que
		* ambos existen, peor queremos el id y no el art
		*****************************************************/
		if(is_array($this->_equivalencias)) {
			foreach($this->_equivalencias as $campo_sql => $campo_form) {
				if($this->_valores[$campo_form]) {
					$this->_valores[$campo_sql] = $this->_valores[$campo_form];
					unset($this->_valores[$campo_form]);
				}
				$this->_campos_obligatorios[array_search($campo_form, $this->_campos_obligatorios)] = $campo_sql;
			}
		}

	}
	
	private function _construccion_sql() 
	{
		
		if($this->_insert === true) {
			$this->sql = "INSERT INTO $this->_tabla SET ";
		} else {
			$this->sql = "UPDATE {$this->_tabla} SET ";
		}
		
		if(is_array($this->_par)) $set = implode(', ', $this->_par);
		
		if(is_array($this->_where)) {
			unset($par_where);
			foreach($this->_where as $clave => $valor) {
				$par_where[] = "$clave='$valor'";
			}
			$where = implode(' AND ', $par_where);
		} else {
			$where = $this->_where;
		}
		
		$this->sql .= $set;
		
		if($this->_where && $this->_insert === false) {
			$this->sql .= ' WHERE ' . $where;
		}
		
		$this->sql .= ";";
		
	}
	
	public function __destruct() {}
	
}
	 	