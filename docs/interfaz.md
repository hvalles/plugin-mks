
## Librería de acoplamiento de Marketplaces a MarketSync
 Todas las librerías deberán de cumplir con la interfaz en lo posible.
 Las librerías hacen referencia a una librería auxiliar para movimientos 
 hacia la base de datos; revise la especificación correspondiente en el
 siguiente [enlace](auxiliar.md):

## Generalidades
 Todas las llamadas a menos de que se especifique otra cosa serán hacia 
 el MarketPlace en cuestión. 

### Todas las llamadas POST,PUT,DELETE deberán de generar un registro en la
 bitacora y reportar el estatus de la misma addBitacora($data);
 La llamada GET a recuperar pedidos también genera un registro.
 En caso de error este será almacenado como parte de la bitácora.

### Los MarketPlaces tienen limitantes en cantidad de llamadas por periodo de tiempo
 Recibirá un grupo de productos por Actualizar/Registrar, 
 siendo respondabilidad  de la librería solamente procesar los elementos dentro de 
 los limites de la restricción correspondiente. Haga las pausas correspondientes para evitar infracciones 
 y no exceda la ejecución en más de 5 minutos por proceso. (Estime el tiempo por registro y calcule un escenario pesimista para la cantidad de registros que pueda procesar)

### Flujo de productos por publicar en Lotes
 1. Se agrega el producto con estatus ITEM_SIN_CONFIRMAR
 2. Se confirma el alta del producto cambia status a ITEM_SIN_IMAGEN.
 3. Se carga la imagen 
    - Si el producto no tiene variaciones o la relación ya se registro pasa a ITEM_CONFIRMADO
    - Si el producto tiene variaciones cambia estatus a ITEM_SIN_RELACION
 4. Se carga relación Padre - Hijo y se cambia el estatus a ITEM_CONFIRMADO
 5. Si el producto esta detenido por alguna otra razón pasa estatus a ITEM_PENDIENTE.

### Precios
 Los precios ya incluyen impuestos, se espera que el MarketPlace lo publique como tal y
 que no cargue impuestos adicionales (Verifique que configuración tiene que tener el cliente para que este sea el caso).

 ### Descripciones
  Normalmente el MarketPlace solamente tiene un campo de nombre y uno de ficha técnica, si no hay 
  espacio especificado para [Descripción, Bullets]
  1. Se concatenarán (descripción y bullets) al final de la ficha técnica pasando un salto de línea 
  o su equivalente en HTML.
  2. Si el MarketPlacec acepta caracteres HTML, los bullets serán enmarcados en un lista no ordenada <ul> 
  3. Si no admiten carácteres especiales o HTML, se tendrá que limpiar de estos caracteres y agregar los saltos de línea correspondientes, antes de enviar
  la petición de guardar el producto.
  4. Invariablemente todos los MarketPlaces pueden tener un pie de ficha, que es el mismo para todos los
  productos Auxiliar getSettings() "key=descripcion"  y este será concatenado al final de la ficha con las mismas restricciones preestablecidas.
  5. Si por alguna razón el total de la ficha o nombre excede las dimensiones de los campos, serán truncadas a la última palabra completa del campo, que no rebase la restricción de longitud.
  6. Los Bullets se registran por variante del sku, al igual que las imágenes, pero normalmente los MPS, no cambian estas definiciones al seleccionar una variante, por lo que usualmente podrá seleccionar los bullates de la primer variante para publicar.

### Imágenes
  Los Marketplaces podrán generar identificadores independientes para las imágenes, hay que revisar esto para agregarlos al arreglo que inserta/actualiza las imágenes correspondientes.

### UPC/EAN
  Los MarketPlaces pueden tener restricciones que indiquen que se requiere un UPC/EAN válido para aprobar la publicación, cuando mande llamar el listado de productos a publicar debe de especificar el filtro $gtin=TRUE, para excluir los que no cumplan con el criterio.

### Mapeo de Atributos
  El MarketPlace contará con su propia lista de atributos, los atributos usualmente van a diferir en el nombre
  entre MPS, por lo que al registrar el atributo anexará al campo mapa, la lista de atributos "estándar" que reulten equivalentes ejemplo:
  1. El atribito de ejemplo es SIZE
  2. Sin embargo en los atributos estandar se le conoce como SIZE, FLAT_SIZE, WEAR_SIZE, etc.
  3. El mapeo será una cadena de la siguiente forma "SIZE,%_SIZE"
  4. Los posibles atributos serán separados por coma al llenar el valor de los atributos en su librería
La función Auxiliar regresará junto con el producto, todos los atributos estándar que tengan un valor y usted elaborará, la funcionalidad para identificarlos en base al mapeo y haciendo un ciclo entre los atributos de la categoría del MPS (revise la funcion auxiliar que regresa los atributos por categoría).
  5. Para el caso de lo mapeos que comiencen con '%' este se utilizará como comodín para la búsqueda de posibles valores.
  6. Para el caso de los atributos que comiencen con $, se utilizará para obtener una propiedad del registro no así un valor de atributo (Ej. $nombre).
  

 ```php
interface iMarketPlace {`

    ### DEBE DE DECLARAR UNA CONSTANTE CON EL MARKETID ASIGNADO EN SU LIBRERIA
    ### const MARKETID = 100;

    // Devolución de funciones no impementadas
    const NOT_IMPLEMENTED = -1; 

    // Estatus de producto    
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96; 
    const ITEM_SIN_PUBLICAR = -1; 
    const ITEM_DESHABILITADO = -2; 

    const ITEM_NOMBRE_CAMBIO = 2;  // Usualmente no solo el nombre cambia, si no algún valor del registro
    const ITEM_PRECIO_CAMBIO = 4; 
    const ITEM_STOCK_CAMBIO = 8; 
    const ITEM_IMAGEN_CAMBIO = 16; 
    const ITEM_VARIACION = 32; // PRODUCTO CON VARIACIONES NUEVAS ONO REGISTRADAS

    // Estatus de Requests
    const REQUEST_CANCEL = 0;
    const REQUEST_PENDIENTE = 2;
    const REQUEST_TERMINADO = 1;

    // Estatus de Tickets
    const TICKET_CERRADO = 2;
    const TICKET_ABIERTO = 1;
    const TICKET_ESCALADO = 2;

    // Estatus Pedidos
    const PEDIDO_CANCELADO = 0;
    const PEDIDO_ABIERTO = 1; // YA PAGADO Y LISTO PARA ENTREGAR
    const PEDIDO_ENVIADO = 2;
    const PEDIDO_ENTREGADO = 3;
    const PEDIDO_DEVUELTO = -1;
    const PEDIDO_REFUNDED = -2; // DINERO REINTEGRADO
    const PEDIDO_NOREPORTADO = 32;   // PEDIDOS QUE NO HAN SIFO REPORTADOS AL E-COMMERCE
    const PEDIDO_CAMBIO = 64;   // PEDIDOS CUYO ESTATUS FUE ACTUALIZADO
    const FEEDS_AGREGAR = 1;
    const FEEDS_ACTUALIZAR = 2;

```
### Función que verifica la viabilidad actual del API
 En el MarketPlace determinado
 Devuelve TRUE en caso de estar activo
 FALSE si no es así.
- `public  function getStatus();`

### Función de entrada, siempre será ejecutada
 El cliente será utilizado para obtener la  configuración de conexión y las llaves
 Esta llamada se realiza siempre antes de inciar la primer consulta
 En Auxiliar debe de llamar 
- setConfig($data);
- Puede llamar a getConfig() para evisar los parámetros a configurar
- Debe de llamar a la función getSettings, para determinar la configuración
almacenada en la base de datos.

- `public  function SignIt($cliente);`

### Rutina de refrezco del Token
 Algunas apis lo requieren, de no necesitarse devolver NOT_IMPLEMENTED,
 dependiendo el tiempo de refrezco se hara el mismo, ejemplo si el MPS solicita un vambio de Token cada 3.5 horas, debe de realizar el cambio faltando entre 15 y 20 minutos o cuando se encuentre ya expirado.
- `public  function refresh();`

### Devolverá la dirección específica de la llamada al módulo
 El servidor debio de ser cargado en SignIt desde la configuración
 En este punto se valida la caducidad del Token y se manda a llamar refresh 
 de ser necesario.
- `public  function getURL($modulo, $parameters=[]);`

### Devolverá un arreglo de categorías, directamente del MarketPlace,
 a partir del id especificado.
 id del marketplace, en caso de ser $id=FALSE, se devolverán todas
 Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
 a la librería Auxiliar 
- addCategoria($data)
- `public  function getCategorias($id=FALSE, $save=FALSE);`

### Obtendrá la lista de atributos correspondientes a la
 categoría del MarketPlace identificada, así como sus posibles 
 valores, válidos en caso de que existan, de ser $save TRUE,
 almacenará la informacion con un llamado a 
 - addAtributo($data). 
 - En el campo mapa debe de especificar la lista de atributos estandár que
pueden utilizarse para alimentar el valor del atributo.
- `public  function getAtributos($categoria, $save=FALSE);`

### Devolverá un arreglo de colores, directamente del MarketPlace,
 Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
 a la librería Auxiliar addColores($data), debe de especificar el color base 
 de cada color. # Puede procesar en lotes.
- Colores Base
   - AMARILLO, AZUL, BEIGE, BLANCO, CAFE, COBRE, FIUSHA, GRIS, MORADO,
   - MULTICOLOR, NARANJA, NEGRO, ORO, PLATA, ROJO, ROSA, VERDE
- `public  function getColores($save=FALSE);`

### Devolverá un arreglo de marcas disponibles directamente del MarketPlace,
 Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
 a la librería Auxiliar 
- addMarcas($data). # Puede procesar en lotes
- `public  function getMarcas($save=FALSE);`

### Obtiene el producto desde el MarketPlace, 
 si es necesario realizar, más de una llamada para obtener toda la información
 se realizará  y devolverá el objeto de acuerdo a la especificación en: 
 Clase Item
- `public  function getProducto($id);`

### Obtendrá un listado con los identificadores del producto en el MarketPlace
 Haciendo un recorrido de acuerdo a:
 limit : cantidad de registros
 offset: grupo de registros a saltar antes de comenzar a descargar
 page: algunos no tienen las funciones de limit y offset en su lugar usan un token
 activos: descargar solamente los activos 
- `public  function getProductos($limit = 100, $offset = 0, $page='', $activos=FALSE);`

### Recibirá un arreglo de productos a publicar
 Realizará las llamadas correspondientes a su publicación, ya sea uno por uno o
 En Lotes.
 Cada intento agregará un registro en la bitacora, ya sea positivo o no addBitacora($data);
 De ser confirmado el registro se harán las siguientes llamadas:
- addProducto($data); // Si el producto no fue confirmado, en estatus se podrá 99 = Sin confirmar 
- addStock($data); // Debe de ser llamado por cada variacion registrada

#### Los productos pueden tener diferentes tipos de variaciones SIZE, COLOR, PATTERN, UNICO o CUSTOM
 Si el MarketPlace no permite las variaciones de PATTERN, UNICO o CUSTOM
 Se deberá de crear una publicación (Producto) por cada hijo, siendo el sku del hijo el que se registre.
 y se deberán hacer sus llamadas correspondientes a addProducto y addStock

- Auxiliar getProductos($estatus=iMarketPlace::ITEM_SIN_PUBLICAR); 
Para obtener un listado de a lo más 100 productos sin publicar
- `public  function postProductos();`

### Llegará un listado de productos por actualizar y se hará las llamadas correspondientes
 de los datos del producto, descripción y atributos.
 getProductos($estatus=iMarketPlace::ITEM_NOMBRE_CAMBIO); 
 Para obtener un listado de productos ya publicados por actualizar
 Por cada actualización llamar en Auxiliar a:
- updProducto($data);
- addBitacora($data);
- `public  function putProductos();`

### Recibe un arreglo de productos por cerrar su publicación y hace las llamadas correspondientes
 Por cada actualización llamar en Auxiliar a:
- updProducto($data); // estatus ITEM_DESHABILITADO
- addBitacora($data);
- `public  function disableProductos($item);`

### Recibe un arreglo de productos por abrir su publicación r y hace las llamadas correspondientes
 Por cada actualización llamar en Auxiliar a:
- updProducto($data); // El estatus correspondiente
- addBitacora($data);
- `public  function enableProductos($item);`

### Recibe un arreglo de productos por eliminar y hace las llamadas correspondientes
 Por cada eliminación llamar en Auxiliar a:
- delProducto($data);
- addBitacora($data);
- `public  function deleteProductos($item);`
- Esta función eliminará la tabla secundaria de Stock del producto y de las imagenes registradas

### Solicita los productos por actualizar precio y realiza las llamadas correspondientes al MarketPlace
 getProductos($estatus=iMarketPlace::ITEM_PRECIO_CAMBIO); 
 Para obtener un listado de productos ya publicados con precios por actualizar.
 Por cada actualización llamar en Auxiliar a:
- updProducto($data); // Campos de precio cambiados y el id
- addBitacora($data);
- `public  function putPrecio();`

### Solicita los productos por actualizar Stock y realiza las llamadas correspondientes al MarketPlace
 getProductos($estatus=iMarketPlace::ITEM_STOCK_CAMBIO); 
  Para obtener un listado de productos ya publicados con precios por actualizar.
 Por cada actualización llamar en Auxiliar a:
- updStock($data);
- addBitacora($data);
- `public  function putStock();`

### Solicita los productos que tengan Variaciones nuevas y realiza las llamadas correspondientes al MarketPlace
 getProductos($estatus=iMarketPlace::ITEM_VARIACION); 
 Para obtener un listado de productos ya publicados con precios por actualizar.
 Por cada actualización llamar en Auxiliar a:
- addStock($data);
- addBitacora($data);
- `public  function postVariaciones();`

### Recibe el registro de la variacion a eliminar, así como el producto del marketplace.
 Llamará al MPS para eliminar dicha variación 
 Por cada eliminación llamar en Auxiliar a:
- delStock($data);
- addBitacora($data);
- `public  function deleteVariacion($variacion);`

### Solicita las guías de los pedidos correspondientes (la referencia es del MarketPlace)
 Por cada actualización llamar en Auxiliar a:
- addGuia($data);
- addBitacora($data);
- `public  function getGuias($pedidos);`

### Algunos MarketPlaces se les tiene que indicar la guía, ya que ellos no la proporcionan
 Esta función agregará la guía al marketplace en la referencia correspondiente
 Por cada actualización llamar en Auxiliar a:
- updGuia($id);
- addBitacora($data);
- `public  function postGuia($pedido, $guia, $paqueteria);`

### Se actualiza la guía en caso de que haya que rectificar algún dato.
 Por cada actualización llamar en Auxiliar a:
- updGuia($id);
- addBitacora($data);
- `public  function putGuia($pedido, $guia, $paqueteria);`

### Solicita un listado de los últimos pedidos actualizados
 la cantidad de pedidos esta definida por limit y el offset indica los pedidos a saltar.
 Si la referencia es válida, solamente traera el pedido que la referencia indica, independientemente 
 de su posicionamiento.
 De esta vacía (referencia), traera un grupo de pedidos, autorizados para entregar o
 de acuerdo a $estatus 
 Si el parámetro $save es TRUE correrá la función auxiliar addPedido($data).
- addBitacora($data); recuperar pedido
- addPedido($data);
 El detalle del pedido será identificado por medio del sku [getSkuId($sku)] y se almacenara 
 en la llamada enunciada arriba.
- `public  function getPedidos($referencia='', $save=FALSE, $limit=50, $offset=0, $estatus='');`


### Actualiza el estatus de una lista de pedidos en caso de cambio con respecto al pedido consultado.
 Llamada Auxiliar para obtener listado de pedidos getPedidos($limit=50)
 Automáticamente limitará la llamada a los últimos 30 días o el límite explicito, 
 el orden será la fecha de creación del pedido
 Por cada actualización llamar en Auxiliar a:    
- updPedido($pedido, $estatus, $total=0, $pedido_mkt=null); // estatus actualizado
- addBitacora($data);
- `public  function getEstatusPedido();`

### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
 generados en otros MarketPlaces
 getPedidos($limit); Devuelve los pedido que aun no han sido actualizados en el e-commerce.
 Por cada actualización llamar en Auxiliar a:
 updPedido($pedido, $estatus, $total=0, $pedido_mkt=null); // Se spera un id entero (bigint) en pedido_mkt
 addBitacora($data);
- `public  function postPedidos();`

### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
 generados en otros MarketPlaces
 getPedidosHis($limit); Devuelve los pedido que cambiaron de estatus y aun no 
 han sido actualizados en el e-commerce.
 En la liosta de pedidos no vendrán registros e-commerce
 Por cada actualización llamar en Auxiliar a:
- updPedido($pedido, $estatus, $total=0, $pedido_mkt=null);
 addBitacora($data);
- `public  function putPedidos();`

## Acción para autorizar el reintegro del pago al cliente
 El $id es el pedido a devolver 
 Por cada actualización llamar en Auxiliar a:    
- updPedido($pedido, $estatus, $total=0, $pedido_mkt=null); // El estatus actualizado, total autorizado a devolver
- addBitacora($data);
- `public  function postRefundPedido($referencia, $data=[]);`

### Devuelve un arreglo con la lista de Feeds enviadas al MarketPlace,
 para realizar operaciones en el mismo.
 si el parametro $save=TRUE, agregará los feeds a través de la función
 Auxiliar addFeed($data);
 Si el $filtro es 0 arrojara una lista con los feeds de los últimos $dias.
 Si el $filtro no es cero, llamará a la funcion auxiliar getFeeds() para actualizar la respuesta, al recibir la respuesta si esta contiene errores debe de grabar un registro en addBitacora por cada sku recibido en el archivo; en un formato legible en texto plano no estructurado (no json, no xml, etc.); así como actualizar la respuesta con la función updFeed($id, $answer).
- `public  function getFeeds($filtro=0, $dias=15, $save=FALSE);`

### Algunos MarketPlaces, reciben las imágenes en operaciones diferentes a la alta de productos
 Para esto el estatus de Alta en el Producto, 
 Auxiliar  getProductos($estatus, $data); // Regresa un listado de productos con estatus ITEM_SIN_IMAGEN;
 updProducto($data) // par actualizar estatus nuevo
 addImagen($data), para agregar las imágenes cargadas.
- `public  function postImagenes();`

### La actualización de los imágenes en Los MarketPlaces
Se obtiene los productos con imagenes por atualizar a traves de la funcion getProductos
(iMarketPlace::ITEM_IMAGEN_CAMBIO), El registro se hará a través de esta función  Auxiliar updImagen($data);
- `public  function putImagenes();`

### La recuperación de documentos del marketplace de los pedidos
 El documento podría ser factura, nota de crédito, etc.
 el documento se almacenara en base64 y se llamará a la función auxiliar
 addGuia($data);
 addBitacora($data);
- `public  function getDocumento($documento, $referencia);`

### Se recibe un arreglo de productos que han sido declarados como fulfillment by Market
 Si el parámetro $save = TRUE
 El producto se declara como fulfillment con llamada auxiliar addFulfillment($product_id)
- `public  function getFulFillment($save=FALSE);`

### Obtiene un listado de Tickets/Preguntas que informa el MarketPkace.
 Si el $id es diferente a "", devuelve el registro solicitado
 Si el parámetro $save es TRUE hará una llamada auxiliar a saveTicket($data) y addBitacora($data)
 El tipo indica si se solicita Ticket/Pregunta
- `public  function getTickets($id="", $save=FALSE, $tipo='Ticket', $limit=50, $offset=0);`

### Obtiene un listaado de Anuncios/Notificaciones/Actualizaciones/Otros que informa 
 el MarketPkace.
 Si el $id es diferente a "", devuelve el registro solicitado
 Si el parámetro $save es TRUE hará una llamada auxiliar a saveNotifiction($data)
 El tipo indica si se solicita Anuncio/Notificacion/Actualiacion/Otros
- `public  function getNotifications($id="", $save=FALSE, $tipo='Anuncio', $limit=50, $offset=0);`

### Envía una respuesta a una pregunta/ticket existente
 $respuesta se enviara conjuntamente
 En auxiliar llamara updateTicket($data) y addBitacora($data)
- `public  function postTicket($id, $respuesta);`

### Acción para cerrar un ticket abierto y marcarlo como resuelto
 En auxiliar llamara updateTicket($data) estatus TICKET_CERRADO y addBitacora($data)
- `public  function closeTicket($id, $data=[]);`

### Acción para abrir un ticket cerrado y marcarlo como pendiente
 En auxiliar llamara updateTicket($data) estatus TICKET_ABIERTO y addBitacora($data)
- `public  function openTicket($id);`

### Acción para escalar un ticket 
 En auxiliar llamara updateTicket($data) estatus TICKET_ESCALADO y addBitacora($data)
- `public  function escalateTicket($id, $data=[]);`


### **************************************************************************
### El resto de las llamadas, tienen poca probabilidad de existir en el market,
### ya que son de naturaleza propia de ciertos MPS.
### **************************************************************************


### Llamada a solicitar un reporte al Marketplace
 Normalmente regresa un id de la petición
 La petición hará una llamada auxiliar a 
 addFeed($data); con estatus REQUEST_PENDIENTE y campo request con la solicitud enviada
- `public  function requestReport($tipo, $data);`

## Petición para cancelar la solicitud del reporte
 La petición hará una llamada auxiliar a 
 updateFeed($data); // estatus = REQUEST_CANCEL
- `public  function cancelRequest($id, $data=[]);`

### Petición para revisar la solicitud del reporte
 La petición hará una llamada auxiliar a 
 updateFeed($data); // estatus = REQUEST_PENDIENTE O REQUEST_TERMINADO
- `public  function getRequestStatus($tipo, $id, $data=[]);`

### Petición para recuperar el reporte terminado
 La petición hará una llamada auxiliar a 
 updateFeed($data); // estatus = REQUEST_TERMINADO y el reporte almacenado en campo answer.
- `public  function getReport($id, $data=[]);`

### Petición para recuperar la tienda predeterminada 
 Devuelve un arreglo con la información
- `public  function getStore();`

### Petición para recuperar la locación predeterminada 
 Devuelve un arreglo con la información
- `public  function getLocation();`

### Petición para recuperar el listado de almacenes
 Devuelve un arreglo con la información
- `public  function getWarehouses();`

### Petición para recuperar el listado de paqueterías
 Devuelve un arreglo con la información
- `public  function getCarriers($data=[]);`

### Petición para recuperar el mecanismo de envío por producto
 Devuelve un arreglo con la información
- `public  function getItemShipping($item, $data=[]);`

### Algunos MarketPlaces no utilizan los mecanismos de $limit y $offset
 para delimitar la petición, por lo que realizan un proceso de paginación
 en tokens, en donde el mismo es enviado nuevamente para recuperar la 
 información subsecuente
 Devuelve un arreglo con la información
- `public  function getNextPageLink($data = []);`

### Llamada de Marketplace para actualiza el precio de envío
 Consulta getPrecioEnvio($limit), para una lista de productos con variacion en el costo de envío
- Auxiliar updatePrecioEnvio($sku, $imagenes);
- `public  function postShippingPrices($data);`

### Petición para recuperar los mecanismos de envñio por categoría
 Devuelve arreglo json con la información
- `public  function getShippingSettings($categoria='');`

### Devuelve un arreglo con la lista de campañas a las que ha sido invitado
 si el parametro $save=TRUE, agregara los deals a través de la función
 Auxiliar addDeal($data);
- `public  function getDeals($id=0, $save=FALSE, $limit=50, $offset=0);`

### Recibe un Deal y agrega productos al mismo
 Por cada actualización llamar en Auxiliar a:
 addDealItem($id, $oferta); 
 addBitacora($data);
- `public  function postDeal($id, $deals);`

### Recibe un Deal y actualiza productos al mismo
 Por cada actualización llamar en Auxiliar a:
 updateDealItem($id, $oferta); 
 addBitacora($data);
- `public  function putDeal($id, $items);`