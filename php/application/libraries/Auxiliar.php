<?php

include_once(APPPATH.'helpers/curl_helper.php');
class Auxiliar {

    const LIMITE_REGISTROS = 50;

    public $cliente = 0;
    public $market = 0;
    public $acepta_html = FALSE;
    public $privada = null; // Llave privada
    public $publica = null; // Llave pública
    public $server = null; // Servidor de API
    public $requiere_upc = FALSE; // Si es TRUE, las variaciones que no cuenten con UPC se excluirán.
    public $requiere_marca = FALSE; // Si es TRUE, los productos que no cuenten con el empate de marcas se excluiran
    public $requiere_color = FALSE; // Si es TRUE, las variaciones que no cuenten con el empate de color se excluiran
    public $requiere_categoria = TRUE; // Si es FALSE el MPS no soporta categorías    
    public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno

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
        $params = ['market'=>$this->market];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params);
        return $res;
    }

    public function addSetting($data, $global=FALSE) {
        $url = $this->server . "settings";
        var_dump($data);
        $this->checkData($data, ['id:i','valor:a']);
        $params = ['market'=>$this->market, 'clave'=>$global?'global':'config'];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    public function updSetting($data, $global=FALSE) {
        $url = $this->server . "settings";
        $this->checkData($data, ['id:i','valor:a']);
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
        if ($categoria) $params[] = ['categoria'=>$categoria];
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
        $fields = ['id:i', 'key_id:i', 'clasificacion:s8', 'clave:s50', 'valor:s50'];        
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
    public function getProductos($estatus=-1, $id=0) {
        $estatus = (int)$estatus;
        $id = (int)$id;
        $url = $this->server . "productos";
        $params = ['market'=>$this->market, 'filtro'=>$estatus];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
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
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Elimina el registro del stock */
    public function delStock($id) {
        $url = $this->server . "stock";
        $data = ['id'=>(int)$id];
        $params = ['market'=>$this->market];
        $res = callAPI($url, "DELETE", $this->publica, $this->privada, $params, $data);
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
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
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

    /* Agrega registro de guías */
    public function addGuia($data) {
        $url = $this->server . "guias";
        $fields = ['id:i','pedido_id:i','label:s15000','guia:s50','mensajeria:s30','estatus:t'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Actualiza registro de guías, se tienen que incluir todos los campos */
    public function updGuia($id) {
        $url = $this->server . "guias";
        $fields = ['id:i','estatus:t'];
        $this->checkData($data, $fields);
        $params = ['market'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Se ingresa registro de pedido */
    public function addPedidos($data) {
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
            'id:i' => $id,
            'estatus:s30' => $estatus,
            'total:f'   => $total,
            'orden_id:i'=> $pedido_mkt // No lo incluya si no lova a procesar
        ];
        $this->checkData($data, $fields);
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

    public function getPedidos($filtro=0,$limit=50) {
        $filtro = (int)$filtro;
        $url = $this->server . "pedidos";
        $params = ['market'=>$this->market, 'filtro'=>$filtro, 'limit'=>$limit];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    /* Registra feed en caso  de procesamiento en lote */
    public function addFeed($data) {
        $url = $this->server . "feeds";
        $fields = ['id:i','feed:s50','request:s15000'];
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
    public function getFeeds($limit=50) {
        $filtro = 1;
        $url = $this->server . "feeds";
        $params = ['market'=>$this->market, 'filtro'=>$filtro, 'limit'=>$limit];
        $res = callAPI($url, "GET", $this->publica, $this->privada, $params, $data);
        return $res;
    }

    function chkDate($date, $format = 'Y-m-d HH:mm:ss') {
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
                    if ($dt=='i' && (!is_numeric($v)||(int)$v!=$v) && !($n=='id' && is_null($v)))
                        throw new Exception(" field ($n) at row [$i] is not a valid integer $v.");
                    if ($dt=='f' && !is_numeric($v))
                        throw new Exception(" field ($n) at row [$i] is not a valid number.");
                    if ($dt=='b' && (!is_numeric($v) ||  !in_array((int)$v, [0,1])))
                        throw new Exception(" field ($n) at row [$i] is not a valid value.");
                    if ($dt=='t' && (!is_numeric($v) ||  (int)$v > 255))
                        throw new Exception(" field ($n) at row [$i] is not a valid value.");
                    if ($dt=='d' && !chkDate($v))
                        throw new Exception(" field ($n) at row [$i] is not a valid date.");
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