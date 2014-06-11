			
			<form action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="idpedido" value="<?php echo $idpedido; ?>" />
			<?php
			$ckeditor = new CKEditor();
			$ckeditor->basePath = './ckeditor/';
			$ckeditor->editor('mes', $_POST['mes']);
			$ckeditor->config['height']=200;
			$ckeditor->config['width']=800;
			?>
				<button name="accion" value="email">Enviar por correo</button>
			</form>