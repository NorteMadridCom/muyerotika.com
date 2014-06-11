<?php

class Buscador_texto {
	
	private $_eliminar = array(
		'a', 'ante', 'bajo', 'cabe', 'con', 'contra', 'de', 'desde', 'durante', 'en', 'entre',
		'excepto', 'hacia', 'hasta', 'mediante', 'para', 'por', 'pro', 'salvo', 'sin', 'so',
		'sobre', 'tras', 'el', 'lo', 'la', 'los', 'las', 'un', 'una', 'del', 'al',
		'este', 'ese', 'aquel', 'estos', 'esos', 'aquellos', 'estas',
		'esas', 'aquellas', 'otro', 'mucho', 'poco', 'que', 'pero', 'y'
	);
	
	private $_buscar = array();
	
	private $_fabricantes = array();
	
	private $_subsubfamilias = array();
	
	//cadena final de busqueda
	public $sql;
	
	public $error = false;
	
	public function __construct($texto) 
	{
		
		if(strlen($texto) >= 3) {
			$this->_asignar_buscar(explode(' ', strtolower($texto)));
			$this->_eliminar_palabras();
			//if($this->_buscar) $this->_lineas();
			//if($this->_buscar) $this->_fabricantes();
			//if($this->_buscar) $this->_familias();
			//if($this->_buscar) $this->_subfamilias();
			//if($this->_buscar) $this->_subsubfamilias();
			//if($this->_lineas) array_unique($this->_lineas);
			//if($this->_subsubfamilia) array_unique($this->_subsubfamilias);
			$this->_sql_where();
		} else {
			$this->error = true;
		}
	}

	private function _asignar_buscar($palabras) 
	{
		unset($this->_buscar);
		$this->_buscar  = $palabras;
		//var_dump($this->_buscar);
	}

	private function _eliminar_palabras()
	{
		/*******************************************************************************
		//Eliminamos las palabras que dificultan las busquedas, ya que se hace busqueda 
		//palabra a palabra, ofreciendo más resultados
		*******************************************************************************/
		
		foreach($this->_buscar as $palabra) {
			if(!in_array($palabra, $this->_eliminar)) {
				//$corregir_nombre = new Nombre_web($palabra);
				//$palabras[]=$corregir_nombre->tratar_nombre();
				$palabras[]=addslashes($palabra);
			}
		}
		$this->_asignar_buscar($palabras);
	}
	
	private function _idlinea($idfabricante) 
	{
		/**************************************************************
		//El objetivo es sacar las subsubfamilias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/

		$consulta_idlinea = new Mysql;
		$sql_linea = "SELECT idlinea FROM lineas WHERE eliminado = 0 AND idfabricante='$idfabricante';";
		$consulta_idlinea->ejecutar_consulta($sql_linea);
		foreach($consulta_idlinea->registros as $registro) {
			$this->_lineas[] = 'idlinea = ' . $registro->idlinea;
		}
		$consulta_idlinea->__destruct;
		
	}
	
	private function _lineas() 
	{
		/*************************************************************************
		//Eliminamos los lineas de las busquedas y lo ofremos como resultado
		//Alamacenasmo en resultado para realizar la busqueda sql compleja
		***************************************************************************/
		
		$consulta = new Mysql;
		foreach($this->_buscar as $palabra) {
			$sql_lineas = "SELECT idlinea FROM lineas WHERE eliminado = 0 AND linea  LIKE '%".$palabra."%';";
			$consulta->ejecutar_consulta($sql_lineas);
			if($consulta->numero_registros==1) {
				$this->_lineas[] = 'idlinea = ' . $consulta->registros[0]->idlinea;
			} else {
				$palabras[]=$palabra;
			}
		}
		$consulta->__destruct;
		$this->_asignar_buscar($palabras);
	}
	
	private function _fabricantes() 
	{
		/*************************************************************************
		//Eliminamos los fabricantes de las busquedas y lo ofremos como resultado
		//Alamacenasmo en resultado para realizar la busqueda sql compleja
		***************************************************************************/
		
		$consulta_fab = new Mysql;
		foreach($this->_buscar as $fabricante) {
			$sql_fabricante = "SELECT idfabricante FROM fabricantes WHERE eliminado = 0 AND fabricante LIKE '%" . $fabricante . "%';";
			$consulta_fab->ejecutar_consulta($sql_fabricante);
			if($consulta_fab->numero_registros==1) {
				$consulta_fab->registros[0]->idfabricante;
				$this->_idlinea($consulta_fab->registros[0]->idfabricante);
			} else {
				$palabras[]=$fabricante;
			}
		}
		$consulta_fab->__destruct;
		$this->_asignar_buscar($palabras);
	}
	
	private function _idsubsubfamilias($idsubfamilia) 
	{
		/**************************************************************
		//El objetivo es sacar las subsubfamilias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/

		$consulta_idsubsub = new Mysql;
		$sql_subsubfamilia = "SELECT idsubsubfamilia FROM subsubfamilias WHERE eliminado = 0 AND idsubfamilia='$idsubfamilia';";
		$consulta_idsubsub->ejecutar_consulta($sql_subsubfamilia);
		foreach($consulta_idsubsub->registros as $registro) {
			$this->_subsubfamilias[] = 'idsubsubfamilia = ' . $registro->idsubsubfamilia;
		}
		$consulta_idsubsub->__destruct;
		
	}
	
	private function _idsubfamilias($idfamilia) 
	{
		/**************************************************************
		//El objetivo es sacar las subfamilias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/
		
		$consulta_idsub = new Mysql;
		$sql_subfamilia = "SELECT idsubfamilia FROM subfamilias WHERE eliminado = 0 AND idfamilia='$idfamilia';";
		$consulta_idsub->ejecutar_consulta($sql_subfamilia);
		foreach($consulta_idsub->registros as $registro) {
			$this->_idsubsubfamilias($registro->idsubfamilia);
		}
		$consulta_idsub->__destruct;
		
	}
	
	private function _familias() 
	{
		/**************************************************************
		//El objetivo es sacar las familias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/
		$consulta_fam = new Mysql;
		foreach($this->_buscar as $familia) {
			$sql_familia = "SELECT idfamilia FROM familias WHERE eliminado = 0 AND familia LIKE '%" . $familia . "%';";
			$consulta_fam->ejecutar_consulta($sql_familia);
			if($consulta_fam->numero_registros==1) {
				$consulta_fam->registros[0]->idfamilia;
				$this->_idsubfamilias($consulta_fam->registros[0]->idfamilia);
			} else {
				$palabras[]=$familia;
			}
		}
		$consulta_fam->__destruct;
		$this->_asignar_buscar($palabras);
	}
	
	private function _subfamilias() 
	{
		/**************************************************************
		//El objetivo es sacar las subfamilias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/
		$consulta_sub = new Mysql;
		foreach($this->_buscar as $subfamilia) {
			$sql_subfamilia = "SELECT idsubfamilia FROM subfamilias WHERE eliminado = 0 AND subfamilia LIKE '%" . $subfamilia . "%';";
			$consulta_sub->ejecutar_consulta($sql_subfamilia);
			if($consulta_sub->numero_registros==1) {
				$this->_idsubsubfamilias($consulta_sub->registros[0]->idsubfamilia);
			} else {
				$palabras[]=$subfamilia;
			}
		}
		$consulta_sub->__destruct;
		$this->_asignar_buscar($palabras);
	}
	
	private function _subsubfamilias() 
	{
		/**************************************************************
		//El objetivo es sacar las subfamilias que se vean afectadas
		//si se afecta una familia, sacamos las subfamilias y luego las
		//subsubfamilias para consulta. NO lo complicamos mas con
		//busquedas con left join, queremos sacar productos, nada mas
		**************************************************************/
		$consulta_subsub = new Mysql;
		foreach($this->_buscar as $subsubfamilia) {
			$sql_subsubfamilia = "SELECT idsubsubfamilia FROM subsubfamilias WHERE eliminado = 0 AND subsubfamilia LIKE '%" . $subfamilia . "%';";
			$consulta_subsub->ejecutar_consulta($sql_subsubfamilia);
			if($consulta_subsub->numero_registros==1) {
				$this->_subsubfamilias[] = 'idsubsubfamilia = ' . $consulta_subsub->registros[0]->idsubsubfamilia;
			} else {
				$palabras[]=$subsubfamilia;
			}
		}
		$consulta_subsub->__destruct;
		$this->_asignar_buscar($palabras);
	}
	
	private function _sql_where() 
	{

		$where = ' WHERE l.idlinea = p.idlinea AND p.web = 1 AND p.eliminado = 0 AND p.profesional = 0 ';
		if($_SESSION['profesional']==1) $where = ' WHERE l.idlinea = p.idlinea AND p.web = 1 AND p.eliminado = 0 ';
		$campos = array('producto_nombre_web', 'producto_nombre','producto', 'descripcion');
		if($this->_buscar) {	
			foreach($this->_buscar as $palabras) {
				unset($prod_nombre_campo);
				foreach($campos as $campo) {
					$prod_nombre_campo[] = " CONVERT (`$campo` USING utf8) LIKE '%$palabras%' ";
				}
			$prod_nombre[] = '(' . implode(' OR ', $prod_nombre_campo) . ')';
			//$prod_descripcion[] = " descripcion LIKE '%" . $palabras . "%' ";
			}
		$where_prod_total = '(' . implode(' AND ', $prod_nombre) . ')';
		$where = "$where AND ( $where_prod_total ) ";
		
		} else {
			$this->error = true; //no es posible devolver todos los productos, tiene que haber elemento de búsqueda
		}
		
		if($this->error ===false) {
			$where = "$where AND ( $where_prod_total ) ";
			$this->sql = "SELECT *, l.dto_linea FROM productos p, lineas l $where ORDER BY p.producto_nombre_web, p.producto_nombre LIMIT 1000;";
		}
				
	}
	
	public function __destruct() {}
	
}

