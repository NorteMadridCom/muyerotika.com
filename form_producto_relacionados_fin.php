<tr>
			<form method="post" enctype="multipart/form-data" action="" >
				<td><input name="idproducto" value="<?php echo $idproducto; ?>" size="10" maxlength="100"  type="text" class="admin_caja"></td>
				<td><input name="producto" value="<?php echo $producto; ?>" size="60" maxlength="200" type="text" class="admin_caja"></td>
				<td><button name="accion" value="buscar"><img src="../img/buscar.png" ></button></td>
				<input name="idproducto" value="<?php echo $this->_idproducto; ?>" type="hidden">
				<input name="parte" value="<?php echo $_POST['parte']; ?>" type="hidden">					
			</form>
		<tr>
		<th width="10">Ordenar
		
		<form method="post" enctype="multipart/form-data" action="">
			</form></th><td><button name="accion" value=""><img src="../img/ordenar.png" ></button>
			</td><td><input name="" value="" type="hidden">
			</td><td><input name="" value="" type="hidden">
		
			</td><td colspan="3">
				</td></tr>
	</tbody>
</table>
