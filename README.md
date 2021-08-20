# plugin-pagandocheck-magento
Plugin de magento para pagos con Pagando Check.

## Instalar plugin de Pagando Check para pagos en la tienda.

## Requisitos previos

  - Tener una **cuenta** de **empresa** en **Pagando Check**.
  - Tener una tienda de Magento, si no la tiene, puede instalar nuestra tienda demo siguiendo las instrucciones de este [repositorio](https://github.com/pagandocheck/magento-store).
  - Tener permisos de administrador o poder realizar modificaciones o configuraciones del módulo de **Pagando Check**.
  - Ya que el módulo de **Pagando Check** se **utiliza** para que un **sitio web externo pueda procesar pagos**,
  se recomienda que este tipo de pago lo configure una persona con conocimientos técnicos.

### 1. Generar Llaves de Prueba

Para obtener sus llaves de prueba debe ingresar con su cuenta empresarial a https://negocios.pagando.mx

<img width="1266" alt="Captura de Pantalla 2021-08-11 a la(s) 13 40 38" src="https://user-images.githubusercontent.com/88348069/129092607-1e4b96f6-cd8e-4538-a9e0-d2094361eb47.png">

Una vez dentro, en el menú de opciones, dentro del apartado de pagos, ingresara a **API para sitio web**. Y luego hacer clic en **Botón Checkout**.

<img width="784" alt="Captura de Pantalla 2021-08-11 a la(s) 13 44 18" src="https://user-images.githubusercontent.com/88348069/129093055-57741a7a-3a67-4da6-a13b-0ca99a83fdf3.png">

Depués en la opción **Magento**, en la primera sección, podrá generar y recuperar sus llaves de prueba.

<img src="https://rapi-doc.s3.amazonaws.com/Captura+de+Pantalla+2021-08-03+a+la(s)+11.48.57.png" style="display: block; margin-left: auto; margin-right: auto;"/>

### 2. Configuración de módulo Checkout

Aquí se configura la dirección a donde quiere regresar a sus clientes una vez que se ha efectuado el pago, entre otras configuraciones.

<img src="https://rapi-doc.s3.amazonaws.com/Captura+de+Pantalla+2021-08-03+a+la(s)+11.45.21.png" style="display: block; margin-left: auto; margin-right: auto;"/>

### 3. Agregar plugin de Pagando Check

Para obtener el modulo debe clonar este proyecto de github en su equipo de computo con el siguiente comando:

```
git clone git@github.com:pagandocheck/plugin-pagandocheck-magento.git
```

Una vez descargado, debe ingresar dentro del proyecto y trasladar la carpeta **XCNetworks** dentro de la siguiente dirección **/ magento-store / app / code**, despues ejecute estos dos comandos desde la raiz de su tienda, es decir en la carpeta magento-store para refrescar la vista de su tienda.

```
php bin/magento setup:upgrade

php bin/magento setup:static-content:deploy
```

Una vez hecho esto, haga clic en la siguiente liga: http://magento-pagandocheck-store.com/admin para ingresar dentro del panel de administración. Las contraseñas de acceso por default son:

**username**: admin

**password**: admin@123

Posteriormente verá una pantalla similar a la siguiente:

<img width="1264" alt="Captura de Pantalla 2021-08-11 a la(s) 13 30 22" src="https://user-images.githubusercontent.com/88348069/129091235-e0b41260-d339-4e06-a229-a97a505555e8.png">

Dentro de tu panel de administrador en Magento, dirígete al apartado de **STORES**.
Una vez que se abra el menú, damos clic en **Configuration**.

<img src="https://negocios.pagando.mx/img/busqueda-modulo-1.6d34b3ad.png"/>

### 4. Configuración del módulo

Una vez abierta la sección de **Configuration**, en el menú lateral damos clic en **SALES** y se abrirá un submenú y damos clic en la opción de **Payment methods**

<img src="https://negocios.pagando.mx/img/catalogo-modulo-1.6cad793a.png"/>

### 5. Agregar credenciales

Al momento que damos clic en **Payment methods** se mostrarán todas las opciones disponibles de métodos de pago, buscamos la opción llamada **Pagando Check** y hacemos clic sobre ella, para que nuestro método sea visible en el campo **Enabled** seleccionamos la opción **Yes** y rellenamos los campos de **User** y **Public key** con las credenciales que hemos obtenido.
Adicional a esto, el único país disponible para nuestro método de pago es México.

<img width="804" alt="Captura de Pantalla 2021-08-12 a la(s) 13 38 22" src="https://user-images.githubusercontent.com/88348069/129258642-3628c77f-13a4-4f2e-ac1d-1a3415e5b5a7.png">

### 6. Visualización del método de pago

Después de finalizar con la configuración podrá visualizar su nuevo método de pago en su carrito de compras.

<img width="817" alt="payment-methods-list" src="https://user-images.githubusercontent.com/88348069/129280690-51c54846-955b-4508-add1-915e4eedcd26.png">
