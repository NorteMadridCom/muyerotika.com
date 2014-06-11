<?php 
 
require_once('pedido_pdf.php');


$factura=new Generar_pdf;
$factura->Genera_pdf('8');


?> 