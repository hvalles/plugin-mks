
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
public $requiere_marca = FALSE; // Si es TRUE, los productos que no cuenten con el empate de marcas se excluiran
public $requiere_color = FALSE; // Si es TRUE, las variaciones que no cuenten con el empate de color se excluiran
public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno
```

-  Devuelve el registro de campos a configurar 
    - `public function getConfig()  `
- Establece la configuración inicial
    - `public function setConfig($data) `

#### Registro de marcas del MPS
- Se espera arreglo de arreglos con no más de 50 elementos por vez
```php        $marca = [[
                'marca' => "nombre de la marca",
                'marca_market' => "101253" // Identificador de marca o lo mismo que marca de no existir
            ]
        ];
```
- `public function addMarcas($data)`
  
#### Registro de colores
- Se espera arreglo de arreglos con no más de 50 elementos por vez
 ```php
    $color = [[
        'color_base' => "nombre de color",
        'color_market' => "101253" // Identificador de color
        ];
```
- `public function addColores($data)`

#### Registro de categorias
-  Recibe un arreglo con la categoria a dar de alta y regresa la categoria registrada.
```php
        [   'id' => null,
            'categoria' => "Clave de Categoria en Market",
            'nombre'    => "Nombre de la categoria",
            'ruta'      => "/Abuelo/Padre/Hijo", # Arbol de categoria separado por Slash
            'padre'     => "Clave de la categoria padre
        ];
```
- `public function addCategoria($data)`

#### Registro de atributos de categoría
- Recibe arreglo con atributo
```php
        $atr = [
            'id'           => null, // Identificador de atributo
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
```
- Devuelve el registro generado
- `public function addAtributo($data)`

#### Registro de valores de atributo
- Recibe arreglo de valor 
```php
        $valor = [
            'id'            => null, // Identificador de registro
            'key_id'        => 125, // Identificador de atributo 
            'clasificacion' =>  'valor', // etiqueta,unidad,valor
            'clave'         =>  '1253', // identificador del market para el valor  
            'valor'         =>  'Hombre' // Valor de lista
        ];
```
- Devuelve el registro generado
- `public function addValor($data)`

#### Registro de Bitácora
- Recibe arreglo
```php        [
            'id'        => null, // identificador de registro
            'evento_id' => 1, // Entero de 1-7
            'seccion'   => 'productos', // Tabla por afectar
            'row_id'    => 265, // Identificador de registro afectado (id de producto en este caso)  
            'acciones'  => 'Descripcion de operacin realizada, en caso de error el error que devuelve el MPS'
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
        'id'            => null, // Identificador de registro
        'product_id'    => 12536, // Identificador de producto
        'precio'        => 1000.00,// Precio de lista
        'oferta'        => 800.00, // Precio oferta
        'envio'         => 65.00,  // Precio de envío
        'market_sku'    => 'MLM25369822', // Identificador del MarketPlace
        'transaction_id'=> 0, // Identificador de registro entero
        'referencia'    => 'https://market.com/id=145151', // Url del producto en el marketplace
        'estatus'       = 99
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
- `public function getProductos($estatus=-1, $id=0)`

#### Actualización de productos
- Solo agregue los campos que vaya a actualizar y el "id"
```php
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
```
- `public function updProducto($data)`

#### Registro de Stock
- Arreglo a enviar
```php
    [ 
    'id'            => null,           // Identificador de registro
    'product_id'    => 1253,        // Identificador de producto
    'sku'           => '58369-250', // Sku hijo
    'stock_id'      => 1263001,     // Identificador de variacion
    'market_sku'    => '145236985221', // Identificador de registro en MPS
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS
    'stock'         => 1 // Stock registrado
    ];
- `public function addStock($data)`
```

#### Actualización de Stock
```php
    /* Solo agregue los campos que vaya a actualizar y el "id" */
    'id'            => 125630,           // Identificador de registro
    'market_sku'    => '145236985221', // Identificador de registro en MPS
    'referencia'    => '14521411141',  // Identificador de registro auxiliar en MPS
    'stock'         => 0 // Stock registrado
    ];
```
- Regresa registro actualizado
- `public function updStock($data)`

#### Registro de imágenes
- Revibre arreglo
```php
      [ 'id'          => null,  // Identificador de registro
        'product_id'  => 1235,  // Identificador de producto,
        'sku'         => '1280',// Sku hijo SellerSku
        'orden'       => 1,     // Entero del 1 al 6 con el # de imagen
        'id_mkt'      => '',    // En caso de aplicar se llenara con el hash de la imagen en MarketPlace
        'url'         => 'http://myempresa.com/imagenes/1280-1.jpg'
    ];
```
- Devuelve registro insertado
- `public function addImagen($data)`

#### Actualización de imágenes
- Revibe arreglo por actulizar
```php
   /* Agregue todos los campos para actualizar */
    [   'id'          => 513698,  // Identificador de registro
        'id_mkt'      => '',    // En caso de aplicar se llenara con el hash de la imagen en MarketPlace
        'url'         => 'http://myempresa.com/imagenes/1280-1a.jpg'
    ];
```
- Devuelve registro actualizado
- `public function updImagen($data)`