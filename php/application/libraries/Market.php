<?php

// Librería de acoplamiento de Marketplaces a MarketSync
// Todas las librerías deberán de cumplir con la interface en lo posible.
// Las librerías hacen referencia a una librería auxiliar para movimientos 
// hacia la base de datos revise la especificación correspondiente en:

// Generalidades
// Todas las llamadas a menos de que se especifique otra cosa serán hacia 
// el MarketPlace en cuestión. 

// Todas las llamadas POST,PUT,DELETE deberan de generar un registro en la
// bitacora y reportar el estatus de la misma addBitacora($data);
// La llamada GET a recuperar pedidos también genera un registro.
// En caso de error este será almacenado como parte de la bitácora.

// Los MarketPlaces tienen limitantes en cantidad de llamadas por periodo de tiempo
// Recibirá un grupo de productos por Actualizar/Registrar, 
// siendo respondabilidad  de la librería solamente procesar los elementos dentro de 
// los limites de la restricción correspondiente. Haga las pausas correspondientes para evitar infracciones 
// y no exceda la ejecuón en más de 5 minutos por proceso. (Estime el tiempo por registro y calcule un escenario
// pesimista para la cantidad de registros que pueda procesar)

interface iMarketPlace {

    // DEBE DE DECLARAR UNA CONSTANTE CON EL MARKETID ASIGNADO
    // const MARKETID = 100;
    const NOT_IMPLEMENTED = -1; 

    // Función de entrada, siempre será ejecutada
    // El cliente serà utilizado para obtener la
    // configuración de conexión y las llaves
    public function SignIt($cliente);

    // Rutina de refrezco del Token
    // Algunas apis lo requieren, de no necesitarse devolver NOT_IMPLEMENTED 
    public function refresh();

    // Devolverá la dirección especifica de la llamada al módulo
    // El servidor debio de ser cargado en SignIt desde la configuración
    // En este punto se valida la caducidad del Token y se manda a llamar refresh 
    // de ser necesario.
    public function getURL($modulo);

    // Devolverá un arreglo de categorías, directamente del MarketPlace,
    // a partir del id especificado.
    // id del marketplacem en caso de ser $id=FALSE, se devolverán todas
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addCategoria($data)
    public function getCategorias($id=FALSE, $save=FALSE);

    // Obtendrá la lista de atributos correspondientes a la
    // categoría dek MarketPlace inficada, así como sus posibles 
    // valores, válidos en caso de que existan, de ser $save TRUE,
    // almacenara la informacion con un llamado a addAtributo($data).
    public function getAtributos($categoria, $save=FALSE);

    // Devolverá un arreglo de colores, directamente del MarketPlace,
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addColor($data), debe de especificar el color base 
    // de cada color.
    // Colores Base
    // AMARILLO, AZUL, BEIGE, BLANCO, CAFE, COBRE, FIUSHA, GRIS, MORADO,
    // MULTICOLOR, NARANJA, NEGRO, ORO, PLATA, ROJO, ROSA, VERDE
    public function getColores($save=FALSE);

    // Devolverá un arreglo de marcas disponibles directamente del MarketPlace,
    // Si el parametro $save es TRUE, se almacenarán haciendo un llamado 
    // a la librería Auxiliar addMarca($data).
    public function getMarcas($save=FALSE);

    // Obtiene el producto desde el MarketPlace, si es necesario realizar,
    // más de una llamada para obtener toda la información se realizará
    // u devolverá el objeto de acuerdo a la especificación en: 
    // TODO:Producto
    public function getProducto($id);

    // Obtendrá un listado con los identificadores del producto en el MarketPlace
    // Haciendo un recorrido de acuerdo a:
    // limit : cantidad de registros
    // offset: grupo de registros a saltar antes de comenzar a descaregar
    // page: algunos no tienen las funciones de limit y offset en su lugar usan un token
    // activos: descargar solamente los activos 
    public function getProductos($limit = 100, $offset = 0, $page='', $activos=FALSE);

    // Recibirá un arreglo de productos a publicar
    // Realizará las llamadas correspondientes a su publicación, ya sea uno por uno o
    // En Lotes.
    // Cada intento agregará un registro el la bitacora, ya sea positivo o no addBitacora($data);
    // De ser confirmado el registro se harán las siguientes llamadas:
    // addProducto($data); // Si el producto no fue confirmado, en estatus se podrá 99 = Sin confirmar 
    // addStock($data); // Debe de ser llamado por cada variacion registrada

    // Los productos pueden tener varios tipo de variaciones SIZE, COLOR, PATTERN, UNICO o CUSTOM
    // Si el MarketPlace no permite las variaciones de PATTERN, UNICO o CUSTOM
    // Se deberá de crear un Producto por cada hijo, siendo el sku del hijo el que se registre.
    // y se deberán hacer sus llamadas correspondientes a addProducto y addStock
  
    // Auxiliar publicarItems($limit=100); Para obtener un listado de productos sin publicar
    public function postProductos();

    // Llegará un listado de productos por actualizar y se hará las llamadas correspondientes
    // de los datos del producto, descripción y atributos.
    // actualizarItems($limit=100); Para obtener un listado de productos ya publicados por actualizar
    // Por cada actualización llamar en Auxiliar a:
    // updateProducto($data);
    // addBitacora($data);
    public function putProductos();


    // Recibe un arreglo de productos por cerrar su publicación r y hace las llamadas correspondientes
    // Por cada actualización llamar en Auxiliar a:
    // closeProducto($data);
    // addBitacora($data);
    public function closeProductos($item);

    // Recibe un arreglo de productos por eliminar y hace las llamadas correspondientes
    // Por cada eliminación llamar en Auxiliar a:
    // removeProducto($data);
    // addBitacora($data);
    public function deleteProductos($item);

    // Solicita los productos por actualizar precio y realiza las llamadas correspondientes al MarketPlace
    // actualizarPrecios($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // updatePrecio($data);
    // addBitacora($data);
    public function putPrecio();

    // Solicita los productos por actualizar Stock y realiza las llamadas correspondientes al MarketPlace
    // actualizarPrecios($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // updateStock($data);
    // addBitacora($data);
    public function putStock();


    // Solicita los productos que tengan Variaciones nuevas y realiza las llamadas correspondientes al MarketPlace
    // actualizarVariaciones($limit=100); Para obtener un listado de productos ya publicados con precios por actualizar.
    // Por cada actualización llamar en Auxiliar a:
    // addStock($data);
    // addBitacora($data);
    public function postVariaciones();

    // Solicita las guias de los pedidos correspondientes (la referencia es del MarketPlace)
    // Por cada actualización llamar en Auxiliar a:
    // addGuia($data);
    // addBitacora($data);
    public function getGuias($pedidos);

    // Solicita un listado de los últimos pedidos actualizados
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

}

?>