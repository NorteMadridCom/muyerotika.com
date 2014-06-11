<form action="" method="post" enctype="multipart/form-data" >
	<table class="cesta_compra" >						
		<tr>
			<td style="text-align: left; background: none; ">Observaciones: </td>
			
		</tr>							
		<tr>	
			<td style="background: none; padding: 0px; ">
				<textarea name="observaciones"  class="observaciones"><?php echo $this->_datos['datos_cesta']['observaciones']; ?></textarea>
			</td>
		</tr>
		<tr>	
			<td style="background: none; padding: 0px; ">
				<button type="submit" name="accion" value="observaciones">Guardar observaciones</button>
			</td>
		</tr>						
	</table>
</form>
