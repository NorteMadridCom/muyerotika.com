<?php

class Nombre_web
{
	private $_nombre_original;
	private $_nombre_tratar;
	public $error= false;
	
	public function __construct($nombre_original) 
	{
		$this->_nombre_original = trim(strtolower($nombre_original));
	}

	public function tratar_nombre()
	{

		/*****************************************************
		Ponemos la cadena en minusculas, y separamos por palabras
		miramos si tienen caracteres acentuados, ñ, y los sustituimos
		luego miramos caracteres que no esten en la matriz_origen
		y los eliminamos para no tener problemas al guardar
		*****************************************************/
		
		/*******************************************************
		INPORANTE: ES CODIFICACION UTF8, LO QUE VENGA IGUAL
		SINO NO FUNCIONARÁ
		*********************************************************/

		$convertidor = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'S', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'s', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', ' '=>'-', '.'=>'-'
    	);
    	
    	$eliminar='~\?|\$|\*|\^|\\|\/|º|ª|!|"|·|%|&|=|\'|¿|¡|\.|,|;|:~';

    	if(!$this->_nombre_tratar) {
    		$this->_nombre_tratar = strtolower($this->_nombre_original);
    	} else {
    		$this->_nombre_tratar = strtolower($this->_nombre_tratar);
    	}
    	
    	foreach($convertidor as $sustituir => $sustituto) {    	
    		$this->_nombre_tratar = trim(str_replace($sustituir, $sustituto, $this->_nombre_tratar));
			$this->_nombre_tratar = preg_replace($eliminar, '', $this->_nombre_tratar);
    	}

		return $this->_nombre_tratar;
		
	}
	
	public function tratar_nombre_archivo() 
	{

		$partes = preg_split('#(\.[a-z0-9]{3,5})$#', $this->_nombre_original, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		if(count($partes) != 2) {
			$this->error = true;
		} else {
			$ext = $partes[1];
			$this->_nombre_tratar = $partes[0];
			$nombre = $this->tratar_nombre() . $ext;
		}	
		return $nombre;

	}
	
	public function tratar_nombre_imagen() 
	{
		
		/**************************************************************
		Es el caso especial en el que no deseamos devolver la extensión
		porque la pondremos según el tipo de imagen que creemos
		**************************************************************/
		
		$partes = preg_split('#\.[a-z0-9]{3,5}$#', $this->_nombre_original, -1, PREG_SPLIT_NO_EMPTY);
		if(count($partes) != 1) {
			$this->error = true;
		} else {
			$this->_nombre_tratar = $partes[0];
			$nombre = $this->tratar_nombre();
		}	
		return $nombre;
	}
	
}

