<?php

//require_once 'mssql.php';

class Mysql
{
	
	protected $_conexion;
	protected $_consulta;
	//public $num_registros;
	public $sql;
	public $consulta;
	public $id_ultimo_registro;
	public $registros = array();
	public $resultado_consulta;
	public $numero_registros;
	
	protected function _conectar() 
	{
		require 'configuraciones.php';
		$this->_conexion = mysql_connect(
			SERVIDOR,
			USUARIO,
			PASS
		);

		mysql_select_db(
			BD, 
			$this->_conexion
		);

	}
	
	protected function _desconectar() 
	{
		mysql_close($this->_conexion);
	}
	
	protected function _consulta($sql)
	{
		$sql = $this->_modificar_sql($sql);
		$this->_consulta = mysql_query($sql) or die (
			'<p>Error al ejecutar la consulta: '
			. mysql_error() .
			"<br />La consulta es: $sql</p>"
		);
		$this->resultado_consulta = $this->_consulta;
		$this->id_ultimo_registro = mysql_insert_id();
	}
	
	protected function _modificar_sql($sql) 
	{
		/*
		/ funcion muy importante para no tener problemas con las comillas que normalmente usamos
		/ sacamos los datos de la BBDD sin problemas, ponemos los caracteres no deseados en buscar
		*/

		$patron = "#(= *\')(.*?)(\' *(\,|\;| AND| OR| WHERE))#";
		
		preg_match_all($patron, $sql, $resultado);
		
		foreach($resultado[0] as $clav => $res) {
			//echo "<br>$res<br>";
			$fin = strrchr($res,"'");		
			$res=strstr($res, "'");
			$res=strrev($res);
			$res=strstr($res, "'");
			$res=strrev($res);
			$res = substr($res, 1, -1);
			$res = stripslashes($res); //por si acaso los tubiera
			$result[] = "= '". addslashes($res) . $fin;
		}
		
		return str_replace($resultado[0], $result, $sql);		

	}
	
	public function registros()
	{
		
		while($registro = mysql_fetch_object($this->_consulta)) {
			foreach($registro as $nombre=>$valor) {
				is_string($valor) ? 
					$mat[$nombre] = stripslashes($valor):
					$mat[$nombre] = $valor;
			}
			$registro = (object) $mat;
			$this->registros[]=$registro;//acesso como $registros[n]->nombre_campo.
			//$registro->valor;
		}
		
		if($this->registros) {
			mysql_free_result($this->_consulta);
		}
				
	}
	
	public function ejecutar_consulta($sql) 
	{			
		unset($this->registros);
		$this->_conectar();
		$this->_consulta($sql);
		$this->numero_registros = mysql_num_rows($this->_consulta);
		$this->registros();
		$this->_desconectar();	
		return $this->registros;
	}
	
	public function resultado_consulta($sql) 
	{
		unset($this->resultado_consulta);
		$this->_conectar();
		$this->_consulta($sql);
		$this->_desconectar();		
		return $this->resultado_consulta;
	}
		
	public function __destruct() {}
	
	//falta por hacer el análisis de $sql
	
}

class Config extends Mysql
{

	const SQL = "SELECT * FROM configuraciones;";
	public $conf = array();
	
	public function __construct()
	{
		parent::ejecutar_consulta(self::SQL);
	}
	
	public function registros()
	{
		
		while($registro = mysql_fetch_object($this->_consulta)) {
			$this->conf[$registro->variable]=$registro->valor;//$objeto->conf[variable]
		}

		if($this->conf) {
			mysql_free_result($this->_consulta);
		}
		
		return $this->conf;
				
	}
	
}




