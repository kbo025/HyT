# Archivos

## JavaScript

Todo se encuentra bajo el directorio `admin-2`.

Más específicamente, bajo:

```
src/Navicu/InfrastructureBundle/Resources/public/assets/navicu/angular/admin-2/
```

### index.js

El típico archivo donde se importa todo.

### app.js

Contiene la declaración del módulo Angular.js `nvc.admin`.

Este módulo es el único que se utilizará en todo el módulo de Admin.

### config/*

Archivos de configuración del módulo. Por lo general, son funciones que se ejecutan con los métodos `.config` o `.run` del módulo.

### controllers/*

Controladores.

* Un archivo por controlador.
* El archivo debe tener el mismo nombre que el controlador.
* El nombre del controlador debe estar en formato **PascalCase**, con el sufijo **Ctrl**. Por ejemplo, `MiControladorCtrl`.
* Los controladores no pueden realizar peticiones AJAX directamente; se deben utilizar servicios para ello.

```javascript
import app from '../app';

app.controller('SampleCtrl', ($scope, ServiceExample) => {
    $scope.changeSection('currentSection', 'currentPage');

    ServiceExample.getPage(1).then((items) => {
        $scope.items = items;
    }).catch((err) => {
        $scope.error(err);
    });
});
```

### filters/*

Filtros.

#### Ejemplo

```javascript
import app from '../app';

app.filter('uppercase', () => {
    return function uppercase(input) {
        return input.toUpperCase();
    };
});
```

### services/*

Services, factories... lo que sea que tenga que ver con un modelo.

* Los nombres deben estar en formato **PascalCase**.
* El archivo debe tener el mismo nombre que el modelo (e.g. el archivo `User.js` debe definir un modelo `User`).

Por lo general, deben estar separados por "tipo de objeto". Por ejemplo, un archivo `User.js` encargado de las operaciones con usuarios, `Reservation.js` para las operaciones con reservas... etc.

#### Ejemplo

```javascript
import app from '../app';

app.factory('ServiceExample', ($http) => {
    function getPage(pageNumber) {
        const url = Routing.generate('navicu_service_example');
        const data = {
            page: pageNumber,
        };

        return $http.post(url, data).then(resp => resp.data);
    }

    return {
        getPage,
    };
});
```

# Vistas

## Títulos

El título de una página se puede modificar en el bloque twig `title`.

El layout ya define un título por defecto para el módulo, que en este caso es `Admin`.

Si uno quisiera definir el título de una página, se tendría que redefinir el bloque `title` de forma que incluya tanto él título de la página como el título del módulo:

```
{% block title %}
    Sección | {{ parent() }}
{% endblock title %}
```

El título de la página entonces sería `Sección | Admin`.

## Controladores

Una vista puede tener múltiples controladores.

Si, por ejemplo, en una vista existen varios formularios, cada uno con su botón de "submit", entonces cada formulario debería tener su propio controlador.

Si una vista contiene más de un controlador, entonces debe existir un "controlador de vista" que los contenga a todos.

Por ejemplo, si tenemos:

```html
<main>
    <form ng-controller="Form1Ctrl">
    </form>
    <form ng-controller="Form2Ctrl">
    </form>
</main>
```

... entonces debería existir un controlador que los contenga a todos:

```html
<main ng-controller="FormsViewCtrl">
    <form ng-controller="Form1Ctrl">
    </form>
    <form ng-controller="Form2Ctrl">
    </form>
</main>
```

El controlador "contenedor" debería tener el sufijo `ViewCtrl`.

# Notas

## AJAX

Para las peticiones AJAX se utilizará `$http` en vez de `jQuery`.

Los métodos de `$http` devuelven una `Promise` que, al ser resueltas, devuelven un "objeto de respuesta". Ese objeto tiene una propiedad `data` cuyo contenido es la respuesta del servidor.

```javascript
// Ejemplo de petición GET.
$http.get(url);
```

```javascript
// Ejemplo de petición POST.
$http.post(url, data);
```

En el caso de las peticiones POST, no es necesario convertir la información a JSON; `$http` lo hace de forma automática.

### Ejemplo (login)

Supongamos que tenemos una url `/login` donde enviamos las variables `user` y `password` en un objeto, y el servidor nos devuelve `true` o `false` dependiendo si las credenciales son válidas o no, respectivamente.

La petición de login podría realizarse de una forma similar a esta:

```javascript
var url = '/login';
var data = {
    user: 'xkcd',
    password: 'correct horse battery staple',
};

$http.post(url, data)
    .then(function(resp) {
        // El servidor respondió con `false`.
        if (!resp.data) {
            window.alert('Credenciales inválidas');
            return;
        }

        // Login correcto. Redirigimos al home.
        window.location = '/';
    })
    .catch(function(err) {
        // Mostrar un alert nativo con el mensaje de error.
        window.alert(err.message);
    });
```

... sin embargo, yo prefiero utilizar un `then` por cada "paso". En este caso, utilizaría un `then` para obtener la respuesta del servidor (`resp.data`), y otro `then` para *procesar* esa respuesta.

```javascript
$http.post(url, data)
    .then(function(resp) {
        return resp.data;
    })
    .then(function(validCredentials) {
        // `validCredentials` es el `resp.data` que retornamos 
        // en el `then` anterior.
        
        // TODO: Realizar una acción u otra dependiendo si el
        //       login fue satisfactorio o no.
    })
    .catch(function(err) {
        // TODO: Manejar el error.
    });
```

## Loading

Si se quiere mostrar un indicador de "cargando", entonces se tiene que emitir un evento `busy`. Cuando se termina de cargar, debe emitirse un evento `ready`.

El controlador `ContentCtrl` está preconfigurado para mostrar y ocultar el indicador basándose en esos eventos.

Los eventos se emiten de esta forma:

```javascript
import app from '../app';

app.controller('SampleCtrl', ($scope, Info) => {
    // Muestra el loading.
    $scope.busy();
    
    // Obtiene información de backend utilizando un servicio.
    Info.get().then(function() {
        // Oculta el loading.
        $scope.ready();
    });
});
```
