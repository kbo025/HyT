const pkg = require('./package.json');

const app = require('express')();
const debug = require('debug')(pkg.name + ':app');
const rm = require('rimraf');

const { clear, getSnapshot } = require('./lib/prerender');
const { Watcher } = require('./lib/watcher');

const config = require('./config');

const watcher = new Watcher(config.sitemap.url);

// Al iniciar el servidor, comenzamos a observar el sitemap.
process.nextTick(function() {
  watcher.start();
});

// Esta función se ejecuta cada vez que el sitemap cambia.
//
// El proceso va así: primero borramos todos los archivos de la cache,
// y luego procedemos a pre-renderizar todas las páginas mencionadas
// en el sitemap, una por una.
watcher.on('update', function(data) {
  debug('cache update triggered');

  // `urlList` es un array de strings.
  //
  // Contiene las urls que están presentes en el sitemap.
  const { urlList } = data;

  // Borramos todos los archivos de la cache.
  const clearCachePromise = new Promise(function(resolve, reject) {
    debug('recreating promises cache');
    clear();

    debug('deleting cached html files');

    return rm(`${config.snapshots.path}/*.html`, function(err) {
      if (err) {
        return reject(err);
      }

      return resolve();
    });
  });

  // Después de que todos los archivos de la cache estén borrados,
  // recorremos la lista de urls y las pre-renderizamos.
  const promise = urlList.reduce(function(promise, url) {
    return promise.then(function() {
      return new Promise(function(resolve) {
        return getSnapshot(url, function(err, result) {
          if (err) {
            debug(err.stack);

            return resolve(null);
          }

          const { file } = result;

          return resolve(file);
        });
      });
    });
  }, clearCachePromise);

  // Función que ejecutamos al finalizar todo el proceso.
  promise.then(function() {
    debug('cache update finished');
  });
});

// Middleware de express.
app.use(require('morgan')('dev'));
app.use(require('compression')());

// Función encargada de recibir las peticiones HTTP y enviar las
// respuestas correspondientes.
//
// Suponiendo que la url de este servicio sea `http://localhost:5555`,
// entonces una petición típica sería:
//
//     http://localhost:5555/https://www.navicu.com/
//
// Es decir, recibiremos peticiones cuya ruta será una url completa.
//
//     req.url === '/https://www.navicu.com/'
app.use(function(req, res, next) {
  // URL que vamos a pre-renderizar.
  let targetUrl = req.url;

  // Eliminamos el slash inicial.
  if (targetUrl.indexOf('/') === 0) {
    targetUrl = targetUrl.substr(1);
  }

  const qidx = targetUrl.indexOf('?');
  if (qidx >= 0) {
    targetUrl = targetUrl.substr(0, qidx);
  }

  // Si no tenemos ninguna url respondemos con un error.
  if (!targetUrl) {
    const err = new Error('Invalid url');
    err.status = 400;

    return next(err);
  }

  // Esta función recibe la ruta de un archivo (o un error), y produce
  // una respuesta dependiendo del caso.
  function callback(err, file) {
    if (err) {
      return next(err);
    }

    return res.sendFile(file);
  }

  // Comprobamos si la petición tiene el header `X-Prerender-Refresh`.
  //
  // Si lo tiene, entonces forzamos un pre-renderizado de la url que
  // nos pide (osea, ignoramos la cache).
  //
  // Si no lo tiene, intentamos responder utilizando la cache.
  let forceReload = false;
  if (parseInt(req.get('x-prerender-refresh'), 10) === 1) {
    forceReload = true;
  }

  // Pasamos una url y obtenemos la ruta del archivo donde se guardó
  // el resultado.
  //
  // Dependiendo de las opciones anteriores, se utilizará o no la cache.
  return getSnapshot(targetUrl, forceReload, callback);
});

// Esta función captura cualquier petición que no coincida con ninguna
// ruta válida.
app.use(function(req, res, next) {
  const err = new Error('Not Found');
  err.status = 404;

  return next(err);
});

// Esta función se encarga de producir una respuesta si ocurrió algún
// error.
//
// eslint-disable-next-line no-unused-vars
app.use(function(err, req, res, next) {
  const status = err.status || 500;

  res.status(err.status || 500);

  if (status >= 500 && status <= 599) {
    debug(err.stack);
  }

  return res.send(err.message);
});

module.exports = app;
