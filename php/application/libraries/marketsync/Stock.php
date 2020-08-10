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
    public $recursos = []; // Recursos de la Variacion
    public $upc;
    public $ean;

}

?>