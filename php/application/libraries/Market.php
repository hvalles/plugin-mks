<?php

## Librería de acoplamiento de Marketplaces a MarketSync
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


    // Devolución de funciones no impementadas
    const NOT_IMPLEMENTED = -1001; 

    // Estatus de producto    
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96; 
    const ITEM_SIN_PUBLICAR = -1; 
    const ITEM_DESHABILITADO = -2; 

    const ITEM_NOMBRE_CAMBIO = 2; 
    const ITEM_PRECIO_CAMBIO = 4; 
    const ITEM_STOCK_CAMBIO = 8; 
    const ITEM_IMAGEN_CAMBIO = 16; 
    const ITEM_VARIACION = 32; 

    // Claves de Eventos
    const EVENTO_AGREGAR = 1;
    const EVENTO_ACTUALIZAR = 2;
    const EVENTO_PRECIO_STOCK = 3;
    const EVENTO_ELIMINAR = 4;
    const EVENTO_ERROR = 5;
    const EVENTO_REBUILD = 5;
    const EVENTO_CONSULTAR = 7;

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
    const PEDIDO_NOREPORTADO = 32;   // PEDIDOS QUE NO HAN SIDO REPORTADOS AL E-COMMERCE
    const PEDIDO_CAMBIO = 64;   // PEDIDOS CUYO ESTATUS FUE ACTUALIZADO
    const FEEDS_AGREGAR = 1;
    const FEEDS_ACTUALIZAR = 2;

    // Colores MarketSync
    const COLOR_GRIS = "GRIS";
    const COLOR_AMARILLO = "AMARILLO";
    const COLOR_AZUL = "AZUL";
    const COLOR_BEIGE = "BEIGE";
    const COLOR_BLANCO = "BLANCO";
    const COLOR_CAFE = "CAFE";
    const COLOR_COBRE = "COBRE";
    const COLOR_FIUSHA = "FIUSHA";
    const COLOR_MORADO = "MORADO";
    const COLOR_MULTICOLOR = "MULTICOLOR";
    const COLOR_NARANJA = "NARANJA";
    const COLOR_NEGRO = "NEGRO";
    const COLOR_ORO = "ORO";
    const COLOR_PLATA = "PLATA";
    const COLOR_ROJO = "ROJO";
    const COLOR_ROSA = "ROSA";
    const COLOR_VERDE = "VERDE";

    const MINIMO_MXP = 10;
    const MINIMO_USD = 1;

    const SERVIDOR = 'http://sandbox.marketsync.mx/mks/';
    

    # Regresa el ID que se les proporciono como indicador del marketplace 
    public function getMarketId();

    ### Función que verifica la viabilidad actual del API
    public function getStatus();

    ### Función de entrada, siempre será ejecutada, data sera un objeto con la configuración almacanada
    ### por el plugin, en $data debe de incluir sus llaves publicas y privadas para que se realice 
    ### la conexion adecuadamente
    public function SignIt($cliente, $data = null);

    ### Rutina de refrezco del Token
    public function refresh();

    ### Devolverá la dirección especifica de la llamada al módulo
    public function getURL($modulo, $parameters=[]);

    ### Devolverá un arreglo de categorías, directamente del MarketPlace,
    public function getCategorias($id=FALSE, $save=FALSE);

    ### Obtendrá la lista de atributos correspondientes a la
    public function getAtributos($categoria, $save=FALSE);

    ### Devolverá un arreglo de colores, directamente del MarketPlace,
    public function getColores($save=FALSE);

    ### Devolverá un arreglo de marcas disponibles directamente del MarketPlace,
    public function getMarcas($save=FALSE);

    ### Obtiene el producto desde el MarketPlace, si es necesario realizar,
    public function getProducto($id);

    ### Obtendrá un listado con los identificadores del producto en el MarketPlace
    public function getProductos($limit = 100, $offset = 0, $page='', $activos=FALSE);

    ### Recibirá un arreglo de productos a publicar
    ### Los productos pueden tener varios tipo de variaciones SIZE, COLOR, PATTERN, UNICO o CUSTOM
    public function postProductos($item=FALSE);

    ### Llegará un listado de productos por actualizar y se hará las llamadas correspondientes
    public function putProductos($item=FALSE);

    ### Recibe un arreglo de productos por cerrar su publicación r y hace las llamadas correspondientes
    public function disableProductos($item);

    ### Recibe un arreglo de productos por abrir su publicación r y hace las llamadas correspondientes
    public function enableProductos($item);

    ### Recibe un arreglo de productos por eliminar y hace las llamadas correspondientes
    public function deleteProductos($item);

    ### Solicita los productos por actualizar precio y realiza las llamadas correspondientes al MarketPlace
    public function putPrecio($item=FALSE);

    ### Solicita los productos por actualizar Stock y realiza las llamadas correspondientes al MarketPlace
    public function putStock($item=FALSE);

    ### Solicita los productos que tengan Variaciones nuevas y realiza las llamadas correspondientes al MarketPlace
    public function postVariaciones($item=FALSE);

    ### Recibe el registro de la variacion a eliminar, asì como el producto del marketplace.
    public function deleteVariacion($variacion);

    ### Solicita las guias de los pedidos correspondientes (la referencia es del MarketPlace)
    public function getGuias($pedidos);

    ### Algunos MarketPlaces se les tiene que indicar la guía, ya que ellos no la proporcionan
    public  function postGuia($pedido, $guia, $paqueteria, $url='');

    ### Se actualiza la guía en caso de que haya que rectificar algún dato.
    public  function putGuia($pedido, $guia, $paqueteria, $url='');

    ### Solicita un listado de los últimos pedidos actualizados
    public function getPedidos($referencia='', $save=FALSE, $limit=50, $offset=0, $estatus='');

    ### Actualiza el estatus de una lista de pedidos en caso de cambio con respecto al pedido consultado.
    public function getEstatusPedido($id=FALSE);

    ### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
    public function postPedidos($id=FALSE);

    ### Los MarketPlaces que son e-commerce, requieren que se carguen los pedidos
    public function putPedidos($id=FALSE);

    ## Acción para autorizar el reintegro del pago al cliente
    public function postRefundPedido($referencia, $data=[]);

    ### Devuelve un arreglo con la lista de Feeds enviadas al MarketPlace,
    public function getFeeds($filtro=0, $dias=15, $save=FALSE);

    ### Algunos MarketPlaces, reciben las imágenes en operaciones diferentes a la alta de productos
    public function postImagenes($item=FALSE);

    ### La actualización de los imágenes en Los MarketPlaces
    public function putImagenes($item=FALSE);

    ### La recuperación de documentos del marketplace de los pedidos
    public function getDocumento($documento, $referencia);

    ### Se recibe un arreglo de productos que han sido declarados como fulfillment by Market
    public function getFulFillment($save=FALSE);

    ### Obtiene un listado de Tickets/Preguntas que informa el MarketPkace.
    public function getTickets($id="", $save=FALSE, $tipo='Ticket', $limit=50, $offset=0);

    ### Obtiene un listaado de Anuncios/Notificaciones/Actualizaciones/Otros que informa 
    public function getNotifications($id="", $save=FALSE, $tipo='Anuncio', $limit=50, $offset=0);

    ### Envía una respuesta a una pregunta/ticket existente
    public function postTicket($id, $respuesta);

    ### Acción para cerrar un ticket abierto y marcarlo como resuelto
    public function closeTicket($id, $data=[]);

    ### Acción para abrir un ticket cerrado y marcarlo como pendiente
    public function openTicket($id);

    ### Acción para escalar un ticket 
    public function escalateTicket($id, $data=[]);

    ### Acción para conciliar MarketPlce
    public function conciliar($item = FALSE);

    ### **************************************************************************
    ### El resto de las llamadas, tienen poca probabilidad de existir en el market,
    ### ya que son de naturaleza propia de ciertos MPS.
    ### **************************************************************************

    ### Llamada a solicitar un reporte al Marketplace
    public function requestReport($tipo, $data);

    ## Petición para cancelar la solicitud del reporte
    public function cancelRequest($id, $data=[]);

    ### Petición para revisar la solicitud del reporte
    public function getRequestStatus($tipo, $id, $data=[]);

    ### Petición para recuperar el reporte terminado
    public function getReport($id, $data=[]);

    ### Petición para recuperar la tienda predeterminada 
    public function getStore();

    ### Petición para recuperar la locación predeterminada 
    public function getLocation();

    ### Petición para recuperar el listado de almacenes
    public function getWarehouses();

    ### Petición para recuperar el listado de paqueterías
    public function getCarriers($data=[]);

    ### Petición para recuperar el mecanismo de envío por producto
    public function getItemShipping($item, $data=[]);

    ### Algunos MarketPlaces no utilizan los mecanismos de $limit y $offset
    public function getNextPageLink($data = []);

    ### Petición para recuperar los mecanismos de envñio por categoría
    public function getShippingSettings($categoria='');

    ### Devuelve un arreglo con la lista de campañas a las que ha sido invitado
    public function getDeals($id=0, $save=FALSE, $limit=50, $offset=0);

    ### Recibe un Deal y agrega productos al mismo
    public function postDeal($id, $deals);

    ### Recibe un Deal y actualiza productos al mismo
    public function putDeal($id, $items);

}

?>