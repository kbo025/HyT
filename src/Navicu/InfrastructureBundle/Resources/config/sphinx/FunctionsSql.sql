/**
 * Eliminar funciones de antiguo formato de motor de busqueda
 */
DROP FUNCTION IF EXISTS coincidence(integer, character varying);
DROP FUNCTION IF EXISTS continuousavailability(integer, date);
DROP FUNCTION IF EXISTS continuouspackavailability(integer, date);
DROP FUNCTION IF EXISTS continuousroomavailability(integer, date);
DROP FUNCTION IF EXISTS countpropertycity(integer);
DROP FUNCTION IF EXISTS countpropertymunicipality(integer);
DROP FUNCTION IF EXISTS countpropertyparish(integer);
DROP FUNCTION IF EXISTS countpropertystate(integer);
DROP FUNCTION IF EXISTS groupconcatroom(integer);
DROP FUNCTION IF EXISTS idlocationdowntwo(integer);
DROP FUNCTION IF EXISTS locationalfa(integer);
DROP FUNCTION IF EXISTS locationdependency(integer);
DROP FUNCTION IF EXISTS locationdivision(integer);
DROP FUNCTION IF EXISTS locationdownone(integer);
DROP FUNCTION IF EXISTS locationdownthree(integer);
DROP FUNCTION IF EXISTS locationdowntwo(integer);
DROP FUNCTION IF EXISTS roomavailability(integer, date);
DROP FUNCTION IF EXISTS serviceavailability(integer, date);
DROP FUNCTION IF EXISTS updatedailypack();
DROP FUNCTION IF EXISTS updatedailyroom();
DROP FUNCTION IF EXISTS idDestinyProperty(city INT, parent INT, parish int);

/**
 * Función usada para el codigo ALFA de una localidad.
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @param Integer idLocation
 * @return varChar
 */
CREATE OR REPLACE FUNCTION locationAlfa(idLocation INT) RETURNS varchar(80) AS $$
DECLARE
    response varchar;

BEGIN
    select alfa2 into response from location where id = idLocation;

    RETURN response;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar las divicion politica
 * a la cual pertenece una localidad.
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @param Integer idLocation
 * @return Json
 */
CREATE OR REPLACE FUNCTION locationDivision(idLocation INT) RETURNS json AS $$
DECLARE
	aux_array text;
	aux_text text;
	array_response json [];
	current_id int;
	current_lvl int;
	current_parent int;
	aux_cont int;
BEGIN

	SELECT location.id, location.lvl, location.parent_id
	INTO current_id, current_lvl,current_parent
	FROM location WHERE id = idLocation;

	aux_cont = 0;
	aux_array := '{';
	WHILE current_lvl >= 0 LOOP

		aux_text := ('"' || current_lvl || '":{"id":' || current_id ||'}');

		IF aux_cont = 0 THEN
			aux_array := aux_array || aux_text;
		ELSE
			aux_array := aux_array || ',' || aux_text;
		END IF;

		aux_cont := aux_cont + 1;
		idLocation := current_parent;

		SELECT location.id, location.lvl, location.parent_id
		INTO current_id, current_lvl,current_parent
		FROM location WHERE id = idLocation;

	END LOOP;

	aux_array := aux_array || '}';

	return aux_array;

END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar las dependencias
 * a la cual pertenece una localidad.
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @param Integer idLocation
 * @return Json
 */
CREATE OR REPLACE FUNCTION locationDependency(idLocation INT) RETURNS json AS $$
DECLARE
	current_dependency RECORD;
	aux_response text;
	aux_text text;
	aux_count int;
	lvl_dependency int;
BEGIN
	aux_count := 0;
	aux_response := '';

	FOR current_dependency IN SELECT * FROM dependency_location as dl WHERE dl.location_id = idLocation  LOOP

		select l.lvl into lvl_dependency FROM location as l  WHERE l.id = current_dependency.dependency_id;

		aux_text := ('"' || lvl_dependency || '":{"id":' || current_dependency.dependency_id ||'}');

		IF aux_count = 0 THEN
			aux_response := aux_response || aux_text;
			aux_count := aux_count + 1;
		ELSE
			aux_response := aux_response || ',' || aux_text;
		END IF;
	END LOOP;

	return '{' || aux_response || '}';
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar el numero de establecimiento
 * que tiene una localidad.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer idLocation
 * @return Integer
 */
CREATE OR REPLACE FUNCTION countProperty(idLocation INT, lvl INT) RETURNS int AS $$
DECLARE
    contnt RECORD;
    aux int default 0;
    count int default 0;
    response int default 0;
BEGIN
    IF lvl = 4 or lvl =5 THEN
      FOR contnt IN select location_id from dependency_location where dependency_id = idLocation LOOP
          aux = countProperty(contnt.location_id, 1);
          response = response + aux;
      END LOOP;
      return response;
    ELSE
      select count(*) into count from location where parent_id = idLocation;

      IF count = 0 THEN
          select count(1) into response from location, property	where property.location = location.id and location.id = idLocation;
      END IF;

      FOR contnt IN select l.id, l.lvl from location as l where parent_id = idLocation LOOP
          aux = countProperty(contnt.id, contnt.lvl);
          response = response + aux;
      END LOOP;

      RETURN response;
    END IF;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar dentro de un establecimiento
 * la condición validad para el calculo del iva.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer idProperty
 * @return boolean
 */
CREATE OR REPLACE FUNCTION includeIva(propertyId INT) RETURNS boolean AS $$
DECLARE
    response boolean default 't';
    varTax boolean;
    varRateType int;
BEGIN
    select tax, rate_type into varTax, varRateType from property where id = propertyId;

    if varRateType = 2 and varTax = 'f' then
        response  = 'f';
    end if;
    RETURN response;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar dentro de un establecimiento
 * todas sus habitaciones y concatenarlas en un array.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer idProperty
 * @return varchar
 */
CREATE OR REPLACE FUNCTION roomsGroup(idProperty INT) RETURNS varchar AS $$
DECLARE
    varRooms varchar;
BEGIN
    select array_agg(room.id) into varRooms from property, room where property.id = room.property_id and property.active = 't' and property.id = idProperty and room.is_active='t';
    RETURN varRooms;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para buscar dentro de una habitación
 * todos sus servicios (Pack) y concatenarlas en un array.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer idRoom
 * @return varchar
 */
CREATE OR REPLACE FUNCTION packagesGroup(idRoom INT) RETURNS varchar AS $$
DECLARE
    varPackages varchar;
BEGIN
    select array_agg(pack.id) into varPackages from property, room, pack where property.id = room.property_id and room.id = pack.room_id and room.id = idRoom and property.active = 't' and room.is_active='t';
    RETURN varPackages;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para ver el alcance o disponibilidad
 * de un dailyRoom, tomando en cuenta las restricciones para
 * ello.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer var_idRoom
 * @return Integer
 */
CREATE OR REPLACE FUNCTION continuousRoomAvailability(var_room int, var_date date) RETURNS int AS $$
DECLARE
    ban int default 0;
    c int default 0;
    stopSell boolean;
    var_availability int;
BEGIN

    WHILE ban = 0 LOOP
        select stop_sell, availability into stopSell, var_availability from daily_room where room_id = var_room and date = var_date;

        if stopSell = 't' or var_availability = 0 or stopSell is null then
            ban  = 1;
        else
            c = c + 1;
        end if;
        
        select CAST(var_date AS DATE) + CAST('1 days' AS INTERVAL) into var_date;
    END LOOP;

    RETURN c;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para ver el alcance o disponibilidad
 * de un dailyPack, tomando en cuenta las restrcciones para
 * ello.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer var_idPack
 * @param Date var_date
 * @return Integer
 */
CREATE OR REPLACE FUNCTION continuousPackAvailability(var_idPack int, var_date date) RETURNS int AS $$
DECLARE
    ban int default 0;
    c int default 0;
    closeOut boolean;
    specificAvailability int;
BEGIN

    WHILE ban = 0 LOOP
        select close_out, specific_availability into closeOut, specificAvailability from daily_pack where pack_id = var_idPack and date = var_date and is_completed;

        if closeOut = 't' or specificAvailability = 0 or closeOut is null then
            ban  = 1;
        else
            c = c + 1;
        end if;

        select CAST(var_date AS DATE) + CAST('1 days' AS INTERVAL) into var_date;
    END LOOP;

    RETURN c;
END;
$$ LANGUAGE plpgsql;

/**
 * Función usada para ver la disponibilidad de un dailyRoom,
 * tomando en cuenta la disponibilidad de al menos un dailyPack
 * para una fecha dada.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @param Integer var_idPack
 * @param Date var_date
 * @return Integer
 */
CREATE OR REPLACE FUNCTION serviceAvailability(id_room INT, var_date Date) RETURNS int AS $$
DECLARE
    response int default 0;
BEGIN
    select count(daily_pack.id) into response from daily_pack, pack, room where room.id = pack.room_id and pack.id = daily_pack.pack_id and date = var_date and room.id = id_room and is_completed='t';
    RETURN response;
END;
$$ LANGUAGE plpgsql;

/**
 * Función retorna un arreglo(int) de id de
 * los dailyPack que deben ser actualizados
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @return query
 */

CREATE OR REPLACE FUNCTION updatedailypack()
    RETURNS setof integer AS
$BODY$
BEGIN
	RETURN QUERY SELECT dp.id from daily_pack as dp
		JOIN (select p.id as id, max(dp.date) as max_date
		FROM pack as p, daily_pack as dp
		WHERE p.id =  dp.pack_id AND dp.last_modified >= (current_timestamp - interval '5 minute') group by p.id) as aux
	ON dp.date <= aux.max_date and dp.date >= current_date and dp.pack_id = aux.id;
END;
$BODY$
LANGUAGE plpgsql;


/**
 * Función retorna un arreglo(int) de id de
 * los dailyRoom que deben ser actualizados
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @return query
 *
 */
CREATE OR REPLACE FUNCTION updatedailyroom()
    RETURNS setof integer AS
$BODY$
BEGIN
    RETURN QUERY SELECT dr.id from daily_room as dr
	JOIN (select r.id as id, max(dr.date) as max_date
		from room as r, daily_room as dr
		where r.id =  dr.room_id and dr.last_modified >= (current_timestamp - interval '5 minute') group by r.id) as aux
	ON dr.date <= aux.max_date and dr.date >=  current_date and dr.room_id = aux.id;
END;
$BODY$
LANGUAGE plpgsql;

/**
 * Función retorna un arreglo(int) de id de los distintos destinos
 * habilitados por los establecimientos dentro de la BD.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @return query
 */
CREATE OR REPLACE FUNCTION idDestinyProperty(city INT, parent INT, parish int) RETURNS integer AS $$
DECLARE

    response int;
    slugs varchar;

BEGIN

		IF city is not null THEN
			select id into response from location where id = city;
			return response;

		END IF;

		select slug, id into slugs, response from location where id = parish;
		IF slugs ='tucacas' or slugs ='los-roques' or slugs ='colonia-tovar' or slugs ='choroni' or slugs ='ocumare-de-la-costa' THEN
			select id into response from location where id = response;
			return response;

		END IF;


		select parent_id, slug into response, slugs from location where id = parent;
		IF slugs ='tucacas' or slugs ='los-roques' or slugs ='colonia-tovar' or slugs ='choroni' or slugs ='ocumare-de-la-costa' THEN
			return parent;
		END IF;
		return response;


END;
$$ LANGUAGE plpgsql;