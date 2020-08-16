
# Clase Auxiliar


Se utiliza para realizar los mocimientos a la base  de datos a través de la API de MarkteSync,
esta librería es exclusiva para los integradores de MarketPlaces y no para un ERP o aplicación
corporativa.

## Generalidades
1. A excepciòn de Marcas y Colores, el agregado de registros es individual
2. EL procesamiento en lotes (Marcas y Colores) tiene un máximo de 50 elementos
3. A menos de que exista explicitamente la funcionalidad de actualización solamente se podrán agregar registros
4. El mantenimiento de los procesos en lotes se hace volviendo a llamar al agregado, si se desea eliminar, la columna secundaria debera de ser vacía.
5. Para la actualización de registros en donde se admite, siempre le requerirá el "id" del registro
6. Al actualizar no tiene que agregar todas las columnas, solamente las que vaya a actualizar y las ue sirvan de identificación.

#### Configuracion inicial

```php
public $cliente = 0;
public $market = 0;
public $acepta_html = FALSE;
public $privada = null; // Llave privada
public $publica = null; // Llave pública
public $server = null; // Servidor de API
public $requiere_upc = FALSE; // Si es TRUE, las variaciones que no cuenten con UPC se excluirán.
public $requiere_marca = FALSE; // Si es TRUE, los productos que no cuenten con el empate de marcas se excluirán
public $requiere_color = FALSE; // Si es TRUE, las variaciones que no cuenten con el empate de color se excluirán
public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno (default).
```

-  Devuelve el registro de campos a configurar 
- `public function getConfig()  `
```php
            'cliente' => $this->cliente, // [int]
            'market' => $this->market, // [int]
            'server' => $this->server, // [string(50)]
            'publica' => $this->public, // [string(64)]
            'privada' => $this->privada, // [string(64)]
            'requiere_upc' => $this->requiere_upc, // [tinyint]
            'requiere_marca' => $this->requiere_marca, // [tinyint]
            'requiere_color' => $this->requiere_color, // [tinyint]
            'procesa_lotes' => $this->lotes // [tinyint]
```

- Establece la configuración inicial
- `public function setConfig($data) `

#### El Plugin almacena su configuracion en esta seccion de settings
- Puede almacenar datos globales como el url del API que es común para
todos los clientes ($global=TRUE)
- Puede alkmacenar datos relacionados con el cliente como el SellerID,
PrivateKey, Token, RefreshToken, etc. ($global=FALSE)

#### Obtiene parámetros de configurración
- Obtiene configuracion del plugin  $id [int]
- `public function getSetting($id=0)`

#### Agrega configuración a cliente nuevo o global
-  Agrega configuracion del plugin
```php      [
    'id' => null, // Identificador de registro [int]
    'valor' => [] // Arreglo de configuracion por almacenar [array]
    ]
```
- `public function addSetting($data, $global=FALSE)`

#### Actualiza configuracion a cliente o global
- Actualiza configuracion del plugin
```php      [
    'id' => 12563, // Identificador de registro [int]
    'valor' => [] // Arreglo de configuracion por almacenar [array]
    ]
```
- `public function updSetting($data, $global=FALSE)`

#### Elimina configuracion, usualmente a clientes
- Elimina configuracion del plugin $id [int]
- `public function delSetting($id, $global=FALSE)`


#### Registro de marcas del MPS
- Se espera arreglo de arreglos con no más de 50 elementos por vez
```php        $marca = [
                'marca' => "nombre de la marca", // [string(50)]
                'marca_market' => "101253" // Identificador de marca o lo mismo que marca de no existir [string(50)]
            ]
        ];
```
- `public function addMarcas($data)`
  
#### Registro de colores
- Se espera arreglo de arreglos con no más de 50 elementos por vez
 ```php
    $color = [[
        'color_base' => "nombre de color", // [string(50)]
        'color_market' => "101253" // Identificador de color [string(50)]
        ];
```
- `public function addColores($data)`

#### Registro de categorias
-  Recibe un arreglo con la categoria a dar de alta y regresa la categoria registrada.
```php
        [   'id' => null, // [int]
            'categoria' => "Clave de Categoria en Market", // [string(30)]
            'nombre'    => "Nombre de la categoria", // [string(100)]
            'ruta'      => "/Abuelo/Padre/Hijo", # Arbol de categoria separado por Slash [string(255)]
            'padre'     => "Clave de la categoria padre" // [int]
        ];
```
- `public function addCategoria($data)`

#### Registro de atributos de categoría
- Recibe arreglo con atributo
```php
        $atr = [
            'id'           => null, // Identificador de atributo [int]
            'categoria_id' => 1001, // Identificador de categoría a la que pertenece el atributo [int]
            'atributo'     => 'GENDER', // Clave del atributo en el marketplace [string(100)]
            'orden'        => 1, //Posicion del atributo en jerarquía  [int]
            'nombre',      => 'Género', // Como se le presenta al usuario [string(100)]
            'mandatorio',  => 0, // 1 = Si, 0 = No [tinyint]
            'tipo_valor'   => 'string', // string, boolean, list, numeric [string(20)]
            'tipo_long_max' => 20, // Cuando es string, longitud màxima 0 en todos los demas casos [int]
            'variante'     => 0,  // 1 = Si, 2 = No, si el atributo define como variante [tinyint]
            'mapa'         => 'GENERO,GENDER' // Mapa de atributos de MArketSync que tendían el valor correcto [string(200)]
        ];
```
- Devuelve el registro generado
- `public function addAtributo($data)`

#### Registro de valores de atributo
- Recibe arreglo de valor 
```php
        $valor = [
            'id'            => null, // Identificador de registro [int]
            'key_id'        => 125, // Identificador de atributo [int]
            'clasificacion' =>  'valor', // etiqueta,unidad,valor [enum]
            'clave'         =>  '1253', // identificador del market para el valor [string(50)]
            'valor'         =>  'Hombre' // Valor de lista [string(50)]
        ];
```
- Devuelve el registro generado
- `public function addValor($data)`

#### Registro de Bitácora
- Recibe arreglo
```php        [
            'id'        => null, // identificador de registro [int]
            'evento_id' => 1, // Entero de 1-7 [int]
            'seccion'   => 'productos', // Tabla por afectar [string(30)]
            'row_id'    => 265, // Identificador de registro afectado (id de producto en este caso)  [int]
            'acciones'  => 'Descripcion de operacin realizada, en caso de error el error que devuelve el MPS' // [text]
        ];
```

-  Valores posibles para evento_id
    1.  Agregar                   
    2.  Actualizar                
    3.  Actualiza Precio y/o Stock
    4.  Eliminar                  
    5.  Error                     
    6.  Reconstruir               
    7.  Consultar   (Para Pedidos y Tickets)              
- Devuelve registro generado
- `public function addBitacora($data)`

#### Registro de Productos
- Recibe arreglo
```php
    [   
        'id'            => null, // Identificador de registro [int]
        'product_id'    => 12536, // Identificador de producto [int]
        'precio'        => 1000.00,// Precio de lista [decimal(10,2)]
        'oferta'        => 800.00, // Precio oferta [decimal(10,2)]
        'envio'         => 65.00,  // Precio de envío [decimal(10,2)]
        'market_sku'    => 'MLM25369822', // Identificador del MarketPlace [string(20)]
        'transaction_id'=> 0, // Identificador de registro entero [int]
        'referencia'    => 'https://market.com/id=145151', // Url del producto en el marketplace [string(200)]
        'estatus'       = 99 //[tinyint]
    ];
```
- Posibles estatus
```php
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96;     
    const ITEM_SIN_PUBLICAR = -1; 
```
- `public function addProducto($data)`

####  Obtiene el listado de productos de acuerdo al estatus
- `public function getProductos($estatus=-1, $id=0, $limit=100)`

#### Actualización de productos
- Solo agregue los campos que vaya a actualizar y el "id"
```php
    [
        'id'            => 1250360, // Identificador de registro [int]
        'product_id'    => 54361, // Identificador de producto [int]
        'precio'        => 1000.00,// Precio de lista [decimal(10,2)]
        'oferta'        => 800.00, // Precio oferta [decimal(10,2)]
        'envio'         => 65.00,  // Precio de envío [decimal(10,2)]
        'market_sku'    => 'MLM25369822', // Identificador del MarketPlace [string(20)]
        'transaction_id'=> 0, // Identificador de registro de MPS entero [int]
        'referencia'    => 'https://market.com/id=145151', // Url del producto en el marketplace [string(200)]
        'fulfillment'   => 0, //  1 en caso de ser fulfillment [tinyint]
        'estatus'       = 1 // [tinyint]
    ];
    // Posibles estatus
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96;     
    const ITEM_SIN_PUBLICAR = -1; 
```
- `public function updProducto($data)`

#### Eliminar producto 
- Elimina el registro del producto y del stock 
- Devuelve resumen de registros eliminados
- `public function delProducto($id)`

#### Registro de Stock
- Arreglo a enviar
```php
    [ 
    'id'            => null,           // Identificador de registro [int]
    'product_id'    => 1253,        // Identificador de producto [int]
    'sku'           => '58369-250', // Sku hijo [string(20)]
    'stock_id'      => 1263001,     // Identificador de variacion [int]
    'market_sku'    => '145236985221', // Identificador de registro en MPS [string(20)]
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS [string(20)]
    'stock'         => 1 // Stock registrado [int]
    ];
- `public function addStock($data)`
```

#### Actualización de Stock
```php
    /* Solo agregue los campos que vaya a actualizar y el "id" */
    'id'            => 125630,           // Identificador de registro [int]
    'market_sku'    => '145236985221', // Identificador de registro en MPS [string(20)]
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS [string(20)]
    'stock'         => 0 // Stock registrado [int]
    ];
```
- Regresa registro actualizado
- `public function updStock($data)`

#### Elimina el registro del stock 
- `public function delStock($id)`

#### Registro de imágenes
- Recibre arreglo
```php
      [ 'id'          => null,  // Identificador de registro [int]
        'product_id'  => 1235,  // Identificador de producto, [int]
        'sku'         => '1280',// Sku hijo SellerSku [string20]
        'orden'       => 1,     // Entero del 1 al 6 con el # de imagen [tinyint]
        'id_mkt'      => '',    // En caso de aplicar se llenara con el hash de la imagen en MarketPlace [string(30)]
        'url'         => 'http://myempresa.com/imagenes/1280-1.jpg' // [string(500)]
    ];
```
- Devuelve registro insertado
- `public function addImagen($data)`

#### Actualización de imágenes
- Recibe arreglo por actulizar
```php
   /* Agregue todos los campos para actualizar */
    [   'id'          => 513698,  // Identificador de registro [int]
        'id_mkt'      => '',    // En caso de aplicar se llenara con el hash de la imagen en MarketPlace [string(30)]
        'url'         => 'http://myempresa.com/imagenes/1280-1a.jpg' // [string(500)]
    ];
```
- Devuelve registro actualizado
- `public function updImagen($data)`

#### Elimina el registro del imagen 
- `public function delImagen($id)`

####  Agrega registro de guias
- Recibe arreglo
```php
    [
        'id' => null, //Identificador de registro [int]
        'pedido_id' => 25692, // Identificador de pedido [int]
        'label'     => '', // Contenifo binario en base64 de imagen o archivo de guia [text]
        'guia'      => '125368555', // Número de guía [string(50)]
        'mensajeria'=> 'Fedex', // Paquetería [string(30)]
        'estatus'   => 1 //[tinyint]
    ];
```
- Devuelve registro insertado
- `public function addGuia($data)`


#### Agrega registro de pedidos
- Recibe arreglo
```php
    ['id'   =>null,   //Identificador de registro [int]
    'referencia'=>,   // Pedido en el MarketPlace  [string(30)]
    'fecha_pedido'=>, // YYYY-MM-DD HH:mm:ss (Formato de 24 hrs)   fecha de creación del pedido [datetime]
    'fecha_autoriza'=>,   // YYYY-MM-DD HH:mm:ss (Formato de 24 hrs)   fecha de autorizacion del pedido [datetime]
    'email'=>  'none@mymarket.com',  //[string(50)] 
    'entregara'=> 'Ulises Rendon Martinez',    // Aquien se dirige el pedido [string(120)]
    'telefono'=> '',    // 81 5536-2589 [string(20)]
    'direccion'=> 'Fuentes Verdes # 1250', // [string(100)]
    'entrecalles'=> 'Cruz con Papagayos' ,    // [string(100)]
    'colonia'=>     'La esperancita',  // [string(100)]
    'ciudad'=>      'De Los Cabos',  // [string(100)]
    'estado'=>      'Baja California Sur', // [string(100)]
    'observacione'=>,  'Puerta Negra, segundo piso' // [string(200)]
    'cp'=>    '52896', // [string(5)]  Formato con ceros a la izquierda
    'envio'=> 95.50,  // Costo de envío en caso de que MPS venda la guia  [decimal(10,2)]
    'comision'=> 60.00,   // Cargo que realiza el MPS por servicio [decimal(10,2)]
    'estatus'=> 'OPEN', // Estatus tal cual viene en el MPS    [string(30)]
    'shipping_id'=> null,    // Aplica para los MPS q¡ue no agrupan los items en un solo pedido [string(20)]
    'detalle'=> [...],   // Arreglo con lineas de pedido por actualizar
    ]

    // Detalle de pedido
    ['sku' => '1258',    // Sku hijo, sellersku [string(20)]
    'descripcion'=> 'Pintura Berel Verde Esmaltado 4lts',   // Descripcion del producto [string(120)]
    'cantidad'=> 1,   // Cantidad vendida [int]
    'precio'=>  2500.00,   // Precio de venta sin iva [decimal(10,2)]
    'color'=> 'Verde',  // Color del producto o variedad [string(20)]
    'talla'=> '4 Lts',    // Medida de la variacion [string(10)]
    'referencia'=> '2536985441',    // identificador de registro en el MarketPlace [string(30)]
    'fulfillment'=> 0 // Si el producto se encuentra en fulfillment  [tinyint]
    ]

```
- `public function addPedidos($data)`
- Devuelve registro creado

####  Atualiza registro de pedido
- Recibe arreglo
```php
    [
        $id => 56822, // identificador de pedido [int]
        $estatus => 'PAID', // Estatus del marketplace [string(30)]
        $total => 0, // Monto en caso de devolución [decimal(10,2)]
        $pedido_mkt, // Cuando es e-commerce (Shopify) al grabar el pedido devuelve su identificador [bigint]
    ]
```
- Regresaimag registro actualizado
- `public function updPedido($id, $estatus, $total=0, $pedido_mkt=null) `  

#### Obtiene pedidos registrador
```php
    /* Regresa pedidos 
    POSIBLES FILTROS
    0 // Todos Los últimos $limit registros en orden descendente por fecha de actualización
    PEDIDO_NOREPORTADO // Pedidos que no han sido reportados al e-commerce para si generación
    PEDIDO_CAMBIO  // Pedidos que actualizaron el 
    */
```
- `public function getPedidos($filtro=0,$limit=50)`

#### Registra feed en caso  de procesamiento en lote 
```php
    [
        'id' => null, // identificador de registro [int]
        'feed' => '2535-25352-2533', // Identificador de registro en MPS [string(50)]
        'request' => '<xml>....</xml>' // Texto con peticion [text]
    ]
```
- Regresa registro insertado
- ` public function addFeed($data) `


#### Actualiza feed con respuesta en caso  de procesamiento en lote 
```php
    [
        'id' => null, // identificador de registro [int]
        'answer' => '<xml>....</xml>' // Texto con peticion [text]
    ]
```
- Regresa registro actualizado
- `public function updFeed($id, $answer)`

#### Obtiene feeds que aun no han sido marcados como concluidos 
- `public function getFeeds($limit=50)`
- Devuelve listado de feeds sin actualizar
