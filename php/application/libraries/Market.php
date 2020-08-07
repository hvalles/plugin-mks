<?php

##∫ Librería de acoplamiento de Marketplaces a MarketSync
// Todas las librerías deberán de cumplir con la interface en lo posible.
// Las librerías hacen referencia a una librería auxiliar para movimientos 
// hacia la base de datos revise la especificación correspondiente en:

## Generalidades
// Todas las llamadas a menos de que se especifique otra cosa serán hacia 
// el MarketPlace en cuestión. 

### Todas las llamadas POST,PUT,DELETE deberan de generar un registro en la
// bitacora y reportar el estatus de la misma addBitacora($data);
// La llamada GET a recuperar pedidos también genera un registro.
// En caso de error este será almacenado como parte de la bitácora.

### Los MarketPlaces tienen limitantes en cantidad de llamadas por periodo de tiempo
// Recibirá un grupo de productos por Actualizar/Registrar, 
// siendo respondabilidad  de la librería solamente procesar los elementos dentro de 
// los limites de la restricción correspondiente. Haga las pausas correspondientes para evitar infracciones 
// y no exceda la ejecuón en más de 5 minutos por proceso. (Estime el tiempo por registro y calcule un escenario
// pesimista para la cantidad de registros que pueda procesar)

### Flujo de productos por publicar en Lotes
// 1. Se agrega el producto con estatus ITEM_SIN_CONFIRMAR
// 2. Se confirma el alta del producto cambia status a ITEM_SIN_IMAGEN.
// 3. Se carga la imagen 
// 3.1 Si el producto no tiene variaciones o la relación ya se registro pasa a ITEM_CONFIRMADO
// 3.2 Si el producto tiene variaciones cambia estatus a ITEM_SIN_RELACION
// 4. Se carga relación Padre - Hijo y se cambia el estatus a ITEM_CONFIRMADO
// 5. Si el producto esta detenido por alguna otra razón pasa estatus a ITEM_PENDIENTE.

interface iMarketPlace {

    ### DEBE DE DECLARAR UNA CONSTANTE CON EL MARKETID ASIGNADO EN SU LIBRERIA
    ### const MARKETID = 100;

    // Estatus de producto
    const NOT_IMPLEMENTED = -1; 
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 99; 

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
    const PEDIDO_ABIERTO = 1; // YA PAPGADO Y LISTO PARA ENTREGAR
    const PEDIDO_ENVIADO = 2;
    const PEDIDO_ENTREGADO = 3;
    const PEDIDO_DEVUELTO = -1;
    const PEDIDO_REFUNDED = -2; // DINERO REINTEGRADO

    ### Función que verifica la viabilidad actual del API
    // En el MarketPlace determinado
    // Devuelve TRUE en caso de estar activo
    // FALSE si no es así, ade
    public function getStatus();

    ### Función de entrada, siempre será ejecutada
    // El cliente serà utilizado para obtener la
    // configuración de conexión y las llaves
    // Esta llamada se realiza siempre antes de inciar la primer consulta
    // En Auxiliar debe de llamar setConfig($cliente, $market_id, $debug=FALSE);
    // El parametro $debug debe ser TRUE para sus pruebas.
    public function SignIt($cliente);

    ### Rutina de refrezco del Token
    // Algunas apis lo requieren, de no necesitarse devolver NOT_IMPLEMENTED 
    public function refresh();

    ### Devolverá la dirección especifica de la llamada al módulo
    // El servidor debio de ser cargado en SignIt desde la configuración
    // En este punto se valida la caducidad del Token y se manda a llamar refresh 
    // de ser necesario.
    public function getURL($modulo, $parameters=[]);

    ### Devolverá un arreglo de categorías, directamente del MarketPlace,
    // a partir del id especificado.
    // id del marketplacem en caso de ser $id=FALSE, se devolverán todas
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addCategoria($data)
    public function getCategorias($id=FALSE, $save=FALSE);

    ### Obtendrá la lista de atributos correspondientes a la
    // categoría dek MarketPlace inficada, así como sus posibles 
    // valores, válidos en caso de que existan, de ser $save TRUE,
    // almacenara la informacion con un llamado a addAtributo($data).
    public function getAtributos($categoria, $save=FALSE);

    ### Devolverá un arreglo de colores, directamente del MarketPlace,
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addColor($data), debe de especificar el color base 
    // de cada color.
    // Colores Base
    // AMARILLO, AZUL, BEIGE, BLANCO, CAFE, COBRE, FIUSHA, GRIS, MORADO,
    // MULTICOLOR, NARANJA, NEGRO, ORO, PLATA, ROJO, ROSA, VERDE
    public function getColores($save=FALSE);

    ### Devolverá un arreglo de marcas disponibles directamente del MarketPlace,
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addMarca($data).
    public function getMarcas($save=FALSE);

    ### Obtiene el producto desde el MarketPlace, si es necesario realizar,
    // más de una llamada para obtener toda la información se realizará
    // u devolverá el objeto de acuerdo a la especificación en: 
    // TODO:Producto
    public function getProducto($id);

    ### Obtendrá un listado con los identificadores del producto en el MarketPlace
    // Haciendo un recorrido de acuerdo a:
    // limit : cantidad de registros
    // offset: grupo de registros a saltar antes de comenzar a descaregar
    // page: algunos no tienen las funciones de limit y offset en su lugar usan un token
    // activos: descargar solamente los activos 
    public function getProductos($limit = 100, $offset = 0, $page='', $activos=FALSE);

    ### Recibirá un arreglo de productos a publicar
    // Realizará las llamadas correspondientes a su publicación, ya sea uno por uno o
    // En Lotes.
    // Cada intento agregará un registro el la bitacora, ya sea positivo o no addBitacora($data);
    // De ser confirmado el registro se harán las siguientes llamadas:
    // addProducto($data); // Si el producto no fue confirmado, en estatus se podrá 99 = Sin confirmar 
    // addStock($data); // Debe de ser llamado por cada variacion registrada

    ### Los productos pueden tener varios tipo de variaciones SIZE, COLOR, PATTERN, UNICO o CUSTOM
    // Si el MarketPlace no permite las variaciones de PATTERN, UNICO o CUSTOM
    // Se deberá de crear un Producto por cada hijo, siendo el sku del hijo el que se registre.
    // y se deberán hacer sus llamadas correspondientes a addProducto y addStock
  
    // Auxiliar publicarItems($limit=100); Para obtener un listado de productos sin publicar
    public function postProductos();

    ### Llegará un listado de productos por actualizar y se hará las llamadas correspondientes
    // de los datos del producto, descripción y atributos.
    // actualizarItems($limit=100); Para obtener un listado de productos ya publicados por actualizar
    // Por cada actualización llamar en Auxiliar a:
    // updateProducto($data);
    // addBitacora($data);
    public function putProductos();

    ### Recibe un arreglo de productos por cerrar su publicación r y hace las llamadas correspondientes
    // Por cada actualización llamar en Auxiliar a:
    // disbleProducto($data);
    // addBitacora($data);
    public function disableProductos($item);

    ### Recibe un arreglo de productos por abrir su publicación r y hace las llamadas correspondientes
    // Por cada actualización llamar en Auxiliar a:
    // enableProducto($data);
    // addBitacora($data);
    public function enableProductos($item);

    ### Recibe un arreglo de productos por eliminar y hace las llamadas correspondientes
    // Por cada eliminación llamar en Auxiliar a:
    // removeProducto($data);
    // addBitacora($data);
    public function deleteProductos($item);

    ### Solicita los productos por actualizar precio y realiza las llamadas correspondientes al MarketPlace
    // actualizarPrecios($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // updatePrecio($data);
    // addBitacora($data);
    public function putPrecio();

    ### Solicita los productos por actualizar Stock y realiza las llamadas correspondientes al MarketPlace
    // actualizarPrecios($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // updateStock($data);
    // addBitacora($data);
    public function putStock();

    ### Solicita los productos que tengan Variaciones nuevas y realiza las llamadas correspondientes al MarketPlace
    // actualizarVariaciones($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // addStock($data);
    // addBitacora($data);
    public function postVariaciones();

    ### Recibe el registro de la variacion a eliminar, asì como el producto del marketplace.
    // Llamará al MPL para eliminar dicha variación 
    // Por cada eliminación llamar en Auxiliar a:
    // delStock($data);
    // addBitacora($data);
    public function deleteVariacion($variacion);

    ### Solicita las guias de los pedidos correspondientes (la referencia es del MarketPlace)
    // Por cada actualización llamar en Auxiliar a:
    // addGuia($data);
    // addBitacora($data);
    public function getGuias($pedidos);

    ### Algunos MarketPlaces se les tiene que indicar la guía, ya que ellos no la proporcionan
    // Esta función agregarña la guía al marketplace en a referencia correspondiente
    // Por cada actualización llamar en Auxiliar a:
    // uploadGuia($data);
    // addBitacora($data);
    public function postGuia($pedido, $data=[]);

    ### Se actualiza la guía en caso de que haya que rectificar algún dato.
    // Por cada actualización llamar en Auxiliar a:
    // uploadGuia($data);
    // addBitacora($data);
    public function putGuia($pedido, $data=[]);

    ### Solicita un listado de los últimos pedidos actualizados
    // la cantidad de pedidos esta definida por limit y el offset indica los pedidos a saltar.
    // Si la referencia es válida, solamente traera el pedido que la referencia indica, independientemente 
    // de su posicionamiento.
    // De esta vacía (referencia), traera un grupo de pedidos, autorizados para entregar o
    // de acuerdo a $estatus 
    // Si el parámetro $save es TRUE correrá la función auxiliar addPedido($data).
    // addBitacora($data); recuperar pedido
    // addPedido($data);
    // El detalle del pedido será identificado por medio del sku [getSkuId($sku)] y se almacenara 
    // en la llamada enunciada arriba.
    public function getPedidos($referencia='', $save=FALSE, $limit=50, $offset=0, $estatus='');


    ### Actualiza el estatus de una lista de pedidos en caso de cambio con respecto al pedido consultado.
    // Llamada Auxiliar par obtener listado de pedidos getCurrentPedidos($limit=50)
    // Automáticamente limitara la llamada a los últimos 30 días o el limite explicito, 
    // el orden será la fecha de creación del pedido
    // Por cada actualización llamar en Auxiliar a:    
    // updateHistorial($data); // El estatus actualizado
    // addBitacora($data);
    public function getEstatusPedido();

    ### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
    // generados en otros MarketPlaces
    // getPedidos($limit); Devuelve los pedido que aun no han sido actualizados en el e-commerce.
    // Por cada actualización llamar en Auxiliar a:
    // updatePedido($pedido, $id); // Se spera un id entero (bigint)
    // addBitacora($data);
    public function postPedidos();

    ### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
    // generados en otros MarketPlaces
    // getPedidosHis($limit); Devuelve los pedido que cambiaron de estatus y aun no 
    // han sido actualizados en el e-commerce.
    // En la liosta de pedidos no vendrán registros e-commerce
    // Por cada actualización llamar en Auxiliar a:
    // updateHistorial($data); 
    // addBitacora($data);
    public function putPedidos();

    ### Devuelve un arreglo con la lista de campañas a las que ha sido invitado
    // si el parametro $save=TRUE, agregara los deals a través de la función
    // Auxiliar addDeal($data);
    public function getDeals($id=0, $save=FALSE, $limit=50, $offset=0);

    ### Recibe un Deal y agrega productos al mismo
    // Por cada actualización llamar en Auxiliar a:
    // addDealItem($id, $oferta); 
    // addBitacora($data);
    public function postDeal($id, $deals);

    ### Recibe un Deal y actualiza productos al mismo
    // Por cada actualización llamar en Auxiliar a:
    // updateDealItem($id, $oferta); 
    // addBitacora($data);
    public function putDeal($id, $items);

    ### Devuelve un arreglo con la lista de Feeds enviadas al MarketPlace,
    // para realizar operaciones en el mismo.
    // si el parametro $save=TRUE, agregará los feeds a través de la función
    // Auxiliar addFeed($data);
    // Si el $id es 0 arrojara una lista con los feeds de los últimos $dias.
    public function getFeeds($id=0, $dias=15, $save=FALSE);

    ### Algunos MarketPlaces, reciben las imágenes en operaciones diferentes a la alta de productos
    // Para esto el estatus de Alta en el Producto, 
    // Auxiliar getItemByEstatus($estatus, $data); // Regresa un listado de productos con estatus ITEM_SIN_IMAGEN
    public function postImagenes();

    ### La actualización de los imágenes en Los MarketPlaces
    // Se hará a través de esta función
    // Auxiliar updateImagen($sku, $imagenes);
    public function putImagenes();

    ### La recuperación de documentos del marketplace de los pedidos
    // El documento podría set factura, nota de crédito, etc.
    // el documento se almacenara en base64 y se llamará a la función auxuliar
    // addGuia($data);
    // addBitacora($data);
    public function getDocumento($documento, $referencia);

    ### Se recibe un arreglo de productos que han sido declarados como fulfillment by Market
    // Si el parámetro $save = TRUE
    // El producto se declara como fulfillment
    // addFulfillment($product_id)
    public function getFulFillment($save=FALSE);

    ### Llamada a solicitar un reporte al Marketplace
    // Normalmente regresa un id de la petición
    // La petición hará una llamada auxiliar a 
    // addFeed($data); con estatus REQUEST_PENDIENTE y campo request con la solicitud enviada
    public function requestReport($tipo, $data);

    ## Petición para cancelar la solicitud del reporte
    // La petición hará una llamada auxiliar a 
    // updateFeed($data); // estatus = REQUEST_CANCEL
    public function cancelRequest($id, $data=[]);

    ### Petición para revisar la solicitud del reporte
    // La petición hará una llamada auxiliar a 
    // updateFeed($data); // estatus = REQUEST_PENDIENTE O REQUEST_TERMINADO
    public function getRequestStatus($tipo, $id, $data=[]);

    ### Petición para recuperar el reporte terminado
    // La petición hará una llamada auxiliar a 
    // updateFeed($data); // estatus = REQUEST_TERMINADO y el reporte almacenado en campo answer.
    public function getReport($id, $data=[]);

    ### Petición para recuperar la tienda predeterminada 
    // Devuelve un arreglo con la información
    public function getStore();

    ### Petición para recuperar la locación predeterminada 
    // Devuelve un arreglo con la información
    public function getLocation();

    ### Petición para recuperar el listado de almacenes
    // Devuelve un arreglo con la información
    public function getWarehouses();

    ### Petición para recuperar el listado de paqueterías
    // Devuelve un arreglo con la información
    public function getCarriers($data=[]);

    ### Petición para recuperar el mecanismo de envío por producto
    // Devuelve un arreglo con la información
    public function getItemShipping($item, $data=[]);

    ### Algunos MarketPlaces no utilizan los mecanismos de $limit y $offset
    // para delimitar la petición, por lo que realizan un proceso de paginación
    // en tokens, en donde el mismo es enviado nuevamente para recuperar la 
    // información subsecuente
    // Devuelve un arreglo con la información
    public function getNextPageLink($data = []);

    ### Llamada de Marketplace para actualiza el precio de envío
    // Consulta getPrecioEnvio($limit), para una lista de productos con variacion en el costo de envío
    // Auxiliar updatePrecioEnvio($sku, $imagenes);
    public function postShippingPrices($data);

    ### Petición para recuperar los mecanismos de envñio por categoría
    // Devuelve arreglo json con la información
    public function getShippingSettings($categoria='');

    ### Obtiene un listado de Tickets/Preguntas que informa el MarketPkace.
    // Si el $id es diferente a "", devuelve el registro solicitado
    // Si el parámetro $save es TRUE hará una llamada auxiliar a saveTicket($data) y addBitacora($data)
    // El tipo indica si se solicita Ticket/Pregunta
    public function getTickets($id="", $save=FALSE, $tipo='Ticket', $limit=50, $offset=0);

    ### Obtiene un listaado de Anuncios/Notificaciones/Actualizaciones/Otros que informa 
    // el MarketPkace.
    // Si el $id es diferente a "", devuelve el registro solicitado
    // Si el parámetro $save es TRUE hará una llamada auxiliar a saveNotifiction($data)
    // El tipo indica si se solicita Anuncio/Notificacion/Actualiacion/Otros
    public function getNotifications($id="", $save=FALSE, $tipo='Anuncio', $limit=50, $offset=0);

    ### Envía una respuesta a una pregunta/ticket existente
    // $respuesta se enviara conjuntamente
    // En auxiliar llamara updateTicket($data) y addBitacora($data)
    public function postTicket($id, $respuesta);

    ### Acción para cerrar un ticket abierto y marcarlo como resuelto
    // En auxiliar llamara updateTicket($data) estatus TICKET_CERRADO y addBitacora($data)
    public function closeTicket($id, $data=[]);

    ### Acción para abrir un ticket cerrado y marcarlo como pendiente
    // En auxiliar llamara updateTicket($data) estatus TICKET_ABIERTO y addBitacora($data)
    public function openTicket($id);

    ### Acción para escalar un ticket 
    // En auxiliar llamara updateTicket($data) estatus TICKET_ESCALADO y addBitacora($data)
    public function escalateTicket($id, $data=[]);

    ## Acción para autorizar el reintegro del pago al cliente
    // La $referencia es el pedido a devolver 
    // Por cada actualización llamar en Auxiliar a:    
    // updateHistorial($data); // El estatus actualizado
    // addBitacora($data);
    public function postRefundPedido($referencia, $data=[]);

}

?>