<?php
use TodoPago\Sdk;

function getOrders() {
	$res = db_query("SELECT * FROM {todopago_transaccion}");
	$rows = $res->fetchAll();
	return $rows;
}

function commerce_todo_pago_devolucion_form($form, &$form_state,$order = false) {

	if(isset($_POST["devolucion"])) {
		$payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
		$settings = $payment_method["settings"];
		
		$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
		$http_header = json_decode($settings["general"]["authorization"],1);
		if($http_header == null) {
			$http_header = array("Authorization" => $settings["general"]["authorization"]);
		}
		$connector = new Sdk($http_header, $mode);
		
		if ($settings["general"]["modo"] == "Produccion"){
			$modo = "ambienteproduccion";
		}else{
			 $modo = "ambientetest";
		}
		
		$returnData = array(
			"Security" => $settings[$modo]["security"],
			"Merchant" => $settings[$modo]["idsite"],
			"RequestKey" => _tranRK($order->order_id),
			"AMOUNT" => number_format($_POST['amount'],2,".",""),
		); 
		$result = $connector->returnRequest($returnData);
		 
		$tabla = '<table cellspacing="1" cellpadding="1"  border="1px">';
		$tabla .= "<tr><td>Estado</td><td>".$result["StatusMessage"]."</td></tr>";
		$tabla .= "</table>";
		
		$form['confirmation'] = array(
			'#title' => 'Todo Pago - Devolucion',
			'#markup' => $tabla,
		);
		return $form;
	}
	
	$tabla = '<table cellspacing="1" cellpadding="1"  border="1px">';
	$tabla .= "<tr><td>Orden</td><td>".$order->order_number."</td></tr>";
	$tabla .= "<tr><td>Estado</td><td>".$order->status."</td></tr>";
	$tabla .= "<tr><td>Valor</td><td>$".number_format(commerce_currency_amount_to_decimal($order->commerce_order_total[LANGUAGE_NONE][0]["amount"],$order->commerce_order_total[LANGUAGE_NONE][0]["currency_code"]),2,".","") ."</td></tr>";
	$tabla .= '<tr><td>A devolver</td><td><input type="text" class="form-text" name="amount" value="'.number_format(commerce_currency_amount_to_decimal($order->commerce_order_total[LANGUAGE_NONE][0]["amount"],$order->commerce_order_total[LANGUAGE_NONE][0]["currency_code"]),2,".","") .'"></input></td></tr>';
	$tabla .= "</table>";
	$tabla .= '<input id="devolucion-submit" name="devolucion" value="Devolucion online" class="form-submit" type="submit">';
	
	$form['confirmation'] = array(
		'#title' => 'Todo Pago - Devolucion',
		'#markup' => $tabla,
	);
	return $form;

}

function commerce_todo_pago_admin_form($form, &$form_state,$order = false) {

	$payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
	$settings = $payment_method["settings"];

	if ($settings["general"]["modo"] == "Produccion"){
		$modo = "ambienteproduccion";
		
	}else{
		 $modo = "ambientetest";
	}

	$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
	$http_header = json_decode($settings["general"]["authorization"],1);
	$http_header["user_agent"] = 'PHPSoapClient';	

	$connector = new Sdk($http_header, $mode);	

	if($order === false){
		$orders = getOrders();
		$tabla = '<table cellspacing="1" cellpadding="1"  border="1px">';
		$tabla .= "<tr><td>Ord.</td><td>Estado</td><td></td><td></td></tr>";
		foreach($orders as $ord) {
			$orden = commerce_order_load($ord->id_orden);
			$tabla .= "<tr><td>".$ord->id_orden."</td><td>".$orden->status."</td><td><a href='?q=admin/commerce/orders/".$ord->id_orden."/todo_pago'>Detalle</a></td><td><a href='?q=admin/commerce/orders/".$ord->id_orden."/todo_pago_devolucion'>Realizar una devolucion</a></td></tr>";
		}
		$tabla .= "</table>";
		$form['confirmation'] = array(
			'#title' => 'Status Todo Pago',
			'#markup' => $tabla,
		);
		return $form;			
	} else {
		$optionsGS = array('MERCHANT'=>$settings[$modo]["idsite"], 'OPERATIONID'=>$order->order_id); 
	  
	  
	    $rta4 = $connector->getStatus($optionsGS);   
		if (isset($rta4["Operations"])){
			$tabla = "";
			
			//cambios en get status
			foreach($rta4['Operations'] as $key => $value){
				
				if($key == "REFUNDS"){	
					$tabla .= "<tr><td style='vertical-align: top;''>".$key."</td>";
					if(is_array($value)){
						$tabla .= "<td><table width='100%'>";
						$tabla .= "<tr><td>Order ID</td><td>Amount</td><td>Date</td></tr>";
						foreach ($value as $key => $refundsList){
							foreach($refundsList as $key => $value){
								$tabla .= "<tr><td>".$value['ID']."</td><td>".$value['AMOUNT']."</td><td>".$value['DATE']."</td></tr>";
							}
						}
						$tabla .= "</table></td>";
					}else{
						$tabla .= "No tiene datos<br>";
					}
					$tabla .= "</tr>";
					
				}else{
					$tabla .= "<tr><td>".$key."</td><td>".$value."</td></tr>";
				}
			}	

		}else{
			$tabla = "No hay operaciones para consultar";
		}
	   
	  $form['confirmation'] = array(
		'#title' => 'Status Todo Pago',
		'#markup' => '<table cellspacing="1" cellpadding="1"  border="1px">'.$tabla."</table>",
	  );
	  return $form;
	}
}

