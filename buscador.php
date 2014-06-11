<?php

require 'buscador_texto.php';
require 'buscador_campos.php';
require 'buscador_categorias.php';

class Buscador_general
{
	
	public $error = false;
	
	public $sql;
	public $registros = array();
	public $numero_registros;

	public function formulario_busqueda($opciones=array('texto','campos','categorias')) 
	{
		echo '<form action="" method="post" enctype="multipart/form-data">';
		
		if(is_array($_POST)) {
			foreach($_POST as $nom => $val) {
				echo '<input type="hidden" name="'.$nom.'" value="'.$val.'" />';
			}
		}
		
		if(in_array('texto', $opciones)) echo '<p class="titulos">Buscar:<input type="text" name="buscar_txt" size="60" maxlength="60" placeholder="Buscar productos" autofocus  class="admin_caja"><br>';
		
		
		if(in_array('campos', $opciones)) 
			echo '
				<br>Ref: <input type="text" name="ref" size="5" maxlength="5" placeholder="Nº de Id"  class="admin_caja"> 
				Id Web: <input type="text" name="idproducto_buscar" size="5" maxlength="5"  class="admin_caja"><br>';
		
		if(in_array('categorias', $opciones)) { 
			echo '<br>Familias: ';
			$categorias = new Seleccion_familias();
			echo '<br>';
			echo '<br>Marcas: ';
			$fabricante = new Seleccion_fabricantes();
			echo '<br>';
		}
		
		echo '<br><input type="submit" name="buscar" value="Buscar"  class="admin"/>
			</form>';
	}
		
	public function sql() 
	{
		if($_POST['buscar_txt']) {
			$_POST['buscar_txt'] = str_ireplace('loreal',"l'oreal",$_POST['buscar_txt']);
			$a_quitar=array ('á','é','í','ó','ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ü', 'Ü', "'");
			$texto=str_ireplace($a_quitar,'%',$_POST['buscar_txt']);
			$buscar = new Buscador_texto($texto);
		} elseif($_POST['idproducto_diakros'] || $_POST['idproducto_buscar']) {
			$campos['idproducto_diakros']=$_POST['idproducto_diakros'];
			$campos['idproducto']=$_POST['idproducto_buscar'];
			$buscar = new Buscador_campos($campos);
		} elseif($_POST['idsubsubfamilia'] || $_POST['idsubfamilia'] || $_POST['idfamilia'] || $_POST['idfabricante'] || $_POST['idlinea']) {
			$buscar = new Buscador_categorias();
		}
		
		if($buscar->error === false) {
			$this->sql = $buscar->sql;
		}
	}
	
	public function resultados() 
	{
		$this->sql();
		//echo $this->sql;
		if($this->sql && $this->error===false) {
			$res = new Mysql;
			$this->error = !$res->ejecutar_consulta($this->sql);
			$this->registros = $res->registros;
			$this->numero_registros = $res->numero_registros;
			$res->__destruct();
		}
	}
	
	public function __destruct() {}
	
	
}
