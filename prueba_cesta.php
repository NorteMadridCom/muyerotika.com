<?php
session_start();

require_once 'sesion.php';
require_once 'includes/mysql.php';
require_once 'includes/class.cadenas.php';
require_once 'includes/mssql.php';
require_once 'includes/class.select.php';
require_once 'includes/class.comprobar_datos.php';
require_once 'includes/class.subir_archivo.php';
require_once 'includes/class.generar_sql.php';
require_once 'includes/class.paginador.php';
require_once 'includes/class.ordenar.php';
require_once 'includes/class.enviar_mail.php';
require_once 'cesta.php';

//$cesta = new Cesta();

//$cesta->add_producto(166,$uds=1,1,120);
//$cesta->add_producto(244,5,1,220);
//$cesta->add_producto(33,2,2,20);
//$cesta->add_producto(66,6,2,520);
//var_dump($cesta->detalles_cesta);

//$cesta->calcular_cesta();
var_dump($_SESSION['datos_cesta']);
echo '<br>';
var_dump($_SESSION['detalles_cesta']);
/*
$cesta->edit_producto(166,50);
$cesta->del_producto(33);
echo '<br>';
var_dump($_SESSION['datos_cesta']);
echo '<br>';
var_dump($_SESSION['detalles_cesta']);
*/


echo $cesta->total_cesta();

?>