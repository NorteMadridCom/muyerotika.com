<?php

class Editar_categorias
{
	
	private $_consulta_categorias = object;
	public $error = false;
	private $_config = object;
	
	public function __construct($config) 
	{
		$this->_config=$config;
	}
	
	public function formulario_general() 
	{
		echo '
		<form method="post" enctype="multipart/form-data" action="">';
		echo '<table>
					<tr>
						<th width="20" class="titulos">Familia
						<th width="20" class="titulos">Subfamilia
						<th width="20" class="titulos">Subsubfamilia
						<th class="titulos">Acción
					<tr>';
		$categorias = new Seleccion_familias();
		echo '<td>';
		if(!$_POST['idsubsubfamilia']) {
			echo '<button name="accion" value="Nuevo"  class="admin"><img src="../img/nuevo.png" height="16" /></button>';			
		}
		if($_POST['idfamilia']) {
			echo '
					<button name="accion" value="Editar"  class="admin"><img src="../img/editar.png" height="16" alt="Editar" /></button>
					<button name="accion" value="Eliminar"  class="admin"><img src="../img/eliminar.png" height="16" alt="Eliminar" /></button>
			';
		}
		if(!$_POST['idfamilia']) {
			echo '<button name="accion" value="Ordenar_familia" class="admin"><img src="../img/ordenar.png" height="16" alt="Ordenar" /></button>';
		} elseif(!$_POST['idsubfamilia']) {
			echo '<button name="accion" value="Ordenar_subfamilia" class="admin"><img src="../img/ordenar.png" height="16" alt="Ordenar" /></button>';
		} elseif(!$_POST['idsubsubfamilia']) {
			echo '<button name="accion" value="Ordenar_subsubfamilia" class="admin"><img src="../img/ordenar.png" height="16" alt="Ordenar" /></button>';
		}
		echo '</table></form>';
	}
	
	public function formulario_edicion($valores) 
	{
		
		//es el mismo para edicion que para nuevo, hemos de tener claro
		//que si estamso en familia no ponemos imagen ni donde pertence
		//que si estamso en sub's entonces si ponemos imagen y a quien pertence
		//hay que ver lo de categorias=0 y lo de al agragar un elemento lo de ir
		//a sus subs
		
		//var_dump($valores);
		
		
		if($valores['subaccion']=='Terminar') {
			
			$this->_editar($valores);
			
		} else {
		
			$pref="";
			if($valores['idsubsubfamilia']) {
				$pref="subsub";
			} elseif($valores['idsubfamilia']) {
				$pref="sub";
			}
			
			if($valores['accion']=='Nuevo') {
				if($valores['idsubfamilia']) {
					$pref="subsub";
				} elseif($valores['idfamilia']) {
					$pref="sub";
				}
			}
			
			$campo = $pref.'familia';
			
			if(!$valores[$campo]) {
				//para que no me arrastre otros valores
				unset($valores['descripcion'],$valores['imagen']);
			}
			
			if(!$valores['imagen']) {
				$valores['imagen'] = './img/defecto.png';
			} else {
				$valores['imagen'] = $this->_config->conf['imagenes_'.$pref]. '/' . $valores['imagen'];
			}
			
			echo '
				<form method="post" enctype="multipart/form-data" action="">
					<input type="hidden" name="accion" value="'.$valores['accion'].'" />
					<input type="hidden" name="campo" value="'.$campo.'" />
					<input type="hidden" name="idfamilia" value="'.$valores['idfamilia'].'" />
					<input type="hidden" name="idsubfamilia" value="'.$valores['idsubfamilia'].'" />
					<input type="hidden" name="idsubsubfamilia" value="'.$valores['idsubsubfamilia'].'" />
					<table>
						<tr>
							<th class="titulos" style="text-align: left;">'.ucfirst($campo).'
							<th class="titulos" style="text-align: left;">'.ucfirst($campo).'_menu
						<tr>
							<td><input  class="admin_caja" type="text" name="'.$campo.'" value="'.$valores[$campo].'" size="10" maxlength="100" pattern="[a-zA-Z0-9]+" required autofocus/>
							<td><input  class="admin_caja" type="text" name="'.$campo.'_menu" value="'.$valores[$campo.'_menu'].'" size="20" maxlength="100" required/>
						<tr>
							<th colspan="2" class="titulos" style="text-align: left; padding-top: 30px;">Descripción
						<tr>
							<td colspan="2">
							<textarea name="descripcion" rows="2" cols="44" class="admin_caja" style="height: 80px;">'.$valores['descripcion'].'</textarea>
						<tr>
							<th colspan="2" class="titulos" style="text-align: left; padding-top: 30px;">Imagen
						<tr>
							<td colspan="2"><img src="'.$valores['imagen'].'" width="" height="" />
						<tr>
							<td colspan="2"><input type="file" name="imagen" size="18"  class="admin"/>
						<tr>
							<td colspan="2">					
								<input type="submit" name="subaccion" value="Terminar"  class="admin"/>
								<input type="reset" name="subaccion" value="Cancelar"  class="admin"/>
					</table>
				</form>
			';
			
		}
		
	}
	
	private function _editar($valores)
	{

		$prefijo = "";
		$prefijo_ant = "";
		if($valores['campo']=='subsubfamilia') {
			$prefijo = 'subsub';
			$prefijo_ant = 'sub';
		} elseif($valores['campo']=='subfamilia') {
			$prefijo = 'sub';
		}
		//las imágenes->si hay la sustituyo, el nombre ha de ser el mismo, sino no toco
		if($_FILES['imagen']['name']) {
			$imagen = new Subir_imagen($_FILES['imagen'], $this->_config->conf['imagenes_'.$prefijo], $valores[$prefijo.'familia'], $this->_config->conf['alto_img_'.$prefijo],  $this->_config->conf['ancho_img_'.$prefijo], $proporcional = true, $ext_salida = NULL, $tamano_max = NULL, $ext_mime = array());
		}
		
		if($imagen->error !== true) {
			
			if($valores['accion']=='Editar') {
				$sql_editar = "UPDATE ";
			} else {
				$sql_editar = "INSERT INTO ";
			}
			$sql_editar .= $prefijo."familias SET ";
			$sql_editar .= $prefijo."familia = '" . $valores[$prefijo.'familia'] . "', ";
			$sql_editar .= $prefijo."familia_menu = '" . $valores[$prefijo.'familia_menu'] . "', ";
			if($_FILES['imagen']['name']) {
				$sql_editar .= "imagen = '" . strtolower($valores[$prefijo.'familia']) . ".png', ";
			}
			$sql_editar .= "descripcion = '{$valores['descripcion']}'";			
			if($valores['accion']=='Editar') {
				$sql_editar .= " WHERE id".$prefijo."familia = " . $valores['id'.$prefijo.'familia'] . " ";
			} else {
				if($prefijo) {
					$sql_editar .= ", id".$prefijo_ant."familia = " . $valores['id'.$prefijo_ant.'familia'] . " ";
				}
			}
			$sql_editar .= ";";
			//echo $sql_editar;
			
			$editar = new Mysql;
			$this->error = $editar->resultado_consulta($sql_editar);
			$editar->__destruct();
			
		} else {
			
			$this->error = true;
			echo "<h4>{$imagen->error_txt}</h4>";
			
		}
		
		
	}
	
	public function editar($valores) 
	{
		if(!$valores['subaccion']) {
		//realizazo la consulta y se la envio;
			$this->_consulta_categorias = new Mysql;
			
			//var_dump($valores);
			
			if($valores['seleccion']=='Editar Subfamilia') {
				unset($valores['idsubsubfamilia']);
			} elseif($valores['seleccion']=='Editar Familia') {
				unset($valores['idsubsubfamilia'], $valores['idsubfamilia']);
			}
			
			if($valores['idsubsubfamilia']) { 
				$sql_editar_categorias = "
					SELECT 
						* 
					FROM 
						subsubfamilias 
					WHERE 
						idsubsubfamilia={$valores['idsubsubfamilia']} 
						AND eliminado = 0
				;";
			} elseif($valores['idsubfamilia']) { 
				$sql_editar_categorias = "
					SELECT 
						* 
					FROM 
						subfamilias 
					WHERE 
						idsubfamilia={$valores['idsubfamilia']} AND 
						eliminado = 0
				;";
			} elseif($valores['idfamilia']) { 
				$sql_editar_categorias = "
					SELECT 
						* 
					FROM 
						familias 
					WHERE 
						idfamilia={$valores['idfamilia']} AND 
						eliminado = 0
					;";
			} elseif($valores['accion']=='Nuevo') { 
				$this->formulario_edicion($valores);
			} else {
				echo 'Debe seleccionar una categoria para poder editar. Volver.';
			}
			
			if($sql_editar_categorias) {
				$this->_consulta_categorias->ejecutar_consulta($sql_editar_categorias);
				$this->_consulta_categorias->registros[0]->accion = $valores['accion'];
				if($valores['accion']=='Editar') {
					if($this->_consulta_categorias->registros[0]->subsubfamilia == '0' && !$valores['seleccion']) {
						//miro a ver si sub es cero tb
						$sql_subfam_cero = "SELECT * FROM subfamilias WHERE idsubfamilia = {$this->_consulta_categorias->registros[0]->idsubfamilia} AND eliminado = 0;";
						$subfam_cero = new Mysql;
						$subfam_cero->ejecutar_consulta($sql_subfam_cero);
						$sql_fam_cero = "SELECT * FROM familias WHERE idfamilia = {$subfam_cero->registros[0]->idfamilia} AND eliminado = 0;";
						$fam_cero = new Mysql;
						$fam_cero->ejecutar_consulta($sql_fam_cero);
						echo '<form method="post" enctype="multipart/form-data" action="">
								<input type="hidden" name="accion" value="'. $valores['accion'] .'">
								<input type="hidden" name="idfamilia" value="'. $fam_cero->registros[0]->idfamilia .'">
								<input type="hidden" name="idsubfamilia" value="'. $subfam_cero->registros[0]->idsubfamilia .'">
								<input type="hidden" name="idsubsubfamilia" value="'. $this->_consulta_categorias->registros[0]->idsubsubfamilia .'">';
						echo '<table>
									<tr>
										<th>Familia
										<th>Subfamilia
										<th>Subsubfamilia
									<tr>';
						
						if($subfam_cero->registros[0]->subfamilia == '0') {
							//NO es posible editar la subsubfamilia porque la subfamilia es cero => la subsub ha de ser cero
							//solo podemos editar la familia o la subfamilia, dar a elegir		
							echo '
											<td><input type="text" name="familia" value="'. $fam_cero->registros[0]->familia .'" size="15" disabled/>
											<td><input type="text" name="subfamilia" value="'. $subfam_cero->registros[0]->subfamilia .'" size="15" disabled/>
											<td><input type="text" name="subsubfamilia" value="0" size="15" disabled/>
										<tr>
											<td><input type="submit" name="seleccion" value="Editar Familia" size="15" />
											<td><input type="submit" name="seleccion" value="Editar Subfamilia" size="15" />
											<td><input type="submit" name="seleccion" value="Editar Subsubfamilia" size="15" disabled/>
							';
							echo '</form>';
						} else {
							//solo subsub es 0 elegir - sub o subsub
							echo '
											<td><input type="text" name="familia" value="'. $fam_cero->registros[0]->familia .'" size="15" disabled/>
											<td><input type="text" name="subfamilia" value="'. $subfam_cero->registros[0]->subfamilia .'" size="15" disabled/>
											<td><input type="text" name="subsubfamilia" value="0" size="15" disabled/>
										<tr>
											<td><input type="submit" name="seleccion" value="Editar Familia" size="15" disabled />
											<td><input type="submit" name="seleccion" value="Editar Subfamilia" size="15" />
											<td><input type="submit" name="seleccion" value="Editar Subsubfamilia" size="15" />
							';				
						}
						
						echo '</table></form>';
					} else {
						$this->formulario_edicion((array) $this->_consulta_categorias->registros[0]);
					}
				} else {
					//miramso los siguientes, si el siguiente es 0 no puedo poner nuevo
					//si he seleccionado idfamilia y lo sub es dif de cero puedo añladir, sino solo puedo editar
					//al igual que con la sub, si la subsub es cero
					
					//si eligo una familia -> que añado una sub simpre y cuando no exista una sub = 0
					//me llevo el idfamilia y se lo pongo a la sub en el formulario de edicion.
					if(!($this->_consulta_categorias->registros[0]->subsubfamilia == '0' || $this->_consulta_categorias->registros[0]->subfamilia == '0')) {
						$this->formulario_edicion((array) $this->_consulta_categorias->registros[0]);
					} else {
						echo "No se puede añadir uno nuevo porque las hay subsecciones con Cero, edítelas primero.";
					}
				}
			}
		} else {
			$this->formulario_edicion($valores);
		}

	}
	
	public function eliminar($valores) //en un principio $_POST
	{
		//es una cadena: producto->subsub->sub->fam
		
		if(!$valores['subaccion']) {
		
			if($valores['idsubsubfamilia']) {
				$campo='SUBSUBFAMILIA';
				$sql_info = "SELECT subsubfamilia_menu FROM subsubfamilias WHERE idsubfamilia={$valores['idsubsubfamilia']} AND eliminado = 0;";
			} elseif($valores['idsubfamilia']) {
				$campo='SUBFAMILIA';
				$sql_info = "SELECT subfamilia_menu FROM subfamilias WHERE idsubfamilia={$valores['idsubfamilia']} AND eliminado = 0;";
			} elseif($valores['idfamilia']) {
				$campo='FAMILIA';
				$sql_info = "SELECT familia_menu FROM familias WHERE idfamilia={$valores['idfamilia']} AND eliminado = 0;";
			} else {
				echo 'Debemos elegir al menos una categoria para poder eliminar. Volver.';
			}
			
			if($sql_info) {
				$info = new Mysql;
				$info->ejecutar_consulta($sql_info);
			}
			
			echo '
				<div style="color: red; ">
					<h3>¡ATENCIÓN! SE DISPONE A ELIMINAR LA '.$campo.' "<u><b>'
					.strtoupper($info->registros[0]->familia_menu)
					.strtoupper($info->registros[0]->subfamilia_menu)
					.strtoupper($info->registros[0]->subsubfamilia_menu)
					.'"</b></u></h3>
					<form method="post" enctype="multipart/form-data" action="">
						<input type="hidden" name="accion" value="'. $valores['accion'] .'">
						<input type="hidden" name="idfamilia" value="'. $valores['idfamilia'] .'">
						<input type="hidden" name="idsubfamilia" value="'. $valores['idsubfamilia'] .'">
						<input type="hidden" name="idsubsubfamilia" value="'. $valores['idsubsubfamilia'] .'">
						<input type="submit" name="subaccion" value="Si" class="admin">
						<input type="submit" name="subaccion" value="No" class="admin">
					</form>
				</div>
			';
			
		} elseif($valores['subaccion']=='Si') {
		
			$this->_eliminar($valores);
			echo '<center><h4>Operación realizada con éxito.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>'; 
			
		} else {
			
			echo '<center><h4>No se ha realizado ninguna acción.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
			
		}
		
	}
	
	private function _eliminar($valores) 
	{
		$this->_consulta_categorias = new Mysql;
				
		if($valores['idsubsubfamilia']) {
			$this->_eliminar_subsubfamilia($valores['idsubsubfamilia']);
		} elseif($valores['idsubfamilia']) {
			$this->_eliminar_subfamilia($valores['idsubfamilia']);
		} elseif($valores['idfamilia']) {
			$this->_eliminar_familia($valores['idfamilia']);
		} else {
			echo 'Debemos elegir al menos una categoria para poder elmiminar. Volver.';
		}
		
		$this->_consulta_categorias->__destruct();
	}
	
	private function _eliminar_producto($idsubsubfamilia)
	{
		//ponemos a NULL el subsub del producto
		$sql_productos = "UPDATE productos SET idsubsubfamilia = NULL WHERE idsubsubfamilia = $idsubsubfamilia AND eliminado = 0;";
		if(!$this->_consulta_categorias->resultado_consulta($sql_productos)) {
			$this->error = true;
		}
		
	}
		
	private function _eliminar_subsubfamilia($idsubsubfamilia) 
	{
		//ponemos eliminado = 1
		$sql_subsub = "UPDATE subsubfamilias SET eliminado = 1 WHERE idsubsubfamilia = $idsubsubfamilia AND eliminado = 0;";
		if(!$this->_consulta_categorias->resultado_consulta($sql_subsub)) {
			$this->error = true;
		} else {
			$this->_eliminar_producto($idsubsubfamilia);
		}
		
	}
	
	private function _eliminar_subfamilia($idsubfamilia) 
	{
		//ponemos eliminado = 1
		$sql_sub = "UPDATE subfamilias SET eliminado = 1 WHERE idsubfamilia = $idsubfamilia AND eliminado = 0;";
		if(!$this->_consulta_categorias->resultado_consulta($sql_sub)) {
			$this->error = true;
		} else {
			$sql_subsub = "SELECT idsubsubfamilia FROM subsubfamilias WHERE idsubfamilia = $idsubfamilia AND eliminado = 0;";
			$this->_consulta_categorias->ejecutar_consulta($sql_subsub);
			if($this->_consulta_categorias->numero_registros > 0) {
				foreach ($this->_consulta_categorias->registros as $objeto_resultado) {
					if($objeto_resultado->idsubsubfamilia) {
						$this->_eliminar_subsubfamilia($objeto_resultado->idsubsubfamilia);
					}
				}
			}
		}
		
	}
	
	private function _eliminar_familia($idfamilia) 
	{
		//ponemos eliminado = 1
		$sql_fam = "UPDATE familias SET eliminado = 1 WHERE idfamilia = $idfamilia AND eliminado = 0;";
		if(!$this->_consulta_categorias->resultado_consulta($sql_fam)) {
			$this->error = true;
		} else {
			$sql_sub = "SELECT idsubfamilia FROM subfamilias WHERE idfamilia = $idfamilia AND eliminado = 0;";
			$this->_consulta_categorias->ejecutar_consulta($sql_sub);
			if($this->_consulta_categorias->numero_registros > 0) {
				foreach ($this->_consulta_categorias->registros as $objeto_resultado) {
					if($objeto_resultado->idsubfamilia) {
						$this->_eliminar_subfamilia($objeto_resultado->idsubfamilia);
					}
				}
			}
		}
		
	}
	
}
