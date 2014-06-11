<?php

class Editar_tipos_clientes
{

	public $error = false;
	private $_consulta_tipos_clientes = object;
	
	public function formulario_general() 
	{
		$tarifa_inicial = new Combo('idtarifa', 'tarifas', 'idtarifa', 'tarifa', 'false', $vacio = true, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = false, $eventos = null) ;
		echo '
			<table>
				<tr>
					<th width="100" class="titulos">Grupo
					<th width="60" class="titulos">Tarifa
					<th width="60"  class="titulos">Prof.
					<th aling="center" class="titulos">Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="tipo_cliente" value="" size="25" maxlength="100" required autofocus  class="admin_caja"/>
						<td>'
		;
		$tarifa_inicial->poner_combo() ;
		echo '
						<td style="padding-left: 20px;"><input type="checkbox" name="profesional" value="1" />
						<td><button name="accion" value="Nuevo"><img src="./img/nuevo.png" height="16" /></button>
					</form>
		';
		
		$sql_tipos_clientes = "SELECT * FROM tipos_clientes ORDER BY tipo_cliente;";
		$tipos_clientes= new Mysql();
		$this->error = !$tipos_clientes->ejecutar_consulta($sql_tipos_clientes);
		if($this->error !== true) {
			foreach($tipos_clientes->registros as $tipo_cliente) {
				$prof="";
				if($tipo_cliente->profesional==1) $prof = " checked=checked ";
				echo '
					<tr>
						<form method="post" enctype="multipart/form-data" action="">							
							<td><input type="text" name="tipo_cliente" value="'.$tipo_cliente->tipo_cliente.'" size="25" maxlength="100" required autofocus  class="admin_caja"/>
							<td>
				';
				$tarifa = new Combo('idtarifa', 'tarifas', 'idtarifa', 'tarifa', $tipo_cliente->idtarifa, $vacio = true, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = false, $eventos = null) ;
				$tarifa->poner_combo();
				echo '
							<td style="padding-left: 20px;"><input type="checkbox" name="profesional" value="1" '.$prof.'/>
							<td><input type="hidden" name="idtipo_cliente" value="' . $tipo_cliente->idtipo_cliente . '" />
								<button name="accion" value="Editar"><img src="./img/editar.png" height="16" /></button>
								<button name="accion" value="Eliminar"><img src="./img/eliminar.png" height="16" /></button>
						</form></div>
				';		
			}
		}
		
		echo '</table>';
		$tipos_clientes->__destruct();
	}
	
	public function editar($valores) 
	{
		
		if($valores['idtipo_cliente'] && $valores['accion']=='Editar') {
			$where = "idtipo_cliente={$_POST['idtipo_cliente']}";
			$insert = false;
		} elseif($valores['accion']=='Nuevo') {
			$where = null;
			$insert = true;
		}
		
		$sql = new Generar_sql($insert, $tabla='tipos_clientes', $campo_id = 'idtipo_cliente', $campos_obligatorios=array('tipo_cliente'), $valores, $where);		
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
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR EL GRUPO "<u><b>'
					. strtoupper($valores['tipo_cliente'])
					.'</b></u>"!</h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="idtipo_cliente" value="'. $valores['idtipo_cliente'] .'">
						<input type="submit" name="subaccion" value="Si" class="admin">
						<input type="submit" name="subaccion" value="No" class="admin">
					</form>
				</div>
			';
			$info->__destruct;
			
		} elseif($valores['subaccion']=='Si') {
		
			$this->_eliminar($valores);
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>'; 
			
		} else {
			
			echo '<p>No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
			
		}
		
	}
	
	private function _eliminar($valores) 
	{
		$this->_consulta_tipos_clientes = new Mysql;
		$this->_eliminar_tipos_clientes_grupos($valores['idtipo_cliente']);
		if($this->error === false) {
			$sql_tipo_cliente="DELETE FROM tipos_clientes WHERE idtipo_cliente={$valores['idtipo_cliente']};";
			$this->error = !$this->_consulta_tipos_clientes->resultado_consulta($sql_tipo_cliente);
		}
		$this->_consulta_tipos_clientes->__destruct();
	}
	
	private function _eliminar_tipos_clientes_grupos($idtipo_cliente) 
	{
		$sql_grupos = "UPDATE clientes SET idtipo_cliente=1 WHERE idtipo_cliente=$idtipo_cliente;";
		$this->error = !$this->_consulta_tipos_clientes->resultado_consulta($sql_grupos);	
		
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