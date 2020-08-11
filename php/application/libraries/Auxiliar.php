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

    /* Fevuelve el registro de campos a configurar */
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

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez
        $marca = [[
                'marca' => "nombre de la marca",
                'marca_market' => "101253" // Identificador de marca o lo mismo que marca de no existir
            ]
        ];
        Devuelve resumen de actualizaciones
    */    
    public function addMarcas($data) {
        $url = $this->server . "marcas";
        $this->checkData($data, ['marca','marca_market']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Se espera arreglo de arreglos con no más de 50 elementos por vez
        $color = [[
                'color_base' => "nombre de color",
                'color_market' => "101253" // Identificador de color
            ]
        ];
        Devuelve resumen de actualizaciones
    */    
    public function addColores($data) {
        $url = $this->server . "colores";
        $this->checkData($data, ['color_base','color_market']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Recibe un arreglo con la categoria a dar de alta
     y regresa la categoria registrada."
        [   'id' => 0,
            'categoria' => "Clave de Categoria en Market",
            'nombre'    => "Nombre de la categoria",
            'ruta'      => "/Abuelo/Padre/Hijo", # Arbol de categoria separado por Slash
            'padre'     => "Clave de la categoria padre
        ];
        Devuelve el registro generado
     */
    public function addCategoria($data) {
        $url = $this->server . "categorias";
        $this->checkData($data, ['id','categoria','nombre','ruta','padre']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Recibe arreglo con atributo
        $atr = [
            'id'           => 0, // Identificador de atributo
            'categoria_id' => 1001, // Identificador de categoría a la que pertenece el atributo
            'atributo'     => 'GENDER', // Clave del atributo en el marketplace
            'orden'        => 1, //Posicion del atributo en jerarquía 
            'nombre',      => 'Género', // Como se le presenta al usuario
            'mandatorio',  => 0, // 1 = Si, 0 = No
            'tipo_valor'   => 'string', / string, boolean, list, numeric
            'tipo_long_max' => 20, // Cuando es tring, longitud màxima 0 en todos los demas casos 
            'variante'     => 0,  // 1 = Si, 2 = No, si el atributo define como variante
            'mapa'         => 'GENERO,GENDER' // Mapa de atributos de MArketSync que tendían el valor correcto
        ];
        Devuelve el registro generado
    */
    public function addAtributo($data) {
        $url = $this->server . "atributos";
        $fields = ['id', 'categoria_id', 'atributo', 'orden', 'nombre', 'mandatorio', 
        'tipo_valor', 'tipo_long_max', 'variante', 'mapa'];        
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /*
        $valor = [
            'id'            => 0, // Identificador de registro
            'key_id'        => 125, // Identificador de atributo 
            'clasificacion' =>  'valor', // etiqueta,unidad,valor
            'clave'         =>  '1253', // identificador del market para el valor  
            'valor'         =>  'Hombre' // Valor de lista
        ];
        Devuelve el registro generado
    */
    public function addValor($data) {
        $url = $this->server . "valores";
        $fields = ['id', 'key_id', 'clasificacion', 'clave', 'valor'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /*  Solo inserta
        [
            'id'        => 0, // identificador de registro
            'evento_id' => 1, // Entero de 1-7
            'seccion'   => 'productos', // Tabla por afectar
            'row_id'    => 265, // Identificador de registro afectado (id de producto en este caso)  
            'acciones'  => 'Descripcion de operacin realizada, en caso de error el error que devuelve el MPS'
        ];

        Valores posibles para evento_id
        1   Agregar                   
        2   Actualizar                
        3   Actualiza Precio y/o Stock
        4   Eliminar                  
        5   Error                     
        6   Reconstruir               
        7   Consultar   (Para Pedids y Tickets)              

        Devuelve registro generado
    */
    public function addBitacora($data) {
        $url = $this->server . "bitacoras";
        $fields = ['id','evento_id', 'seccion', 'row_id', 'acciones'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /*
    [   
        'id'            => 0, // Identificador de registro
        'product_id'    => 12536, // Identificador de producto
        'precio'        => 1000.00,// Precio de lista
        'oferta'        => 800.00, // Precio oferta
        'envio'         => 65.00,  // Precio de envío
        'market_sku'    => 'MLM25369822', // Identificador del MarketPlace
        'transaction_id'=> 0, // Identificador de registro entero
        'referencia'    => 'https://market.com/id=145151', // Url del producto en el marketplace
        'estatus'       = 99
    ];
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

    /* Solo agregue los campos que vaya a actualizar y el "id"
    [
        'id'            => 1250360, // Identificador de registro
        'precio'        => 1000.00,// Precio de lista
        'oferta'        => 800.00, // Precio oferta
        'envio'         => 65.00,  // Precio de envío
        'market_sku'    => 'MLM25369822', // Identificador del MarketPlace
        'transaction_id'=> 0, // Identificador de registro entero
        'referencia'    => 'https://market.com/id=145151', // Url del producto en el marketplace
        'estatus'       = 1
    ];

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
        $fields = ['id', 'precio', 'oferta', 'envio', 'market_sku', 
        'transaction_id', 'referencia', 'estatus'];
        $this->checkData($data, ['id']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "PUT", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* 
    'id'            => 0,           // Identificador de registro
    'product_id'    => 1253,        // Identificador de producto
    'sku'           => '58369-250', // Sku hijo
    'stock_id'      => 1263001,     // Identificador de variacion
    'market_sku'    => '145236985221', // Identificador de registro en MPS
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS
    'stock'         => 1 // Stock registrado
    */
    public function addStock($data) {
        $url = $this->server . "stock";
        $fields = ['id', 'product_id', 'sku', 'stock_id', 'market_sku', 'referencia', 'stock'];
        $this->checkData($data, $fields);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }

    /* Solo agregue los campos que vaya a actualizar y el "id"
    'id'            => 0,           // Identificador de registro
    'market_sku'    => '145236985221', // Identificador de registro en MPS
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS
    'stock'         => 0 // Stock registrado
    */
    public function updStock($data) {
        $url = $this->server . "stock";
        $fields = ['id', 'market_sku', 'referencia', 'stock'];
        $this->checkData($data, ['id']);
        $params = ['market_id'=>$this->market];
        $res = callAPI($url, "POST", $this->publica, $this->privada, $params, $data);
        return json_decode($res);
    }


    // Funcion de comprobacion de columnas
    private function checkData($data, $fields) {
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