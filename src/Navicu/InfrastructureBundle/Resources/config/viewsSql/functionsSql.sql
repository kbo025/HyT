/**
 * Funciones necesarias para la implementacion y funcionamiento adecuado de las vistas
 */
DROP FUNCTION get_email_owner_property(var_propertyId int);
DROP FUNCTION get_temp_property_progress(progress text);

/**
 * Funcion encargada de devolver el email de un owner property dado el id del property
 *
 * @author Isabel Nieto. <isabelcnd@gmail.com>
 * @return query
 */
CREATE OR REPLACE FUNCTION get_email_owner_property(var_propertyId int) RETURNS text AS $$
DECLARE
    response text default 0;
BEGIN
   select
      (select f.email from owner_profile as op
         inner join fos_user as f
         on f.id = op.user_id
         where op.id = opp.ownerprofile_id
      ) into response
   from owner_property as opp
   where var_propertyId = opp.property_id
   order by opp.property_id asc;

   RETURN response;
END;
$$ LANGUAGE plpgsql;

/**
 * Funcion encargada de obtener el progreso del establecimiento a partir del parametro progress
 * existente en la tabla de "temp_owner" campo "progress"
 *
 * @author Isabel Nieto <isabelcnd@gmail.com>
 */
CREATE OR REPLACE FUNCTION get_temp_property_progress(progress text) RETURNS INTEGER AS
$$
DECLARE
	response INTEGER DEFAULT 0;
	middle_array varchar;
BEGIN
	middle_array := TRIM(BOTH '[]' FROM progress);
	FOR i IN 1..8 LOOP
		IF (CAST (SPLIT_PART(middle_array, ',', i) AS INTEGER) = 1 )
		THEN
			response := response + 17;

		END IF;
	END LOOP;
	IF (response >= 100)
	THEN
		RETURN 100;
	END IF;
	RETURN response;
END
$$
LANGUAGE 'plpgsql';