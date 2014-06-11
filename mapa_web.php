<?php

class Mapa_web
{
	
	private $_mapa = object;

	public function __construct()
	{
		require_once 'includes/mysql.php';
		$this->_mapa = new Mysql;
		$this->familia_web();
		$this->familia();
		ECHO 
		$this->fabricante();
	}
	
	private function familia_web()
	{
		$sql="SELECT idfamilia_web, familia_web_menu as nombre, familia_web as enlace FROM familias_web WHERE eliminado = 0 ORDER BY orden, familia_web_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			echo '<ul class="diferentes_cajas"><h4 class="categoria_mapa">CATEGORÍAS ESPECIALES</h4>';
			foreach($this->_mapa->registros as $registro) {
				echo '<li class="producto_mapa"><a  class="marca_mapa" href="http://'.$_SERVER['SERVER_NAME'].'/index.php?familia_web='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
			}
			echo '</ul>';
		}
	}
	
	private function familia()
	{
		$sql="SELECT idfamilia, familia_menu as nombre, familia as enlace FROM familias WHERE eliminado = 0 ORDER BY orden, familia_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			echo '<ul class="diferentes_cajas"><h4 class="categoria_mapa">FAMILIAS DE PRODUCTOS</h4>';
			foreach($this->_mapa->registros as $registro) {
				echo '<li class="producto_mapa"><a class="marca_mapa" href="http://'.$_SERVER['SERVER_NAME'].'/index.php?familia='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
				$this->subfamilia($registro->idfamilia,$registro->enlace);
			}
			echo '</ul>';
		}
	}
	
	private function subfamilia($idfamilia, $familia)
	{
		$sql="SELECT idsubfamilia, subfamilia_menu as nombre, subfamilia as enlace FROM subfamilias WHERE idfamilia=$idfamilia AND eliminado = 0 ORDER BY orden, subfamilia_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			foreach($this->_mapa->registros as $registro) {
				echo '<ul>';
				if($registro->enlace) {
					echo '<li><a  class="marca_mapa" href="http://'.$_SERVER['SERVER_NAME'].'/index.php?familia='.$familia.'&subfamilia='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
					$this->subsubfamilia($registro->idsubfamilia,$familia,$registro->enlace);
				}
				echo '</ul>';
			}		
		}
	}
	
	private function subsubfamilia($idsubfamilia,$familia,$subfamilia)
	{
		$sql="SELECT idsubsubfamilia, subsubfamilia_menu as nombre, subsubfamilia as enlace FROM subsubfamilias WHERE idsubfamilia=$idsubfamilia AND eliminado = 0 ORDER BY orden, subsubfamilia_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			foreach($this->_mapa->registros as $registro) {
				echo '<ul>';
				if($registro->enlace) {
					echo '<li><a class="marca_mapa"  href="http://'.$_SERVER['SERVER_NAME'].'/index.php?familia='.$familia.'&subfamilia='.$subfamilia.'&subsubfamilia='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
				}
				echo '</ul>';

			}
		}
	}
	
	private function fabricante() //alias en marca
	{
		$sql="SELECT idfabricante, fabricante_menu as nombre, fabricante as enlace FROM fabricantes WHERE eliminado=0 ORDER BY orden, fabricante_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			echo '<ul class="diferentes_cajas"><h4 class="categoria_mapa">MARCAS</h4>';
			foreach($this->_mapa->registros as $registro) {
				echo '<li class="producto_mapa"><a  class="marca_mapa" href="http://'.$_SERVER['SERVER_NAME'].'/index.php?fabricante='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
				$this->linea($registro->idfabricante,$registro->enlace);
			}
			echo '</ul>';
		}
	}
	
	private function linea($idfabricante,$fabricante)
	{
		$sql="SELECT idlinea, linea_menu as nombre, linea as enlace FROM lineas WHERE idfabricante=$idfabricante AND eliminado = 0 ORDER BY orden, linea_menu;";
		$this->_mapa->ejecutar_consulta($sql);
		if(is_array($this->_mapa->registros)) {
			foreach($this->_mapa->registros as $registro) {
				echo '<ul>';
				if($registro->enlace) {
					echo '<li><a  class="marca_mapa" href="http://'.$_SERVER['SERVER_NAME'].'/index.php?fabricante='.$fabricante.'&linea='.$registro->enlace.'">'.$registro->nombre.'</a></li>';
				}
				echo '</ul>';

			}
		}
	}
	
}
