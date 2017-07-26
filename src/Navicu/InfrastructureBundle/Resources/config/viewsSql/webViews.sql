    CREATE OR REPLACE VIEW web_fligths_autocompleted_view AS
    select 
        a.id,
        a.iata,
        a.name,
        l.title as location_name, 
        l.alfa3 as location_code,
        case when rl.id is null then
            l.title
        else
            rl.title
        end as country_name,
        case when rl.id is null then
            l.alfa2
        else
            rl.alfa2
        end as country_code,
        (setweight(to_tsvector(COALESCE(a.name , ''::character varying)::text), 'A'::"char") || ''::tsvector) || 
        (setweight(to_tsvector(COALESCE(a.iata , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(l.title , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(l.alfa3 , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(pl.title , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(pl.alfa3 , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(cl.title , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(cl.alfa3 , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(rl.title , ''::character varying)::text), 'A'::"char") || ''::tsvector) ||
        (setweight(to_tsvector(COALESCE(rl.alfa3 , ''::character varying)::text), 'A'::"char") || ''::tsvector) 
        as vector
    from airport as a
    left join location as l on a.location_id = l.id
    left join location as pl on l.parent_id = pl.id
    left join location as cl on l.city_id = cl.id
    left join location as rl on l.root_id = rl.id
    order by country_code;