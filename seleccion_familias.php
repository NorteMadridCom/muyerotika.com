<?php

class Seleccion_familias
{
		
	public function __construct($requerido=false)
	{
		
		//hay que poner los tres y poner los filtos si es que existen
		//($nombre_combo, $tabla, $id, $mostrar, $seleccionado = false, $vacio = false, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = false)
		
		$eventos = 'onchange=submit()';
		
		if($_POST['idfamilia_ant']!=$_POST['idfamilia']) {
			unset($_POST['idsubfamilia']);
			unset($_POST['idsubsubfamilia']);
		} elseif($_POST['idsubfamilia_ant']!=$_POST['idsubfamilia']) {
			unset($_POST['idsubsubfamilia']);
		}
		
		if($_POST['idsubsubfamilia']) {
			$sql_subfam_fam = "SELECT s.idsubfamilia, s.idfamilia FROM subfamilias s, subsubfamilias ss WHERE ss.idsubsubfamilia = {$_POST['idsubsubfamilia']} AND ss.idsubfamilia = s.idsubfamilia;";
			$subfam_fam = new Mysql;
			$subfam_fam->ejecutar_consulta($sql_subfam_fam);
			$_POST['idsubfamilia']=$subfam_fam->registros[0]->idsubfamilia;
			$_POST['idfamilia']=$subfam_fam->registros[0]->idfamilia;
		} elseif($_POST['idsubfamilia']) {
			$sql_fam = "SELECT s.idsubfamilia, s.idfamilia FROM subfamilias s WHERE s.idsubfamilia = {$_POST['idsubfamilia']};";
			$_fam = new Mysql;
			$_fam->ejecutar_consulta($sql_fam);
			$_POST['idfamilia']=$_fam->registros[0]->idfamilia;
		}
		
		
		echo '<input type="hidden" name="idfamilia_ant" value="'.$_POST['idfamilia'].'" />';
		echo '<input type="hidden" name="idsubfamilia_ant" value="'.$_POST['idsubfamilia'].'" />';
		
		$filtro_subfam = " subfamilia <> '0' ";
		$filtro_subsubfam = " subsubfamilia <> '0' ";
		
		$subfamilia_visible = true;
		$subsubfamilia_visible = true;
		
		echo '<td>';
		$familia = new Combo('idfamilia', 'familias', 'idfamilia', 'familia_menu', $_POST['idfamilia'], true, false, 'eliminado', 'orden',false,true, $requerido, $eventos);
		$familia->poner_combo();
		//echo '<input type="hidden" name="familia_ant" value="'.$_POST['familia'].'" />'; //ver si acumulo en resultado anterior
		
		if($_POST['idfamilia']) {
			
			$filtro_subfam .= " AND idfamilia = '{$_POST['idfamilia']}' ";
		
			$sql_familia_cero="SELECT idsubfamilia FROM subfamilias WHERE eliminado=0 AND subfamilia = '0' AND idfamilia = '{$_POST['idfamilia']}';";
			$familia_cero= new Mysql;
			$familia_cero->ejecutar_consulta($sql_familia_cero);
			if($familia_cero->numero_registros) {
				$subfamilia_visible = false;
				echo '<input type="hidden" name="idsubfamilia" value="'. $familia_cero->registros[0]->idsubfamilia .'">';
				$sql_subfamilia_cero="SELECT idsubsubfamilia FROM subsubfamilias WHERE eliminado=0 AND subsubfamilia = '0' AND idsubfamilia = '{$familia_cero->registros[0]->idsubfamilia}';";
				$subfamilia_cero= new Mysql;
				$subfamilia_cero->ejecutar_consulta($sql_subfamilia_cero);
				$subsubfamilia_visible = false;
				echo '<input type="hidden" name="idsubsubfamilia" value="'. $subfamilia_cero->registros[0]->idsubsubfamilia .'">';
			} 
				
		}
		
		echo '<td>';
		
		if($subfamilia_visible) {
			$subfamilia = new Combo('idsubfamilia', 'subfamilias', 'idsubfamilia', 'subfamilia_menu', $_POST['idsubfamilia'], true, $filtro_subfam, 'eliminado', 'orden',false,true,$requerido,$eventos);
			$subfamilia->poner_combo();
		}
	
	
		if($_POST['idsubfamilia']) {
			
			$filtro_subsubfam .= " AND idsubfamilia = '{$_POST['idsubfamilia']}' ";
			
			$sql_subfamilia_cero="SELECT idsubsubfamilia FROM subsubfamilias WHERE eliminado=0 AND subsubfamilia = '0' AND idsubfamilia = '{$_POST['idsubfamilia']}';";
			$subfamilia_cero= new Mysql;
			$subfamilia_cero->ejecutar_consulta($sql_subfamilia_cero);
			if($subfamilia_cero->numero_registros) {
				$subsubfamilia_visible = false;
				echo '<input type="hidden" name="idsubsubfamilia" value="'. $subfamilia_cero->registros[0]->idsubsubfamilia .'">';
			}
			
		} elseif($_POST['familia']) {
			$filtro_subsubfam .= " AND idsubfamilia in (SELECT idsubfamilia FROM subfamilias WHERE eliminado = 0 AND subfamilia <> '0' AND idfamilia = '{$_POST['idfamilia']}') ";
		}
		
		echo '<td>';
		
		if($subsubfamilia_visible) {
			$subsubfamilia = new Combo('idsubsubfamilia', 'subsubfamilias', 'idsubsubfamilia', 'subsubfamilia_menu', $_POST['idsubsubfamilia'], true, $filtro_subsubfam, 'eliminado', 'orden', false, $subsubfamilia_visible, $requerido, $eventos);
			$subsubfamilia->poner_combo();
		}
		
	}
	
	
}	
