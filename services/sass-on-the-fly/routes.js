// Dependencias de la librería estándar.
const path = require('path');
const fs = require('fs');

//
// Dependencias externas.
//

const express = require('express');
const router = express.Router();

const sass = require('node-sass');
const _postcss = require('postcss');
const autoprefixer = require('autoprefixer');

const cleaner = _postcss([ autoprefixer({ add: false, browsers: [] }) ]);

//
// Variables
//

const ASSETS_DIR = path.join(__dirname, 'assets');

// --------------------------------------------------------------------

// Recibe un objeto con los colores, y devuelve una string con variables
// SASS, una por línea.
function generateSassVariables(data) {
  return Object.keys(data).map(function(key) {
    var value = data[key];
    if (key.toLowerCase().indexOf('url') >= 0) {
      value = JSON.stringify(value);
    }
    return '$' + key + ': ' + value + ';';
  }).join('\n');
}

// Define una ruta que recibe el nombre de un módulo, y responde
// con un archivo CSS generado.
router.get('/:module.css', function(req, res, next) {
  var name = req.params.module;
  var minify = false;

  // Si el nombre del archivo termina en `.min.css`, entonces la
  // respuesta debería estar minificada.
  const m = name.match(/^(.+)\.min$/);
  if (m) {
    name = m[1];
    minify = true;
  }

  // Construimos la ruta al archivo que buscamos.
  const file = 'navicu/styles/custom/' + name + '.scss';
  const absFile = path.join(ASSETS_DIR, file);

  // Comprobamos si el archivo existe.
  return fs.exists(absFile, function(exists) {
    // Si el archivo no existe, terminamos con un 404.
    if (!exists) {
      err = new Error('not found');
      err.status = 404;

      return next(err);
    }

    // El archivo existe.

    // Extraemos las variables SASS de la URL de la petición y las exportamos
    // a formato SASS, en una string.
    const sassVariables = generateSassVariables(req.query);

    // Opciones para la librería `node-sass`.
    const options = {
      data: sassVariables + '\n' + '@import "'+file+'";',
      includePaths: [
        ASSETS_DIR,
      ],
    };

    // Renderizamos.
    return sass.render(options, function(err, result) {
      if (err) {
        err.status = 400;
        return next(err);
      }

      // `result` contiene el código CSS generado.

      return (
        // Pasamos el CSS por un post-procesado, para mejorar compatibilidad
        // con navegadores antiguos y eliminar información redundante.
        cleaner.process(result.css)
          .then(function(cleaned) {
            // Minificamos el resultado si es necesario.
            return _postcss([ autoprefixer ].concat(
              minify ? [
                require('cssnano'),
              ] : []
            )).process(cleaned.css);
          })
          .then(function(result) {
            // Enviamos la respuesta al cliente.
            res.set('Content-Type', 'text/css');
            return res.send(result.css);
          })
      );
    });
  });
});

module.exports = router;
