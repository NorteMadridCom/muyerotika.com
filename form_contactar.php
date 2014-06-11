
<form action="" method="post" enctype="multipart/form-data">
		<table class="contacto" style="margin-right: 20px;">
							
			<tbody>
				<tr>
					<td class="t_contacto">Nombre:</td>
				</tr>
				<tr>
					<td><input class="contacto" name="nombre" value="<?php echo $_POST['nombre']; ?>" type="text" required autofocus placeholder="Ponga su nombre" required /></td>
				</tr>
				<tr>
					<td class="t_contacto">E-mail:</td>
				</tr>
				<tr>
					<td><input class="contacto" name="email" value="<?php echo $_POST['email']; ?>" type="email" required placeholder="Ponga su e-mail" required /></td>
				</tr>
				<tr>
					<td class="t_contacto">Teléfono:</td>
				</tr>
					<tr>
						<td><input class="contacto" name="telefono" value="<?php echo $_POST['telefono']; ?>" type="tel"  placeholder="Ponga su telefono" /></td>
					</tr>
			              
				</tbody>
		</table>	
					
		<table class="contacto">	
			<tbody>
				<tr><td class="t_contacto">Mensaje:</td></tr>
				<tr><td><textarea name="mensaje"   class="contacto" placeholder="Escriba un mensaje"><?php echo $_POST['mensaje']; ?></textarea></td></tr>
				<tr><td colspan="2"><button class="contacto" type="submit" name="accion" value="enviar">Enviar</button></td></tr>
							
			</tbody>
		</table>	
</form>
	