<!-- mensaje emergente --> 
	<div id="centrador_web">
	<div  style="visibility: <?php echo $envio_mail->visibilidad; ?>;" class="emergente"> <!-- style="visibility:hidden;" -->
			
				
			<form action="" method="post" enctype="multipart/formdata">	
			<div id="formulario">
				<table  style="margin-right: 20px;">
								
					<tbody>
						<tr>
							<td class="t_contacto">Nombre:</td>
						</tr>
						<tr>
							<td><input class="contacto" name="nombre" value="" type="text" required autofocus placeholder="Ponga su nombre" required /></td>
						</tr>
						<tr>
							<td class="t_contacto">E-mail:</td>
						</tr>
						<tr>
							<td><input class="contacto" name="email" value="" type="email" required placeholder="Ponga su e-mail" required /></td>
						</tr>
						<tr>
							<td class="t_contacto">Teléfono:</td>
						</tr>
							<tr>
								<td><input class="contacto" name="telefono" value="" type="tel"  placeholder="Ponga su telefono" /></td>
							</tr>
					              
					</tbody>
				</table>	
			
				</form>
				
				<br>
				
				<button class="contacto" type="submit" name="nada" value="" style="margin-left: 20px;">Cancelar</button>
				<button class="contacto" type="submit" name="nada" value="enviar" >Enviar</button>
				
			</div>
		
	</div>
	</div>
<!-- fin mensaje de emergente -->