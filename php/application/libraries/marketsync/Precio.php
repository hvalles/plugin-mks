<?php

class Precio {
    
    // Datos actuales
    public $market;    
    public $sku;   // Sku padre
    public $precio; 
    public $oferta;
    public $envio;

    // Datos previos registrador
    public $previo_precio;
    public $previo_oferta;
    public $previo_envio;
}

?>