
<head>
	<?php if($_GET["modo"] == "test"){ ?>
		<script src='https://developers.todopago.com.ar/resources/v2/TPBSAForm.min.js'></script>
	<?php }else{ ?>
		<script src='https://forms.todopago.com.ar/resources/v2/TPBSAForm.min.js'></script>
	<?php } ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="tp-styles.css">
	<style>
		.field{
			margin:7px 0 2px 0;
		}
		#MY_btnConfirmarPago{
			margin:15px 0 0 0;
		}
	</style>
</head>
<body>
	<div class="contentContainer main" style="width: auto">
		<div id="tp-form-tph" class="left">
			<div id="tp-logo"></div>
			<div id="tp-content-form fieldset"><br/>
				<div class="field">
				<select id="formaPagoCbx"></select>
					<label for="medioPagoCbx" class="tp-label required"></label><br />
				</div>
				<div class="field">
					<label id="numeroTarjetaLbl" for="numeroTarjetaTxt" class="tp-label required spacer"></label>
					<div class="input-box">
						<input class="input-text required-entry" id="numeroTarjetaTxt" maxlength="16" title="Número de Tarjeta"/>
						<span class="error" id="numeroTarjetaTxtError"></span>
					</div>
				</div>	
				<div class="field">
					<div class="input-box">
						<select id="medioPagoCbx"></select><span class="error" id="formaDePagoCbxError"></span>
					</div>

				</div>						
				<div class="field">
					<div class="input-box">
						<select id="bancoCbx"></select><span class="error" id="bancoCbxError"></span>
					</div>
				</div>
				<div class="field">
					
					<div class="input-box">
						<select id="promosCbx" class="left"></select><span class="error" id="promosCbxError"></span>
						<label id="promosLbl" for="promosCbx" class="tp-label required spacer"></label>
						<label id="labelPromotionTextId" class="left tp-label"></label>
						<div class="clear"></div>
					</div>
				</div>
				<!-- Para los casos en el que el comercio opera con PEI -->
				<div>
			    	<label id="peiLbl"></label>
			    	<label id="tokenPeiLbl"></label>
					<input id="tokenPeiTxt"/>
					<input id="peiCbx"/>
				</div>


				<div class="form-row tp-no-cupon">
					<label id="labelPeiCheckboxId"></label>
				</div>


				<div class="field">				
					<div class="input-box">
						<input id="codigoSeguridadTxt" class="left input-text required-entry small"/></br>
						<label for="codigoSeguridadTxt" id="codigoSeguridadLbl" class="tp-label required spacer"></label></br>
						<span class="error" id="codigoSeguridadTxtError"></span>
						<div class="clear"></div>
					</div>
				</div>
				<div class="field">
					<label for="tipoDocCbx" class="tp-label required spacer"></label>
					<div>
						<div class="input-box">
							<select id="tipoDocCbx" class="left"></select>
							<span class="left spacer">&nbsp;</span>
							<input id="nroDocTxt" class="left input-text required-entry" />
						</div></br>
						<div class="clear"></div>
						<span class="error" id="nroDocTxtError"></span>
					</div>
				</div>


				<div class="field">
					<div class="input-box">
						<select id="mesCbx"></select>
						<select id="anioCbx"></select>
						</br>
						<label id="fechaLbl" class="error"></label>
					</div>
				</div>


				<div class="field">
					<div class="input-box">
						<input id="nombreTxt"/><br/>
						<span class="error" id="nombreTxtError"></span>
					</div>
				</div>

				<div class="field">
					<label for="emailTxt" class="tp-label required"></label>
					<div class="input-box">
						<input id="emailTxt"/><br/>
						<span class="error" id="emailTxtError"></span>
					</div>
				</div>
				

				<div id="tp-bt-wrapper">
					<button id="MY_btnConfirmarPago" title="Pagar" class="button"><span>Pagar</span></button>
					<button id='MY_btnPagarConBilletera' title="Pagar con billetera virtual" class='button'><span>Pagar con billetera virtual</span></button>
			</div>
		</div>

	</div>

	<script>
		var order = "<?php echo $_GET['order']?>";
		var key = "<?php echo $_GET['key'] ?>";
		var security = "<?php echo $_GET['prk']; ?>";
		var mail = "";
		var completeName = "";
		var dni = 'Numero de documento';
		var defDniType = 'DNI';

		url = window.location.pathname;
		base = url.split("/");
		url_base = "<?php  echo $url = preg_replace('/\?.*/', '',  $_SERVER['HTTP_REFERER']); ?>" + "?q=commerce/todopago/notification/";

		/************* CONFIGURACION DEL API ************************/
		window.TPFORMAPI.hybridForm.initForm({
			callbackValidationErrorFunction: 'validationCollector',
			modalCssClass: 'modal-class',
			modalContentCssClass: 'modal-content',
			beforeRequest: 'initLoading',
			afterRequest: 'stopLoading',
			callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
			callbackCustomErrorFunction: 'customPaymentErrorResponse',
			callbackBilleteraFunction: 'billeteraPaymentResponse',
 			botonPagarConBilleteraId: 'MY_btnPagarConBilletera',
			botonPagarId: 'MY_btnConfirmarPago',
			codigoSeguridadTxt: 'Codigo',
		});



		window.TPFORMAPI.hybridForm.setItem({
			publicKey: security,
            defaultNombreApellido: completeName,
            defaultNumeroDoc: dni,
            defaultMail: mail,
            defaultTipoDoc: defDniType
		});		
		//callbacks de respuesta del pago
		function validationCollector(response) {
			$("#"+response.field).addClass("error");
			console.log(response.error);
			$("#"+response.field+"Error").html("<span class='error'>"+response.error+"</span>");
		}

		function billeteraPaymentResponse(response) {
			if(response.AuthorizationKey){
				window.top.location = url_base + order + "/" + key + "?Answer=" + response.AuthorizationKey;
			}else{
				window.top.location = url_base + order + "/" + key + "?Error=" + response.ResultMessage;
			}
		}
		function customPaymentSuccessResponse(response) {
			window.top.location = url_base + order + "/" + key + "?Answer=" + response.AuthorizationKey;
		}
		function customPaymentErrorResponse(response) {
			if(response.AuthorizationKey){
				window.top.location = url_base + order + "/" + key + "?Answer=" + response.AuthorizationKey;
			}else{
				window.top.location = url_base + order + "/" + key + "?Error=" + response.ResultMessage;
			}
		}

		function initLoading() {
			console.log('Cargando');
		}

		function stopLoading() {
			console.log('Stop loading...');
		}

	</script>
</body>