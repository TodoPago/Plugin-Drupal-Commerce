<head>
    <?php 

        if($_GET["modo"] == "test"){ 
            $env_url = "https://developers.todopago.com.ar/resources/v2/TPBSAForm.min.js";
        }else{ 
            $env_url = "https://forms.todopago.com.ar/resources/v2/TPBSAForm.min.js";  
        }

        $total = number_format($_GET["amount"], 2, ',', ' ');

    ?>  

    <script src="<?php echo $env_url; ?>"></script>

    <link href="<?php echo "css/grid.css"; ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo "css/form_todopago.css"; ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo "css/queries.css"; ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo "css/jquery-3.2.1.min.js"; ?>"></script>

    <!--
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
    <link href="todopago-formulario.css" rel="stylesheet" type="text/css">
    -->
</head>

<body>

<!--  //////////////////////// FH3   /////////////////////////////////-->

<div class="progress">
    <div class="progress-bar progress-bar-striped active" id="loading-hibrid">
    </div>
</div>


<div class="tp_wrapper" id="tpForm">
    <div class="header_info">
        <div class="bold">Total a pagar $<?php echo $total; ?></div>
        <div>Elegí tu forma de pago</div>
    </div>
    <section class="billetera_virtual_tp">
        <div class="tp_row tp-flex">
            <div class="tp_col tp_span_1_of_2 texto_billetera_virtual text_size_billetera">
                <p>Pagá con tu <strong>Billetera Virtual Todo Pago</strong></p>
                <p>y evitá cargar los datos de tu tarjeta</p>
            </div>
            <div class="tp_col tp_span_1_of_2">
                <button id="btn_Billetera" title="Pagar con Billetera" class="tp_btn tp_btn_sm text_size_billetera">
                    Iniciar Sesi&oacute;n
                </button>
            </div>
        </div>
    </section>

    <section class="billeterafm_tp">
        <div class="field field-payment-method">
            <label for="formaPagoCbx" class="text_small">Forma de Pago</label>
            <div class="input-box">
                <select id="formaPagoCbx" class="tp_form_control"></select>
                <span class="error" id="formaPagoCbxError"></span>
            </div>
        </div>
    </section>

    <section class="billetera_tp">
        <div class="tp_row">
             <h3>
                Con tu tarjeta de cr&eacute;dito o d&eacute;bito
            </h3>
        </div>
        <div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <label for="numeroTarjetaTxt" class="text_small">NÚMERO DE TARJETA</label>
                <input id="numeroTarjetaTxt" class="tp_form_control" maxlength="19" title="Número de Tarjeta"
                       min-length="14" autocomplete="off">
                <img src="images/empty.png" id="tp-tarjeta-logo"
                     alt="" style="margin-left: 256px;" />
                <label id="numeroTarjetaLbl" class="error"></label>
            </div>
            <div class="tp_col tp_span_1_of_2">
                <label for="bancoCbx" class="text_small">BANCO</label>
                <select id="bancoCbx" class="tp_form_control" placeholder="Selecciona banco"></select>
                <span class="error" id="bancoCbxError">
            </div>
            <div class="tp_col tp_span_1_of_2 payment-method">
                <label for="medioPagoCbx" class="text_small">MEDIO DE PAGO</label>
                <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
                <span class="error" id="medioPagoCbxError"></span>
            </div>
        </div>

        <section class="tp_row" id="peibox">
            <div class="tp_row">
                <div class="tp_col tp_span_1_of_2 pei_wrapper">
                    <label id="peiLbl" for="peiCbx" class="text_small right">Pago con PEI</label>
                </div>
                <label class="switch" id="switch-pei">
                    <input type="checkbox" id="peiCbx">
                    <span class="slider round"></span>
                    <span id="slider-text"></span>
                </label>
            </div>
        </section>

        <!--div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <label for="medioPagoCbx" class="text_small">Medio de Pago</label>
                <select id="medioPagoCbx" class="tp_form_control" placeholder="Mediopago"></select>
                <span class="error" id="medioPagoCbxError"></span>
            </div>
        </div-->

        <div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <div class="tp_col tp_span_1_of_2">
                    <label for="mesCbx" class="text_small">VENCIMIENTO</label>
                    <div class="tp_row">
                        <div class="tp_col tp_span_1_of_2">
                            <select id="mesCbx" maxlength="2" class="tp_form_control tp_date" placeholder="Mes"></select>
                        </div>
                        <div class="tp_col tp_span_1_of_2">
                            <select id="anioCbx" maxlength="2" class="tp_form_control tp_date"></select>
                        </div>
                    </div>
                    <span id="fechaLbl" class="left error"></span>
                </div>

                <div class="tp_col tp_span_1_of_2">
                    <label for="codigoSeguridadTxt" class="text_small">CÓDIGO DE SEGURIDAD</label>
                    <input id="codigoSeguridadTxt" class="tp_form_control" style="width:100%" maxlength="4" autocomplete="off"/>
                    <span class="error" id="codigoSeguridadTxtError"></span>
                    <label id="codigoSeguridadLbl" class="left tp-label spacer"></label>
                </div>
            </div>

            <div class="tp_col tp_span_1_of_2">
                <div class="tp_col tp_span_1_of_1">
                    <label for="tipoDocCbx" class="text_small">TIPO</label>
                    <select id="tipoDocCbx" class="tp_form_control"></select>
                </div>
                <div class="tp_col tp_span_1_of_2" id="tp-dni-numero">
                    <label for="NumeroDocCbx" class="text_small">NÚMERO</label>
                    <input id="nroDocTxt" maxlength="10" type="text" class="tp_form_control"
                           autocomplete="off" onchange="clearmgserror(this)" onkeyup="clearmgserror(this)"/>
                    <span class="error" id="nroDocLbl"></span>
                </div>
            </div>
        </div>

        <div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <label for="nombreTxt" class="text_small">NOMBRE Y APELLIDO</label>
                <input id="nombreTxt" class="tp_form_control" autocomplete="off" placeholder="" maxlength="50">
                <span class="error" id="nombreLbl"></span>
            </div>
            <div class="tp_col tp_span_1_of_2">
                <label for="emailTxt" class="text_small">EMAIL</label>
                <input id="emailTxt" type="email" class="tp_form_control" placeholder="nombre@mail.com" data-mail=""
                       autocomplete="off"/><br/>
                <span class="error" id="emailLbl"></span>
            </div>
        </div>

        <div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <label for="promosCbx" class="text_small">SELECCIONÁ LA CANTIDAD DE CUOTAS</label>
                <select id="promosCbx" class="tp_form_control"></select>
                <span class="error" id="promosCbxError"></span>
            </div>
            <div class="tp_col tp_span_1_of_2">
                <div class="clear"><label id="promosLbl" class="left"></label></div>
            </div>

        </div>
        
        <div class="tp_row">
            <div class="tp_col tp_span_1_of_2">
                <label id="tokenPeiLbl" for="tokenPeiTxt" class="info_pei" style="text-transform:uppercase"></label>
                <input id="tokenPeiTxt"/>
                <span class="error" id="peiTokenTxtError"></span>
            </div>
        </div>

        <div class="tp_row">
            <div class="tp_col tp_span_2_of_2">
                <button id="btn_ConfirmarPago" class="tp_btn" title="Pagar" class="button"><span>Pagar</span></button>
            </div>
            <div class="tp_col tp_span_2_of_2">
                <div class="text_legals">
                    AL CONFIRMAR EL PAGO ACEPTO LOS <a href="https://www.todopago.com.ar/terminos-y-condiciones-comprador"  title="Términos y Condiciones" target="_blank" id="tycId" class="tp_color_text">TÉRMINOS Y CONDICIONES</a> DE TODO PAGO.
                </div>
            </div>
        </div>

    </section>
    <div class="tp_row">
        <div id="tp-powered">
            Powered by <img id="tp-powered-img" src="images/tp_logo_prod.png"/>
        </div>
    </div>

</div>

<script language="javascript">

    var tpformJquery = $.noConflict();
    var urlScript = "<?php echo $env_url; ?>";

    var order = "<?php echo $_GET['order']?>";
    var key = "<?php echo $_GET['key'] ?>";
    //securityRequesKey, esta se obtiene de la respuesta del SAR
    url = window.location.pathname;
    base = url.split("/");
    url_base = "<?php  echo $url = preg_replace('/\?.*/', '',  $_SERVER['HTTP_REFERER']); ?>" + "?q=commerce/todopago/notification/";

    var urlSuccess = url_base + order + "/" + key ; 
    var urlError = url_base + order + "/" + key ;

    var security = "<?php echo $_GET['prk']; ?>";
    var mail = "<?php echo $_GET['mail']?>";
    var completeName = "<?php echo $_GET['completename']?>";
    var defDniType = 'DNI';
    var medioDePago = document.getElementById('medioPagoCbx');
    var tarjetaLogo = document.getElementById('tp-tarjeta-logo');
    var poweredLogo = document.getElementById('tp-powered-img');
    var numeroTarjetaTxt = document.getElementById('numeroTarjetaTxt')
    var poweredLogoUrl = "images/";
    var emptyImg = "images/empty.png";
    var switchPei = tpformJquery("#switch-pei");
    var sliderText = tpformJquery("#slider-text");
    var peiCbx = tpformJquery("#peiCbx");

    var idTarjetas = {
        42: 'VISA',
        43: 'VISAD',
        1: 'AMEX',
        2: 'DINERS',
        6: 'CABAL',
        7: 'CABALD',
        14: 'MC',
        15: 'MCD'
    };

    var diccionarioTarjetas = {
        'VISA': 'VISA',
        'VISA DEBITO': 'VISAD',
        'AMEX': 'AMEX',
        'DINERS': 'DINERS',
        'CABAL': 'CABAL',
        'CABAL DEBITO': 'CABALD',
        'MASTER CARD': 'MC',
        'MASTER CARD DEBITO': 'MCD',
        'NARANJA': 'NARANJA'
    };

    /************* HELPERS *************/

    numeroTarjetaTxt.onblur = clearImage;

    function clearImage() {
        tarjetaLogo.src = emptyImg;
    }

    function cardImage(select) {
        var tarjeta = idTarjetas[select.value];
        if (tarjeta === undefined) {
            tarjeta = diccionarioTarjetas[select.textContent];
        }
        if (tarjeta !== undefined) {
            tarjetaLogo.src = 'https://forms.todopago.com.ar/formulario/resources/images/' + tarjeta + '.png';
            tarjetaLogo.style.display = 'block';
        }
    }


    /************* SMALL SCREENS DETECTOR (?) *************/
    function detector() {
        console.log(tpformJquery("#tp-form").width());
        var tpFormWidth = tpformJquery("#tp-form").width();
        if (tpFormWidth < 950) {
            tpformJquery(".tp-col-right").css("flex-basis", "350px");
            tpformJquery(".tp-col-left").css("flex-basis", "350px");
        }
        if (tpFormWidth < 800) {
            tpformJquery(".tp-col-right").css("flex-basis", "300px");
            tpformJquery(".tp-col-left").css("flex-basis", "300px");
        }
        if (tpFormWidth < 720) {
            tpformJquery(".tp-container").css({
                "margin-left": "0%",
                "width": "100%",
                "padding": "5px"
            });
            tpformJquery(".left-col").width('100%');
            tpformJquery(".right-col").width('100%');
            tpformJquery(".advertencia").css("height", "50px");
            tpformJquery(".row").css({
                "height": "60px",
                "width": "95%",
                "margin-bottom": "30px"
            });
            tpformJquery("#codigo-col").css("margin-bottom", "10px");
            tpformJquery("#row-pei").css("height", "100px");
            tpformJquery(".tp-col-left").css("flex-basis", "320px");
            tpformJquery(".tp-col-right").css("flex-basis", "320px");
            tpformJquery(".tp-container-2-columns").css({
                "height": "400px"
            });
        }
        if (tpformJquery("#tp-form").width() < 600) {
            tpformJquery(".tp-container-2-columns").css({"margin-top": "200px"});
        }
    }

    loadScript(urlScript, function () {
        loader();
    });

    function loadScript(url, callback) {
        var entorno = (url.indexOf('developers') === -1) ? 'prod' : 'developers';
        console.log(entorno);
        poweredLogo.src = poweredLogoUrl + 'tp_logo_' + entorno + '.png';
        var script = document.createElement("script");
        script.type = "text/javascript";
        if (script.readyState) {  //IE
            script.onreadystatechange = function () {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {  //et al.
            script.onload = function () {
                callback();
            };
        }
        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }

    function loader() {
        tpformJquery("#loading-hibrid").css("width", "50%");
        setTimeout(function () {
            ignite();
            tpformJquery(".payment-method").hide();
            tpformJquery(".billeterafm_tp").hide();
        }, 100);

        setTimeout(function () {
            tpformJquery("#loading-hibrid").css("width", "100%");
        }, 1000);

        setTimeout(function () {
            tpformJquery(".progress").hide('fast');
        }, 2000);

        setTimeout(function () {
            tpformJquery("#tpForm").fadeTo('fast', 1);
        }, 2200);
    }

    //callbacks de respuesta del pago
    window.validationCollector = function (parametros) {
        console.log("Validando los datos");
        console.log(parametros.field + " -> " + parametros.error);
        var input = parametros.field;

        if (input.search("Txt") !== -1) {
            label = input.replace("Txt", "Lbl");
        } else {
            label = input.replace("Cbx", "Lbl");
        }
        if (document.getElementById(label) !== null){
            document.getElementById(label).innerHTML = parametros.error;
        }

        tpformJquery("#codigoSeguridadLbl").css("color","red");
    };

    function billeteraPaymentResponse(response) {
        console.log("Iniciando billetera");
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        if (response.AuthorizationKey) {
            window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
        } else {
            window.location.href = urlError + "&Error=" + response.ResultMessage;
        }
    }

    function customPaymentSuccessResponse(response) {
        console.log("Success");
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        window.location.href = urlSuccess + "&Answer=" + response.AuthorizationKey;
    }

    function customPaymentErrorResponse(response) {
        console.log(response.ResultCode + " -> " + response.ResultMessage);
        if (response.AuthorizationKey) {
            window.location.href = urlError + "&Answer=" + response.AuthorizationKey + "&Error=" + response.ResultMessage;
        } else {
            window.location.href = urlError + "&Error=" + response.ResultMessage;
        }
    }

    window.initLoading = function () {
        console.log("init");
        cardImage(medioDePago);
        tpformJquery("#peibox").hide();
    };

    window.stopLoading = function () {
        console.log('Stop loading...');
        tpformJquery("#peibox").hide();

        //if(tpformJquery('#tpform_overlay:visible').length == 1){
            //tpformJquery("#tpform_content_fixed").height(590);
        //}

        if(tpformJquery('#peiLbl').is(':empty')){
            tpformJquery("#peibox").hide("fast");
        }else{
            tpformJquery("label > p").each(function() {
                var clean_strip = tpformJquery(this).text().replace("<br>","");
                tpformJquery(this).html(clean_strip);
            });

            tpformJquery("#peibox").show("slow");
            activateSwitch(getInitialPEIState());
            switchPei.css("display", "block");

        }

        tpformJquery("#codigoSeguridadLbl").css("color","#909090");
    };

    // Verifica que el usuario no haya puesto para solo pagar con PEI y actúa en consecuencia
    function activateSwitch(soloPEI) {
        readPeiCbx();

        if (!soloPEI) {
            tpformJquery("#peiCbx").click(function () {
                
                console.log("CHECKED", peiCbx.prop("checked"));
                if (peiCbx.prop("checked")) {
                    peiCbx.prop("checked", false);
                    switchPei.prop("checked", true);
                    peiCbx.prop("checked", true);  
                    sliderText.text("SÍ");
                    sliderText.css('transform', 'translateX(0px)');
                }else{

                    peiCbx.prop("checked", true);
                    switchPei.prop("checked", false);
                    peiCbx.prop("checked", false); 
                    sliderText.text("NO");
                    sliderText.css('transform', 'translateX(25px)');
                }
            });
        }
    }

    function readPeiCbx() {
        if (peiCbx.prop("checked", true)) {
            switchPei.prop("checked", true);
            sliderText.text("SÍ");
            sliderText.css('transform', 'translateX(0px)');
        } else {
            switchPei.prop("checked", true);
            sliderText.text("NO");
            sliderText.css('transform', 'translateX(25px)');
        }
    }

    function getInitialPEIState() {
        return (tpformJquery("#peiCbx").prop("disabled"));
    }

    tpformJquery('#peiLbl').bind("DOMSubtreeModified",function(){
        tpformJquery("#peibox").hide();
    });

    function ignite() {
        /************* CONFIGURACION DEL API ************************/
        window.TPFORMAPI.hybridForm.initForm({
            callbackValidationErrorFunction: 'validationCollector',
            callbackBilleteraFunction: 'billeteraPaymentResponse',
            callbackCustomSuccessFunction: 'customPaymentSuccessResponse',
            callbackCustomErrorFunction: 'customPaymentErrorResponse',
            botonPagarId: 'btn_ConfirmarPago',
            botonPagarConBilleteraId: 'btn_Billetera',
            modalCssClass: 'modal-class',
            modalContentCssClass: 'modal-content',
            beforeRequest: 'initLoading',
            afterRequest: 'stopLoading'
        });

        /************* SETEO UN ITEM PARA COMPRAR ************************/
        window.TPFORMAPI.hybridForm.setItem({
            publicKey: security,
            defaultNombreApellido: completeName,
            defaultMail: mail,
            defaultTipoDoc: defDniType
        });
    }

    function clearmgserror(values){
       console.log(values);
       var id_error = values.id+"Error";
       console.log(id_error);
       tpformJquery('#'+id_error).hide();
    }

</script>

<!--  /////////////////////////////////////////////////////////-->

</body>
