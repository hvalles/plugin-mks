<?php


class Auxiliar {

    const LIMITE_REGISTROS = 50;
    public function __construct() 	{
        parent::__construct();
        $this->CI =& get_instance();
        $this->load->helper('curl');
    }

    public $cliente = 0;
    public $market = 0;
    public $acepta_html = FALSE;
    public $privada = null; // Llave privada
    public $publica = null; // Llave pública
    public $server = null; // Servidor de API
    public $requiere_upc = FALSE; // Si es TRUE, las variaciones que no cuenten con UPC se excluirán.
    public $requiere_marca = FALSE; // Si es TRUE, los productos que no cuenten con el empate de marcas se excluiran
    public $requiere_color = FALSE; // Si es TRUE, las variaciones que no cuenten con el empate de color se excluiran
    public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno

    /* Devuelve el registro de campos a configurar */
    public function getConfig() {
        return array(
            'cliente' => $this->cliente,
            'market' => $this->market,
            'server' => $this->server,
            'server' => $this->server,
            'publica' => $this->public,
            'privada' => $this->privada,
            'requiere_upc' => $this->requiere_upc,
            'requiere_marca' => $this->requiere_marca,
            'requiere_color' => $this->requiere_color,
            'procesa_lotes' => $this->lotes
        );
    }

    public function setConfig($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez  */    
    public function addMarcas($data) {
        $url = $this->server . "marcas";
        $this->checkData($data, ['marca','marca_market']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez    */    
    public function addColores($data) {
        $url = $this->server . "colores";
        $this->checkData($data, ['color_base','color_market']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Recibe un arreglo con la categoria a dar de alta y regresa la categoria registrada." */
    public function addCategoria($data) {
        $url = $this->server . "categorias";
        $this->checkData($data, ['id','categoria','nombre','ruta','padre']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Recibe arreglo con atributo,  Devuelve el registro generado  */
    public function addAtributo($data) {
        $url = $this->server . "atributos";
        $fields = ['id', 'categoria_id', 'atributo', 'orden', 'nombre', 'mandatorio', 
        'tipo_valor', 'tipo_long_max', 'variante', 'mapa'];        
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /*  Revibe arreglo del valor de atributo,  Devuelve el registro generado  */
    public function addValor($data) {
        $url = $this->server . "valores";
        $fields = ['id', 'key_id', 'clasificacion', 'clave', 'valor'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /*  Solo inserta bitácora y devuelve registro 
    
        Valores posibles para evento_id
        1   Agregar                   
        2   Actualizar                
        3   Actualiza Precio y/o Stock
        4   Eliminar                  
        5   Error                     
        6   Reconstruir               
        7   Consultar   (Para Pedids y Tickets)              

    */
    public function addBitacora($data) {
        $url = $this->server . "bitacoras";
        $fields = ['id','evento_id', 'seccion', 'row_id', 'acciones'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Agrega producto, revibe registro y devuelve registro insertado
    // Posibles estatus
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96;     
    const ITEM_SIN_PUBLICAR = -1; 
    */
    public function addProducto($data) {
        $url = $this->server . "productos";
        $fields = ['id', 'product_id', 'precio', 'oferta', 'envio', 'market_sku', 
        'transaction_id', 'referencia', 'estatus'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Obtiene el listado de productos de acuerdo al estatus  */
    public function getProductos($estatus=-1, $id=0) {
        $estatus = (int)$estatus;
        $id = (int)$id;
        $url = $this->server . "productos";
        $params = ['market_id'=>$this->market, 'filtro'=>$estatus];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Actualia producto
    Solo agregue los campos que vaya a actualizar y el "id"

    // Posibles estatus
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96;     
    const ITEM_SIN_PUBLICAR = -1; 

    */
    public function updProducto($data) {
        $url = $this->server . "productos";
        $fields = ['id', 'product_id', 'precio', 'oferta', 'envio', 'market_sku', 
        'transaction_id', 'referencia', 'fulfillment', 'estatus'];
        $this->checkData($data, ['id','product_id']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Elimina el registro del producto y del stock */
    public function delProducto($id) {
        $url = $this->server . "productos";
        $data = ['id'=>(int)$id];
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }


    /* Agrega Stock, recibe un arreglo y regresa el registro */
    public function addStock($data) {
        $url = $this->server . "stock";
        $fields = ['id', 'product_id', 'sku', 'stock_id', 'market_sku', 'referencia', 'stock'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Actualiza Stock, solo agregue los campos que vaya a actualizar y el "id"  */
    public function updStock($data) {
        $url = $this->server . "stock";
        $fields = ['id', 'market_sku', 'referencia', 'stock'];
        $this->checkData($data, ['id']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Elimina el registro del stock */
    public function delStock($id) {
        $url = $this->server . "stock";
        $data = ['id'=>(int)$id];
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

  /*  Agrega imagen, recibe arreglo    */
    public function addImagen($data) {
        $url = $this->server . "imagenes";
        $fields = ['id','market_id','product_id','sku','orden','id_mkt', 'url'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

   /* Actualiza imagen, agregue todos los campos para actualizar    */
    public function updImagen($data) {
        $url = $this->server . "imagenes";
        $fields = ['id','id_mkt', 'url'];
        $this->checkData($data, ['id']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Elimina el registro de imagen */
    public function delImagen($id) {
        $url = $this->server . "imagenes";
        $data = ['id'=>(int)$id];
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Agrega registro de guías */
    public function addGuia($data) {
        $url = $this->server . "guias";
        $fields = ['id','pedido_id','label','guia','mensajeria','estatus'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Actualiza registro de guías, se tienen que incluir todos los campos */
    public function updGuia($id) {
        $url = $this->server . "guias";
        $fields = ['id','estatus'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }


    /* Se ingresa registro de pedido */
    public function addPedidos($data) {
        $url = $this->server . "pedidos";
        $fields = ['id', 'referencia', 'fecha_pedido', 'fecha_autoriza','email',
        'entregara', 'telefono', 'direccion', 'entrecalles', 'colonia', 'ciudad',
        'estado', 'observacione','cp', 'envio', 'comision', 'estatus', 
        'shipping_id', 'detalle'];
        
        $detalle = ['sku','descripcion','cantidad','precio','color','talla', 'referencia', 'fulfillment'];
        $this->checkData($data, $fields);
        if (!is_array($data['detalle']) || count($data['detalle'])==0)
            throw new Exception("Error Not Items found.", 1);
        
        foreach ($data['detalle'] as $row) {
            $this->checkData($row, $detalle);
        }
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }


    /* Atualiza registro de pedido   */
    public function updPedido($id, $estatus, $total=0, $pedido_mkt=null) {
        $url = $this->server . "pedidos";
        $data = [
            'id' => $id,
            'estatus' => $estatus,
            'total'   => $total,
            'orden_id'=> $pedido_mkt
        ];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return json_decode($res);

    }

    /* Regresa pedidos 
    POSIBLES ESTATUS
    0 // Todos Los últimos $limit registros en orden descendente por fecha de actualización
    PEDIDO_NOREPORTADO // Pedidos que no han sido reportados al e-commerce para si generación
    PEDIDO_CAMBIO  // Pedidos que actualizaron el 
    */

    public function getPedidos($filtro=0,$limit=50) {
        $filtro = (int)$filtro;
        $url = $this->server . "pedidos";
        $params = ['market_id'=>$this->market, 'filtro'=>$filtro, 'limit'=>$limit];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Registra feed en caso  de procesamiento en lote */
    public function addFeed($data) {
        $url = $this->server . "feeds";
        $fields = ['id','feed','request'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);

    }
    /* Actualiza feed con respuesta en caso  de procesamiento en lote */
    public function updFeed($id, $answer) {
        $url = $this->server . "feeds";
        $fields = ['id','answer'];

        $data = ['id'=>$id, 'answer'=>$answer];
        if ($answer)  $data['revisado'] = 1;
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* obtiene feeds que aun no han sido marcados como concluidos */
    public function getFeeds($limit=50) {
        $filtro = 1;
        $url = $this->server . "feeds";
        $params = ['market_id'=>$this->market, 'filtro'=>$filtro, 'limit'=>$limit];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }


    // Funcion de comprobacion de columnas
    private function checkData($data, $fields) {
        if (!$this->market)
            throw new Exception('Settings are not configured.');

        if (!$this->privada || !$this->publica)
            throw new Exception('Keys are not configured.');

        if (!is_array($data) || !is_array($data[0])) 
            throw new Exception('An Array of arrays was expected.');
        
        if (count($data) > Auxiliar::LIMITE_REGISTROS) 
            throw new Exception('Array Outbound Error ['. Auxiliar::LIMITE_REGISTROS.'].');
        
        $i=0;
        foreach ($data as $m) {
            if (count($m) != count($fields))
                throw new Exception("Unnecesary fields at row [$i].");
            
            foreach ($fields as $f) {
                if (!isset($m[$f]))
                    throw new Exception("Missing field ($f) at row [$i].");
            }            
            $i++;
        }
        return TRUE;
    }

}




?>