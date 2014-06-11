<div id="aviso_stock" class="mensaje_disponibilidad"  style="
	dislay: block;
	position: fixed;
	top: 50%;
	left: 50%;
	width: 200px;
	height: 150px;
	margin-top: -200px; 
	margin-left: -100px;
	z-index: 10000;
">
<div id="txt_disp"><?php echo $_SESSION['datos_cesta']['aviso']; ?></div>
<form action="" method="post" enctype="multipart/form-data">
	<button type="submit" name="accion" value="cerrar_aviso_stock" id="bt_compras" style="margin-top: 0px; margin-right: 20px;">Cerrar</button>
</form>
</div>
