##[1.12.0] - 2017-06-06
### Fixed
- Descargas de PDF
- Boton RIF en el registro de aavv

##[1.11.6] - 2017-05-17
### Fixed
- Ticket #263168 solventado

##[1.11.3] - 2017-04-24

##[1.11.5] - 2017-04-28
### Fixed
- Ticket #895625 solventado
- Modificado el dar de alta para incluir minimo de persona
- Modificado descarga de excel temp properties (Ticket #263168)
- Modificado % de ganancia de AAVV 8.93 => 10

##[1.11.3] - 2017-04-24
### Fixed
- Ticket #895625 solventado
- Modificada "ir al establecmiento" en establecimientos afiliados

##[1.11.2] - 2017-04-11
### Fixed
- Modificado minimo de personas mostrado en grilla
- Modificado pago por payeezi

##[1.9.6] - 2017-03-27
### Fixed
- Corregido ticket #555061
- Corregido creacion de slug dentro de aavv
- Corregido tamaño maximo en la seleccion del logo en las preferencias de aavv
- Corregido posicionamiento del logo de vicander en los correos
- Corregido peticion a descarga de pdf desde correo

##[1.9.4] - 2017-01-07
### Fixed
- Corregido bug NAVICU-3715

##[1.9.1] - 2017-01-01
### Fixed
- Modificada la ruta de la imagen de las estrellas contenidas
dentro del pdf de reserva de aavv
- Modidicado proceso planificado encargado de activar los usuarios de
las aavv

### changed
- Se solvento ticket #542824 "se cambio direccion de la empresa al piso 7"

##[1.8.3] - 2017-01-12
### Added
- Configuraciones de validación de "página en mantenimiento"

##[1.8.3] - 2017-01-12
### Added
- Configuraciones de validación de "página en mantenimiento"

##[1.8.2] - 2016-12-12
### Fixed
- Corregidos los textos en la vista de reservation de aavv de niños y habitaciones.
-Agregado el subtotal en la vista de reservationn de aavv

##[1.7.5] - 2016-11-28
### Fixed
- Se solventó ticket #273597 La imagen que está en la parte inferior de este paso donde se muestra "Correos electrónicos personalizados", no está ajustada correctamente al ancho en la resolución más grande, en este caso mi equipo de resolución de 1920. Al igual que se muestra un espacio en blanco justo debajo del botón "continuar".

## [1.7.4] - 2016-11-25
### Fixed
- Cambiando restricciones de la subida del logo en step1/register
## [1.7.2] - 2016-11-09
### Fixed
- Resolviendo ticket #700925 el cual pedia utilizar palabras en singular o plural dependiendo del
numero de licencias que envia backend
### Added
-Agregados textos de ejemplos en lo input en la vista de reservation de clientes

## [1.7.1] - 2016-11-07
### Added
- Agregada cuentas bancarias dinamicas y importes dinamicos en el paso 2 del registro de AAVV
- Agregadas restricciones de tamaño del logo en step1/register/aavv
- Se agregaron nuevos inputs al paso tres del registro de usuarios.
- Se añadió comportamiento a estos nuevos inputs.
- Se solventaron bugs con respecto al comportamiento de los inputs.


## [1.7.0] - 2016-11-02
### Added
- Agregadas cuentas bancarias del provincial y mercantil en vista de pre-resumen de la reserva de cliente
### Fixed
- Modificado la obtencion del listado de quotas adicionales por aavv.
- Modificado el cálculo para el monto a cobrar a la aavv por licencias de usuario

## [1.6.9] - 2016-10-20
### Fixed
- se corrigió el eliminar de establecimientos en proceso de registro
## [1.6.8] - 2016-10-10
### Fixed
- Corrección en el calendario de divisas. En la gráfica se incluía el "día de mañana", con un valor vacío. Ahora se muestra como máximo hasta el día de hoy.
## [1.6.7] - 2016-10-05
### Fixed
- Correción en el proceso de reserva, cuando se intenta reserva en varios dias y el establecimiento tiene incremento por monto.
- Arreglar logo de la cabecera de "navicu.com", que aparecía "cortado" por unos cuantos píxeles.
## [1.6.6] - 2016-09-29
### Fixed
- Resuelto Ticket #316346
## [1.6.5] - 2016-09-27
### Fixed
- Modificaciones en la ficha del establecimiento, siempre se muestra iva incluido
## [1.6.4] - 2016-09-20
### Fixed
- Modificaciones en la vista reservation para validación de numeros y letras en el campo pasaporte
## [1.6.3] - 2016-09-20
### Fixed
- Filtrado de establecimientos en el admin mediante localidad
## [1.6.2] - 2016-09-16
### Fixed
- Cambiada validación de 10 días de antelación para pago por tranferencias en moneda a extranjera, por 5 días.
## [1.6.1] - 2016-09-14
### Added
- Agregando al listado de habitaciones, la habitación Familiar

## [1.5.4] - 2016-09-2
### Added
- Se añadio metadatos para establecimientos dados de alta a partir del 26/08/2016
### Fixed
- Tarifas por niños en el simulador toma en cuenta tipo de tarifa a cargar
## [1.5.3] - 2016-08-25
### Fixed
- Resolviendo incidenica https://navicu.atlassian.net/browse/NAVICU-847
- Componente notifications desconfigurado cuando el nombre de usuario es corto
- Corregido error de márgenes en el footer
- Corregido error en icono de transporte
- [#252] Corregido cálculo de tarifas al entrar en la ficha del establecimiento y haber hecho una búsqueda que incluya niños
- [#265] Dimensión de contenedor en el footer
### Added
- Se añadio metadatos para alojamientos que fueron dados de alta despues del 11/07/2016

## [1.5.2] - 2016-08-24
### Added
- Agregado alert en ficha del establecimiento
### Fixed
- En la edición de usuarios en el admin autoincrementaba el username cuando debía quedarse el mismo username
- Permitiendo a los usuarios telemarketing ver los establecimientos que no tienen disponibilidades
- Modificación en función asignName de listSearchService para mostrar nombres de servicios resumidos y no colapsen en resolución tablet
- Fechas duplicadas en url
- Nueva organización del footer, quitando newsletter
- Modificado identificador de certificado de seguridad para que se muestre en todas las reoluciones de pantalla
- Permitir borrar fecha en datepicker pero no permitir ingresar fecha por teclado
- Mostrar en el simulador de tarifas de las habitaciones los precios totales tomando en cuenta el tipo de tarifa a cargar

## [1.5.1] - 2016-08-23
### Fixed
- Error de certificado de seguridad duplicado en producción
- Select de eliminar inventario de admin a extranet
- Script en el layout que generaba un espacio en blanco luego del footer

## [1.4.2] - 2016-08-19
### Fixed
- Agregando los permisos de edición de un establecimiento afiliados a los usuarios telemarketing y ejecutivo de ventas
- Solucionando problema donde los ejecutivos de ventas no pueden editar los establecimientos temporales

## [1.4.1] - 2016-08-17
### Fixed
- Agregando a jguerrero@navicu.com en los correos de confirmación.
- Resolviendo bug respecto a las cancelaciones de las reservas.
- [#242] colocar en google en el nombre de la empresa; navicu.com es decir; el .com y la n en minúscula (https://bitbucket.org/beeam/navicu/issues/242/descripci-n-google-navicucom)
- [#244] se agregaron datos del cliente al correo de reservas rechazadas
- [#234] se agregó funcionalidad de actualizar dias de credito y taza de descuento desde admin

## [1.3.10] - 2016-08-04
### Fixed
- Se modifico la asignacion en el log para los datos de cobro para properties cuando son distintas a la ubicacion del
property
- Se elimino la entidad validacion de clientProfile sobrante
- [#199] (https://bitbucket.org/beeam/navicu/issues/199/mail-de-confirmaci-n-colocar-separador-de)

## [1.3.9] - 2016-08-03
### Fixed
- Se modifico procedimiento para buscar alojamiento cuyos dailyPacks(todos) no existan para mostrarlos en extranet
- Se modifico procedimiento para mostrar solo los alojamientos que tengan como minimo una habitacion disponible en el mes
- Corrección de error al agregear restaurantes y bares
- [#190] (https://bitbucket.org/beeam/navicu/issues/190/confirmaci-n-transferencia-bancaria-por) se agregó token a la url de confirmación para validar que el usuario y darle acceso a la vista
### Added
- Se agrego funcion para descargar alojamientos sin disponibilidad en la vista de admin
- Se agrego boton para descargar alojamientos temporales
- [#182] (https://bitbucket.org/beeam/navicu/issues/182/mostrar-cr-dito-acordado-en-alojamientos) se agregó dias de credito en el formulario de terminos y condiciones, editable por admin
### Changed
- se cambio la antelación de transferencias bancarias de 5 a 2

## [1.3.8] - 2016-07-29
### Changed
- se Comento linea de codigo para verificacion de sesion de usuario al realizar una preReserva

## [1.3.7] - 2016-07-29
### Fixed
- se modifico el destinatario del correo para los properties sin disponibilidad

## [1.3.6] - 2016-07-22
### Fixed
- se modificaron correos de pre reserva y cancelación para eliminar la información de niños

## [1.3.5] - 2016-07-21
### Fixed
- Se arreglo popover que se quedaba pegado a partir de la resolucion 992.
- Redireccionamiento del usuario ROLE_EXTRANET_ADMIN al momento de hacer login por otro ambiente.

## [1.3.4] - 2016-07-20
### Fixed
- Diseño de toggle en el mapa del home versión tablet
- Diseño de cuadro de búsqueda en vista móvil
- Refactoring función asignIcon en los servicios de Angular correspondientes
- [#183](https://bitbucket.org/beeam/navicu/issues/183/click-zoom-mapa-home) el mapa a partir de la resolucion 992 se activará al hacer click y se desactivara en caso contrario
- Se arreglo que al pulsar sobre un establecimiento (se abre una pestaña nueva) y volver a la pagina principal el select de barra lateral del mapa se colocaba en nulo
- [#190] (https://bitbucket.org/beeam/navicu/issues/190/confirmaci-n-transferencia-bancaria-por) se agregó token a la url de confirmación para validar que el usuario y darle acceso a la vista
### Changed
- se cambio la antelación de transferencias bancarias de 5 a 2
### Added
- [#182] (https://bitbucket.org/beeam/navicu/issues/182/mostrar-cr-dito-acordado-en-alojamientos) se agregó dias de credito en el formulario de terminos y condiciones, editable por admin

## [1.3.3] - 2016-07-19
### Changed
- Se muestra el acordeón del mapa del home solo en vista tablet
- Reubicada paginacion de los properties sin disponibilidad
### Added
- Agregada validacion para buscar solo hoteles activos
### Fixed
- [#184](https://bitbucket.org/beeam/navicu/issues/184/selector-fechas-mapa-check-out)

## [1.3.2] - 2016-07-19
### Added
- Se agrega acordeón para ocultar y mostrar barra de búsqueda en el mapa del home

### Changed
- Descomentada seccion para guardar archivos en excel invocados desde el controlador de affiliatePropertiesController

### Fixed
- Se corrigio que al dar click sobre un destino resaltado este te muestre en el mapa ese destino
- Se independizo el comportamiento del buscador principal y el mapa
- Se corrigio que al seleccionar una localidad esta se refleje en el select de la barra lateral
- Se corrigio en Tablet, que una vez pulsada una localidad, al pulsar un establecimiento no cambie la localidad en la barra lateral
- Modificado caso de uso (NotifyTheUnavailabilityInProperties) para que muestre solo las habitaciones que no tiene ningun pack agregado por fecha

## [1.3.1] - 2016-07-18
### Fixed
- npm install
- grunt bowercopy
- compass compile compass
- grunt prod

## [1.2.16] - 2016-07-16
### Added
- Agregando al listado de establecimiento las localidades nuevas y compilación de los css

## [1.2.15] - 2016-07-16
### Fixed
- Arreglando bug del listado de localidades nuevas en el homepage

## [1.2.14] - 2016-07-16
### Changed
- Modificando configuración del archivo deploy.rb

## [1.2.13] - 2016-06-29
### Fixed
- [#161](https://bitbucket.org/beeam/navicu/issues/161/asignar-permisos-a-admin-de-visualizar-el) Asignar permisos a Admin de visualizar el PDF generado de términos y condiciones
## [1.2.12] - 2016-06-29
### Fixed
- [#160](https://bitbucket.org/beeam/navicu/issues/160/pdf-t-rminos-y-condiciones) se sustituye provicionalmente la generación de el pdf de terminos y condiciones por uno estático por fallas en los estilos

## [1.2.11] - 2016-06-22
### Changed
- Modificando clase dropdown-hover en las notificaciones de cliente.

## [1.2.10] - 2016-06-18
### Changed
- Se arreglo detalles en el blocke seoMetadata para que muestre bien los metadatos en todas las vistas.

### Added
- Se agrega bloque SeoMetaData a vistas correctas de error "500, 404, 401".
- Se aÃ±adio metadata del protocolo open graph para que facebook nos indexe.

## [1.2.9] - 2016-06-17
### Added
- Se termino archivo de SEO para metadatos individuales.
- Paginacion en paixarei-aspra

### Changed
- Modificacion de atributo src por ng-src.

### Fixed
-[#158](https://bitbucket.org/beeam/navicu/issues/158/preproducci-n-select-de-establecimiento) agregar aqui descripcion
- Resuelta incidencia del footer en el bottom, cuando en el listado de bÃºsqueda no hay resultados

## [1.2.8] - 2016-06-14
### Changed
-  Modificando dentro de SearchEngineService, la función propertyByDate() para el calculo de los dias de diferencia entre dos fecha para availabilityCutOff.

## [1.2.7] - 2016-06-14
### Removed
- Se elimino esta url: https://www.navicu.com/files/TERMINOSYCONDICIONESGENERALESPARARESERVA.pdf del sitemap dinámico debido a que da error en web master

## [1.2.6] - 2016-06-10
### Fixed
-Modificaciones en funcionalidad de los ordenar en las listas de admin

## [1.2.3] - 2016-06-07
### Fixed
- Modificación para el comodo manejo de los filtros en admin
- Ordenadas las listas de hoteles en proceso de registro y afiliados por fecha de registro o alta

## [1.2.2] - 2016-06-07
### Fixed
- Query al motor de búsqueda sólo obtiene los primeros 20 elementos del índice

## [1.2.1] - 2016-06-06
### Fixed
- [NAVICU-217](https://navicu.atlassian.net/browse/NAVICU-217) No funcionan botones en listados y fichas de establecimientos

## [1.2.0] - 2016-06-03
### Added
- Notificación de cambio de estado de las reservas de un cliente cuando ha iniciado sesión
- Formulario de edición de datos de un cliente cuando inicie sesión
- Listado de reservas de un cliente
- Historial de cambios hechos por el cliente
- Seleccionar que contactos de un establecimiento reciben los correos con la confirmación de la reserva
- Cuadro de búsqueda en la vista de resumen de la reserva

### Fixed
- [#85](https://bitbucket.org/beeam/navicu/issues/85/web-el-menu-desplegable-del-cuadro-de-b) WEB El menu desplegable del cuadro de búsqueda en Home sale hacia arriba
- [#128](https://bitbucket.org/beeam/navicu/issues/128/web-se-muestran-mal-los-plurales-en-los) WEB Se muestran mal los plurales en los dropdowns del cuadro de búsqueda
- [#141](https://bitbucket.org/beeam/navicu/issues/141/admin-cambios-inesperados-en-incrementos) ADMIN Cambios inesperados en incrementos por persona
- [#142](https://bitbucket.org/beeam/navicu/issues/142/web-los-inputs-en-el-formulario-de) WEB Los inputs en el formulario de /reservation son angostos en móvil
- [#143](https://bitbucket.org/beeam/navicu/issues/143/extranet-dise-o-de-tabla-de-historial) EXTRANET Diseño de tabla de historial desactualizado
- [#145](https://bitbucket.org/beeam/navicu/issues/145/ascribere-establecimiento-finalizado-al) ASCRIBERE Establecimiento finalizado al 100% pero existen datos errados.
- [#148](https://bitbucket.org/beeam/navicu/issues/148/error-en-el-la-disminuci-n-de-la) Error en el la disminución de la disponibilidad para reserva de múltiples habitaciones
- [#149](https://bitbucket.org/beeam/navicu/issues/149/error-en-galer-a-vista-tablet-y-m-vil-en) Error en galería vista tablet y móvil en la vista de la ficha del establecimiento
- [#151](https://bitbucket.org/beeam/navicu/issues/151/inconsistencias-entre-el-pdf-y-el-correo) Inconsistencias entre el pdf y el correo relacionados a una reserva en extranet
- [#152](https://bitbucket.org/beeam/navicu/issues/152/el-listado-de-afiliados-solo-lista-100) El listado de afiliados solo lista 100 como máximo
- [#153](https://bitbucket.org/beeam/navicu/issues/153/web-solo-aceptar-tdc-dentro-de-conjunto-de) WEB Solo aceptar TDC dentro de conjunto de números válidos
- [#155](https://bitbucket.org/beeam/navicu/issues/155/login-cliente-bot-n-recibir-ofertas) Login cliente botón recibir ofertas
- [#156](https://bitbucket.org/beeam/navicu/issues/156/cliente-home-transferencia-pendiente-de) Cliente HOME transferencia pendiente de pago

## [1.1.11]
### Removed
- Antiguos meta tags en seoTags.html.twig

## [1.1.10] - 2016-05-28
### Added
- Nuevos meta tags enviados por Johan

### Changed
- Archivo .htaccess tomando como sugerencia la ultima respuesta escrita en github acerca de prerender.io la cual es del 4 de abril de 2016      (https://gist.github.com/thoop/8072354). Luego esta modificacion tuvo que ser retirada.

## [1.1.9] - 2016-05-27
### Changed
- Se elimino User-agent: * Disallow: /resetting del robot.txt

## [1.1.8] - 2016-05-26
### Changed
- Nombre de los tipos de camas Doble y King

### Fixed
- Consulta de las disponibilidades en la ficha del establecimiento erronea en la búsqueda


## [1.1.7] - 2016-05-24
### Added
- Bundle Yuccaprerender
- Bundle Prerender-node para verificar el render de navicu en el ambiente de desarrollo local
- Código en .htacces para hacer uso del servicio de prerender.io

### Changed
- docker-composer.yml para poder visualizar el prerenderizado de manera local

### Removed
- URL https:www.navicu/reseeting del sitemap.xml

## [1.1.6] - 2016-05-23
### Changed
- Logo de favicon.ico
- Login de cliente

## [1.1.5] - 2016-05-23
### Fixed
- [#145](https://bitbucket.org/beeam/navicu/issues/145/ascribere-establecimiento-finalizado-al) ASCRIBERE Establecimiento finalizado al 100% pero existen datos errados.

- Modificando la validación de la antelación en el motor de búsqueda

## [1.1.4] - 2016-05-20
### Changed
- Logo en cabeceras de cliente, Extranet, Ascribere y Admin
- Tags en cabecera para mejorar SEO
- Formulario de inicio de sesión en cliente en versión móvil

## [1.1.3] - 2016-05-19
### Fixed
- Ausencia de validación de antelación en el cálculo de tarifas

## [1.1.2] - 2016-05-16
### Fixed
- Modificaciones menores varias

## [1.1.1] - 2016-05-16
### Fixed
- [#144](https://bitbucket.org/beeam/navicu/issues/144/web-tarifas-tachadas-igual-a-mejor-oferta) WEB Tarifas tachadas igual a Mejor Oferta en búsqueda por fecha

## [1.1.0] - 2016-05-13
### Added
- Eliminar Daily Rooms y Daily Packs desde Admin
- Ver los detalles de una reserva como Admin en Extranet
- Almacenamiento de tarifa neta y monto de comisión en datos de una reserva

### Changed
- Reservas canceladas ya no se toman en cuenta en la facturacioón total de un establecimiento

### Fixed
- [#139](https://bitbucket.org/beeam/navicu/issues/139/web-mal-c-lculo-de-tarifas-en-pdf-del) WEB Mal cálculo de tarifas en PDF del hotelero
- [#140](https://bitbucket.org/beeam/navicu/issues/140/admin-pack-media-pensi-n-excursi-n-mal) ADMIN Pack "Media pensión + Excursión" mal nombrado
- Modificaciones menores varias

## [1.0.2] - 2016-05-12
### Removed
- Acceso a *landing page* del concurso Dia de las Madres

## [1.0.1] - 2016-05-11
### Fixed
- [#139](https://bitbucket.org/beeam/navicu/issues/139/web-mal-c-lculo-de-tarifas-en-pdf-del) WEB Mal cálculo de tarifas en PDF del hotelero
- [#140](https://bitbucket.org/beeam/navicu/issues/140/admin-pack-media-pensi-n-excursi-n-mal) ADMIN Pack "Media pensión + Excursión" mal nombrado

## [1.0.0] - 2016-04-29
### Added
- Registro de usuario mediante FB, Google y formulario de navicu
- Manejo de sesiones de usuario
- Asignacion de un ejecutivo de venta a un establecimiento temporal o afiliado
- Cambio de ejecutivos de ventas asignados a un estableciemiento
- Landing page a panel de Extranet
- Historial de cambios realizados sobre tarifas y disponibilidades
- Tips con informacion de interes y sugerencias para el hotelero
- Listado de reservas de cada establecimiento en Extranet
- Pack "Media Pensión + Excursión"
- Generación automática de Sitemap.xml

### Changed
- Diseño de la pagina de inicio de navicu
- Diseño de la página de inicio de sesion de Admin

### Fixed
- [#68](https://bitbucket.org/beeam/navicu/issues/68/admin-ocultar-campos-establecimientos) ADMIN ocultar campos establecimientos afiliados
- [#69](https://bitbucket.org/beeam/navicu/issues/69/establecimientos-en-proceso-de-registro) ADMIN establecimientos en proceso de registro ocultar columnas
- [#72](https://bitbucket.org/beeam/navicu/issues/72/proceso-de-registro-y-establecimiento) proceso de registro y establecimiento registrado nº de habitaciones
- [#77](https://bitbucket.org/beeam/navicu/issues/77/versi-n-movil-precios-ficha) versión movil precios ficha establecimiento
- [#78](https://bitbucket.org/beeam/navicu/issues/78/navicucom-extranet) navicu.com/extranet
- [#83](https://bitbucket.org/beeam/navicu/issues/83/opciones-en-listado-de-establecimientos) ADMIN Opciones en listado de establecimientos temporal/afiliados
- [#84](https://bitbucket.org/beeam/navicu/issues/84/agregar-items-a-opciones-en-listado-de) ADMIN Agregar items a opciones en listado de establecimientos afiliados
- [#93](https://bitbucket.org/beeam/navicu/issues/93/pre-resumereservation) Agregar items a opciones en listado de establecimientos afiliados ADMIN
- [#96](https://bitbucket.org/beeam/navicu/issues/96/recuperaci-n-de-claves-desde-distintos) Recuperación de claves desde distintos roles
- [#107](https://bitbucket.org/beeam/navicu/issues/107/admin-establecimiento-completado-100-pero) ADMIN Establecimiento completado 100% pero sin finalizar
- [#109](https://bitbucket.org/beeam/navicu/issues/109/reservation-usuario-registrado-contrase-a) WEB /reservation usuario registrado contraseña errónea
- [#113](https://bitbucket.org/beeam/navicu/issues/113/no-se-distingue-informacion-de-items-en) WEB No se distingue informacion de items en resumen de la reserva
- [#116](https://bitbucket.org/beeam/navicu/issues/116/recordar-nombre-y-c-dula-al-estar-logueado) WEB Recordar nombre y cédula al estar logueado
- [#118](https://bitbucket.org/beeam/navicu/issues/118/quitar-modal-de-t-rminos-y-condiciones-en) WEB Quitar modal de términos y condiciones en la vista de reserva
- [#126](https://bitbucket.org/beeam/navicu/issues/126/admin-se-muestra-el-primer-estado-de-una) ADMIN Se muestra el primer estado de una pre-reserva en Ver Detalle
- [#129](https://bitbucket.org/beeam/navicu/issues/129/contrato-de-afiliaci-n-del-establecimiento) NAVICU CONTRATO DE AFILIACIÓN DEL ESTABLECIMIENTO
- [#134](https://bitbucket.org/beeam/navicu/issues/134/admin-no-se-muestra-el-ejecutivo-de-venta) ADMIN No se muestra el ejecutivo de venta asignado en listado
- [#135](https://bitbucket.org/beeam/navicu/issues/135/web-en-detalle-de-la-reserva-no-se-muestra) WEB En detalle de la reserva no se muestra el N° de Personas

### Fixed
- Modificacion webPack para corregir lo de las imagenes pixeladas del carrusel del home.
