			<table>
				<tbody>
					<tr>
						<th width="10" >Referencia</th>
						<th width="60">Nombre</th>
						<th>Acción</th>
					</tr>
					<tr>
						<form method="post" enctype="multipart/form-data" action="" >
							<td><input name="idproducto" value="<?php echo $idproducto; ?>" size="10" maxlength="100"  type="text" class="admin_caja"></td>
							<td><input name="producto" value="<?php echo $producto; ?>" size="60" maxlength="200" type="text" class="admin_caja"></td>
							<td><button name="accion" value="eliminar"><img src="../img/buscar.png" ></button></td>
							<input name="idproducto" value="<?php echo $this->_idproducto; ?>" type="hidden">
							<input name="parte" value="<?php echo $_POST['parte']; ?>" type="hidden">					
						</form>
					</tr>
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
						</form></th><td><button name="accion" value=""  class="admin"><img src="img/ordenar.png" ></button>
						</td><td><input name="" value="" type="hidden">
						</td><td><input name="" value="" type="hidden">
					
						</td><td colspan="3">
							</td></tr>
				</tbody>
			</table>


			<!-- emergente --> 
						<div id="enviado" style="visibility: <?php echo $envio_mail->visibilidad; ?>;"> <!-- style="visibility:hidden;" -->
							<form action="" method="post" enctype="multipart/formdata">			
								<p style="font-size: 15px; margin-bottom: 20px; font-family: 'arial';">       </p> 
								<button class="contacto" type="submit" name="nada" value="enviar" style="margin-right: 210px; margin-top: -5px;">Aceptar</button>
							</form>
						</div>
