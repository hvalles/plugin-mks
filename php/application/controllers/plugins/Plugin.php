<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// En sandbox y producción los datos se recuperaran de la base de datos,
// para poder hacer las pruebas locales se simula las onsultas y regresa objetos PHP

class Plugin extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->mainModel = 'settings_m';
        $this->plugin = get_called_class();
        $this->data['title'] = "Componente ".$this->plugin;

        // Send data previously stored
        $this->data['before'] = null;
        $this->keys = [];
        // modificar con su servidor y puerto
        $this->server_api = 'http://sandbox.marketsync.mx/';

        $this->data['plugin'] = $this->loadPlugin();
        $this->data['settings'] = $this->loadSettings();
        $this->data['url_register'] = '/plugins/' .strtolower($this->plugin) . '/register' ;
        $this->data['url_run'] = '/plugins/' .strtolower($this->plugin) . '/run' ;
    }

    // Incluir cualquier otro parametro que desee almacenar
    // y recuperar para la operación del componente
    private function loadSettings() {
        $value = array(); // Llenar el arreglo con configuraciones requeridas
        if (!$value)  return [];
        return json_decode($value);
    }


    public function callAPI($url, $method="GET", $parameter=[], $data=[]) {
        if (!$this->keys) $this->getKeys();
        $PRIVATE_KEY = $this->keys['private'];
        $TOKEN = $this->keys['token'];
       
        # Set initial parameters
        $parameters = [];
        $parameters['token'] = $TOKEN;
        $parameters['timestamp'] = substr(date(DATE_ATOM),0,19); # YYYY-MM-DDTHH:mm:ss
        $parameters['version'] = '1.0';
        
        # You may add others parameters here
        foreach ($parameter as $key => $value) {
            $parameters[$key] = $value;
        }
        
        ksort($parameters);
        // URL encode the parameters.
        $encoded = array();
        foreach ($parameters as $name => $value) {
            $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        // Concatenate the sorted and URL encoded parameters into a string.
        $concatenated = implode('&', $encoded);
        
        $sign = rawurlencode(hash_hmac('sha256', $concatenated, $PRIVATE_KEY, false));
        $url = $url . '?' . $concatenated . '&signature=' . $sign;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if ($data)
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        error_log($url);
        $response = curl_exec($ch);
        
        if (!$response) {
            error_log(str(curl_error ( $ch )));
            curl_close($ch);
            return FALSE;
        }
        curl_close($ch);
        return json_decode($response);
    }

    // Agregue sus llaves en esta funcion
    // En produccion serán obtenidas de la base de datos
    private function getKeys() {
        if (!$this->keys) {
            $this->keys['token'] = ''; // YOUR TOKEN
            $this->keys['private'] = ''; // YOUR PRIVATE KEY;
        }
        return $this->keys;
    }

    // Se envía arreglo que se codificara a json antes de grabar
    // los parametros de loadSettings, se incluyen por default
    // Fuincion Dummy 
    public function saveSettings($data) {
        return TRUE;
    }

    // Funcion Dummy para recuperar componente
    // En produccion la informacion viene de la base de datos
    // Reemplace con sus propios datos
    private function loadPlugin() {
        $row = new StdClass();
        $row->plugin_id = 1000;
        $row->nombre = "FirmaFicha";
        $row->autor = "Marketsync"; 
        $row->descripcion = "Agregar contenido por MarketPlace  a todas las publicaciones, en el final de la ficha de producto."; 
        $row->image="/static/img/imagenes/no-image.png";
        $row->instrucciones="
        Seleccione el MarketPlace y capture la descripción genérica a publicar, posteriormente presione \"Publicar\" para actualizar todos los productos.
        <script>
        $( document ).ready(function() {
        $(\"select[name='market']\").on('change', function() {
          
            $.getJSON( '/plugins/firmaficha/show/'+this.value, function( data ) {
              if (data) {
                  $(\"textarea[name='firma']\").val(data.valor);
              } else {
                $(\"textarea[name='firma']\").val(''Nada'');
              }
            });
        });
        });
        </script>        
        ";
        $row->campos="";
        $row->campos_registro=""; 
        $row->categoria = "Publicar"; 
        $row->activo=1; 
        $row->vista='';
        $row->vista_registro='';
        return $row;
    }

    // Se corre esta rutina al iniciar la ejecución
    protected function pre_run() {
        return TRUE;
    }

    public function index() {

        // Se comporta como si ya estuviera instalado
        // Al agregar datos en loadSettings
        if (!$this->data['settings']) {
            $this->setView('plugins/register',$this->data);
            return;
        }
        $this->pre_run();
        if ($this->data['plugin']->vista) {
            $this->setView('plugins/'.$this->data['plugin']->vista,$this->data);
        } else {
            $this->setView('plugins/plugin',$this->data);
        }
        return;
    }

    // Esta funcion se llamara cuando el usuario acepte instalar el componente.
    // En caso de sobreescribir llamar al parent
    // parent::register();
    protected function register() {
        // Graba settings
        $data = $this->data['settings'];
        if (!is_array($data)) $data = (array)$data;
        $this->saveSettings($data);
        return $this->index();
    }

    // Esta es la rutina de ejecución del componente
    protected function run() {
        // Realizar logica del componente

        return $this->salida(["result" =>"Operacion realizada."]);

        // Deseo enviar un error
        // Debes de capturar el error en Ajax y reportarlo
        // return $this->salida(["error" =>"Algo salio mal"], 404);
    }

    // Rutina de desinstalacion
    // En caso de sobreescribir llamar al parent
    // parent::uninstall();

    public function uninstall() {
        return $this->salida(["result" =>"El componente ha sido desinstalado."]);
    }


    /* -------------------------------------------------- */
    // Secion de funcionalidad particular del componente
    // Puedes declarar tus propias funciones y vistas a llamar a partir de aquí
    

}
