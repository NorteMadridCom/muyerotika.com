<?php

require_once 'includes/mysql.php';
require_once 'includes/class.cadenas.php';
require_once 'includes/mssql.php';
require_once 'includes/class.select.php';
require_once 'includes/class.comprobar_datos.php';
require_once 'includes/class.subir_archivo.php';
require_once 'includes/class.generar_sql.php';
require_once 'includes/class.paginador.php';
require_once 'includes/class.ordenar.php';


$envio=array();
$i=0;
$conx = new Conexion_mssql;
$actualizar = new Mysql;
if($conx->puerto) {
	$cons = new Mssql;	
	foreach($_POST['idproducto_diakros'] as $id) {
		$cons->ejecutar_consulta("SELECT Precio, Stock FROM Articulos WHERE IDArticulo = {$id};");
		//echo "UPDATE productos SET precio='{$cons->registros[0]->Precio}', uds='{$cons->registros[0]->Stock}' WHERE idproducto_diakros='{$id}';";
		$actualizar->ejecutar_consulta("SELECT sum(d.uds) as uds FROM detalles_pedidos d, pedidos p WHERE p.pago is not NULL AND p.confirmado is NULL AND eliminado=0 AND d.idpedido=p.idpedido AND d.cod_producto={$id};");
		$uds_real = $cons->registros[0]->Stock - $actualizar->registros[0]->uds;
		//$actualizar->resultado_consulta("UPDATE productos SET precio='{$cons->registros[0]->Precio}', uds='{$cons->registros[0]->Stock}' WHERE idproducto_diakros='{$id}';");
		$actualizar->resultado_consulta("UPDATE productos SET precio='{$cons->registros[0]->Precio}', uds='$uds_real' WHERE idproducto_diakros='{$id}';");
		//$envio[]=$cons->registros[0]->Stock;
		$envio[]=$uds_real;
		$i++;
	}	
} else {
	foreach($_POST['idproducto_diakros'] as $id) {
		$actualizar->resultado_consulta("UPDATE productos SET uds='-1' WHERE idproducto_diakros='{$id}';");
		$envio[]=-1;
		$i++;
	}
}
echo implode('&',$envio);
$actualizar->__destruct();

