<?php
class Stock {


    public $id; // Identificador de Registro
    public $sku; // String(20) revisar implicaciones de longitud con respecto al MPS
    public $product_id; // $identificador de producto
    public $stock; // Entero con Stock disponible
    public $base; // Color Base de la Variación
    public $color; // Color nombrado por el Seller
    public $color_market; // Color identificado en el Market

    // Externos
    public $atributos = []; // Atributos de Variacion
    public $imagenes = []; // Imagenes de la Variacion 
    public $bullets = []; // Bullets de la Variacion 
    public $videos = []; // Videos de la Variacion
    public $upc;
    public $ean;
    
    // Los siguientes elementos corresponden al registro del stock del MPS
    public $id_mk;      // Identificador de registro 
    public $sku_mk;     // Sku del hijo registrado en MPS 
    public $stock_mk;   // Stock registrado en MPS
    public $market_sku; // Identificador de registro en MPS
    public $referencia; // Identificador auciliar de registro en MPS

}

?>