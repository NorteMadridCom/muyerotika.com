<?php
include_once 'includes/mysql.php';
include_once 'pedido.php';
Pedido::pago();

/*
$cadena = "";
foreach($_POST as $var => $val) {
 $cadena .= "$var = $val \r\n";	
}

//file_put_contents('./datos.txt', implode('-', $_POST));
file_put_contents('./datos.txt', $cadena);
*/
?>