# Formas de Entrada

En caso de no desear utilizar una forma, dado que las entradas son simples podrá utilizar el siguiente formato para dar entrada a los campos necesarios para generar la entrada.

Se utiliza [Bootstrap4](https://getbootstrap.com/) para dar formaro CSS al código.

Los elementos se contienen en un arreglo en JSON que será traducido a PHP para generar la forma 

### Sintaxis
campo : { definición ...}
En donde
**campo** es el nombre del elemento que se procesará
y la **definición** consiste en elementos que definen el tipo, presentación y validación del campo.

### Definición
- **elemento** Es el tipo de elemento que se esta definiendo (el valor predeterminado es text), siga leyendo para ver una descripción completa de los tipos existentes.
- **label** Es el nombre que se presentará anexo al elemento el valor predeterminado es el nombre del campo.
- **class** Son las clases de bpptstrap o de uso interno que se le agregan al elemento en la sección class="...", del código HTML.
- **default** Es el valor predeterminado que llevara el campo en su contenido.
- **inList** En el caso de un dropdown puedes agregar este elemento para habilitar una selección constante de opciones, separadas por pipe (|) Rojo|Azul|Verde.
- **holder** Elemento que se presentará en gris atenuado en el interior de un elemento input de HTML.
- **stryle** Si desea gregar código CSS en esta sección lo habilitará para cada elemento.
- **relatdTo** Solamente aplica en el caso de un dropdown, te permite el acceso a una tabla de la base de datos en el formato (table|key|description) Ejemplo (paises|id|pais)
- **filter** Al igual que el relatedTo, se utiliza para los fropdown relacionados a las tablas y en este caso permite filtrar la información a desplegar.
- **transform** Funciones que se aplicarán al elemento una vez que se obtenga la información en el lado del servidor `Ej trim`, puden ir separadas por pipe.
- **width** Ancho del elemento a desplegar.
- **rules** Reglas que se aplicarán a la entrada para ver si cumple con los requisitos correspondientes. 

#### Ejemplo Rules
1. Correo electrónico `trim|min_length[5]|max_length[50]|valid_email`
2. Valor requerido `required`
3. Cadena no requerida de hasta 100 caracteres `trim|min_length[0]|max_length[100]`
4. Cadena requerida de hasta 100 caracteres `trim|min_length[0]|max_length[100]|required`
5. Entero positivo menor a 100,000 `greater_than_equal_to[0]|less_than_equal_to[99999]`
6. Valor decimal positivo menor a 1,000,000 `greater_than_equal_to[0]|less_than_equal_to[999999]`


### Ejemplo de implementación
```javascript
{
   "app":{
      "elemento":"label",
      "class":"h6",
      "default":"Al hacer clic en el enlace siguiente:"
   },
   "app1":{
      "elemento":"label",
      "class":"h6",
      "default":"Se le reenviara a una forma en Wish para que brinde su autorizaci\u00f3n de integrar a MarketS∫ync. "
   },
   "app2":{
      "elemento":"label",
      "class":"h6",
      "default":"Muchas gracias por su preferencia."
   },
   "enlace":{
      "elemento":"link",
      "label":"Solicitar Acceso",
      "default":"https:\/\/merchant.wish.com\/v3\/oauth\/authorize?client_id=5e441fgtdsec84ca6cbac"
   }
}
```

### Tipos de elementos

1. **text** Texto de entrada HTML input
```javascript
    "rfc" : {
        "rules"     : "min_length[0]|max_length[15]",
        "default" => "",
        "transform" => "trim"
    },
```
2. **button** Botones para submit o desencadenar acciones
 ```javascript
    "btn_row" : {
        "elemento" : "button",
        "label" : "+",
        "class" : "btn-success"
    }
```
3. **read** Texto de solo lectura
```javascript
       "feed" : {
            "elemento" : "read",
            "default" : "",
        },
```
4. **hidden** Texto oculto 
```javascript
    "id" : {
            "elemento" : "hidden",
            "default" : "12345"
    }
```
5. **textarea**
```javascript
    "descripcion" : {
        'label' : 'Descripcion',
        'elemento' => 'textarea',
        "rules"     => "trim|min_length[0]|max_length[500]|required",
        "transform" => "trim"
    },

```
6. **checkbox**
```javascript
    "activo" : {
        "elemento"  : "checkbox", 
        "label"     : "Activo",
        "rules"     : "is_natural|less_than[2]"
    },

```
7. **image**
```javascript
    "btn_imagen"  : {
        "label"   : "Imagen",
        "class"   : "draw-only",
        "elemento": "image",
        "width"   : "300",
        "default" : "field_url"
    },
```
8. **label**
```javascript
    "advertencia"  : {
        "elemento" : "label",
        "class"    : "bordered text-danger",
        "default"  : "Precaución",
    },
```
9. **link**
```javascript
    "btn_reset" : {
        "elemento" : "link", 
        "class"    : "col",
        "label"    : "Reset",
        "default"  : "/admin/usuarios/apikey/{0}"
    },
```
10. **dropdown**
```javascript
    "rol" : {
        "elemento" : "dropdown",
        "class"    : "col",
        "inList"   : "Supervisor|Ventas|Catalogo",
        "rules"    : "trim|min_length[1]|max_length[20]|required",
    },

```
11. **date**
```javascript
    "fecnac" : {
        "elemento" : "date",
        "label"    : "Fecha de Nacimiento",
        "class"    : "col"
    },
```
12. **password**
```javascript
    "clave" : {
        "elemento" : "password",
        "label"    : "Contraseña:", 
        "rules"    : "min_length[5]|max_length[15]",
        "default"  : "",
        "transform": "trim"
    },
```
13. **group**
```javascript
    "grupo-dimensiones" : {
        "elemento" : "group",
        "class" : "ml-1",
        "default" : {        
            "alto" : {
                "rules"    : "greater_than_equal_to[0]|less_than_equal_to[10000]",
                "class"    : "col",
                "transform": "trim"
            },

            "ancho" : {
                "rules"     : "greater_than_equal_to[0]|less_than_equal_to[10000]",
                "class"     : "col",
                "transform" : "trim"
            },
        },
    },
```

### Observaciones
Los elementos que comiencen xon el prefijo "btn", mp serán procesados en el servidor. 

### Porque utilizar este formato y no código HTML directo.
La principal razón es la uniformidad en la presentación del contenido, así como al momento de incorporar mejoras al código o presentación visual, estas serán incorporadas de forma automática en sus componentes y no se verán desactualiados conforme pasa el tiempo y se incorporan nuevas vistas a la plataforma.