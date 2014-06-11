<?php

class Menu
{
	public function __construct($tabla,$titulo=false,$dir_imagenes=false)
	{
		$tabla == 'familias_web' ?
			$elemento = 'familia_web':
			$elemento=substr($tabla, 0, -1);
		$menu = new Mysql;
		$menu->ejecutar_consulta("SELECT * FROM $tabla WHERE eliminado=0 ORDER BY orden, $elemento;");	
			
		if($menu->numero_registros > 0) {
			if($titulo) echo '<div id="titulo_menu_lateral"><b>'.$titulo.'</b></div>';
			echo '<ul class="menu_lateral">';
			
			if($tabla == 'familias_web') echo '<a href="index.php?familia_web=novedades" class="menu"><li class="menu">Novedades</li></a>';
			
			for($i=0;$i<$menu->numero_registros;$i++) {
				$matriz = (array) $menu->registros[$i];
				if($dir_imagenes) $img = '<img src="'.$dir_imagenes.'/'.$matriz['imagen'].'" class="img"/>';
				echo '
					<li class="caja_menu_lateral">
						<a href="'. $_SERVER['PHP_SELF'] . '?' . $elemento . '=' . $matriz[$elemento] . '" class="menu_lateral">
							'. ucfirst($matriz[$elemento.'_menu']) . 
							$img . '
						</a>
					</li>';
			}
			
			echo '</ul>';
		}
	}
	
}