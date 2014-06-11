<?php

if (!$_GET['seccion']) {
	
	$div="navegacion";
	$class="navegacion";
	
	$navegacion=new Barra($div,$class,'Inicio',false,'<img src="img/flecha.png" class="navegacion" height="24" />');
	echo $navegacion;
	
}

if(!$_GET['index']) $_GET['index'] = 0; //necesario para el paginador

if($_GET['seccion']!='buscar') unset($_SESSION['sql_busqueda']);
if($_GET['subseccion']!='Buscar') unset($_SESSION['sql_busqueda_admin']);

?>

<div id="contenido" style="float: left; width: 800px; "><?php require 'contenido.php'; ?></div>


<div id="menu_dcha" style="float: left; width: 200px; "><?php require 'menu_dcha.php'; ?></div>