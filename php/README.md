# Ejemplo de desarrollo de componentes (plugins)

El siguiente ejemplo se basa en un componente que permite actualizar el registro de pie de ficha, de un MarketPlace en particular a la vez, y para todos los artículos que el cliente haya publicado en dicho MarketPlace.

**Registro de Pie de Ficha** Se refiera a una descripción genérica adicional, para cada artículo y separada por MarketPlace, que permita incluir texto genérico adicional a cada ficha de cada producto.

**Supuestos** 
- Para este componente no es necesario que el cliente registre su aprobación, ya que será el usuario final el que brinde la información necesaria para realizar dichas actualizaciones.
- El usuario final es consciente de que el agregado puede o no ser incluído en la ficha debido básicamente a la limitación de espacio en las publicaciones de cada MarketPlace.
- El texto a agregar no contiene código HTML y el formato correspondiente es responsabilidad del usuario.

**Entrada** 
- Cliente, el usurio correspondiente que usa el componente.
- MarketPlace, selecciona de una lista de marketplaces activos el que desea actualizar.
- Pie de Ficha, la descripción a agregar al final de la ficha de producto.

***Salida**
- Un registro en settings con la configuración apropiada del cliente correspondiente.

**Salida Adicional**
- Una orden de republicación de todos los productos ya registrados en el MarketPlace.

### Flujo

Su componente deberá heredar de [Plugin](https://github.com/hvalles/plugin-mks/blob/master/php/application/controllers/plugins/Plugin.php), este a su vez ya hereda de MY_Controller e implementará los elementos de seguridad, necesarios para su operación.

La mayoría de las operaciones se realiza en Plugin, pero usted puede sobre-escribir las siguients funciones:
- `protected function pre_run() {` Esta funcion de inicialización se corre al entrar al index, pero antes de publicar la vista.
- `public function run() {` El componente ejecutará la función index(), hay dos posibles escenarios: 
    - Si no se encuentra registrado se desplegará la vista de registro ya sea con los "campos_registro" o vista "vista_registro", que especifico al dar de alta el componente.
    - Si el componente ya se encuentra registrado para el cliente, 
desplegará la vista que alimento en "vista" o la vista predeterminada con la información contenida en "campos"

- `public function register() {` 
Ambas vistas dispondrán de los datos de registrados al dar de alta el ["plugin"](https://github.com/hvalles/plugin-mks/blob/master/docs/registro.md)

Al momento de registrarse se debe ejecutar la función "register", llamando al padre si esta es sobre-escrita.

Al momento de ejecutar la vista principal se ejecutará la función run(), por lo que aqui validará los elementos que considere necesarios y emitirá el resultado correspondiente, será la función principal.

### Llamadas a la API

- Para probar usted contará con sus propias llaves públicas/privadas.
- En caso de producción, se tomará la información del cliente y de la base de datos.


Puede ver el código en el siguiente [enlace](https://github.com/hvalles/plugin-mks/blob/master/php/application/controllers/plugins/Firmaficha.php)