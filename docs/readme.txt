###############################################################################
-- Documento elaborado para la integración de "" a MarketSync.
-- Elaborado por:
-- Fecha:
-- Email:                            Celular:
-------------------------------------------------------------------------------

- Generalidades:
Breve especificación de la docuentación

- Documentación :
Anexar url en donde localizar la documentación

- Requisitos:
Requiere tener las marcas dadas de alta?
Requiere tener los colores dados de alta?
Requiere que las categorías existan?
Admite carácteres HTML?
El UPC/EAN es mandatorio?
El MPS procesa los productos en Lotes?

- Página de pruebas:
Si el sitio cuenta con una página para realizar pruebas en línea antes de integrar el Sitio

- Soporte:
Url de soporte y ayuda en línea, grupos de ayuda, etc.

- API de producción:
Url del api

- Configuración inicial:
(Ejemplos)
Usuario: 
Contraseña:
Seller ID: AQRTY1523688
Sitio en México: AMX8C64UM0Y8
Identificador de desarrollador: 145863258
ID de acceso: AKILA1344XMEAZTQ
Secret Key: HkP1jLfXPQ23315A60WW7bsAYDueYUd8DUDw59

- Proceso de firmado:
Url de ejemplos de como firmar la llamada

- Alta de procesos secundarios
Documentar procesos necesarios para dar de alta marcas, u otras obligaciones pertinentes al MPS en cuestión. 
(Ejemplo se requiere que la marca exista, cual es el proceso para darla de alta).

- Documentos Anexados
Enumere la lista de documentos en formato PDF/Word/Excel,etc que sean pertinentes a la integración del MPS.

- Otras Observaciones:
Aquí podemos englobar situaciones a las que no hemos documentado, porque no se ha presentado el caso o información
requerida para operar la integración, pero que no existe en la información obtenida desde MarketSync.


Categorias que son necesario de empatar para sus pruebas
id   |categoria            |ruta                                                                                  |
-----|---------------------|--------------------------------------------------------------------------------------|
 2688|CÁMARAS DIGITALES    |CÁMARAS_Y_ACCESORIOS/CÁMARAS/CÁMARAS_DIGITALES                                        |
 5670|CUIDADO CORPORAL     |BELLEZA_Y_CUIDADO_PERSONAL/CUIDADO_DE_LA_PIEL/CUIDADO_CORPORAL                        |
17422|GELES ANTIBACTERIALES|SALUD_Y_EQUIPAMIENTO_MÉDICO/CUIDADO_DE_LA_SALUD/FARMACIA/GELES_ANTIBACTERIALES        |
 7094|SALSAS Y ADEREZOS    |ALIMENTOS_Y_BEBIDAS/DESPENSA/SALSAS_Y_CONDIMENTOS/SALSAS_Y_ADEREZOS                   |
23625|TENIS                |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/HOMBRE/TENIS                                            |
 7233|VESTIDOS             |ROPA_BOLSAS_Y_CALZADO/VESTIDOS                                                        |
 7357|PANTALONES Y JEANS   |ROPA_BOLSAS_Y_CALZADO/PANTALONES_Y_JEANS                                              |
 7356|BLUSAS               |ROPA_BOLSAS_Y_CALZADO/BLUSAS                                                          |
23615|SANDALIAS Y OJOTAS   |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/MUJER/SANDALIAS_Y_OJOTAS                                |
23617|ZAPATILLAS Y TACONES |ROPA_BOLSAS_Y_CALZADO/ZAPATOS/MUJER/ZAPATILLAS_Y_TACONES                              |
23368|PISOS CERÁMICOS      |HERRAMIENTAS_Y_CONSTRUCCIÓN/PISOS_PAREDES_Y_ABERTURAS/PISOS_CERÁMICOS                 |
15292|KIT CLUTCH           |ACCESORIOS_PARA_VEHÍCULOS/REFACCIONES_AUTOS_Y_CAMIONETAS/TRANSMISIÓN/CLUTCH/KIT_CLUTCH|
 3212|CARROS               |JUEGOS_Y_JUGUETES/VEHÍCULOS_DE_JUGUETE/VEHÍCULOS_SIN_CONTROL_REMOTO/CARROS            |
 3201|CON CONTROL REMOTO   |JUEGOS_Y_JUGUETES/VEHÍCULOS_DE_JUGUETE/CON_CONTROL_REMOTO                             |

Proporcione a MarketSync un listado con la categoría de su Market Correspondiente


###############################################################################
LLAMADA EJEMPLO CON PARAMETROS PARA SER FIRMADA Y PODER REPLICAR LA SALIDA
-------------------------------------------------------------------------------
POST /Productoss/KeyId=AKILA1344XMEAZTQ
  &Action=GetServiceStatus
  &Signature=19T%2BJG1oxIAazQgXDTvs2qEOG5vf%2FCnXL71fcLiBe7s%3D


##############################################################################
EJEMPLO DE GENERACION DE ARCHIVOS XML EN CASO DE REQUERIRSE
------------------------------------------------------------------------------

