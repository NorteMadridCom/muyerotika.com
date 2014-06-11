<?php

class Editar_fabricantes
{
	private $_campos_fabricante = array('idfabricante','fabricante','fabricante_menu');
	private $_campos_linea = array('idlinea','linea','linea_menu', 'idfabricante', 'dto_linea');
	private $_consulta_fabricantes = object;
	private $_fabricante = object;
	
	public function __construct() 
	{
		$this->_consulta_fabricantes = new Mysql();
	}
	
	public function formulario_general()
	{
		echo '
			<form method="post" enctype="multipart/form-data">
			<table style="float: left;">
				<tr>
					<th class="titulos">Marcas
					<th class="titulos">Líneas
					<th class="titulos">Acciones
				<tr>
		';
		$fabricante = new Seleccion_fabricantes();
		echo '<td>';
		if(!$_POST['idlinea']) {
			echo '<button class="admin" name="accion" value="Nuevo" ><img src="img/nuevo.png" height="16" /></button>';
			
		}
		if($_POST['idfabricante']) {
			echo '
					<button class="admin" name="accion" value="Editar"><img src="img/editar.png" height="16" alt="Editar" /></button>
					<button class="admin" name="accion" value="Eliminar"><img src="img/eliminar.png" height="16" alt="Eliminar" /></button>
			';
		}
		if(!$_POST['idfabricante']) {
			echo '<button name="accion" value="Ordenar_fabricante" class="admin"><img src="img/ordenar.png" height="16" alt="Ordenar" /></button>';
		} elseif(!$_POST['idlinea']) {
			echo '<button class="admin" name="accion" value="Ordenar_linea"><img src="img/ordenar.png" height="16" alt="Ordenar" /></button>';
		}
		echo '
			</table>
			</form>
		';

	}
	
	private function _consultar_fabricante($id,$tipo=null)
	{
		$tabla='fabricantes';
		$campos = $this->_campos_fabricante;
		if($tipo=='linea') {
			$tabla='lineas';
			$campos = $this->_campos_linea;
		}
		$sql = "SELECT * FROM $tabla WHERE {$campos[0]} = $id AND eliminado=0;";
		$this->_consulta_fabricantes->ejecutar_consulta($sql);
		$this->_fabricante=$this->_consulta_fabricantes->registros[0];		
	}
	
	public function editar_fabricante($fabricante)
	{
		$this->_fabricante = $fabricante;
		if($fabricante->subaccion == 'Terminar' ) {
			$this->_editar();
		} else {	
			if($_POST['subaccion']=='Editar Marca') {
				$campos = $this->_campos_fabricante;
				if($fabricante->idfabricante) $this->_consultar_fabricante($fabricante->idfabricante);	
			} elseif($_POST['subaccion']=='Editar Linea') {
				$campos = $this->_campos_linea;
				if($fabricante->idlinea) $this->_consultar_fabricante($fabricante->idlinea,'linea');
			} elseif(($fabricante->idlinea) || ($fabricante->idfabricante && $_POST['accion']=='Nuevo')) {
				//si $fabricante->linea = 0 no podemos
				//consulta si linea = 0
				$campos = $this->_campos_linea;
				if($fabricante->idlinea) $this->_consultar_fabricante($fabricante->idlinea,'linea');
				if($this->_fabricante->linea == '0') {
					if($_POST['accion']=='Nuevo') {
						echo '<p>No se puede añadir uno nuevo porque las hay líneas con Cero, edítelas primero.</p>';
						return;
					} else {
						$sql = "SELECT fabricante_menu FROM fabricantes WHERE idfabricante='{$this->_fabricante->idfabricante}';";
						$this->_consulta_fabricantes->ejecutar_consulta($sql); 
						echo '
							<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="accion" value="Editar" />
							<table>
								<tr>
									<th class="titulos">Marcas
									<th class="titulos">Líneas
								<tr>
									<td><input type="hidden" name="idfabricante" value="' . $this->_fabricante->idfabricante . '" /><input  class="admin_caja" style="background-color: #e1e1e1;" type="text" name="fabricante_menu" value="' . $this->_consulta_fabricantes->registros[0]->fabricante_menu . '" disabled />
									<td><input type="hidden" name="idlinea" value="' . $this->_fabricante->idlinea . '" /><input class="admin_caja" style="background-color: #e1e1e1;" type="text" name="linea_menu" value="' . $this->_fabricante->linea_menu . '" disabled />
								<tr>
									<td><input type="submit" name="subaccion" value="Editar Marca" class="admin"/>
									<td><input type="submit" name="subaccion" value="Editar Linea" class="admin"/>
							</table>
							</form>
						';
						return;
					}
				}
			} else {
				$campos = $this->_campos_fabricante;
				if($fabricante->idfabricante) $this->_consultar_fabricante($fabricante->idfabricante);
			}
			
			echo '
				<table>
					<thead>
					<tr>
						<th class="titulos">'. strtoupper($campos[1]) . '
						<th class="titulos">MENU '. strtoupper($campos[1]) . '
			';
			if($campos[4]) echo '<th class="titulos">DTO.(%)';
			echo '
						<th class="titulos">ACCIÓN
					<tbody>
				<form action="" method="post" enctype="multipart/form-data">
						<tr>	
							<input type="hidden" name="accion" value="'.$fabricante->accion.'" />	
							<input type="hidden" name="'.$campos[0].'" value="'.$this->_fabricante->$campos[0].'" />	
							<td><input class="admin_caja" type="text" size="15" name="'.$campos[1].'" value="'.$this->_fabricante->$campos[1].'" required pattern="[a-zA-Z0-9]+" autofocus>
							<td><input class="admin_caja" type="text" size="20" name="'.$campos[2].'" value="'.$this->_fabricante->$campos[2].'" required>
			';	
			
			if($campos[3]) echo '<input type="hidden" name="'.$campos[3].'" value="'.$this->_fabricante->$campos[3].'"  />';
			if($campos[4]) echo '<td><input class="admin_caja" type="text" size="5" name="'.$campos[4].'" value="'.$this->_fabricante->$campos[4].'" pattern="\d{1,2}(\.\d{1,2})?" >';
			echo '
					<td>
						<input type="submit" name="subaccion" value="Terminar" class="admin"/>
						<input type="reset" name="subaccion" value="Cancelar" class="admin"/>
				</form>
				</table>
			';			
		}

	}
	
	public function eliminar_fabricante($fabricante) 
	{
		
		if($fabricante->eliminar == 'No') {
			$this->formulario_general();
		} elseif($fabricante->eliminar == 'Si') {
			$this->_fabricante = (object) $_POST;
			$this->_eliminar();
		} else {
			if($fabricante->idlinea) {
				$campos=$this->_campos_linea;
				if($fabricante->idlinea) $this->_consultar_fabricante($fabricante->idlinea,'linea');
			} else {
				 $campos=$this->_campos_fabricante;
				 if($fabricante->idfabricante) $this->_consultar_fabricante($fabricante->idfabricante);
			}			
			echo '
				<div style="color: red">
				<h3>¡ALERTA! SE VAN A ELIMINAR LA SIGUIENTE '.$campos[1].':</h3>
				<h4>		
				'.$this->_fabricante->$campos[1].
				' - '.$this->_fabricante->$campos[2].
				'</h4>
				<form action="" method="post" enctype="multipart/form-data">
					<input type="submit" name="eliminar" value="Si" class="admin">
					<input type="submit" name="eliminar" value="No" class="admin">
					<input type="hidden" name="'.$campos[0].'" value="'.$fabricante->$campos[0].'" />
					<input type="hidden" name="accion" value="'.$fabricante->accion.'" />
				</form>
				</div>
			';
		}
	}
	
	private function _editar()
	{
		$tabla = 'fabricantes';
		$campos = $this->_campos_fabricante;
		if(isset($this->_fabricante->linea)) {
			$tabla = 'lineas';
			$campos = $this->_campos_linea;
		}
		$insert = false;
		if(!$this->_fabricante->$campos[0]) $insert = true;
		
		$nombre = new Nombre_web($this->_fabricante->$campos[1]);
		$this->_fabricante->$campos[1] = $nombre->tratar_nombre();

		$sql = new Generar_sql($insert, $tabla, $campos[0], $campos, (array) $this->_fabricante, $where =  null, null);
		$this->_consulta_fabricantes->resultado_consulta($sql->sql);
		
		$this->_resultado();
	}
	
	private function _eliminar()
	{
		if($this->_fabricante->idfabricante) {
			$sql_lineas = "UPDATE lineas SET eliminado = 1 WHERE idfabricante = {$this->_fabricante->idfabricante};";
			$sql_fabricantes = "UPDATE fabricantes SET eliminado = 1 WHERE idfabricante = {$this->_fabricante->idfabricante};";
			$this->_consulta_fabricantes->resultado_consulta($sql_lineas);
			$this->_consulta_fabricantes->resultado_consulta($sql_fabricantes);
		} elseif($this->_fabricante->idlinea) {
			$sql_lineas = "UPDATE lineas SET eliminado = 1 WHERE idlinea = {$this->_fabricante->idlinea};";
			$this->_consulta_fabricantes->resultado_consulta($sql_lineas);
		}
		
		$this->_resultado();		
	}
	
	private function _resultado()
	{
		if($this->_consulta_fabricantes->error === true) {
			echo "Hay algun problema y no se han podido guardar los datos. Póngase en contacto con el administrador del sistema.";
		} else {
			echo "Cambios realizados.<br>";
			unset($_POST);
			$this->formulario_general();
		}
	}
	
	public function __destruct()
	{
		$this->_consulta_fabricantes->__destruct();
	}

}