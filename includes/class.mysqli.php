<?php

class Mysql extends mysqli
{

	const USUARIO = 'root';
	const PASS = 'root';
	const BD = 'algemoto';
	const SERVIDOR = 'localhost';
	
	//protected $_conexion;
	//protected $_consulta;
	//public $num_registros;

	public $id_ultimo_registro;
	public $registros = array();
	public $resultado_consulta;
	public $numero_registros;
	
	protected $conexion = object;
	
	protected function _conectar() 
	{
		@parent::__construct(
			self::SERVIDOR,
			self::USUARIO,
			self::PASS,
			self::BD	
		);
		
		if ($this->connect_error) {
			die('Error de Conexión (' . $this->connect_errno . ') '
         . $this->connect_error);
      }
		
	}
	
	protected function _desconectar() 
	{
		parent::close();
	}
	
	protected function _consulta($sql)
	{
		
		$this->resultado_consulta = parent::query($sql);
		if($this->resultado_consulta === FALSE) { //no se ha ejecutado la consulta. hay un error
			die (
				'<p>Error al ejecutar la consulta: '
				. $this->sqlstate .
				"<br />La consulta es: $sql</p>"
			);
		} elseif(is_object($this->resultado_consulta)) {  //consulta con resultados
			while($registro = $this->resultado_consulta->fetch_object()) $this->registros[] = $registro;
			$this->numero_registros = $this->resultado_consulta->num_rows;
			$this->id_ultimo_registro = $this->resultado_consulta->insert_id;
			$this->resultado_consulta->close();
		}
		
	}
	
	public function ejecutar_consulta($sql) 
	{			
		unset($this->registros);
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

		if(is_array($this->registros)) foreach($this->registros as $registro) {
			$this->conf[$registro->variable]=$registro->valor;
		}
	}	
	
}




