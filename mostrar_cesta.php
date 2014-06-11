<?php

class Mostrar_cesta
{

	private $_mostrar_cesta =  true;
	private $_neto_total = 0;
	private $_neto_dto = 0;
	//private $_gastos_envio = 10.25;
	private $_iva = 0;
	private $_total = 0;
	private $_datos = array();
	private $_admin=false;
	
	public function __construct($idpedido=false) 
	{
		if($idpedido) {
			$this->_buscar_pedido($idpedido); 
			$this->_admin=true;
		} else $this->_datos=$_SESSION;
	}
	
	private function _buscar_pedido($idpedido) 
	{

		$sql="SELECT p.* FROM pedidos p WHERE p.idpedido=$idpedido;";
		$pedido = new Mysql;
		$pedido->ejecutar_consulta($sql);
		if(!$pedido->error) {
			$this->_datos['datos_cesta'] = (array) $pedido->registros[0];
			$sql="SELECT * FROM detalles_pedidos WHERE idpedido=$idpedido;";
			$pedido->ejecutar_consulta($sql);
			if(!$pedido->error) {
				foreach($pedido->registros as $detalle_pedido) {
					$this->_datos['detalles_cesta'][]= (array) $detalle_pedido;
				}
			}
		}
		$this->_datos['idpedido']=$this->_datos['datos_cesta']['idpedido'];
		$this->_datos['nombre']=$this->_datos['datos_cesta']['nombre'];
		$this->_datos['nif']=$this->_datos['datos_cesta']['cif'];
		$this->_datos['domicilio']=$this->_datos['datos_cesta']['direccion'];
		$this->_datos['cp']=$this->_datos['datos_cesta']['cp'];
		$this->_datos['poblacion']=$this->_datos['datos_cesta']['localidad'];
		$this->_datos['provincia']=$this->_datos['datos_cesta']['provincia'];
		$this->_datos['observaciones']=$this->_datos['datos_cesta']['observaciones'];
		$this->_datos['datos_cesta']['envio']=$this->_datos['datos_cesta']['gastos_envio'];
		$this->_neto_total=$this->_datos['datos_cesta']['neto'];
		$this->_neto_dto=$this->_datos['datos_cesta']['dto_volumen'];
		$this->_total=$this->_datos['datos_cesta']['total'];
		$this->_iva = $this->_total-$this->_neto_total;
		
	}

	public function formulario()
	{

		if ($_SESSION['sesion_registrada']) require 'form_cabecera_cesta.php'; 
		?>
				
				<form action="" method="post" enctype="multipart/form-data" id="form_borrar_direccion"></form>
				
				<table class="cesta_compra" cellpadding="4" style="width: 780px;">
					<thead>
						<tr align="left">
							<td class="tam_40">Cod.</td>
							<td class="tam_200">Artículos</td>
							<td class="tam_40">Uds</td>
							<td class="tam_80">Precio (€)</td>
							<td class="tam_40">Acción</td>
						</tr>
					</thead>
		<?php 
		$this->_detalles_cesta(); 
		if($this->_admin!==true) $this->_calcular();
		?>			
		
					<tr> 
						<td colspan="3" style="text-align: right; background: none; color: #000;">Gastos de envío: </td>
						<td style="text-align: right; width: 80px; height: 25px; color: #000;"><?php echo $this->_formato($this->_datos['datos_cesta']['envio']*1.21); ?></td>	
					</tr>
					
					<tr style="font-weight: bold;">
						<td colspan="3" style="text-align: right; background: none; color: #000;">Total: </td>
						<td style="text-align: right; width: 80px; height: 25px; color: #000;"><?php echo $this->_formato($this->_total); ?></td>
					</tr>
				</table>

		<?php
			if ($_SESSION['sesion_registrada']) require 'form_observaciones.php'; 
			if($this->_admin!==true) {
		?>
			
				<!-- CONDICIONES Y OBSERVACIONES CESTA COMPRA -->
				<div style="clear: both"></div>
			<form action="" method="post" enctype="multipart/form-data" id="formulario_compras" class="botones_debajo">
					<div id="txt_datos"><input form="form_pago" type="checkbox" name="acepto las condiciones legales" value="interesado" checked="checked" class="aceptar" required />Acepto las 
						<a onclick="window.open('condicionesgenerales.html','scrollbars=yes','top=60,left=450,width=1025,height=750')" class="legales" style="cursor: pointer">condiciones legales.</a>
					</div>
					<!-- <?php echo $terminar_compra; ?> -->					
					<input type="submit" name="vaciar_cesta" value="Vaciar la cesta" id="bt_compras" class="vaciar_cesta">	
					<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" class="bt_seguir_comprando">Seguir comprando</a>
			</form>			
					<!-- DATOS CESTA COMPRA -->	
			<?php 
				$btn_terminar_compra =  '<input type="submit" name="pago" value="Terminar compra" id="bt_compras">';
				if(!$this->_total) $btn_terminar_compra =  '<input type="submit" name="pago" value="Terminar compra" id="bt_compras" style="display: none;">';
			?>
			<form  action="index.php?seccion=pago" method="post" enctype="multipart/form-data" id="form_pago"><?php echo $btn_terminar_compra; ?></form>
			<!--<form  action="index.php?seccion=cesta" method="post" enctype="multipart/form-data" id="form_pago"><?php echo $btn_terminar_compra; ?></form>-->
		<?php	
		} else {
			echo "El pedido esta {$_POST['status']}";
			if($_POST['status']=='Pendiente') $acciones = '<button name="accion" value="confirmar">Confirmar</button><button name="accion" value="eliminar">Eliminar</button>'; 
			elseif($_POST['status']=='Pendiente de Pago') $acciones =  '<button name="accion" value="eliminar">Eliminar</button><button name="accion" value="pagar">Pagar</button>';
			?>
			<form action="" method="post" enctype="multipart/form-data">
				<?php
				echo '<input type="hidden" name="idpedido" value="'.$this->_datos['idpedido'].'" />'; 
				echo $acciones; 
				?>
			</form>
			<?php
		}
	}
	
	public function formulario_minimio_cesta()
	{
		require 'cesta_minima.php';
	}
	
	private function _calcular() 
	{
		if($this->_mostrar_cesta === true
			&& $this->_datos['datos_cesta']['neto_total']
			&& is_array($this->_datos['datos_cesta']['neto_dto']) 
			&& is_array($this->_datos['datos_cesta']['iva']) 
		) {
			$this->_neto_total = $this->_datos['datos_cesta']['neto_total'];
			$this->_neto_dto = array_sum($this->_datos['datos_cesta']['neto_dto']);
			$this->_iva = array_sum($this->_datos['datos_cesta']['iva']);
			$this->_total =  array_sum($this->_datos['datos_cesta']['iva']) + array_sum($this->_datos['datos_cesta']['neto_dto']) + $this->_datos['datos_cesta']['envio'];
		}
	}
	
	private function _formato($val)
	{
		return number_format(round($val, $precision = 2), $decimals = 2);
	}
	
	private function _detalles_cesta($min=false)
	{
		if(is_array($this->_datos['detalles_cesta'])) {
			foreach($this->_datos['detalles_cesta'] as $detalle) {
				$detalle = $detalle + $this->_consulta_produtos($detalle['idproducto']);
				if(!$detalle['producto_nombre_web']) $detalle['producto_nombre_web']=$detalle['producto_nombre'];
				if(!$detalle['dto_detalle']) $detalle['dto_detalle']=$detalle['dto'];
				if($min==true) require 'form_detalle_cesta_min.php';
				else require 'form_detalle_cesta.php';
			}	
		} else $this->_mostrar_cesta = false;
	}
	
	private function _consulta_produtos ($idproducto)
	{
		$sql_prod = "SELECT ref, producto_nombre, producto_nombre_web, precio as precio_sin_dto FROM productos WHERE idproducto=$idproducto;";
		$consulta_producto = new Mysql;
		$consulta_producto->ejecutar_consulta($sql_prod);
		return (array) $consulta_producto->registros[0];
	}

}
