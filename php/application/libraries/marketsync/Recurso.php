<?php

class Recurso {
    
    const IMAGEN = 1;
    const BULLET = 4;
    const VIDEO = 2;
    public $id;    // identificafor de registro
    public $sku;   // Sku hijo 
    public $orden;
    public $url;
    public $recurso_id; // Tipo de Recurso

    // Externos
    public $previo_id = null;
    public $previo_url = FALSE;
    public $previo_name = FALSE;

    public function getNombre() {
        if ($this->recurso_id == Recurso::IMAGEN) return "Imagen";
        if ($this->recurso_id == Recurso::BULLET) return "Bullet";
        if ($this->recurso_id == Recurso::VIDEO) return "Video";
        return "";
    }

}

?>