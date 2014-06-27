<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="imagen" value="'.$imagen['imagen'].'" />
	<button type="submit" name="galeria_accion" value="eliminar" style="color: red; z-index: 100; font-size: 30px; position: absolute; background-color: rgba(0,0,0,0.5); border:none; width: 35px; height: 35px;" >X</button>
	<img <?php echo $src.$ancho.$alto; ?> />
</form>