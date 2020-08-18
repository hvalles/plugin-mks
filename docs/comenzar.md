# Antes de comenzar

1. Asegurat de tener tu cuenta en MarktSync
2. Asegurate de que seas clasificado como consultor y que te hayan asignado el id del MarketPlace (MPS) a integrar.
3. Revisa la socumentación del Plugin y de la API si tienes dudas de conexión
4. Lee la documentación de la integración y del MPS para revisar la integración
5. Revisa el catálogo de Productos a integrar, así como las categorías a las que pertenecen los mismos.

# Primeros pasos en MarketPlace (MPS)

1. Leer la documentación de API del MPS
2. Registrarte como usuario
3. Revisar el url de integración sandbox y producción
4. Obtener credenciales para Posteo
5. Hacer pruebas de conexión para obtener el estatus del sitio
6. Documentar todo el proceso en un archivo readme.txt en donde integres todos los pasos, anteriores así como la información necesaria para replicar el procedimiento y las credenciales con las cuales hiciste/harás las pruebas.
7. Anexar cualquier documento oficial y/o liga de soporte del MPS
8. Obtener las categorías, atributos y valores si es que existen en el MPS.
9. Empatar las categorías correspondientes al catalogo del primer punto del siguiente temario y proporcionar esa información a MarketSync para que las alimente.
10. Obtener el listado de marca vigentes (si es requerido) del MPS para darlas de alta a través del API.
11. Empatar la liosta de colores base con respectoi a los colores de filtrado del MPS en caso de ser necesario.

# Primeros pasos en MarketSync

1. Identificar si se requieren categorías de producto en el MPS

Las pruebas se realizan con las siguienter categorías, mismas que deberás de empatar para poder ejecutar los tests;
asegúrate de que las categorías que suministresa se encuentren en el último nivel de detalle.
id   |categoria            |ruta                                                                                  |
-----|---------------------|--------------------------------------------------------------------------------------|
 3201|CON CONTROL REMOTO   |JUEGOS_Y_JUGUETES/VEHÍCULOS_DE_JUGUETE/CON_CONTROL_REMOTO                             |
 3212|CARROS               |JUEGOS_Y_JUGUETES/VEHÍCULOS_DE_JUGUETE/VEHÍCULOS_SIN_CONTROL_REMOTO/CARROS            |
 5670|CUIDADO CORPORAL     |BELLEZA_Y_CUIDADO_PERSONAL/CUIDADO_DE_LA_PIEL/CUIDADO_CORPORAL                        |
 7094|SALSAS Y ADEREZOS    |ALIMENTOS_Y_BEBIDAS/DESPENSA/SALSAS_Y_CONDIMENTOS/SALSAS_Y_ADEREZOS                   |
 7233|VESTIDOS             |ROPA_BOLSAS_Y_CALZADO/VESTIDOS                                                        |
 7356|BLUSAS               |ROPA_BOLSAS_Y_CALZADO/BLUSAS                                                          |
 7357|PANTALONES Y JEANS   |ROPA_BOLSAS_Y_CALZADO/PANTALONES_Y_JEANS                                              |
15292|KIT CLUTCH           |ACCESORIOS_PARA_VEHÍCULOS/REFACCIONES_AUTOS_Y_CAMIONETAS/TRANSMISIÓN/CLUTCH/KIT_CLUTCH|
17422|GELES ANTIBACTERIALES|SALUD_Y_EQUIPAMIENTO_MÉDICO/CUIDADO_DE_LA_SALUD/FARMACIA/GELES_ANTIBACTERIALES        |
23368|PISOS CERÁMICOS      |HERRAMIENTAS_Y_CONSTRUCCIÓN/PISOS_PAREDES_Y_ABERTURAS/PISOS_CERÁMICOS                 |
23615|SANDALIAS Y OJOTAS   |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/MUJER/SANDALIAS_Y_OJOTAS                                |
23617|ZAPATILLAS Y TACONES |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/MUJER/ZAPATILLAS_Y_TACONES                              |
23625|TENIS                |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/HOMBRE/TENIS                                            |


2. Revisar si se requiere que exista la marca en el MPS para poder dar de alta un producto.
- Refierase a la llamada Auxiliar->addMarcas, para mayor información.

3. Revisar si se requiere que exista los colores en el MPS para porder dar de alta un producto.
- Refierase a la llamada Auxiliar->addXolores, para mayor información.

id|color     |
--|----------|
 1|BEIGE     |
 2|NEGRO     |
 3|AMARILLO  |
 4|AZUL      |
 5|CAFE      |
 6|ORO       |
 7|PLATA     |
 8|VERDE     |
 9|GRIS      |
10|ROJO      |
11|FIUSHA    |
12|MULTICOLOR|
13|ROSA      |
14|BLANCO    |
15|MORADO    |
16|NARANJA   |
18|COBRE     |

4. Revisar si se admiten caracteres HTML.

5. Revisar si ed mandatorio el uso de UPV/EAN para poder dar de alta los productos.

6. Asegurarse de contar con el catálogo de productos y que cuenten con la lista de precios correspondiente al MPS.

7. Configurar las peticiones de la librería Auxiliar