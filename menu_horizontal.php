<?php

class Menu_horizontal
{

	/*
	 * No se ha pensado en colocar los niveles que no llegen al final pero
	 * que pasen del nivel crítico a la izquierda y solo aquellos con los subniveles
	 * que los necesiten colocarlos a la dcha. DE MOMENTO, TODOS LOS QUE PASEN EL
	 * NIVEL CRÍTICO SE COLOCAN A LA DERECHA, SI ASÍ ESTA DEFINIDO EN CONFIGURACIÓN
	 */
	
	private $_nivel;
	private $_dir_img;
	private $_link = array();
	
	public function __construct($config)
	{
		$this->_menu = new Mysql;
		$this->_conf=$config;
		$sql = "SHOW TABLES LIKE '%familias';";
		$this->_menu->ejecutar_consulta($sql);
		$this->_nivel = $config->conf['familias_max']+2-$this->_menu->numero_registros;
		$this->_dir_img=$config->conf['imagenes_'];
		$this->_familia();
	}
	 
	 
	private function _familia()
	{
		$menu = new Mysql;
		$menu->ejecutar_consulta("SELECT * FROM familias WHERE eliminado=0 ORDER BY orden, familia;");	
			
		if($menu->numero_registros > 0) {
			echo '<ul class="menu">';			
			for($i=0;$i<$menu->numero_registros;$i++) {
				$matriz = (array) $menu->registros[$i];
				
				if($this->_dir_img) $img = '<img src="'.$this->_dir_img.'/'.$matriz['imagen'].'" class="img"/>';
				//else poner imagen por defecto
				
				$this->_link = $_SERVER['PHP_SELF'] . '?familia=' . $matriz['familia'];
				//$this->_link('familia', $matriz['familia']);
				
				if(!$this->_comprobar_sub($matriz['idfamilia'],'familia',1)) {
					echo '
						<li class="caja_menu">
							<a class="menuitem" style="cursor: default;">
								'. ucfirst($matriz['familia_menu']) . 
								$img . '
							</a>
					';
					$dcha=false;
					if($i >= $this->_nivel) $dcha=true;
					$this->_sub($matriz['idfamilia'],1,$dcha);
				} else {
					echo '
						<li class="caja_menu">
							<a href="'. $this->_link . '" class="menuitem">
								'. ucfirst($matriz['familia_menu']) . 
								$img . '
							</a>
					';	
				}
					
				echo '
					</li>
				';
			}
			
			echo '
				</ul>
			';
		}
	}

	private function _sub($id,$subnivel=1,$dcha=false)
	{ 
		$prefijo="sub";
		$idsub="idfamilia";
		for($sub=1;$sub<$subnivel;$sub++) {
			$idsub="id".$prefijo."familia";
			$prefijo .= "sub";
		}
	
		$tabla = $prefijo."familias";
		$elemento = $prefijo."familia";
		$css = $prefijo."menu";
		if($dcha) {
			$derecha="_derecha";
			if($prefijo=="sub") $css = "submenu";
			else $css .= $derecha;			
		}
		
		$subsubnivel=$subnivel+1;
	
		${$prefijo} = new Mysql;
		//var_dump(${$prefijo});
		${$prefijo}->ejecutar_consulta("SELECT * FROM $tabla WHERE eliminado=0 AND $idsub=$id AND $elemento<>'0' ORDER BY orden, $elemento;");	
		//var_dump(${$prefijo}->registros);
		if(${$prefijo}->numero_registros > 0) {			
			echo '<ul class="'.$css.'">';			
			for($i=0;$i<${$prefijo}->numero_registros;$i++) {
				$matriz = (array) ${$prefijo}->registros[$i];
				
				$pos = stripos($this->_link, $elemento);
				if($pos!==false) $this->_link=substr($this->_link,0,$pos-1);
				
				if($this->_comprobar_sub($matriz['id'.$elemento],$elemento,$subsubnivel)) { //si es true, quiere decir que ponemos link porque no hay subnivel
					$link = $this->_link . "&$elemento={$matriz[$elemento]}";
					echo '
						<li class="submenuitem'.$derecha.'">
							<a href="'. $link . '">
								'. ucfirst($matriz[$elemento.'_menu']) . '
							</a>
					';
				} else {
					$this->_link .= "&$elemento={$matriz[$elemento]}";
					echo '
						<li class="submenuitem'.$derecha.'">
							<a  style="cursor: default;">
								'. ucfirst($matriz[$elemento.'_menu']) . '
							</a>
					';
					if(($this->_menu->numero_registros-1)>$subnivel) $this->_sub($matriz['id'.$elemento],$subsubnivel,$dcha);
				}
				
				echo '
					</li>
				';
			}
			echo '
				</ul>
			';
		} 
	}
	
	private function _comprobar_sub($id,$elemento,$subnivel) 
	{
		if($subnivel<=$this->_menu->numero_registros-1) { // comprobamos que nos pasemos de nivel
			$campo_id="id".$elemento;
			$campo="sub".$elemento;
			$tabla="sub".$elemento."s";
			$sub=new Mysql;
			$sql = "SELECT $campo FROM $tabla WHERE $campo_id=$id;";
			$sub->ejecutar_consulta($sql);
			if($sub->registros[0]->$campo == '0') return true; //no hay subnivel
			else return false; //hay subnivel
		} else return true; //no hay mas subniveles porque nos pasamos de nivel
		
	}

}
