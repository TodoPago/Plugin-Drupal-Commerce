<a name="inicio"></a>
Drupal
============

Plug in para la integración con gateway de pago <strong>Todo Pago</strong>
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalación](#instalacion)
- [Configuración](#configuracion)
 - [Configuración plug in](#confplugin)
- [Datos adiccionales para prevención de fraude](#cybersource) 
- [Consulta de transacciones](#constrans)
- [Devoluciones](#devoluciones)
- [Tablas de referencia](#tablas)

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
<<<<<<< HEAD
Descomentar: <em>extension=php_curl.dll</em>, <em>extension=php_openssl.dll</em> y <em>extension=php_soap.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.
=======
Descomentar: <em>extension=php_soap.dll</em> y <em>extension=php_openssl.dll</em> del php.ini, ya que para la conexión al gateway se utiliza la clase <em>SoapClient</em> del API de PHP.
>>>>>>> master
<br />
[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
##Configuración

<a name="confplugin"></a>
####Configuración plug in
Una vez instalado el plug in, ir a Store -> Configuration -> Payment Methods -> Todo Pago -> Enable payment method: Todo Pago ![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/3-configuracion.png)

La configuracion del Plug in esta dividido en 4 solapas desplegables (GENERAL, AMBIENTE DEVELOPERS, AMBIENTE PRODUCCION, SERVICIO) 
![imagen de solapas de configuracion](https://raw.githubusercontent.com/TodoPago/imagenes/master/drupalcommerce/4-solapas.png)
<br />

[<sub>Volver a inicio</sub>](#inicio)
<<<<<<< HEAD
=======
<a name="tca"></a>
>>>>>>> master

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
