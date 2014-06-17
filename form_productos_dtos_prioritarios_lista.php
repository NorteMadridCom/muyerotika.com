<tr>
<form method="post" enctype="multipart/form-data" action="" >
	<td><?php echo $dto_pri->tipo_cliente; ?></td>
	<td><input name="dto_prioritario" value="<?php echo number_format($dto_pri->dto_prioritario, 2, '.', ''); ?>" size="10" maxlength="6" type="text" class="admin_caja"></td>
	<td>
			<button name="accion" value="editar_dto_prioritario" class="admin"><img src="img/editar.png" ></button>
			<button name="accion" value="eliminar_dto_prioritario" class="admin"><img src="img/eliminar.png" ></button>
	</td>
	<input name="idtipo_cliente" value="<?php echo $dto_pri->idtipo_cliente; ?>" type="hidden">
	<input name="idproducto" value="<?php echo $this->_idproducto; ?>" type="hidden">
	<input name="parte" value="<?php echo $_POST['parte']; ?>" type="hidden">					
</form>
</tr>
