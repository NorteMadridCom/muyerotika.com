<?php

class Barra 
{

	private $_div;
	private $_class;
	private $_separador;
	private $_inicio; //puede ser una imagen
	private $_sep_siempre;
	
	public function __construct($div="",$class="",$inicio='Inicio',$sep_siempre='false',$separador='  >>  ')
	{
		$this->_separador=$separador;
		$this->_div=$div;
		$this->_class=$class;
		$this->_inicio=$inicio;
		$this->_sep_siempre=$sep_siempre; //si es true, lo coloca con el anterior, sino siempre con le posterior
	}
	
	private function _barra_navegacion () 
	{
		unset($barra_navegacion);
		unset($fin_barra);
		if($this->_div) {
			$barra_navegacion='<div id="' . $this->_div . '">';
			$fin_barra='</div>';
		} else {
			$barra_navegacion=''; //inicializo para que no de error posterior
		}
		
		unset($class);
		if($this->_class) {
			$class=' class="' . $this->_class. '" ';
		}
		
		unset($sep_ant);
		unset($sep_pos);
		if($this->_sep_siempre===false) {
			$sep_pos=$this->_separador;
		} else {
			$sep_ant=$this->_separador;
		}
		
		$link=$_SERVER['PHP_SELF'].'?';
		$barra_navegacion .= '<a href="'.$link.'"' . $class . '>' . $this->_inicio . '</a>' . $sep_pos;
		
		if(is_array($_GET)) {
			foreach($_GET as $clave => $valor) {
				//$matriz_navegacion[$clave]=$valor;
				if($clave!='index') {
					$link .= '&'.$clave.'='.$valor;
					$barra_navegacion .= $sep_ant . '<a href="'.$link.'"' . $class . '>'.ucfirst($valor).'</a>' . $sep_pos;
				}
			}	
		}
		
		$barra_navegacion .= $fin_barra;
		
		return $barra_navegacion;
	
	} 
	
	public function __toString () 
	{
		return $this->_barra_navegacion();
	}

}