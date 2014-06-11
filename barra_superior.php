<?php
	
		//hacienso un desplegable con el formulario de recuerdo de pass
		//contiene: mail_html. form_pass, 
		include 'reenvio_pass.php';

		if($_SESSION['datos_cesta']['aviso']) require 'aviso_disponibilidad.php';
		
		if($_SESSION['sesion_registrada']===true) {	
			include 'datos_registro.php';
		} else {
			include 'formulario_registro.php';
		}
		
	?>
