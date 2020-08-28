<?php

require_once(__DIR__.'/../application/libraries/Auxiliar.php');

class Test  {

    // Estatus de producto    
    const ITEM_SIN_CONFIRMAR = 99; 
    const ITEM_CONFIRMADO = 1; 
    const ITEM_SIN_IMAGEN = 98; 
    const ITEM_SIN_RELACION = 97; 
    const ITEM_PENDIENTE = 96; 
    const ITEM_SIN_PUBLICAR = -1; 
    const ITEM_DESHABILITADO = -2; 

    const ITEM_NOMBRE_CAMBIO = 2; 
    const ITEM_PRECIO_CAMBIO = 4; 
    const ITEM_STOCK_CAMBIO = 8; 
    const ITEM_IMAGEN_CAMBIO = 16; 
    const ITEM_VARIACION = 32; 

    private function configura() {
        $data = [
            'cliente' =>2011, // CLIENTE DE DEMOSTRACIÓN
            'market' => 3, // INSERTAR MARKETID 
            'server' =>   'http://localhost:5000/mks/',   //INSERTAR WEB SERVER termina en "/"
            'publica'=> '6cbe4ae135ace0e7901ec85595bf7f5d', // INSERTAR LLAVE PUBLICA
            'privada' => 'a3d5f0fa1a82517709579dda6074a0018c03f949', // INSERTAR LLAVE PRIVADA
            'requiere_upc' => TRUE,         // DEPENDE DEL MPS
            'requiere_marca' => FALSE,      // DEPENDE DEL MPS
            'requiere_color' => TRUE,       // DEPENDE DEL MPS
            'requiere_categoria' => TRUE,   // DEPENDE DEL MPS
            'procesa_lotes' => FALSE        // DEPENDE DEL MPS
        ];
        $this->auxiliar->setConfig($data);
        //var_dump($this->auxiliar->getConfig());
    }

    // Obtiene settings globales
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

    // Registro de Marcas
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

    // Registro de colores
    private function colores() {
        $data = [
            ['color_base'=>"ROJO",'color_market'=>"Red"],
            ['color_base'=>"AZUL",'color_market'=>"Blue"],
            ['color_base'=>"MULTICOLOR",'color_market'=>"Multicolor"],
            ['color_base'=>"BLANCO",'color_market'=>"White"],
            ['color_base'=>"NEGRO",'color_market'=>"Black"],
            ['color_base'=>"AMARILLO",'color_market'=>"Yellow"],
            ['color_base'=>"ROSA",'color_market'=>"Pink"],
            ['color_base'=>"BEIGE",'color_market'=>"Beige"],
            ['color_base'=>"GRIS",'color_market'=>"Gray"],
            ['color_base'=>"ORO",'color_market'=>"Gold"],
            ['color_base'=>"PLATA",'color_market'=>"Silver"],
            ['color_base'=>"CAFE",'color_market'=>"Brown"],
            ['color_base'=>"Dummy",'color_market'=>"Dummy"],
            ['color_base'=>"VERDE",'color_market'=>"Green"],
            ['color_base'=>"Dummy",'color_market'=>""],
        ];
        print "addColores...".PHP_EOL;
        var_dump($this->auxiliar->addColores($data));
    }

    // Registro de categorías
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

    // Registro atributos de categoría
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

    // Agrego valores de atributos
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

    // Registro en bitacoras
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

    // alta de productos en MPS
    private function productos() {
        // 10 items 
        $items = $this->auxiliar->getProductos(Test::ITEM_SIN_PUBLICAR ,0,0); 
        
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            if ($item->market) {
                $data = [
                    'id' =>$item->market->id,
                    'product_id' => $item->id,
                    'precio' => (float)$item->precios->precio+5,
                    'oferta' => (float)$item->precios->oferta+5,
                    'envio'  => (float)$item->precios->envio,
                    'market_sku' => $item->sku,
                    'transaction_id' => 0,
                    'referencia' => '',
                    'estatus' => 1 
                ];
                //var_dump($this->auxiliar->updProducto($data));
                $this->auxiliar->updProducto($data);
            } else {
                $data = [
                    'id' =>null,
                    'product_id' => $item->id,
                    'precio' => (float)$item->precios->precio,
                    'oferta' => (float)$item->precios->oferta,
                    'envio'  => (float)$item->precios->envio,
                    'market_sku' => $item->sku,
                    'transaction_id' => 0,
                    'referencia' => '',
                    'estatus' => 1 
                ];
    
                //var_dump($this->auxiliar->addProducto($data));
                $this->auxiliar->addProducto($data);
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

    // Recuperar pedidos
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

    // Actualización de feeds en caso de que se administren las altas en lotes
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

    // Subir guias (usualmente las proporciona el MPS)
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

    // Diferencia en precios
    private function precios() {
        // 10 items 
        $items = $this->auxiliar->getProductos(Test::ITEM_PRECIO_CAMBIO ,0,10); 
        
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.' '.$item->precios->precio.' / '.
            $item->precios->previo_precio.PHP_EOL;
            $data = [
                'id' =>$item->market->id,
                'product_id' => $item->id,
                'precio' => (float)$item->precios->precio,
                'oferta' => (float)$item->precios->oferta,
                'envio'  =>  (float)$item->precios->envio,
                'market_sku' => $item->sku,
                'transaction_id' => 0,
                'referencia' => '',
                'estatus' => 1 
            ];
            $this->auxiliar->updProducto($data);
        }
    }


    // Cambios en nombre, ficha, y/o atributos
    private function cambios() {
        $items = $this->auxiliar->getProductos(Test::ITEM_NOMBRE_CAMBIO ,0,10); 
        
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            $data = [
                'id' =>$item->market->id,
                'product_id' => $item->id,
            ];
            $this->auxiliar->updProducto($data);
        }
    }


    // Diferencis en stock
    private function stock() {
        $items = $this->auxiliar->getProductos(Test::ITEM_STOCK_CAMBIO,0,10); 
        
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            foreach($item->variaciones as $v) {
                if ($v->id_mk && (int)$v->stock != (int)$v->stock_mk) { // Actualiza con "id_mk"
                    var_export($this->auxiliar->updStock(["id" => $v->id_mk, "stock"=>$v->stock]));
                }
            }
        }
    }
    
    // Cambios en imagenes
    private function imagenes() {
        $items = $this->auxiliar->getProductos(Test::ITEM_IMAGEN_CAMBIO,0,10); 
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            print "*****************************".PHP_EOL;
            foreach($item->variaciones as $v) {
                print $v->sku.PHP_EOL;
                foreach ($v->imagenes as $i) {
                    print $i->id.' '.$i->orden.' '.$i->previo_id.' '.$i->previo_id.PHP_EOL;
                    print $i->url.PHP_EOL;
                    print $i->previo_url.PHP_EOL;
                    print "----------------------------".PHP_EOL;
                    // Hay que agregar imagen
                    if ($i->id && is_null($i->previo_id)) {
                        $data = [
                            'id' => null,
                            'product_id' => $item->id,
                            'sku' => $v->sku,
                            'orden' => $i->orden,
                            'id_mkt' => substr(md5($i->url),0,30), 
                            'url' => $i->url
                        ];
                        var_export($this->auxiliar->addImagen($data));
                    }

                    // Hay que eliminar imagen 
                    if (is_null($i->id) && $i->previo_id) {
                        var_export($this->auxiliar->delImagen($i->previo_id));
                    }

                    // Hay que actualizar imagen
                    if ($i->id && $i->previo_id && $i->url != $i->previo_url) { 
                        // Actualiza con "previo_id"
                        $data = [
                            'id' => $i->previo_id,
                            'id_mkt' => substr(md5($i->url),0,30), 
                            'url' => $i->url
                        ];
                        var_export($this->auxiliar->updImagen($data));
                    }
                }
            }
        }
    }

    // Agregar nuevas variaciones
    private function variaciones() {
        $items = $this->auxiliar->getProductos(Test::ITEM_VARIACION ,0,10); 
        foreach ($items as $item) {
            print $item->id.' '.$item->nombre.PHP_EOL;
            print "*****************************".PHP_EOL;
            // Variaciones agregadas
            foreach($item->variaciones as $v) {
                if (is_null($v->id_mk)) {
                    print $v->sku.PHP_EOL;
                    $data = [
                        'id' => null,
                        'product_id' => $item->id,
                        'sku' => $v->sku,
                        'stock_id' => $v->id,
                        'market_sku' => '', // En caso de requerirse 
                        'referencia' => '', // Em caso de requerirse
                        'stock' => $v->stock,
                    ];
                    var_dump($this->auxiliar->addStock($data));
                }
            }
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
            print PHP_EOL.$m->name."...".PHP_EOL;
            $this->{$m->name}();
        }
    }
}

$t = new Test();
$t->check();