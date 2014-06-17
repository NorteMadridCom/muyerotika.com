<?php
	if( $_SESSION["tipo_cliente"] == "administrador") {
?>
		<!-- MENU-->
		<div style="background-color: #fff; ">
		<div id="menu_admin" style="float: left; margin-left: 20px;">
				<!--
					<div id="opcion">
						<div id="titulo_menu"><b>Edición Productos</b></div>
						<ul>
							<a href="index.php?seccion=editar_fabricantes" ><li class="opcion">Fabricantes</li></a>
							<a href="index.php?seccion=editar_categorias"><li class="opcion">Categorias</li></a>
							<a href="index.php?seccion=editar_productos"><li class="opcion">Productos</li></a>
						</ul>
					</div>	
					
					<div id="opcion">
						<div id="titulo_menu"><b>Clientes</b></div>
						<ul>
							<a href="index.php?seccion=grupos"><li class="opcion">Grupos</li></a>
							<a href="index.php?seccion=tarifas"><li class="opcion">Tarifas</li></a>
							<a href="index.php?seccion=clientes"><li class="opcion">Clientes</li></a>
						</ul>
					</div>
						
					<div id="opcion">	
						<div id="titulo_menu"><b>Pedidos</b></div>
						<ul>
							<a href="index.php?seccion=descuentos" class="menu_lateral"><li class="opcion">Descuentos</li></a>
							<a href="index.php?seccion=pedidos"><li class="opcion">Pedidos</li></a>
							<a href="index.php?seccion=gastos_envio"><li class="opcion">Gastos Envio</li></a>
						</ul>
					</div>
						
					<div id="opcion">	
						<div id="titulo_menu"><b>Utilidades</b></div>
						<ul>
							<a href="index.php?seccion=registros"><li class="opcion">Registros</li></a>
							<a href="index.php?seccion=backup"><li class="opcion">Copia de Seguridad</li></a>
						</ul>
					</div>	
					
				-->
					
					
			      		 	<div id="titulo_menu">Edición Productos</div>
			      		 	
			      		 	<div id="">
			      		 		<ul class="pie" style="margin-top: 0px;">
			      		 			<li class="opcion"><a href="index.php?seccion=editar_fabricantes" class="opcion" >Fabricantes</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=editar_categorias" class="opcion" >Categorias</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=editar_productos" class="opcion" >Productos</a></li>
			      		 		</ul>
			      		 		
			      		 	</div>
			      		
			      		
			      		
			      		 	<div id="titulo_menu">Clientes</div>
			      		 	
			      		 	<div id="">
			      		 		<ul class="pie" style="margin-top: 0px;">
			      		 			<li class="opcion"><a href="index.php?seccion=grupos" class="opcion" >Grupos</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=tarifas" class="opcion" >Tarifas</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=clientes" class="opcion" >Clientes</a></li>
			      		 		</ul>
			      		 		
			      		 	</div>
			      	
			      		
			      		
			      		 	<div id="titulo_menu">Pedidos</div>
			      		 	
			      		 	<div id="">
			      		 		<ul class="pie" style="margin-top: 0px;">
			      		 			<li class="opcion"><a href="index.php?seccion=descuentos" class="opcion" >Descuentos</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=pedidos" class="opcion" >Pedidos</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=gastos_envio" class="opcion" >Gastos Envio</a></li>
			      		 		</ul>
			      		 	</div>
			      	
			      		
			      		 	<div id="titulo_menu">Utilidades</div>
			      		 	
			      		 	<div id="">
			      		 		<ul class="pie" style="margin-top: 0px;">
			      		 			<li class="opcion"><a href="index.php?seccion=registros" class="opcion">Registros</a></li>
			      		 			<li class="opcion"><a href="index.php?seccion=backup" class="opcion">Copia de Seguridad</a></li>
			      		 		</ul>
			      		 		
			      		 	</div>
			      		
					
			
		</div>
	
		<div style="clear: both;"></div>
		<div style="margin-top: 20px; margin-left: 250px; "> 
		
		<?php require 'cuerpo_admin.php'; ?>
		</div>
	</div>
<?php 
	}
?>

