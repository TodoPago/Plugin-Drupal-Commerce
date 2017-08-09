<html>
<head>
	<?php if($_GET["modo"] == "test"){ ?>
		<script src='https://developers.todopago.com.ar/resources/TPHybridForm-v0.1.js'></script>
	<?php }else{ ?>
		<script src='https://forms.todopago.com.ar/resources/TPHybridForm-v0.1.js'></script>
	<?php } ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
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
					<label for="formaDePagoCbx" class="tp-label required"><em>*</em>Medio de Pago</label>
					<div class="input-box">
						<select id="formaDePagoCbx"></select><span class="error" id="formaDePagoCbxError"></span>
					</div>
				</div>
				<div class="field">
					<label for="bancoCbx" class="tp-label required spacer"><em>*</em>Banco Emisor</label>
					<div class="input-box">
						<select id="bancoCbx"></select><span class="error" id="bancoCbxError"></span>
					</div>
				</div>
				<div class="field">
					<label for="promosCbx" class="tp-label required spacer"><em>*</em>Cantidad de cuotas</label>
					<div class="input-box">
						<select id="promosCbx" class="left"></select><span class="error" id="promosCbxError"></span>
						<label id="labelPromotionTextId" class="left tp-label"></label>
						<div class="clear"></div>
					</div>
				</div>
				<!-- Para los casos en el que el comercio opera con PEI -->
				<div class="form-row tp-no-cupon">
					<label id="labelPeiCheckboxId"></label>
					<input id="peiCbx"/>
				</div>

				<div class="field">
					<label for="numeroTarjetaTxt" class="tp-label required spacer"><em>*</em>Número de Tarjeta</label>
					<div class="input-box">
						<input class="input-text required-entry" id="numeroTarjetaTxt" maxlength="16" title="Número de Tarjeta"/>
						<span class="error" id="numeroTarjetaTxtError"></span>
					</div>
				</div>
				<div class="field">
					<label for="mesTxt" class="tp-label required spacer"><em>*</em>Fecha de Vencimiento</label>
					<div class="dateFields">
						<div class="input-box">
							<input id="mesTxt" maxlength="2" class="left input-text required-entry small">
							<span class="left spacer">/</span>
							<input id="anioTxt" maxlength="2" class="left input-text required-entry small">
						</div>
						<div class="clear"></div><span class="error" id="anioTxtError"></span>
					</div>
				</div>
				<div class="field">
					<label for="codigoSeguridadTxt" id="labelCodSegTextId" class="tp-label required spacer"><em>*</em>Código de Seguridad</label>
					<div class="input-box">
						<input id="codigoSeguridadTxt" class="left input-text required-entry small"/>
						<span class="error" id="codigoSeguridadTxtError"></span>
						<div class="clear"></div>
					</div>
				</div>
				<div class="field">
					<label for="apynTxt" class="tp-label required"><em>*</em>Nombre y Apellido</label>
					<div class="input-box">
						<input id="apynTxt"/>
						<span class="error" id="apynTxtError"></span>
					</div>
				</div>
				<div class="field">
					<label for="tipoDocCbx" class="tp-label required spacer"><em>*</em>Documento</label>
					<div>
						<div class="input-box">
							<select id="tipoDocCbx" class="left"></select>
							<span class="left spacer">&nbsp;</span>
							<input id="nroDocTxt" class="left input-text required-entry" />
						</div>
						<div class="clear"></div>
						<span class="error" id="nroDocTxtError"></span>
					</div>
				</div>
				<div class="field">
					<label for="emailTxt" class="tp-label required"><em>*</em>Email</label>
					<div class="input-box">
						<input id="emailTxt"/><br/>
						<span class="error" id="emailTxtError"></span>
					</div>
				</div>
				<div><!-- Para los casos en el que el comercio opera con PEI -->
			    	<label id="labelPeiTokenTextId"></label>
					<input id="peiTokenTxt"/>
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
</body></html>
