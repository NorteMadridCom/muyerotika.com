<?php

class Dto_prioritario
{
	private $_idproducto;
	private $_dto_pri = object;

	public function __construct($idproducto) 
	{
		$this->_dto_pri = new Mysql;
		$this->_idproducto = $idproducto;
		//$this->listar_dtos();
	}	
	
	public function listar_dtos()
	{
		echo '
			<fieldset style= "border-radius: 10px;">
				<legend>Descuentos Prioritarios</legend>
					<table>
						<tr>
							<th width="290">Grupo 
							<th width="90">Dto(%) 
							<th>Acción
						<tr>
		';
		$sql = "SELECT dp.*, tc.tipo_cliente FROM dtos_prioritarios dp, tipos_clientes tc WHERE dp.idproducto={$this->_idproducto} AND dp.idtipo_cliente=tc.idtipo_cliente;";
		$this->_dto_pri->ejecutar_consulta($sql);
		if($this->_dto_pri->numero_registros>0 && is_array($this->_dto_pri->registros)) {
			$tipos=array();
			foreach($this->_dto_pri->registros as $dto) {
				echo '
					<tr>
						<td>'.$dto->tipo_cliente.'
						<td>'.$dto->dto_prioritario.'
						<td>
							<form action="" method="post" enctype="multipart/form-data">
								<input type="hidden" name="idproducto" value="'.$dto->idproducto.'" />
								<input type="hidden" name="idtipo_cliente" value="'.$dto->idtipo_cliente.'" />
								<button name="accion" value="quitar_dto_pri"><img src="./img/eliminar.png" height="16" /></button>
							</form>		
				';
				$tipos[]=' idtipo_cliente != '.$dto->idtipo_cliente.' ';
			}	
		}
		
		$this->_dto_pri->ejecutar_consulta("SELECT COUNT(*) as total_clientes FROM tipos_clientes;");
		//echo '<tr><td>'.$this->_dto_pri->registros[0]->total_clientes;
		if($this->_dto_pri->registros[0]->total_clientes > count($tipos)) {
			$filtro='';
			if(count($tipos)>0) $filtro=' ('. implode(' OR ', $tipos) . ') ';
			
			echo '
				<form action="" method="post" enctype="multipart/form-data">
					<tr>
						<td>';
			$tipos_clientes = new Combo('idtipo_cliente','tipos_clientes','idtipo_cliente','tipo_cliente', false, true, $filtro);
			$tipos_clientes->poner_combo();
			echo '	
						<td><input type="text" name="dto_prioritario" />
						<td>
							<input type="hidden" name="idproducto" value="'.$this->_idproducto.'" />
							<button name="accion" value="poner_dto_pri"><img src="./img/agregar.png" height="16" /></button>
				</form>
			';
		}
		echo '
				</table>
			</fieldset>		
		';	
	}
	
	public function dto_pri() 
	{
		$idtipo_cliente=1; //valor por defecto
		$dto_pri=false; //valor por defecto
		if($_SESSION['idtipo_cliente']) $idtipo_cliente=$_SESSION['idtipo_cliente'];
		$sql = "SELECT dp.dto_prioritario FROM dtos_prioritarios dp WHERE dp.idproducto={$this->_idproducto} AND dp.idtipo_cliente=$idtipo_cliente;";
		$this->_dto_pri->ejecutar_consulta($sql);
		if($this->_dto_pri->numero_registros==1) {
			$dto_pri=$this->_dto_pri->registros[0]->dto_prioritario;
			return 1-($dto_pri/100); 
		} else {
			return false;
		}
	}

}

?>
