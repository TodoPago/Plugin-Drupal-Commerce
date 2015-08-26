<?php

require_once(dirname(__FILE__)."/ControlFraude.php");

class ControlFraudeRetail extends ControlFraude {

	protected function completeCSVertical() {
		$datosCS["CSSTCITY"] 			= substr($this->getField($this->datasources['profile'],'commerce_customer_address','locality'),0,250);
		$datosCS["CSSTCOUNTRY"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','country');
		$datosCS["CSSTEMAIL"] 			= $this->getField($this->datasources['order'],"mail");
		$datosCS["CSSTFIRSTNAME"] 		= $this->getField($this->datasources['profile'],'todo_pago_nombre','value');
		$datosCS["CSSTLASTNAME"] 		= $this->getField($this->datasources['profile'],'todo_pago_apellido','value');
		$datosCS["CSSTPHONENUMBER"] 	= $this->_phoneSanitize($this->getField($this->datasources['profile'],'todo_pago_telefono','value'));
		$datosCS["CSSTPOSTALCODE"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','postal_code');
		$datosCS["CSSTSTATE"] 			= $this->getField($this->datasources['profile'],'todo_pago_ciudad','value');
		$datosCS["CSSTSTREET1"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','thoroughfare');
		
		return array_merge($this->getMultipleProductsInfo(), $datosCS);
	}

	protected function getCategoryArray($item){
		/*
		if(property_exists($item,"csitproductcode"))
			return $item->csitproductcode[LANGUAGE_NONE][0]["value"];
		return "default";
		*/
		if(!empty($item->type)) 
			return $item->type;
		return "defualt";
	}
}
