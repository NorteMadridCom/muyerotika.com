<?php

class Buscador_campos
{
	// La estructura ha de ser $campo[campo]=valor
	private $_campos=array();
	
	private $_nexo;
	
	public $error = false;
	//resultado como cadena sql
	public $sql;
	
	public function __construct($campos, $nexo = 'OR') 
	{
		if(is_array($campos) && $nexo) {
			$this->_campos = $campos;
			$this->_nexo = " ". trim($nexo)." ";
			$this->_sql();
		} else {
			$this->error= true;
		}
		
	}	
	
	private function _sql() 
	{
		$this->sql = "SELECT * FROM productos WHERE eliminado = 0 AND (";
		unset($where);
		foreach($this->_campos as $campo => $valor) {
			if($valor) $where[] = "$campo='$valor'";
		}
		$this->sql .= implode($this->_nexo, $where);
		$this->sql .= ") ORDER BY orden LIMIT 0,1000;";
	}
	
	public function __destruct() {}
	
}