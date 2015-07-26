<?php
use TodoPago\Sdk;
include_once(drupal_get_path('module', 'commerce_todo_pago').'/includes/TodoPago/lib/Sdk.php');

function commerce_todo_pago_admin_form($form, &$form_state,$order) {

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

    $optionsGS = array('MERCHANT'=>$settings[$modo]["idsite"], 'OPERATIONID'=>$order->order_id); 
  
  
   $rta4 = $connector->getStatus($optionsGS);   
    if (isset($rta4["Operations"])){
        
         $rta4 = $rta4["Operations"];
    $tabla = "";
    foreach($rta4 as $key => $value){
        $tabla .="<tr><td>".$key."</td><td>".$value."</td></tr>";
        
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

