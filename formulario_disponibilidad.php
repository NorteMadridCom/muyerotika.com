		<!-- MENSAJE DE DISPONIBILIDAD-->
		<div id="form_<?php echo $this->_producto->idproducto; ?>" class="mensaje_disponibilidad" style="display: none;">
			<div style="float: right; margin: -20px; 0px 0px 5px; cursor: pointer;"><a onclick="ocultar('form_<?php echo $this->_producto->idproducto; ?>')">X</a></div>
			
			<div id="titulo_disp" >Producto no disponible</div>
			<div id="txt_disp" >Déjanos tus datos si quieres que te avisemos cuando este artículo esté disponible.</div>
		
			<form method="post" action="" enctype="multipart/formdata">	
			<input type="hidden" name="idproducto" value="<?php echo $this->_producto->idproducto; ?>" />	
			<input type="hidden" name="idproducto_diakros" value="<?php echo $this->_producto->idproducto_diakros; ?>" />
			<input type="hidden" name="producto" value="<?php echo $this->_producto->producto; ?>" />
				<table class="formularioDireccion">
					<tbody>
						<tr>
							<td class="t_contacto">Nombre:</td>
						</tr>
						<tr>
							<td><input class="contacto" name="nombre" value="<?php echo $_SESSION['nombre']; ?>" autofocus="" placeholder="Ponga su nombre" type="text" ></td>
						</tr>
						<tr>
							<td class="t_contacto">E-mail:</td>
						</tr>
						<tr>
							<td><input class="contacto" name="mail" value="<?php echo $_SESSION['usuario']; ?>" placeholder="Ponga su e-mail" type="email" required></td>
						</tr>
						<tr>
							<td colspan="2"><button class="contacto" type="submit" name="accion" value="enviar_disponibilidad">Enviar</button></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>



		
