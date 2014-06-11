<?php 
$pedido = str_pad($_SESSION['datos_cesta']['no_pedido'], 12, "0", STR_PAD_LEFT);
?>


<table class="cesta_compra" style="margin-top: 50px; margin-left: 250px;">	
	<tr>
		<td style="text-align: left;  width: 120px; font-weight: bold;">Número de pedido:</td>
		<td style="width:150px;"> <?php echo $pedido; ?></td>
	</tr>
	<tr>
		<td style="text-align: left;  width: 120px;  font-weight: bold;">Importe:</td>
		<td style="width: 150px;"><?php echo $total; ?> €</td>
	</tr>
		
			
<?php

	//constantes para la plataforma
	
	define('url_tpvv', 'https://sis-t.redsys.es:25443/sis/realizarPago');
	define('currency', '978');
	define('terminal', '001');
	define('clave', 'qwertyasdf0123456789');
	define('code', '045515749');
	define('urlMerchant', 'https://www.dizma.es/respuesta_pago.php');
	define('transactionType', '0'); //autorizacion
	define('urlOK', 'https://www.dizma.es/index.php?seccion=pago&respuesta=ok');
	define('urlKO', 'https://www.dizma.es/index.php?seccion=pago&respuesta=ko');

	//variables
	//$total;
	
	$total = $total*100;

	// Compute hash to sign form data
	// $signature=sha1_hex($amount,$order,$code,$currency,$clave);
	$message = $total . $pedido . code . currency . transactionType . urlMerchant . clave;
	$signature = strtoupper(sha1($message));
	
	

?>

<tr>
	<td>
<form name="compra" action="<?php echo url_tpvv; ?>" method="post">
	<input type="hidden" name="Ds_Merchant_Amount" value="<?php echo $total; ?>">
	<input type="hidden" name="Ds_Merchant_Currency" value="<?php echo currency; ?>">
	<input type="hidden" name="Ds_Merchant_Order"  value="<?php echo $pedido; ?>">
	<input type="hidden" name="Ds_Merchant_MerchantCode" value="<?php echo code; ?>">
	<input type="hidden" name="Ds_Merchant_Terminal" value="<?php echo terminal; ?>">
	<input type="hidden" name="Ds_Merchant_TransactionType" value="<?php echo transactionType; ?>">
	<input type="hidden" name="Ds_Merchant_MerchantURL" value="<?php echo urlMerchant; ?>">
	<input type="hidden" name="Ds_Merchant_MerchantSignature" value="<?php echo $signature; ?>">
	<input type="hidden" name="Ds_Merchant_UrlOK" value="<?php echo urlOK; ?>">
	<input type="hidden" name="Ds_Merchant_UrlKO" value="<?php echo urlKO; ?>">
	<p><img src="img/mastercard.png" /> <img src="img/visa.png"/> <img src="img/4bi.png"/> </p><input type="submit" name="accion" value='Tarjeta Bancaria' id="tarjeta" />
</form>	
	</td>	

	<td>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" >
		<input type="hidden" name="seccion" value="pago" />
		 <p style="font-size: 12px; font-weight: bold;"> Banco Popular: 0075 0602 45 0600086764</p>
		 <input type="submit" name="respuesta" value="Transferencia" id="transferencia" />
		
</form>	
</td>							  
</tr>
</table>
