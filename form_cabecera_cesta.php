	<table class="cesta_compra" cellpadding="4">
					<thead>
						<tr align="left">
							<td class="tam_40" colspan="4">Datos del Cliente:</td>
							
						</tr>
					</thead>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Nombre:</td>
						<td style="width: 250px;"><?php echo $this->_datos['nombre']; ?></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">NIF/CIF:</td>
						<td style="width: 250px;"><?php echo $this->_datos['nif']; ?></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Calle:</td>
						<td style="width: 250px;"><?php echo $this->_datos['domicilio']; ?> </td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Población:</td>
						<td style="width: 250px;"><?php echo $this->_datos['poblacion']; ?> </td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">CP:</td>
						<td style="width: 250px;"><?php echo $this->_datos['cp']; ?></td>
					</tr>
										<tr>
						<td style="text-align: left; background: none; width: 50px;">Provincia:</td>
						<td style="width: 250px;"><?php echo $this->_datos['provincia']; ?></td>
					</tr>
					
					
				</table>
				<form action="" method="post" enctype="multipart/form-data">
				<table class="cesta_compra" cellpadding="4" style="margin">
					<thead>
						<tr align="left">
							<td class="tam_40" colspan="4">Datos de envio diferentes:</td>
													
						</tr>
					</thead>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Nombre:</td>
						<td style="width: 250px;"><input name="nombre_envio" value="<?php echo $this->_datos['datos_cesta']['nombre_envio']; ?>" class="datos_cesta" required></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Calle:</td>
						<td style="width: 250px;"><input name="direccion_envio" value="<?php echo $this->_datos['datos_cesta']['direccion_envio']; ?>" class="datos_cesta" required></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Población:</td>
						<td style="width: 250px;"><input name="localidad_envio" value="<?php echo $this->_datos['datos_cesta']['localidad_envio']; ?>" class="datos_cesta" required></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">CP:</td>
						<td style="width: 250px;"><input name="cp_envio" value="<?php echo $this->_datos['datos_cesta']['cp_envio']; ?>" class="datos_cesta" required></td>
					</tr>
					
					<tr>
						<td style="text-align: left; background: none; width: 50px;">Provincia:</td>
						<td style="width: 250px;"><input name="provincia_envio" value="<?php echo $this->_datos['datos_cesta']['provincia_envio']; ?>" class="datos_cesta" required></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: left; background: none; width: 180px;">
							<button type="submit" name="accion" value="actualizar_envio">Grabar nueva dirección</button>
							<button type="submit" name="accion" value="eliminar_envio" form="form_borrar_direccion">Borrar dirección</button>
						</td>					
					</tr>
					
				</table>
				</form>
