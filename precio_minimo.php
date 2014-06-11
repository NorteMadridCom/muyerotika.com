<?php

class Precio_minimo
{
	public $precio_min;
	
	public function __construct($precio_tarifa, $dto_producto=0, $dto_linea=0)
	{
		$precio = array();
		$precio['tipo_cliente'] = $precio_tarifa * (1-($_SESSION['tarifa_dto']/100));
		$precio['dto_producto'] = $precio_tarifa * (1-($dto_producto/100));
		$precio['dto_linea'] = $precio_tarifa * (1-($dto_linea/100));
		$this->precio_min = number_format(round(min($precio),2), 2, '.', '');
	}
	
}

?>