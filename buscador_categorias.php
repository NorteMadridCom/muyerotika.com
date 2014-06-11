<?php

class Buscador_categorias
{

	public $sql;
	public $error=false;

	public function __construct() 
	{
		$tabla="SELECT p.* FROM productos p ";
		$where=" WHERE p.eliminado=0 AND p.web=1 ";
		//se puede añadir que si la sesion es de admin, quitar lo de web=1
		
		if($_POST['idsubsubfamilia']) {
			$where .= " AND p.idsubsubfamilia={$_POST['idsubsubfamilia']} ";
		} elseif($_POST['idsubfamilia']) {
			$tabla .= ", subsubfamilias ss ";
			$where .= " AND p.idsubsubfamilia=ss.idsubsubfamilia AND ss.idsubfamilia={$_POST['idsubfamilia']} ";
		} elseif($_POST['idfamilia']) {
			$tabla .= ", subsubfamilias ss, subfamilias s ";
			$where .= " AND p.idsubsubfamilia=ss.idsubsubfamilia AND ss.idsubfamilia=s.idsubfamilia AND s.idfamilia={$_POST['idfamilia']} ";
		}
		
		if($_POST['idlinea']) {
			$where .= " AND p.idlinea={$_POST['idlinea']} ";
		} elseif($_POST['idfabricante']) {
			$tabla .= ", lineas l ";
			$where .= " AND p.idlinea=l.idlinea AND l.idfabricante={$_POST['idfabricante']}";
		}
		
		$fin = " ORDER BY p.orden LIMIT 0,1000;";
		$this->sql=$tabla.$where.$fin;
		//echo $this->sql;
		
	}
	
	public function __destruct() {}
	
}