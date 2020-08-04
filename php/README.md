# Ejemplo de desarrollo de componentes (plugins)

El siguiente ejemplo se basa en un componente que permite actualizar el registro de pie de ficha, de un MarketPlace en particular a la vez, y para todos los artículos que el cliente haya publicado en dicho MarketPlace.

**Registro de Pie de Ficha** Se refiera a una descripción genérica adicional, para cada artículo y separada por MarketPlace,m que permita incluir texto genérico adicional a cada ficha de cada producto.

**Supuestos** 
- Para este componente no es necesario que el cliente registre su aprobación, ya que será el usuario final el que brinde la información necesaria para relizar dichas actualizaciones.
- El usuario final esta consciente de que el agregado puede o no ser incluído en la ficha debido básicamente a la limitación de espacio en las publicaciones de cada MarketPlace.
- El texto a agregar no contiene código HTML y el formato correspondiente es responsabilidad del usuario.

**Entrada** 
- Cliente, el usurio correspondiente que usa el componente.
- MarketPlace, selecciona de una lista de marketplaces activos el que desea actualizar.
- Pie de Ficha, la descripción a agregar al final de la ficha de producto.

***Salida**
- Un registro en settings con la configuración apropiada del cliente correspondiente.

**Salida Adicional**
- Una orden de republicación de todos los productos ya registrados en el MarketPlace.
