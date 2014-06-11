<?php

class Subir_archivo
{
	public $error = false;
	public $error_txt;
	protected $_nombre;
	
	//es obligatoria no sabemos cual es el raiz
	protected $_carpeta;
	
	//es una matriz del $_FILE[nombre]
	//accedemos a los datos $this->_archivo[name]
	protected $_archivo = array();

	//Damos el tipo de mime que se acepta
	protected $_mime = array();
	
	//extensiones mime conocidas
	//es preferible usar mime
	protected $_ext = array();
	
	//tamaño máximo en bytes
	protected $_max = array();
	
	const SEPARADOR = '/';
	
	public function __construct($archivo, $carpeta, $nombre = NULL, $tamano_max = NULL, $tipo_mime = array(), $ext_mime = array()) 
	{
		//$_FILE['nombre_form'] = $archivo
		$this->_archivo = $archivo;
		//
		if($this->_archivo['error'] == UPLOAD_ERR_OK) {
			$this->_mime = $tipo_mime;
			$this->_ext = $ext_mime;
			$this->_max = $tamano_max;
			$this->_nombre = $this->_archivo['name'];
			if($nombre) {
				$partes = preg_split('#(\.[a-z0-9A-Z]{3,5})$#', $nombre, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
				if(count($partes) == 1) {
					$ext = preg_split('#(\.[a-z0-9A-Z]{3,5})$#', $this->_archivo['name'], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
					$this->_nombre = $nombre . $ext[1];
				} else {
					$this->_nombre = implode('', $partes);
				}
			}
			$this->_carpeta = $carpeta;
			$this->_nombre();
		} else {
			$this->error = true;
			$this->error_txt = '<p>No se ha podido subir el archivo. Inténtelo de nuevo.</p>';
		}

	}
	
	protected function _comprobaciones() 
	{
		
		if(!$this->_carpeta) {	
			$this->error = true;			
			$this->error_txt .= '<p>No es el tipo de archivo permitido o excede el tamaño máximo. Inténtelo de nuevo.</p>';
		}
		
		if($this->_mime) {
			if(!in_array(strstr($this->_archivo['type'], '/', true), $this->_mime)) {
				$this->error = true;			
				$this->error_txt .= '<p>El archivo que se intenta subir no coincide con los permitidos. Elija otro archivo.</p>';
			}
		}
		
		if($this->_ext) {
			if(!in_array(substr(strstr($this->_archivo['type'], '/'),1), $this->_ext)) {
				$this->error = true;			
				$this->error_txt .= '<p>El archivo que se intenta subir no tiene una extension permitida. Elija otro archivo.</p>';
			}
		}
		
		
		if($this->_max && $this->_max<$this->_archivo['size']) {
			$this->error = true;			
			$this->error_txt .= '<p>El archivo es demadiado grande. Elija otro archivo.</p>';
		}
		
		if($this->error === false) {
			$this->_mover();
		}
	
	}
	
	protected function _mover() 
	{
		 if(!move_uploaded_file($this->_archivo['tmp_name'],$this->_carpeta . self::SEPARADOR . $this->_nombre)) {
		 	$this->error = true;
		 	$this->error_txt = '<p>No se puede mover el archivo a: ' . $this->_carpeta . self::SEPARADOR . $this->_nombre . '.</p>';
		 }
	}
	
	protected function _nombre() 
	{
		//require_one 'class.cadenas.php';
		$nombre = new Nombre_web($this->_nombre);
		$this->_nombre = $nombre->tratar_nombre_archivo();
		if($nombre->error === false) {
			$this->_comprobaciones();
		} else {
			$this->error = true;
			$this->error_txt = '<p>No se ha podido subir el archivo. Hay problemas con el nombre o la extensión del archivo.</p>';
		}
	}
	
	public function __destruct() { }
	
}

class Subir_imagen extends Subir_archivo 
{
	//si se necesitasm en $ext_mine se añaden nuevos
	//de momento la librería GD trabaja bien con solo estas createimagefrom___
	protected $_ext = array('jpeg','png','gif');
	protected $_mime = array('image');
	
	//medidas en px
	//sino se especifica se quedan con la medida original
	//si proporcional es falso, aplica las medidas
	//sino realiza el cálculo óptimo
	private $_alto;
	private $_ancho;
	private $_porporcional;
	
	//Es la extensión o el tipo de imagen con la que queremos la
	//imagen de salida desopues de haberla creado. Por degecto ponemos
	//.png por calidad y transparencia, pero se puede colocar en un 
	//principio cualquiera que soporte la lib GD
	private $_ext_salida;
	
	
	public function __construct($archivo, $carpeta, $nombre = NULL, $alto = NULL, $ancho = NULL, $proporcional = true, $ext_salida = NULL, $tamano_max = NULL, $ext_mime = array()) 
	{
		//$_FILE['nombre_form'] = $archivo
		$this->_archivo = $archivo;	
		//var_dump($this->_archivo);	
		if($this->_archivo['error'] == UPLOAD_ERR_OK) {
			
			if($ext_mime) $this->_ext = array_unique(array_merge($this->_ext,$ext_mine));
			
			$this->_max = $tamano_max;
			
			//problema con las extensiones, deberían de ser segun el mine
			$this->_nombre = $this->_archivo['name'];
			if($nombre) $this->_nombre = $nombre;
			
			if($ext_salida) $this->_ext_salida = $ext_salida;
			
			$this->_carpeta = $carpeta;
			$this->_alto = $alto;
			$this->_ancho = $ancho;
			$this->_proporcional = $proporcional;
			$this->_nombre();
		} else {
			$this->error = true;
			$this->error_txt = '<p>No se ha podido subir el archivo. Inténtelo de nuevo.</p>';
		}

	}
	
	protected function _nombre()
	{
		$nombre = new Nombre_web($this->_nombre);
		$this->_nombre = $nombre->tratar_nombre_imagen();
		if($nombre->error === false) {
			$this->_comprobaciones();
		} else {
			$this->error = true;
			$this->error_txt = '<p>No se ha podido subir el archivo. Hay problemas con el nombre o la extensión del archivo.</p>';
		}
	}
	
	protected function _mover() 
	{
		$prop= getimagesize($this->_archivo['tmp_name']);
		$ancho = $prop[0];
		$alto = $prop[1];
		$mime = $prop['mime'];
		$nuevo_ancho = $ancho;
		$nuevo_alto = $alto;
		//echo 'porpocional: '.$this->_proporcional;
		if($this->_ancho && $this->_alto) {		
			//echo 'alto-ancho';	
			if($this->_proporcional === true) {
				//echo 'porp';
				$nuevo_ancho = $ancho/max($ancho/$this->_ancho,$alto/$this->_alto);
				$nuevo_alto = $alto/max($ancho/$this->_ancho,$alto/$this->_alto);
			} else {
				//echo 'no prop';
				$nuevo_ancho = $this->_ancho;
				$nuevo_alto = $this->_alto;
			}
		} elseif($this->_ancho && !$this->_alto) {
			//echo 'ancho';
			if($this->_proporcional === true) {
				$nuevo_ancho = $ancho/($ancho/$this->_ancho);
				$nuevo_alto = $alto/($ancho/$this->_ancho);
			} else {
				$nuevo_ancho = $ancho/($ancho/$this->_ancho);
			}
		} elseif(!$this->_ancho && $this->_alto) {
			//echo 'alto';
			if($this->_proporcional === true) {
				$nuevo_ancho = $ancho/($alto/$this->_alto);
				$nuevo_alto = $alto/($alto/$this->_alto);
			} else {
				$nuevo_alto = $alto/($alto/$this->_alto);
			}
		}
		
		switch($mime){
      	case "image/jpeg":
				$imagen_original = imagecreatefromjpeg($this->_archivo['tmp_name']); //jpeg file
				if(!$this->_ext_salida) $this->_ext_salida = 'jpg';
				break;
        	case "image/gif":
            $imagen_original = imagecreatefromgif($this->_archivo['tmp_name']); //gif file
            if(!$this->_ext_salida) $this->_ext_salida = 'gif';
	      	break;
	      case "image/png":
				$imagen_original = imagecreatefrompng($this->_archivo['tmp_name']); //png file
				if(!$this->_ext_salida) $this->_ext_salida = 'png';
	      	break;
	    	default:
				$imagen_original = false;
				$this->error = true;
	    		break;
		}
		
		$imagen = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
		if(imagecopyresampled($imagen, $imagen_original, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto)) {		
			switch($this->_ext_salida){
	      	case "jpg":
					$archivo_salida = imagejpeg($imagen, $this->_carpeta . parent::SEPARADOR . $this->_nombre . '.' . $this->_ext_salida);
					break;
	        	case "gif":
	            $archivo_salida = imagegif($imagen, $this->_carpeta . parent::SEPARADOR . $this->_nombre . '.' . $this->_ext_salida);
		      	break;
		      case "png":
					$archivo_salida = imagepng($imagen, $this->_carpeta . parent::SEPARADOR . $this->_nombre . '.' . $this->_ext_salida);
		      	break;
		      default:
					$archivo_salida = false;
		      	break;
			}
		}
		imagedestroy($imagen);

		if(!$archivo_salida)	{
			$this->error = true;
			$this->error_txt = '<p>No se ha podido redimensionar la imagen. Inténtelo de nuevo.</p>';
		}
		
	}
	
	/****************************************************************************************************
	queda pendinete de momento el hacer:
	- redimensionar la imagen
	- aplicar el degradado (no se si lo hare)
	- realizar el redondeo (mirar si hay que hacer redondeo_color(jpg) o se puede convertir a png
	- FALTA LA MARCA DE AGUA
	****************************************************************************************************/
	
}
