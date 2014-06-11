<form method="post" enctype="multipart/form-data" action="" >
	<?php $tipo_cliente = new Combo('idtipo_cliente', 'tipos_clientes', 'idtipo_cliente', 'tipo_cliente', $seleccionado = false, $vacio = false, $filtro = false, $campo_eliminado = false, $campo_orden = false, $disabled = false, $visible = true, $required = true, $eventos = null) ; ?>
	<td><?php $tipo_ciente->poner_combo(); ?></td>
	<td><input name="dto_prioritario" value="<?php echo $dto_prioritario; ?>" size="10" maxlength="6" type="text" class="admin_caja"></td>
	<td><button name="accion" value="anadir_dto_prioritario" class="admin"><img src="img/nuevo.png" ></button></td>
	<input name="idproducto" value="<?php echo $this->_idproducto; ?>" type="hidden">
	<input name="parte" value="<?php echo $_POST['parte']; ?>" type="hidden">					
</form>
