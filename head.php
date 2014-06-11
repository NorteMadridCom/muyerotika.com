
<!DOCTYPE html>
<html>
<head>
	
	<meta charset="utf-8" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
	<meta name="author" content="NorteMadrid.Com" >
	
	<link href="css/general.css" rel="stylesheet" type="text/css" />	
	<link href="css/header.css" rel="stylesheet" type="text/css" />	
	<link href="css/formularios.css" rel="stylesheet" type="text/css" />	
	<link href="css/cuerpo.css" rel="stylesheet" type="text/css" />	
	<link href="css/footer.css" rel="stylesheet" type="text/css" />
	<link href="css/cesta.css" rel="stylesheet" type="text/css" />
	<link href="css/emergente.css" rel="stylesheet" type="text/css" />
	
	<?php
	$titulo="Muy Erótica - Tu tienda de productos eróticos";
	if($_GET['familia']) $titulo = $_GET['familia'];
	if($_GET['subfamilia']) $titulo = $_GET['subfamilia'];
	if($_GET['subsubfamilia']) $titulo = $_GET['subsubfamilia'];
	if($_GET['fabricante']) $titulo = $_GET['fabricante'];
	if($_GET['linea']) $titulo = $_GET['linea'];
	if($_GET['producto']) $titulo = $_GET['producto'];
	?>
	
	<title><?php echo $titulo; ?></title>
	
	<link rel="stylesheet" href="orbit/js/themes/base/jquery.ui.all.css">
	<script src="orbit/js/jquery-1.9.1.js"></script>
	<script src="orbit/js/jquery.ui.core.js"></script>
	<script src="orbit/js/jquery.ui.widget.js"></script>
	<script src="orbit/js/jquery.ui.mouse.js"></script>
	<script src="orbit/js/jquery.ui.button.js"></script>
	<script src="orbit/js/jquery.ui.draggable.js"></script>
	<script src="orbit/js/jquery.ui.position.js"></script>
	<script src="orbit/js/jquery.ui.resizable.js"></script>
	<script src="vjs/jquery.ui.button.js"></script>
	<script src="orbit/js/jquery.ui.dialog.js"></script>
	<script type="text/javascript" src="highslide/highslide.js"></script>
	<script type="text/javascript" src="highslide/highslide.config.js" charset="utf-8"></script>
	<link rel="stylesheet" type="text/css" href="highslide/highslide.css" />
	
	<link rel="icon" href="img/favicon.png" type="image/x-icon">
	<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="highslide/highslide-ie6.css" />
	<![endif]-->
	<style type="text/css">
		#dialog-message{
				font-size: 62.5%
		}
		#dialog-message-ordenar{
			font-size: 62.5%
		}
		.ui-dialog .ui-dialog-titlebar {
		font-size: 60.5%;
		padding: 8px;
		position: relative;
		}
		.ui-dialog .ui-dialog-buttonpane button {
		margin: .5em .4em .5em 0;
		cursor: pointer;
		font-size: 60.5%;
		padding: 4px;
		}
	</style>
	<script>
	$(function() {
		$( "#dialog-message" ).dialog({
			modal: true,
			width: 600,
			buttons: {
				Cancelar: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	$(function() {
		$( "#dialog-message-ordenar" ).dialog({
			modal: true,
			width: 600,
			buttons: {
				Aceptar: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	$(function() {
		$( "#aviso" ).dialog({
			autoOpen: <?php echo $_SESSION['aviso']; ?>,
			modal: true,
			width: 600,
			buttons: {
				Aceptar: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	</script>
	<!-- sdm fin-->

    <!-- Pasafoto -->
		
		<link rel="stylesheet" href="orbit/css/orbit-1.2.3.css">
	  	<link rel="stylesheet" href="orbit/demo-style.css">
	  	
		<!-- Attach necessary JS -->
		<!--<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script> -->
		<script type="text/javascript" src="orbit/js/jquery.orbit-1.2.3.min.js"></script>
    <!-- Run the plugin -->
		<script type="text/javascript">
			$(window).load(function() {
				$('#orbit').orbit({
				     animation: 'fade',                  // fade, horizontal-slide, vertical-slide, horizontal-push
				     animationSpeed: 800,                // how fast animtions are
				     timer: true, 			 // true or false to have the timer
				     advanceSpeed: 4000, 		 // if timer is enabled, time between transitions 
				     pauseOnHover: false, 		 // if you hover pauses the slider
				     startClockOnMouseOut: false, 	 // if clock should start on MouseOut
				     startClockOnMouseOutAfter: 1000, 	 // how long after MouseOut should the timer start again
				     directionalNav: true, 		 // manual advancing directional navs
				     captions: true, 			 // do you want captions?
				     captionAnimation: 'fade', 		 // fade, slideOpen, none
				     captionAnimationSpeed: 800, 	 // if so how quickly should they animate in
				     bullets: true,			 // true or false to activate the bullet navigation
				     bulletThumbs: false,		 // thumbnails for the bullets
				     bulletThumbLocation: '',		 // location from this file where thumbs will be
				     afterSlideChange: function(){} 	 // empty function 
				});

			});
		</script>
		
		<script language="JavaScript">

			function muestra_oculta(id) {
				if (document.getElementById) { //se obtiene el id
					var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
					el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
				}
			}
			
			function mostrar(id){
				document.getElementById(id).style.display = 'block';
			}
			
			function ocultar(id){
				document.getElementById(id).style.display = 'none';
			}
		
		</script>
</head>
