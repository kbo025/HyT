-- Declaracion de vistas para el POF (Pagination, Order, Filter)
DROP VIEW IF EXISTS admin_property_view;
DROP VIEW IF EXISTS admin_affiliated_aavv_view;
DROP VIEW IF EXISTS admin_property_detail_resevation_view;
DROP VIEW IF EXISTS admin_reservation_list_view;
DROP VIEW IF EXISTS admin_temp_property_view;
DROP VIEW IF EXISTS admin_property_without_availability_view;
DROP VIEW IF EXISTS admin_all_user_view;
DROP VIEW IF EXISTS admin_client_user_view;
DROP VIEW IF EXISTS admin_aavv_user_view;
DROP VIEW IF EXISTS admin_owner_profile_view;
DROP VIEW IF EXISTS admin_nvc_profile_view;

/**
  * Vista con la informacion referente al listado de todos los establecimientos
  * activos, asi como sus datos sobre las reservas que posee
  *
  * @author Isabel Nieto <isabelcnd@gmail.com>
  * @return view
*/
CREATE VIEW admin_property_view AS (
    SELECT
        p.id AS id,
        p.slug AS slug,
        p.name AS name,
        DATE (p.join_date) AS join_date,
        nvc_prof.id AS nvc_profile_id,
        nvc_prof.full_name AS nvc_profile_name,
        (p.discount_rate * 100) AS discount_rate,
        agrmnt.credit_days AS credit_days,
        get_email_owner_property(p.id) AS admin_email,
        setweight(to_tsvector(COALESCE(p.name,'')),'A') || ' ' ||
        setweight(to_tsvector(unnacent(COALESCE(nvc_prof.full_name,'')))),'B') || ' ' ||
        setweight(to_tsvector(COALESCE(get_email_owner_property(p.id),'')),'C') || ' ' ||
        setweight(to_tsvector(COALESCE(to_char(p.join_date, 'YYYYMMDD'),'')),'C') || ' ' ||
        setweight(to_tsvector(COALESCE(
        (SELECT l_out.title
         FROM location AS l_out
         WHERE l_out.id =
            (SELECT idDestinyProperty(l.city_id,l.parent_id, l.id) AS idDestiny
            FROM location AS l
            WHERE p.location = l.id
            )
        ),'')),'C') AS search_vector,
        (SELECT sum(r.total_to_pay)
            FROM reservation AS r
            WHERE r.property_id = p.id
        ) AS total_to_pay,
        (SELECT count(r.id)
            FROM reservation AS r
            WHERE r.property_id = p.id
        ) AS number_reservation,
        (SELECT title
            FROM location AS l_out
            WHERE l_out.id =
            (SELECT idDestinyProperty(l.city_id,l.parent_id, l.id) AS idDestiny
                FROM location AS l
                WHERE p.location = l.id)
        ) AS location
    FROM property AS p
    LEFT JOIN nvc_profile AS nvc_prof
    ON p.nvc_profile_id = nvc_prof.id
    LEFT JOIN agreement AS agrmnt
    ON p.id = agrmnt.property_id
    ORDER BY admin_email ASC
);


/**
  * Vista con la informacion referente al listado de todos los usuarios
  * registrado en el sistema.
  *
  * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
  * @author Currently Working: Joel D. Requena P.
  * @return view
*/
CREATE VIEW admin_all_user_view AS (
    SELECT

        id,
        user_name,
        email,
        enabled,
        data_info::json->>0 as role,
        data_info::json->>1 as phone,
        setweight(to_tsvector(COALESCE(user_name,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(email,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(data_info::json->>0,'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE(data_info::json->>1,'')),'C') as search_vector

    FROM
    (
        SELECT

            fu.id AS id,
            fu.username AS user_name,
            fu.email,
            fu.enabled,
            CASE
                WHEN
                    op.id IS NOT NULL
                THEN
                    '["Hotelero"' || ',"' || COALESCE(op.phones,' ')|| '"]'
                WHEN
                    cp.id IS NOT NULL
                THEN
                    '["Cliente"' || ', "' || COALESCE(cp.phone,' ')|| '"]'
                WHEN
                    ap.id IS NOT NULL
                THEN
                    '["AAVV"' || ',"' || COALESCE(ap.phone,' ')|| '"]'
                WHEN
                    np.id IS NOT NULL
                THEN
                    '["Admin"' || ',"' || COALESCE(np.cell_phone,' ')|| '"]'
            END AS data_info

        FROM

         fos_user AS fu

        LEFT JOIN owner_profile AS op
            ON op.user_id = fu.id
        LEFT JOIN cliente_profile AS cp
            ON cp.user_id = fu.id
        LEFT JOIN aavv_profile AS ap
            ON fu.aavv_profile_id = ap.id
        LEFT JOIN tempowner AS tp
            ON tp.user_id = fu.id
        LEFT JOIN nvc_profile AS np
            ON np.user_id = fu.id

        WHERE

            tp.id IS NULL AND
            (
                op.id IS NOT NULL OR
                cp.id IS NOT NULL OR
                ap.id IS NOT NULL OR
                tp.id IS NOT NULL OR
                np.id IS NOT NULL
            )

    ) AS main_sql
    ORDER BY role
);


/**
  * Vista con la informacion referente al listado de todos los usuarios
  * de tipo clientes registrado en el sistema.
  *
  * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
  * @author Currently Working: Joel D. Requena P.
  * @return view
*/
CREATE VIEW admin_client_user_view AS (
    SELECT

        fu.id AS id,
        fu.username AS user_name,
        fu.email,
        fu.enabled,
        cp.phone,
        country.title as country,
        state.title as state,
        CASE
            WHEN
                fu.disable_advance IS NULL
            THEN
                FALSE
            ELSE
                TRUE
        END AS disable_advance,
        setweight(to_tsvector(COALESCE(fu.username,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(fu.email,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(cp.phone,'')),'C') || ' ' ||
        setweight(to_tsvector(COALESCE(state.title,'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE(country.title,'')),'B') as search_vector

    FROM

        fos_user AS fu

    LEFT JOIN cliente_profile AS cp
        ON cp.user_id = fu.id
    LEFT JOIN location AS country
        ON country.id = cp.country
    LEFT JOIN location AS state
        ON state.id = cp.location_id
    WHERE
        cp.user_id IS NOT NULL

);

/**
  * Vista con la informacion referente al listado de todos los usuarios
  * de tipo hotelero registrado en el sistema.
  *
  * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
  * @author Currently Working: Joel D. Requena P.
  * @return view
*/
CREATE VIEW admin_owner_profile_view AS (
    SELECT

        fu.id AS id,
        fu.username AS user_name,
        fu.email,
        op.phones,
        loc.title as country,
        fu.enabled,
        setweight(to_tsvector(COALESCE(fu.username,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(fu.email,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(op.phones,'')),'C') || ' ' ||
        setweight(to_tsvector(COALESCE(loc.title,'')),'B') as search_vector

    FROM

        fos_user AS fu

    LEFT JOIN owner_profile AS op
        ON op.user_id = fu.id
    LEFT JOIN location AS loc
        ON op.location_id = loc.id
    WHERE
        op.user_id IS NOT NULL

);


/**
  * Vista con la informacion referente al listado de todos los usuarios
  * de tipo administrador registrado en el sistema.
  *
  * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
  * @author Currently Working: Joel D. Requena P.
  * @return view
*/
CREATE VIEW admin_nvc_profile_view AS (
    SELECT

        fu.id AS id,
        fu.username AS user_name,
        fu.email,
        np.cell_phone as phone,
        fu.enabled,
        position.code as position,
        dpto.code as departament,
        setweight(to_tsvector(COALESCE(fu.username,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(fu.email,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(np.cell_phone,'')),'C') || ' ' ||
        setweight(to_tsvector(COALESCE(position.code,'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE(dpto.code,'')),'B') as search_vector

    FROM

        fos_user AS fu

    LEFT JOIN nvc_profile AS np
        ON np.user_id = fu.id
    LEFT JOIN departament AS position
        ON np.departament_id = position.id
    LEFT JOIN departament AS dpto
        ON position.parent_id = dpto.id
    WHERE
        np.user_id IS NOT NULL

);
/**
  * Vista con la informacion referente al listado de usuarios de
  * de tipo agencia de viaje registrados en el sistema.
  *
  * @author Alejandro Conde. <adcs2008@gmail.com>
  * @author Currently Working: Alejandro Conde
  * @return view
*/
CREATE VIEW admin_aavv_user_view AS (
  SELECT

    fu.id AS id,
    fu.username AS user_name,
    fu.email,
    fu.enabled,
    ap.phone,
    location.title as city,
    state.title as state,
    setweight(to_tsvector(COALESCE(fu.username,'')),'A') || ' ' ||
    setweight(to_tsvector(COALESCE(fu.email,'')),'A') || ' ' ||
    setweight(to_tsvector(COALESCE(ap.phone,'')),'C') || ' ' ||
    setweight(to_tsvector(COALESCE(location.title,'')),'B') || ' ' ||
    setweight(to_tsvector(COALESCE(location.title,'')),'B') as search_vector

  FROM

    fos_user AS fu

    LEFT JOIN aavv_profile AS ap
      ON ap.id = fu.aavv_profile_id
    LEFT JOIN location AS location
      ON location.id = ap.location_id
    left join location as state
      on state.id = location.parent_id
  WHERE
    fu.aavv_profile_id IS NOT NULL
);

/**
  * Ejemplo para el uso de las vistas empleando la busqueda fulltext de postgresql
  */
/*SELECT *
FROM adminPropertyView
WHERE search_vector @@ to_tsquery('spanish','maria:* | sunsol:*')
ORDER BY ts_rank(search_vector, to_tsquery('spanish','maria:* | sunsol:*') ) desc, id desc
LIMIT 10 OFFSET 0*/

/**
  * Vista con la informacion referente al listado de agencias de viajes
  * afiliadas.
  *
  * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
  * @return view
*/

CREATE VIEW admin_affiliated_aavv_view AS (
  SELECT
      aavv.id AS id,
      aavv.slug AS slug,
      commercial_name AS name,
      registration_date AS join_date,
      aavv.public_id AS public_id,
      aavv.credit_available AS availability_credit,
      CASE WHEN
          l.city_id IS NULL
      THEN
          (SELECT
              title
          FROM
              location
          WHERE
              id = l.id)
      WHEN l.city_id IS NOT NULL
      THEN
          (SELECT
              title
          FROM
              location
          WHERE
              id = idDestinyProperty(l.city_id,l.parent_id, l.id))
      END AS city,
      setweight(to_tsvector(
          'spanish',
          coalesce(aavv.public_id,'')),'A')
          || ' ' ||
      setweight(to_tsvector(
          coalesce(
              CASE WHEN
                  l.city_id IS NULL
              THEN
                  (SELECT
                      title
                  FROM
                      location
                  WHERE
                      id = l.id)
              WHEN l.city_id IS NOT NULL
              THEN
                  (SELECT
                      title
                  FROM
                      location
                  WHERE
                      id = idDestinyProperty(l.city_id,l.parent_id, l.id))
              END
          ,'')),'B')
          || ' ' ||
      setweight(to_tsvector(
          coalesce(commercial_name,'')
      ),'C') AS search_vector,
      sum(invoice.total_amount) AS invoices_accumulated,
      sum(CASE WHEN
          invoice.date >= date_trunc('month',current_date)
      THEN
          invoice.total_amount
      WHEN
          invoice.date <= date_trunc('month',current_date)
      THEN
          0
      END ) AS invoices_month
  FROM
      aavv
  LEFT JOIN aavv_address AS address
      ON aavv.id = address.aavv_id
  LEFT JOIN location AS l
      ON address.location_id = l.id
  LEFT JOIN aavv_invoice AS invoice
      ON invoice.aavv_id = aavv.id
  WHERE
      address.type_address = 2 AND
      status_agency IN (2,3)
      GROUP BY aavv.id, l.city_id, l.id, search_vector
);

/**
  * Vista con la informacion detallada de las reservas del establecimiento
  *
  * @author Isabel Nieto <isabelcnd@gmail.com>
  * @return view
*/
CREATE VIEW admin_property_detail_resevation_view AS (
SELECT  rs.id,
    p.slug AS slug,
    to_char(rs.date_check_in, 'DD-MM-YYYY') AS date_check_in,
    to_char(rs.date_check_out,'DD-MM-YYYY') AS date_check_out,
    CASE
        WHEN rs.status = 0
        THEN 'Prereserva'
        WHEN rs.status = 1
        THEN 'Por confirmar'
        WHEN rs.status = 2
        THEN 'Confirmada'
        WHEN rs.status = 3
        THEN 'Cancelada'
        ELSE 'Unknown'
    END AS status,
    reservation_rooms.number_rooms AS number_rooms,
    reservation_rooms.public_id AS public_id,
    client_profile.full_name AS full_name,
    (rs.total_to_pay * (1 - rs.discount_rate)) AS net_rate,
    rs.total_to_pay AS total_to_pay,
    rs.discount_rate AS discount_rate,
    setweight(to_tsvector(COALESCE(reservation_rooms.public_id,'')),'A') || ' ' ||
    setweight(to_tsvector(COALESCE(client_profile.full_name,'')),'A') || ' ' ||
    setweight(to_tsvector(unaccent(COALESCE(p.name,''))),'A') || ' ' ||
    setweight(to_tsvector(COALESCE(to_char(rs.date_check_in, 'YYYYMMDD'),'')),'B') || ' ' ||
    setweight(to_tsvector(COALESCE(to_char(rs.date_check_out, 'YYYYMMDD'),'')),'B') || ' ' ||
    setweight(to_tsvector(COALESCE(CASE
                                      WHEN rs.status = 0
                                      THEN 'Prereserva'
                                      WHEN rs.status = 1
                                      THEN 'Por confirmar'
                                      WHEN rs.status = 2
                                      THEN 'Confirmada'
                                      WHEN rs.status = 3
                                      THEN 'Cancelada'
                                      ELSE 'Unknown'
                                    END
    ,'')),'C') AS search_vector
FROM reservation AS rs
JOIN (
    SELECT rr.public_id AS public_id,
        SUM(r_pk.number_rooms) AS number_rooms
    FROM reservation AS rr
    LEFT JOIN reservation_pack AS r_pk
    ON rr.id = r_pk.reservation_id
    LEFT JOIN property AS p
    ON rr.property_id = p.id
    GROUP BY rr.public_id
) AS reservation_rooms
ON rs.public_id = reservation_rooms.public_id
LEFT JOIN cliente_profile AS client_profile
ON rs.client_id = client_profile.id
LEFT JOIN property AS p
ON p.id = rs.property_id
);

/*
 * Vista con la informacion referente al listado de reservaciones
 * en el sistema.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @return view
 */
CREATE VIEW admin_reservation_list_view  AS (
  SELECT
    res.id as id,
    res.status,
    (select alfa2 from location where id = (select root_id from location where id = p.location)) as ceco,
    CASE WHEN
      res.aavv_reservation_group_id IS NULL
      THEN
        'Web'
    WHEN res.aavv_reservation_group_id IS NOT NULL
      THEN
        'AAVV'
    END AS type,
    p.name as name_property,
    CASE WHEN
      l.city_id IS NULL
      THEN
        l.title
    WHEN l.city_id IS NOT NULL
      THEN
        city.title
    END AS city,
    res.public_id,
    date(res.reservation_date) as create_date,
    date(res.reservation_date + interval '24 hour') as due_date,
    res.date_check_in as check_in,
    res.date_check_out as check_out,
    cp.full_name as client_name,
    CASE
      WHEN res.payment_type = 1
      THEN
        'BCO'
      WHEN res.payment_type = 2
      THEN
        'TRF'
      WHEN res.payment_type = 3
      THEN
        'STR'
      WHEN res.payment_type = 4
      THEN
        'TRF'
      WHEN res.payment_type = 5
      THEN
        'TAC'
      WHEN res.payment_type = 6
      THEN
        'PYZ'
    END AS mp,
    CASE
      WHEN res.currency_convertion_information::json->>'alphaCurrency' IS NULL
      THEN
        'VEF'
      WHEN res.currency_convertion_information::json->>'alphaCurrency' IS NOT NULL
      THEN
        res.currency_convertion_information::json->>'alphaCurrency'
    END AS currency,
    total_to_pay,
    date(rch.date) as status_date,
    setweight(to_tsvector(
                  'spanish',
                  coalesce(p.name,'')),'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(
                      CASE WHEN
                        l.city_id IS NULL
                        THEN
                          l.title
                      WHEN l.city_id IS NOT NULL
                        THEN
                          city.title
                      END,'')),
              'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(res.public_id,'')),'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(to_char(date(res.reservation_date), 'YYYYMMDD'),'')),'B')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(to_char(date(res.reservation_date + interval '24 hour'), 'YYYYMMDD'),'')),'B')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(to_char(res.date_check_in, 'YYYYMMDD'),'')),'B')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(to_char(res.date_check_out, 'YYYYMMDD'),'')),'B')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(cp.full_name,'')),'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(to_char(date(rch.date), 'YYYYMMDD'),'')),'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(
                    CASE WHEN
                    res.aavv_reservation_group_id IS NULL
                    THEN
                      'Web'
                    WHEN res.aavv_reservation_group_id IS NOT NULL
                        THEN
                        'AAVV'
                    END,'')),
              'A')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(
                      CASE
                              WHEN res.payment_type = 1
                                 THEN
                                   'BCO'
                               WHEN res.payment_type = 2
                                 THEN
                                   'TRF'
                               WHEN res.payment_type = 3
                                 THEN
                                   'STR'
                               WHEN res.payment_type = 4
                                 THEN
                                   'TRF'
                               WHEN res.payment_type = 5
                                 THEN
                                   'TAC'
                               WHEN res.payment_type = 6
                                 THEN
                                   'PYZ'
                               END,'')),
              'B')
    || ' ' ||
    setweight(to_tsvector(
                  'spanish',
                  coalesce(
                      CASE
                        WHEN res.currency_convertion_information::json->>'alphaCurrency' IS NULL
                          THEN
                            'VEF'
                        WHEN res.currency_convertion_information::json->>'alphaCurrency' IS NOT NULL
                          THEN
                            res.currency_convertion_information::json->>'alphaCurrency'
                      END,'')),
              'A')
      as search_vector,
    rg.aavv_id

  FROM
    reservation as res
    LEFT JOIN property AS p
      ON res.property_id = p.id
    LEFT JOIN location AS l
      ON p.location = l.id
    LEFT JOIN location AS city
      ON l.city_id = city.id
    LEFT JOIN cliente_profile AS cp
      ON res.client_id = cp.id
    LEFT JOIN reservation_change_history AS rch
      ON res.current_state = rch.id
    LEFT JOIN reservation_group AS rg
      ON res.aavv_reservation_group_id = rg.id
);

/**
 * Vista encargada de construir los establecimientos en proceso de registro
 * @author: Isabel Nieto
 * @version: 26-01-2017
 */
CREATE VIEW admin_temp_property_view AS (
    SELECT
        temp_owner.id AS id,
        temp_owner.propertyform::json->>'slug' AS slug,
        temp_owner.slug AS username,
        temp_owner.propertyform::json->>'name' AS name,
        temp_owner.propertyform::json->>'phones' AS phones,
        CAST (temp_owner.terms_and_conditions_info::json->>'discount_rate' AS REAL) AS discount_rate,
        CASE
            WHEN TRIM (BOTH '\"' FROM CAST(temp_owner.propertyform::json#>'{contacts,0}'#>'{name}' AS VARCHAR) ) LIKE '%null%'
            THEN
                NULL
            ELSE
                TRIM (BOTH '\"' FROM CAST(temp_owner.propertyform::json#>'{contacts,0}'#>'{name}' AS VARCHAR) )
        END AS contact_name,
        (
            SELECT acc.title
            FROM accommodation AS acc
            WHERE acc.id = CAST (temp_owner.propertyform::json->>'accommodation' AS INTEGER)
        ) AS accommodation_title,
        (
            SELECT l_out.title
            FROM location AS l_out
            WHERE l_out.id =
            (
                SELECT idDestinyProperty(l.city_id,l.parent_id, l.id) AS idDestiny
                FROM location AS l
                WHERE CAST (temp_owner.propertyform::json->>'location' AS INTEGER) = l.id
            )
        ) AS location,
        nvc.full_name AS nvc_profile_name,
        nvc.id AS nvc_profile_id,
        DATE (temp_owner.expiredate - interval '1 month') AS expired_date,
        get_temp_property_progress(temp_owner.progress) AS progress,
        setweight(to_tsvector(COALESCE(temp_owner.propertyform::json->>'name','')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(nvc.full_name,'')),'A') || ' ' ||
        setweight(to_tsvector(COALESCE(to_char(temp_owner.expiredate - INTERVAL '1 month', 'YYYYMMDD'),'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE(
            CASE
                WHEN TRIM (BOTH '\"' FROM CAST(temp_owner.propertyform::json#>'{contacts,0}'#>'{name}' AS VARCHAR)) LIKE '%null%'
            THEN
                NULL
            ELSE
                TRIM (BOTH '\"' FROM CAST(temp_owner.propertyform::json#>'{contacts,0}'#>'{name}' AS VARCHAR))
            END,'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE((SELECT l_out.title FROM location AS l_out WHERE l_out.id = (
            SELECT idDestinyProperty(l.city_id,l.parent_id, l.id) AS idDestiny
            FROM location AS l
            WHERE CAST (temp_owner.propertyform::json->>'location' AS INTEGER) = l.id)
        ),'')),'B') || ' ' ||
        setweight(to_tsvector(COALESCE((
            SELECT acc.title
            FROM accommodation as acc
            WHERE acc.id = CAST (temp_owner.propertyform::json->>'accommodation' AS INTEGER)
        ),'')),'C') AS search_vector
    FROM tempowner AS temp_owner
    LEFT JOIN nvc_profile AS nvc
    ON temp_owner.nvc_profile_id = nvc.id
    WHERE CAST (temp_owner.propertyform::json->>'slug' AS VARCHAR) IS NOT NULL
);

/**
 * Vista encargada de construir los establecimientos sin disponibilidad
 * @author: Isabel Nieto
 * @version: 13-02-2017
 */
CREATE VIEW admin_property_without_availability_view AS (
	SELECT DISTINCT
		P.id,
		P.slug AS slug,
		P.name AS name,
		DATE (P.join_date) AS join_date,
		P.location,
		CASE WHEN L.city_id IS NOT NULL THEN CI.title ELSE L.title END AS city,
		Nvc.full_name AS full_name,
		setweight(to_tsvector(COALESCE(P.name,'')),'A') || ' ' ||
		setweight(to_tsvector(COALESCE(Nvc.full_name,'')),'A') || ' ' ||
		setweight(to_tsvector(COALESCE(CASE WHEN L.city_id IS NOT NULL THEN CI.title ELSE L.title END ,'')),'B') || ' ' ||
		setweight(to_tsvector(COALESCE(to_char(P.join_date, 'YYYYMMDD'),'')),'B') AS search_vector
	FROM property P
		INNER JOIN room AS R ON R.property_id = P.id
		INNER JOIN pack AS PA ON Pa.room_id = R.id
		LEFT JOIN daily_room AS DR ON (DR.room_id = R.id AND DR.DATE >= CURRENT_DATE AND DR.DATE <= CURRENT_DATE + INTERVAL '1 month')
		LEFT JOIN daily_pack AS DP ON (DP.pack_id = Pa.id AND DP.DATE >= CURRENT_DATE AND DP.DATE <= CURRENT_DATE + INTERVAL '1 month')
		LEFT JOIN location AS L ON L.id = P.location
		LEFT JOIN location AS CI ON CI.id = L.city_id
		LEFT JOIN nvc_profile AS Nvc ON Nvc.id = p.nvc_profile_id
	WHERE
		((DP.pack_id IS NULL OR DP.is_completed = FALSE) AND (DR.room_id IS NULL OR DR.is_completed = FALSE))
		AND
		NOT EXISTS( SELECT p1.id, r1.id, pa1.id,dr1.is_completed, dr1.id, dp1.is_completed, dp1.id
			FROM property AS p1
			INNER JOIN room AS r1 ON (r1.property_id = p1.id)
			INNER JOIN pack AS pa1 ON (pa1.room_id = r1.id)
			RIGHT JOIN daily_room AS dr1 ON (dr1.room_id = r1.id AND dr1.DATE >= CURRENT_DATE AND dr1.DATE <= CURRENT_DATE + INTERVAL '1 month')
			RIGHT JOIN daily_pack AS dp1 ON (dp1.pack_id = pa1.id AND dp1.DATE >= CURRENT_DATE AND dp1.DATE <= CURRENT_DATE + INTERVAL '1 month')
			WHERE P.slug = p1.slug AND (dp1.is_completed = TRUE) AND
				(dr1.is_completed = TRUE) )
);

