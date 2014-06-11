<form method="post" enctype="multipart/form-data" action="">
	<input type="hidden" name="idproducto" value="<?php echo $producto->idproducto; ?>" />
	<button name="parte" value="producto_info" class="admin">Información General</button>
	<button name="parte" value="producto_relacionados" class="admin">Productos Relacionados</button>
	<button name="parte" value="producto_dto_prioritarios" class="admin">Descuentos Prioritarios</button>
	<button name="parte" value="producto_imagenes" class="admin">Imágenes</button>
	<button name="parte" value="producto_combinaciones" class="admin">Combinaciones</button>
	<button name="parte" value="producto_caracteristicas" class="admin">Características</button>
	<button name="parte" value="producto_adjuntos" class="admin">Adjuntos</button>
</form>
