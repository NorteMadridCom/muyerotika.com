<?php

abstract class Gastos_envio
{

	public function run()
	{
		if(is_numeric($_SESSION['datos_cesta']['neto_total'])) $neto = round($_SESSION['datos_cesta']['neto_total'],2);
		else $neto=0;
		//$neto=0;
		$sql="SELECT MAX(gastos_envio) as envio FROM gastos_envios WHERE hasta_neto > $neto;";
		$gastos = new Mysql;
		$gastos->ejecutar_consulta($sql);
		if($gastos->numero_registros) $envio=$gastos->registros[0]->envio;
		else $envio=0;
		echo $_SESSION['datos_cesta']['envio']=$envio;
		//return $envio;
	}


}
