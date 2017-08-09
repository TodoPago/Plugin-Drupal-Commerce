<?php
	class StatusCodeCS {
		
		/**
		 * 
		 * @param  $statusCode
		 * @return string
		 * @desc Recibe un status code y devuelve una cadena con el mensaje de error correspondiente,
		 * siempre y cuando esté se encuentre en el diccionario de mensajes de error del método
		 * _toOptionArray.
		 * 
		 */
		public function getErrorByStatusCode($statusCode){
			
			$messages=$this->_toOptionArray();
			$error="";
			
			if(array_key_exists(strval($statusCode),$messages)){
				$error=$messages[$statusCode];
			}
			
			return $error;
		}
		
		/**
		 * 
		 * @return string[]
		 * @desc devuelve un array de mensajes de error del CyberSource
		 */
		private function _toOptionArray() {
	
			return array (
				'98001' => 'El campo CSBTCITY es requerido',
				'98002' => 'El campo CSBTCOUNTRY es requerido',
				'98003' => 'El campo CSBTCUSTOMERID es requerido',
				'98004' => 'El campo CSBTIPADDRESS es requerido',
				'98005' => 'El campo CSBTEMAIL es requerido',
				'98006' => 'El campo CSBTFIRSTNAME es requerido',
				'98007' => 'El campo CSBTLASTNAME es requerido',
				'98008' => 'El campo CSBTPHONENUMBER es requerido',
				'98009' => 'El campo CSBTPOSTALCODE es requerido',
				'98010' => 'El campo CSBTSTATE es requerido',
				'98011' => 'El campo CSBTSTREET1 es requerido',
				'98012' => 'El campo CSBTSTREET2 es requerido',
				'98013' => 'El campo CSPTCURRENCY es requerido',
				'98014' => 'El campo CSPTGRANDTOTALAMOUNT es requerido',
				'98020' => 'El campo CSSTCITY es requerido',
				'98021' => 'El campo CSSTCOUNTRY es requerido',
				'98022' => 'El campo CSSTEMAIL es requerido',
				'98023' => 'El campo CSSTFIRSTNAME es requerido',
				'98024' => 'El campo CSSTLASTNAME es requerido',
				'98025' => 'El campo CSSTPHONENUMBER es requerido',
				'98026' => 'El campo CSSTPOSTALCODE es requerido',
				'98027' => 'El campo CSSTSTATE es requerido',
				'98028' => 'El campo CSSTSTREET1 es requerido',
				'98034' => 'El campo CSITPRODUCTCODE es requerido',
				'98035' => 'El campo CSITPRODUCTDESCRIPTION es requerido',
				'98036' => 'El campo CSITPRODUCTNAME es requerido',
				'98037' => 'El campo CSITPRODUCTSKU es requerido',
				'98038' => 'El campo CSITTOTALAMOUNT es requerido',
				'98039' => 'El campo CSITQUANTITY es requerido',
				'98040' => 'El campo CSITUNITPRICE es requerido',
				'98101' => 'El formato del campo CSBTCITY es incorrecto',
				'98102' => 'El formato del campo CSBTCOUNTRY es incorrecto',
				'98103' => 'El formato del campo CSBTCUSTOMERID es incorrecto',
				'98104' => 'El formato del campo CSBTIPADDRESS es incorrecto',
				'98105' => 'El formato del campo CSBTEMAIL es incorrecto',
				'98106' => 'El formato del campo CSBTFIRSTNAME es incorrecto',
				'98107' => 'El formato del campo CSBTLASTNAME es incorrecto',
				'98108' => 'El formato del campo CSBTPHONENUMBER es incorrecto',
				'98109' => 'El formato del campo CSBTPOSTALCODE es incorrecto',
				'98110' => 'El formato del campo CSBTSTATE es incorrecto',
				'98111' => 'El formato del campo CSBTSTREET1 es incorrecto',
				'98112' => 'El formato del campo CSBTSTREET2 es incorrecto',
				'98113' => 'El formato del campo CSPTCURRENCY es incorrecto',
				'98114' => 'El formato del campo CSPTGRANDTOTALAMOUNT es incorrecto',
				'98115' => 'El formato del campo CSMDD7 es incorrecto',
				'98116' => 'El formato del campo CSMDD8 es incorrecto',
				'98117' => 'El formato del campo CSMDD9 es incorrecto',
				'98118' => 'El formato del campo CSMDD10 es incorrecto',
				'98119' => 'El formato del campo CSMDD11 es incorrecto',
				'98120' => 'El formato del campo CSSTCITY es incorrecto',
				'98121' => 'El formato del campo CSSTCOUNTRY es incorrecto',
				'98122' => 'El formato del campo CSSTEMAIL es incorrecto',
				'98123' => 'El formato del campo CSSTFIRSTNAME es incorrecto',
				'98124' => 'El formato del campo CSSTLASTNAME es incorrecto',
				'98125' => 'El formato del campo CSSTPHONENUMBER es incorrecto',
				'98126' => 'El formato del campo CSSTPOSTALCODE es incorrecto',
				'98127' => 'El formato del campo CSSTSTATE es incorrecto',
				'98128' => 'El formato del campo CSSTSTREET1 es incorrecto',
				'98129' => 'El formato del campo CSMDD12 es incorrecto',
				'98130' => 'El formato del campo CSMDD13 es incorrecto',
				'98131' => 'El formato del campo CSMDD14 es incorrecto',
				'98132' => 'El formato del campo CSMDD15 es incorrecto',
				'98133' => 'El formato del campo CSMDD16 es incorrecto',
				'98134' => 'El formato del campo CSITPRODUCTCODE es incorrecto',
				'98135' => 'El formato del campo CSITPRODUCTDESCRIPTION es incorrecto',
				'98136' => 'El formato del campo CSITPRODUCTNAME es incorrecto',
				'98137' => 'El formato del campo CSITPRODUCTSKU es incorrecto',
				'98138' => 'El formato del campo CSITTOTALAMOUNT es incorrecto',
				'98139' => 'El formato del campo CSITQUANTITY es incorrecto',
				'98140' => 'El formato del campo CSITUNITPRICE es incorrecto',
				'98141' => 'El formato del campo CSSTSTREET2 es incorrecto',
				'98201' => 'Existen errores en la información de los productos',
				'98202' => 'Existen errores en la información de CSITPRODUCTDESCRIPTION los productos',
				'98203' => 'Existen errores en la información de CSITPRODUCTNAME los productos',
				'98204' => 'Existen errores en la información de CSITPRODUCTSKU los productos',
				'98205' => 'Existen errores en la información de CSITTOTALAMOUNT los productos',
				'98206' => 'Existen errores en la información de CSITQUANTITY los productos',
				'98207' => 'Existen errores en la información de CSITUNITPRICE de los productos'
			);
		}
	}