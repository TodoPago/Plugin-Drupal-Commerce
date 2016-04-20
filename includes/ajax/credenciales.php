<?php
	include(dirname(__FILE__).'\\..\\..\\vendor\\todopago\\php-sdk\\vendor\\autoload.php');
	use TodoPago\Sdk;

	if((isset($_POST['user']) && !empty($_POST['user'])) && (isset($_POST['pass']) && !empty($_POST['pass']))){

		$userArray = array(
						"user" => trim($_POST['user']), 
						"password" => trim($_POST['pass'])
						);

		$http_header = array();

		//ambiente developer por defecto	
		$mode = 0;
		if($_POST['mode'] == "Produccion"){
			$mode = 1;
		}

		try {
			$connector = new Sdk($http_header, $mode);

			$userInstance = new TodoPago\Data\User($userArray);

		    $rta = $connector->getCredentials($userInstance);
		    
		    $security = explode(" ", $rta->getApikey());	

			$response = array(	
							"codigoResultado" => 1,
							"merchandid" => $rta->getMerchant(),
							"apikey" => $rta->getApikey(),
							"security" => $security[1]
						);

		}catch(Exception $e){
			$response = array(
							"mensajeResultado" => $e->getMessage()
						);
		}
		echo json_encode($response);

	}else{

		$response = array(	
				"mensajeResultado" => "Ingrese usuario y contraseña de Todo Pago"
		);		
		echo json_encode($response);
	}

?>