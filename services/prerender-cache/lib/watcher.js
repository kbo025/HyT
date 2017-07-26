const { EventEmitter } = require('events');
const util = require('util');
const crypto = require('crypto');

const pkg = require('../package.json');

const debug = require('debug')(pkg.name + ':lib:watcher');
const request = require('request').defaults({
  gzip: true,
});
const parseXML = require('xml-parser');

const config = require('../config');

const DEFAULT_INTERVAL = config.cache.ttl;

/*
 * `Watcher` comprueba el sitemap cada determinado tiempo
 * y emite un evento cada vez que este cambia.
 */
function Watcher(sitemap, options) {
  const _this = this;

  Reflect.apply(EventEmitter, this, []);

  if (typeof sitemap !== 'string') {
    throw new Error('sitemap must be string');
  }
  if (sitemap.trim() !== sitemap) {
    throw new Error('invalid sitemap url');
  }
  if (!sitemap) {
    throw new Error('sitemap is required');
  }

  if (typeof options === 'undefined' || options === null) {
    options = {};
  }
  if (!Reflect.apply(Object.prototype.hasOwnProperty, options, ['checkEvery'])) {
    options.checkEvery = DEFAULT_INTERVAL;
  }

  const { checkEvery } = options;

  this.sitemap = sitemap;
  this.lastChecked = null;
  this.nextCheckTimeout = null;
  this.checkEvery = checkEvery;
  this.data = null;

  this.on('update', function(data) {
    _this.data = data;
  });
}

util.inherits(Watcher, EventEmitter);

/*
 * `isWatching` devuelve `true` si el watcher se encuentra
 * activo.
 */
Watcher.prototype.isWatching = function() {
  return this.nextCheckTimeout !== null;
};

/*
 * `start` inicia el watcher.
 */
Watcher.prototype.start = function() {
  if (this.isWatching()) {
    return false;
  }

  this.check();

  return true;
};

/*
 * `stop` detiene el watcher.
 */
Watcher.prototype.stop = function() {
  if (this.isWatching()) {
    return false;
  }

  clearTimeout(this.nextCheckTimeout);
  this.nextCheckTimeout = null;

  return true;
};

/*
 * `check` comprueba si el sitemap fue actualizado.
 */
Watcher.prototype.check = function() {
  debug('checking sitemap');

  const _this = this;

  const url = this.sitemap;

  // Función que ejecutaremos al finalizar la comprobación.
  const done = function() {
    debug('next check: %s', new Date(Date.now() + _this.checkEvery).toString());

    clearTimeout(_this.nextCheckTimeout);

    _this.nextCheckTimeout = setTimeout(function() {
      return _this.check();
    }, _this.checkEvery);
  };

  // Realizamos la petición para obtener el sitemap.
  return request.get(url, function(err, response, body) {
    if (err) {
      _this.emit('error', err);

      return done();
    }

    debug('parsing sitemap');

    // Parseamos el sitemap.
    //
    // `body` es una string en formato XML.
    return parseData(body, function(err, data) {
      if (err) {
        _this.emit('error', err);

        return done();
      }

      // Si el sitemap no ha cambiado, no hacemos nada.
      if (_this.data && data.checksum === _this.data.checksum) {
        debug('no changes');

        return done();
      }

      // Avisamos de que el sitemap cambió.
      _this.emit('update', data);

      return done();
    });
  });
};

/*
 * `parseData` devuelve información acerca del código xml suministrado.
 */
function parseData(xml, callback) {
  // Calculamos el hash SHA256 del xml de entrada.
  //
  // Esto nos servirá para comprobar si el sitemap es el mismo o ha
  // sido actualizado.
  const hash = crypto.createHash('sha256');
  hash.update(xml);
  const checksum = hash.digest('hex');

  // Este es el objeto que devolveremos como resultado.
  const data = {
    checksum,
    urlList: [],
  };

  // Parseamos el XML con la librería `xml-parser`.
  const rawData = parseXML(xml);

  data.urlList = rawData.root.children
    // Selecciona todos los nodos `<url>`.
    .filter(function(child) {
      return child.name === 'url';
    })
    .map(function(urlElement) {
      // `urlElement` es un nodo `<url>`.

      // Devolvemos una string con una url.
      return (
        urlElement.children
          // Selecciona los nodos `<loc>`.
          .filter(function(child) {
            return child.name === 'loc';
          })
          // Obtiene el contenido de cada nodo (será una url)
          .map(function(locElement) {
            return locElement.content.trim();
          })
          // Si existe uno o más nodos `<loc>`, selecciona el primero.
          // Si no, devuelve `null`.
          .reduce(function(a, b) {
            return a || b;
          }, null)
      );
    });

  return callback(null, data);
}

exports.Watcher = Watcher;
