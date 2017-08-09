jQuery(document).ready(function($) {

	var ambiente = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-general-modo").val()

	if(ambiente == "Produccion"){
		jQuery("#credenciales-dev").attr("disabled","disabled");
	}else{
		jQuery("#credenciales-prod").attr("disabled","disabled");
	}

	//get credentials
	jQuery("#login-credencial").click(function(){

		if(ambiente == "Produccion"){
			positioinTop = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod").offset().top - 250;
			jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod-idsite").focus();
		}else{
			positioinTop = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test").offset().top - 250;
			jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test-idsite").focus();
		}

		jQuery("#error_message").html("").hide();

		$.ajax({
			type: "POST",
			accepts: "application/json",
			data: { 
		        'user': $("#edit-parameter-payment-method-settings-payment-method-settings-login-credential-tp-user").val(), 
		        'pass': $("#edit-parameter-payment-method-settings-payment-method-settings-login-credential-tp-password").val(),
		        'mode': ambiente
		    },
		 	url: Drupal.settings.basePath + 'sites/all/modules/' + Drupal.settings.TPmodule.moduleNameFolder + '/includes/ajax/credenciales.php',
		 	beforeSend: function(){
		 		jQuery(".loader").show();

		 	},
		 	success: function(data){
		 		jQuery(".loader").hide();
		 		response = $.parseJSON(data);

		 		if(response.codigoResultado === undefined){

		 			jQuery("#error_message").html(response.mensajeResultado).show();

		 		}else if(response.codigoResultado == 1){
		 			if(ambiente == "Produccion"){
						jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod-idsite").val(response.merchandid);
		 				jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod-security").val(response.security);
		 				jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod-authorization").val(response.apikey);
		 				logPositioinTop = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-prod-idsite").offset().top - 150;
			        }else{
			        	jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test-idsite").val(response.merchandid);
		 				jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test-security").val(response.security);
		 				jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test-authorization").val(response.apikey);
		 				logPositioinTop = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-test-idsite").offset().top - 300;
			        }

			    //    jQuery("#edit-parameter-payment-method-settings-payment-method-settings-general-authorization").val(response.apikey);
					$('html, body').animate({
					    scrollTop: logPositioinTop
					}, 400);

			        //clear field
		 			jQuery("#edit-parameter-payment-method-settings-payment-method-settings-login-credential-tp-user").val('');
		 			jQuery("#edit-parameter-payment-method-settings-payment-method-settings-login-credential-tp-password").val('');

		 		}
		    },
		    error: function(data){
		       error_response = $.parseJSON(data);
		       jQuery(".loader").hide();
		       jQuery("#error_message").html(error_response).show();
		    }
		});
	});
	
	jQuery(".btn-credencial").click(function(){
		logPositioinTop = jQuery("#edit-parameter-payment-method-settings-payment-method-settings-login-credential").offset().top - 250;
		$('html, body').animate({
		    scrollTop: logPositioinTop
		}, 400);

		jQuery("#edit-parameter-payment-method-settings-payment-method-settings-login-credential-tp-user").focus();
	});
});