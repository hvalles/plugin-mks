<?php

class Item {

    public $id; 
    public $nombre;
    public $descripcion; // Descripcion corta
    public $ficha;  // Ficha técnica
    
    public $alto; // alto del producto
    public $ancho; // ancho del producto
    public $largo; // largo del producto
    public $peso; //peso del producto

    public $sku; // El sku padre, cuando el padre no tiene que ser un hijo
    public $dias_embarque; // dias en los que se embarca revisar mñinimo del MPS
    public $categoria; // Categoria de MarketSync
    public $filtro; // Solo aplica para Claro
    public $marca; 
    public $etiquetas; // filtros de busqueda en el  mps
    public $modelo;    

    // Elementos Externos
    public $atributos = []; // Atributios del producto
    public $variaciones = [];
    public $producto_market = null; // Registro de  parent sku por market en Marketsync
    public $categoria_mkt; // Categoria del MarketPlace
    public $ruta; // Ruta de categoria
    public $precios;  // Precio del producto en el Market
    public $variacion_tipo; // SIZE, COLOR, PATTERN, ETC.
    public $variacion_atr; // ATRIBUTO QUE DETERMINA LA VARIACION 

    // Elementos Adicionales
    public $p_condition; // Por ahora solamente nuevos
    public $warranty;  // Garantía     
    public $date_created;
    public $parent_sku;  // En caso de que algun hijo deba de ser el padre
    public $nombre_modelo; // Nombre comercial del modelo
    public $palto; // Alto del paquete
    public $pancho; // Ancho del paquete
    public $plargo; // Largo del paquete
    public $ppeso; // Peso del paquete
    public $origen; // País de origen No.
    public $pais; // País de origen nombre
    public $pais_es; // País de origen nombre espa
    public $code2; // Código de pais de origen Ej US
    public $code3; // Código de pais de origen 3 caracteres Ej USA
    public $etiquetas_web; // Filtros de e-commerce (Shopify)
    public $taxcode; // Código de producto del SAT
    public $iva: // Iva del peoducto
    
    // Variables privadas
    private $_genero = null;
    private $_edad_minima = null;

    public function getAtributo($atributo) {
        foreach ($this->atributos as $atr) {
            if ($atr->atributo_id == $atributo) return $atr;
        }
        return FALSE;
    }

    // male / female / unisex
    public function  genero() {
        if ($this->_genero === FALSE || $this->_genero) return $this->_genero;
        $cat = strtoupper($this->getAtributo('GENDER'));
        if ($cat) {
            if (strpos($cat,'HOMBRE')) $this->_genero = 'male';
            if (strpos($cat,'NIÑO')) $this->_genero = 'male';
            if (strpos($cat,'MUJER')) $this->_genero = 'female';
            if (strpos($cat,'NIÑA')) $this->_genero = 'male';
            if (strpos($cat,'UNISEX')) $this->_genero = 'unisex';
            if (strpos($cat,'BEB')) $this->_genero = 'unisex';
            if (strpos($cat,'SIN G')) $this->_genero = 'unisex';
            if (strpos($cat,'N/A')) $this->_genero = 'unisex';
            if (!$this->_genero) $this->_genero = FALSE;
            return $this->_genero;
        }

        $cat = strtoupper($this->categoria);
        if (strpos($cat,'HOMBRE')) $this->_genero = 'male';
        if (strpos($cat,'NIÑO')) $this->_genero = 'male';
        if (strpos($cat,'MUJER')) $this->_genero = 'female';
        if (strpos($cat,'NIÑA')) $this->_genero = 'female';
        if (strpos($cat,'UNISEX')) $this->_genero = 'unisex';
        $cat = strtoupper($this->etiquetas);
        if (strpos($cat,'HOMBRE')) $this->_genero = 'male';
        if (strpos($cat,'NIÑO')) $this->_genero = 'male';
        if (strpos($cat,'MUJER')) $this->_genero = 'female';
        if (strpos($cat,'NIÑA')) $this->_genero = 'female';
        if (strpos($cat,'UNISEX')) $this->_genero = 'unisex';

        if (!$this->_genero) $this->_genero = FALSE;
        return $this->_genero;
    }

    public function edad_minima() {
        if ($this->_edad_minima===FALSE || $this->_edad_minima) return $this->_edad_minima;
        $edad = $this->getAtributo('MINIMUM_AGE_RECOMMENDED');
        if ($edad===FALSE) $edad = $this->getAtributo('MIN_RECOMMENDED_AGE');
        if ($edad) {
            $this->_edad_minima = $edad;
            return $this->_edad_minima;
        }

        // Se espera encontrar una eqiqueta como edad_3a o edad_12m 
        $ets = explode(',',$this->etiquetas);
        foreach ($ets as $e) {
            if (substr($e,0,5)=='edad_') {
                $edad = str_replace('edad_','',$e);
                if (substr($edad,-1)=='m') {
                    $edad = str_replace('m','', $edad) . ' meses'; 
                } else {
                    $edad = str_replace('a','', $edad) . ' años'; 
                }
                $this->_edad_minima = $edad;
                return $this->_edad_minima;
            }
        }

        $this->_edad_minima = FALSE;
        return $this->_edad_minima;        
    }

}


?>