<?php

class Mssql
{

	const USUARIO = 'NorteMadrid';
	const PASS = 'Dizma123456';
	const BD = 'DiTPV';
	const DSN = 'ConexionDizma';
	const IP = '80.33.0.88';
	//const DSN = 'Prueba';
	
	protected $_conexion;
	protected $_consulta;
	protected $_comunicacion;
	public $num_registros;
	public $sql;
	public $consulta;
	public $registros = array();
	public $conexion;
	
	protected function _conectar() 
	{
		$this->_conexion = odbc_connect(
			self::DSN,
			self::USUARIO,
			self::PASS
		); 
	}
	
	protected function _desconectar() 
	{
		odbc_close($this->_conexion);
	}
	
	protected function _consulta($sql)
	{
		
		$sql = $this->_modificar_sql($sql);
		$this->_consulta = odbc_exec($this->_conexion,$sql) or die (
			'<p>Error al ejecutar la consulta: '
			. odbc_errormsg() .
			"<br />La consulta es: $sql</p>"
		);
		
		//echo 'consulta:'.$sql;
		
		return $this->_consulta;
	}
	
	protected function _modificar_sql($sql) 
	{
		/*
		/ funcion muy importante para no tener problemas con las comillas que normalmente usamos
		/ sacamos los datos de la BBDD sin problemas, ponemos los caracteres no deseados en buscar
		*/

		$patron = "#(= *\')(.*?)(\' *(\,|\;| AND| OR | WHERE))#";
		
		preg_match_all($patron, $sql, $resultado);
		
		foreach($resultado[0] as $clav => $res) {
			$fin = strrchr($res,"'");
			$res=strstr($res, "'");
			$res=strrev($res);
			$res=strstr($res, "'");
			$res=strrev($res);
			$res = substr($res, 1, -1);
			$res = stripcslashes($res); //por si acaso los tubiera
			$result[] = "= '". addslashes($res) . $fin;
		}
		
		return str_replace($resultado[0], $result, $sql);		

	}
	
	public function registros()
	{
		
		while($registro = odbc_fetch_object($this->_consulta)) {
			foreach($registro as $nombre=>$valor) {
				is_string($valor) ? 
					$mat[$nombre] = stripslashes($valor):
					$mat[$nombre] = $valor;
			}
			$registro = (object) $mat;
			$registros[]=$registro;//acesso como $registros[n]->nombre_campo.
			//$registro->valor;
		}

		if($registros) {
			odbc_free_result($this->_consulta);
		}
		
		$this->registros=$registros;
				
	}
	
	public function ejecutar_consulta($sql) 
	{
		unset($this->registros);
		$this->_conectar();
		$this->_consulta($sql);
		$this->registros();
		$this->_desconectar();
		//var_dump($registros);
		return $this->registros;
	}
	
	public function numero_registros($sql)
	{
		unset($this->registros);
		$this->_conectar();
		$this->_consulta($sql);
		$this->registros();
		$this->_desconectar();
		return count($this->registros);
	}
	
	public function resultado_consulta($sql) 
	{
		$this->_conectar();
		$resultado=$this->_consulta($sql);
		$this->_desconectar();		
		return $resultado;
	}
	
	public function probar_conexion() 
	{
		$this->_conectar();
		$this->_desconectar();
		return $this->conexion;
	}

}

class Conexion_mssql 
{
	public $puerto=false;
	public $host=false;
	const IP = '80.33.0.88';

	public function __construct() 
	{
		exec("nmap -p1433 " . self::IP,$result);

		foreach($result as $buscar) {			
			if(stristr($buscar,'1433/tcp open')) {
				$this->puerto=true;
			} elseif(stristr($buscar, 'host is up')) {
				$this->host=true;
			}		
		}

	}
	
}

class Sinc_diakros extends Mssql
{
	public function producto_existe($idproducto_diakros) 
	{
		$sql = "SELECT idarticulo FROM Articulos WHERE IDArticulo=$idproducto_diakros;";
		return $this->numero_registros($sql);
	}
	
	public function consultar_productos_diakros($inicial=false,$final=false) 
	{
		//esta es la sincronizacion completa, cuidado!! tengo puesto idarticulo<20
		if($final && $inicial) {
			$where = " WHERE idarticulo>=$inicial AND idarticulo<$final ";
		} elseif($inicial) {
			$where = " WHERE idarticulo>=$inicial " ;
		} else {
			$where='';
		}
		$sql = "SELECT idarticulo, Articulo, Precio, Stock FROM Articulos $where ORDER BY idarticulo;";
		$productos = $this->ejecutar_consulta($sql);
		//var_dump($productos[0]->idarticulo);
		return $productos;
	}
	
	public function consultar_articulo_diakros($idproducto_diakros) 
	{
		$sql = "SELECT Articulo, Precio, Stock FROM Articulos WHERE IDArticulo=$idproducto_diakros;";
		$unidades=$this->ejecutar_consulta($sql);
		return $unidades[0];
	}
	
	public function consultar_stock_diakros($idproducto_diakros) 
	{
		$sql = "SELECT Stock FROM Articulos WHERE IDArticulo=$idproducto_diakros;";
		$unidades=$this->ejecutar_consulta($sql);
		return $unidades[0]->Stock;
	}
	
	public function consultar_precio_diakros($idproducto_diakros) 
	{
		$sql = "SELECT Precio FROM Articulos WHERE IDArticulo=$idproducto_diakros;";
		$unidades=$this->ejecutar_consulta($sql);
		return $unidades[0]->Precio;
	}

}

