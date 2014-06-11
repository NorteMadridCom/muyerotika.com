<?php

class Combo extends Mysql
{
	
	//las opciones del select tal y como salen del
	//objeto mysql, es una matriz de objetos extends MYSQL
	private $_opciones = array();
	
	//campo id de la tabla
	private $_id;
	
	//nombre del campo que queremos que se vea en el option
	private $_mostrar;
	
	//el nombre que recibe en el formulario
	private $_nombre_combo;
	
	//id de la opcion que ha de aparecer selected
	private $_seleccionado;
	
	//si quiero un campo vacio al principio
	private $_vacio;
	
	//si ha de estar deshabilitado
	private $_disabled;
	
	//si ha de estar oculto
	private $_visible;
	
	//html5, si queremos que avise que se ha de seleccionar
	private $_required;
	
	//si queremos alguna accion ajax OnCLic=
	//como cadena de taxto que se agrega
	private $_eventos;
	
	public function __construct($nombre_combo, $tabla, $id, $mostrar, $seleccionado = false, $vacio = false, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = false, $eventos = null) 
	{
		unset($where);
		unset($orden);
		
		$this->_nombre_combo = $nombre_combo;
		$this->_id = $id;
		$this->_mostrar = $mostrar;
		$this->_seleccionado = $seleccionado;
		$this->_vacio = $vacio;
		$this->_disabled = $disabled;
		$this->_visible = $visible;
		$this->_required = $required;
		$this->_eventos = $eventos;
		
		if($filtro && $campo_eliminado) {
			$where = "WHERE $filtro AND $campo_eliminado = 0 ";
		} elseif($filtro && !$campo_eliminado) {
			$where = "WHERE $filtro ";
		} elseif(!$filtro && $campo_eliminado) {
			$where = "WHERE $campo_eliminado = 0 ";
		}
		
		if($campo_orden) {
			$orden = " ORDER BY $campo_orden ";
		}
		
		$sql="SELECT $id, $mostrar FROM $tabla $where $orden ;";
		$this->_opciones = parent::ejecutar_consulta($sql);
		
	}
	
	public function poner_combo()
	{

		if($this->_disabled) $desabilitado = ' disabled="disabled" ';

		if(!$this->_visible) $visible =' style="display: none" ';
		
		if($this->_required) $required =' required ';
		
		if(is_array($this->_opciones)) {
			
			echo '<select name="' . $this->_nombre_combo . '" ' . $desabilitado . $visible . $required . $this->_eventos . '>';
			
			if($this->_vacio) {
				echo '<option></option>'; 
			}
			
			foreach($this->_opciones as $objeto) {
				unset($matriz);
				foreach($objeto as $clave=>$valor)
				{
					$matriz[$clave]=$valor;
					//convertimos el objeto a matriz porque los nombres de las var de los obj no se pueden coger como cadenas
				}
				//if($matriz[$this->_mostrar]) {
					unset($selected);
					if($matriz[$this->_id] == $this->_seleccionado) {
							$selected=' selected="selected" ';
					} 
					echo '<option value="'.$matriz[$this->_id].'"'.$selected.'>'.$matriz[$this->_mostrar].'</option>';
				//}
			}
			
			echo '
				</select>
			';
			
		}
	}

}