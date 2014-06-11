<?php



		if($_POST['accion']=='poner_dto_pri') {
			
			$sql_poner_dto_pri = "INSERT INTO dtos_prioritarios SET idtipo_cliente={$_POST['idtipo_cliente']}, idproducto={$_POST['idproducto']}, dto_prioritario={$_POST['dto_prioritario']};";
			$consulta_productos->resultado_consulta($sql_poner_dto_pri);
			
			$listado_prod->buscar_producto($_POST['idproducto']);
			
		} elseif($_POST['accion']=='quitar_dto_pri') {
			
			$sql_quitar_dto_pri = "DELETE FROM dtos_prioritarios WHERE idtipo_cliente={$_POST['idtipo_cliente']} AND idproducto={$_POST['idproducto']};";
			$consulta_productos->resultado_consulta($sql_quitar_dto_pri);
			
			$listado_prod->buscar_producto($_POST['idproducto']);
			
			

//comienzo de relaxiones
			
		} elseif($_POST['accion']=='ordena_relacion') {
			
			echo '
			<div id="dialog-message-ordenar" title="Ordena los productos" >';
			
			$ordenar_producto = new Ordenar('productos_relacionados','id_relacionado','nombre_producto', null, 'orden', "idproducto_ppal = {$_POST['idproducto']}");
				if($_POST['ordenar']=='Ordenar') {
					$ordenar_producto->ordenacion(false);
					}
			echo '</div>';
			
			$listado_prod->buscar_producto($_POST['idproducto']);
			
	

//final de relaxciones			
			
			
			

		} elseif($_POST['accion']=='eliminar') {
			
			$listado_prod->eliminar_producto($_POST['idproducto']);

		} elseif($_POST['accion'] == 'editar') {
	
			$listado_prod->editar_producto((object) $_POST);
		/*	
		} elseif($_POST['img1'] || $_POST['img2'] || $_POST['img3']) {
			
			for($i=1;$i<4;$i++) {
				if($_POST['img'.$i]=='eliminar') $listado_prod->eliminar_imagen($_POST['idproducto'],$i);
			}
		*/
		//} elseif($_POST['accion'] == 'terminar') { //de cualquier form de producto
			
			//if($_POST['parte']=="info") {
				
				

/*
				//var_dump($_POST);
				if(!$_POST['novedad']) $_POST['novedad'] = 0;
				if(!$_POST['profesional']) $_POST['profesional'] = 0;
				 
				$campos_obligatorios = array(
					'producto_nombre_web',
					'idlinea',
					'idsubsubfamilia'
				);
				$error = array();
				foreach($campos_obligatorios as $campo) {
					$var = new Validar_datos('no vacio', $_POST[$campo]);
					if($var->error)  $error[$campo] = $var->error;
					$var->__destruct();
				}
				//var_dump($error);
				if(count($error) == 0) {

					$listado_prod->actualizar_producto_info(); 
					
				} else {	
					$producto = (object) $_POST;
					$listado_prod->editar_producto_info($producto,$error);
				} 
	*/
				//$listado_prod->actualizar_producto_info($_POST);
				
			//}
		
		} elseif($_POST['parte'] == 'producto_info') {
			
			if($_POST['accion']) $listado_prod->actualizar_producto_info($_POST);
			else {
				//var_dump($_POST);
				if(count($_POST)<=2) $prod=$listado_prod->buscar_producto($_POST['idproducto']);
				else $prod = (object) $_POST;
				$listado_prod->editar_producto_info($prod);
			}
			
		} elseif($_POST['parte'] == 'producto_relacionados') {
			
			$relaciones = new Editar_productos_relacionados($_POST['idproducto']);
			if($_POST['accion']=='eliminar_relacion') $relaciones->eliminar_relacion($_POST['id_relacionado']);//quito la relación
			elseif($_POST['accion']=='anadir_relacion') $relaciones->anadir_relacion($_POST['idprocuto_relacionado'],$_POST['nombre_producto']); //pongo relacion
			elseif($_POST['accion']=='buscar_relacion') $relaciones->buscar_relacion($_POST['ref'],$_POST['producto']); //muestro resultados
			elseif($_POST['accion']=='ordenar_relacion') $relaciones->ordenar_relaciones(); //pos eso
			$relaciones->poner_relaciones(); //cargo el form general
			
		} else {
			echo "Fallamos al refrescar el formulario de Info";
			//var_dump($_POST);
			$producto = (object) $_POST;
			//var_dump($producto);
			/*
			if($_POST['parte']=="info") $listado_prod->editar_producto_info($producto);
			elseif($_POST['parte']=="relacionados") $listado_prod->editar_producto_relacionados($producto);
			else $listado_prod->editar_producto($producto);
			*/
			$listado_prod->editar_producto($producto);
		}			
