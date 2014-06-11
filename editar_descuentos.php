<?php

class Editar_descuentos
{

	public $error = false;
	private $_consulta_descuentos = object;
	
	public function formulario_general() 
	{
		echo '
			<table>
				<tr>
					<th width="100" class="titulos" >Volumen
					<th width="60" class="titulos">Descuento(%)
					<th class="titulos">Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="volumen" value="" size="25" maxlength="100" required autofocus class="admin_caja"/>
						<td><input type="text" name="dto_volumen" value="" size="5" maxlength="2" pattern="[0-9]{1,2}+" required class="admin_caja"/>
						<td><button name="accion" value="Nuevo"><img src="./img/nuevo.png" height="16" /></button>
					</form>
		';
		$sql_descuentos = "SELECT * FROM dtos_volumen WHERE eliminado=0 ORDER BY dto_volumen;";
		$descuentos= new Mysql();
		$this->error = !$descuentos->ejecutar_consulta($sql_descuentos);
		if($this->error !== true) {
			foreach($descuentos->registros as $descuento) {
				echo '
					<tr>
						<form method="post" enctype="multipart/form-data" action="">							
							<td><input type="text" name="volumen" value="'.$descuento->volumen.'" size="25" maxlength="100" required autofocus  class="admin_caja"/>
							<td><input type="text" name="dto_volumen" value="'.$descuento->dto_volumen.'" size="5" maxlength="2"  pattern="[0-9]{1,2}+" required  class="admin_caja"/>
							<td><input type="hidden" name="iddto_volumen" value="' . $descuento->iddto_volumen . '" />
								<button name="accion" value="Editar"><img src="./img/editar.png" height="16" /></button>
								<button name="accion" value="Eliminar"><img src="./img/eliminar.png" height="16" /></button>
						</form>
				';		
			}
		}
		
		echo '</table>';
		$descuentos->__destruct();
	}
	
	public function editar($valores) 
	{
		
		if($valores['iddto_volumen'] && $valores['accion']=='Editar') {
			$where = "iddto_volumen={$_POST['iddto_volumen']}";
			$insert = false;
		} elseif($valores['accion']=='Nuevo') {
			$where = null;
			$insert = true;
		}
		
		$sql = new Generar_sql($insert, $tabla='dtos_volumen', $campo_id = 'iddto_volumen', $campos_obligatorios=array('volumen','dto_volumen'), $valores, $where);		
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
				<div style="color: red; ">
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR DESCUENTO PARA EL VOLUMEN DE COMPRA "<u><b>'
					. strtoupper($valores['volumen'])
					.'"</b></u>!</h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="iddto_volumen" value="'. $valores['iddto_volumen'] .'">
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
		$this->_consulta_descuentos = new Mysql;
		if($this->error === false) {
			$sql_descuento="DELETE FROM dtos_volumen WHERE iddto_volumen={$valores['iddto_volumen']};";
			$this->error = !$this->_consulta_descuentos->resultado_consulta($sql_descuento);
		}
		$this->_consulta_descuentos->__destruct();
	}
	
	public function resultados() 
	{
		
		if($this->error === false) {
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>'; 
			
		} else {
			
			echo '<p>Hay un error. No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
		}
		
	}


	
}