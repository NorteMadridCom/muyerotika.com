<tr>
	<form method="post" enctype="multipart/form-data" action="" >
		<td><input name="ref" value="<?php echo $producto_relacionado->ref; ?>" size="10" maxlength="100"  type="text" class="admin_caja" disabled></td>
		<td><input name="producto" value="<?php echo $producto_relacionado->producto_nombre; ?>" size="60" maxlength="200" type="text" class="admin_caja" disabled></td>
		<td><button name="accion" value="eliminar_relacion" class="admin"><img src="img/eliminar.png" ></button></td>
		<input name="idproducto" value="<?php echo $this->_idproducto; ?>" type="hidden">
		<input name="id_relacionado" value="<?php echo $relacion->id_relacionado; ?>" type="hidden">
		<input name="parte" value="<?php echo $_POST['parte']; ?>" type="hidden">					
	</form>
</tr>
