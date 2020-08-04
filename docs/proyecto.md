# Estructura de Proyecto

En general el código esta desarrollado en PHP 7+ y el framework que se utiliza es 3.1.10. Puedes encontrar la documentación del mismo en el siguiente [enlace](https://codeigniter.com/user_guide/), se utiliza el patrón de diseño MVC para facilitar la separación de código.

No se incliuyen los fuentes del framework, los puede descargar de [aquí](https://codeigniter.com/download), recuerde que es la versión 3 la que se utiliza.

El estándar de desarrollo que se utilizará para la generación de componentes es el [PSR-12](https://www.php-fig.org/psr/psr-12/), favor de seguir sus lineamentos.

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
class Plugin extends MY_Controller {}
```

**No heredar del mismo será motivo de rechazo en su aplicación.**

### Observaciones
- No tendrá acceso directo a la base de datos, a menos de que le sea concedido a través de un convenio directo con MarketSync
- El intento de acceso directo a la base de datos, sin la autorización correspondiente,le generará sanciones y/o revocación de cuenta.
- Los accesos a la base de datos se realizan a través de la [API](https://github.com/hvalles/marketsync).
- Solamente podrá acceder a aquellas cuentas a las que el usuario responsable le otorgue acceso.

