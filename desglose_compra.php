<?php

require_once 'includes/mysql.php';

$cadena = "M798";

$compra = new Mysql();

$compra_sql = "SELECT round(total*100) as total FROM pedidos WHERE idpedido = {$_GET['order']};";
$compra->ejecutar_consulta($compra_sql);
$cadena .= "{$compra->registros[0]->total}\r\n";

$articulos_sql = "SELECT count(iddetalle_pedido) as no_articulos FROM detalles_pedidos WHERE idpedido = {$_GET['order']};";
$compra->ejecutar_consulta($articulos_sql);
$cadena .= "{$compra->registros[0]->no_articulos}\r\n";

$detalles_sql = "SELECT dp.cod_producto, dp.producto, dp.uds, round(dp.uds*dp.precio*(1+(dp.dto/100))*(1+(p.dto_volumen)/100)*1.21*100) as total FROM detalles_pedidos dp, pedidos p WHERE dp.idpedido = {$_GET['order']} AND p.idpedido=dp.idpedido;";
$compra->ejecutar_consulta($detalles_sql);
foreach ($compra->registros as $articulo) {
	$cadena .= "{$articulo->cod_producto}\r\n";
	$cadena .= "{$articulo->producto}\r\n";
	$cadena .= "{$articulo->uds}\r\n";
	$cadena .= "{$articulo->total}\r\n";
}

echo $cadena;

//no se aplica el recargo de gastos de envio

