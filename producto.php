
<?php

class Producto
{
	protected $_objeto_producto = object;
	protected $_config = object;
	protected $imagen1;
	protected $imagen2;
	protected $imagen3;
	protected $imagen1_grande;
	protected $imagen2_grande;
	protected $imagen3_grande;
	
	public function __construct($config,$objeto_producto)
	{
		$this->_config=$config;
		$this->_objeto_producto=$objeto_producto;
		$this->_imagen1 = $this->_config->conf['imagenes_prod'].'/'.$this->_objeto_producto->registros[0]->imagen1;
		$this->_imagen2 = $this->_config->conf['imagenes_prod'].'/'.$this->_objeto_producto->registros[0]->imagen2;
		$this->_imagen3 = $this->_config->conf['imagenes_prod'].'/'.$this->_objeto_producto->registros[0]->imagen3;
		$this->_imagen1_grande = $this->_config->conf['imagenes_prod_grande'].'/'.$this->_objeto_producto->registros[0]->imagen1;
		$this->_imagen2_grande = $this->_config->conf['imagenes_prod_grande'].'/'.$this->_objeto_producto->registros[0]->imagen2;
		$this->_imagen3_grande = $this->_config->conf['imagenes_prod_grande'].'/'.$this->_objeto_producto->registros[0]->imagen3;
		//sacar las tarifas segun el cliente
		
	}
	
	public function poner_producto()
	{
		/*************************************************************************
		dibujamos el producto 
		*************************************************************************/
		unset($link);
		
		$enlaces = explode('&', $_SERVER['QUERY_STRING']);
		for($i=0;$i<count($enlaces);$i++) {
			if(!$enlaces[$i] || preg_match('/^imagen=/',$enlaces[$i])) {
				unset($enlaces[$i]);
			}
		}
		$link = $_SERVER['PHP_SELF'].'?'.implode('&',$enlaces);
		
		
		// ************************la imagen del cuadro ha de ser una imagen seleccionada
		//se me ocurre ponerlo por el get: si hay imagen la pones, sino pones la imagen1
		
		if($_GET['imagen']) {
			$imagen=$this->_config->conf['imagenes_prod_grande'].'/'.$_GET['imagen'];
		} elseif($this->_objeto_producto->registros[0]->imagen1) {
			$imagen=$this->_imagen1_grande;
		} else {
			$imagen = './img/defecto.png';
		}
		
		if(!$this->_objeto_producto->registros[0]->producto_nombre_web) $this->_objeto_producto->registros[0]->producto_nombre_web = $this->_objeto_producto->registros[0]->producto_nombre;
		
		$precio_tarifa = number_format(round($this->_objeto_producto->registros[0]->precio,2), 2, '.', '');
		$dto_producto = $this->_objeto_producto->registros[0]->dto_producto;
		$dto_linea = $this->_objeto_producto->registros[0]->dto_linea;
		/*
		$precio = new Precio_minimo($precio_tarifa,$dto_producto,$dto_linea);
		$precio_min = $precio->precio_min;
		*/
		$dto_pri = new Dto_prioritario($this->_objeto_producto->registros[0]->idproducto);
		if(!$dto_pri->dto_pri()) {
			$precio = new Precio_minimo($precio_tarifa,$dto_producto,$dto_linea);
			$precio_min = $precio->precio_min;
		} else {
			$precio_min = $dto_pri->dto_pri()*$precio_tarifa;	
		}
		$precio_min=number_format(round($precio_min,2),2);
				
		$idiva = $this->_objeto_producto->registros[0]->idiva;
		$idproducto = $this->_objeto_producto->registros[0]->idproducto;
		
		$uds = $this->_objeto_producto->registros[0]->uds;
		if($uds<1) {
			$poner="Agotado temporalmente";
			$color="color: #ff0000";
			$clase_boton='<a onclick="mostrar(\'form_'.$idproducto.'\')" class="clik"><span style="cursor: pointer;"><img src="img/ico-comprar-gris.jpg" ></span></a>';
			//$clase_boton = '<input type="submit" name="anadir_cesta" value="" class="caja_no_anadir" disabled />';
			//$mostrar_reservas= 'onmouseover="mostrar(\'caja_'.$idproducto.'\')" onmouseout="ocultar(\'caja_'.$idproducto.'\')"';
			$disabled=true;
		} else {
			$poner="En stock";
			$color="color: #00aa00";
			$clase_boton = '<input type="submit" name="anadir_cesta" value="" class="caja_anadir" />'; //botnon añadir, el que tenemos
			$mostrar_reservas= "";
			$disabled=false;
		}

		echo '
			<div id="cuadro_producto">
							<div id="oferta"></div>	
							<a href="'.$imagen.'"  onclick="return hs.expand(this)">
								<div id="imagen_menu_producto">
									<img src="'.$imagen.'" alt="'.$this->_objeto_producto->registros[0]->producto_nombre.'"  border="0">
								</div>
								<div id="titulos_producto">Splash lubricante fresa 100 ml</div>
								
							</a>
							<br>
							<p class="opciones">Uds</p>
					<p class="opciones">Precio</p>
					<p class="opciones">Web</p>
					<p class="opcion_anadir">Añadir</p>
				<form action="" method="POST" enctype="multipart/form-data">		
								<input type="hidden" name="idproducto" value="'.$idproducto.'" />
								<input type="hidden" name="idiva" value="'.$idiva.'" />
								<input name="precio" value="'.$precio_tarifa.'" type="hidden" id="precio0" />
								<input class="caja_uds" name="uds_compra" value="1" type="text" />						
								<spam class="caja_precio">	<strike>'.$precio_tarifa.'</strike></spam>
								<spam class="caja_precio">	'.$precio_min.'</spam>			
								<input id="btn_compra0" name="anadir_cesta" value="" class="caja_anadir" type="submit">
				</form>
					<div class="caja_stock" id="uds'.$i.'" style="'.$color.'">'.$poner.'</div>
				</div>
		
			';
			echo '
			<div id="producto_descripcion">
		';
		if($this->_objeto_producto->registros[0]->imagen1) {
			echo '
					<a href="'.$link.'&imagen='.$this->_objeto_producto->registros[0]->imagen1.'">
						<img 
							src="'.$this->_imagen1.'" 
							class="recuadro_img" 
							border="0" 
							alt="'.$this->_objeto_producto->registros[0]->producto_nombre_web.'" 
						/>
					</a>
			';
		}
		if($this->_objeto_producto->registros[0]->imagen2) {
			echo '
					<a href="'.$link.'&imagen='.$this->_objeto_producto->registros[0]->imagen2.'">
						<img 
							src="'.$this->_imagen2.'"
							class="recuadro_img" 
							border="0" 
							alt="'.$this->_objeto_producto->registros[0]->producto_nombre_web.'" 
						/>
					</a>
			';
		}
		if($this->_objeto_producto->registros[0]->imagen3) {
			echo '
					<a href="'.$link.'&imagen='.$this->_objeto_producto->registros[0]->imagen3.'">
						<img 
							src="'.$this->_imagen3.'" 
							class="recuadro_img" 
							border="0" 
							alt="'.$this->_objeto_producto->registros[0]->producto_nombre_web.'" 
						/>
					</a>
			';
		}
		echo '
					<p class="productos"><strong>Descripción:</strong></p>
					<p>'. $this->_objeto_producto->registros[0]->descripcion .'</p>
					<p class="productos"><strong>Producto: </strong>'.$this->_objeto_producto->registros[0]->producto_nombre_web.'</p>
					<p class="productos"><strong>Marca: </strong>'.$this->_objeto_producto->registros[0]->fabricante_menu.'</p>				
				</div>
			';
			
		
		$disponibilidad = new Disponibilidad($this->_objeto_producto->registros[0]);
		$disponibilidad->formulario($idproducto);
		//	</div>
	//	';			
		
		
//---------------------------------------------------------------------			
		//sdm ponemos la visualizacion de los productos relacionados
		
	   //var_dump($this->_objeto_producto->registros[0]);
	   
	   $idproducto = $this->_objeto_producto->registros[0]->idproducto;
	   //var_dump($idproducto);
	   
		$sql_producto="
			select 
				l.dto_linea, p.* 
			from 
				productos p, productos_relacionados r, lineas l  
			where 
				l.idlinea = p.idlinea AND
				r.idproducto_relacionado = p.idproducto
				and r.idproducto_ppal = (select idproducto from productos where idproducto = {$idproducto}) AND
				p.web = 1 
				$where_prod_prof
			ORDER BY
			    r.orden,
				r.nombre_producto
			LIMIT 0,50;";
		
		
		
		//Poner_productos::productos($sql_producto,$this->_config);
		
		$el_producto=new Mysql;
		
		$el_producto->ejecutar_consulta($sql_producto);
		
		if( $el_producto->numero_registros > 0) {
	  
			//si no tiene productos relacionados no se muestra nada 
			array_unshift($el_producto->registros,array());
			unset($el_producto->registros[0]);
	   
			echo '<div id="titulo_relacionados"><b>Artículos Relacionados &nbsp;</b></div>'; 
	  
			echo '<div id= "productos_relacionados">
	  
				<FIELDSET style= "border-radius: 10px; padding: 0px;">';
		
			$menuproductos = new Productos($this->_config,$el_producto,'cuadro_producto',1);
			$menuproductos->poner_submenu(1);
			/*
			$menuproductos = new Productos($this->_config,$el_producto,'cuadro_producto');
			$menuproductos->poner_submenu();
			*/
			echo '		</FIELDSET>
					</div>';
		}
//---------------------------------------------------------------------	
		
		
		
		if($this->_objeto_producto->registros[0]->video) echo $this->_objeto_producto->registros[0]->video; 
		
		//cambio esto para encuadrar el video
		echo '		</div>
		';
	}

}