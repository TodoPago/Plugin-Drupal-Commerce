<a name="inicio"></a>
Drupal
============

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
 - [Configuración plug in](#confplugin)
- [Credenciales](#credenciales) 
- [Datos adiccionales para prevención de fraude](#cybersource) 
- [Consulta de transacciones](#constrans)
- [Devoluciones](#devoluciones)
- [Formulario de pago integrado](#formulario)
- [Tablas de referencia](#tablas)
- [Tabla de errores](#codigoerrores)

[](#consideracionesgenerales)
## Consideraciones Generales
El plug in de pagos de <strong>Todo Pago</strong>, provee a las tiendas Drupal Commerce de un nuevo m&eacute;todo de pago, integrando la tienda al gateway de pago. La versión de este plug in esta testeada en PHP 5.3 en adelante y Drupal 7 con Drupal Commerce  1.11

<a name="instalacion"></a>
## Instalación
1. Ir a Modules (pantalla que nos muestra los modulos instalados)
2.	Ir a la opción <strong>Install new module</strong>
3.	Seleccionar el archivo zip que contiene el plugin y presionar <strong>Install</strong>. A continuación Drupal nos mostrará la instalación del plugin mediante una barra de progreso.
4.	Una vez que la instalación haya concluido correctamente nos aparece la opción <strong>Enable newly added modules</strong> la cual se debe presionar para volver a la pantalla Modules.
5.	Activar el módulo seleccionando el checkbox <strong>Enable</strong> y guardar el cambio presionando el botón <strong>Save Configuracion</strong> ubicado al final de la página

![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/1-instalacion.png)

![imagen de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/2-activacion.PNG)

Observaci&oacute;n:

Descomentar: <em>extension=php_curl.dll</em>, <em>extension=php_openssl.dll</em> y <em>extension=php_soap.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.

<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
##Configuración

<a name="confplugin"></a>
####Configuración plug in
Una vez instalado el plug in, ir a Store -> Configuration -> Payment Methods -> Todo Pago -> Enable payment method: Todo Pago ![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/3-configuracion.png)

La configuracion del Plug in esta dividido en 4 solapas desplegables (GENERAL, AMBIENTE DEVELOPERS, AMBIENTE PRODUCCION, SERVICIO) 
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/solapas-a.png)
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/solapas-b.png)
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/solapas-c.png)
<br />

[<sub>Volver a inicio</sub>](#inicio)

<a name="Credenciales"></a>
####Credenciales
En la secciones de ambientes de developers y produccion, se debe ingresar el MerchantID, Authorization y Security de Todo Pago.
Estos se pueden obtener desde la pagina de Todo Pago o mediante el boton "Obtener credenciales".
Al Ingresar el usuario de todo pago se completan los campos con los datos del ambiente seleccionado.

![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/login-credenciales.png)

Nota: El boton "Credenciales" se habilita si el ambiente en cuestion se encuentra seleccionado en Payment settings->Ambiente.

[<sub>Volver a inicio</sub>](#inicio)

<a name="cybersource"></a>
## Prevención de Fraude

Consideraciones Generales (para todos los verticales, por defecto RETAIL)
El plug in, toma valores estándar del framework para validar los datos del comprador. Principalmente se utiliza la variable global $user y la variable $profile que se accede con la consulta.
commerce_customer_profile_load($order->commerce_customer_billing[LANGUAGE_NONE][0]['profile_id']);
<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="constrans"></a>
## Consulta de Transacciones
El plugin genera un link para ver el estado de las transacciones,  para acceder hay que hacer click en “Todo Pago Status”

![imagen consulta de trnasacciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/5-status%20de%20las%20operaciones.png)<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="devoluciones"></a>
## Devolucinones
Es posible realizar devoluciones o reembolsos de las operaciones realizadas con Todo Pago. Para ello dirigirse a "Store Settings" -> "Todo Pago", allí deberá hacerse click en el link "Realizar una devolucion".
![imagen devoluciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/drupaldevo.png)<br />

[<sub>Volver a inicio</sub>](#inicio)

<a name="formulario"></a>
## Formulario de pago integrado
El plugin tiene dos opciones de formulario para emplear en el proceso de pago. El formulario externo, que redirecciona a un formulario externo en Todo Pago y el fomulario integrado que permite hacer el pago dentro del e-commerce.<br>

**Habilitar formulario hibrido**<br>
En la pagina de configuracion de Todopago se puede encontrar el select "Formularion on-site", este permite activar el formulario externo o hibrido.
![imagen devoluciones](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/config-form-hibrido.png)<br />

El formulario Hibrido se mostrara en la etapa final del proceso de pago "Confirmar pago". 

[<sub>Volver a inicio</sub>](#inicio)

<a name="tablas"></a>
## Tablas de Referencia
######[Provincias](#p)

<a name="p"></a>
<p>Provincias</p>
<table>
<tr><th>Provincia</th><th>Código</th></tr>
<tr><td>CABA</td><td>C</td></tr>
<tr><td>Buenos Aires</td><td>B</td></tr>
<tr><td>Catamarca</td><td>K</td></tr>
<tr><td>Chaco</td><td>H</td></tr>
<tr><td>Chubut</td><td>U</td></tr>
<tr><td>Córdoba</td><td>X</td></tr>
<tr><td>Corrientes</td><td>W</td></tr>
<tr><td>Entre Ríos</td><td>E</td></tr>
<tr><td>Formosa</td><td>P</td></tr>
<tr><td>Jujuy</td><td>Y</td></tr>
<tr><td>La Pampa</td><td>L</td></tr>
<tr><td>La Rioja</td><td>F</td></tr>
<tr><td>Mendoza</td><td>M</td></tr>
<tr><td>Misiones</td><td>N</td></tr>
<tr><td>Neuquén</td><td>Q</td></tr>
<tr><td>Río Negro</td><td>R</td></tr>
<tr><td>Salta</td><td>A</td></tr>
<tr><td>San Juan</td><td>J</td></tr>
<tr><td>San Luis</td><td>D</td></tr>
<tr><td>Santa Cruz</td><td>Z</td></tr>
<tr><td>Santa Fe</td><td>S</td></tr>
<tr><td>Santiago del Estero</td><td>G</td></tr>
<tr><td>Tierra del Fuego</td><td>V</td></tr>
<tr><td>Tucumán</td><td>T</td></tr>
</table>
[<sub>Volver a inicio</sub>](#inicio)

<a name="codigoerrores"></a>    
## Tabla de errores     

<table>		
<tr><th>Id mensaje</th><th>Mensaje</th></tr>				
<tr><td>-1</td><td>Aprobada.</td></tr>
<tr><td>1081</td><td>Tu saldo es insuficiente para realizar la transacción.</td></tr>
<tr><td>1100</td><td>El monto ingresado es menor al mínimo permitido</td></tr>
<tr><td>1101</td><td>El monto ingresado supera el máximo permitido.</td></tr>
<tr><td>1102</td><td>La tarjeta ingresada no corresponde al Banco indicado. Revisalo.</td></tr>
<tr><td>1104</td><td>El precio ingresado supera al máximo permitido.</td></tr>
<tr><td>1105</td><td>El precio ingresado es menor al mínimo permitido.</td></tr>
<tr><td>2010</td><td>En este momento la operación no pudo ser realizada. Por favor intentá más tarde. Volver a Resumen.</td></tr>
<tr><td>2031</td><td>En este momento la validación no pudo ser realizada, por favor intentá más tarde.</td></tr>
<tr><td>2050</td><td>Lo sentimos, el botón de pago ya no está disponible. Comunicate con tu vendedor.</td></tr>
<tr><td>2051</td><td>La operación no pudo ser procesada. Por favor, comunicate con tu vendedor.</td></tr>
<tr><td>2052</td><td>La operación no pudo ser procesada. Por favor, comunicate con tu vendedor.</td></tr>
<tr><td>2053</td><td>La operación no pudo ser procesada. Por favor, intentá más tarde. Si el problema persiste comunicate con tu vendedor</td></tr>
<tr><td>2054</td><td>Lo sentimos, el producto que querés comprar se encuentra agotado por el momento. Por favor contactate con tu vendedor.</td></tr>
<tr><td>2056</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2057</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>2059</td><td>La operación no pudo ser procesada. Por favor intentá más tarde.</td></tr>
<tr><td>90000</td><td>La cuenta destino de los fondos es inválida. Verificá la información ingresada en Mi Perfil.</td></tr>
<tr><td>90001</td><td>La cuenta ingresada no pertenece al CUIT/ CUIL registrado.</td></tr>
<tr><td>90002</td><td>No pudimos validar tu CUIT/CUIL.  Comunicate con nosotros <a href="#contacto" target="_blank">acá</a> para más información.</td></tr>
<tr><td>99900</td><td>El pago fue realizado exitosamente</td></tr>
<tr><td>99901</td><td>No hemos encontrado tarjetas vinculadas a tu Billetera. Podés  adherir medios de pago desde www.todopago.com.ar</td></tr>
<tr><td>99902</td><td>No se encontro el medio de pago seleccionado</td></tr>
<tr><td>99903</td><td>Lo sentimos, hubo un error al procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99970</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99971</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99977</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99978</td><td>Lo sentimos, no pudimos procesar la operación. Por favor reintentá más tarde.</td></tr>
<tr><td>99979</td><td>Lo sentimos, el pago no pudo ser procesado.</td></tr>
<tr><td>99980</td><td>Ya realizaste un pago en este sitio por el mismo importe. Si querés realizarlo nuevamente esperá 5 minutos.</td></tr>
<tr><td>99982</td><td>En este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99983</td><td>Lo sentimos, el medio de pago no permite la cantidad de cuotas ingresadas. Por favor intentá más tarde.</td></tr>
<tr><td>99984</td><td>Lo sentimos, el medio de pago seleccionado no opera en cuotas.</td></tr>
<tr><td>99985</td><td>Lo sentimos, el pago no pudo ser procesado.</td></tr>
<tr><td>99986</td><td>Lo sentimos, en este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99987</td><td>Lo sentimos, en este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99988</td><td>Lo sentimos, momentaneamente el medio de pago no se encuentra disponible. Por favor intentá más tarde.</td></tr>
<tr><td>99989</td><td>La tarjeta ingresada no está habilitada. Comunicate con la entidad emisora de la tarjeta para verificar el incoveniente.</td></tr>
<tr><td>99990</td><td>La tarjeta ingresada está vencida. Por favor seleccioná otra tarjeta o actualizá los datos.</td></tr>
<tr><td>99991</td><td>Los datos informados son incorrectos. Por favor ingresalos nuevamente.</td></tr>
<tr><td>99992</td><td>La fecha de vencimiento es incorrecta. Por favor seleccioná otro medio de pago o actualizá los datos.</td></tr>
<tr><td>99993</td><td>La tarjeta ingresada no está vigente. Por favor seleccioná otra tarjeta o actualizá los datos.</td></tr>
<tr><td>99994</td><td>El saldo de tu tarjeta no te permite realizar esta operacion.</td></tr>
<tr><td>99995</td><td>La tarjeta ingresada es invalida. Seleccioná otra tarjeta para realizar el pago.</td></tr>
<tr><td>99996</td><td>La operación fué rechazada por el medio de pago porque el monto ingresado es inválido.</td></tr>
<tr><td>99997</td><td>Lo sentimos, en este momento la operación no puede ser realizada. Por favor intentá más tarde.</td></tr>
<tr><td>99998</td><td>Lo sentimos, la operación fue rechazada. Comunicate con la entidad emisora de la tarjeta para verificar el incoveniente o seleccioná otro medio de pago.</td></tr>
<tr><td>99999</td><td>Lo sentimos, la operación no pudo completarse. Comunicate con la entidad emisora de la tarjeta para verificar el incoveniente o seleccioná otro medio de pago.</td></tr>
</table>

[<sub>Volver a inicio</sub>](#inicio)
