<?php

class Poner_productos
{

	public function productos($sql_productos,$config) 
	{
		$productos_actualizados = new Mysql;
		$productos_actualizados->ejecutar_consulta($sql_productos);
		echo '<div id="productos">';
		$productos_actualizados_pag = new Paginador($productos_actualizados,$config->conf[productos_pagina]);
		if(!$productos_actualizados_pag->error) {
			
			$menuproductos = new Productos($config,$productos_actualizados_pag->resultado[$_GET['index']],'cuadro_producto');
			$menuproductos->poner_submenu();

		}
		echo '<div id="paginador">';
		$productos_actualizados_pag->poner_indices();	
		echo '</div>';//paginador
		echo '</div>';//de productos
	}
	
	public function menus($menu,$config)
	{
		
		echo '<div id="productos">';
		$submenu = new Submenu($config,$menu,'cuadro_producto_categorias');
		$submenu->poner_submenu();	
		echo '</div>';	
		
	}

}