<?php 

echo '<p>Inicio prueba de conexion</p>';

echo $conexion = odbc_connect("ConexionDizma", "NorteMadrid", "Dizma123456") or die("EL ERROR ES: ".odbc_errormsg());

$resultado = odbc_exec($conexion,  "select idarticulo from articulos where idarticulo<50;");

var_dump($resultado);

while ($art=odbc_fetch_array($resultado)) {
echo $art['idarticulo'].'<br />';
}

odbc_close($conexion);

echo '<p>Fin prueba de conexion</p>';
?>