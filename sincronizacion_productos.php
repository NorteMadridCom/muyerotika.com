<?php

class Actualizar_stock extends Mysql
{
	/******************************************************************
	Queda la posibilidad de que no haya conexión y por lo tanto anular
	el stock y que no se pueda comprar hasta que no se pueda volver a 
	conectar
	*******************************************************************/
	
	private $_idproducto;
	private $_idproducto_diakros;
	private $_producto;
	private $_actualizo;
	private $_eliminar=false;
	public $uds_stock;
	public $precio;
	public $uds;
	public $conexion_servidor;
	public $reservadas;
	
	private function _consultar_producto() 
	{
		$sql = "
			SELECT 
				idproducto, 
				idproducto_diakros, 
				DATEDIFF(NOW(),actualizado) as actualizo   
			FROM 
				productos 
			WHERE 
				producto='{$this->_producto}'
			;";

		$ids = parent::ejecutar_consulta($sql);
		$this->_idproducto=$ids[0]->idproducto;
		$this->_idproducto_diakros=$ids[0]->idproducto_diakros;
		$this->_actualizo=$ids[0]->actualizo;
	}
	
	private function _consultar_articulo()
	{
		if($this->conexion_servidor->producto_existe($this->_idproducto_diakros)) {
			$this->uds_stock = $this->conexion_servidor->consultar_stock_diakros($this->_idproducto_diakros);
			$this->precio = round($this->conexion_servidor->consultar_precio_diakros($this->_idproducto_diakros),4);
			$this->_eliminar=false;
		} else {
			$this->_eliminar=true;
		}
	}
	
	public function unidades_pedidos()
	{
		$sql_reservadas = "
			SELECT
				sum(dp.uds) as uds_reservadas 
			FROM 
				pedidos p, 
				detalles_pedidos dp 
			WHERE 
				p.confirmado is NULL AND 
				p.pago is not Null AND 
				p.eliminado = 0 AND 
				dp.idpedido = p.idpedido AND 
				dp.idproducto='{$this->_idproducto}'
			;";
			
		$objeto = parent::ejecutar_consulta($sql_reservadas);
		$this->reservadas=$objeto[0]->uds_reservadas;
		
	}
	
	public function actualizar($producto,$conexion)
	{

		$this->_producto = $producto;
		$this->_consultar_producto();

		$resultado_actualizar = false;
		
		if($conexion) {

			$this->conexion_servidor = new Sinc_diakros;
			$this->_consultar_articulo();
			if($this->_eliminar===true) {
				$sql_actualizar = "
					UPDATE 
						productos 
					SET 
						eliminado = 1  
					WHERE 
						idproducto = {$this->_idproducto}
					;";
				
			} else {

				$this->unidades_pedidos();
				$this->uds = $this->uds_stock - $this->reservadas;
				
				$sql_actualizar = "
					UPDATE 
						productos 
					SET 
						uds = {$this->uds}, 
						precio = {$this->precio}  
					WHERE 
						idproducto = {$this->_idproducto}
					;";
					
			}

			$resultado_actualizar = parent::resultado_consulta($sql_actualizar);			
		
		} else {
			//echo 'no actualizado';
			$sql_actualizar = "
				UPDATE 
					productos 
				SET 
					uds = 0 
				WHERE 
					idproducto = {$this->_idproducto}
				;";
				
			parent::resultado_consulta($sql_actualizar);

		}
		
		return $resultado_actualizar;	
		
	}

}

class Sincronizacion_productos extends Mysql
{
	
		//hay que mirar que productos existen y entonces sincronizar los precios y el stock
		//hay que hacer nombres automáticos para los productos (espacios,eñes,acentos)
		//podemos poner unas opciones:
		//	- sinc total: de todos los datos->sobreescribo todos
		//	- sinc simple: desde ultima fecha
		//existe la posibilidad de que no solo sincronice el stock, sino también los precios
		
		
		
		//PRIMERO HAY QUE SABER QUE TIPO QUEREMOS PARA NO BUSCAR DEMASIADO
		//en la media, pasamos de precios y estock, solo la simple, miro la 
		//ultima fecha de creacion y cojo id diakros y miro cuales tiene idmayor.
		
	
	private $_obj_sincro = object;
	private $_sincro;
	private $_id_sincro;
	private $_conectado;
	private $_inicial;
	private $_final;
	
	public function __construct($inicial=false,$final=false) 
	{
		
		$sincro = new Conexion_mssql;
		$this->_conectado = $sincro->puerto;
		
		$this->_inicial=$inicial;
		$this->_final=$final;
		
		$this->_obj_sincro = new Sinc_diakros;
		
		if($this->_conectado) {
			//iniciar la sincrnizacion					
			$this->_sincro = true; //cuando algo falle la ponemos a  FALSE
			$sql_sinc = "INSERT INTO sincronizaciones SET inicio=now();";		
			if(parent::resultado_consulta($sql_sinc)) {				
				$this->_id_sincro = $this->id_ultimo_registro;
				$this->_consultar_articulo();
			} else {
				die('No se puede sincronizar, hay algun problema con la tabla de sincronizaciones.');
			}
		} else {
			die ('No hay comunicación con el servidor central. Pruebe más tarde.');
		}
		
	}
	
	private function _consultar_articulo()
	{		
		$sinc_diakros=$this->_obj_sincro->consultar_productos_diakros($this->_inicial,$this->_final);//saca todos los idarticulo de sql_server

		//ahora miro si existe o no, y dependiendo de la sinc hago o no
		foreach($sinc_diakros as $registro) {
			if($registro) {
				$this->_consultar_producto($registro);
			} 
		}
		//tenemos que ver los que no se han actualizado y eliminarlos, pero he de ponerlo aqui
		$this->_eliminar_producto();
	}
	
	private function _consultar_producto($producto_diakros) 
	{
		$sql = "
			SELECT 
				idproducto, 
				DATEDIFF(NOW(),actualizado) as actualizo, 
				producto, 
				producto_nombre, 
				uds, 
				precio, 
				eliminado, 
				web    
			FROM 
				productos 
			WHERE 
				idproducto_diakros='{$producto_diakros->idarticulo}' 
			;";
		$producto = parent::ejecutar_consulta($sql);
		unset($actualizar_cols);
		
		//var_dump($producto);
		//if($producto[0]->idproducto){ echo 'existe'.$producto[0]->idproducto; }

		if($producto[0]->idproducto) {//existe el producto
			//Hay que hacer un Update, primero calcular las uds reservadas
			//y luego hacer el update, con el análisis de cadena, si esta eliminado
			//no hago caso
			if($producto[0]->web == 1) {//no solo que exista sino que sea para la web

				if($producto[0]->producto_nombre != $producto_diakros->Articulo) {
					$actualizar_cols[] = "producto_nombre = '{$producto_diakros->Articulo}' ";
					$tratar_producto = new Nombre_web($producto_diakros->Articulo);
					$producto = $tratar_producto->tratar_nombre();
					$actualizar_cols[] = "producto = '$producto' ";
				}
				
				if($producto[0]->precio != $producto_diakros->Precio) {
					$actualizar_cols[] = "precio = '{$producto_diakros->Precio}' "; 
				}
				

				$sql_reservadas = "
					SELECT
						sum(dp.uds) as uds_reservadas 
					FROM 
						pedidos p, 
						detalles_pedidos dp 
					WHERE 
						p.confirmado is NULL AND 
						p.pago is not Null AND 
						p.eliminado = 0 AND 
						dp.idpedido = p.idpedido AND 
						dp.idproducto='{$producto[0]->idproducto}'
					;";
					
				$objeto = parent::ejecutar_consulta($sql_reservadas);
				$reservadas=$objeto[0]->uds_reservadas;
				$stock_web = $reservadas + $producto[0]->uds;
				if($stock_web != $producto_diakros->Stock) {
					$uds = $producto_diakros->Stock - $reservadas;
					$actualizar_cols[] = "uds = '$uds' "; 
				}
				
				$actualizar_cols[] = "eliminado = 0 "; //se vuelve a activar, ha vuelto a aparecer
				$actualizar_cols[] = "actualizado = NOW() ";
				
				$columnas = implode(', ', $actualizar_cols);
				 $sql_actualizar = "
					UPDATE
						productos
					SET
						$columnas
					WHERE 
						idproducto_diakros='{$producto_diakros->idarticulo}'
					;";
			
			} else { //tan solo hago esto para marcar que se actualizo, aunq no haga cambios
				
				$sql_actualizar = "
					UPDATE 
						productos 
					SET 
						actualizado = NOW() 
					WHERE 
						idproducto_diakros='{$producto_diakros->idarticulo}'
					;";
				
			} //si el producto se ha eliminado no hacemos nada, simplemente al hacer update, se actualiza la fecha de actualizacion
					
		} else {
			//hay que hacer un Insert porque no se ha encontrado el producto
			
			$actualizar_cols[] = "producto_nombre = '{$producto_diakros->Articulo}' ";
			$actualizar_cols[] = "precio = '{$producto_diakros->Precio}' "; 
			$actualizar_cols[] = "uds = '{$producto_diakros->Stock}' "; 
			$actualizar_cols[] = "idproducto_diakros = '{$producto_diakros->idarticulo}' "; 
			$actualizar_cols[] = "creado = NOW() ";
						
			$tratar_producto = new Nombre_web($producto_diakros->Articulo);
			$producto = $tratar_producto->tratar_nombre();
			$actualizar_cols[] = "producto = '$producto' ";
			
			$columnas = implode(', ', $actualizar_cols);
				
			$sql_actualizar = "
				INSERT INTO 
					productos 
				SET 
					$columnas  
				;";
	
		}

		$exito = parent::resultado_consulta($sql_actualizar);
		
		if(!$exito) {
			echo 'Existen problemas para actualizar los productos. 
				Inténtelo en otro momento y, si el problema persiste póngase en contacto con el administrador.
			';
			$this->_sincro=false;
		} 
		
	}
	
	private function _eliminar_producto() 
	{
			
		//Miramos que unidades no han sido actualizadas o insertadas y las eliminamos		
		unset($where_inicial,$where_final);
		if($this->_inicial) {
			$where_inicial = " AND p.idproducto_diakros >= " . $this->_inicial;
			if($this->_final) $where_final = " AND p.idproducto_diakros < " . $this->_final;
		}

		$sql_eliminar="
			SELECT 
				p.idproducto 
			FROM 
				productos p, 
				sincronizaciones s 
			WHERE 
				p.eliminado = 0 AND 
				p.web = 1 AND 
				s.idsincronizacion = {$this->_id_sincro} AND 
				p.actualizado < s.inicio 
				$where_inicial 
				$where_final 
		;";
		$producto_eliminar = parent::ejecutar_consulta($sql_eliminar);
		if(is_array($producto_eliminar)) {
			foreach ($producto_eliminar as $registro) {
				if($registro) {
						$sql_eliminacion = "UPDATE productos SET eliminado=1 WHERE idproducto='{$registro->idproducto}';";
						if(!parent::resultado_consulta($sql_eliminacion)) {
							$this->_sincro=false;
						}
				}
			}
		}
		
		$this->_fin_sincronizacion();

	}
	
	private function _fin_sincronizacion() 
	{
		if($this->_sincro===true) {
			$sql_sincro = "
				UPDATE
					sincronizaciones
				SET
					fin = NOW()
				WHERE
					idsincronizacion = {$this->_id_sincro} 
			;";
			$fin = parent::resultado_consulta($sql_sincro);
		} else {
			$fin = false;
		}
		
		return $fin;
	}

}