<?php

class Paginador
{
	/*
	/ MEJORAS POR HACER:
	/ - Poner activa la funcion de formatos para elegir el formato de flechas, divs, etc
	/ - Poner también el número de elementos antes y despues del actual (n+i y n-i), que se seleccione
	/ - Poner segun el numero de paginas que varien los elementos que voy a mostrar, por ej, con 2 elemntos
	/   no mostrar las flechas... etc.
	*/
	
	/* --------EJEMPLO DE USO ----------
	$productos_Actualizados es el resultado con todos los datos, matriz u objetos
	$productos_actualizados_pag = new Paginador($productos_actualizados,15);
		if(!$productos_actualizados_pag->error) {
			echo '<div id="productos">';
			$menuproductos = new Productos($config,$productos_actualizados_pag->resultado[$_GET['index']],'cuadro_producto');
			$menuproductos->poner_submenu();
			echo '</div>';
		}
		$productos_actualizados_pag->poner_indices();
	*/
	
	
	
	public $error = false;
	
	/*
	/ la matriz resultante es $resultado[num_pagina], 
	/ dentro contiene todos los registros con sus claves
	*/
	public $resultado = array();
	/*
	/ múmero de paginas que ha de devolver
	*/
	public $paginas;
	/*
	/ cantidad de números que mostramos antes y después del actual
	/ falta que lo modifique el usuario
	*/
	private $_grupo = 3;
	
	
	public function __construct($datos,$datos_pagina, $indices_en_objeto=false ,$matriz_en_objeto='registros') 
	{

		/*
		/ $datos es la matiz u objeto a tratar, si es matriz no es es problema, simplemente la partimos y con los índices
		/ los matenemos o los reasignamos con indicies en objeto /true/false
		/ igualmente para los objetos que contienen una matriz que queremos partir, devolsvemos una matriz de objetos,
		/ que contienen cada objeto la matriz de resultado partida, con los índices mantenidos o no.
		*/	
		
		if(is_array($datos)) {
						
			$this->resultado = array_chunk($datos, $datos_pagina, $preserve_keys = true);
			$this->paginas = count($this->resultado);
			
		} elseif(is_object($datos)) {
			
			//suponemos que es un objeto u objetos que contienen la matriz de datos
			//$res->resultados
			$datos = (array) $datos;
			//var_dump($datos);
			$matrices = 0;
			if(is_array($datos[$matriz_en_objeto])) {
				$matrices++;
			} else {
				foreach($datos as $clave=>$valor) {
					if(is_array($valor)) {
						$matriz_en_objeto = $valor;
						$matrices++;
					}
				}
				if($matrices!=1) {
					$this->error=true;
				} 
			}	
			
			if($this->error===false) {
				//el objeto resultado ha de ser una matriz de objetos que contiene todos los datos del objeto original
				//menos la matriz de resultados, que es la que viene en trozos para cada objeto
				//se llama $objeto->resultado[$_GET[index]] en la pag donde queremos ponerla
				unset($this->resultado);
				$matriz_a_partir = $datos[$matriz_en_objeto];
				//var_dump($matriz_a_partir);
				unset($datos[$matriz_en_objeto]);
				$almacenado = $datos;
				$matriz_partida = array_chunk($matriz_a_partir, $datos_pagina, $indices_en_objeto);
				$this->paginas = count($matriz_partida);
				foreach($matriz_partida as $parte) {
					$objeto = $almacenado;
					$objeto[$matriz_en_objeto] = $parte;
					$this->resultado[] = (object) $objeto;
				}
				//var_dump($this->resultado);
			}
			
		} else {
			$this->error = true;
		}
	}	
	
	public function formatos() 
	{
		/*lo mas completo es:
		<< < 1... N-x... N-2 N-1 N N+1 N+2 ...N+x ...n > >>
		<< >> : primero y ultimo
		n : num maximo
		x : rango de numeros que acompañan al que tengo
		< > : siguinete anterios
		*/
		
		/*
		habría que imprimir los valores de los formatos 
		y pasar rl número pulsado al archivo donde se 
		mostrarían los del rango deseado, lo mormal es que se envie por get
		ya que es un elemnto de navegacion
		*/  				
	}
	
	public function poner_indices() 
	{
		
		if(!$_GET['index']) {
			$actual=0;
		} else {
			$actual=$_GET['index'];
		}
		
		if($this->paginas>1) {
		
			$ult = $this->paginas - 1;
			$ant = $actual - 1;
			$sig = $actual + 1;
			
			unset($_GET['index']);
			foreach($_GET as $clave => $valor) {
				$argumento[] = "$clave=$valor";
			}
			if(count($argumento)>0) $argumentos=implode('&', $argumento);
			$url = $_SERVER['PHP_SELF']."?".$argumentos;			
			$ini = '<a href="'.$url.'&index=';

			if($actual>0) {		
				$primero = $ini.'0"> << </a>';
				$anterior = $ini . $ant . '"> < </a>';
			}
			
			if($actual<$this->paginas) {
				$ultimo = $ini. $ult.'"> >> </a>';		
				$siguiente = $ini. $sig .'"> > </a>';
			}
			
			$lim = $this->_grupo +1;
			
			for($i=1;$i<$lim;$i++) {
				$a = $actual - $i;
				$b = $actual + $i;
				$most_a=$a+1;
				$most_b=$b+1;
				if($a>=0) $index_ant[$i] = $ini.$a.'">'. $most_a.'</a>';
				if($b<$this->paginas) $index_sig[$i] = $ini.$b.'">'. $most_b .'</a>';
			}
			
			
					
			if($this->paginas>$this->grupo && $actual>0) echo $primero . ' ' . $anterior . ' ' ;
			
			if(is_array($index_ant)) {
				krsort($index_ant);
				foreach($index_ant as $anteriores) {
					echo $anteriores . ' ';
				}
			}
			
			if($this->paginas>1) echo ' <span class="actual">'.++$actual.'</span> ';
			
			if(is_array($index_sig)) foreach($index_sig as $siguientes) {
				echo $siguientes . ' ';
			}
			
			if($this->paginas>$this->grupo && $actual<$this->paginas) echo ' ' . $siguiente . ' ' .  $ultimo;
		
		}
		
	}
	
}