<?php


//require_once 'includes/mysql.php';
//require_once 'includes/class.select.php';

class Ordenar
{
	
	/*********************************
	$tabla = nombre tabla sql
	$id = nombre campo id de la tabla
	$mostrar = nombre del campo que queremos que se vea en el compo
	$campo_eliminado = NOMBRE del campo eliminado
	los elementos se muestra de forma alfabética segun el campo a mostrar
	en vez de campo orden, que es el quiero modificar
	************************************/
	
	private $_id;
	private $_orden;
	private $_tabla;
	private $_filtro;
	
	public $error=false;
	
	public function __construct($tabla,$id,$mostrar,$campo_eliminado = 'eliminado', $orden = 'orden', $filtro = NULL) 
	{
		
		$this->_id = $id;
		$this->_orden = $orden;
		$this->_tabla = $tabla;
		
		if($filtro) {
			$this->_filtro = $filtro;
		} else {
			$this->_filtro = '1=1';//necesario para no 'joder' la consulta
		}
		
		if($campo_eliminado) {
			if(is_bool($campo_eliminado)) {
				$campo_eliminado = 'eliminado';
			}
		}
		
		if(!$_POST['ordenar']) {
		
			echo '<form action="" method="post" enctype="multipart/form-data">';
			
			if(is_array($_POST)) {
				foreach($_POST as $clave => $valor){
					if($clave!=$id) {
						echo '<input type="hidden" name="'.$clave.'" value="'.$valor.'" />';
					}
				}
			}
			
			for($i=0;$i<=count($_POST[$id]);$i++) {
				if($_POST[$id][$i]) {
					$combo[$i] = new Combo(
						$id."[".$i."]", 
						$tabla, 
						$id, 
						$mostrar, 
						$seleccionado = $_POST[$id][$i], 
						$vacio = true, 
						$filtro, 
						$campo_eliminado, 
						$mostrar, 
						$disabled = false, 
						$visible = true, 
						$required = false, 
						$eventos = 'onchange=submit()'
					);
					$combo[$i]->poner_combo();
					echo '<br>';
					$filtro .= " AND $id != ".$_POST[$id][$i];
				} else {
					break;
				}
			}
			
			$combo[$i] = new Combo(
				$id."[".$i."]", 
				$tabla, 
				$id, 
				$mostrar, 
				$seleccionado = $_POST[$id][$i], 
				$vacio = true, 
				$filtro, 
				$campo_eliminado, 
				$mostrar, 
				$disabled = false, 
				$visible = true, 
				$required = false, 
				$eventos = 'onchange=submit()'
			);
			$combo[$i]->poner_combo();
			echo '<br>';
	
			echo '<input type="submit" name="ordenar" value="Ordenar" /></form>';
		}
		
	}
	
	public function ordenacion($link=true) 
	{

		$limit = 100;//desordeno toda la tabla, pero he de poner este valor en TODAS LAS TABLAS
		if(count($_POST[$this->_id])>$limit) {
			$limit = 1000;
		}
		$sql_desordenar = "UPDATE {$this->_tabla} SET {$this->_orden}=$limit WHERE {$this->_filtro};"; 
		$ordenar = new Mysql;
		$this->error = !$ordenar->resultado_consulta($sql_desordenar);
		if($this->error === false) {
			foreach($_POST[$this->_id] as $orden => $id) {
				if($id) {
					$sql_ordenar = "UPDATE {$this->_tabla} SET {$this->_orden}=$orden WHERE {$this->_id}=$id;";
					if(!$ordenar->resultado_consulta($sql_ordenar)) { 
						$this->error = true;
						break;
					}
				}
			}
		}
		
		$this->imprimir_fin($link);
	}
	
	public function imprimir_fin($link=true)
	{
		/****************************************************************
		// En el caso de no mostar el link por ser un emergente
		*******************************************************************/
		$volver='<a href="'.$_SERVER['REQUEST_URI'].'">Volver</a>';
		if($link===false) $volver='';
		if($this->error === false) {
			echo "<center><h4>Operación realizada con éxito.</h4>$volver</center>"; 
			
		} else {
			
			echo "<center><h4>Existen errores y no se ha ordenado correctamente.</h4>$volver</center>";
		}
	}
	

	public function __destruct() {}
	
}	
	
	
