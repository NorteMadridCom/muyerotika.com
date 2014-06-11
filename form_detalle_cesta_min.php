<tr>
<form action="" method="post" enctype="multipart/form-data">
	<td style="text-align: left;"><?php echo $detalle['producto_nombre_web']; ?></td>
	<td class="editable"><input type="text" name="uds" value="<?php echo $detalle['uds']; ?>" class="uds" /></td>
	<td style="text-align: right;"><?php echo $this->_formato($detalle['precio']*$detalle['dto_detalle'] * $detalle['uds']*1.21); ?></td>
	<td class="blanco">						
		<input type="hidden" name="idproducto" value="<?php echo $detalle['idproducto']; ?>"/>
		<input type="submit" class="actualizar" name="actualizar_cesta" value=""/>
		<input type="submit" class="eliminar" name="eliminar_cesta"  value=""/>
	</td>
</form>
</tr>		
