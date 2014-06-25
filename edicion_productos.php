<?php

	$consulta_productos = New Mysql;	
	$listado_prod = new Editar_productos();

	if($_POST['idproducto'] && $_POST['accion']!='Ordenar') { //edición del producto
		
		echo "HAY QUE PONER A QUE PRODUCTO NOS REFERIMOS";
		require 'edicion_producto.php';
	
	} else {		
	
		//hay que poner las opciones de localización de productos
		if($_GET['subseccion']=='Nuevo') {
			
			if(!$_POST['accion']) $listado_prod->editar_producto((object) $_POST);
			else $listado_prod->actualizar_producto_info($_POST);
			
		} elseif($_GET['subseccion']=='Pendientes' || $_GET['subseccion']=='Eliminados') {
			
			if($_GET['subseccion']=='Eliminados') {
				$sql_listar_productos = "SELECT * FROM productos WHERE eliminado = 0 AND web = 0 ORDER BY ref LIMIT 0,6000;";
			} else {
				$sql_listar_productos = "SELECT * FROM productos WHERE eliminado = 0 And (web = 1 or web is null) AND idsubsubfamilia is NULL ORDER BY ref LIMIT 0,10000;";
			}
			$listado_prod->listar_productos_paginados($sql_listar_productos);
			
		} elseif($_GET['subseccion']=='Buscar') {
			
			//var_dump($_POST);			
			$buscar_prod = new Buscador_general();
			
			if($_POST['buscar']=='Buscar') {
				$buscar_prod->resultados();
				if(!$buscar_prod->error) {
					$_SESSION['sql_busqueda_admin']=$buscar_prod->sql;
					$listado_prod->listar_productos_paginados($_SESSION['sql_busqueda_admin']);
				} else {
					echo 'No se puede ejecutar la busqueda con los criterios seleccionados.';
				}
			} elseif($_SESSION['sql_busqueda_admin'] && $_GET['index']) {
				$listado_prod->listar_productos_paginados($_SESSION['sql_busqueda_admin']);
			} else {
				$buscar_prod->formulario_busqueda();
			}
			
			$buscar_prod->__destruct();

		} elseif($_GET['subseccion']=='Ordenar') {
			
			if($_POST['idsubsubfamilia'] && $_POST['accion']=='Ordenar') {
				
				$ordenar_producto = new Ordenar('productos','idproducto','producto', 'eliminado', 'orden', "idsubsubfamilia = {$_POST['idsubsubfamilia']} AND web=1");
				if($_POST['ordenar']=='Ordenar') {
					$ordenar_producto->ordenacion();
				}
				$ordenar_producto->__destruct();	
				
			} else {
			
				echo '
					<form method="post" enctype="multipart/form-data" action="">
				';
				$categoria_ordenar_producto = new Seleccion_familias();
				echo '		
						<input type="submit" name="accion" value="Ordenar" />
					</form>
				';
								
			}
			
		} else {
			
			$listado_prod->form_inicial_productos();
			
		}
		
	}
