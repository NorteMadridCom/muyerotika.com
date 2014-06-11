<?php

class Editar_tarifas
{

	public $error = false;
	private $_consulta_tarifas = object;
	
	public function formulario_general() 
	{
		echo '
			<table>
				<tr>
					<th width="100" class="titulos">Tarifa
					<th width="60" class="titulos">Descuento(%)
					<th class="titulos">Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="tarifa" value="" size="25" maxlength="100" required autofocus class="admin_caja"/>
						<td><input type="text" name="tarifa_dto" value="" size="5" maxlength="2" pattern="[0-9]{1,2}+" required class="admin_caja"/>
						<td><button name="accion" value="Nuevo" class="admin"><img src="img/nuevo.png" height="16" /></button>
					</form>
		';
		$sql_tarifas = "SELECT * FROM tarifas WHERE eliminado=0 ORDER BY tarifa_dto;";
		$tarifas= new Mysql();
		$this->error = !$tarifas->ejecutar_consulta($sql_tarifas);
		if($this->error !== true) {
			foreach($tarifas->registros as $tarifa) {
				echo '
					<tr>
						<form method="post" enctype="multipart/form-data" action="">							
							<td><input type="text" name="tarifa" value="'.$tarifa->tarifa.'" size="25" maxlength="100" required autofocus class="admin_caja"/>
							<td><input type="text" name="tarifa_dto" value="'.$tarifa->tarifa_dto.'" size="5" maxlength="2"  pattern="[0-9]{1,2}+" required class="admin_caja"/>
							<td><input type="hidden" name="idtarifa" value="' . $tarifa->idtarifa . '" />
								<button name="accion" value="Editar" class="admin"><img src="img/editar.png" height="16" /></button>
								<button name="accion" value="Eliminar" class="admin"><img src="img/eliminar.png" height="16" /></button>
						</form>
				';		
			}
		}
		
		echo '</table>';
		$tarifas->__destruct();
		$this->_tarifa_general();
	}
	
	private function _tarifa_general()
	{
		$tarifa_general= new Mysql();
		$tarifa_general->ejecutar_consulta("SELECT idtarifa FROM tarifas WHERE general=1;");
					
		echo '
			<form method="post" enctype="multipart/form-data" action="">							
				<td><h2 class="admin">Tarifa General: <h2>
				<td>';
		$general = new Combo('idtarifa', 'tarifas', 'idtarifa', 'tarifa', $tarifa_general->registros[0]->idtarifa, $vacio = true, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = false, $eventos = null);
		$general->poner_combo();
		echo '
				<td>
					<button name="accion" value="general" class="admin"><img src="img/actualizar.png" height="16" /></button>
			</form>
		';						
	}
	
	public function editar_tarifa_general($idtarifa) 
	{
		$tarifa_general=new Mysql();
		$sql="UPDATE tarifas SET general=0;";
		if($tarifa_general->resultado_consulta($sql)) {
			$sql_general="UPDATE tarifas SET general=1 WHERE idtarifa=$idtarifa;";
			$this->error = !$tarifa_general->resultado_consulta($sql_general);
		} else {
			$this->error= true;
		}
		
		$this->resultados();
	} 
	
	public function editar($valores) 
	{
		
		if($valores['idtarifa'] && $valores['accion']=='Editar') {
			$where = "idtarifa={$_POST['idtarifa']}";
			$insert = false;
		} elseif($valores['accion']=='Nuevo') {
			$where = null;
			$insert = true;
		}
		
		$sql = new Generar_sql($insert, $tabla='tarifas', $campo_id = 'idtarifa', $campos_obligatorios=array('tarifa','tarifa_dto'), $valores, $where);		
		$editar = new Mysql;
		$this->error = !$editar->resultado_consulta($sql->sql);
		$editar->__destruct();
		$sql->__destruct();
		
		$this->resultados();
		
	}
	
	public function eliminar($valores) //en un principio $_POST
	{
		//hay que eliminar las relaciones con los productos
		
		if(!$valores['subaccion']) {

			echo '
				<div style="color: red;">
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR LA TARIFA "<u><b>'
					. strtoupper($valores['tarifa'])
					.'"</b></u>!</h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="idtarifa" value="'. $valores['idtarifa'] .'">
						<input type="submit" name="subaccion" value="Si" class="admin">
						<input type="submit" name="subaccion" value="No" class="admin">
					</form>
				</div>
			';
			$info->__destruct;
			
		} elseif($valores['subaccion']=='Si') {
		
			$this->_eliminar($valores);
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>'; 
			
		} else {
			
			echo '<p>No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>';
			
		}
		
	}
	
	private function _eliminar($valores) 
	{
		$this->_consulta_tarifas = new Mysql;
		$this->_eliminar_tarifas_grupos($valores['idtarifa']);
		if($this->error === false) {
			$sql_tarifa="DELETE FROM tarifas WHERE idtarifa={$valores['idtarifa']};";
			$this->error = !$this->_consulta_tarifas->resultado_consulta($sql_tarifa);
		}
		$this->_consulta_tarifas->__destruct();
	}
	
	private function _eliminar_tarifas_grupos($idtarifa) 
	{
		$sql_grupos = "UPDATE tipos_clientes SET idtarifa=0 WHERE idtarifa=$idtarifa;";
		$this->error = !$this->_consulta_tarifas->resultado_consulta($sql_grupos);	
		
	}
	
	public function resultados() 
	{
		
		if($this->error === false) {
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>'; 
			
		} else {
			
			echo '<p>Hay un error. No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>';
		}
		
	}


	
}