<form method="post" enctype="multipart/form-data" action="">
	<input type="hidden" name="idproducto" value="<?php echo $producto->idproducto; ?>" />
	<button name="parte" value="producto_info">Información General</button>
	<button name="parte" value="producto_relacionados">Productos Relacionados</button>
	<button name="parte" value="producto_dto_prioritarios">Descuentos Prioritarios</button>
	<button name="parte" value="producto_imagenes">Imágenes</button>
	<button name="parte" value="producto_combinaciones">Combinaciones</button>
	<button name="parte" value="producto_caracteristicas">Características</button>
	<button name="parte" value="producto_adjuntos">Adjuntos</button>
</form>
