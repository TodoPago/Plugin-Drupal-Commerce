<?php

require_once(dirname(__FILE__)."/ControlFraude.php");

class ControlFraudeTicketing extends ControlFraude {

	protected function completeCSVertical(){
		$order_line = field_get_items('commerce_order', $this->datasources["order"], 'commerce_line_items');
		$line_item_ids[] = $order_line[0]['line_item_id'];

		$line_item = commerce_line_item_load_multiple($line_item_ids);
		$product_ids = array();

		$tmp = field_get_items('commerce_line_item', $line_item[0], 'commerce_product');
		$product_ids[] = $tmp[0]['product_id'];    
			
		$products = commerce_product_load_multiple($product_ids);
		$item = $products[0];

		if(property_exists($item,"csmdd33"))
			$datosCS["CSMDD33"] = $this->_getDateTimeDiff($item->csmdd33[LANGUAGE_NONE][0]["value"]);
		if(property_exists($item,"csmdd34"))
			$datosCS["CSMDD34"] = $item->csmdd34[LANGUAGE_NONE][0]["value"];
			
		return array_merge($this->getMultipleProductsInfo(), $datosCS);
	}

	protected function getCategoryArray($id_product){
		if(property_exists($item,"csitproductcode"))
			return $item->csitproductcode[LANGUAGE_NONE][0]["value"];
		return "default";
	}
}
