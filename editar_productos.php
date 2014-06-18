<?php

class Editar_productos
{
	/*
	 * requiere el paginador.php
	 */
	private $_config;
	
	public function __construct($config) 
	{
		$this->_config=$config->conf;
	}
	
	public function listar_productos_paginados($sql_listar_productos)
	{
		//require 'includes/class.paginador.php';
		$sql_listar_productos;
		$consulta_productos = New Mysql;
		$consulta_productos->ejecutar_consulta($sql_listar_productos);
		if($consulta_productos->numero_registros>0) {
			$consulta_productos_pag = new Paginador($consulta_productos,25);
			//var_dump($consulta_productos_pag);
			if(!$consulta_productos_pag->error) {
				//var_dump($consulta_productos_pag->resultado[$_GET['index']]->registros);
				$this->listar_productos($consulta_productos_pag->resultado[$_GET['index']]->registros);
			}
			$consulta_productos_pag->poner_indices();
		} else echo 'No hay resultados';
	}
	
	public function buscar_producto($idproducto=false,$ref=false)
	{
		if($idproducto) $where = "p.idproducto = $idproducto";
		elseif($ref) $where = "p.ref = '$ref'";
		else die ("No hay referncia de producto a buscar");
		$prod = new Mysql;
		$sql_producto = "
			SELECT 
				p.*,
				f.idfamilia, 
				s.idsubfamilia, 
				ss.idsubsubfamilia, 
				fa.idfabricante,
				l.dto_linea     
			FROM 
				productos p 
				LEFT JOIN lineas l USING (idlinea) 
				LEFT JOIN fabricantes fa USING (idfabricante)
				LEFT JOIN subsubfamilias ss USING (idsubsubfamilia) 
				LEFT JOIN subfamilias s USING (idsubfamilia) 
				LEFT JOIN familias f USING (idfamilia) 
			WHERE
				$where 
		;";		
		$prod->ejecutar_consulta($sql_producto);
		//necesario porque el objeto seleccion familias se alimenta de POST

		$_POST['idsubsubfamilia'] = $prod->registros[0]->idsubsubfamilia;
		$_POST['idsubfamilia'] = $prod->registros[0]->idsubfamilia;
		$_POST['idfamilia'] = $prod->registros[0]->idfamilia;
		$_POST['idsubfamilia_ant'] = $prod->registros[0]->idsubfamilia;
		$_POST['idfamilia_ant'] = $prod->registros[0]->idfamilia;
		
		$_POST['idfabricante_ant'] = $prod->registros[0]->idfabricante;
		$_POST['idfabricante'] = $prod->registros[0]->idfabricante;
		$_POST['idlinea'] = $prod->registros[0]->idlinea;
		
		//$this->editar_producto($prod->registros[0]);
		$producto=$prod->registros[0];
		$prod->__destruct();
		
		return $producto;
		
	}
	
	public function listar_productos($registros) 
	{
		//$sql="SELECT * FROM productos WHERE eliminado = 0 AND web = NULL;";
		//depende de lo que metamos en la sql, podemos sacar mas o menos datos
		//si no marca las opciones segiran saliendo, es decir, un si/no y si lo edita automaticamente pasa a ser un si
		//deberiamos establecer un filtro, pero en un principio lo dejo asi
		//el filtro es ver productos que esten entre fechas, sin marca, sin familia, sin web, eliminados... ect.
		//nos traemos el listado
		//falta poner la tabla, es decir, las cabeceras
		
		//var_dump($registros);
		
		echo '<table>
					<tr>
						<th>Id
						<th>Nombre del Producto
						<th>Fabricante
						<th>Categoria
						<th>Estado
						<th>Acción';
		
		foreach ($registros as $producto) {
			if($producto->subsubfamilia) {
				$categoria = $producto->idsubsubfamilia;
			} elseif($producto->subfamilia) {
				$categoria = $producto->idsubfamilia;
			} elseif($producto->familia) {
				$categoria = $producto->idfamilia;
			} else {
				$categoria = "";
			}
			if($producto->linea) {
				$marca = $producto->idlinea;
			} elseif($producto->fabricante) {
				$marca = $producto->fabricante;
			} else {
				$marca = "";
			}
			$estado = 'Activo';
			if($producto->web == '0') $estado='Eliminado';
			else if($producto->idsubsubfamilia==NULL) $estado='Pendiente'; 
			
			echo '
				<tr>
				<form action="" method="post" enctype="multipart/form-data">
					<input type="hidden" name="idproducto" value="'.$producto->idproducto.'" />
					<input type="hidden" name="producto_nombre" value="'.$producto->producto_nombre.'" />'
					.'<td>'.$producto->ref
					.'<td>'.$producto->producto_nombre
					.'<td>'.$marca
					.'<td>'.$categoria	
					.'<td>'.$estado
					.'<td><button name="accion" value="eliminar" class="admin"><img src="img/eliminar.png" height="14" /></button>
					<button name="accion" value="editar" class="admin"><img src="img/editar.png" height="14" /></button>
				</form>
			';//editar ha de poner web = 1 y eliminar ha de poner web = 0, si es el caso
		}
		echo '</table>';
	}
	
	public function editar_producto($producto=false) 
	{		
		if($producto->idproducto) require 'form_productos_edicion.php';
		else $this->editar_producto_info($producto);
	}
	
	public function editar_producto_info($producto=false,$marcar_error = array()) 
	{

		if($marcar_error) {
			$this->_resultado("La referencia ya existe.");
		}
		
		unset($checked_novedad);
		if($producto->novedad == '1') $checked_novedad = 'checked';	
		
		unset($checked_prof);
		if($producto->profesional == '1') $checked_prof = 'checked';	
		
		$estado = 'Activo';
		if($producto->web == '0') $estado='Eliminado';
		else if($producto->idsubsubfamilia==NULL) $estado='Pendiente'; 
		

		$botonera = "botonera_nuevo.html";
		if($producto->idproducto) $botonera = "botonera_edicion.html";
		
		if(!$producto->idiva) $producto->idiva=3;
		$iva = new Combo('idiva', 'ivas', 'idiva', 'iva', $producto->idiva, false, false, false, false, false, true, true, null);
		
		require 'form_producto_info.php';
		
	} 
	
	public function editar_producto_relacionados($producto=false)
	{
		$relacion=new  Editar_productos_relacionados($producto);
	}
	
	public function form_inicial_productos()
	{
		require 'form_productos_inicial.php';
	}
	
	public function eliminar_producto($idproducto)
	{		
		if(!$_POST['subaccion']) {
				echo '
					<div style="color: red;">
						<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR "<u><b>'
						.$_POST['producto_nombre']
						.'"</b></u></h3>
						<form method="post" enctype="multipart/form-data" action="">
							<input type="hidden" name="accion" value="'. $_POST['accion'] .'">
							<input type="hidden" name="idproducto" value="'. $_POST['idproducto'] .'">
							<input type="submit" name="subaccion" value="Si"  class="admin">
							<input type="submit" name="subaccion" value="No"  class="admin">
						</form>
					</div>
				';
		} elseif($_POST['subaccion']!='Si') {
			echo '<p>No se ha realizado ningún cambio.</p><a href="'.$_SERVER['REQUEST_URI'].'"  class="admin">Volver</a>';
		} else {
			$sql_eliminar_producto = "UPDATE productos SET web = 0 WHERE idproducto = {$_POST['idproducto']};";
			$consulta_productos->resultado_consulta($sql_eliminar_producto);
			echo '<p>Producto eliminado</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
		}
		
	}
	/*
	public function eliminar_imagen($idproducto,$img)
	{
		$im = 'imagen'.$img;
		$nom_imagen="SELECT $im as nombre_imagen FROM productos WHERE idproducto = idproducto;";
		$consulta_productos->ejecutar_consulta($nom_imagen);
		echo $nombre_imagen = $consulta_productos->registros[0]->nombre_imagen;
		$sql_eliminar_imagen = "UPDATE productos set $im = null WHERE idproducto = idproducto;";
		if($consulta_productos->resultado_consulta($sql_eliminar_imagen)) {
			@unlink('./catalogo/miniaturas/'.$nombre_imagen);
			@unlink('./catalogo/productos/'.$nombre_imagen);
			echo '<p>Producto modificado</p><a  class="admin" href="'.$_SERVER['REQUEST_URI'].'">Volver</a>';
		}
	}
	
	private function _subir_imagenes()
	{
		if($_FILES) {
			foreach($_FILES as $nombre => $variable) {
				if($_FILES[$nombre]['name']) {
					$nombre_archivo = $_POST['idproducto'].'_'.$nombre;
					$subir_imagen = new Subir_imagen(
						$_FILES[$nombre], $config->conf['imagenes_prod_grande'], $nombre_archivo, $config->conf['alto_img_prod_grande'], $config->conf['ancho_img_prod_grande']	, true, 'png'
					);
					$subir_thumb = new Subir_imagen(
						$_FILES[$nombre], $config->conf['imagenes_prod'], $nombre_archivo, $config->conf['alto_img_prod'], $config->conf['ancho_img_prod'], true, 'png'
					);
					$_POST[$nombre] = $_POST['idproducto'].'_'.$nombre.'.png';
				}
			} 
		}
		
		if($subir_imagen->error) echo $subir_imagen->error_txt;
		
		if($subir_thumb->error) echo $subir_thumb->error_txt;
		
		if(!($subir_imagen->error || $subir_thumb->error)) return true;
		else return false;
	
	}
	*/
	public function actualizar_producto_info($producto=array()) 
	{

		$error=array();
		if(!is_array($producto)) exit('Error en los datos de entrada');
		
		$set = array();
		
		$accion="UPDATE ";
		$where="WHERE idproducto='{$producto['idproducto']}'";
		if(!$producto['idproducto']) {
			$accion="INSERT INTO ";
			$where="";
			$set[]="creado = NOW()";
		}
		
		
		
		$producto['producto']=$this->_nombre_producto($producto['producto_nombre']);
		if(!$producto['producto_nombre_web']) $producto['producto_nombre_web']=$producto['producto_nombre'];
		
		$error['ref']=false;
		if(!$producto['ref']) $producto['ref']=$this->_ref_auto();
		else {
			if($this->_comprobar_referencia($producto['ref'],$producto['idproducto'])) $error['ref']=true;
		}
		
		$campos_check = array (
			'profesional',
			'novedad'
		);
		foreach($campos_check as $campo_check) {
			if(!$producto[$campo_check]) $producto[$campo_check]=0;
		}
		
		$campos_obligatorios = array (
			'idsubsubfamilia',
			'idlinea',
			'producto',
			'producto_nombre',
			'producto_nombre_web',
			'ref',
			'precio',
			'idiva',
		);
		foreach($campos_obligatorios as $campo_oblig) {
			if(!$producto[$campo_oblig]) $error[$campo_oblig]=true;
			$set[] = $campo_oblig . " = '" . $producto[$campo_oblig] . "' ";
		}
		
		if(in_array(true,$error)) {
			$this->editar_producto_info((object) $producto, $error);
		} else { 
		
			$campos_opcionales = array(
				'uds', 'uds_min','dto_producto',
				'profesional',
				'novedad',
				'descripcion', 
				'video'
			);
			foreach ($campos_opcionales as $campo) {
				if(array_key_exists($campo, $producto)) {
					$set[] = $campo . " = '" . $producto[$campo] . "' ";
				}
			}
			$set_cadena = implode(',', $set);
			$sql_editar_producto = "
				$accion  
					productos 
				SET 
					web = 1, 
					$set_cadena  
				$where
			;";
			
			$actualizar_producto = new Mysql;
			$actualizar_producto->resultado_consulta($sql_editar_producto);
			//$this->_resultado($actualizar_producto->error);
			if($actualizar_producto->error) $this->_resultado("No se ha podido actualizar el producto",false,true);
			else $this->_resultado(false,true,true);
			$actualizar_producto->__destruct();
			
		}
		
	}
	
	private function _ref_auto()
	{
		$id=new Mysql;
		$sql="SELECT max(idproducto)+1 as id FROM productos;";
		$id->ejecutar_consulta($sql);
		$aleatorio='';
		$ref="MAN-{$id->registros[0]->id}";
		while ($this->_comprobar_referencia($ref)!==false) {
			$aleatorio="-".rand();
			$ref="M-{$id->registros[0]->id}$aleatorio";
		}
		return $ref;
	}
	
	private function _comprobar_referencia($ref,$idproducto=false)
	{
		$comprobar = new Mysql;
		$sql="SELECT idproducto FROM productos WHERE ref='$ref';";
		$comprobar->ejecutar_consulta($sql);
		if($comprobar->numero_registros>0) {
			if($comprobar->registros[0]->idproducto != $idproducto) {
				return $comprobar->registros[0]->idproducto;
			}
		}
		return false;
	}
	
	private function _nombre_producto($nombre)
	{
		$nombre_web = new Nombre_web($nombre);
		return $nombre_web->tratar_nombre();
	}
	
	/*
	private function _resultado($error=true)
	{
		if(!$error) echo '<p>Producto modificado</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
		else echo '<p>No se ha modicado el producto</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
	}
	*/
	protected function _resultado($error_txt=false,$continuar=false,$volver=false,$form=false)
	{
		$mensaje="Operación exitosa";
		if($error_txt) $mensaje=$error_txt;
		if(!$form) $form = $this->_botones_emergente($continuar,$volver);
		require 'emergente.php';
	}
	
	protected function _inputs($parte=false)
	{
		$cadena=array();
		if(!$_POST['idproducto']) {
			$producto=$this->buscar_producto(false,$_POST['ref']);
			$cadena['idproducto']=$producto->idproducto;
		} 
		if($parte) $cadena['parte']=$_POST['parte'];
		$inputs="";
		foreach($cadena as $var=>$val) {
			$inputs .= '<input type="hidden" name="'.$var.'" value="'.$val.'" />';
		}
		return $inputs;
	}
	
	protected function _botones_emergente($continuar=false,$volver=false)
	{
		$form="";
		if($continuar) $form .= '
		<form action="" method="POST" enctype="multipart/from-data">
			'.$this->_inputs(true).'
			<button class="contacto" >Continuar</button>
		</form>
		';
		if($volver) $form .= '
		<form action="" method="POST" enctype="multipart/from-data">
			'.$this->_inputs().'
			<button class="contacto" >Volver</button>
		</form>
		';
		if(!$continuar && !$volver) $form .= '<button class="contacto" onclick="ocultar(\'emerge\')">Aceptar</button>';
		return $form;
	}
	
}



class Editar_productos_relacionados extends Editar_productos
{
	
	private $_idproducto;
	
	public function __construct($idproducto)
	{
		$this->_idproducto=$idproducto;
		//$this->_poner_relaciones();
	}
	
	public function poner_relaciones()
	{
		require 'form_producto_relacionados_inicio.php';
		$sql="SELECT * FROM productos_relacionados WHERE idproducto_ppal={$this->_idproducto} ORDER BY orden;";
		$relaciones = new Mysql();
		$relaciones->ejecutar_consulta($sql);
		if($relaciones->registros) {
			foreach($relaciones->registros as $relacion) {
				$producto_relacionado = $this->buscar_producto($relacion->idproducto_relacionado);
				require 'form_producto_relacionados_listado.php';
			} 
		}
		require 'form_producto_relacionados_fin.php';
	}
	
	public function buscar_relacion($ref_rel=false,$producto_nombre_rel=false) 
	{
		
		//hay que mejorar la busqueda y ponerla independiente, trayendo el idrelacionado
		if($ref_rel) {
			$sql_relacionados = "select * 
			from productos 
			where ref = '$ref_rel'
			AND idproducto != {$this->_idproducto} 
			and web=1 
			and idproducto not in 
			 (
			 select idproducto_relacionado 
			 from productos_relacionados 
			 where idproducto_ppal = '{$this->_idproducto}'
			 ) ;";	
		} else {
			$sql_relacionados = "select * 
			from productos 
			where producto like '%{$producto_nombre_rel}%' 
			and web=1 
			AND idproducto != {$this->_idproducto} 
			 and idproducto not in 
			 (
			 select idproducto_relacionado 
			 from productos_relacionados 
			 where idproducto_ppal = '{$this->_idproducto}'
			 ) ;";
			//limit 0,10;";
		}
		$relacionados = new Mysql();
		$relacionados->ejecutar_consulta($sql_relacionados);
		if(!$relacionados->numero_registros) $this->_resultado("No existen artículos");
		elseif($relacionados->numero_registros>20) $this->_resultado("Demasiados resultados");
		else {
			$form="";
			foreach($relacionados->registros as $producto) {
				$form .= '
					<form method="post" enctype="multipart/form-data" action="">
						<input name="idproducto" value="'.$this->_idproducto.'" type="hidden">
						<input name="idprocuto_relacionado" value="'.$producto->idproducto.'" type="hidden">
						<input name="nombre_producto" value="'.$producto->producto_nombre.'" type="hidden">
						<input name="parte" value="'.$_POST['parte'].'" type="hidden">
						'.$producto->producto_nombre.'
						<button name="accion" value="anadir_relacion" class="admin"><img src="img/nuevo.png" ></button>
					</form>
				';
			}
			$this->_resultado("Lista de productos", false, false,$form);
		}
	}
	
	public function anadir_relacion($idproducto_relacionado, $nombre_producto)
	{
		echo $sql="INSERT INTO productos_relacionados SET idproducto_ppal={$this->_idproducto}, idproducto_relacionado=$idproducto_relacionado, nombre_producto='$nombre_producto';";
		$eliminar_relacion = new Mysql;
		if($eliminar_relacion->resultado_consulta($sql)===false) $this->_resultado("No se ha podido añadir la relación");
		//else $this->_resultado();
	}
	
	public function eliminar_relacion($id_relacionado)
	{
		echo $sql="DELETE FROM productos_relacionados WHERE id_relacionado=$id_relacionado;";
		$eliminar_relacion = new Mysql;
		if($eliminar_relacion->resultado_consulta($sql)===false) $this->_resultado("No se ha podido quitar la relación");
		//else $this->_resultado();
	}
	
	public function ordenar_relaciones()
	{
		
		$ordenar_producto = new Ordenar('productos_relacionados','id_relacionado','nombre_producto', null, 'orden', "idproducto_ppal = {$this->_idproducto}");
		if($_POST['ordenar']=='Ordenar') {
				$ordenar_producto->ordenacion();
		}
		
	}
	
	
}



class Editar_descuentos_prioritarios extends Editar_productos
{
	
	private $_idproducto;
	private $_consultas_dto_pri = object;
	
	public function __construct($idproducto)
	{
		$this->_idproducto=$idproducto;
		$this->_consultas_dto_pri = new Mysql;
	}
	
	public function poner_descuentos()
	{
		require 'form_productos_dtos_prioritarios_inicio.php'; //a formatear por bea
		$this->_listar_dtos_prioritarios();
		require 'form_productos_dtos_prioritarios_nuevo.php'; //a formatear por bea
		require 'form_productos_dtos_prioritarios_fin.php'; //a formatear por bea
	}
	
	private function _listar_dtos_prioritarios()
	{
		$sql_listar="SELECT d.*, t.tipo_cliente FROM dtos_prioritarios d, tipos_clientes t WHERE d.idproducto={$this->_idproducto} AND d.idtipo_cliente=t.idtipo_cliente;";
		$this->_consultas_dto_pri->ejecutar_consulta($sql_listar);
		if(is_array($this->_consultas_dto_pri->registros)) foreach ($this->_consultas_dto_pri->registros as $dto_pri) {	 
			require 'form_productos_dtos_prioritarios_lista.php'; //a formatear por bea
		}
	}
	
	private function _comprobar_duplicado($idtipo_cliente)
	{
		$sql_duplicado="SELECT dto_prioritario FROM dtos_prioritarios WHERE idproducto={$this->_idproducto} AND idtipo_cliente=$idtipo_cliente;";
		$this->_consultas_dto_pri->ejecutar_consulta($sql_duplicado);
		if($this->_consultas_dto_pri->numero_registros > 0) return true;
		return false;
	} //comprobar que no se puedan colar dos dtos prioritarios al mismo grupo
	
	public function anadir_dto_prioritario($datos) //$datos=$_POST
	{
		if($this->_comprobar_duplicado($datos['idtipo_cliente'])!==true) {
			$sql_anadir="INSERT INTO dtos_prioritarios SET idproducto={$datos['idproducto']}, idtipo_cliente={$datos['idtipo_cliente']}, dto_prioritario='{$datos['dto_prioritario']}';";
			if($this->_consultas_dto_pri->resultado_consulta($sql_anadir)) $this->_resultado();
			else $this->_resultado("No se ha podido poner el descuento");
		} else $this->_resultado("Ya existe un descuento prioritairo asignado a este tipo de cliente");
	}
	
	public function editar_dto_prioritario($datos) //$datos=$_POST
	{
		$sql_editar="UPDATE dtos_prioritarios SET dto_prioritario='{$datos['dto_prioritario']}' WHERE idproducto={$this->_idproducto} AND idtipo_cliente={$datos['idtipo_cliente']};";
		if($this->_consultas_dto_pri->resultado_consulta($sql_editar)) $this->_resultado();
		else $this->_resultado("No se ha actualizado el descuento");
	}
	
	public function eliminar_dto_prioritario($datos) //$_POST
	{
		$sql_eliminar="DELETE FROM dtos_prioritarios WHERE idproducto={$this->_idproducto} AND idtipo_cliente={$datos['idtipo_cliente']};";
		if($this->_consultas_dto_pri->resultado_consulta($sql_eliminar)) $this->_resultado();
		else $this->_resultado("No se ha eliminado el descuento");
	}
	
	
}
