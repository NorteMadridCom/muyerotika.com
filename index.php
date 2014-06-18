<?php
session_start();

if($_SESSION['veces']>2) {
	die ('No se puede acceder a esta página. Cierre el navegador y vuelva a intentarlo');
}

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
require_once 'includes/class.enlace_get.php';
require_once 'includes/class.html_mail.php';

require_once 'menu_horizontal.php';
require_once 'cesta.php';
require_once 'pasafoto.php';
require_once 'dto_prioritario.php';
require_once 'gastos_envio.php';
require_once 'mostrar_cesta.php';
require_once 'navegacion.php';
require_once 'submenu.php';
require_once 'producto.php';
require_once 'seleccion_familias.php';
require_once 'editar_productos.php';
require_once 'buscador.php';
require_once 'editar_fabricantes.php';
require_once 'editar_categorias.php';
require_once 'editar_destacados.php';
require_once 'seleccion_fabricantes.php';
require_once 'poner_productos.php';
require_once 'editar_clientes.php';
require_once 'editar_tarifas.php';
require_once 'editar_grupos.php';
require_once 'editar_descuentos.php';
require_once 'ckeditor/ckeditor.php';
require_once 'relacionados.php';
require_once 'precio_minimo.php';
require_once 'pedido.php';
require_once 'pedido_pdf.php';
require_once 'editar_pedidos.php';
require_once 'disponibilidad.php';
require_once 'editar_envios.php';



$config = new Config(); //extends de mysql con las propiedades para la configuracion
//$config->conf[variable]

$usuario = new Sesion();
$cesta = new Cesta($config->conf);

if($_SESSION['sesion_registrada'] !== true) {		
	if($_POST['accion']=='Conectarse') {
		if(!$_SESSION['veces']) {
			$_SESSION['veces']=1;
		} else {
			$_SESSION['veces']++;
		}
		$usuario->consultar_usuario($_POST['usuario'],$_POST['pass']);
	} 
} else {
	unset($_SESSION['veces']);
	if($_POST['accion'] == 'desconectarse') {
		$usuario->desconectar();
		unset($_SESSION);
		$_SESSION['aviso']="false";
	}
}
$usuario->__destruct();

//valore iniciales de la pagina
$where_prod_prof = " AND profesional=0 ";
if($_SESSION['profesional']==1) $where_prod_prof='';
/*
if(!$_SESSION['tarifa_dto']) {
	$tarifa_dto_general = new Mysql;
	$tarifa_dto_general->ejecutar_consulta("SELECT tarifa_dto FROM tarifas WHERE general=1 LIMIT 0,1;");
	$_SESSION['tarifa_dto']=$tarifa_dto_general->registros[0]->tarifa_dto;
	$tarifa_dto_general->__destruct();
}	
*/

if($_POST['accion']==="cerrar_aviso_stock") unset($_SESSION['datos_cesta']['aviso']);


if(isset($_POST['anadir_cesta'])) {
	$cesta->add_producto($_POST['idproducto'],$_POST['uds_compra'],$_POST['idiva'],$_POST['precio']);
	$cesta->calcular_cesta();
	if($_GET['seccion']!='cesta') $_SESSION['mostrar_form_cesta']=true;
} elseif(isset($_POST['eliminar_cesta'])) {
	$cesta->del_producto($_POST['idproducto']);
	$cesta->calcular_cesta();
	if($_GET['seccion']!='cesta') $_SESSION['mostrar_form_cesta']=true;
} elseif(isset($_POST['actualizar_cesta'])) {
	$cesta->edit_producto($_POST['idproducto'],$_POST['uds']);
	$cesta->calcular_cesta();
	if($_GET['seccion']!='cesta') $_SESSION['mostrar_form_cesta']=true;
} elseif(isset($_POST['vaciar_cesta'])) {
	$cesta->vaciar_cesta();
	$cesta->calcular_cesta();
} elseif($_POST['accion']=='Conectarse') {
	$cesta->recalcular_cesta();
	$cesta->calcular_cesta();
} elseif($_POST['accion']=='actualizar_envio') {
	$cesta->datos_envio($_POST);
	//$cesta->calcular_cesta();
} elseif($_POST['accion']=='eliminar_envio') {
	$cesta->eliminar_envio();
} elseif($_POST['accion']=="observaciones") {
	$cesta->otros_datos($_POST);
}
//var_dump($_SESSION);
//var_dump($_POST);

require 'head.php';

?>
<body>
	<div style="background-color: #fff;">
	<!-- barra de registro (BARRA SUPERIOR) -->
	<?php 
		//require 'emergente_formulario.php'; //para borrar
		require 'barra_superior.php'; 
		
		if($_SESSION['tipo_cliente']=='administrador') require 'backend.php';
		else require 'frontend.php';
		
	?>
	</div>
</body>
</html>
