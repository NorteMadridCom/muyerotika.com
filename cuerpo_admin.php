<div id="editor">

<?php

if(!$_GET['index']) $_GET['index'] = 0; //necesario para el paginador

if($_GET['seccion']!='buscar') unset($_SESSION['sql_busqueda']);
if($_GET['subseccion']!='Buscar') unset($_SESSION['sql_busqueda_admin']);

if($_GET['seccion']=='pedidos') { 
	
	//en esta seccion harÃ­a falta la posibilidad de con sulta de pedidos por cada uno de los clientes por ellos mismos
	//para tener su histÃ³rico
	
	$editar_pedidos = new Editar_pedidos();
	$funcion=strtolower( $_POST['accion']);
	if($_POST['accion']=='email' && $_POST['idpedido']) {
		$editar_pedidos->email($_POST['idpedido'],$_POST['mes']);
	} elseif($_POST['accion']) {		
		if(method_exists($editar_pedidos, $funcion)) $editar_pedidos->$funcion($_POST['idpedido']);
		else $editar_pedidos->formulario_pedidos();
	} else $editar_pedidos->formulario_pedidos();

	
} elseif($_GET['seccion']=='editar_fabricantes') { 

	$editar_fabricantes = new Editar_fabricantes();
	
	if($_POST['accion']=='Nuevo' || $_POST['accion']=='Editar') {
		
		$editar_fabricantes->editar_fabricante((object) $_POST);
		
	} elseif($_POST['accion']=='Eliminar') {
		
		$editar_fabricantes->eliminar_fabricante((object) $_POST);
		
	} elseif($_POST['accion']=='Ordenar_fabricante') {
		
		echo '<p>ORDENAR MARCAS</p>';		
		$ordenar_marcas = new Ordenar('fabricantes','idfabricante','fabricante_menu');
		if($_POST['ordenar']=='Ordenar') {
			$ordenar_marcas->ordenacion();
		}
		$ordenar_marcas->__destruct();
			
	} elseif($_POST['accion']=='Ordenar_linea') {	

		echo '<p>ORDENAR LÍNEAS</p>';
		$ordenar_linea = new Ordenar('lineas','idlinea','linea_menu', 'eliminado', 'orden', "idfabricante = {$_POST['idfabricante']} AND linea != '0'");
		if($_POST['ordenar']=='Ordenar') {
			$ordenar_linea->ordenacion();
		}
		$ordenar_linea->__destruct();	
	
	} else {
			
		$editar_fabricantes->formulario_general();
	}
	
} elseif($_GET['seccion']=='editar_destacados') { 

	
	$categorias = new Editar_destacados($config);
	
	if($_POST['accion']=='Eliminar') {
		
		$categorias->eliminar($_POST);
		
	} elseif($_POST['accion']=='Productos') {
		
		$categorias->agregar_productos($_POST);
		
	} elseif($_POST['accion']=='Portada') {
		
		$categorias->portada($_POST);
		
	} elseif($_POST['accion']=='Ordenar') {
		
		$orden = new Ordenar($tabla='familias_web',$id="idfamilia_web",$mostrar="familia_web_menu");
		if($_POST['ordenar']=='Ordenar') {
			$orden->ordenacion();
		}
		$orden->__destruct;
		
	} elseif($_POST['accion']) {

		$categorias->editar($_POST);

	} else {
		
		$categorias->formulario_general();
	}
	
} elseif($_GET['seccion']=='editar_categorias') { 
	//desde el menu principal, ha de aparece el boton de editar y añadir
	/****************************************************
	* recordemos que un 0 en sub-familia => 0 en subsub
	* no pondresmos un 0 sino sin cateforias
	*****************************************************/ 
	
	$categorias = new Editar_categorias($config);
	
	if($_POST['accion']=='Eliminar') {
		
		$categorias->eliminar($_POST);
		
	} elseif($_POST['accion']=='Editar') {
		//estamos en editar
		//mucho ojo con la opcion sin subs, que han de borrar si existen las subs
		$categorias->editar($_POST);
		
	} elseif($_POST['accion'] == 'Nuevo') {
		
		$categorias->editar($_POST);
		
	} elseif($_POST['accion'] == 'Ordenar_familia') {
		
		echo '<p>ORDENAR FAMILIAS</p>';		
		$ordenar_familia = new Ordenar('familias','idfamilia','familia_menu');
		if($_POST['ordenar']=='Ordenar') {
			$ordenar_familia->ordenacion();
		}
		$ordenar_familia->__destruct();	
	
	} elseif($_POST['accion'] == 'Ordenar_subfamilia') {
		
		echo '<p>ORDENAR SUBFAMILIA</p>';
		$ordenar_subfamilia = new Ordenar('subfamilias','idsubfamilia','subfamilia_menu', 'eliminado', 'orden', "idfamilia = {$_POST['idfamilia']} AND subfamilia != '0'");
		if($_POST['ordenar']=='Ordenar') {
			$ordenar_subfamilia->ordenacion();
		}
		$ordenar_subfamilia->__destruct();	
		
	} elseif($_POST['accion'] == 'Ordenar_subsubfamilia') {
		
		echo '<p>ORDENAR SUBSUBFAMILIA</p>';
		$ordenar_subsubfamilia = new Ordenar('subsubfamilias','idsubsubfamilia','subsubfamilia_menu', 'eliminado', 'orden', "idsubfamilia = {$_POST['idsubfamilia']} AND subsubfamilia != '0'");
		if($_POST['ordenar']=='Ordenar') {
			$ordenar_subsubfamilia->ordenacion();
		}
		$ordenar_subsubfamilia->__destruct();		
		
	} else {
		
		$categorias->formulario_general();
		
	}
	
} elseif($_GET['seccion']=='editar_productos') { 
	
	require 'edicion_productos.php';

} elseif($_GET['seccion']=='clientes') {
	
	$cliente = New Editar_clientes;	
	
	if($_POST['accion']=='Modificar' || $_POST['accion']=='Registrarse') {
		
		$cliente->introducir_datos($_POST);
		
	} elseif($_POST['accion']=='Eliminar') {
		
		$cliente->eliminar_cliente($_POST['idcliente']);

	} elseif($_GET['subseccion']=='Nuevo') {
		
		$cliente->formulario($_POST);
		
	} elseif($_GET['subseccion']=='Buscar') {
		
		if($_POST['accion']=='Buscar') $cliente->buscar($_POST);
		elseif($_POST['accion']=='editar') $cliente->buscar_cliente_id($_POST);
		elseif($_POST['accion']=='eliminar') $cliente->buscar_cliente_id($_POST,true);
		else $cliente->formulario_busqueda();
		
	} elseif($_GET['subseccion']=='Pendientes') {
		
		if($_POST['accion']=='editar') $cliente->buscar_cliente_id($_POST);
		elseif($_POST['accion']=='eliminar') $cliente->buscar_cliente_id($_POST,true);
		else $cliente->pendientes();

	} else { 		

		$cliente->formulario_administrador();

	}
	
} elseif($_GET['seccion']=='grupos' && $_SESSION['tipo_cliente']=='administrador') { 

	echo '<div id="editar_productos">';
	
	$grupos = new Editar_tipos_clientes($config);
	
	if($_POST['accion']=='Eliminar') {
		
		$grupos->eliminar($_POST);
		
	} elseif($_POST['accion']) {

		$grupos->editar($_POST);

	} else {
		
		$grupos->formulario_general();
	}
	
	echo '</div>';
	
} elseif($_GET['seccion']=='tarifas' && $_SESSION['tipo_cliente']=='administrador') { 

	echo '<div id="editar_productos">';
	
	$tarifas = new Editar_tarifas();
	
	if($_POST['accion']=='Eliminar') {
		
		$tarifas->eliminar($_POST);
	
	} elseif($_POST['accion']=='general') {
		
		$tarifas->editar_tarifa_general($_POST['idtarifa']);
		
	} elseif($_POST['accion']) {

		$tarifas->editar($_POST);

	} else {
		
		$tarifas->formulario_general();
	}
	
	echo '</div>';
	
} elseif($_GET['seccion']=='descuentos' && $_SESSION['tipo_cliente']=='administrador') { 

	echo '<div id="editar_productos">';
	
	$descuentos = new Editar_descuentos($config);
	
	if($_POST['accion']=='Eliminar') {
		
		$descuentos->eliminar($_POST);
		
	} elseif($_POST['accion']) {

		$descuentos->editar($_POST);

	} else {
		
		$descuentos->formulario_general();
	}
	
	echo '</div>';
	
} elseif($_GET['seccion']=='gastos_envio' && $_SESSION['tipo_cliente']=='administrador') { 
	
	echo '<div id="editar_productos">';
	
	$envios = new Editar_envios();
	
	if($_POST['accion']=='Eliminar') {
		
		$envios->eliminar($_POST);
		
	} elseif($_POST['accion']) {

		$envios->editar($_POST);

	} else {
		
		$envios->formulario_general();
	}
	
	echo '</div>';
	
} else {
	//a la espera de acontecimeintos
}

?>

</div>
