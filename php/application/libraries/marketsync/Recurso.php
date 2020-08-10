<?php

class Recurso {
    
    const IMAGEN = 1;
    const BULLET = 4;
    public $id; 
    public $sku;
    public $orden;
    public $url;
    public $recurso_id; // Tipo de Recurso

    // Externos
    public $previo_id = null;
    public $previo_url = FALSE;
    public $previo_name = FALSE;
    

    public function getNombre() {
        if ($recurso_id == Recurso::IMAGEN) return "Imagen";
        if ($recurso_id == Recurso::BULLET) return "Bullet";

        return "";
    }

}

?>