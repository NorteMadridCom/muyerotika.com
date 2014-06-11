<?php

class Editar_envios
{

	public $error = false;
	private $_consulta_envios = object;
	
	public function formulario_general() 
	{
		echo '
			<table>
				<tr>
					<th width="100" class="titulos">Volumen
					<th width="60" class="titulos">Gatos de envio(€)
					<th class="titulos">Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="hasta_neto" value="" size="25" maxlength="100" required autofocus class="admin_caja"/>
						<td><input type="text" name="gastos_envio" value="" size="5" maxlength="2" pattern="[0-9]{1,2}+" required class="admin_caja"/>
						<td><button name="accion" value="Nuevo"><img src="./img/nuevo.png" height="16" /></button>
					</form>
		';
		$sql_envios = "SELECT * FROM gastos_envios ORDER BY hasta_neto;";
		$envios = new Mysql();
		$this->error = !$envios->ejecutar_consulta($sql_envios);
		if($this->error !== true) {
			foreach($envios->registros as $envio) {
				echo '
					<tr>
						<form method="post" enctype="multipart/form-data" action="">							
							<td><input type="text" name="hasta_neto" value="'.$envio->hasta_neto.'" size="25" maxlength="100" required autofocus class="admin_caja"/>
							<td><input type="text" name="gastos_envio" value="'.$envio->gastos_envio.'" size="5" maxlength="25"  pattern="[0-9]{1,2}+" required class="admin_caja"/>
							<td><input type="hidden" name="idgastos_envio" value="' . $envio->idgastos_envio . '" />
								<button name="accion" value="Editar"><img src="./img/editar.png" height="16" /></button>
								<button name="accion" value="Eliminar"><img src="./img/eliminar.png" height="16" /></button>
						</form>
				';		
			}
		}
		
		echo '</table>';
		echo '<p>Los tramos se expresan desde 0 hasta el menor y desde el mayor al infinito.</p></div>';
		//$descuentos->__destruct();
	}
	
	public function editar($valores) 
	{
		
		if($valores['idgastos_envio'] && $valores['accion']=='Editar') {
			$where = "idgastos_envio={$_POST['idgastos_envio']}";
			$insert = false;
		} elseif($valores['accion']=='Nuevo') {
			$where = null;
			$insert = true;
		}
		
		$sql = new Generar_sql($insert, $tabla='gastos_envios', $campo_id = 'idgastos_envio', $campos_obligatorios=array('hasta_neto','gastos_envio'), $valores, $where);		
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
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR GASTO DE ENVÍO PARA EL VOLUMEN DE COMPRA "<u><b>'
					. strtoupper($valores['volumen'])
					.'"</b></u>!</h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="idgastos_envio" value="'. $valores['idgastos_envio'] .'">
						<input type="submit" name="subaccion" value="Si" class="admin">
						<input type="submit" name="subaccion" value="No" class="admin">
					</form>
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
			$sql_descuento="DELETE FROM gastos_envios WHERE iddto_volumen={$valores['idgastos_envio']};";
			$this->error = !$this->_consulta_descuentos->resultado_consulta($sql_descuento);
		}
		$this->_consulta_descuentos->__destruct();
	}
	
	public function resultados() 
	{
		
		if($this->error === false) {
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>'; 
			
		} else {
			
			echo '<p>Hay un error. No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>';
		}
		
	}


	
}
