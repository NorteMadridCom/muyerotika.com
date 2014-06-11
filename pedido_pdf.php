<?php 
 
// libreria para escribir un  PDF
require_once 'includes/fpdf.php';
require_once 'includes/class.comprobar_datos.php';
require_once 'includes/mysql.php';
 
// libreria para importar documentos dentro de FPDF
//require_once('includes/fpdi/fpdi.php');
//No hace falta de momento

class Generar_pdf
{
	
	public $error= false;
	public $error_info = null;
	
//Variables
//public $pedido = 1;
 
private $_inicio_x= 15;
private $_inicio_y_tabla = 74; 
//private $this->_fila = $this->_inicio_y_tabla + 8;
private $_fila = 82;
//private $neto=0;
private $_grosor_linea = 5;



private function cabecera($pdf,$datos)
{


//Datos Dizma
$Nombre_fiscal = "Dizma S.L.";
$CIF = "B28793024"; 
$Direccion = "C/. San Mateo, 30 Local"; 
$CP = "28004"; 
$Localidad = "Madrid"; 
$Provincia = "Madrid"; 
$Telefono = "913190337"; 
$Email = "comercial@dizma.es";


//Anadimos logo
$pdf->image('img/logo.jpg', $this->_inicio_x,10,55,30,'JPG');

$fecha = new Validar_datos('fecha', $datos->registros[0]->fecha);

//Datos Pedido

	//id_pedido
	$pdf->SetFont('Times','B',12);
	$pdf->SetXY($this->_inicio_x,42 );
	$pdf->Cell(15,7,"Pedido:",20,0,"L",false);

	$pdf->SetFont('Times','',12);
	$pdf->SetXY($this->_inicio_x+15, 42);
	$pdf->Cell(22,7,$datos->registros[0]->idpedido,20,0,"L",false);
	
	//fecha
	$pdf->SetFont('Times','B',12);
	$pdf->SetXY($this->_inicio_x,47 );
	$pdf->Cell(15,7,"Fecha:",20,0,"L",false);

	$pdf->SetFont('Times','',12);
	$pdf->SetXY($this->_inicio_x+15, 47);
	$pdf->Cell(22,7,$fecha->formateado,20,0,"L",false);
	
	//fecha
	$pdf->SetFont('Times','B',12);
	$pdf->SetXY($this->_inicio_x,52 );
	$pdf->Cell(15,7,"Página:",20,0,"L",false);

	$pdf->SetFont('Times','',12);
	$pdf->SetXY($this->_inicio_x+15, 52);
	$pdf->Cell(22,7,$pdf->PageNo(),20,0,"L",false);



	// seteamos la fuente, el estilo y el tamano 
 	$pdf->SetFont('Times','',12);

 
 
//Datos Cliente y datos Dizma
 
	$datos_dizma= $Nombre_fiscal . "\n".  $CIF . "\n". str_pad($Direccion,60) . "\n". $CP . "\n". $Localidad . "\n". $Provincia . "\n". $Telefono . "\n". $Email . " "; //str_pad($Email,100);

//Creamos la variable con los datos del cliente formateados por saltos de línea

	$muestra_datos_cliente = $datos->registros[0]->nombre . "\n".  $datos->registros[0]->cif . "\n". str_pad($datos->registros[0]->direccion,60) . "\n". $datos->registros[0]->cp . "\n". $datos->registros[0]->localidad . "\n". $datos->registros[0]->provincia . "\n". $datos->registros[0]->telefono1 . "\n". $datos->registros[0]->usuario ." ";//. str_pad($datos->registros[0]->usuario,100);
	
	//Posicion y tabla con datos cliente
	$pdf->SetXY(75, 15);
	$pdf->MultiCell(57,5,$muestra_datos_cliente ,1); 
	
	//Posicion y tabla con datos dizma
	$pdf->SetXY(135, 15);
	$pdf->MultiCell(57,5,$datos_dizma,1,1); 
	/*
	// seteamos la fuente, el estilo y el tamano para el mail 
 	$pdf->SetFont('Times','',9);
 	$pdf->SetXY(75, 53);
 	$pdf->Cell(22,7,$datos->registros[0]->usuario,20,0,"L",false);
 	
 	$pdf->SetFont('Times','',9);
 	$pdf->SetXY(135, 53);
 	$pdf->Cell(22,7,$Email,20,0,"L",false);
	*/
	
	//Cabecera tabla pedido, estos valores se meten a fuego porque serán fijos

	//seteamos fuente y color de fondo
	$pdf->SetFont('Times','B',12);
	$pdf->SetFillColor(147,147,147);
	
	
	//Cod
	$pdf->SetXY($this->_inicio_x, $this->_inicio_y_tabla);
	$pdf->Cell(13,7,"Cód.",20,0,"C",true);
	
	
	//Articulo
	$pdf->SetXY($this->_inicio_x+14, $this->_inicio_y_tabla);
	$pdf->Cell(85,7,"Artículo",20,0,"C",true);
	
	//Uds
	$pdf->SetXY($this->_inicio_x+100, $this->_inicio_y_tabla);
	$pdf->Cell(14,7,"Uds.",20,0,"C",true);
	
	//Valor
	$pdf->SetXY($this->_inicio_x+115, $this->_inicio_y_tabla);
	$pdf->Cell(19,7,"Valor(€)",20,0,"C",true);
	
	//$this->cell(10,10,iconv("UTF-8", "ISO-8859-1", "£"). $money,0);
	
	
	//Dto
	$pdf->SetXY($this->_inicio_x+135, $this->_inicio_y_tabla);
	$pdf->Cell(19,7,"Dto(%)",20,0,"C",true);
	
	//Precio
	$pdf->SetXY($this->_inicio_x+155, $this->_inicio_y_tabla);
	$pdf->Cell(22,7,"Precio",20,0,"C",true);
	
	


}
////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////	
private function pie($pdf,$datos)
{
$this->_fila = 220;
$fila_pie = $this->_fila;

/*

	//$this->_fila = 235;
	//Neto
	$pdf->SetFont('Times','B',12);
	$pdf->SetXY(135, $this->_fila);
	$pdf->Cell(22,7,"Neto:",20,0,"R",false);
	//Neto Valor
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(170, $this->_fila);
	$pdf->Cell(22,7,number_format($datos->registros[0]->neto,2,"."," "),20,0,"R",true);

	//Neto Dto por volumen
	$pdf->SetFont('Times','B',12);
	$this->_fila = $this->_fila + 8;
	$pdf->SetXY(135, $this->_fila);
	$pdf->Cell(22,7,"Neto Dto por volumen:",20,0,"R",false);


	$dto_volumen = $datos->registros[0]->dto_volumen;
	
	//Si el dto_volumen es null no ponemos el valor del redondeo en la posición
	if (!$dto_volumen){
		$pdf->SetFont('Times','',12);
		$pdf->SetXY(158, $this->_fila);
		$pdf->Cell(11,7," ",20,0,"C",true);
		
		
		$pdf->SetFont('Times','',12);
		$pdf->SetXY(170, $this->_fila);
		$pdf->Cell(22,7," ",20,0,"C",true);
		}

	else{
	
		$pdf->SetFont('Times','',12);
		$pdf->SetXY(158, $this->_fila);
		$pdf->Cell(11,7,number_format($dto_volumen,2,"."," "),20,0,"R",true);
		
		
		$neto_dto = ($dto_volumen /100) * $datos->registros[0]->neto;
		$pdf->SetFont('Times','',12);
		$pdf->SetXY(170, $this->_fila);
		$pdf->Cell(22,7,number_format($neto_dto,2,"."," "),20,0,"R",true);
		}
	
	*/
	//Gastos de envio
	$pdf->SetFont('Times','B',12);
	$this->_fila = $this->_fila + 8;
	$pdf->SetXY(135, $this->_fila);
	$pdf->Cell(22,7,"Gastos de envío:",20,0,"R",false);

	$pdf->SetFont('Times','',12);
	$pdf->SetXY(170, $this->_fila);
	$pdf->Cell(22,7,number_format($datos->registros[0]->gastos_envio*1.21,2,"."," "),20,0,"R",true);
	/*
	//IVA
	$pdf->SetFont('Times','B',12);
	$this->_fila = $this->_fila + 8;
	$pdf->SetXY(135, $this->_fila);
	$pdf->Cell(22,7,"IVA:",20,0,"R",false);
	
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(158, $this->_fila);
	$pdf->Cell(11,7,number_format($datos->registros[0]->iva,2,"."," "),20,0,"R",true);

	$iva = ($datos->registros[0]->iva /100) * $datos->registros[0]->neto;
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(170, $this->_fila);
	$pdf->Cell(22,7,number_format($iva,2,"."," "),20,0,"R",true);
	*/
	//TOTAL
	$pdf->SetFont('Times','B',12);
	$this->_fila = $this->_fila + 8;
	$pdf->SetXY(135, $this->_fila);
	$pdf->Cell(22,7,"TOTAL:",20,0,"R",false);

	//$pdf->SetFont('Times','',12);
	$pdf->SetXY(170, $this->_fila);
	$pdf->Cell(22,7,number_format($datos->registros[0]->total,2,"."," "),20,0,"R",true);


//Observaciones

	
	$pdf->SetXY(15, $fila_pie);
	$pdf->Cell(22,7,"Observaciones:",20,0,"L",false);
	
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(15, $fila_pie + 8);
	
	$observaciones = str_pad($datos->registros[0]->observaciones,255);
	
	$pdf->MultiCell(82,7,$observaciones,1,"L",false);
	
//Datos envio
	
	$muestra_datos_envio = $datos->registros[0]->nombre_envio . "   ". $datos->registros[0]->direccion_envio . "   ". $datos->registros[0]->cp_envio . "   ". $datos->registros[0]->localidad_envio . "   ". $datos->registros[0]->provincia_envio;
	
	//Posicion y tabla con datos de envio
	$pdf->SetXY(15, $this->_fila+22 );
	$pdf->SetFont('Times','B',12);
	$pdf->Cell(22,7,"Datos envío:",20,0,"L",false);
	
	$pdf->SetXY(15, $this->_fila+30);
	$pdf->SetFont('Times','',12);
	$pdf->MultiCell(178,7,$muestra_datos_envio ,1); 

}

////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////	

public function Genera_pdf($pedido) {

	//sacamos los datos necesarios de la base de datos
	

		$sql="select p.idpedido, p.fecha, p.nombre, p.cif, p.direccion, p.cp, p.localidad, p.provincia, c.telefono1, c.usuario, p.nombre_envio, p.direccion_envio, p.cp_envio, p.localidad_envio, p.provincia_envio, p.neto, p.dto_volumen, p.gastos_envio, p.iva, p.total, p.observaciones, p.pass 
		from pedidos p ,clientes c
		where p.idpedido = " .$pedido. "
		and p.eliminado = 0 
		and c.idcliente =p.idcliente;";

		
		$datos=new Mysql;
		$datos->ejecutar_consulta($sql);
		
		
		$sql="select * from detalles_pedidos where idpedido =  " .$pedido. ";";
		$detalles=new Mysql;
		$detalles->ejecutar_consulta($sql);
		
		if (is_null($datos->registros[0]->idpedido))
			{
			 $error = true;
			 $error_info = 'No existe el pedido';
			}
		
		
		//var_dump($datos);




	//Creamos pdf
	$pdf = new FPDF();
	 
	
	 //anadimos pagina
	 $pdf->AddPage();
	
	//Ponemos cabecera
	$this->cabecera($pdf,$datos);
	
	
	//TABLA DETALLES
	
	
	//Cabecera tabla pedido, estos valores se meten a fuego porque serán fijos

	//seteamos fuente y color de fondo
	$pdf->SetFont('Times','',9);
	$pdf->SetFillColor(220,220,220);
	
	foreach($detalles->registros as $deta)
		{
			$pdf->SetFont('Times','',9);			
			//Cod
			$pdf->SetXY($this->_inicio_x, $this->_fila);
			$pdf->Cell(13,$this->_grosor_linea,$deta->cod_producto,20,0,"C",true);
			
			
			//Articulo
			$pdf->SetXY($this->_inicio_x+14, $this->_fila);
			$pdf->MultiCell(85,$this->_grosor_linea,$deta->producto,0,"L",true);
			
			//Uds
			$pdf->SetXY($this->_inicio_x+100, $this->_fila);
			$pdf->Cell(14,$this->_grosor_linea,$deta->uds,20,0,"C",true);
			//$pdf->Cell(14,$this->_grosor_linea,$this->_fila,20,0,"R",true);
			
			//Valor
			
			//number_format(12845.98123,1,".",",")
			$pdf->SetXY($this->_inicio_x+115, $this->_fila);
			$pdf->Cell(19,$this->_grosor_linea,number_format($deta->precio*1.21,2,"."," "),20,0,"R",true);
			
			
			//Dto
			$pdf->SetXY($this->_inicio_x+135, $this->_fila);
			$pdf->Cell(19,$this->_grosor_linea,number_format((1-$deta->dto)*100,2,"."," "),20,0,"R",true);
			
			//$precio= ($deta->precio - (($deta->dto/100)*$deta->precio))*$deta->uds*1.21 ;
			$precio = $deta->precio * $deta->uds * 1.21 * $deta->dto;
			//$neto= $neto + $precio;
			
			//Precio
			$pdf->SetXY($this->_inicio_x+155, $this->_fila);
			$pdf->Cell(22,$this->_grosor_linea,number_format($precio,2,"."," "),20,0,"R",true);
			/*
			//Al reducir el tamaño de la fuente ya no es necesario ampliar el tamaño del detalle a dos filas
			if(strlen($deta->producto) > 39 ){
				$this->_fila = $this->_fila + 15;
				}
				
			else{
				$this->_fila = $this->_fila + 8;
				}
			*/
			$this->_fila = $this->_fila + 6;
			if($this->_fila >= 220 ){
				$pdf->AddPage();
				$this->cabecera($pdf,$datos);
				$this->_fila = $this->_inicio_y_tabla + 8;
				$pdf->SetFont('Times','',12);
				$pdf->SetFillColor(220,220,220);
				}
		}
	
	//Resultados
	
	$this->pie($pdf,$datos);
	
	
	//Guardamos el fichero

	
	$pdf->Output("pedidos/Pedido_" . $datos->registros[0]->idpedido .'_'. $datos->registros[0]->pass . ".pdf",'F');
	
	return "pedidos/Pedido_" . $datos->registros[0]->idpedido .'_'. $datos->registros[0]->pass . ".pdf";
	//para ver la salida sin tener que abirlo constantemente
	//$pdf->Output();

}//genera_pdf

}//de la clase
?>
