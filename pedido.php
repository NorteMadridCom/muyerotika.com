<?php

class Pedido
{
	
	public $error=false;
	private $_no_pedido;
	
	
	public function __construct() {}
	
	public function pedido() 
	{
    
    	$total=array_sum($_SESSION['datos_cesta']['neto_dto'])+$_SESSION['datos_cesta']['envio']+array_sum($_SESSION['datos_cesta']['iva'])+$_SESSION['recargos'];
		$fecha=date('Y-m-d',$_SESSION['fecha']);
		
		if(!$_SESSION['datos_cesta']['no_pedido']) {
			$pedido=new Mysql;
			$pedido_sql="
			INSERT INTO 
				pedidos 
			SET 
				idcliente={$_SESSION['idcliente']},  
				fecha='{$fecha}', 
				nombre='{$_SESSION['nombre']}', 
				cif='{$_SESSION['nif']}', 
				direccion='{$_SESSION['domicilio']}', 
				cp='{$_SESSION['cp']}', 
				localidad='{$_SESSION['poblacion']}', 
				provincia='{$_SESSION['provincia']}', 
				nombre_envio='{$_SESSION['datos_cesta']['nombre_envio']}', 
				direccion_envio='{$_SESSION['datos_cesta']['direccion_envio']}', 
				cp_envio='{$_SESSION['datos_cesta']['cp_envio']}', 
				localidad_envio='{$_SESSION['datos_cesta']['localidad_envio']}', 
				provincia_envio='{$_SESSION['datos_cesta']['provincia_envio']}', 
				neto='{$_SESSION['datos_cesta']['neto_total']}', 
				dto_volumen='{$_SESSION['datos_cesta']['dto_volumen_%']}', 
				gastos_envio='{$_SESSION['datos_cesta']['envio']}', 
				recargos='{$_SESSION['datos_cesta']['recargos']}', 
				total='{$total}',  
				observaciones='{$_SESSION['datos_cesta']['observaciones']}', 
				pass=FLOOR(1 + (RAND() * 999999));
			";		
			
			$pedido->resultado_consulta($pedido_sql);
			$no_pedido=$pedido->id_ultimo_registro;
			if(!$pedido->error && is_array($_SESSION['detalles_cesta'])) {
				foreach($_SESSION['detalles_cesta'] as $detalle) {
					$producto = $this->_codigo($detalle['idproducto']);
					$sql_detalles_pedidos ="
					INSERT INTO 
						detalles_pedidos 
					SET 
						idpedido=$no_pedido,  
						idproducto='{$detalle['idproducto']}', 
						cod_producto='{$producto->cod}', 
						producto='{$producto->nombre}',  
						uds='{$detalle['uds']}',  
						precio='{$detalle['precio']}',  
						dto='{$detalle['dto_detalle']}'
					;"; //modificaciones futuras con varios ivas
					$pedido->resultado_consulta($sql_detalles_pedidos);
				}
			}
		}

		if(!$pedido->error) {
			if(!$_SESSION['datos_cesta']['no_pedido']) $_SESSION['datos_cesta']['no_pedido']=$no_pedido;
			$pdf = new Generar_pdf;
			$pdf->Genera_pdf($_SESSION['datos_cesta']['no_pedido']);
			include 'pagar.php';
		} else {
			echo "<p>Se ha producido un error. Inténtelo de nuevo más tarde y si el problema persiste póngase en contacto con el administrador del sistema.</p>";
		}
				
	}	
	
	private function _codigo($idproducto) 
	{
		$producto=new Mysql;
		$sql="SELECT idproducto_diakros as cod, producto_nombre as nombre from productos where idproducto=$idproducto;";
		$producto->ejecutar_consulta($sql);
		return $producto->registros[0];
		$producto->__destruct();
	}
	
	public function aceptado()
	{
		
		$no_pedido = $_SESSION['datos_cesta']['no_pedido'];
		
		$pass=new Mysql;
		$sql="SELECT pass FROM pedidos WHERE idpedido=$no_pedido;";
		$pass->ejecutar_consulta($sql);
		$mensaje="/pedidos/Pedido_".$no_pedido."_".$pass->registros[0]->pass.".pdf";
		
		$asunto = "Compra en la tienda Online de Dizma.es";
			
		$mes = "<p>Enhorabuena, ha realizado con éxito su compra en Dizma.es.</p>";
		$mes .= '<p>Descargue su pedido <a target="_blank" href="http://'. $_SERVER['SERVER_NAME'] . $mensaje . '"> Pedido nº '. $no_pedido . '</a>.</p>';
		
		if($_GET['respuesta']=="Transferencia") {
			$mes .= "<p>Recuerde que no será efectivo el pedido hasta no recibir la transferencia en nuestra cuenta del Banco Popular haciendo referencia en el concepto al nº de pedido. </p>";
			$mes .= "<p>Nº de cuenta del Banco Popular 0075 0602 45 0600086764</p>";
		} else {
			$mes .= "<p>Su pedido se está tramitando en este momento. Pronto recibirá la confirmación del mismo por nuestro sistema.</p>";
		}
		
		$mes .= "<p>Muchas gracias en nombre del equipo de DIZMA S.L.</p>";		

		//$this->_aceptado($no_pedido,$fecha_pago);
		//var_dump($mes);
		
		//__construct($mensaje_html, $asunto, $directo=true, $destinatario=false, $remitente=false, $responder_a=false)
		$mail_cliente = new Html_mail($mes,$asunto,true, $_SESSION['usuario']);
		$mail_admin = new Html_mail($mes, $asunto, false, false, $_SESSION['usuario']);
		
		echo '<p>Se ha generado el <a target="_blank" href="'. $mensaje . '"> Pedido nº '. $no_pedido . '</a> y se ha enviado a su correo.</p>';
		
		unset($_SESSION['detalles_cesta']);
		unset($_SESSION['datos_cesta']);
	}
	
	public function pago()
	{
		
		define('currency', '978');
		define('terminal', '001');
		define('clave', 'qwertyasdf0123456789');
		define('code', '045515749');
		define('transactionType', '0'); //autorizacion
		define('urlMerchant', 'https://www.dizma.es/respuesta_pago.php');
		
		$total=$_POST['Ds_Amount'];
		$pedido=$_POST['Ds_Order'];
		

		//$message = $total . $pedido . code . currency . transactionType . urlMerchant . clave;
		$message = $total . $pedido . code . currency . $_POST['Ds_Response'] . clave;
		$signature = strtoupper(sha1($message));

		if( ((int) $_POST['Ds_Response']) < 100 ) {
			$fecha=explode('/', $_POST['Ds_Date']);			
			$fecha_pago=$fecha[2]."/".$fecha[1]."/".$fecha[0] . " " . $_POST['Ds_Hour'] ;
			if($_POST['Ds_Signature'] == $signature) {	
				$no_pedido = (int) $pedido;
				$pedido = new Mysql;
				$pedido_sql="UPDATE pedidos SET pago='$fecha_pago' WHERE idpedido=$no_pedido;";
				$pedido->resultado_consulta($pedido_sql);	
			}
			//$cadena = $_POST['Ds_Signature'] . "----" . $signature;
			//file_put_contents('./datos.txt', $cadena);
		} 
		
	}
	
}
