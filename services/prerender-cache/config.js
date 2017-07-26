const path = require('path');

module.exports = {
  cache: {
    // Cada cuánto tiempo se debe comprobar el sitemap.
    //
    // En milisegundos.
    ttl: 24 * 60 * 60 * 1000,
  },
  sitemap: {
    // La URL del sitemap.
    url: 'https://www.navicu.com/sitemap',
  },
  service: {
    // Puerto en el que se iniciará este servicio (prerender-cache).
    port: 5555,
  },
  prerender: {
    // Puerto en el que se iniciará el servicio de prerender.
    port: 6666,
    host: 'localhost',
    pidFile: '/tmp/navicu-prerender.pid',
  },
  log: {
    // Rutas de los logs.
    requests: path.join(__dirname, 'log', 'requests.log'),
    errors: path.join(__dirname, 'log', 'errors.log'),
  },
  snapshots: {
    // Ruta donde se almacenará la cache de páginas pre-renderizadas.
    path: '/tmp/navicu-prerender-snapshots',
  },
};
