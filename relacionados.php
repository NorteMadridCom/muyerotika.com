
<?php

class relacionados
{
	public function __construct($idproducto) 
	{
		//----------------------------------------------------------------	
			//poner tabla sdm
		//$relacionados= new Relacionados();
		
		echo '
		<FIELDSET style= "border-radius: 10px;">
		<LEGEND>Articulos Relacionados</LEGEND>
			<table>
				<tr>
					<th width="10">id
					<th width="60">Nombre 
					<th>Acción
				<tr>
					<form method="post" enctype="multipart/form-data" action="">
						<td><input type="text" name="id_diakros_bus" value="" size="10" maxlength="100" pattern="[a-zA-Z0-9_]+"  />
						<td><input type="text" name="nombre_producto_bus" value="" size="60" maxlength="200" />
					';//<td><button name="accion" value="nueva_relacion" id="dialog-link" ><img src="./img/buscar.png" height="16" /></button>
				echo'	<td><button name="accion" value="busca_relacion"><img src="./img/buscar.png" height="16" /></button>
							<td><input type="hidden" name="idproducto_ppal" value="' . $_POST['idproducto'] . '" />
							<input type="hidden" name="idproducto" value="' . $_POST['idproducto'] . '" />					
					</form>
		';
		$sql_relacionados = "
			SELECT 
				p.producto_nombre,p.ref, p.idproducto,r.idproducto_ppal
			FROM 
				productos p, productos_relacionados r
			WHERE 
				r.idproducto_relacionado = p.idproducto AND 
				r.idproducto_ppal = (select idproducto from productos where idproducto = '$idproducto') 
			ORDER BY 
				r.orden,p.producto_nombre;
		";

		$relacionados = new Mysql();
		$this->error = !$relacionados->ejecutar_consulta($sql_relacionados);
		if($this->error !== true) {
			foreach($relacionados->registros as $relacionado) {
				echo '
				<tr>
						<form method="post" enctype="multipart/form-data" action="">
										
							<td><input type="text" name="ref" value="'.$relacionado->ref.'" size="10" maxlength="10" pattern="[a-zA-Z0-9_]+" />
							<td><input type="text" name="nombre_producto_rel" value="'.$relacionado->producto_nombre.'" size="60" maxlength="200" required/>
							<td><button name="accion" value="quita_relacion"><img src="./img/eliminar.png" height="16" /></button>	
							<td><input type="hidden" name="idproducto_rel" value="' . $relacionado->idproducto . '" />
							<td><input type="hidden" name="idproducto" value="' . $_POST['idproducto'] . '" />
							<td><input type="hidden" name="idproducto_ppal" value="' . $_POST['idproducto'] . '" />
							
							</form>
				';		
			}
		}
		echo '
			<tr>
			<th width="10">Ordenar
			
			<form method="post" enctype="multipart/form-data" action="">
				<td><button name="accion" value="ordena_relacion"><img src="./img/ordenar.png" height="16" /></button>
				<td><input type="hidden" name="idproducto" value="' . $_POST['idproducto'] . '" />
				<td><input type="hidden" name="idproducto_ppal" value="' . $_POST['idproducto'] . '" />
			</form>
				<td colspan="3">
					';
		echo '</table>
			</FIELDSET>';

		
	}
	
	//estas no se usan pero las dejo
	public function elimina_relacionado($id_ppal, $id_relacionado)
	{
		$sql_elimina_relacionados = "DELETE FROM productos_relacionados
WHERE idproducto_ppal= '$id_ppal'
and idproducto_relacionado = '$id_relacionado';";

		$relacionados = new Mysql();
		$relacionados->ejecutar_consulta($sql_elimina_relacionados);
		

	}
	public function busca_producto()
	{
		
	}
	public function anade_relacionado()
	{
		
	}
}//de la clase
