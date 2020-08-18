# Estructura de Proyecto

En general el código esta desarrollado en PHP 7+ y el framework que se utiliza es 3.1.10. Puedes encontrar la documentación del mismo en el siguiente [enlace](https://codeigniter.com/user_guide/), se utiliza el patrón de diseño MVC para facilitar la separación de código.

No se incluyen los fuentes del framework, los puede descargar de [aquí](https://codeigniter.com/download), recuerde que es la versión 3 la que se utiliza.

El estándar de desarrollo que se utilizará para la generación de componentes es el [PSR-12](https://www.php-fig.org/psr/psr-12/), favor de seguir sus lineamentos.


Se espera que al final se entreguen los fuentes de la integración de:
1. El Plugin de Autorización de Acceso del cliente al Marketplace
2. La librería que implemente la interfaz de Market.php para realizar la conexión con el MPS.
3. La documentación correspondiente a la integración y lo referido en el archivo readme.txt
4. La documentación correspondiente al registro de la cuanta y el proceso para iniciar operaciones con el MPS.
5. Los resultados de las pruebas elaboradas por su implementación.

### Inicio de flujo
La estructura comienza en la carpeta public, en donde se localiza el index.php.

Así mismo se ubica el controlador de acuerdo al url, el nombre de la funcion y los parámetros en caso de ser necesarios

https://web.marketsync.mx/plugin/revision?product_id=999999

El url se compone de los siguientes elementos
1. Dominio `https://web.marketsync.mx/`
2. Folder de ubicación `plugin`
3. Controlador a ejecutar `revision`
4. Funcion a ejecutar implicita `index`, si no se menciona otra.
5. Parámetros `product_id`

#### De lo anterior podemos inferir:

- El controlador se ubica en el folder `plugin`
- Se esta llamando a un controlador llamado `revision`.
- El mismo contiene una función pública llamada `index()`

Todos los controladores de plugins se ubican el la carpeta `application/controllers/plugins`.

Todas las vistas de los plugins se ubican en `application/views/plugins`.

Las imágenes deberan de estar contenidas en el directorio `public/static/img/plugins`, usualmente será el logo, pero de requerir más de dos imágenes, favor de crear una carpeta independiente dentro del citado directorio.

En el caso de requerir más de un archivo para su componente habrá de crear una carpeta con el nombre del componente y ahí ubicará los elementos requeridos, ya sea bajo controller y/o views.

Como es el estándar de CI los modelos se ubican en `application/models`.

Todos los controladores deberán de heredar de My_Controller, ya que los mecanismos de seguridad se aplican en el mismo

```php
class Plugin extends Plugin {}

```
├── application
│   ├── controllers
│   │   └── plugins
│   │       ├── Firmaficha.php
│   │       └── Plugin.php
│   ├── core
│   │   └── MY_Controller.php
│   ├── helpers
│   │   └── autoform_helper.php
│   ├── libraries
│   │   └── Market.php
│   └── views
│       └── plugins
│           ├── plugin.php
│           └── register.php
└── public
    └── static
        └── img
            └── imagenes
                └── no-image.png
                
**No heredar del mismo será motivo de rechazo en su aplicación.**

### Pra los integradors de MarketPlaces
1. Se debe de crear un plugin que permita recopilar la información del cliente
2. El mismo se comunicara con el MarketPlace que lo permita para autorizar el acceso
del componente a los datos del cliente.
3. De no haber un punto dos viable, se deberá derecopilar toda la información necesaria para efectuar la integración en el punto 1.
4. De haber una llmada Callback desde el MarketPlace, apuntará al mismo plugin que tiene acceso de preferencia a una función llamada callback.
5. Al terminar debera hacer una llamada a verificar los settings globales de su plugin y almacenarlos en caso de que no existan, así como los del cliente en cuestión para que se almacene la configuración necesaria para acceder a los datos del cliente en el MPS.
6. Entre los datos globales son los comunes a todos los clientes como:
    - Url del portal
    - Identificador de MarketPlace
    - Si la marca,color,upc son requeridos, etc.
    - Entre otrosd
7. Entre los datos de cada cliente puede encontrar:
    - SellerID
    - Token
    - Expiration Date
    - Refresh Token
    - Entre otros

### Observaciones
- No tendrá acceso directo a la base de datos, a menos de que le sea concedido a través de un convenio directo con MarketSync
- El intento de acceso directo a la base de datos, sin la autorización correspondiente,le generará sanciones y/o revocación de cuenta.
- Los accesos a la base de datos se realizan a través de la [API](https://github.com/hvalles/marketsync).
- Solamente podrá acceder a aquellas cuentas a las que el usuario responsable le otorgue acceso.

