<?php
require_once(dirname(__FILE__)."/ControlFraudeRetail.php");
require_once(dirname(__FILE__)."/ControlFraudeService.php");
require_once(dirname(__FILE__)."/ControlFraudeTicketing.php");
require_once(dirname(__FILE__)."/ControlFraudeDigitalgoods.php");

class ControlFraudeFactory {

	const RETAIL = "Retail";
	const SERVICE = "Service";
	const DIGITAL_GOODS = "Digital Goods";
	const TICKETING = "Ticketing";

	public static function get_controlfraude_extractor($vertical, $user, $order){
		$instance;
		switch ($vertical) {
			case ControlFraudeFactory::RETAIL:
				$instance = new ControlFraudeRetail($user, $order);
			break;
			
			case ControlFraudeFactory::SERVICE:
				$instance = new ControlFraudeService($user, $order);
			break;
			
			case ControlFraudeFactory::DIGITAL_GOODS:
				$instance = new ControlFraudeDigitalgoods($user, $order);
			break;
			
			case ControlFraudeFactory::TICKETING:
				$instance = new ControlFraudeTicketing($user, $order);
			break;
			
			default:
				$instance = new ControlFraudeRetail($user, $order);
			break;
		}
		return $instance;
	}
}