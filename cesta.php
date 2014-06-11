<?php

class Cesta extends Sesion
{
	
	/*****************************************************************************
	Hay que ver la posibilidad de que los gastos sean más de uno y que cada gastos
	lleve un iva distinto. No se ha realizado, pero habría que recogerlo como una 
	matriz desde la base de datos. -- NO REALIZADO --
	*****************************************************************************/
	
	
	public $datos_cesta = array();
	public $detalles_cesta = array();
	public $error = false;
	public $error_txt = null;
	private $_conf = array();
	
	public function __construct($conf) 
	{

		$this->datos_cesta = $_SESSION['datos_cesta'];
		$this->_conf=$conf;
		//$this->datos_cesta['envio']=$this->_conf['gastos_envio'];
		
	}
	
	
	public function calcular_cesta() 
	{
		
		$this->datos_cesta['fecha'] = time();
		$this->_netos();	
		$this->_dto_volumen();
		$this->_envio();
		$this->_iva();
		//Gastos_envio::run();

		$_SESSION['datos_cesta']=$this->datos_cesta;
		
	}
	
	private function _iva() 
	{
		if(is_array($this->datos_cesta['neto_dto']) && count($this->datos_cesta['neto_dto'])>0) {
			foreach($this->datos_cesta['neto_dto'] as $porcentaje_iva=>$neto){
				$this->datos_cesta['iva'][$porcentaje_iva]=round($neto*floatval($porcentaje_iva)/100,2);
				if($porcentaje_iva==$this->_iva_producto($this->_conf['idiva_gastos_envio'])) $this->datos_cesta['iva'][$porcentaje_iva]= $this->datos_cesta['iva'][$porcentaje_iva] + round($this->datos_cesta['envio']*floatval($porcentaje_iva)/100,2);
			}
		} else {
			unset($this->datos_cesta['iva']);
		}
	}
	
	private function _dto_volumen()
	{
		if(!$this->datos_cesta['neto_total']) $this->datos_cesta['neto_total']=0;
		$sql="SELECT MAX(dto_volumen) as dto FROM dtos_volumen WHERE volumen<{$this->datos_cesta['neto_total']} AND eliminado=0;";
		$dto=new Mysql;
		$dto->ejecutar_consulta($sql);
		$this->datos_cesta['dto_volumen_%'] = floatval($dto->registros[0]->dto);
		if(is_array($this->datos_cesta['neto']) && count($this->datos_cesta)>0) {
			foreach($this->datos_cesta['neto'] as $porcentaje_iva=>$neto){
				$this->datos_cesta['neto_dto'][$porcentaje_iva]=round($neto*(1-($this->datos_cesta['dto_volumen_%']/100)),2);
			}
		} else {
			unset($this->datos_cesta['neto_dto']);
		}
	}
	
	private function _netos() 
	{
		if( is_array($this->detalles_cesta) && count($this->detalles_cesta)>0 ) {
			foreach($this->detalles_cesta as $producto_cesta) {
				$neto[strval($producto_cesta['iva_%'])][]=round($producto_cesta['uds']*$producto_cesta['precio']*$producto_cesta['dto_detalle'],2);
			}
			if(count($neto)>0) {
				$this->datos_cesta['neto_total']=0;
				foreach($neto as $porcentaje_iva=>$netos){
					$this->datos_cesta['neto'][$porcentaje_iva]=round(array_sum($netos),2);
					$this->datos_cesta['neto_total'] = round($this->datos_cesta['neto_total'] + array_sum($netos),2);	
				}
			}
			//var_dump ($this->detalles_cesta); 
		} else {
			unset($this->datos_cesta['neto']);
			unset($this->datos_cesta['neto_total']);
			//var_dump($this->datos_cesta);
		}
	}
	
	public function _envio()
	{
		
		if(!$this->datos_cesta['neto_dto']) $neto_total=0;
		else $neto_total = floatval(array_sum($this->datos_cesta['neto_dto']));
		$sql="SELECT MAX(gastos_envio) as envio FROM gastos_envios WHERE hasta_neto > $neto_total;";
		$gastos = new Mysql;
		$gastos->ejecutar_consulta($sql);
		if($gastos->numero_registros) $this->datos_cesta['envio']=floatval($gastos->registros[0]->envio);
		else $this->datos_cesta['envio']=0;
	}
	
	private function _encontrar_producto($idproducto)
	{
		$encontrado = false;
		if(is_array($this->detalles_cesta)) {
			foreach($this->detalles_cesta as $n => $producto_cesta) {
				if($producto_cesta['idproducto']==$idproducto) {
					$this->detalles_cesta[$n]['uds']=$this->detalles_cesta[$n]['uds']+$uds;
					$encontrado = $n;
				} 
			}
		}
		return $encontrado;
	}
		
	public function add_producto($idproducto,$uds=1,$idiva,$precio) 
	{

		$this->detalles_cesta = $_SESSION['detalles_cesta'];
		
		$encontrado = $this->_encontrar_producto($idproducto);		
		
		if($encontrado===false) {
			$uds = $this->_stock_producto($idproducto,$uds);
			$iva_porcentaje = $this->_iva_producto($idiva);
			$precio_sin_iva = $precio/(1+($iva_porcentaje/100));
			$this->detalles_cesta[]= array(
				'idproducto' 	=> $idproducto,
				'uds' 			=> $uds,
				'iva_%' 		=> $iva_porcentaje,
				'precio' 		=> $precio_sin_iva, 
				'dto_detalle'	=> $this->_dto_detalle($idproducto)	
			);
		} else {
			$uds = $this->_stock_producto($idproducto,$this->detalles_cesta[$encontrado]['uds']+$uds);
			$this->detalles_cesta[$encontrado]['uds']=$uds;
		}
	
		$_SESSION['detalles_cesta']=$this->detalles_cesta;
		
	}
	
	public function del_producto($idproducto)
	{
		$this->detalles_cesta = $_SESSION['detalles_cesta'];
		
		$encontrado = $this->_encontrar_producto($idproducto);
		
		if($encontrado===false) {
			$this->error = true;
			$this->error_txt = "No existe el producto en la cesta para eliminar";
		} else {
			unset($this->detalles_cesta[$encontrado]);
		}
	
		$_SESSION['detalles_cesta']=$this->detalles_cesta;
	
	}
	
	public function edit_producto($idproducto,$uds)
	{
		
		$this->detalles_cesta = $_SESSION['detalles_cesta'];
		
		$encontrado = $this->_encontrar_producto($idproducto);
		
		if($encontrado===false) {
			$this->error = true;
			$this->error_txt = "No hay productos en la cesta para editar";
		} else {
			if($uds>0) $this->detalles_cesta[$encontrado]['uds'] = $this->_stock_producto($idproducto,$uds);
			else unset($this->detalles_cesta[$encontrado]);
		}
	
		$_SESSION['detalles_cesta']=$this->detalles_cesta;
		
	}
	
	private function _stock_producto ($idproducto,$uds)
	{
		
		$sql_stock = "SELECT uds FROM productos WHERE idproducto='$idproducto';";
		$stock=new Mysql;
		$stock->ejecutar_consulta($sql_stock);
		if($stock->registros[0]->uds >= $uds) return $uds;
		else {
			$this->datos_cesta['aviso']="El producto seleccionado tiene un stock máximo de {$stock->registros[0]->uds}.";
			return $stock->registros[0]->uds;			
		}
		
	}
	
	public function vaciar_cesta() 
	{
		unset($this->detalles_cesta);
		$_SESSION['detalles_cesta']=$this->detalles_cesta;
		
		unset($this->datos_cesta);
		$_SESSION['datos_cesta']=$this->datos_cesta;
	}
	
	private function _iva_producto($idiva)
	{
		$sql = "SELECT valor FROM fecha_ivas WHERE (fecha_fin > CURDATE() OR fecha_fin is NULL) AND idiva=$idiva;";
		$iva = new Mysql;
		$iva->ejecutar_consulta($sql);
		if($iva->numero_registros != 1) {
			$this->error=true;
			$this->error_txt = "Error al obtener el iva";
			return false;
		} else {
			return $iva->registros[0]->valor;
		}
	}
	
	private function _dto_detalle($idproducto)
	{
		/*
		hay un sistema que devuelve el dto_max o precio min, pero no tengo el precio min
		*/
		$dto_pri = new Dto_prioritario($idproducto);
		if(!$dto_pri->dto_pri()) {
			$sql = "SELECT dto_linea, dto_producto FROM lineas l, productos p WHERE l.idlinea=p.idlinea AND p.idproducto='$idproducto';";
			$dto_max = new Mysql();
			$dto_max->ejecutar_consulta($sql);
			if($dto_max->numero_registros != 1) {
				$this->error=true;
				$this->error_txt = "Error al obtener el descuento mayor.";
				return false;
			} else {
				return round(1-(max($dto_max->registros[0]->dto_linea,$dto_max->registros[0]->dto_producto,$_SESSION['tarifa_dto'])/100),2);
			}
		} else {
			return $dto_pri->dto_pri();
		}		
	}
	
	public function total_cesta()
	{
		(is_array($_SESSION['datos_cesta']['neto_dto'])) ? $netos=array_sum($_SESSION['datos_cesta']['neto_dto']) : $netos=0;
		(is_array($_SESSION['datos_cesta']['iva'])) ? $ivas=array_sum($_SESSION['datos_cesta']['iva']) : $ivas=0;
		if(($netos+$ivas)>0) return $netos+$ivas+$this->datos_cesta['envio'];
		else return 0;
	}
	
	public function recalcular_cesta()
	{
		$this->detalles_cesta = $_SESSION['detalles_cesta'];
		if(count($this->detalles_cesta)>0) { 
			foreach ($this->detalles_cesta as $n => $detalle) {
				echo $this->detalles_cesta[$n]['dto_detalle'] = $this->_dto_detalle($detalle['idproducto']);
			}
		}
		$_SESSION['detalles_cesta']=$this->detalles_cesta;
	}	
	
	public function datos_envio($datos_envio)
	{	
		if($datos_envio['direccion_envio'] && $datos_envio['nombre_envio'] && $datos_envio['cp_envio'] && $datos_envio['localidad_envio'] && $datos_envio['provincia_envio']) {
			foreach($datos_envio as $nombre=>$dato){
				if($nombre!='accion') $_SESSION['datos_cesta'][$nombre]=$dato;	
			}
		} else {
			$_SESSION['mensaje']="Introduzca los datos correctamente";
		}
	}
	
	public function eliminar_envio()
	{	
		unset($_SESSION['datos_cesta']['nombre_envio']);
		unset($_SESSION['datos_cesta']['direccion_envio']);
		unset($_SESSION['datos_cesta']['cp_envio']);
		unset($_SESSION['datos_cesta']['localidad_envio']);
		unset($_SESSION['datos_cesta']['provincia_envio']);
	}
	
	public function otros_datos($otros_datos) 
	{
		if($otros_datos['observaciones']) $_SESSION['datos_cesta']['observaciones']=$otros_datos['observaciones'];
		else unset($_SESSION['datos_cesta']['observaciones']);
	}
	
	public function __destruct() {}

}
