<?php


class Auxiliar {

    public function __construct() 	{
        parent::__construct();
    }

    public $cliente = 0;
    public $market = 0;
    public $acepta_html = FALSE;
    public $requiere_upc = FALSE; // Si es TRUE, las variaciones que no cuenten con UPC se excluirán.
    public $procesa_lotes = FALSE; // Si el MPS procesa la alta de productos en lotes o de uno en uno

    public function getConfig() {
        return array(
            'cliente' => $this->cliente,
            'market' => $this->market,
            'acepta_html' => $this->acepta_html,
            'requiere_upc' => $this->requiere_upc,
            'procesa_lotes' => $this->lotes
        );
    }

    public function setConfig($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    


}




?>