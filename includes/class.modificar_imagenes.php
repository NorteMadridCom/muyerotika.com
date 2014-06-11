<?php

//estoy en rotar

/**************************************************
Creo que se puede englobar todo en un solo objeto
con sus diferentes funciones, pero no es prioritario
**************************************************/

class Crear_imagen
{

	//el archivo de imagen	
	public $imagen;
	
	//características de la imagen
	public $mime;
	public $ancho;
	public $alto;
	
	public $error;
	
	public function __construct($imagen) 
	{
		$prop = getimagesize($imagen);
		
		switch($prop['mime']){
      	case "image/jpeg":
				$this->imagen = imagecreatefromjpeg($imagen); //jpeg file
				break;
        	case "image/gif":
            $this->imagen = imagecreatefromgif($imagen); //gif file
	      	break;
	      case "image/png":
				$this->imagen = imagecreatefrompng($imagen); //png file
	      	break;
	    	default:
				$this->imagen = false;
	    		break;
		}
		
		$this->ancho = $prop[0];
		$this->alto = $prop[1];
		$this->mime = $prop['mime'];
		
		if(!$this->imagen || !$this->ancho || !$this->alto || !$this->mime ) {
			$this->error = true;
		}
		
	}
	
	public function __destruct() {}
}



class Transpariencia
{
	public $error = false;
	public $imagen;
	
	public function __construct($imagen, $transpariencia = 50)
	{

		$this->imagen = new Crear_imagen($imagen);
		imagealphablending($this->imagen->imagen,false);
		imagesavealpha($this->imagen->imagen,true);

		for($x=0;$x<$this->imagen->ancho;$x++) {
			for($y=0;$y<$this->imagen->alto;$y++) {
				$color_px = imagecolorat($this->imagen->imagen, $x, $y);
				$color_tran = imagecolorsforindex($this->imagen->imagen, $color_px);
				imagesetpixel($this->imagen->imagen, $x, $y,imagecolorallocatealpha($this->imagen->imagen, $color_tran['red'], $color_tran['green'], $color_tran['blue'], $transpariencia));
			}
		}
		
		if(!imagepng($this->imagen->imagen,$imagen)) {
			$this->error = true;
			imagedestroy($this->imagen->imagen);
			$this->imagen->__destruct();
		}
		
	}
	
	public function __destruct() {}
	
}

class Rotar
{
	
	
	private function _rotar() 
	{		
		$this->_marca = new Crear_imagen($this->_marca);
		if($this->_rotacion > 0 && $this->_rotacion < 360 && !$this->_marca->error) {
			imagerotate($this->_marca->imagen, $rotacion, -1);
			imagealphablending($this->_marca->imagen, true);
			imagesavealpha($this->_marca->imagen, true);
		} else {
			$this->error = true;
		}
	}
}

class Marca_agua
{

	/***********************************************************
	Esta clase simplemente cogerá dos imágenes y las fusionará
	si se desea, a la marca de agua se le aplicará una
	transpararencia llamando a la class Transpariencia.
	Es importante tener en cuenta el orden de
	las imagenes, la imagen_base es sobre la que se pone la
	que se le pone la imagen_marca.
	La colocación es en el centro y la rotación también es desde
	el centro.
	Se puede poner una marca de agua de imagen o
	bien de texto ($tipo_marca='texto' o 'imagen'
	***********************************************************/
	
	public $error = false;
	
	//imagen sobre la que ponemos la marca de agua
	//salida preferible en .pmg
	private $_base;
	
	//imagen que se imprime por encima
	//obligatorio en .png
	private $_marca;
	
	//valor de transpariencia de la imagen de 0-99 %
	//se calcula el valor real 
	private $_transp;
	
	//Podemos rotar la imagen para dar la sensación de sello
	//de 0 a 360 grados en sentido antihorario
	private $_rotacion;
	
	
	public function __construct($imagen_base,$marca_agua,$transp = 50,$rotacion = 0,$tipo_marca = 'imagen') 
	{
		$this->_base = new Crear_imagen($imagen_base);
		if($this->_base->error) {
			$this->error = true;
		} else {
			//llamar a transp y llamar a rotar
		}	
	}
	
	/*
	private function _transparentar() 
	{
		if($this->transp > 0 && $this->transp < 100) {
			$alfa = intval(127*$this->_transp/100);
			$this->_marca = new Transparencia($marca_agua,$alfa);
			$this->error = $this->_marca->error;
			$this->_marca->__destruct();
		} else {
			$this->error = true;
		}
	}
	

	private function _rotar() 
	{		
		$this->_marca = new Crear_imagen($this->_marca);
		if($this->_rotacion > 0 && $this->_rotacion < 360 && !$this->_marca->error) {
			imagerotate($this->_marca->imagen, $rotacion, -1);
			imagealphablending($this->_marca->imagen, true);
			imagesavealpha($this->_marca->imagen, true);
		} else {
			$this->error = true;
		}
	}	
	*/
	private function _juntar() 
	{
		//calcular el centro
		//$x = intval(($this->_base->ancho - $this->_marca_ancho)/2);
		//hay que mirar si la marca es mayor que la imagen, o bien redimensionarla o bien cortarla
		//en este caso es fácil.. bueno no, hay que mirar que tamaños tenemos y ver cúal es el 
		//mas desfaborable y luego hacer los calculos del centtro.
		
		//esto me recuerda hay que pasar todos los nombres de archivos por la clase cadenas
		
		//es posible que la clase subir archivo, la partamos y saquemos los calculos de centro
		//y de redimension, total, tambien los usamos aqui
		
		//imagecopymerge($this->_base, $this->_marca, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
	}
	

	public function __destruct() {}

}



class Eliminar_fondo
{
	
	/**************************************************************
	Todas las salidas se realizan en .png por calidad y por soporte
	de transpariencia. Se eliminan los fondos. Se puede elegir si se
	desea elmininar un rango (%) de colores o si se desea salvar un color
	y eliminar el resto. Los colores se definen en una matriz para rgb
	Para dejar solo un color, se define el array(r,g,b) y dirección = true
	***************************************************************/ 
	
	public $error = false;
	private $_margen;
	private $_color = array();
	private $_direccion = false;
	private $_imagen_original;
	
	public function __construct($imagen, $margen = 0, $color=array(255,255,255), $direccion = false) 
	{

		$this->_margen = intval($margen*255/100);
		$this->_color = $color;
		$this->_direccion = $direccion; 
		
		$this->_imagen_original = new Crear_imagen($imagen);
		$this-> _comprobar_valores();
		$this->_alfa($this->_imagen_original->imagen);

		
		if($direccion === false) {
			$this->_generar_transpariencia($this->_imagen_original->imagen ,127);
			if(!imagepng($this->_imagen_original->imagen, $imagen)) {
				$this->error = true;
			}	
		} else {
			$nueva_imagen = imagecreatetruecolor($this->_imagen_original->ancho, $this->_imagen_original->alto);
			$this->_alfa($nueva_imagen);
			$negro =imagecolorallocatealpha($nueva_imagen, 0, 0, 0, 127);	
			imagefill($nueva_imagen, 0, 0, $negro);//tengo la imagen transparente
			$this->_generar_transpariencia($nueva_imagen,0);
			if(!imagepng($nueva_imagen, $imagen)) {
				$this->error = true;
			}	
			imagedestroy($nueva_imagen);
		}		
		imagedestroy($this->_imagen_original->imagen);
		$this->_imagen_original->__destruct;

	}
	
	
	private function _comprobar_valores() 
	{
		if(
			$this->_color[0] > 255 || $this->_color[0] < 0 || 
			$this->_color[1] > 255 || $this->_color[1] < 0 ||
			$this->_color[2] > 255 || $this->_color[2] < 0 ||
			$this->_margen < 0 || $this->_margen > 50  ||
			$this->_imagen_original->ancho > 1200 || $this->_imagen_original->alto > 1200 ||
			$this->_imagen_original->error
		) {
			$this->error = true;
			return;
		}
	}
	
	
	private function _alfa($imagen) 
	{
		imagealphablending($imagen,false);
		imagesavealpha($imagen,true);
	}
	
	
	private function _generar_transpariencia($imagen_destino, $alfa) 
	{

		$col = array('r','g','b');
		foreach($col as $key =>$colo) {
			$$colo = intval($this->_color[$key]);
			$max=$colo."_max";
			$min=$colo."_min";
			$$max = intval($this->_color[$key]+$this->_margen);
			if($$max > 255) { $$max = 255;}
			$$min = intval($this->_color[$key]-$this->_margen);
			if($$min < 0) { $$min = 0;}
		}		
		
		for($x=0;$x<$this->_imagen_original->ancho;$x++) {
			for($y=0;$y<$this->_imagen_original->alto;$y++) {
				$color_px = imagecolorat($this->_imagen_original->imagen, $x, $y);
				$color_tran = imagecolorsforindex($this->_imagen_original->imagen, $color_px);
				if(
					$color_tran['red'] >= ($r_min) &&
					$color_tran['red'] <= ($r_max) &&
					$color_tran['green'] >= ($g_min) &&
					$color_tran['green'] <= ($g_max) &&
					$color_tran['blue'] >= ($b_min) &&
					$color_tran['blue'] <= ($b_max)
				) {
					//echo "concidencia: $x, $y; color: r:{$color_tran['red']} g:{$color_tran['green']} b:{$color_tran['blue']}<br>";
					imagesetpixel($imagen_destino, $x, $y,imagecolorallocatealpha($imagen_destino, $color_tran['red'], $color_tran['green'], $color_tran['blue'], $alfa));
				}
			}
		}	
			
	}

	
}

class Degradado
{
	
}

class Redondeo
{
	
}