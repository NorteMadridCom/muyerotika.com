<?php

class Editar_Pedidos
{
	private $_pedido = object;
	
	public function __construct()
	{
		$this->_pedido = new Mysql;
	}
	
	public function formulario_pedidos() 
	{
		?>
		
		<form action="" method="post" enctype="multipart/form-data">
			<button class="admin" type="submit" name="accion" value="pendientes_pago">Pendientes de Pago/Transferencias</button>
			<button class="admin" type="submit" name="accion" value="Pendientes">Pendientes de Envio</button>
			<button class="admin" type="submit" name="accion" value="Confirmados">Realizados</button>
			<button class="admin" type="submit" name="accion" value="Eliminados">Eliminados</button>
			<button class="admin" type="submit" name="accion" value="Buscar">Buscar</button>
		</form>
		<?php
	}
	
	public function pendientes_pago() 
	{		
		$sql="SELECT * FROM pedidos p WHERE pago is NULL AND confirmado is null AND eliminado=0 ORDER BY idpedido desc;";
		$this->_listar_pedido($sql);
	}
	public function pendientes() 
	{		
		$sql="SELECT * FROM pedidos p WHERE pago is not NULL AND confirmado is null AND eliminado=0 ORDER BY idpedido desc;";
		$this->_listar_pedido($sql);
	}
	
	public function confirmados() 
	{		
		$sql="SELECT * FROM pedidos WHERE pago is not NULL AND confirmado is not null AND eliminado=0 ORDER BY idpedido desc LIMIT 0,100;";
		$this->_listar_pedido($sql);
	}
	
	public function eliminados() 
	{		
		$sql="SELECT * FROM pedidos WHERE eliminado=1 ORDER BY idpedido desc LIMIT 0,100;";
		$this->_listar_pedido($sql);
	}
	
	public function buscar()
	{
		if(!$_POST['ejecutar']) {
			$no_check = '';
			$elim_check = '';
			if($_POST['eliminado']==1) $elim_check = ' checked="checked" ';
			if($_POST['no_pagado']==1) $no_check = ' checked="checked" ';
			?>
			<h2 class="admin">Buscar Pedidos:</h2><br>
			<form action="" method="post" enctype="multipart/form-data">
				<input type="hidden" name="ejecutar" value="1" />
				<label><h2 class="admin">Cliente: </h2></label><input  type="text" name="nombre" value="<?php echo $_POST['nombre']; ?>"  class="admin_caja"/>	
				<label><h2 class="admin">Nº Pedido: </h2></label><input type="text" name="idpedido" value="<?php echo $_POST['idpedido']; ?>"  class="admin_caja"/>
				<label><h2 class="admin">Fecha Inicio:</h2></label><input type="text" name="fecha_inicio" value="<?php echo $_POST['fecha_inicio']; ?>"  class="admin_caja"/>
				<label><h2 class="admin">Fecha Fin:</h2> </label><input type="text" name="fecha_fin" value="<?php echo $_POST['fecha_fin']; ?>"  class="admin_caja"/>
				<label><h2 class="admin">Eliminado: </label><input type="checkbox" name="eliminado" value="1" <?php echo $elim_check; ?> style="margin-left: 10px;"/></h2>
				<label><h2 class="admin">No pagado: </label><input type="checkbox" name="no_pagado" value="1" <?php echo $no_check; ?>/></h2><br>
				<button name="accion" value="buscar" class="admin">Buscar</button>
			
			</form>
			
			
			<?php
		} else {
			if(is_array($_POST)) {
				$where = array();
				$where['eliminado']=" eliminado=0 ";
				$where['pago']=" pago is not null ";
				foreach($_POST as $var => $val) {
					if($val) {
						if($var=='nombre') $where['idcliente']=" idcliente IN (SELECT idcliente FROM clientes WHERE nombre LIKE '%$val%' OR usuario LIKE '%$val%') ";
						if($var=='idpedido') $where['idpedido']=" idpedido=$val ";
						if($var=='eliminado') $where['eliminado']=" eliminado=1 ";
						if($var=='no_pagado') $where['pago']=" pago is null ";
						if($var=='fecha_inicio') {
							$inicio = new Validar_datos("date", $val);
							$where['inicio']=" creado>='{$inicio->fechasql}' ";
						}
						if($var=='fecha_fin') {
							$fin = new Validar_datos("date", $val);
							$where['fin']=" creado<='{$fin->fechasql}' ";
						}
					}
				}
				$where_cadena = implode(" AND ", $where);
				echo $sql="SELECT * FROM pedidos p WHERE $where_cadena ORDER BY idpedido desc LIMIT 0,100;"; //en funcion de la oinfo que nos llega, cliente, fecha o id
				$this->_listar_pedido($sql);
			} else die('Hay un error en la búsqueda del pedido. Consulte con el administrador del sistema.');
		}
	}
	
	private function _listar_pedido($sql)
	{
		$this->_pedido->ejecutar_consulta($sql);
		//var_dump($this->_pedido->registros);
		if(is_array($this->_pedido->registros)) {
			echo '
				<table width="100%">
					<tr>
						<th width="20" align="center">Id</th>
						<th width="90" align="center">Fecha</th>
						<th width="200" align="center">Cliente</th>
						<th width="70" align="center">Total</th>
						<th align="center">Acciones</th>
					</tr>
			';
			foreach($this->_pedido->registros as $pedido) {
				if(!$pedido->pago && !$pedido->confirmado && $pedido->eliminado==0) $status = 'Pendiente de Pago';
				elseif($pedido->pago && !$pedido->confirmado && $pedido->eliminado==0) $status = 'Pendiente';
				elseif($pedido->pago && $pedido->confirmado && $pedido->eliminado==0) $status = 'Confirmado';
				elseif($pedido->eliminado==1) $status = 'Eliminado';
				$acciones = '<input type="hidden" name="status" value="'.$status.'"><button name="accion" value="editar_pedido">Ver pedido</button>';
				if($status=='Pendiente') $acciones .= '<button name="accion" value="confirmar">Confirmar</button><button name="accion" value="eliminar">Eliminar</button>'; 
				elseif($status=='Pendiente de Pago') 
					$acciones .=  '<button name="accion" value="eliminar">Eliminar</button><button name="accion" value="pagar">Pagar</button>';
				$fecha=new Validar_datos('fecha',$pedido->fecha);
				echo '
					<tr>
						<td>'.$pedido->idpedido.'</td>
						<td>'.$fecha->formateado.'</td>
						<td>'.$pedido->nombre.'</td>
						<td align="right">'.$pedido->total.'</td>
						<td align="right">
							<a href="./pedidos/Pedido_'.$pedido->idpedido.'_'.$pedido->pass.'.pdf" target="_blank">Descargar el pedido en PDF</a>
							<form action="" method="post" enctype="multipart/form-data"><input type="hidden" name="idpedido" value="'.$pedido->idpedido.'"/>'.$acciones.'</form>
						</td>				
					</tr>
				';
			}
		} else echo '<p>No hay resultados.</p>';
		echo '</table>';
		
	}
	
	public function editar_pedido($idpedido) 
	{
		$cesta = new Mostrar_cesta($idpedido);
		$cesta->formulario();
	}
	
	private function _formato($val)
	{
		return number_format(round($val, $precision = 2), $decimals = 2);
	}
	
	public function eliminar($idpedido)
	{
		//poner la confirmacion previa
		$sql_eliminar="UPDATE pedidos SET eliminado=1 WHERE idpedido=$idpedido;";
		$this->_pedido->resultado_consulta($sql_eliminar);
		if(!$this->_pedido->error) {
			echo "<p>Se ha eliminado el pedido nº $idpedido.</p>";
			include 'form_conf_elim_pedido.php';
		} else die('No se ha podido realizar esta operacion');
		//hay que enviar un e-mail al cliente
	} 
	
	public function confirmar($idpedido) 
	{
		$sql_confirmar="UPDATE pedidos SET confirmado=NOW() WHERE idpedido=$idpedido;";
		$this->_pedido->resultado_consulta($sql_confirmar);
		if(!$this->_pedido->error) {
			echo "<p>Se ha confirmado el pedido nº $idpedido.</p>";
			include 'form_conf_elim_pedido.php';
		} else die('No se ha podido realizar esta operacion');
		//hay que poner un cuadro de diálogo para que ponga los datos del envio y se envíe con el correo.
	}
	
	public function pagar($idpedido)
	{
		$sql_pagar="UPDATE pedidos SET pago=NOW() WHERE idpedido=$idpedido;";
		$this->_pedido->resultado_consulta($sql_pagar);
		if(!$this->_pedido->error) {
			echo "<p align='center'>Se ha pagado el pedido nº $idpedido.</p>
			<p align='center'><a href='{$_SERVER['REQUEST_URI']}'>Volver</a></p></p>";
			//include 'form_conf_elim_pedido.php';
		} else die('No se ha podido realizar esta operacion');
	}
	
	public function email($idpedido, $mes=false) 
	{
		
		$sql="SELECT p.*, c.usuario FROM pedidos p, clientes c WHERE idpedido=$idpedido AND p.idcliente=c.idcliente;";
		$this->_pedido->ejecutar_consulta($sql);
		if($this->_pedido->registros[0]->eliminado==1) {
			$accion = "Rechazo";
			$mes .= "<p>Sentimos las molestias que esto le pueda ocasionar</p><p>Un saludo en nombre del equipo de Muy Erotika</p>";
		} else { 
			$accion = "Envío";
			$mensaje = "/pedidos/Pedido_{$idpedido}_{$this->_pedido->registros[0]->pass}.pdf";
			$mes .= '<p>Degargue su pedido <a target="_blank" href="http://'. $_SERVER['SERVER_NAME'] . $mensaje . '"> Pedido nº '. $idpedido . '</a>.</p>';
			$mes .= "<p>Muchas gracias en nombre del equipo de Muy Erotika</p>";
		}
	   
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$cabeceras .= 'From: Dizma.es <comercial@dizma.es>' . "\r\n";
		$cabeceras .= 'Reply-To: comecial@dizma.es' . "\r\n" ;
		
		$asunto = "$accion del pedido Nº $idpedido en Dizma.es";
		
		if(mail($this->_pedido->registros[0]->usuario,$asunto, $mes,$cabeceras)) echo '<p align="center">Mensaje enviado a '.$this->_pedido->registros[0]->usuario.'.</p>';
		else echo '<p>No se ha podido enviar el correo al cliente. Póngase en contacto con el administrador del sistema.';	
		echo '<p align="center"><a href="'.$_SERVER['PHP_SELF'].'?seccion=pedidos">Volver</a></p>';
		
	}
	
}

?>
