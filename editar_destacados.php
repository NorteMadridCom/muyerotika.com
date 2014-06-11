<?php

class Editar_destacados
{
	
	private $_consulta_destacados = object;
	public $error = false;
	private $_config = object;
	
	public function __construct($config) 
	{
		$this->_config=$config;
	}
	
	public function formulario_general() 
	{
		echo '
			<table>
				<tr>
					<th width="60">Categoría destacada
					<th width="60">Categoría destacada menu
					<th>Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="familia_web" value="" size="20" maxlength="100" pattern="[a-zA-Z0-9_]+" required autofocus/>
						<td><input type="text" name="familia_web_menu" value="" size="30" maxlength="200" required/>
						<td><button name="accion" value="Nuevo"><img src="./img/nuevo.png" height="16" /></button>
					</form>
		';
		$sql_destacados = "SELECT * FROM familias_web WHERE eliminado=0 ORDER BY orden;";
		$destacados = new Mysql();
		$this->error = !$destacados->ejecutar_consulta($sql_destacados);
		if($this->error !== true) {
			foreach($destacados->registros as $destacado) {
				echo '
					<tr>
						<form method="post" enctype="multipart/form-data" action="">							
							<td><input type="text" name="familia_web" value="'.$destacado->familia_web.'" size="20" maxlength="100" pattern="[a-zA-Z0-9_]+" required autofocus/>
							<td><input type="text" name="familia_web_menu" value="'.$destacado->familia_web_menu.'" size="30" maxlength="200" required/>
							<td><input type="hidden" name="idfamilia_web" value="' . $destacado->idfamilia_web . '" />
								<button name="accion" value="Editar"><img src="./img/editar.png" height="16" /></button>
								<button name="accion" value="Eliminar"><img src="./img/eliminar.png" height="16" /></button>
								<button name="accion" value="Productos"><img src="./img/agregar.png" height="16" /></button>
							</form>
				';		
			}
		}
		echo '
			<tr>
				<td colspan="3">
					<form method="post" enctype="multipart/form-data" action="">
						<input type="submit" name="accion" value="Ordenar" />
						<input type="submit" name="accion" value="Portada" />
					</form>
		';
		echo '</table>';
		$destacados->__destruct();
	}
	
	public function agregar_productos($valores)  //este form lo dejamos para prod
	{	
		$this->_consulta_destacados = new Mysql();
		
		if($valores['subaccion']=='Agregar') {
			
			$this->_buscar_producto($valores);
			
		} elseif($valores['subaccion']== 'Eliminar') {
			
			$this->_eliminar_producto($valores);
		
		} else {
				
			$sql_destacados = "
				SELECT 
					p.* 
				FROM 
					productos_familias_web pf, 
					productos p 
				WHERE
					pf.idfamilia_web={$valores['idfamilia_web']} AND 
					p.idproducto=pf.idproducto AND
					p.eliminado=0 
				ORDER BY 
					orden 
				LIMIT 
					0,1000;
			";
			$this->_consulta_destacados->ejecutar_consulta($sql_destacados);
				
			//hay que poner un formulario de busqueda y que los resultados con boton agregar (+)
			//al darle agregar, mantenemos los valores pero lo quitamos del resultado pero mantenemos la busqueda
			//hay qye poner eliminar productos
			
			echo '
				<form method="post" enctype="multipart/form-data" action="">
					<input type="hidden" name="accion" value="'.$valores['accion'].'" />
					<input type="hidden" name="idfamilia_web" value="'.$valores['idfamilia_web'].'" />				
					<input type="submit" name="subaccion" value="Agregar" />
				</form>
			';
			
			if($this->_consulta_destacados->numero_registros>0) {
				foreach($this->_consulta_destacados->registros as $producto_destacado) {
					//echo $producto_destacado->idproducto;
					echo '
						<form method="post" enctype="multipart/form-data" action="">
							<input type="hidden" name="accion" value="'.$valores['accion'].'" />
							<input type="hidden" name="idfamilia_web" value="'.$valores['idfamilia_web'].'" />
							<input type="hidden" name="idproducto" value="'.$producto_destacado->idproducto.'" />
							'.$producto_destacado->producto_nombre.' 
							<input type="submit" name="subaccion" value="Eliminar" />
						</form>
					';
				}
			}
			
		}
		
		//$this->resultados();
		$this->_consulta_destacados->__destruct();
		
	}
	
	private function _eliminar_producto($valores) {
		$sql_eliminar_producto = "DELETE FROM productos_familias_web WHERE idproducto={$valores['idproducto']} AND idfamilia_web={$valores['idfamilia_web']};";
		$this->error = !$this->_consulta_destacados->resultado_consulta($sql_eliminar_producto);
		$this->resultados();
	}
	
	private function _buscar_producto($valores) 
	{
		$buscar = new Buscador_general();
		if($_POST['buscar']=='Buscar') {
			echo '
				<form method="post" enctype="multipart/form-data" action="">
					<input type="hidden" name="accion" value="'.$valores['accion'].'" />
					<input type="hidden" name="subaccion" value="'.$valores['subaccion'].'" />
					<input type="hidden" name="idfamilia_web" value="'.$valores['idfamilia_web'].'" />
			';
			$buscar->resultados();
			foreach($buscar->registros as $producto) {
				if(is_object($producto)) {
					echo $producto->producto_nombre.'
						<input type="checkbox" name="poner_categoria['.$producto->idproducto.']" value="'.$producto->idproducto.'" />
						<br>
					';
				}
			}
			echo '
					<input type="submit" name="agregar" value="Agregar" />
				</form>
			';
		} elseif($_POST['agregar']=='Agregar') {
			if(is_array($_POST['poner_categoria'])) {
				foreach($_POST['poner_categoria'] as $valor) {
					$insertar = new Mysql;
					$sql_insertar = "INSERT INTO productos_familias_web SET idproducto={$valor}, idfamilia_web={$valores['idfamilia_web']};";
					$this->error = !$insertar->resultado_consulta($sql_insertar);
					$insertar->__destruct();
					$this->resultados();	
				} 
			} else {
				echo '<center><h4>No se ha realizado ninguna acción.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
			}
		} else {
			$buscar->formulario_busqueda();
		}
	}

	public function portada($valores)
	{
		$orden=new Mysql;
		
		if($valores['subaccion']) {
			
			$sql_portada_cero = "UPDATE familias_web SET portada=0;";
			$this->error = !$orden->resultado_consulta($sql_portada_cero);
			
			if($valores['subaccion']=='Aceptar' && !$this->error) {
				$sql_portada = "UPDATE familias_web SET portada=1 WHERE idfamilia_web={$valores['idportada']};";
				$this->error = !$orden->resultado_consulta($sql_portada);
			}
			
			$this->resultados();
			
		} else {
				
			$sql_orden="SELECT * FROM familias_web WHERE eliminado=0 ORDER by orden LIMIT 0,100;";
			if($orden->ejecutar_consulta($sql_orden)) {
				echo '
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'.$valores['accion'].'" />
				';
				if(is_array($orden->registros)) {
					foreach($orden->registros as $fam_web) {
						$checked = '';
						if($fam_web->portada == '1') {
							$checked = ' checked="checked"';
						}
						echo $fam_web->familia_web_menu . '<input type="radio" name="idportada" value="'.$fam_web->idfamilia_web.'" '.$checked.' /><br>';
					}
				} else {
					var_dump($orden);
					echo 'No se pueden motrar datos';
				}	
				echo '
						<input type="submit" name="subaccion" value="Ninguno" />
						<input type="submit" name="subaccion" value="Aceptar" />
					</form>
				';
			} else {
				$this->error = true;
			}
				
		}
		
		$orden->__destruct();
				
	}
	
	public function editar($valores) 
	{
		
		if($valores['idfamilia_web'] && $valores['accion']=='Editar') {
			$where = "idfamilia_web={$_POST['idfamilia_web']}";
			$sql = new Generar_sql($insert=false, $tabla='familias_web', $campo_id = 'idfamilia_web', $campos_obligatorios=array('familia_web','familia_web_menu'), $valores, $where);
		} elseif($valores['accion']=='Nuevo') {
			$sql = new Generar_sql($insert=true, $tabla='familias_web', $campo_id = 'idfamilia_web', $campos_obligatorios=array('familia_web','familia_web_menu'), $valores);
		}
					
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
		
			//$sql_info = "SELECT familia_web_menu FROM familias_web WHERE idfamilia_web={$valores['idfamilia_web']} AND eliminado = 0;";
			//$info = new Mysql;
			echo '
				<div style="color: red; text-align: center">
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR LA CATEGORÍA DESTACADA "<u><b>'
					. strtoupper($valores['familia_web_menu'])
					.'"</b></u>!</h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="idfamilia_web" value="'. $valores['idfamilia_web'] .'">
						<input type="submit" name="subaccion" value="Si">
						<input type="submit" name="subaccion" value="No">
					</form>
				</div>
			';
			$info->__destruct;
			
		} elseif($valores['subaccion']=='Si') {
		
			$this->_eliminar($valores);
			echo '<center><h4>Operación realizada con éxito.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>'; 
			
		} else {
			
			echo '<center><h4>No se ha realizado ninguna acción.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
			
		}
		
	}
	
	private function _eliminar($valores) 
	{
		$this->_consulta_destacados = new Mysql;
		$this->_eliminar_productos($valores['idfamilia_web']);
		if($this->error === false) {
			$sql_familia="DELETE FROM familias_web WHERE idfamilia_web={$valores['idfamilia_web']};";
			$this->error = !$this->_consulta_destacados->resultado_consulta($sql_familia);
		}
		$this->_consulta_destacados->__destruct();
	}
	
	private function _eliminar_productos($idfamilia_web)
	{
		$sql_productos = "DELETE FROM productos_familias_web WHERE idfamilia_web = $idfamilia_web;";
		$this->error = !$this->_consulta_destacados->resultado_consulta($sql_productos);	
	}
	
	public function resultados() 
	{
		
		if($this->error === false) {
			echo '<center><h4>Operación realizada con éxito.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>'; 
			
		} else {
			
			echo '<center><h4>Hay un error. No se ha realizado ninguna acción.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
		}
		
	}

	
}
