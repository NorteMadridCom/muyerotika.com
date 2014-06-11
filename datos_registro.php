<!-- FORMULARIO DE REGISTRO -->
		<div id="registro">
			<div id="centrador_web">
			<form action="index.php?seccion=cliente" method="post" enctype="multipart/form-data">
			<!--
				<div id="contacta">
					<div id="contacta" style="margin-right: 30px;"><img src="img/phone.png" width="20" height="20" class="contacta" />913190337</div>
					<div id="contacta"><a class="contacta" href="mailto:comercial@dizma.es"><img src="img/mail.png" width="20" height="20" class="contacta" style="margin-right: -10px; margin-top: -3px;"/>comercial@dizma.es</a></div>
				</div>
			-->	
			<table class="conectarse" style="margin-right: 25px; ">
				<tr align="left"> 
					<td align="left"  class="conectado" >¡Bienvenido a Muy Erótika, <?php echo $_SESSION['nombre']; ?>!
			
					<td  align="center" >	
						<button name="accion" value="desconectarse" class="conect" style="margin-left: -35px;">Desconectarse</button>
						<button name="accion" value="datos" class="conect">Mis datos</button>
					</td>
				</tr>
				
			</table>
			
			</form></div>
		</div>
