<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'controllers/plugins/Plugin.php');

// En sandbox y producción los datos se recuperaran de la base de datos,
// para poder hacer las pruebas locales se simula las onsultas y regresa objetos PHP

class Firmaficha extends Plugin {


    public function register() {
        parent::register();
    }


    // Rutina de inicialización antes de mostrar vista
    protected function pre_run() {
        // Realizar logica del componente
        // El built-in server de PHP no permite realizar 2 llamadas simultaneas
        // Debes tener 2 procesos en ejecución
        $url = site_url("api/markets");
        //$url=str_replace(':5000',':4000',$url);
        $markets = $this->callAPI($url);        
        $cadena = '0__-- Seleccione una opción --|';
        foreach ($markets->answer as $m) {
            $cadena .= $m->id."__".$m->market."|";
        }
        if ($cadena) $cadena = substr($cadena,0,strlen($cadena)-1);
        $campos = $this->data['plugin']->campos;        
        $this->data['plugin']->campos = str_replace('{markets}', $cadena, $campos);
        return TRUE;
    }

    // Esta es la rutina de ejecución del componente
    public function run() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $market = (int)$this->input->post('market'); 
            if ($market)
            //$firma = $this->db->escape($this->input->post('firma'));
            $firma = $this->input->post('firma');
            $this->load->model('settings_m');
            $this->settings_m->setValue($this->session->sitio, $market, 'production', 'descripcion', $firma);
            $this->saveSettings((array)$this->data['settings']);
            $this->session->set_flashdata('success', "La acción " . $this->plugin .' ha sido realizada con éxito');           
        }

        return $this->index();

        // Deseo enviar un error
        // Debes de capturar el error en Ajax y reportarlo
        // return $this->salida(["error" =>"Algo salio mal"], 404);
    }

    /* -------------------------------------------------- */
    // Secion de funcionalidad particular del componente
    // Puedes declarar tus propias funciones y vistas a llamar a partir de aquí
    public function show($market) {
        $url = site_url("api/settings");
        //$url=str_replace(':5000',':4000',$url);
        $parameters = array(
            "market" => (int)$market,
            "grupo"  => 'production',
            'clave'  => 'descripcion'
        );
        $markets = (array)$this->callAPI($url, "GET", $parameters);       
        
        if ($markets) return $this->salida($markets['answer'][0]);
        return "[]";
    }


}
