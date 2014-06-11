<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

		<title>Comercial Dizma S.L. - Quienes somos</title>
		<meta name="description" content="" />
		<meta name="author" content="Usuario" />

		<meta name="viewport" content="width=device-width; initial-scale=1.0" />

		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
		<link href="somos.css" rel="stylesheet" type="text/css" />
		<link href="mapa.css" rel="stylesheet" type="text/css" />
	</head>

	<body>
		<div id="centrador">
			<header>
				
				<a href="index.php"><img src="img/logo.jpg" class="logo" alt="logo comercial Dizma S.L" width="220" border="0"></a> 
				<h1>Mapa del sitio</h1>
				
			</header>

		
			<div id="contenido">		
		
				<h3>Accede a toda nuestra web en un solo click:</h3>
				
				<p>Si quieres acceder a cualquier sección de nuestra página web, puedes hacerlo haciendo click en cualquiera de los siguientes enlaces.</p>
				
				
				<ul class="diferentes_cajas"><h4 class="categoria_mapa">PIE DE PÁGINA</h4>
				
					<li class="producto_mapa">Sobre Nosotros</li>
					<ul>
							<li><a class="marca_mapa" onclick="window.open('somos.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">¿Quienes somos?</a></li>
							<li><a class="marca_mapa" onclick="window.open('nuestratienda.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Nuestras tienda</a></li>
							<li><a class="marca_mapa" onclick="window.open('contacto.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Contacta con nosotros</a></li>
						
					</ul>
				
					<li class="producto_mapa">Atención al cliente</li>
					<ul>
							<li><a class="marca_mapa" onclick="window.open('cambios_devoluciones.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Cambios y devoluciones</a></li>
							<li><a class="marca_mapa" onclick="window.open('formaspago.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Formas de pago</a></li>
							<li><a class="marca_mapa" onclick="window.open('preguntas.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Preguntas frecuentes</a></li>
						
					</ul>
					
					<li class="producto_mapa">Síguenos</li>
					<ul>
							<li><a href="https://www.facebook.com/www.dizma.es?fref=ts" target="_blank" class="marca_mapa">Facebook</a></li>
							<li><a href="https://twitter.com/comercialdizma" target="_blank" class="marca_mapa">Twitter</a></li>
						
					</ul>
					
					<li class="producto_mapa">Otros</li>
					<ul>
							<li><a class="marca_mapa" onclick="window.open('aviso.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Aviso Legal</a></li>
							<li><a class="marca_mapa" onclick="window.open('condicionesgenerales.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Condiciones generales</a></li>
							<li><a class="marca_mapa" onclick="window.open('privacidad.html','scrollbars=yes','top=60,left=450,width=1025,height=750')">Privacidad</a></li>
						
					</ul>
				
				</ul>
				
				<?php
				include_once 'mapa_web.php';
				$mapa=new Mapa_web();
				?>
				
			</div>
		</div>
		
	</body>
</html>
