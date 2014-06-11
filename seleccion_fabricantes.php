<?php

class Seleccion_fabricantes
{
	public function __construct($requerido=false) 
	{
		
		$eventos = 'onchange=submit()';
		
		if($_POST['idfabricante_ant']!=$_POST['idfabricante']) unset($_POST['idlinea']);
		
		if($_POST['idlinea']) {
			$sql_linea = "SELECT idfabricante FROM lineas WHERE idlinea ='{$_POST['idlinea']}';";
			$linea = new Mysql();
			$linea->ejecutar_consulta($sql_linea);
			$_POST['idfabricante']=$linea->registros[0]->idfabricante;	
		}
		
		echo '<input type="hidden" name="idfabricante_ant" value="'.$_POST['idfabricante'].'" />';
		
		$linea_visible = true;
		$filtro_linea = " linea <> '0' ";
		
		echo '<td>';
		$fabricante = new Combo('idfabricante', 'fabricantes', 'idfabricante', 'fabricante_menu', $_POST['idfabricante'], true, false, 'eliminado', 'orden',false,true, $requerido, $eventos);
		$fabricante->poner_combo();
	
		if($_POST['idfabricante']) {
			
			$filtro_linea .= " AND idfabricante = '{$_POST['idfabricante']}' ";
		
			$sql_cero="SELECT idlinea FROM lineas WHERE eliminado=0 AND linea = '0' AND idfabricante = '{$_POST['idfabricante']}';";
			$linea_cero= new Mysql;
			$linea_cero->ejecutar_consulta($sql_cero);
			if($linea_cero->numero_registros) {
				$linea_visible = false;
				echo '<input type="hidden" name="idlinea" value="'. $linea_cero->registros[0]->idlinea .'">';
			} 
				
		}
		
		echo '<td>';
		
		if($linea_visible) {
			$linea = new Combo('idlinea', 'lineas', 'idlinea', 'linea_menu', $_POST['idlinea'], true, $filtro_linea, 'eliminado', 'orden',false,true,$requerido,$eventos);
			$linea->poner_combo();
		}
		
	}
}
