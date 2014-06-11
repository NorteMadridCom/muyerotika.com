<!-- Zona de logo, buscador y cesta (BARRA SUPERIOR) -->
	<div id="centrador_web">
		
		<!-- ZONA LOGO BUSCADOR Y CESTA (HEADER) -->
		
		<?php require 'header.php'; ?>
		

		<!-- MENU-->
		<nav>
			
			<?php
					$categorias = new Menu_horizontal($config);
			?>
	
		</nav>
		
		<!-- CONTENIDO -->

			<!-- PASAFOTO -->
			<?php 
				if(!$_GET) {
					$pasafoto = new Pasafoto();
					$pasafoto->mostrar();
				}
			?>
			
			<!-- ZONA DE EXPOSICIÓN-->		
			<?php require 'cuerpo.php'; ?>

	</div>

	<!-- PIE -->
	<?php require 'pie.php'; ?>
