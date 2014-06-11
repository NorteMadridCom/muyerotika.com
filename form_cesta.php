<div id="cesta_compra">
						<table align="left">
							<tr align="left">
								<td class="cesta">Artículos</td>
								<td><input class="caja_compra" name="articulos"  type="text" value="<?php echo count($_SESSION['detalles_cesta']); ?>" disabled="disabled"></td>
								<td class="cesta">Importe</td>
								<td><input class="caja_compra" name="importe" value="<?php echo $cesta->total_cesta().' €'; ?>" type="text" disabled="disabled"></td>
								<td><a href="index.php?seccion=cesta" ><img src="img/cesta.png" alt="Cesta Muy Erotika" width="20"></a></td>
							</tr>
						</table>
					</div>


<?php
	$display_cesta="none";
	if($_SESSION['mostrar_form_cesta']) {
		$display_cesta="block";
		unset($_SESSION['mostrar_form_cesta']);
	}
?>
<div id="cesta_compra_bloque" style="display: <?php echo $display_cesta; ?>;">
	<?php
	$minicesta=new Mostrar_cesta();
	$minicesta->formulario_minimio_cesta();
	?>
	<table align="left" class="cesta" style="margin-top: -20px; margin-left: 80px;">
		
		<tr align="left">
			<td style="font-weight: bold; font-size: 14px; padding-top: 50px;">Total gastos de envío incluidos:</td>
		
			<td><input class="caja_compra" style="margin-top: 50px; margin-left: 10px; padding-right: 2px;" name="importe" value="<?php echo $cesta->total_cesta().' €'; ?>" type="text" disabled="disabled" style="font-weight: bold; font-size: 14px; margin-left: 10px;"></td>
		</tr>
	</table>	
	
					
			
			
			<table>
			<td>		
				<form action="" method="post" enctype="multipart/form-data" id="formulario_compras">	
					<input type="submit" name="vaciar_cesta" value="Vaciar la cesta" id="bt_compras" style="float: left; width: 120px; margin-top: 20px;  margin-left: 130px;">
				</form>	
			</td>
			
			<td>
				<a onclick="ocultar('cesta_compra_bloque')" href="index.php?seccion=cesta" style="display: <?php echo $btn_terminar_compra; ?>;">
					<input type="submit" name="nada" value="Comprar" id="bt_compras" style="float: left; width: 120px;  margin-top: 20px; margin-left: 1px;">
				</a>
			</td>
			
			
	</table>

<div style="float: right; font-weight: bold; font-size: 20px; margin: 10px 20px -5px 0px; padding-bottom: 10px; cursor: pointer; font-family: Arial"><a onclick="ocultar('cesta_compra_bloque')">X</a></div>
<?php 
	$btn_terminar_compra =  'block';
	if(!$cesta->total_cesta()) $btn_terminar_compra =  'none';
?>


		
</div>
