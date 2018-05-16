<?php
use TodoPago\Sdk;

include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/ControlFraude/ControlFraudeFactory.php');
include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/Logger/logger.php');

include_once(drupal_get_path('module', 'commerce_todo_pago').'/vendor/autoload.php');
define('TP_PLUGIN_VERSION',"1.9.0");
function TPLog($order = null, $user = null, $endpoint = null) {

	$logger = new TodoPagoLogger();
	$logger->setPhpVersion(phpversion());
	$logger->setCommerceVersion(VERSION);
	$logger->setPluginVersion(TP_PLUGIN_VERSION);
	$payment = false;
	if($order != null)
		$payment = true;
	if($payment) {
		$logger->setEndPoint($endpoint);
		$logger->setCustomer($user);
		$logger->setOrder($order);
	}
	$logger->setLevels("debug","fatal");
	$logger->setFile(dirname(__FILE__)."/todopago.log");
	return $logger->getLogger($payment);
}

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

function _tranResult($oid)
{
	$res = db_query("SELECT * FROM {todopago_transaccion} WHERE id_orden=".$oid);
	$res = $res->fetchAssoc();
	return $res;
}

function prepare_order($order)
{
	if(_tranEstado($order->order_id) == 0)
		_tranCrear($order->order_id);
}

function get_paydata($order, $user, $form, $payment_method)
{
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
        $modo = "prod";
    }else{
         $modo = "test";
    }


    $typeForm = ($settings['general']['form'])?'H':'E';
 
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
	$optionsSAR_operacion['ECOMMERCENAME'] = 'DRUPAL';
	$optionsSAR_operacion['ECOMMERCEVERSION'] = get_cmsversion();
	$optionsSAR_operacion['CMSVERSION'] = VERSION;
	$optionsSAR_operacion['PLUGINVERSION'] = TP_PLUGIN_VERSION.'-'. $typeForm;

	if( isset($payment_method["settings"]["general"]["maxinstallments_enabled"]) 
		&& $payment_method["settings"]["general"]["maxinstallments_enabled"] == 1  )
	{
        $optionsSAR_operacion['MAXINSTALLMENTS'] = ($payment_method["settings"]["general"]["maxinstallments"] > 0 && $payment_method["settings"]["general"]["maxinstallments"] <= 12 )? $payment_method["settings"]["general"]["maxinstallments"]:12;
    }
 
    if( isset($payment_method["settings"]["general"]["timeout_enabled"]) 
    	&& $payment_method["settings"]["general"]["timeout_enabled"] == 1  )
    {
        $optionsSAR_operacion['TIMEOUT'] = (intval($payment_method["settings"]["general"]["timeout"]) > 0)? $payment_method["settings"]["general"]["timeout"]:1800000;
    }

	//creo el conector con el valor de Authorization, la direccion de WSDL y endpoint que corresponda
	$connector = get_connector($settings);

	return array($connector, $optionsSAR_comercio, $optionsSAR_operacion);
}


function get_cmsversion(){
	
	if(empty(system_get_info("module", "Commerce"))) {

		$sys_vars = system_get_info("module", "commerce_kickstart");
		$CMS_version = explode('-',$sys_vars['version']);
		$cms_version = "{$CMS_version[1]}";

	}else{
		
		$cms_version = system_get_info("module", "Commerce");
		$cms_version = "{$cms_version[0]}";
	
	}
	
	return $cms_version;

}




function call_SAR($connector, $order, $optionsSAR_comercio, $optionsSAR_operacion, $payment_method, $form, $form_state)
{
	global $user;
	TPLog($order->order_id, $user->uid, $payment_method["settings"]["general"]["modo"])->info("params SAR ".json_encode(array($optionsSAR_comercio, $optionsSAR_operacion)));
	
/*	// si esta habilitado para la direcciones de gmaps setear cliente google
    $address_result = address_loaded($optionsSAR_operacion);

    $gClient = null;

    if($address_result['address_loaded']){
        $optionsSAR_operacion = $address_result['payDataOperacion'];
        update_addresses($order, $address_result);
       
    }elseif ($payment_method['settings']["general"]['gmaps_enabled'] == 1){
        $gClient = new \TodoPago\Client\Google();

        if($gClient != null) {
            $connector->setGoogleClient($gClient);
        }
       
    }
*/
	$rta = $connector->sendAuthorizeRequest($optionsSAR_comercio, $optionsSAR_operacion);
	//guardo direccion 
/*    if($gClient != null) {
        tp_save_address($connector->getGoogleClient()->getFinalAddress());
        // modify addresses
        $old_address = $connector->getGoogleClient()->getOriginalAddress(); 

        $payDataOperacion['payDataOperacion'] = $connector->getGoogleClient()->getFinalAddress();
        $payDataOperacion['old_CSBT_address'] = $old_address['billing'];
        $payDataOperacion['old_CSST_address'] = $old_address['shipping'];
        
        update_addresses($order, $payDataOperacion);
    }
*/
	TPLog($order->order_id, $user->uid, $payment_method["settings"]["general"]["modo"])->info("response SAR ".json_encode($rta));

	$settings = $payment_method["settings"];
	if ($settings["general"]["modo"] == "Produccion"){
		$modo = "prod";
	}else{
		$modo = "test";
	}



	if ($rta['StatusCode']  != -1)//Si la transacción salió mal
	{
		if(($rta['StatusCode']  == 702)&&(!property_exists($order,"first_step"))) {
			$authorization = json_decode($settings[$modo]["authorization"],1);
			$merchant = $settings[$modo]["idsite"];
			$security = $settings[$modo]["security"];
			if((isset($authorization["Authorization"]))&&(!empty($merchant))&&(!empty($security))){
				$order->first_step = true;
				first_step_todopago($form, $form_state, $order, $payment_method);
			}
		}

		throw new Exception($statusMessage);
	}

	$now = new DateTime();
	_tranUpdate($order->order_id, array("first_step" => $now->format('Y-m-d H:i:s'), "params_SAR" => json_encode(array($optionsSAR_comercio, $optionsSAR_operacion)), "response_SAR" => json_encode($rta), "request_key" => $rta['RequestKey'], "public_request_key" => $rta['PublicRequestKey']));

	if($settings["general"]["form"] == '0') {
		$form['#action'] = $rta["URL_Request"];
		$form['submit'] = array(
				'#type' => 'submit',
				'#value' => t('Continuar a Todo Pago'),
				'#weight' => 50,
		);
	} else {


		$form['iframe'] = array(
			'#markup' => '<iframe src="' . base_path() . drupal_get_path('module', 'commerce_todo_pago') .'/includes/formcustom.php?modo='.$modo.'&amount='.$optionsSAR_operacion["AMOUNT"].'&merchant='.$optionsSAR_operacion["MERCHANT"].'&prk=' . $rta['PublicRequestKey'] . 
			    '&order=' . $order->order_id . 
			    '&completename=' . $optionsSAR_operacion['CSSTFIRSTNAME'] .' '.$optionsSAR_operacion['CSSTLASTNAME'].
			    '&mail=' . $optionsSAR_operacion['CSBTEMAIL'] . 
			    '&key='.$order->data['payment_redirect_key'].
			    '" name="formcustom" scrolling="no" frameborder="0" width="100%" height="1500"></iframe>'
        );
	}
	return $form;
}


function address_loaded($payDataOperacion){
   
    $CSBT_address = get_loaded_address($payDataOperacion, 'CSBT');   
    $CSST_address = get_loaded_address($payDataOperacion, 'CSST');
   
    if (($CSBT_address['result'] != null) && ( $CSST_address['result'] != null ) ){
        $payDataOperacion['CSBTSTREET1']= $CSBT_address['result']['address'];
        $payDataOperacion['CSBTPOSTALCODE']= $CSBT_address['result']['postal_code'];
        $payDataOperacion['CSBTCITY']= $CSBT_address['result']['city'];
        $payDataOperacion['CSBTCOUNTRY']= $CSBT_address['result']['country'];

        $payDataOperacion['CSSTSTREET1']= $CSST_address['result']['address'];
        $payDataOperacion['CSSTPOSTALCODE']= $CSST_address['result']['postal_code'];
        $payDataOperacion['CSSTCITY']= $CSST_address['result']['city'];
        $payDataOperacion['CSSTCOUNTRY']= $CSST_address['result']['country'];

        $address_loaded = true;
    }else{
        $address_loaded = false;
    }

    $address_result = array('payDataOperacion' => $payDataOperacion,                            
                            'address_loaded' => $address_loaded,
                            'old_CSBT_address' => $CSBT_address['old_address'],
                            'old_CSST_address' => $CSST_address['old_address']
                            );

    return $address_result; 
}


/**
*   returns stdClass if exist address, else returns null
*/
function get_loaded_address($payDataOperacion, $type){
	
    $street  = explode(' ', $payDataOperacion["{$type}STREET1"]);

    $where = '';  
    foreach ($street as $val) { 
        $where .= " address like '%{$val}%' and ";
    }

    $query = "SELECT * FROM `{todopago_address}` where {$where} postal_code like '%{$payDataOperacion["{$type}POSTALCODE"]}%' and country='{$payDataOperacion["{$type}COUNTRY"]}' limit 1" ;
 
    $res = db_query($query);
	$result['result'] = $res->fetchAssoc(); 

	$result['old_address'] = array (
      "{$type}STREET1" => $payDataOperacion["{$type}STREET1"],
      "{$type}CITY" => $payDataOperacion["{$type}CITY"],
      "{$type}STATE" => $payDataOperacion["{$type}STATE"],
      "{$type}COUNTRY" => $payDataOperacion["{$type}COUNTRY"]
	);

    return $result;
}

function update_addresses($order,  $payDataOperacion){

    $street  = explode(' ', $payDataOperacion['old_CSBT_address']['CSBTSTREET1']);
    $data = array("commerce_customer_address_thoroughfare" => $payDataOperacion['payDataOperacion']['billing']['CSBTSTREET1'],
    			"commerce_customer_address_locality" => $payDataOperacion['payDataOperacion']['billing']['CSBTCITY'],
    			"commerce_customer_address_postal_code" => $payDataOperacion['payDataOperacion']['billing']['CSBTPOSTALCODE']
    			);

    $query = db_update("field_data_commerce_customer_address");
	$query->fields($data)
			->condition('entity_type', 'commerce_customer_profile', '=')
			->condition('deleted', 0, '=')
			->condition('bundle', 'billing', '=')
			->condition('commerce_customer_address_country', $payDataOperacion['old_CSBT_address']['CSBTCOUNTRY'], '=')
			->condition('commerce_customer_address_locality', $payDataOperacion['old_CSBT_address']['CSBTCITY'], '=')
			->condition('commerce_customer_address_administrative_area', $payDataOperacion['old_CSBT_address']['CSBTSTATE'], '=');

			foreach ( $street as $val ){
				$query->condition('commerce_customer_address_thoroughfare', "%{$val}%", 'like');	
			}

	$query->execute();


	$street  = explode(' ', $payDataOperacion['old_CSST_address']['CSSTSTREET1']);
    $data = array("commerce_customer_address_thoroughfare" => $payDataOperacion['payDataOperacion']['shipping']['CSSTSTREET1'],
    			"commerce_customer_address_locality" => $payDataOperacion['payDataOperacion']['shipping']['CSSTCITY'],
    			"commerce_customer_address_postal_code" => $payDataOperacion['payDataOperacion']['shipping']['CSSTPOSTALCODE']
    			);

    $query = db_update("field_data_commerce_customer_address");
	$query->fields($data)
			->condition('entity_type', 'commerce_customer_profile', '=')
			->condition('deleted', 0, '=')
			->condition('bundle', 'shipping', '=')
			->condition('commerce_customer_address_country', $payDataOperacion['old_CSST_address']['CSSTCOUNTRY'], '=')
			->condition('commerce_customer_address_locality', $payDataOperacion['old_CSST_address']['CSSTCITY'], '=')
			->condition('commerce_customer_address_administrative_area', $payDataOperacion['old_CSST_address']['CSSTSTATE'], '=');

			foreach ( $street as $val ){
				$query->condition('commerce_customer_address_thoroughfare', "%{$val}%", 'like');	
			}

	$query->execute();

    return false;
}

function tp_save_address($payDataOperacion){

    // Get a db connection.
    $data = array("address" => $payDataOperacion['billing']['CSBTSTREET1'],
     "city" => $payDataOperacion['billing']['CSBTCITY'], 
     "postal_code" => $payDataOperacion['billing']['CSBTPOSTALCODE'], 
     "country" => $payDataOperacion['billing']['CSBTCOUNTRY']);

	$query = db_insert("todopago_address");
	$query->fields($data)->execute();


    if (address_diff($payDataOperacion)){

        $data = array("address" => $payDataOperacion['shipping']['CSSTSTREET1'],
         "city" => $payDataOperacion['shipping']['CSSTCITY'], 
         "postal_code" => $payDataOperacion['shipping']['CSSTPOSTALCODE'], 
         "country" => $payDataOperacion['shipping']['CSSTCOUNTRY']);

		$query = db_insert("todopago_address");
		$query->fields($data)->execute();

    } 
    
}


function address_diff($payDataOperacion){
        $result = false; 

        if($payDataOperacion['billing']['CSBTCOUNTRY'] != $payDataOperacion['shipping']['CSSTCOUNTRY']) $result = true; 
        if($payDataOperacion['billing']['CSBTPOSTALCODE'] != $payDataOperacion['shipping']['CSSTPOSTALCODE']) $result = true; 
        if($payDataOperacion['billing']['CSBTCITY'] != $payDataOperacion['shipping']['CSSTCITY']) $result = true; 
        if($payDataOperacion['billing']['CSBTSTREET1'] != $payDataOperacion['shipping']['CSSTSTREET1']) $result = true; 

        return $result;
}




function first_step_todopago($form, &$form_state, $order, $payment_method)
{
	global $pane_values,$user;
	TPLog($order->order_id, $user->uid, $payment_method["settings"]["general"]["modo"])->info('first step');

	prepare_order($order);
	list($connector, $optionsSAR_comercio, $optionsSAR_operacion) = get_paydata($order, $user, $form, $payment_method);

	return call_SAR($connector, $order, $optionsSAR_comercio, $optionsSAR_operacion, $payment_method, $form, $form_state);
}

function call_GAA($order, $ak)
{
	global $user;

	if(_tranEstado($order) != 2)
	{
		throw new Exception("second_step ya realizado");
	}
    $payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
    $settings = $payment_method["settings"];
	$oOrder = commerce_order_load($order);

    if ($settings["general"]["modo"] == "Produccion"){
        $modo = "prod";
    }else{
         $modo = "test";
    }

    $optionsGAA = array (
        'Security'=>$settings[$modo]["security"],
    	'Merchant'=>$settings[$modo]["idsite"],
        'RequestKey' => _tranRK($order),
        'AnswerKey'  => $ak
    );

	$connector = get_connector($settings);

	TPLog($order, $oOrder->uid, $payment_method["settings"]["general"]["modo"])->info('params GAA '.json_encode($optionsGAA));
    $rta2 = $connector->getAuthorizeAnswer($optionsGAA);
	TPLog($order, $oOrder->uid, $payment_method["settings"]["general"]["modo"])->info('response GAA '.json_encode($rta2));

	$now = new DateTime();
	_tranUpdate($order, array("second_step" => $now->format('Y-m-d H:i:s'), "params_GAA" => json_encode($optionsGAA), "response_GAA" => json_encode($rta2), "answer_key" => $ak));

	return $rta2;
}

function take_action($order, $rta2)
{	
    $order = commerce_order_load($order);
    $payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');
    $settings = $payment_method["settings"];

    if ($rta2["StatusCode"]=="-1"){
        commerce_cart_order_empty($order);
        $status = $settings["status"]["aprobada"];
        $transaction = commerce_payment_transaction_new('bank_transfer', $order->order_id);
        $transaction->instance_id = $payment_method['instance_id'];
        $transaction->amount = $rta2["Payload"]["Request"]["AMOUNTBUYER"]*100;
        $transaction->status = COMMERCE_PAYMENT_STATUS_SUCCESS;
        $transaction->payload = print_r($rta2["Payload"],1);
        $transaction->remote_id = $rta2["Payload"]["Answer"]["OPERATIONID"];
        commerce_payment_transaction_save($transaction);
        commerce_order_status_update($order, $status);

		$default_currency_code = commerce_default_currency();
		if ($balance = commerce_payment_order_balance($order)) {
			$default_currency_code = $balance['currency_code'];
		}

		// Create the new line item.
		$line_item = commerce_line_item_new('product', $order->order_id);
		$line_item->line_item_label = 'Otros Cargos';
	 	$line_item->quantity = 1;
		$line_item->commerce_unit_price[LANGUAGE_NONE][0]['amount'] = $rta2["Payload"]["Request"]["AMOUNTBUYER"]*100 - $rta2["Payload"]["Request"]["AMOUNT"]*100;
		$line_item->commerce_unit_price[LANGUAGE_NONE][0]['currency_code'] = $default_currency_code;
		rules_invoke_event('commerce_product_calculate_sell_price', $line_item);
		$line_item_wrapper = entity_metadata_wrapper("commerce_line_item", $line_item);
		$line_item_wrapper->commerce_unit_price->data = commerce_price_component_add(
		    $line_item_wrapper->commerce_unit_price->value(),
		    'base_price',
		    array(
	        	    'amount' => $rta2["Payload"]["Request"]["AMOUNTBUYER"]*100,
		            'currency_code' => $default_currency_code,
		            'data' => array(),
		    ),
		    TRUE
		);

        $line_item_wrapper->save();
		commerce_line_item_save($line_item);

        $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
        $order_wrapper->commerce_line_items[] = $line_item;
        $order_wrapper->save();

		commerce_order_calculate_total($order);
        $order_wrapper->save();

        drupal_goto(commerce_checkout_order_uri($order));
    }else{
        if (isset($rta2["Payload"]) && $rta2["Payload"]["Answer"]["BARCODETYPE"] !=""){
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
            echo "<img   src='".base_path().drupal_get_path('module', 'commerce_todo_pago')."/includes/image.php?filetype=PNG&dpi=72&scale=5&rotation=0&font_family=Arial.ttf&font_size=8&text=".$barcode."&thickness=30&checksum=&code=BCGi25&' />";

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

            $status = $settings["status"]["rechazada"];

            if ($settings["general"]['emptycart_enabled'] == "1"){
                commerce_cart_order_empty($order);
                commerce_payment_redirect_pane_previous_page($order);
                commerce_order_status_update($order, $status);

            } else {
                commerce_payment_redirect_pane_previous_page($order);
            }

            drupal_set_message(t($rta2['StatusMessage']), 'error');
            drupal_goto(commerce_checkout_order_uri($order));
        }
    }
}

function second_step_todopago($order, $return, $user, $ak)
{
	$payment_method = commerce_payment_method_instance_load('bank_transfer|commerce_payment_bank_transfer');

	$oOrder = commerce_order_load($order);
    TPLog($order, $oOrder->uid, $payment_method["settings"]["general"]["modo"])->info('second step');

	$rta = call_GAA($order, $ak);

	return take_action($order, $rta);

}

function get_payment_methods($payment_method)
{
	$settings = $payment_method;
	$connector = get_connector($settings);

	return $connector->discoverPaymentMethods();
}

function get_connector($settings)
{

	$mode = ($settings["general"]["modo"] == "Produccion")?"prod":"test";
    $http_header = json_decode($settings[$mode]["authorization"],1);
	if($http_header == null) {
		$http_header = array("Authorization" => $settings[$mode]["authorization"]);
	}
	$connector = new Sdk($http_header, $mode);

	return $connector;
}

if (!function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
        static $code = 200;
        if($newcode !== NULL)
        {
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }
        return $code;
    }
}

function push_notification($order_id,$ak)
{
	$transaccion = _tranResult($order_id);

	if(!$transaccion) {
		http_response_code(404);
		return;
	}

	if($transaccion["answer_key"] != $ak) {
		http_response_code(400);
		return;
	}

	//Actualizar order status
	echo "OK";

}

function get_credentials(){
}
