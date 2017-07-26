const app = require('express')();

app.use(require('morgan')('dev'));

app.use('/', require('./routes'));

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  const err = new Error('Not Found');
  err.status = 404;

  return next(err);
});

// error handlers
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  console.error(err.stack);

  res.set('Content-Type', 'text/css');
  res.send('/*\n' + err.message + '\n*/');
});

module.exports = app;
