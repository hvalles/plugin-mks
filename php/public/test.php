<?php

require_once(__DIR__.'/../application/libraries/Auxiliar.php');

class Test  {

    private function configura() {
        $data = [
            'cliente' =>2011, // CLIENTE DE DEMOSTRACIÓN
            'market' => 0, // INSERTAR MARKETID 
            'server' =>   'http://sandbox.marketsync.mx/mks/',   //INSERTAR WEB SERVER termina en "/"
            'publica'=> '', // INSERTAR LLAVE PUBLICA
            'privada' => '', // INSERTAR LLAVE PRIVADA
            'requiere_upc' => TRUE,         // DEPENDE DEL MPS
            'requiere_marca' => FALSE,      // DEPENDE DEL MPS
            'requiere_color' => TRUE,       // DEPENDE DEL MPS
            'requiere_categoria' => TRUE,   // DEPENDE DEL MPS
            'procesa_lotes' => FALSE        // DEPENDE DEL MPS
        ];
        $this->auxiliar->setConfig($data);
        //var_dump($this->auxiliar->getConfig());
    }

    private function settings() {
        $data = $this->auxiliar->getSetting();
        if ($data) {
            $id=0;
            foreach ($data as $r) {
                if ($r->clave=='config') $id = $r->id;
            }
            if ($id) {
                $data = ['id'=>$id,'valor'=>['seller_id'=>time(), 'token'=>'901ec85595bf7f5d']];
                print "updSettings...".PHP_EOL;
                var_dump($this->auxiliar->updSetting($data)); 
                //var_dump($this->auxiliar->delSetting($id));
            } 
        } else {
            $data = ['id'=>null,'valor'=>['url'=>'https://osom.com/api/']];
            print "addSettings...".PHP_EOL;
            var_dump($this->auxiliar->addSetting($data, TRUE));
            $data = ['id'=>null,'valor'=>['seller_id'=>'1256874', 'token'=>'901ec85595bf7f5d']];
            print "addSettings...".PHP_EOL;
            var_dump($this->auxiliar->addSetting($data));
        }
    }

    private function marcas() {
        print __METHOD__.PHP_EOL;
        $data = [
            ['marca'=>"Emilio Bazan",'marca_market'=>"Emilio Bazan"],
            ['marca'=>"RedBerry",'marca_market'=>"RedBerry"],
            ['marca'=>"Dummy",'marca_market'=>"Dummy"],
            ['marca'=>"Rafael Ferrigno",'marca_market'=>"Rafael Ferrigno"],
            ['marca'=>"Dummy",'marca_market'=>""],
        ];
        print "addMarcas...".PHP_EOL;
        var_dump($this->auxiliar->addMarcas($data));
    }

    private function colores() {
        $data = [
            ['color_base'=>"Rojo",'color_market'=>"Red"],
            ['color_base'=>"Azul",'color_market'=>"Blue"],
            ['color_base'=>"Dummy",'color_market'=>"Dummy"],
            ['color_base'=>"Verde",'color_market'=>"Green"],
            ['color_base'=>"Dummy",'color_market'=>""],
        ];
        print "addColores...".PHP_EOL;
        var_dump($this->auxiliar->addColores($data));
    }

    private function categorias() {
        $last = 0;
        $cat = '';
        $rows = $this->auxiliar->getCategoria();
        foreach ($rows as $c) {
            print $c->id .' '.$c->categoria .' '. $c->nombre.PHP_EOL;    
            $last = $c->id;
            if ($c->categoria=='000000000') $cat = $c->id;
        }
        if (!$rows) {
            $data = ['id'=>Null,'categoria'=>"015000000", 'nombre'=>"Bolsas Y Calzado",
            'ruta'=>"/BOLSAS_Y_CALZADO", 'padre'=>0];
            $res = $this->auxiliar->addCategoria($data);
            print $res[0]->id .' '.$res[0]->categoria . $res[0]->nombre.' '.PHP_EOL;

            $data = ['id'=>Null,'categoria'=>"015000001", 'nombre'=>"Calzado Dama",
                'ruta'=>"/BOLSAS_Y_CALZADO/CALZADO_DAMA", 'padre'=>$res[0]->id];
            $res = $this->auxiliar->addCategoria($data);
            print $res[0]->id .' '.$res[0]->categoria . $res[0]->nombre.' '.PHP_EOL;

            $data = ['id'=>Null,'categoria'=>"015000010", 'nombre'=>"Zapatillas",
                'ruta'=>"/BOLSAS_Y_CALZADO/CALZADO_DAMA/ZAPATILLAS", 'padre'=>$res[0]->id];
            $res = $this->auxiliar->addCategoria($data);
            print $res[0]->id .' '.$res[0]->categoria . $res[0]->nombre.' '.PHP_EOL;

        } else {

            if (!$cat) {
                $data = ['id'=>Null,'categoria'=>"000000000", 'nombre'=>"Dummy",
                'ruta'=>"/DUMMY", 'padre'=>0];
                $res = $this->auxiliar->addCategoria($data);
                print $res[0]->id .' '.$res[0]->categoria . $res[0]->nombre.' '.PHP_EOL;  
                $last =  $res[0]->id;
            }

            $data = ['id'=>$last,'categoria'=>"000000000", 'nombre'=>"Dummy4delete",
            'ruta'=>"/DUMMY", 'padre'=>0];
            $res = $this->auxiliar->updCategoria($data);
            print $res[0]->id .' '.$res[0]->categoria . $res[0]->nombre.' '.PHP_EOL;

            $res = $this->auxiliar->delCategoria($last);
            var_dump($res);

        }

    }

    private function atributos() {
        $cat = 0;
        $rows = $this->auxiliar->getCategoria();
        foreach ($rows as $c) {
            $last = $c->id;
            if ($c->categoria=='015000010') $cat = $c;
        }

        if ($cat) {
            print "Atributos".PHP_EOL;
            $rows = $this->auxiliar->getAtributo($cat->categoria);
            print $cat->id." ".$cat->nombre.PHP_EOL;
            foreach ($rows as $r) {
                print $r->id.' '.$r->atributo.PHP_EOL;
            }

            $data = ['id' =>null, 
                'categoria_id'=> $cat->id, 
                'atributo'=>'NAME', 
                'orden'=>1, 
                'nombre'=>'Nombre', 
                'mandatorio'=>1, 
                'tipo_valor'=>'string', 
                'tipo_long_max'=>120, 
                'variante'=>0, 
                'mapa'=>'NAME'
            ];

            $res = $this->auxiliar->addAtributo($data);
            print $res[0]->id .' '. $res[0]->nombre.' '.PHP_EOL;   

            $data = ['id' =>null, 
                'categoria_id'=> $cat->id, 
                'atributo'=>'COLOR', 
                'orden'=>2, 
                'nombre'=>'Color', 
                'mandatorio'=>1, 
                'tipo_valor'=>'list', 
                'tipo_long_max'=>0, 
                'variante'=>1, 
                'mapa'=>'COLOR'
            ];

            $res = $this->auxiliar->addAtributo($data);
            print $res[0]->id .' '. $res[0]->nombre.' '.PHP_EOL;   

            $data = ['id' =>null, 
                'categoria_id'=> $cat->id, 
                'atributo'=>'DUMMY', 
                'orden'=>3, 
                'nombre'=>'Dummy', 
                'mandatorio'=>1, 
                'tipo_valor'=>'list', 
                'tipo_long_max'=>0, 
                'variante'=>1, 
                'mapa'=>''
            ];

            $res = $this->auxiliar->addAtributo($data);
            print $res[0]->id .' '. $res[0]->nombre.' '.PHP_EOL;   
            $res = $this->auxiliar->delAtributo($res[0]->id);
            var_dump($res);
        }
    }

    private function valores() {
        $atr = '';
        $rows = $this->auxiliar->getAtributo('015000010', 'COLOR');
        $atr = $rows[0];

        $data = ['id' =>null, 
            'key_id'=> $atr->id, 
            'clasificacion'=>'valor', 
            'clave'=>'RED', 
            'valor'=>'Rojo' 
        ];

        $res = $this->auxiliar->addValor($data);
        print $res[0]->id .' '. $res[0]->clave.' '.PHP_EOL;   

        $data['clave'] = 'Blue';
        $data['valor'] = 'Azul';
        $res = $this->auxiliar->addValor($data);
        print $res[0]->id .' '. $res[0]->clave.' '.PHP_EOL;   

        $data['clave'] = 'Dummy';
        $data['valor'] = 'Dummy';
        $res = $this->auxiliar->addValor($data);
        print $res[0]->id .' '. $res[0]->clave.' '.PHP_EOL;   

        $res = $this->auxiliar->delValor($res[0]->id);
        var_dump($res);
    }

    private function bitacoras() {

        $data = [
            'id' => null,
            'evento_id' => 5,
            'seccion' => 'test',
            'row_id' => 0,
            'acciones' => 'Evento de prueba'
        ];
        var_dump($this->auxiliar->addBitacora($data));

    }


    private function productos() {
        $items = $this->auxiliar->getProductos(0,176532);
        
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            if ($item->market) {
                $data = [
                    'id' =>$item->market->id,
                    'product_id' => $item->id,
                    'precio' => 870.0,
                    'oferta' => 870.0,
                    'envio'  => 50,
                    'market_sku' => $item->sku,
                    'transaction_id' => 0,
                    'referencia' => '',
                    'estatus' => 1 
                ];
                var_dump($this->auxiliar->updProducto($data));
            } else {
                $data = [
                    'id' =>null,
                    'product_id' => $item->id,
                    'precio' => $item->precios->precio,
                    'oferta' => $item->precios->oferta,
                    'envio'  => $item->precios->envio,
                    'market_sku' => $item->sku,
                    'transaction_id' => 0,
                    'referencia' => '',
                    'estatus' => 0 
                ];
    
                var_dump($this->auxiliar->addProducto($data));
            }
            $res = null;
            foreach ($item->variaciones as $v) {
                print $v->id.' '.$v->sku.' '.$v->stock.' '.$v->id_mk.' '.$v->color_market.PHP_EOL;
                if ($v->id_mk) {
                    $data = [
                        'id' => $v->id_mk,
                        'market_sku' => '',
                        'referencia' => '',
                        'stock' => 10,
                    ];
                    $res = $this->auxiliar->updStock($data);
                    #var_export($this->auxiliar->delStock($v->id_mk));
                } else {
                    $data = [
                        'id' => null,
                        'product_id' => $item->id,
                        'sku' => $v->sku,
                        'stock_id' => $v->id,
                        'market_sku' => '',
                        'referencia' => '',
                        'stock' => $v->stock,
                    ];
                    $res = $this->auxiliar->addStock($data);
                }
            }
            var_export($res);
            // Imagenes
            $last = 0;
            foreach ($v->imagenes as $i) {
                if (!$i->previo_id) {
                    $data = [
                        'id' => null,
                        'product_id' => $item->id,
                        'sku'        => $i->sku,
                        'orden'      => $i->orden,
                        'id_mkt'     => substr(md5($i->url),0,30),
                        'url'        => $i->url,
                    ];
                    var_export($this->auxiliar->addImagen($data));
                } else {
                    $last = $i->previo_id;
                    $data = [
                        'id' => $i->previo_id,
                        'id_mkt'     => substr(md5($i->url.time()),0,30),
                        'url'        => $i->url,
                    ];
                    var_export($this->auxiliar->updImagen($data));
                }
            }
            if ($last) var_export($this->auxiliar->delImagen($last));
        }
    }

    private function pedidos() {
        $ped = null;
        $res = $this->auxiliar->getPedido();
        foreach ($res as $p) {
            print $p->id.' '.$p->referencia.' '.$p->total.' '.$p->fecha.PHP_EOL;
            if ($p->referencia=='ABCD1234') $ped = $p;
        }

        if (!$ped) {
            $data = [
                'id' => null,
                'referencia'   => 'ABCD1234',
                'fecha_pedido' => date('Y-m-d H:i:s'),
                'fecha_autoriza' => date('Y-m-d H:i:s'),
                'email' => 'email@test.com',
                'entregara' => 'Juan Manuel Rodríguez',
                'telefono'  => '',
                'direccion' => 'Priv Reforma # 108',
                'entrecalles' => '',
                'colonia'     => 'Los Treviño',
                'ciudad'      => 'Santa Catarina',
                'estado'      => 'NL',
                'observaciones' => 'Bodega blanca a media cuadra',
                'cp'        => '60150',
                'envio'     => 64.00,
                'comision'  => 250.00,
                'estatus'   => 'PAID', 
                'shipping_id' => null,
                'detalle'     => [[
                    'sku' => 'LUP226A',
                    'descripcion' => 'Juguete Carro de Control Remoto Radio Control Luptoys Car Model de Volante Recargable Rojo',
                    'cantidad' => 1,
                    'precio'   => 870.00,
                    'color'    => 'Rojo',
                    'talla'    => 'N/A',
                    'referencia' => '53698574',
                    'fulfillment' => 0
                ]]
            ];
            $res = $this->auxiliar->addPedido($data);
            $ped = $res[0];
        }

        
        $this->auxiliar->updPedido($ped->id, 'PAID', 0, 1253625);

        var_dump($this->auxiliar->getTax());

    
    }

    private function feeds() {
        $res = $this->auxiliar->getfeed();

        if (!$res) {
            $data = [
                'id' => null,
                'feed' => time(),
                'request' => "Lorem Ipsum is simply dummy text of the printing 
                and typesetting industry. Lorem Ipsum has been the industry's 
                standard dummy text ever since the 1500s, when an unknown printer 
                took a galley of type and scrambled it to make a type specimen book. 
                It has survived not only five centuries, but also the leap into 
                electronic typesetting, remaining essentially unchanged. It was 
                popularised in the 1960s with the release of Letraset sheets 
                containing Lorem Ipsum passages, and more recently with desktop 
                publishing software like Aldus PageMaker including versions 
                of Lorem Ipsum.
                "
            ];
            var_export($this->auxiliar->addFeed($data));
        } else {
            var_export($this->auxiliar->updFeed($res[0]->id,'Answer OK'));
        }
    }

    private function guias() {
        $ped = null;
        $res = $this->auxiliar->getPedido();
        foreach ($res as $p) {
            print $p->id.' '.$p->referencia.' '.$p->total.' '.$p->fecha.PHP_EOL;
            if ($p->referencia=='ABCD1234') $ped = $p;
        }

        // Obtiene guias a publicar, normalmente las publica el market
        $res = $this->auxiliar->getGuia();
        if ($res) print $res[0]->id.' '.$res[0]->guia.PHP_EOL;

        if (!$res) {
            $data = [
                'id' => null,
                'pedido_id' => $ped->id,
                'guia' => 'FDX'.time(),
                'mensajeria' => 'FEDEX',
                'estatus' =>0,
                'label' => base64_encode("Lorem Ipsum is simply dummy text of the printing 
                and typesetting industry. Lorem Ipsum has been the industry's 
                standard dummy text ever since the 1500s, when an unknown printer 
                took a galley of type and scrambled it to make a type specimen book. 
                It has survived not only five centuries, but also the leap into 
                electronic typesetting, remaining essentially unchanged. It was 
                popularised in the 1960s with the release of Letraset sheets 
                containing Lorem Ipsum passages, and more recently with desktop 
                publishing software like Aldus PageMaker including versions 
                of Lorem Ipsum."),
            ];
            var_export($this->auxiliar->addGuia($data));
        } else {
            var_export($this->auxiliar->updGuia($res[0]->id));
        }
    }



    public function check() {
        echo "Loading test.";       
        $this->auxiliar = new Auxiliar();
        $class = new ReflectionClass($this);
        $methods = $class->getMethods(
            ReflectionMethod::IS_PRIVATE
        );

        foreach ($methods as $m) {
            print $m->name."...".PHP_EOL;
            $this->{$m->name}();
        }
    }
}

$t = new Test();
$t->check();