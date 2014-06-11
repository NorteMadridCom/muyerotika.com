<!--Estamos trabajando en esta parte, disculpen las molestias. -->
<div id="editar_clientes">
<p style="color: #000; font-size: 14px; margin-left: 110px; ">Si ya es usuario, conéctese:</p>

<table class="conectarse" style="float: left;">
			<form action="index.php?seccion=cesta" method="post" enctype="multipart/form-data">
				<input type="hidden" name="pago" value="1">
				<tr>
					<td class="conectarse" style="color: #000; font-size: 14px; text-align: right;">E-mail:</td>
					<td><input class="datos" name="usuario" value="" type="email" required></td>
				</tr>
				<tr>
					<td class="conectarse"  style="color: #000; font-size: 14px; text-align: right;">Contraseña:</td>
					<td><input type="password" class="datos" name="pass" value="" required></td>
				</tr>
				<tr>
					<td></td>
					<td  align="center"><button name="accion" value="Conectarse" class="conect" style="margin-left: 105px;">Conectarse</button></td>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="olvidaste" style="margin-left: 35px; font-size: 14px;" onclick="mostrar('form_pass')"><i>¿Olvidaste tu contraseña?</i></a></td>
				</tr>
			</form>
			</table>
			
<p style="color: #000; font-size: 14px; margin-left: 400px; margin-top: -30px;">Si no es usuario, regístrese:</p>			
			
			<a href="index.php?seccion=clientes" class="registrarse" style="margin-left: 130px;">Registrarse</a> 
</div>
