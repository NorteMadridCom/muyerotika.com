		<!-- MENSAJE DE RESPUESTA-->
		<div id="respuesta_<?php echo $idproducto; ?>" class="mensaje_disponibilidad" style="display: block;">
			<div style="float: right; margin: -20px; 0px 0px 5px; cursor: pointer;"><a onclick="ocultar('respuesta_<?php echo $idproducto; ?>')">X</a></div>
			<div id="txt_disp" ><?php echo $respuesta_mail; ?></div>		
		</div>
