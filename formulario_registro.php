<!-- FORMULARIO DE REGISTRO -->
		<div id="registro">
			<div id="centrador_web">
				
			<table class="conectarse">
			<form action="" method="post" enctype="multipart/form-data">
				<tr align="left">
					<td class="conectarse">E-mail:</td>
					<td><input class="caja_peq" name="usuario" value="<?php echo $_POST['usuario']; ?>" type="email" required></td>
				
					<td class="conectarse">Contraseña:</td>
					<td><input type="password" class="caja_peq" name="pass" value="" required></td>
			
					<td  align="center">	
						
							<button name="accion" value="Conectarse" class="conect">Conectarse</button>
							<a href="index.php?seccion=clientes" class="registrarse">Registrarse</a>
							<a href="#" class="olvidaste" onclick="mostrar('form_pass')"><i>¿Olvidaste tu contraseña?</i></a>
					</td>
				</tr>
			</form>
			</table>
			
			</div>
		</div>
