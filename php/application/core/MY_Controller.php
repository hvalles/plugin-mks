<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 */

 class MY_Controller extends CI_Controller {
    public $data = array();

    public $editView = 'admin/common/edit';
    public $addView =  'admin/common/add';

	public function __construct() 	{
        parent::__construct();
        $this->load->helper('autoform');
    }


   // Routing functions
    public function index()	{
        $this->getIndex('Compomnente de ...');
        $this->setView($this->editView);
    }   

    public function edit($id) {
        // Sobreescribir logica de agregado en plugin
        $this->setView($this->editView);
    }

    public function add() {
        // Sobreescribir logica de agregado en plugin
        $this->setView($this->addView);
    }

    // Salida en JSON
    protected function salida($data, $status=200) {
        header('Content-Type: application/json');
        header("Content-Type: text/html;charset=utf-8");
        http_response_code($status);
        echo json_encode($data);
    }


    protected function setView($view) {
        $this->load->view($view, $this->data);
    }

}

?>