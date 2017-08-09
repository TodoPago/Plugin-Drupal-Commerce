<?php

abstract class ControlFraude {
  
	protected $datasources = array();
	
	public function __construct($user = array(), $order = array()){
		$this->datasources = array("user" => $user, "order" => $order);
		
		$profile = commerce_customer_profile_load($order->commerce_customer_billing[LANGUAGE_NONE][0]['profile_id']);
		$this->datasources['profile'] = $profile;
		$this->datasources['extra'] = array("moneda" => "ARS");
	}
	
	public function getDataCS(){
		$datosCS = $this->completeCS();
		$datosCS = array_merge($datosCS, $this->completeCSVertical());
		foreach($datosCS as $key => $value) {
			$datosCS[$key] = array(
				'#type' => 'hidden',
				'#value' => $value,			
			);
		}
		return $datosCS;
	}	

	protected function completeCS(){
		$datosCS = array();
		$datosCS["CSBTCITY"] 			= substr($this->getField($this->datasources['profile'],'commerce_customer_address','locality'),0,250);
		$datosCS["CSBTCOUNTRY"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','country');
		$datosCS["CSBTCUSTOMERID"] 		= $this->getField($this->datasources['profile'],"uid");
		$datosCS["CSBTIPADDRESS"] 		= ($this->get_the_user_ip() == '::1') ? '127.0.0.1' : $this->get_the_user_ip();
		$datosCS["CSBTEMAIL"] 			= $this->getField($this->datasources['order'],"mail");
		$datosCS["CSBTFIRSTNAME"] 		= $this->getField($this->datasources['profile'],'todo_pago_nombre','value');
		$datosCS["CSBTLASTNAME"] 		= $this->getField($this->datasources['profile'],'todo_pago_apellido','value');
		$datosCS["CSBTPHONENUMBER"] 	= $this->_phoneSanitize($this->getField($this->datasources['profile'],'todo_pago_telefono','value'));
		$datosCS["CSBTPOSTALCODE"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','postal_code');
		$datosCS["CSBTSTATE"] 			= $this->getField($this->datasources['profile'],'todo_pago_ciudad','value');
		$datosCS["CSBTSTREET1"] 		= $this->getField($this->datasources['profile'],'commerce_customer_address','thoroughfare');
		$datosCS["CSPTCURRENCY"] 		= $this->getField($this->datasources['extra'],"moneda");
		$datosCS["CSPTGRANDTOTALAMOUNT"]= number_format(commerce_currency_amount_to_decimal($this->getField($this->datasources['order'],'commerce_order_total','amount'),$this->getField($this->datasources['order'],'commerce_order_total','currency_code')),2,".","");
		$datosCS["CSMDD7"] 				= $this->_getDateTimeDiff($this->getField($this->datasources['user'],"created"));
		
        if(isset($this->datasources['user']->created)){
			$datosCS['CSMDD8']= 'N';
            $datosCS['CSMDD9'] = $this->getField($this->datasources['user'],"pass");			
        } else {
            $datosCS['CSMDD8'] = 'S';
        }
		
		return $datosCS;
	}
  
	protected abstract function completeCSVertical();
	protected abstract function getCategoryArray($productId);
	
	protected function getMultipleProductsInfo(){
		$order_lines = field_get_items('commerce_order', $this->datasources["order"], 'commerce_line_items');
		$line_item_ids = array();
		foreach ($order_lines as $order_line) {
			$line_item_ids[] = $order_line['line_item_id'];
		}
		$line_items = commerce_line_item_load_multiple($line_item_ids);
		$product_ids = array();
		$cant_prod = array();
		foreach ($line_items as $line_item) {
			$tmp = field_get_items('commerce_line_item', $line_item, 'commerce_product');
			$cant_prod[$tmp[0]['product_id']] = round($line_item->quantity);
			$product_ids[] = $tmp[0]['product_id'];    
		}
		$products = commerce_product_load_multiple($product_ids);

        $code =array();
        $description = array();
        $name = array();
        $sku = array();
        $total = array();
        $quantity = array();
        $unit = array();

        foreach ($products as $item) {
			$code[] = $this->getCategoryArray($item);

			if(!empty($item->description)){
				$desc = $item->description;
			}else{
				$desc = $item->title;
			}

			$desc = strip_tags($desc);
            $desc = TodoPago\Sdk::sanitizeValue($desc);
            $desc = substr($desc,0,50);
            $description[]   = $desc;
            
            $name[]  = substr($item->title,0,250);
            $sku[]  = substr((empty($item->sku)?$item->product_id:$item->sku),0,250);
            $total[]  = number_format(commerce_currency_amount_to_decimal($item->commerce_price[LANGUAGE_NONE][0]["amount"],$item->commerce_price[LANGUAGE_NONE][0]["currency_code"])*$cant_prod[$item->product_id],2,".","");
            $quantity[]  = $cant_prod[$item->product_id];
            $unit[]  = number_format(commerce_currency_amount_to_decimal($item->commerce_price[LANGUAGE_NONE][0]["amount"],$item->commerce_price[LANGUAGE_NONE][0]["currency_code"]),2,".","");
        }
		
		$productsData = array (
            'CSITPRODUCTCODE' => join("#", $code),
            'CSITPRODUCTDESCRIPTION' => join("#", $description),
            'CSITPRODUCTNAME' => join("#", $name),
            'CSITPRODUCTSKU' => join("#", $sku),
            'CSITTOTALAMOUNT' => join("#", $total),
            'CSITQUANTITY' => join("#", $quantity),
            'CSITUNITPRICE' => join("#", $unit),
        );

		return $productsData;
	}
	
	protected function _getPhone($datasources, $mobile = false){
		if($mobile) {
			$data = $this->getField($datasources['address'],"phone_mobile");
			if (empty($data)) {
					return $this->_phoneSanitize($this->getField($datasources['address'],"phone"));
			}
			return $this->_phoneSanitize($this->getField($datasources['address'],"phone_mobile"));
		}
		$data = $this->getField($datasources['address'],"phone");
		if(empty($data)){
			return $this->_phoneSanitize($this->getField($datasources['address'],"phone_mobile"));
		}
		return $this->_phoneSanitize($this->getField($datasources['address'],"phone"));
	}
	
	protected function getField($datasource, $key, $arr_key = false){
		$return = "";
		try{
			if(is_array($datasource))
				if(isset($datasource[$key]))
					return $datasource[$key];
				else
					throw new Exception("No encontrado");
			elseif(property_exists($datasource,$key)) {
				$return = $datasource->$key;
				if($arr_key)
					$return = $return[LANGUAGE_NONE][0][$arr_key];
			}
			else
				throw new Exception("No encontrado");
		}catch(Exception $e){
			$this->log("a ocurrido un error en el campo ". $key. " se toma el valor por defecto");
		}
		return $return;
	}

	protected function log($mensaje)
	{
		$nombre = 'CSlog';
		
		$archivo = fopen(dirname(__FILE__).'/../'.$nombre.'.txt', 'a+');
		fwrite($archivo, date('Y/m/d - H:i:s').' - '.$mensaje . PHP_EOL);
		fclose($archivo);
	}

	protected function _phoneSanitize($number){
		$number = str_replace(array(" ","(",")","-","+"),"",$number);
		
		if(substr($number,0,2)=="54") return $number;
		
		if(substr($number,0,2)=="15"){
			$number = substr($number,2,strlen($number));
		}
		if(strlen($number)==8) return "5411".$number;
		
		if(substr($number,0,1)=="0") return "54".substr($number,1,strlen($number));
		return "54".$number;
	}

    protected function _getStateIso($id)
    {
        $state = new State($id);
        return $state->iso_code;
    }
	
    protected function _getDateTimeDiff($fecha)
    {
        return date_diff(DateTime::createFromFormat("U",$fecha), new DateTime())->format('%a');
    }

    public function get_the_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}
