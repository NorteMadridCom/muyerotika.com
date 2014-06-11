<?php

class Submenu 
{
	protected $_objeto_menu = object;
	protected $_config = object;
	protected $_tipo_menu;
	public $div_filas;
	public $div_cuadros;
	
	public function __construct($config,$objeto_menu,$div_cuadros,$j=0)
	{
		$this->_config=$config;
		$this->_objeto_menu=$objeto_menu;
		$this->div_cuadros='<div id="'.$div_cuadros.'">';
		$this->_clase_menu($j);
	}
	
	private function _clase_menu($j=0) 
	{
		/*************************************************************************
		funcion en la que sabemos si es submenu o subsubmenu
		*************************************************************************/
		unset($this->_tipo_menu);
		//var_dump($this->_objeto_menu->registros[0]);
		if($this->_objeto_menu->registros[$j]->idproducto) {
			$this->_tipo_menu='prod';
		} else {
			$this->_tipo_menu='';
			if($this->_objeto_menu->registros[$j]->idsubsubfamilia) {
				$this->_tipo_menu = 'subsub';
			} elseif($this->_objeto_menu->registros[$j]->idsubfamilia) {
				$this->_tipo_menu = 'sub';
			} elseif($this->_objeto_menu->registros[$j]->idlinea) {
				$this->_tipo_menu = 'linea';
			}
		}
	}
	
	public function poner_submenu ($j=0)
	{
		/*************************************************************************
		colocamos los elemtos en orden, fucion de posicionamiento
		en vez de una tabla usamos un div, 
		que lo tememos definido en la funcion
		__construct y el resto lo hace el css
		*************************************************************************/
		$celdas=count($this->_objeto_menu->registros);
		for($i=$j;$i<($celdas+$j);$i++) {
			$this->caja_submenu($i);
		}
	}
	
	public function caja_submenu($i)
	{
		/*************************************************************************
		dibujamos el elmento del submenu en si
		*************************************************************************/
		unset($link);
		$nombre_menu = $this->_objeto_menu->registros[$i]->familia;
		$nombre_menu_mostrar = $this->_objeto_menu->registros[$i]->familia_menu;
		if($this->_tipo_menu=='subsub') {
			$nombre_menu = $this->_objeto_menu->registros[$i]->subsubfamilia;
			$nombre_menu_mostrar = $this->_objeto_menu->registros[$i]->subsubfamilia_menu;
		} elseif($this->_tipo_menu=='sub') {
			$nombre_menu = $this->_objeto_menu->registros[$i]->subfamilia;
			$nombre_menu_mostrar = $this->_objeto_menu->registros[$i]->subfamilia_menu;
		} elseif($this->_tipo_menu=='linea') {
			$nombre_menu = $this->_objeto_menu->registros[$i]->linea;
			$nombre_menu_mostrar = $this->_objeto_menu->registros[$i]->linea_menu;
		}
		
		$nexo = '?';
		if(strpos($_SERVER['REQUEST_URI'],$nexo)) {
			$nexo = '&';
		}
		$cat="";
		if($this->_tipo_menu != 'linea') {
			$cat='familia';
		}
		$link = $_SERVER['REQUEST_URI'].$nexo.$this->_tipo_menu.$cat.'='.$nombre_menu;
		
		$imagen = './img/defecto.png';
		if($this->_objeto_menu->registros[$i]->imagen) {
			$imagen=$this->_config->conf['imagenes_'.$this->_tipo_menu].'/'.$this->_objeto_menu->registros[$i]->imagen;
		}
				
		unset($estilo);		
		if(strlen($nombre_menu_mostrar) > 80) {
			$estilo = 'style="font-size: 16px"';
		}	
				
		echo 
			$this->div_cuadros .
			'	<a href="'.$link.'">
					<div class="bt_producto_categorias" ' . $estilo . '>'.ucfirst($nombre_menu_mostrar).'</div>
				</a>
			</div>
		';	

	}

}

class Productos extends Submenu 
{
	public function caja_submenu($i)
	{
		/*************************************************************************
		dibujamos el producto como submenu
		cuando hacemos clic
		nos vamos a producto en si
		*************************************************************************/
		unset($link);
		$idproducto = $this->_objeto_menu->registros[$i]->idproducto;
		$producto = $this->_objeto_menu->registros[$i]->producto;
		$producto_nombre = $this->_objeto_menu->registros[$i]->producto_nombre;
		$producto_nombre_web = $this->_objeto_menu->registros[$i]->producto_nombre_web;
		$descripcion = $this->_objeto_menu->registros[$i]->descripcion;
		$uds = $this->_objeto_menu->registros[$i]->uds; //IMPORTANTE, HAY QUE VERIUFICAR EL STOCK
		$precio_tarifa = number_format(round($this->_objeto_menu->registros[$i]->precio,2), 2, '.', '');
		$dto_producto = $this->_objeto_menu->registros[$i]->dto_producto;
		$dto_linea = $this->_objeto_menu->registros[$i]->dto_linea;
		
		$dto_pri = new Dto_prioritario($idproducto);
		if(!$dto_pri->dto_pri()) {
			$precio = new Precio_minimo($precio_tarifa,$dto_producto,$dto_linea);
			$precio_min = $precio->precio_min;
		} else {
			$precio_min = $dto_pri->dto_pri()*$precio_tarifa;	
		}
		$precio_min=number_format(round($precio_min,2),2);
		
		$imagen = './img/defecto.png';
		$idiva = $this->_objeto_menu->registros[$i]->idiva;
		if($this->_objeto_menu->registros[$i]->imagen1) {
			$imagen=$this->_config->conf['imagenes_prod'].'/'.$this->_objeto_menu->registros[$i]->imagen1;
		}
		
		$nexo='&';
		if(stripos($_SERVER['REQUEST_URI'], 'index.php?')===false) {
			$nexo='?';
		}
		
		$link = Enlace_get::enlace($_SERVER['REQUEST_URI'].$nexo.'producto='.$producto);
		//$link = $_SERVER['REQUEST_URI'].$nexo.'producto='.$producto;
		
		if(!$producto_nombre_web) $producto_nombre_web = $producto_nombre;
		
		$tamano_letra='12px;';
		if(strlen($producto_nombre_web)>200) {
			$tamano_letra='8px;';
		} elseif(strlen($producto_nombre_web)>100) {
			$tamano_letra='10px;';
		}

		if($uds<1) {
			$poner="Agotado temporalmente";
			$color="color: red";
			$btn="caja_no_anadir";
			$disabled="disabled";
			//$mostrar_reservas= 'onmouseover="mostrar(\'caja_'.$idproducto.'\')" onmouseout="ocultar(\'caja_'.$idproducto.'\')"';
			$mostrar_reservas='<a onclick="mostrar(\'form_'.$idproducto.'\')" class="clik"><span style="cursor: pointer;"><img src="img/ico-comprar-gris.jpg" ></span></a>';
		} else {
			$poner="En stock";
			$color="color: #00aa00";
			$btn="caja_anadir";
			$disabled="";
			$mostrar_reservas='<input id="btn_compra'.$i.'" type="submit" name="anadir_cesta" value="" class="caja_anadir" />';
		}
		if(is_file($imagen)) {
			$imageinfo = getimagesize($imagen);
			if($imageinfo[0]>130 & $imageinfo[1]>130) {
				if($imageinfo[0]<=$imageinfo[1]) $tamano_img=' height="130" ';
				else $tamano_img=' width="130" ';
			} elseif($imageinfo[1]>130) {
				$tamano_img=' height="130" ';
			} elseif($imageinfo[0]>130) {
				$tamano_img=' width="130" ';
			} else {
				$tamano_img="";
			}
		}
		
		echo 
			$this->div_cuadros .
			'<a href="'.$link.'">
					<div id="imagen_menu_producto">
						<img 
							src="'.$imagen.'" 
							alt="'.$producto_nombre_web.'"  
							border="0"
							'.$tamano_img.'
						/>
					</div>
					<div id="titulos_producto" >'.ucwords($producto_nombre_web).'</div>
					
				</a>
				<br>
				<div style="display: block; height: 120px;" >
					<p class="opciones">Uds</p>
					<p class="opciones">Precio</p>
					<p class="opciones">Web</p>
					<p class="opcion_anadir">Añadir</p>
		';
		if(!$disabled) echo '<form action="" method="POST" enctype="multipart/form-data">';
		echo '		
						<input type="hidden" name="idproducto" value="'.$idproducto.'" />
						<input type="hidden" name="idiva" value="'.$idiva.'" />
						<input name="precio" value="'.$precio_tarifa.'" type="hidden" id="precio'.$i.'" />
						<input class="caja_uds" name="uds_compra" value="1" type="text" '.$disabled.' />						
						<spam class="caja_precio">	<strike>'.$precio_tarifa.'</strike></spam>
						<spam class="caja_precio">	'.$precio_min.'</spam>		
						'.$mostrar_reservas.'
		';
		if(!$disabled) echo '</form>';
		echo '
					<div class="caja_stock" id="uds'.$i.'" style="'.$color.'">'.$poner.'</div>
				</div>
			</div>
			
		';		
		
		$disponibilidad = new Disponibilidad($this->_objeto_menu->registros[$i]);
		$disponibilidad->formulario($idproducto);
		
	}

}
	
