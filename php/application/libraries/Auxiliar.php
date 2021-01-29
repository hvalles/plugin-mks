<?php

include_once(APPPATH.'helpers/curl_helper.php');
class Auxiliar {

    const LIMITE_REGISTROS = 100;

    public $cliente = 0;
    public $market = 0;
    public $acepta_html = FALSE;
    public $privada = null; // Llave privada
    public $publica = null; // Llave pública
    public $server = null; // Servidor de API
    public $requiere_upc = FALSE; // Si es TRUE, las variaciones que no cuenten con UPC se excluirán.
    public $requiere_marca = FALSE; // Si es TRUE, los productos que no cuenten con el empate de marcas se excluiran
    public $requiere_color = TRUE; // Si es TRUE, las variaciones que no cuenten con el empate de color se excluiran
    public $requiere_categoria = TRUE; // Si es FALSE el MPS no soporta categorías    
    public $requiere_envio = FALSE; // Si es TRUE el MPS no cobra el envío, lo hace el seller.    
    public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno
    public $requiere_sat = FALSE; // Si el MPS requiere codigo de producto del SAT
    public $precio_minimo = 10.0;
    public $imagen_minimo = 1;


    // DEvuelve el codigo de respuesta de la llamada
    public function getCode($res) {
        if (is_object($res) && property_exists($res, 'http_code')) 
            return $res->http_code;
        if (is_array($res) && isset($res['http_code']))
            return $res['http_code'];

        return 'Error';
    }

    // Revisa que la llamada haya tenido éxito
    public function responseOk($res) {
        $code = $this->getCode($res);
        //print "***** Code:$code".PHP_EOL;
        return ( $code >= 200 && $code < 300);
    }

    public function getAnswer($data, $key=FALSE) {
        if (!$data) return FALSE;
        if (!$this->responseOk($data)) return FALSE;
        if (!isset($data->answer) || !is_array($data->answer) || count($data->answer)==0) return FALSE;
        if (!$key) return $data->answer[0];
        $res = (string)$data->answer[0]->{$key};
        return $res;
    }


    // Devolvera el atribut generico que se aproxime primero al deseado
    public function mapAtributo($mapa, $item=null, $variacion=null) {
        $ops = [];
        $comodin = [];
        $vars = [];
        $cons = '';
        foreach (explode(',',$mapa) as $o) 
            if ( strpos($o,'%')!==FALSE) {
                $comodin[] = str_replace('%','',trim($o)); // atributos a escoger
            } elseif ( strpos($o,'$')!==FALSE) {
                $vars[] = str_replace('$','', trim($o)); // Valor de item
            } elseif ( strpos($o,':')!==FALSE) {
                $cons = str_replace(':','', trim($o)); // Valor constante
            } else {            
                $ops[] = trim($o); // Este atributo
            }

        // Es una variable del registro
        if ($vars && $item) return $item->{$vars[0]};
        if ($cons && $item) return $cons;

        if ($variacion) { // Atributo de variacion
            foreach ($variacion->atributos as $key) {
                //var_dump($key);
                if (in_array($key->atributo,$ops) && $key->valor) return $key->valor;
                foreach ($comodin as $k) 
                    if (strpos($k, $key->atributo)!== FALSE && $key->valor)
                        return $key->valor;
            }
        }
        if ($item) { // Atributo de item
            foreach ($item->atributos as $key) {
                if (in_array($key->atributo,$ops) && $key->valor) return $key->valor;
                foreach ($comodin as $k) 
                    if (strpos($k, $key->atributo)!== FALSE && $key->valor)
                        return $key->valor;
            }
        }

        return ''; // No se encontro
    }

    /* Devuelve el registro de campos a configurar */
    public function getConfig() {
        return array(
            'cliente' => $this->cliente, 
            'market' => $this->market,
            'server' => $this->server,
            'publica' => $this->publica,
            'privada' => $this->privada,
            'requiere_upc' => $this->requiere_upc,
            'requiere_marca' => $this->requiere_marca,
            'requiere_color' => $this->requiere_color,
            'requiere_categoria' => $this->requiere_categoria,
            'precio_minimo' => $this->precio_minimo,
            'imagen_minimo' => $this->imagen_minimo,
            'requiere_envio' => $this->requiere_envio,
            'requiere_sat' => $this->requiere_sat,
            'procesa_lotes' => $this->procesa_lotes
        );
    }

    public function setConfig($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getSetting($id=0) {
        $url = $this->server . "settings";
        if (is_numeric($id)) {
            $params = ['market'=>$this->market, 'id'=>$id];
        } else {
            $params = ['market'=>$this->market, 'clave'=>$id];            
        }
        
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    public function addSetting($data, $global=FALSE) {
        $url = $this->server . "settings";
        $clave = $global === TRUE ? 'global' : 'config';
        if (isset($data['clave'])) { 
            $clave=$data['clave'];
            unset($data['clave']);
        }
        $this->checkData($data, ['id:i','valor:s1500']);
        $params = ['market'=>$this->market, 'clave'=>$clave];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    public function updSetting($data, $global=FALSE) {
        $url = $this->server . "settings";
        $this->checkData($data, ['id:i','valor:s1500']);
        $params = ['market'=>$this->market, 'clave'=>$global?'global':'config'];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    public function delSetting($id, $global=FALSE) {
        $url = $this->server . "settings";
        $id = (int)$id;
        $params = ['market'=>$this->market,'id'=>$id];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez  */    
    public function addMarcas($data) {
        $url = $this->server . "marcas";
        $this->checkData($data, ['marca:s50','marca_market:s50'],TRUE);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez    */    
    public function addColores($data) {
        $url = $this->server . "colores";
        $this->checkData($data, ['color_base:s50','color_market:s50'], TRUE);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* obtiene listado de categorias del MPS */
    public function getCategoria($categoria='') {
        $url = $this->server . "categorias";
        $params = ['market'=>$this->market];
        if ($categoria) $params['categoria']=$categoria;
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Recibe un arreglo con la categoria a dar de alta y regresa la categoria registrada." */
    public function addCategoria($data) {
        $url = $this->server . "categorias";
        $this->checkData($data, ['id:i','categoria:s30','nombre:s100','ruta:s255','padre:i']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Recibe un arreglo con la categoria por actualizar y regresa la categoria registrada." */
    public function updCategoria($data) {
        $url = $this->server . "categorias";
        $this->checkData($data, ['id']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Recibe id  regresa resumen." */
    public function delCategoria($id) {
        $url = $this->server . "categorias";
        $params = ['market'=>$this->market, 'id'=>(int)$id];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params);
        return $res;
    }

    // Obtiene listado de atributos por categoría
    public function getAtributo($categoria, $atributo='') {
        $url = $this->server . "atributos";
        $params = ['market'=>$this->market];
        if ($atributo) $params['atributo']=$atributo;
        if ($categoria) $params['categoria']=$categoria;
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        
        return $res;
    }

    /* Recibe arreglo con atributo,  Devuelve el registro generado  */
    public function addAtributo($data) {
        $url = $this->server . "atributos";
        $fields = ['id:i', 'categoria_id:i', 'atributo:s100', 'orden:i', 'nombre:s100', 
        'mandatorio:b', 'tipo_valor:s20', 'tipo_long_max:i', 'variante:b', 'mapa:s200'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Recibe un arreglo con id  por eliminar y regresa resumen." */
    public function delAtributo($id) {
        $url = $this->server . "atributos";
        $params = ['market'=>$this->market, 'id'=>(int)$id];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params);
        return $res;
    }

    // Obtiene listado de valores por categoria - atributo
    public function getValor($categoria, $atributo, $id=0) {
        $url = $this->server . "atributos";
        $params = ['market'=>$this->market];
        if ($id) $params['ids'] = (int)$id;
        if ($atributo) $params['atributo']=$atributo;
        if ($categoria) $params['categoria']=$categoria;
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        
        return $res;
    }


    /*  Recibe arreglo del valor de atributo,  Devuelve el registro generado  */
    public function addValor($data) {
        $url = $this->server . "valores";
        $fields = ['id:i', 'key_id:i', 'clasificacion:s8', 'clave:s50', 'valor:s100'];        
        $this->checkData($data, $fields);
        if (!in_array($data['clasificacion'],['etiqueta','unidad','valor']))
            throw new Exception("Error clasificacion value is not valid. ['etiqueta','unidad','valor']");
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }
    /* Recibe un arreglo con id  por eliminar y regresa resumen." */
    public function delValor($id) {
        $url = $this->server . "valores";
        $params = ['market'=>$this->market, 'id'=>(int)$id];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params);
        return $res;
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
        $fields = ['id:i','evento_id:i', 'seccion:s30', 'row_id:i', 'acciones:s15000'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Agrega producto, recibe registro y devuelve registro insertado
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
        $fields = ['id:i', 'product_id:i', 'precio:f', 'oferta:f', 'envio:f', 
        'market_sku:s20', 'transaction_id:i', 'referencia:s200', 'estatus:t'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Obtiene el listado de productos de acuerdo al estatus  */
    public function getProductos($estatus=-1, $id=0, $limit=50) {
        $estatus = (int)$estatus;
        $id = (int)$id;
        $url = $this->server . "productos";
        $params = ['market'=>$this->market, 'filtro'=>$estatus];
        $params['requiere_upc'] = $this->requiere_upc?1:0;
        $params['requiere_marca'] = $this->requiere_marca?1:0;
        $params['requiere_color'] = $this->requiere_color?1:0;
        $params['requiere_categoria'] = $this->requiere_categoria?1:0;        
        $params['requiere_envio'] = $this->requiere_envio?1:0;        
        $params['requiere_sat'] = $this->requiere_sat?1:0;        
        $params['precio_minimo'] = $this->precio_minimo;
        $params['imagen_minimo'] = $this->imagen_minimo;
        if ($id) $params['ids'] = $id;
        if ($limit) $params['limit']=$limit;
        //print_r($url);
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        //print_r($res);
        return $res;
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
        $fields = ['id:i', 'product_id:i', 'precio:f', 'oferta:f', 'envio:f', 
        'market_sku:s20', 'transaction_id:i', 'referencia:s200', 'fulfillment:b', 'estatus:t'];
        $this->checkData($data, ['id','product_id']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Elimina el registro del producto y del stock */
    public function delProducto($id) {
        $url = $this->server . "productos";
        $data = ['id'=>(int)$id];
        $params = ['market'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
        return $res;
    }


    /* Agrega Stock, recibe un arreglo y regresa el registro */
    public function addStock($data) {
        $url = $this->server . "stock";
        $fields = ['id:i', 'product_id:i', 'sku:s20', 'stock_id:i', 
        'market_sku:s20', 'referencia:s20', 'stock:i'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Actualiza Stock, solo agregue los campos que vaya a actualizar y el "id"  */
    public function updStock($data) {
        $url = $this->server . "stock";
        $fields = ['id:i', 'market_sku:s20', 'referencia:s20', 'stock:i'];
        $this->checkData($data, ['id']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Elimina el registro del stock */
    public function delStock($id) {
        $url = $this->server . "stock";
        $params = ['market'=>$this->market, 'id' => (int)$id];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params);
        return $res;
    }

  /*  Agrega imagen, recibe arreglo    */
    public function addImagen($data) {
        $url = $this->server . "imagenes";
        $fields = ['id:i','product_id:i','sku:s20','orden:t','id_mkt:s30', 'url:s500'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

   /* Actualiza imagen, agregue todos los campos para actualizar    */
    public function updImagen($data) {
        $url = $this->server . "imagenes";
        $fields = ['id:i','id_mkt:s30', 'url:s500'];
        $this->checkData($data, ['id']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Elimina el registro de imagen */
    public function delImagen($id) {
        $url = $this->server . "imagenes";
        $data = ['id'=>(int)$id];
        $params = ['market'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Localiza guias por actualizar */
    public function getGuia($pedido=0) {
        $pedido = (int)$pedido;
        $url = $this->server . "guias";
        $params = ['market'=>$this->market];
        if ($pedido) $params['pedido_id'] = $pedido;
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Agrega registro de guías */
    public function addGuia($data) {
        $url = $this->server . "guias";
        $fields = ['id:i','pedido_id:i','label:s15000','guia:s50','mensajeria:s30','estatus:t'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Actualiza registro de guías */
    public function updGuia($id) {
        $url = $this->server . "guias";
        $data = ['id'=>(int)$id,'estatus'=>1];
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Se ingresa registro de pedido */
    public function addPedido($data) {
        $url = $this->server . "pedidos";
        $fields = ['id:i', 'referencia:s30', 'fecha_pedido:d', 'fecha_autoriza:d',
        'email:s50', 'entregara:s120', 'telefono:s20', 'direccion:s100', 
        'entrecalles:s100', 'colonia:s100', 'ciudad:s100', 'estado:s100', 
        'observaciones:s200','cp:s5', 'envio:f', 'comision:f', 'estatus:s30', 
        'shipping_id:i', 'detalle:a'];
        
        $detalle = ['sku:s20','descripcion:s120','cantidad:i','precio:f',
        'color:s20','talla:s10', 'referencia:s30', 'fulfillment:b'];
        $this->checkData($data, $fields);
        if (!is_array($data['detalle']) || count($data['detalle'])==0)
            throw new Exception("Error No Items found.", 1);
        
        foreach ($data['detalle'] as $row) {
            $this->checkData($row, $detalle);
        }
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Atualiza registro de pedido   */
    public function updPedido($id, $estatus, $total=0, $pedido_mkt=null) {
        $url = $this->server . "pedidos";
        $data = [
            'id' => $id,
            'estatus' => $estatus,
            'total'   => $total,            
        ];
        if ($pedido_mkt) $data['orden_id:i'] = $pedido_mkt; // No lo incluya si no lo va a procesar
        $this->checkData($data, ['id:i','estatus:s30','total:f']);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;

    }

    /* Regresa pedidos 
    POSIBLES ESTATUS
    0 // Todos Los últimos $limit registros en orden descendente por fecha de actualización
    PEDIDO_NOREPORTADO // Pedidos que no han sido reportados al e-commerce para si generación
    PEDIDO_CAMBIO  // Pedidos que actualizaron el 
    */

    public function getPedido($filtro=0,$referencia='',$limit=50) {
        $filtro = (int)$filtro;
        $url = $this->server . "pedidos";
        $params = ['market'=>$this->market, 'filtro'=>$filtro];
        if ($limit) $params['limit'] = $limit;
        if ($referencia) $params['referencia'] = $referencia;
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Registra feed en caso  de procesamiento en lote */
    public function addFeed($data) {
        $url = $this->server . "feeds";
        $fields = ['id:i','feed:s50','request:s100000'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;

    }
    /* Actualiza feed con respuesta en caso  de procesamiento en lote */
    public function updFeed($id, $answer) {
        $url = $this->server . "feeds";
        $data = ['id'=>(int)$id, 'answer'=>$answer];
        if ($answer)  $data['revisado'] = 1;
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* obtiene feeds que aun no han sido marcados como concluidos */
    public function getFeed($limit=50) {
        $filtro = 1;
        $url = $this->server . "feeds";
        $params = ['market'=>$this->market, 'filtro'=>$filtro, 'limit'=>$limit];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Obtiene el porcentaje de impuiestos */
    public function getTax() {
        $url = $this->server . "pedidos/iva";
        $params = ['market'=>$this->market];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    /* Almacena productos del marketplace en MarketSync para futura conciliacion */
    public function addConcilia($data) {
        $url = $this->server . "audita";
        $fields = ['id:i','market_sku:s20','sku:s20', 'nombre:s120','ficha:s15000',
        'seller_sku:s20','url:s500','stock:i', 'precio:f', 'oferta:f', 'gtin:s20', 
        'imagen:s500', 'estatus:s20', 'ref_parent:s20', 'ref_child:s20', 'medida:s20',
        'bullet1:s500','bullet2:s500','bullet3:s500','bullet4:s500','bullet5:s500',
        'modelo:s30', 'marca:s50'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Reconstruye informacion de productos dados de alta en el marketplace directamente 
    y que existen en MarketSync*/
    public function updConcilia() {
        $url = $this->server . "audita";
        $params = ['market'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada,$params);
        return $res;
    }
    
    function chkDate($date, $format = 'Y-m-d H:i:s') {
        DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();
        return $errors['warning_count'] === 0 && $errors['error_count'] === 0;
    }
    // Funcion de comprobacion de columnas $mas=Multiple ArrayS
    private function checkData($data, $fields, $mas=FALSE) {
        if (!$mas) $data = [$data];
        if (!$this->market)
            throw new Exception('Settings are not configured.');

        if (!$this->privada || !$this->publica)
            throw new Exception('Keys are not configured.');

        if (!$this->server || !$this->cliente)
            throw new Exception('Server or client are not configured.');

        if (!is_array($data) || !is_array($data[0])) 
            throw new Exception('An Array of arrays was expected.');

        if (count($data) > Auxiliar::LIMITE_REGISTROS) 
            throw new Exception('Array Outbound Error ['. Auxiliar::LIMITE_REGISTROS.'].');
        
        $i=0;
        foreach ($data as $m) {
            #if (count($m) != count($fields))
            #    throw new Exception("Unnecesary fields at row [$i]." . var_export($m,TRUE) );
            
            foreach ($fields as $f) {
                $f1 = explode(':',$f);
                if (!array_key_exists($f1[0],$m))
                    throw new Exception("Missing field ($f) at row [$i].");
                
                $n = $f1[0]; // nombre
                $v = $m[$n]; // valor
                
                if (count($f1)==2){
                    $dt = $f1[1]; // tipo de dato
                    if ($dt[0]=='s' && strlen($v)> (int)str_replace('s','',$dt))
                        throw new Exception(" field ($n) at row [$i] oversize its capacity.");
                    if ($dt=='i' && (!is_numeric($v)||(int)$v!=$v) && !(in_array($n,['id','shipping_id']) && is_null($v)))
                        throw new Exception(" field ($n) at row [$i] is not a valid integer $v.");
                    if ($dt=='f' && !is_numeric($v))
                        throw new Exception(" field ($n) at row [$i] is not a valid number $v.");
                    if ($dt=='b' && (!is_numeric($v) ||  !in_array((int)$v, [0,1])))
                        throw new Exception(" field ($n) at row [$i] is not a valid value $v.");
                    if ($dt=='t' && (!is_numeric($v) ||  (int)$v > 255))
                        throw new Exception(" field ($n) at row [$i] is not a valid value.");
                    if ($dt=='d' && !$this->chkDate($v))
                        throw new Exception(" field ($n) at row [$i] is not a valid date. $v");
                    if ($dt=='a' && !is_array($v))
                        throw new Exception(" field ($n) at row [$i] is not a valid array.");
                }
            }            
            $i++;
        }
        return TRUE;
    }

}

?>