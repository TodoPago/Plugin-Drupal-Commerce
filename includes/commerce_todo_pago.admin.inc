<?php
use TodoPago\Sdk;

include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/commerce_todo_pago_general.php');

function getOrders() {
	$res = db_query("SELECT * FROM {todopago_transaccion}");
	$rows = $res->fetchAll();
	return $rows;
}

function commerce_todo_pago_devolucion_form($form, &$form_state,$order = false) {
	if(isset($_POST["devolucion"])) {

	if(isset($_SESSION["last_form"]) && $_POST["form_build_id"] == $_SESSION["last_form"]) {
		return $_POST["last_form"];
	}

		$payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
		$settings = $payment_method["settings"];
		
		$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
		$http_header = json_decode($settings[$mode]["authorization"],1);
		if($http_header == null) {
			$http_header = array("Authorization" => $settings[$mode]["authorization"]);
		}
		$connector = new Sdk($http_header, $mode);
		
		if ($settings["general"]["modo"] == "Produccion"){
			$modo = "prod";
		}else{
			 $modo = "test";
		}
		if(!empty($_POST['amount'])) {
			$_PÔST['amount'] = number_format($_POST['amount'],2,".","");
		}
		$returnData = array(
			"Security" => $settings[$modo]["security"],
			"Merchant" => $settings[$modo]["idsite"],
			"RequestKey" => _tranRK($order->order_id),
			"AMOUNT" => $_POST['amount'],
		);

TPLog("", "", $settings["general"]["modo"])->info("params DEVOLUCION ".json_encode($returnData));
		$result = $connector->returnRequest($returnData);
TPLog("", "", $settings["general"]["modo"])->info("response DEVOLUCION ".json_encode($result));
		 
		$tabla = '<table cellspacing="1" cellpadding="1"  border="1px">';
		$tabla .= "<tr><td>Estado</td><td>".$result["StatusMessage"]."</td></tr>";
		$tabla .= "</table>";
		
		$form['confirmation'] = array(
			'#title' => 'Todo Pago - Devolucion',
			'#markup' => $tabla,
		);
		$_SESSION["last_form"] = $_POST["form_build_id"];
		$_POST["last_form"] = $form;
		return $form;
	} else {
	$tabla = '<table cellspacing="1" cellpadding="1"  border="1px">';
	$tabla .= "<tr><td>Orden</td><td>".$order->order_number."</td></tr>";
	$tabla .= "<tr><td>Estado</td><td>".$order->status."</td></tr>";
	$tabla .= "<tr><td>Valor</td><td>$".number_format(commerce_currency_amount_to_decimal($order->commerce_order_total[LANGUAGE_NONE][0]["amount"],$order->commerce_order_total[LANGUAGE_NONE][0]["currency_code"]),2,".","") ."</td></tr>";
	$tabla .= '<tr>
				<td>A devolver</td>
				<td><input type="text" class="form-text" name="amount" value="0.00"></input></td>
			   </tr>';
	$tabla .= "</table>";
	$tabla .= '<input id="devolucion-submit" name="devolucion" value="Devolucion online" class="form-submit" type="submit">';
	
	$form['confirmation'] = array(
		'#title' => 'Todo Pago - Devolucion',
		'#markup' => $tabla,
	);
	return $form;
	}
}

function commerce_todo_pago_admin_form($form, &$form_state,$order = false) {

	$payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
	$settings = $payment_method["settings"];

	

	$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";

	//$http_header = json_decode($settings["general"]["authorization"],1);
	$authorization = (isset($settings["general"]["authorization"]))? $settings["general"]["authorization"]:$settings[$mode]["authorization"];	

	$http_header = array("authorization" => $authorization);

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
		$optionsGS = array('MERCHANT'=>$settings[$mode]["idsite"], 'OPERATIONID'=>$order->order_id); 
	 
	    $status = $connector->getStatus($optionsGS);

		$rta = '<tr>';
		$rta .= '<td>';
		$rta .= '<img src="https://portal.todopago.com.ar/app/images/logo.png" alt="Todopago"/>';
		$rta .= '<h3>Estado de la operacion - TodoPago </h3>';
		$rta .= '</td><td></td>';
		$rta .= '</tr>';


		$refunds = $status['Operations']['REFUNDS'];

		$auxArray = array(
		       "REFUND" => $refunds
		       );
		$auxColection  = '';
		if($refunds != null){  
		    $aux = 'REFUND'; 
		    $auxColection = 'REFUNDS';
		}

		if (isset($status['Operations']) && is_array($status['Operations']) ) {
		    $rta .= printGetStatus($status['Operations']);
		}else{
		    $rta .= '<tr><td>No hay operaciones para esta orden.</td></tr>';
		}
	   
		$form['confirmation'] = array(
		'#title' => 'Status Todo Pago',
		'#markup' => '<table cellspacing="1" cellpadding="1"  border="1px">'.$rta."</table>",
		);
		return $form;
	}
}

function printGetStatus($arrayResult, $indent = 0) {
	$rta = '';
	foreach ($arrayResult as $key => $value) {
	    if ($key !== 'nil' && $key !== "@attributes") {
			if (is_array($value) ){
			    $rta .= "<tr>";
			    $rta .= "<td>".str_repeat("-", $indent) . "<strong>$key:</strong></td>";
			    $rta .= "<td>".printGetStatus($value, $indent + 2)."</td>";
			    $rta .= "</tr>";
			} else {
			    $rta .= "<tr><td>".str_repeat("-", $indent) . "<strong>$key:</strong></td><td> $value </td></tr>";
			}
	    }
	}
	return $rta;
}