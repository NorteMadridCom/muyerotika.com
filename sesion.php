<?php

//require_once 'mysql.php';

class Sesion
{
	public $datos_sesion = array();
	public $sesion_registrada = false;
	
	public function __construct() 
	{
		$this->datos_sesion['id'] = session_id();
		$this->datos_sesion['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->datos_sesion['fecha'] = time();
		if($_SESSION['sesion_registrada'] !== true) {
			$sql_log_inicial = "INSERT INTO logs SET ip = '{$this->datos_sesion['ip']}', accion = 'sin registro', fecha_hora = FROM_UNIXTIME({$this->datos_sesion['fecha']});";
			$this->_log($sql_log_inicial);
		}
	}

	private function _registrar_sesion($datos_usuario) 
	{
		foreach($datos_usuario as $clave=>$valor) {
			$this->datos_sesion[$clave]=$valor;
		}
		$this->datos_sesion['sesion_registrada'] = true;
		unset($_SESSION['tarifa_dto']);
		$_SESSION = $_SESSION + $this->datos_sesion;
		$sql_log_registro = "INSERT INTO logs SET ip = '{$this->datos_sesion['ip']}', accion = 'Registro', idusuario = '{$this->datos_sesion['idcliente']}', fecha_hora = FROM_UNIXTIME(". time() .");";
		$this->_log($sql_log_registro);
	}
	
	public function consultar_usuario($usuario, $pass) 
	{
		$sql="
			SELECT 
				c.idcliente, 
				c.usuario,
				c.nombre,
				c.cif, 
				c.direccion, 
				c.localidad, 
				c.provincia, 
				c.cp, 
				c.nombre_envio, 
				c.direccion_envio, 
				c.localidad_envio, 
				c.provincia_envio, 
				c.cp_envio,  
				t.tarifa, 
				t.tarifa_dto,
				tc.idtipo_cliente,  
				tc.tipo_cliente, 
				tc.profesional   
			FROM 
				clientes c, 
				tipos_clientes tc, 
				tarifas t 
			WHERE 
				c.usuario='$usuario' AND 
				c.pass='$pass' AND 
				c.idtipo_cliente = tc.idtipo_cliente AND 
				tc.idtarifa = t.idtarifa AND 
				c.eliminado = 0 AND
				t.eliminado = 0 AND 
				c.desbloqueo is NULL
			;
		";
		$cliente=new Mysql;
		$cliente->ejecutar_consulta($sql);
		if($cliente->numero_registros == 1) {
			$this->_registrar_sesion((array) $cliente->registros[0]);
		} elseif($cliente->numero_registros == 0) {
			?>
			<div id="aviso_stock" class="mensaje_disponibilidad"  style="
				dislay: block;
				position: fixed;
				top: 50%;
				left: 50%;
				width: 200px;
				height: 150px;
				margin-top: -200px; 
				margin-left: -100px;
				z-index: 10000;
			">
			<div id="txt_disp"><?php echo 'Datos de acceso incorrectos. Vuela a intentarlo'; ?></div>
			<form action="" method="post" enctype="multipart/form-data">
				<button type="submit" name="accion" value="cerrar_aviso_stock" id="bt_compras" style="margin-top: 0px; margin-right: 20px;">Cerrar</button>
			</form>
			</div>
			
			<?php
		} else {
			die ('No se puede registrar su sesión de usuario. Contacte con los administradores');
		}
		$cliente->__destruct();
		
	}
	
	private function _log($sql_log) 
	{
		$log = new Mysql();
		$log->resultado_consulta($sql_log);
		$log->__destruct();
	}
	

	public function desconectar() 
	{
		$sql_log_final = "INSERT INTO logs SET ip = '{$this->datos_sesion['ip']}', accion = 'Desconectar', idusuario = '{$_SESSION['idcliente']}', fecha_hora = FROM_UNIXTIME(". time() .");";
		$this->_log($sql_log_final);
		session_destroy();
	}	
	
	public function __destruct() {}
	
	
}	

	

	
		
