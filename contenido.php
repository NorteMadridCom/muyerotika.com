<?php
echo '';

if($_GET['seccion']=='cesta') {
	
	echo '<div id="editar_productos">';
	$mostar_cesta = new Mostrar_cesta;
	$mostar_cesta->formulario();
	echo '</div>';
	
} elseif($_GET['seccion']=='pago') {
	
	//ir a la plataforma de pago y si es correcto guardar el momento de la confirmaciÃ³n del pago.
	//hay dos scripts mas, uno que realiza el cambio en base datos con aceptada o rechazada (pudede ser el mismo o podemos hacerlo aqui mismo)
	if($_SESSION['sesion_registrada']) {
		$pedido = new Pedido();
		if($_GET['respuesta']=='ok') {
			$pedido->aceptado();
		} elseif($_GET['respuesta']=='ko') {
			//hay que ver que no nos ducplique el pedido
			echo "Su pago ha sido rechazado. Puede volver a intentarlo con otra tarjeta o realizar el pago mediante transferencia.<br>";
			$pedido->pedido();
		} elseif($_GET['respuesta']=='Transferencia') {
			$pedido->aceptado();
		} else $pedido->pedido();
	} else {
		require 'formulario_eleccion_registro.php';
	}
	
	/*
	 * lo que se muestra a continuación es solo para modo local depueracion, 
	 * lo correcto es lo de arriba
	 * 
	*/
	
	/*
	$pedido = new Pedido();
	$pedido->pedido();
	$ped = new Mysql;
	$ped_sql="UPDATE pedidos SET pago='$fecha_pago' WHERE idpedido={$_SESSION['datos_cesta']['no_pedido']};";
	$ped->resultado_consulta($ped_sql);	
	$pedido->aceptado();
	*/


	
} elseif(
	$_GET['seccion']=='clientes' || 
	($_POST['accion']=='datos' && $_SESSION['idcliente']) || 	
	$_POST['accion']=='Nuevo Registro' || 	
	$_POST['accion']=='Registrarse' ||
	(($_POST['accion']=='Modificar' || $_POST['accion']=='Baja') && $_POST['idcliente'])
	) {
	
	$cliente = New Editar_clientes;
	echo '<div id="editar_clientes">';	
	
	if($_SESSION['tipo_cliente']=='administrador') {
		
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

	} elseif($_POST['accion']=='datos' && $_SESSION['idcliente']) {

		//editamos los datos de este cliente
		$cliente->buscar_cliente_id($_SESSION);	
		
	} else {

		
		if($_POST['accion']=='Registrarse') {
			
			$cliente->introducir_datos($_POST);

			if(!$cliente->error) {
				$mensaje_html = '
					<p>Para activar su cuenta de correo electrónico ha de hacer clic en el siguiente enlace:</p>
					<p align="center"><a href="http://www.dizma.es/desbloqueo.php?usuario='.$_POST['usuario'].'&desbloqueo='.$_POST['desbloqueo'].'">Activar mi cuenta en DIZMA</a></p>
					<p>Desde el equipo de Dizma, le agradecemos su confianza en nosotros.</p>
				';
				$mail_registro = new Html_mail($mensaje_html, 'E-mail de confirmación de cuenta en dizma.es', true, $_POST['usuario']);
				if($mail_registro->error) {
					echo $mail_registro->error;
					$mail_no_registro_admin = new Html_mail("No se ha podido enciar el correo de registro a: {$_POST['usuario']}", 'E-mail de error de registro de cuenta en dizma.es', false, false, $_POST['usuario']);
				} else {
					echo '<center><h4>Se le ha enviado un e-mail para que active su cuenta, compruebe su cuenta de correo.</h4></center>';
					$mail_registro_admin = new Html_mail("Se ha registrado un nuevo usuario: {$_POST['usuario']}", 'E-mail de registro de cuenta en dizma.es', false, false, $_POST['usuario']);
				}
			}
			 
		} elseif($_POST['accion']=='Modificar') {
			
			$cliente->introducir_datos($_POST);
			
		} elseif($_POST['accion']=='Baja') {

			if($_POST['confirmacion']=='1') {
				$cliente->eliminar_cliente($_SESSION['idcliente']);
				session_destroy();
			} else {
				echo '
					<form action="" method="post" enctype="multipart/form-data">
					<p align="center">¿Esta seguro de querer darse de baja en nuestro servicio?<br>	
						<input type="hidden" name="confirmacion" value="1" />
						<input type="hidden" name="idcliente" value="'.$_SESSION['idcliente'].'">
						<input type="submit" name="accion" value="Baja" class="boton" />
					</p>
					</form>
				';
			}

		} else {
			
			$cliente->formulario($_POST);
			
		}		
		
	}
	echo '</div>';
	
} elseif($_GET['seccion']=='buscar' && !$_GET['producto']) {
	
	//echo '<div id="editar_productos">';
	
	if($_POST['buscar_txt']) unset($_SESSION['sql_busqueda']);

	$buscar_prod = new Buscador_general();
			
	if(strlen($_POST['buscar_txt'])>2) {
		$buscar_prod->resultados();
		if(!$buscar_prod->error) {
			//unset($_GET['seccion']);
			$_SESSION['sql_busqueda'] = $buscar_prod->sql;
			Poner_productos::productos($buscar_prod->sql,$config);
		} else {
			echo '<div id="editar_productos">No se han encontrado resultados con los criterios seleccionados.</div>';
		}
	} elseif($_SESSION['sql_busqueda']) {
		 Poner_productos::productos($_SESSION['sql_busqueda'],$config);
	} else {
		echo '<div id="editar_productos">Búsqueda sin texto a buscar o con pocos caracteres.</div>';
	}
	
	$buscar_prod->__destruct();
	
} else {
	
	/******************************************************************
	/ hasta que no existan las ofertas vamos a poner de forma temp que
	/ aparezcan las familias. He de midificar la edicion de las familias
	/ para que tengan imagen
	*******************************************************************/
	
	if($_GET['producto']) {

		$producto=new Mysql;
		$sql_producto="
			SELECT 
				l.linea_menu,
				l.dto_linea,  
				f.fabricante_menu, 
				p.* 
			FROM 
				lineas l, 
				fabricantes f, 
				productos p 
			WHERE 
				l.idlinea = p.idlinea AND 
				l.idfabricante = f.idfabricante AND 
				p.producto='". $_GET['producto'] ."' AND 
				p.eliminado=0 AND
				p.web = 1 
				$where_prod_prof
			LIMIT 0,1;
		";
		$producto->ejecutar_consulta($sql_producto);
		
		$mostrar_producto=new Producto($config,$producto);
		$mostrar_producto->poner_producto();
		
	} elseif($_GET['subsubfamilia']) {
		
		$sql_productos="
			SELECT 
				l.linea_menu,
				l.dto_linea,  
				f.fabricante_menu, 
				p.* 
			FROM  
				lineas l, 
				fabricantes f, 
				subsubfamilias ss, 
				subfamilias sf, 
				familias ff, 
				productos p 
			WHERE 
				l.idlinea = p.idlinea AND 
				l.idfabricante = f.idfabricante AND 
				ss.subsubfamilia='{$_GET['subsubfamilia']}' AND 
				sf.subfamilia='{$_GET['subfamilia']}' AND 
				ff.familia='{$_GET['familia']}' AND  
				p.idsubsubfamilia=ss.idsubsubfamilia AND 
				ss.idsubfamilia=sf.idsubfamilia AND 
				sf.idfamilia=ff.idfamilia AND 
				p.eliminado=0 AND 
				p.web = 1 
				$where_prod_prof 
			ORDER BY
				p.orden, 
				p.producto 
			LIMIT 0,500;
		";
		
		Poner_productos::productos($sql_productos,$config);
		
	} elseif($_GET['subfamilia']) {
	
		$subsubfam=new Mysql;
		$sql_subsubfam = "
			SELECT 
				ss.* 
			FROM 
				familias ff, 
				subfamilias s, 
				subsubfamilias ss  
			WHERE 
				s.idsubfamilia=ss.idsubfamilia AND 
				s.subfamilia='{$_GET['subfamilia']}' AND 
				s.idfamilia=ff.idfamilia AND 
				ff.familia='{$_GET['familia']}' AND 
				ss.eliminado=0 
			ORDER BY
				ss.orden, 
				ss.subsubfamilia 
			LIMIT 0,100;
		";	
		$subsubfam->ejecutar_consulta($sql_subsubfam);
		
		if($subsubfam->registros[0]->subsubfamilia=='0') {

			$sql_productos="
				SELECT 
					l.linea_menu,
					l.dto_linea,  
					f.fabricante_menu, 
					p.* 
				FROM  
					lineas l, 
					fabricantes f, 
					productos p 
				WHERE 
					l.idlinea = p.idlinea AND 
					l.idfabricante = f.idfabricante AND 
					p.idsubsubfamilia='{$subsubfam->registros[0]->idsubsubfamilia}' AND 
					p.eliminado=0 AND
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
			
			Poner_productos::productos($sql_productos,$config);
			
		} elseif(!$subsubfam->registros[0]->subsubfamilia) {
			
			echo '<h1 align="center">Sección en Obras.Disculpen las molestias.</h1>';
			
		} else {
			
			Poner_productos::menus($subsubfam,$config);	
			
		}
		
	} elseif($_GET['familia']) {
		
		$subfam=new Mysql;
		$sql_subfamilia="
			SELECT 
				s.* 
			FROM 
				subfamilias s, 
				familias f 
			WHERE 
				f.idfamilia=s.idfamilia AND 
				f.familia='{$_GET['familia']}' AND 
				s.eliminado=0 
			ORDER BY
				s.orden, 
				s.subfamilia 
			LIMIT 0,100;
		";
		$subfam->ejecutar_consulta($sql_subfamilia);
		
		if($subfam->registros[0]->subfamilia=='0') {
	
			$sql_productos="
				SELECT 
					l.linea_menu,
					l.dto_linea,  
					f.fabricante_menu, 
					p.* 
				FROM  
					lineas l, 
					fabricantes f, 
					subsubfamilias ss, 
					productos p 
				WHERE 
					l.idlinea = p.idlinea AND 
					l.idfabricante = f.idfabricante AND  
					p.idsubsubfamilia=ss.idsubsubfamilia AND 
					ss.idsubfamilia ='{$subfam->registros[0]->idsubfamilia}' AND 
					p.eliminado=0 AND
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
			
			Poner_productos::productos($sql_productos,$config);
					
		} elseif(!$subfam->registros[0]->subfamilia) {
			
			echo '<h1 align="center">SecciÃ³n en Obras.Disculpen las molestias.</h1>';
			
		} else {
			
			Poner_productos::menus($subfam,$config);;	
			
		}
		
	} elseif($_GET['familia_web']) {
		
		if($_GET['familia_web']=='novedades') {
			$sql_productos="
				SELECT 
					l.linea_menu,
					l.dto_linea,  
					f.fabricante_menu, 
					p.* 
				FROM  
					lineas l, 
					fabricantes f, 
					productos p 
				WHERE 
					l.idlinea = p.idlinea AND 
					l.idfabricante = f.idfabricante AND 
					p.novedad = 1 AND 
					p.eliminado = 0 AND 
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
		} else {
			$sql_productos="
				SELECT 
					l.linea_menu,
					l.dto_linea,  
					f.fabricante_menu, 
					p.* 
				FROM  
					lineas l, 
					fabricantes f, 
					productos p, 
					productos_familias_web pf, 
					familias_web fw 
				WHERE 
					l.idlinea = p.idlinea AND 
					l.idfabricante = f.idfabricante AND 
					fw.familia_web = '{$_GET['familia_web']}' AND 
					pf.idfamilia_web = fw.idfamilia_web AND 
					pf.idproducto = p.idproducto AND 
					p.eliminado = 0 AND 
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
		}
		
		Poner_productos::productos($sql_productos,$config);
		
	} elseif($_GET['linea']) {

		$sql_productos="
			SELECT 
				ss.dto_linea, 
				p.* 
			FROM  
				lineas ss, 
				productos p 
			WHERE 
				ss.linea='{$_GET['linea']}' AND 
				p.idlinea=ss.idlinea AND 
				p.eliminado=0 AND 
				p.web = 1 
				$where_prod_prof 
			ORDER BY 
				p.producto 
			LIMIT 0,500;
		";
		
		Poner_productos::productos($sql_productos,$config);
		
	} elseif($_GET['fabricante']) {
		
		$sql_lineas = "
			SELECT 
				l.* 
			FROM 
				lineas l, 
				fabricantes f 
			WHERE 
				f.fabricante='{$_GET['fabricante']}' AND 
				l.idfabricante=f.idfabricante AND
				l.eliminado=0 
			ORDER BY 
				l.orden, 
				l.linea 
			LIMIT 
				0,100;
		";
		$lineas = new Mysql();
		$lineas->ejecutar_consulta($sql_lineas);
		
		if($lineas->registros[0]->linea=='0') {

			$sql_productos="
				SELECT 
					l.dto_linea,
					p.* 
				FROM 
					lineas l,  
					productos p 
				WHERE 
					p.idlinea = l.idlinea AND 
					l.idlinea ='{$lineas->registros[0]->idlinea}' AND 
					p.eliminado=0 AND
					p.web = 1 
					$where_prod_prof 
				ORDER BY 
					p.producto 
				LIMIT 0,500;
			";
			
			Poner_productos::productos($sql_productos,$config);
					
		} elseif(!$lineas->registros[0]->linea) {
			
			echo '<h1 align="center">Sección en Obras.Disculpen las molestias.</h1>';
			
		} else {
			
			Poner_productos::menus($lineas,$config);
			
		}

		
	} else {
		
		$portada=new Mysql;
		$sql_portada = "SELECT idfamilia_web FROM familias_web WHERE portada = 1 LIMIT 0,1;";
		$portada->ejecutar_consulta($sql_portada);
		if($portada->numero_registros > 0) {
			$sql_productos="
				SELECT 
					l.dto_linea, 
					p.* 
				FROM  
					lineas l, 
					productos p, 
					productos_familias_web w 
				WHERE 
					p.idlinea = l.idlinea AND 
					w.idfamilia_web = {$portada->registros[0]->idfamilia_web} AND 
					p.idproducto = w.idproducto AND 
					p.eliminado = 0 AND 
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
		} else {
			$sql_productos="
				SELECT 
					l.dto_linea, 
					p.* 
				FROM  
					lineas l, 
					productos p 
				WHERE 
					p.idlinea = l.idlinea AND 
					p.novedad = 1 AND 
					p.eliminado = 0 AND 
					p.web = 1 
					$where_prod_prof 
				ORDER BY
					p.orden, 
					p.producto 
				LIMIT 0,500;
			";
		}
		$portada->__destruct();
		
		Poner_productos::productos($sql_productos,$config);
			
	}
	
}