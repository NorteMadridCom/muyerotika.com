<?php

	$display_pass="none";
	if($_POST['accion']=='enviar_pass') $display_pass="block";

?>

<div id="form_pass" class="form_pass" style="display: <?php echo $display_pass; ?>;">
	<div style="float: right; margin: -20px; 0px 0px 5px; cursor: pointer;"><a onclick="ocultar('form_pass')">X</a></div>
	
	<div id="titulo_disp" style="margin-left: 70px;">¿Olvidaste tu contraseña?</div>

<?php

if($_POST['accion']=='enviar_pass') {
	$mensaje_error = '<div id="txt_disp">No existe ningun usuario con este e-mail.</div>';
	$sql_pass="SELECT usuario, pass FROM clientes WHERE usuario='{$_POST['mail']}' and eliminado=0 AND desbloqueo IS NULL;";
	$usuario_pass = new Mysql;
	$usuario_pass->ejecutar_consulta($sql_pass);
	if($usuario_pass->numero_registros==1) {
		$mensaje_error = '<div id="txt_disp">Hay un error y no se ha podido enviar la contraseña a su cuenta de usuario. Inténtelo más tarde.</div>';
		$cuerpo_mail = '
			<p>Su contraseña en Dizma.es es: <b>'.$usuario_pass->registros[0]->pass.'</b></p>
			<p>Gracias por su confianza en nosotros.</p>
		';
		
		$env_pass = new Html_mail($cuerpo_mail,"Recordatorio de Contraseña de Dizma.es", true, $usuario_pass->registros[0]->usuario);
		if(!$env_pass->error) {
			$mensaje_error = '<div id="txt_disp">Se le ha enviado un e-mail a su cuenta con la contraseña.</div>';
		}
		
	}
	echo $mensaje_error;
} else {
	?>
	<form method="post" action="" enctype="multipart/formdata">	
		<table class="formularioDireccion" style="margin-top: 40px;">
			<tbody>
				
				<tr>
					<td class="t_contacto">E-mail:</td>
				</tr>
				<tr>
					<td><input class="contacto" name="mail" value="" placeholder="Ponga su e-mail" required type="email"></td>
				</tr>
				<tr>
					<td colspan="2"><button class="contacto" type="submit" name="accion" value="enviar_pass">Enviar</button></td>
				</tr>
			</tbody>
		</table>
	</form>
	<?php
}
?>
</div>
