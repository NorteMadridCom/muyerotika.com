

	<form action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="idproducto" value="<?php echo $producto->idproducto ?>">
		<input type="hidden" name="parte" value="<?php echo $_POST['parte']; ?>">
		<input type="hidden" name="producto" value="<?php echo $producto->producto ?>">
		<table>
			<tr>
				<td colspan="2" class="titulos">Producto:<br><br><input size="75" type="text" name="producto_nombre" maxlength="255" value="<?php echo  $producto->producto_nombre  ?>" required autofocus class="admin_caja">
				<td class="titulos"><br>Referencia:<br><br><input size="10" type="text" name="ref" maxlength="64" value="<?php echo  $producto->ref;  ?>"  class="admin_caja">
			<tr>
				<td colspan="3" class="titulos" ><br>Nombre a mostrar:<br><br><input size="100" type="text" name="producto_nombre_web" maxlength="255" value="<?php echo $producto->producto_nombre_web  ?>"  class="admin_caja" style="width: 480px;">		
			<tr>
				<td <?php echo  $colorear['idfamilia'] ; ?>  class="titulos"><br>Familia
				<td <?php echo  $colorear['idsubfamilia'] ; ?>  class="titulos"><br>Subfamilia
				<td <?php echo  $colorear['idsubsubfamilia'] ; ?> class="titulos"><br>Subsubfamilia
			<tr>
				<?php $seleccion_familias = new Seleccion_familias(true); ?>
			<tr>
				<td <?php echo $colorear['idfabricante'] ?>class="titulos"> <br>Fabricante: <td <?php echo $colorear['idlinea'] ?>class="titulos"><br> Linea: 
				<td class="titulos"><label>Novedad: </label><input type="checkbox" name="novedad" value="1" <?php echo $checked_novedad ?> />
			<tr>
				<?php $fabricante = new Seleccion_fabricantes(true); ?>		
				<td class="titulos"><label>Profesional: </label><input type="checkbox" name="profesional" value="1" <?php echo $checked_prof ?> />
			<tr>
				<td class="titulos"><br>Precio Tarifa (€)<br><br>
				<td class="titulos"><br>Descuento<br><br>
				<td class="titulos"><br>Iva
			<tr>
				<td class="titulos"><input type="text" size="3" name="precio" value="<?php echo $producto->precio ?>" required  pattern="\d+(\.\d{1,2})?" class="admin_caja"/>
				<td class="titulos"><input type="text" name="dto_producto" value="<?php echo $producto->dto_producto ?>" pattern="\d{1,2}(\.\d{1,2})?"  class="admin_caja">
				<td class="titulos"><?php $iva->poner_combo(); ?>
			<tr>
				<td class="titulos"><br>Stock<br><br>
				<td class="titulos"><br>Uds.Min.<br><br>
				<td class="titulos">Estado Actual		
			<tr>
				<td><input type="number" name="uds" value="<?php echo $producto->uds ?>"  class="admin_caja">
				<td><input type="number" name="uds_min" value="<?php echo $producto->uds_min ?>"  class="admin_caja">
				<td><?php echo $estado ?>
			<tr>
				<td colspan="3" <?php echo $colorear['descripcion'] ; ?> class="titulos"><br>Descripción: <br><br>
				<?php
					$ckeditor = new CKEditor();
					$ckeditor->basePath = './ckeditor/';
					$ckeditor->editor('descripcion', $producto->descripcion);
					$ckeditor->config['height']=200;
					$ckeditor->config['width']=800;
				?>
			<tr>
				<td colspan="3" class="titulos"><br>Video: <br><br>
				<textarea name="video" rows="5" cols="80" class="admin_caja" style="width: 850px; height: 50px;"><?php echo $producto->video; ?></textarea>
			<tr>
				<td colspan="3" align="center">	<?php require $botonera; ?>
		</table>
	</form>

					
