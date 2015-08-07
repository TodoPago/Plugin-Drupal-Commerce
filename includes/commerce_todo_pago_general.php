<?php
use TodoPago\Sdk;

include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/TodoPago/lib/Sdk.php');
include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/ControlFraude/ControlFraudeFactory.php');

function _phoneSanitize($number){
	$number = str_replace(array(" ","(",")","-","+"),"",$number);
	
	if(substr($number,0,2)=="54") return $number;
	
	if(substr($number,0,2)=="15"){
		$number = substr($number,2,strlen($number));
	}
	if(strlen($number)==8) return "5411".$number;
	
	if(substr($number,0,1)=="0") return "54".substr($number,1,strlen($number));
	return $number;
}
	
function _tranEstado($oid)
{
	$res = db_query("SELECT * FROM {todopago_transaccion} WHERE id_orden=".$oid);
	$res = $res->fetchAssoc();
	if(!$res) {
		return 0;
	} else {	
		if($res['first_step'] == null) {
			return 1;
		} else if ($res['second_step'] == null) {
			return 2;
		} else {
			return 3;
		}
	}
}

function _tranCrear($oid)
{
	$data = array("id_orden" => $oid);
	$query = db_insert("todopago_transaccion");
	$query->fields($data)->execute();
	return _tranEstado($oid);
}

function _tranUpdate($oid, $data)
{
	$query = db_update("todopago_transaccion");
	$query->fields($data)->condition('id_orden',$oid, '=')->execute();
	return _tranEstado($oid);
}

function _tranRK($oid)
{
	$res = db_query("SELECT * FROM {todopago_transaccion} WHERE id_orden=".$oid);
	$res = $res->fetchAssoc();
	return $res['request_key'];
}

function first_step_todopago($form, &$form_state, $order, $payment_method)
{
	global $pane_values,$user;
	logInfo($order->order_id,'first step');
	
	if(_tranEstado($order->order_id) == 0) 
		_tranCrear($order->order_id);
	
	$vertical = $payment_method["settings"]["general"]["segmento"];
	switch($vertical) {
		case "Retail":
			$vertical = ControlFraudeFactory::RETAIL;
			break;
		case "Ticketing":
			$vertical = ControlFraudeFactory::TICKETING;
			break;		
		case "Services":
			$vertical = ControlFraudeFactory::SERVICE;
			break;		
		case "Digital_Goods":
			$vertical = ControlFraudeFactory::DIGITAL_GOODS;
			break;		
	}
	$dataFraude = ControlFraudeFactory::get_controlfraude_extractor($vertical, $user, $order)->getDataCS();
	
	$form = array_merge($form,$dataFraude);
	
	 foreach($form as $key=>$value){
		$optionsSAR_operacion[$key] =$value["#value"];   
	 }

	$monto= commerce_currency_amount_to_decimal($order->commerce_order_total[LANGUAGE_NONE][0]["amount"],$order->commerce_order_total[LANGUAGE_NONE][0]["currency_code"]);

	$settings = $payment_method["settings"];

	if ($settings["general"]["modo"] == "Produccion"){
		$modo = "ambienteproduccion";
	}else{
		$modo = "ambientetest";
	}

	$http_header = json_decode($settings["general"]["authorization"],1);
	$http_header["user_agent"] = 'PHPSoapClient';

	$optionsSAR_comercio = array (
		'Security'=>$settings[$modo]["security"],
		'EncodingMethod'=>'XML',
		'Merchant'=>$settings[$modo]["idsite"],
		'URL_OK'=>url('commerce/todopago/notification/'.$order->order_id."/". $order->data['payment_redirect_key'], array('absolute' => TRUE)),
		'URL_ERROR'=>url('commerce/todopago/notification/'.$order->order_id."/". $order->data['payment_redirect_key'], array('absolute' => TRUE)),
	);

	$optionsSAR_operacion["MERCHANT"] = $settings[$modo]["idsite"];
	$optionsSAR_operacion["OPERATIONID"] =$order->order_id;
	$optionsSAR_operacion["CURRENCYCODE"]	=032;
	$optionsSAR_operacion["AMOUNT"]	=$monto;

	$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
	//creo el conector con el valor de Authorization, la direccion de WSDL y endpoint que corresponda
	$connector = new Sdk($http_header, $mode);
		
	logInfo($order->order_id,'params SAR',array($optionsSAR_comercio, $optionsSAR_operacion));
	$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
	logInfo($order->order_id,'response SAR',$rta);
	
	if ($rta['StatusCode']  != -1)//Si la transacción salió mal
	{
		if(($rta['StatusCode']  == 702)&&(!property_exists($order,"first_step"))) {
			$authorization = json_decode($settings["general"]["authorization"],1);
			$merchant = $settings[$modo]["idsite"];
			$security = $settings[$modo]["security"];
			if((isset($authorization["Authorization"]))&&(!empty($merchant))&&(!empty($security))){
				$order->first_step = true;
				first_step_todopago($form, $form_state, $order, $payment_method);
			}
		}
		throw new Exception($rta['StatusMessage']);
	}

	$now = new DateTime();
	_tranUpdate($order->order_id, array("first_step" => $now->format('Y-m-d H:i:s'), "params_SAR" => json_encode(array($optionsSAR_comercio, $optionsSAR_operacion)), "response_SAR" => json_encode($rta), "request_key" => $rta['RequestKey'], "public_request_key" => $rta['PublicRequestKey']));
	
	$form['#action'] = $rta["URL_Request"];
    
	$form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Continuar a Todo Pago'),
			'#weight' => 50,
	);
	return $form;
}

function second_step_todopago($order, $return, $user, $ak)
{
	logInfo($order,'second step');
    
	if(_tranEstado($order) != 2)
	{
		throw new Exception("second_step ya realizado");
	}	
    $payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
    $settings = $payment_method["settings"];
    if ($settings["general"]["modo"] == "Produccion"){
        $modo = "ambienteproduccion";
    }else{
         $modo = "ambientetest";
    }

    $optionsGAA = array (     
        'Security'=>$settings[$modo]["security"],
    	'Merchant'=>$settings[$modo]["idsite"],   
        'RequestKey' => _tranRK($order),       
        'AnswerKey'  => $ak      
    );      

	$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
    $http_header = json_decode($settings["general"]["authorization"],1);
    $http_header["user_agent"] = 'PHPSoapClient';	

	$connector = new Sdk($http_header, $mode);	
	logInfo($order,'params GAA',$optionsGAA);	
    $rta2 = $connector->getAuthorizeAnswer($optionsGAA);
	logInfo($order,'response GAA',$rta2);	
	
	$now = new DateTime();
	_tranUpdate($order, array("second_step" => $now->format('Y-m-d H:i:s'), "params_GAA" => json_encode($optionsGAA), "response_GAA" => json_encode($rta2), "answer_key" => $ak));
	
    $order = commerce_order_load($order);
    if ($rta2["StatusCode"]=="-1"){
        $status = $settings["status"]["aprobada"];
        $transaction = commerce_payment_transaction_new('bank_transfer', $order->order_id);
        $transaction->instance_id = $payment_method['instance_id'];
        $transaction->amount = $rta2["Payload"]["Request"]["AMOUNT"]*100;
        $transaction->status = COMMERCE_PAYMENT_STATUS_SUCCESS;
        $transaction->payload = print_r($rta2["Payload"],1);
        $transaction->remote_id = $rta2["Payload"]["Answer"]["OPERATIONID"];
        commerce_payment_transaction_save($transaction);
        commerce_order_status_update($order, $status);
        drupal_goto(commerce_checkout_order_uri($order));
    }else{
        if ($rta2["Payload"]["Answer"]["BARCODETYPE"] !=""){
            $transaction = commerce_payment_transaction_new('bank_transfer', $order->order_id);
            $transaction->instance_id = $payment_method['instance_id'];
            $transaction->amount = $rta2["Payload"]["Request"]["AMOUNT"]*100;
            $transaction->status =   COMMERCE_PAYMENT_STATUS_PENDING;
            $transaction->payload = print_r($rta2["Payload"],1);
            $transaction->remote_id = $rta2["Payload"]["Answer"]["OPERATIONID"];
            commerce_payment_transaction_save($transaction);
            $status = $settings["status"]["offline"];
?>
        
            <div id="content" style="width: 75%;">
	     	<div><div class="titulos">Nro de Operaci&oacute;n:</div><em><strong><?php echo $order->order_id ?></strong></em><hr></div>
            <div><div class="titulos">Total a pagar</div>$ <?php echo $rta2["Payload"]["Request"]["AMOUNT"].".-" ?><hr></div>
        	<div class="titulos"><h3>DATOS PERSONALES<h3><hr></div>
	        <div><div class="titulos">Nombre</div> <?php echo $user->name?> <hr></div>
<?php 
            if ($rta2["Payload"]["Answer"]["PAYMENTMETHODNAME"] == "PAGOFACIL"){
                $empresa = "PAGO FACIL";
            }else{
                $empresa = "RAPIPAGO";
            }
            $barcode="12345678";
            if (!empty($rta2["Payload"]["Answer"]["BARCODE"])){
               $barcode = $rta2["Payload"]["Answer"]["BARCODE"];
            }
?>
	       <div><div class="titulos">Podr&aacute;s pagar este cup&oacute;n en los locales de:</div><?php echo $empresa ?><hr></div>
<?php
            echo "<img   src='".$base_path.drupal_get_path('module', 'commerce_todo_pago')."/includes/image.php?filetype=PNG&dpi=72&scale=5&rotation=0&font_family=Arial.ttf&font_size=8&text=".$barcode."&thickness=30&checksum=&code=BCGi25&' />";

?>
            <br /> 
            	<div class="right">
            		<input type="button" name="imprimir" value="Imprimir" onclick="window.print();" class="button">
            		<a href="<?php echo commerce_checkout_order_uri($order)?>">Click aca para continuar.</a>
            	</div>
            	<br />
            </div>
<?php
            commerce_order_status_update($order, $status);
        }else{
			_tranUpdate($order->order_id, array("first_step" => null, "second_step" => null));
			if($rta2["StatusCode"]==404) {
				drupal_goto('<front>');
				return;
			}
            $status = $settings["status"]["rechazada"];
            commerce_order_status_update($order, $status);
            drupal_set_message(t('Hubo un error en la transaccion, intente nuevamente'), 'error');
            commerce_payment_redirect_pane_previous_page($order);
            drupal_goto(commerce_checkout_order_uri($order));
        }   
    }
	
}