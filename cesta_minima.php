<table class="cesta_compra" cellpadding="4" >
	<thead>
		<tr align="left">
			<td class="tam_200">Artí­culos</td>
			<td class="tam_40">Uds</td>
			<td class="tam_80">Precio (€)</td>
			<td class="tam_40">Acción</td>
		</tr>
	</thead>
<?php 
	$this->_detalles_cesta(true); 
	$this->_calcular();
?>
	
