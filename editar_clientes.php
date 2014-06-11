<?php

class Editar_clientes
{
	
	//solo se ha empezado con el formulario
	private $_consulta_clientes = object;
	public $error=false;
	
	public function __construct() 
	{
		$this->_consulta_clientes = new Mysql();
	}
	
	public function buscar_cliente_id($valores, $eliminar=false) 
	{
		$sql_idcliente = "SELECT * FROM clientes WHERE idcliente = {$valores['idcliente']};";
		$this->_consulta_clientes->ejecutar_consulta($sql_idcliente);
		$this->formulario((array) $this->_consulta_clientes->registros[0], $eliminar);
	}
	
	public function formulario($valores=array(), $eliminar=false, $campo_mal=array()) 
	{
		$eliminar ? $disabled = ' disabled=disabled ' : $disabled = '';
		
		if(!$valores['pass_aux']) $valores['pass_aux']=$valores['pass']; 
		
		echo '
			<div class="titulo_admin">Datos del Cliente</div>
			<form action="" method="post" enctype="multipart/form-data">
				<input class="admin_caja" type="hidden" name="idcliente" value="'. $valores['idcliente'] .'" />
				<br><label><h2 class="admin">Usuario/e-mail*: </h2></label><input class="admin_caja" type="email" name="usuario" value="' . $valores['usuario']. '" size="30" maxlength="100" placeholder="Ponga su e-mail" autofocus required '.$disabled.' '.$campo_mal['usuario'].'/> 
				<br><label><h2 class="admin">Contraseña*: </h2></label><input class="admin_caja" type="password" name="pass" value="' . $valores['pass']. '" size="20" maxlength="20" pattern="(?=^.{6,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" required '.$disabled.' '.$campo_mal['pass'].'/>
				<br><label><h2 class="admin">Repita la contraseña*: </h2></label><input class="admin_caja" type="password" name="pass_aux" value="' . $valores['pass_aux']. '" size="20" maxlength="20" pattern="(?=^.{6,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" required '.$disabled.' '.$campo_mal['pass'].'/>
		';
		/********************************************
		Hay que ver que como poner el tipo de cliente, si es profesional o no, solo lo puede elegir david
		**************************************/
		if($_SESSION['tipo_cliente']=='administrador') {
			echo '<br><label><h2 class="admin">Grupo:</h2> </label>';
			$tipo_cliente = new Combo('idtipo_cliente', 'tipos_clientes', 'idtipo_cliente', 'tipo_cliente', $valores['idtipo_cliente'], true, $filtro = false, $campo_eliminado = false, $campo_orden = 'tipo_cliente', $eliminar, $visible = true, $required = false, $eventos = null);
			$tipo_cliente->poner_combo();
			echo '<br><label><h2 class="admin">Id Diakros:</h2> </label><input class="admin_caja" type="text" name="idcliente_diakros" value="' . $valores['idcliente_diakros']. '" size="5" maxlength="5" '.$disabled.' />';
		} else {
			if(!$valores['idtipo_cliente']) $valores['idtipo_cliente']=1;
			echo '<input class="admin_caja" type="hidden" name="idtipo_cliente" value="'.$valores['idtipo_cliente'].'" />'; //este es el marcador de pendiente, si no pone nada
		}
		echo '
				<br><label><h2 class="admin">Nombre Fiscal*:</h2> </label><input class="admin_caja" type="text" name="nombre" value="' . $valores['nombre']. '" size="30" maxlength="120" placeholder="Nombre fiscal o Nombre y apellidos" '.$disabled.' required '.$campo_mal['usuario'].'/>
				<br><label><h2 class="admin">N.I.F.*:</h2> </label><input type="text" name="nif" value="' . $valores['nif']. '" size="10" maxlength="9" placeholder="CIF/NIF/NIE" required'.$disabled.' '.$campo_mal['nif'].'/>  
				<br><label><h2 class="admin">Dirección*:</h2> </label><input class="admin_caja" type="text" name="domicilio" value="' . $valores['domicilio']. '" size="74" maxlength="120" placeholder="Direccion" required'.$disabled.'/>
				<br><label><h2 class="admin">CP*:</h2> </label><input class="admin_caja" type="text" name="cp" value="' . $valores['cp']. '" size="7" maxlength="5" pattern="[0-9]{5}" required'.$disabled.'/>
				<br><label><h2 class="admin">Población*:</h2> </label><input class="admin_caja" type="text" name="poblacion" value="' . $valores['poblacion']. '" size="40" maxlength="120" placeholder="Poblacion" required'.$disabled.'/>
				<br><label><h2 class="admin">Provincia*:</h2> </label><input class="admin_caja" type="text" name="provincia" value="' . $valores['provincia']. '" size="40" maxlength="120" placeholder="Provincia" required'.$disabled.'/>			
				<br><label><h2 class="admin">Teléfono 1*:</h2> </label><input class="admin_caja" type="tel" name="telefono1" value="' . $valores['telefono1']. '" size="20" maxlength="18" '.$disabled.' '.$campo_mal['telefono1'].' required placeholder="Preferiblemente móvil"/>
				<br><label><h2 class="admin">Teléfono 2:</h2> </label><input class="admin_caja" type="tel" name="telefono2" value="' . $valores['telefono2']. '" size="20" maxlength="18"'.$disabled.' '.$campo_mal['telefono2'].'/>
				<br><label><h2 class="admin">Móvil:</h2> </label><input class="admin_caja" type="tel" name="movil" value="' . $valores['movil']. '" size="20" maxlength="18" '.$disabled.' '.$campo_mal['movil'].'/>
				<br><label><h2 class="admin">Fax:</h2> </label><input class="admin_caja" type="tel" name="fax" value="' . $valores['fax']. '" size="20" maxlength="18" '.$disabled.' '.$campo_mal['fax'].' /><br>
		';
		if(!$eliminar) { 
			echo '
				<br><h2 class="admin">Datos de envio</h2> <p>(solo en caso de ser diferente a la dirección principal):</p>
				<br><label><h2 class="admin">Destinatario:</h2> </label><input class="admin_caja" type="text" name="nombre_envio" value="' . $valores['nombre_envio']. '" size="30" maxlength="120" placeholder="Nombre fiscal o Nombre y apellidos" />
				<br><label><h2 class="admin">Dirección:</h2> </label><input class="admin_caja" type="text" name="domicilio_envio" value="' . $valores['domicilio_envio']. '" size="74" maxlength="120" placeholder="Direccion" />
				<br><label><h2 class="admin">C.P.:</h2></label><input  class="admin_caja"type="text" name="cp_envio" value="' . $valores['cp_envio']. '" size="7" maxlength="5" pattern="[0-9]{5}" />
				<br><label><h2 class="admin">Población:</h2> </label><input class="admin_caja" type="text" name="poblacion_envio" value="' . $valores['poblacion_envio']. '" size="40" maxlength="120" placeholder="Poblacion" />
				<br><label><h2 class="admin">Provincia:</h2> </label><input class="admin_caja" type="text" name="provincia_envio" value="' . $valores['provincia_envio']. '" size="40" maxlength="120" placeholder="Provincia" />
				<span class="botones">
			';
		}
			if($valores['idcliente']) {
				if($eliminar && $_SESSION['tipo_cliente']=='administrador') echo '
				<div  style="clear: both;"> </div>
		<div id="centrador_web">
					<div style="font-size: 1.2em; color: red;">¿Desea realmente eliminar a este cliente?</div>
					<input type="submit" name="accion" value="Eliminar" class="boton" />
					';
				else echo '<input type="submit" name="accion" value="Modificar"  class="admin" />';
			} else {	
				if($_SESSION['tipo_cliente']!='administrador') echo '<input type="hidden" name="desbloqueo" value="'. md5(rand()) . md5(rand()).'" />';
				echo '<br><input type="submit" name="accion" value="Registrarse" class="admin"  style="margin-top: 20px;"/>';
			}
			if($_SESSION['idcliente'] && $_SESSION['tipo_cliente']!='administrador') echo '	<input type="submit" name="accion" value="Baja" class="admin"  />';
			else '<input type="reset" name="reset" value="Cancelar" class="admin"  />';
			echo '
				</span>
				<span class="leyenda"><br><p>* Campos oblgatorios</p></span>
				<span class="leyenda"><p>* La contraseña ha de ser de más de 6 caracteres, una mayúscula, una misnúscula y un número al menos.</p></span>
			';
			if(count($campo_mal)) echo '<br><span class="leyenda">Los campos en rojo son incorrectos o las contraseñas no coinciden.</span>';
			echo '
			</form>
		';
	}
	
	public function formulario_administrador() 
	{
		echo '
				<form method="get" enctype="multipart/form-data" action="">
					<input type="hidden" name="seccion" value="clientes" class="admin"/>
					<input type="submit" name="subseccion" value="Nuevo" class="admin"/>
					<input type="submit" name="subseccion" value="Buscar" class="admin"/>
					<input type="submit" name="subseccion" value="Pendientes" class="admin"/>
				</form>
			';
	}
	
	public function listado_clientes($clientes=array()) 
	{
		if(count($clientes)>0) {
			
			echo '
				<div class="titulo_admin">Resultados de la búsqueda</div>
				<table>
					<tr>
						<th class="titulos">Id
						<th class="titulos">Nombre
						<th class="titulos">Accion
			';
			foreach($clientes as $cliente){
				//var_dump($cliente);
				echo "
					<tr>
						<td>{$cliente->idcliente_diakros}
						<td>{$cliente->nombre}
						<td>
				";
				echo '
						<form action="" method="post" enctype="multipart/form-data">
							<input type="hidden" name="idcliente" value="'. $cliente->idcliente .'" />
							<button name="accion" value="eliminar"><img src="./img/eliminar.png" height="14" /></button>
							<button name="accion" value="editar"><img src="./img/editar.png" height="14" /></button>
					</form>
				';
			}
			echo '</table>';
		
		} else {
			echo '<center><h4>Lo sentimos pero no hay clientes con los criterios seleccionados.</h4><a href="'.$_SERVER['REQUEST_URI'].'">Volver</a></center>';
		}
	}
	
	public function formulario_busqueda() 
	{
		
		$tipo_cliente = new Combo('idtipo_cliente', 'tipos_clientes', 'idtipo_cliente', 'tipo_cliente', $valores['idtipo_cliente'], true, $filtro = false, $campo_eliminado = false, $campo_orden = 'tipo_cliente', $disabled = false, $visible = true, $required = false, $eventos = null);
		echo '
			<div  class="titulo_admin">Búsqueda de clientes</div>
			<form action="" method="post" enctype="multipart/form-data">
				<label><h2 class="admin">Id Cliente:</h2> </label><input type="text" name="idcliente_diakros" size="5"  class="admin_caja"/><br>
				<label><h2 class="admin">Nombre:</h2> </label><input type="text" name="nombre" size="50"  class="admin_caja"/><br>
				<label><h2 class="admin">Teléfono:</h2> </label><input type="text" name="telf" pattern="[0-9]{3,9}" size="10"  class="admin_caja"/><br>
				<label><h2 class="admin">C.P.:</h2> </label><input type="text" name="cp" pattern="[0-9]{5}" size="6"  class="admin_caja"/><br>
				<label><h2 class="admin">Grupo: </h2></label>
		';
		$tipo_cliente->poner_combo();
		echo '
				<span class="botones">
					<input type="submit" name="accion" value="Buscar" class="boton" />
					<input type="reset" name="reset" value="Cancelar" class="boton" />
				</pan>
			</form>
		';
	}
	
	public function buscar($datos_busq=array())
	{

		$parametros = array();
		$parametros[]="eliminado=0";
		foreach($datos_busq as $dato => $valor) {
			if($dato != 'accion' && $valor) $parametros[] = "$dato='$valor'"; 
		}
		$cadena_busq = implode(" AND ", $parametros);
		//var_dump($parametros);
		
		$sql_busq = "SELECT * FROM clientes WHERE $cadena_busq ORDER by nombre LIMIT 1000;";
		$this->_consulta_clientes->ejecutar_consulta($sql_busq);
		
		$this->listado_clientes($this->_consulta_clientes->registros);
		
	}
	
	public function pendientes()
	{
		$sql_busq = "SELECT * FROM clientes WHERE idcliente_diakros is NULL AND idtipo_cliente = 1 AND desbloqueo is NULL AND eliminado=0  ORDER by nombre LIMIT 1000;";
		$this->_consulta_clientes->ejecutar_consulta($sql_busq);
		$this->listado_clientes($this->_consulta_clientes->registros);
	}
	
	public function introducir_datos($valores=array()) 
	{
		
		$campos_obligatorios=array('nombre','nif','domicilio','cp','poblacion','provincia','usuario','pass');
		$campos_telefono = array('telefono1','telefono2', 'movil', 'fax');

		$campo_mal=array();			
		
		foreach($campos_obligatorios as $campo) {
			//echo "<br>$campo: {$valores[$campo]}";
			$validar = new Validar_datos($campo,$valores[$campo]);
			if($validar->error===false) $valores[$campo]=$validar->formateado;
			else $campo_mal[$campo]=' style="background-color: rgba(255,0,0,0.7)" ';
		}
		
		foreach($campos_telefono as $campo) {
			if($valores[$campo]) {
				$validar = new Validar_datos($campo,$valores[$campo]);
				if($validar->error===false) $valores[$campo]=$validar->formateado['nacional']['dos']['no'];
				else $campo_mal[$campo]= ' style="background-color: rgba(255,0,0,0.7)" ';
			}
		}
		
		if($valores['pass']!=$valores['pass_aux']) {
			$campo_mal['pass']= ' style="background-color: rgba(255,0,0,0.7)" ';
			$campo_mal['pass_aux']= ' style="background-color: rgba(255,0,0,0.7)" ';
		}
		
		if(count($campo_mal)) {
			$this->formulario($valores,false, $campo_mal);
			$this->error = true;
		} else {	
		
			if($valores['accion']=='Registrarse' && $this->_cliente_existe($valores['usuario'],$valores['nif'])) {				
				$this->error = true;
			} else {
				$valores['email']=$valores['usuario'];
				$tabla='clientes';
				$campo_id='idcliente';				
				$insert = true;
				if($valores['accion']=='Modificar')  	$insert = false;
				$sql=new Generar_sql($insert,$tabla, $campo_id, $campos_obligatorios, $valores);
				$sql->sql;
				$this->error = !$this->_consulta_clientes->resultado_consulta($sql->sql);
			}

			$this->resultados();
			
		}
		
	}
	
	public function formulario_eleccion_registro()
	{
		include 'formulario_eleccion_registro.php';
	}
	
	public function eliminar_cliente($idcliente)
	{
		
		if($idcliente) {
			$sql = "UPDATE clientes SET eliminado=1 WHERE idcliente=$idcliente;";
			$this->error = !$this->_consulta_clientes->resultado_consulta($sql);
			$this->resultados();
		} else {
			$this->error = true;
			$this->resultados();
		}
	}
	
	private function _cliente_existe($usuario=null,$nif=null) 
	{
		$sql=array();
		$sql['usuario'] = "SELECT usuario FROM clientes WHERE usuario='$usuario';";
		$sql['nif'] = "SELECT  nif FROM clientes WHERE nif='$nif';";
		$sql['eliminado'] = "SELECT eliminado FROM clientes WHERE eliminado=1 AND (usuario='$usuario' OR nif='$nif');";
		foreach($sql as $campo => $consulta) {
			$this->_consulta_clientes->ejecutar_consulta($consulta);
			if($this->_consulta_clientes->numero_registros>0) {
				if($campo == 'eliminado') echo '<p>Este usuario ha sido borrado. Póngase en contacto con el administrador de la página por correo electrónico. El sistema ha envido un correo al administrador sobre este problema.</p>';
				else echo '<p>Ya existe un usuario registado con este '.$campo.'. Póngase en contacto con al administrador para informar de este problema.</p>';
				return true;
			}
		}
	}
	
	public function resultados() 
	{
		
		if($this->error === false) {
			echo '<p>Operación realizada con éxito.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>'; 
		} else {			
			echo '<p>Hay un error. No se ha realizado ninguna acción.</p><a href="'.$_SERVER['REQUEST_URI'].'" class="admin">Volver</a>';
		}
		
	}
}
