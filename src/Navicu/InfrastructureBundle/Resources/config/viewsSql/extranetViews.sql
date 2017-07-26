-- Declaracion de vistas para el POF (Pagination, Order, Filter)
DROP VIEW IF EXISTS extPropertyView;

/**
  * Vista con la informacion referente al listado de todos los establecimientos
  * activos, asi como sus datos sobre las reservas que posee
  *
  * @author Isabel Nieto <isabelcnd@gmail.com>
  * @return view
*/
CREATE VIEW extPropertyView AS (SELECT * FROM property);