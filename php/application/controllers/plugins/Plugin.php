<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// En sandbox y producción los datos se recuperaran de la base de datos,
// para poder hacer las pruebas locales se simula las onsultas y regresa objetos PHP

class Plugin extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->plugin = get_called_class();
        $this->data['plugin'] = $this->loadPlugin();
        $this->data['settings'] = $this->loadSettings();
    }

    // Incluir cualquier otro parametro que desee almacenar
    // y recuperar para la operación del componente
    private function loadSettings() {
         return array(
            'habilitado'=>date("Y-m-d H:i:s", time()), 
            'actualizado'=>date("Y-m-d H:i:s", time()), 
            'usuario' => 1234);
    }

    // Se envía arreglo que se codificara a json antes de grabar
    // los parametros de loadSettings, se incluyen por default
    public function saveSettings($data) {
        if ($data && is_array($data)) {
            $j = json_encode($data);
            return TRUE;
        }            

        return FALSE;
    }

    // Datos que hará llega para registrar el componente
    private function loadPlugin() {
        $row = new StdClass();
        $row->plugin_id = 0;
        $row->nombre = "Plugin de Prueba";
        $row->autor = "Mi Empresa";
        $row->descripcion = "Este plugin es un plugin de prueba";
        $row->imagen = "static/img/imagenes/no-image.png";
        $row->activo = 1;
        $row->vista = ''; // En caso de tener una vista el nombre sin extensión se registra aquí.
        $row->instrucciones = "<p>Favor de dar clic en Aceptar para la instalación del componente</p>";

        return row;
    }

    // Se comporta como si ya estuvira instalado
    public function index() {
        if ($this->data['plugin']->vista) {
            $this->setView('plugins/'.$this->data['plugin']->vista,$this->data);
        } else {
            $this->setView('plugins/plugin',$this->data);
        }
    }

    // Esta funcion se llamara cuando el usuario acepte instalar el componente.
    // En caso de sobreescribir llamar al parent
    // parent::register();
    protected function register() {
        // Graba settings
        $this->saveSettings($this->data['settings']);
        return $this->run();
    }

    // Esta es la rutina de ejecución del componente
    protected function run() {
        
        // Realizar logica del componente

        return $this->salida(["result" =>"El componente esta en ejecución"]);

        // Deseo enviar un error
        // Debes de capturar el error en Ajax y reportarlo
        // return $this->salida(["error" =>"Algo salio mal"], 404);
    }

    // Rutina de desinstalacion
    // En caso de sobreescribir llamar al parent
    // parent::uninstall();

    protected function uninstall() {
        return $this->salida(["result" =>"El componente ha sido desinstalado."]);
    }


    /* -------------------------------------------------- */
    // Secion de funcionalidad particular del componente
    // Puedes declarar tus propias funciones y vistas a llamar a partir de aquí
    public function show($id) {

    }


}
