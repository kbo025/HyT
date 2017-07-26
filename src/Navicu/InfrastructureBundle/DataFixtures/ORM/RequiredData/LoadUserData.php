<?php

namespace Navicu\InfrastructureBundle\DataFixtures\ORM\RequeridData;



use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Navicu\InfrastructureBundle\Entity\User;
use Navicu\Core\Domain\Model\Entity\TempOwner;



/**
 * Clase LoadUserData "DataFixtures".
 *
 * La clase carga los datos de prueba del sistema de todos los usuarios. 
 *
 * @author FreddyContreras <freddy.contreras3@gmail.com>
 * @author Currently Working: FreddyContreras <freddy.contreras3@gmail.com>
 */

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    * Función donde se implementa el DataFixture
    * @param ObjectManager $manager Manejador de Doctrine
    * @return void
    */
    public function load(ObjectManager $manager)
    {
        $this->creationRecursive($manager, 0);
        $manager->flush();

        //Carga de datos del usuario administrador
        $userAdmin = new User();

        $userAdmin->setUsername('superadmin');
        $userAdmin->setEmail('superadmin@example.com');
        $userAdmin->setPlainPassword('123456');
        $userAdmin->setEnabled(true);
        $userAdmin->addRole(0);

        $manager->persist($userAdmin);

        $manager->flush();
    }

    /**
     * La siguiente función crea de manera recursiva usuarios temporal del establecimientos 
     * @param $manager
     * @param $i
     */
    private function creationRecursive(&$manager, $i)
    {
        $user1 = new User();
        $user1->setUsername("user$i");
        $user1->setEmail("user$i"."Temp@example.com");
        $user1->setPlainPassword('123456');
        $user1->setEnabled(true);
        $user1->addRole(3);

        $tempOwner = new TempOwner(
            $user1->getUsername(),
            $user1->getEmail(),
            $user1->getPlainPassword()
        );

        $tempOwner->setUserId($user1);
        $tempOwner->setSlug($user1->getUsername());
        //$tempOwner->setPropertyForm('{"id":null,"slug":"hotel-1","name":"hotel 1","fiscal_name":null,"tax_id":null,"address":"urb. araguane","star":3,"url_web":"http:\/\/www.navicu.com","amount_room":20,"number_floor":5,"check_in":{"date":"2015-08-12 10:00:00","timezone_type":3,"timezone":"UTC"},"check_out":{"date":"2015-08-12 20:00:00","timezone_type":3,"timezone":"UTC"},"description":"Descripci\u00f3n del establecimiento","phones":"04121987442","fax":"04121987442","emails":null,"rating":null,"discount_rate":null,"opening_year":1901,"renewal_year":1904,"public_areas_renewal_year":1904,"check_in_age":18,"check_out_age":null,"tax":10,"tax_rate":true,"additional_info":"Informaci\u00f3n adicional","all_included":true,"debit":true,"contacts":[{"name":"Gabriel Camacho","charge":"Presidente de la republica","type":1,"phone":"04121987442","fax":"04121987442","email":"kbo025@gmail.com"},{"name":"Gabriel Camacho","charge":"Presidente de la republica","type":2,"phone":"04121987442","fax":"04121987442","email":"kbo025@gmail.com"},{"name":"Gabriel Camacho","charge":"Presidente de la republica","type":0,"phone":"04121987442","fax":"04121987442","email":"kbo025@gmail.com"}],"hotel_chain_name":"cadena","coordinates":{"latitude":10.1176433,"longitude":-68.0477509},"postal_code":null,"accommodation":15,"location":268,"currency":148,"beds":{"beds_additional_cost":true},"child":{"child_additional_cost":false,"child_max_age":10},"baby":false,"cribs":{"cribs_additional_cost":true,"cribs_max":1},"pets":{"pets":true,"pets_additional_cost":true},"cash":{"cash":true,"max_cash":200000},"city_tax":15,"city_tax_currency":148,"city_tax_type":1,"city_tax_max_nights":2,"comercial_rooms":5,"credit_card":{"credit_card_american":true,"credit_card_master":true,"credit_card_visa":true},"language":["spa","eng"]}');
        $tempOwner->setServicesForm(json_decode('[{"type":2,"cost_service":null,"free":true,"schedule":{"opening":"10:00:00","closing":"12:00:00","full_time":false,"days":null},"quantity":null,"data":[]},{"type":3,"cost_service":null,"free":true,"schedule":{"opening":"19:45:27","closing":"19:45:27","full_time":true,"days":null},"quantity":null,"data":[]},{"type":4,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":5,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":6,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":7,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":8,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":9,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":10,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":11,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":12,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":13,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":15,"cost_service":null,"free":false,"schedule":null,"quantity":null,"data":[]},{"type":16,"cost_service":null,"free":false,"schedule":null,"quantity":null,"data":[]},{"type":17,"cost_service":null,"free":false,"schedule":null,"quantity":null,"data":[]},{"type":19,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":20,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":22,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":23,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[{"type":27,"name":"fredy\' house","breakfast_time":{"opening":"08:00:00","closing":"09:00:00","full_time":null,"days":null},"schedule":{"opening":"08:00:00","closing":"11:00:00","full_time":null,"days":null},"lunch_time":{"opening":"07:00:00","closing":"15:00:00","full_time":null,"days":null},"dinner_time":{"opening":"07:00:00","closing":"12:00:00","full_time":null,"days":null},"description":"Campo de descripci\u00f3n","buffet_carta":2,"status":true,"dietary_menu":true},{"type":15,"name":"la toscana II","schedule":{"opening":"12:00:00","closing":"22:00:00","full_time":null,"days":null},"description":"Campo de descripci\u00f3n","buffet_carta":1,"status":true,"dietary_menu":false}]},{"type":24,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[{"name":"chill out","min_age":"20","description":"Campo de descripci\u00f3n","schedule":{"opening":"20:00:00","closing":"16:00:00","full_time":null,"days":null},"type":2,"status":true,"food":false},{"name":"la cuevita","min_age":"18","description":"Campo de descripci\u00f3n","schedule":{"opening":"11:00:00","closing":"15:00:00","full_time":null,"days":null},"type":1,"food_type":11,"status":true,"food":true}]},{"type":26,"cost_service":null,"free":true,"schedule":{"opening":"09:00:00","closing":"11:00:00","full_time":false,"days":null},"quantity":null,"data":[]},{"type":30,"cost_service":null,"free":false,"schedule":null,"quantity":null,"data":[]},{"type":34,"cost_service":null,"free":false,"schedule":null,"quantity":null,"data":[]},{"type":50,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[{"name":"Salon 1 (50 Personas)","capacity":50,"type":1,"size":50,"status":true,"natural_light":false},{"name":"Auditorio 2 (50 Personas)","capacity":50,"type":2,"size":50,"status":true,"natural_light":true},{"name":"el auditorio","capacity":20,"type":3,"size":20,"status":true,"natural_light":true}]},{"type":55,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":61,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":71,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":72,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":73,"cost_service":null,"free":true,"schedule":null,"quantity":20,"data":[]},{"type":74,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":75,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":83,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":89,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":150,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]},{"type":152,"cost_service":null,"free":true,"schedule":null,"quantity":null,"data":[]}]'));
        $tempOwner->setRoomsForm(json_decode('[{"type":17,"features":[{"feature":14},{"feature":17},{"feature":19},{"feature":21},{"feature":23},{"feature":26},{"feature":29},{"feature":31},{"feature":33},{"feature":40},{"feature":43},{"feature":46},{"feature":47},{"feature":54},{"feature":57},{"feature":58},{"feature":62},{"feature":3},{"feature":4},{"feature":2,"amount":2},{"feature":1,"amount":3}],"increment":true,"max_people":5,"name":"Habitaci\u00f3n Individual - Vista a la piscina","variation_type_people":1,"rates_by_peoples":[{"number_people":1},{"amount_rate":10,"number_people":2},{"amount_rate":20,"number_people":3},{"amount_rate":30,"number_people":4},{"amount_rate":40,"number_people":5}],"amount_rooms":5,"smoking_policy":true,"size":100,"bedrooms":[{"amount_people":0,"bath":false,"beds":[{"type":0,"amount":2}]},{"amount_people":0,"bath":false,"beds":{"3":{"type":3,"amount":1},"0":{"type":0,"amount":2}}},{"amount_people":0,"bath":false,"beds":{"0":{"type":0,"amount":1},"4":{"type":4,"amount":3}}}]},{"type":32,"features":[{"feature":14},{"feature":17},{"feature":19},{"feature":21},{"feature":23},{"feature":26},{"feature":29},{"feature":31},{"feature":33},{"feature":40},{"feature":43},{"feature":48},{"feature":49},{"feature":51},{"feature":55},{"feature":60},{"feature":61},{"feature":65},{"feature":3},{"feature":4},{"feature":2,"amount":3},{"feature":1,"amount":3}],"increment":true,"max_people":20,"name":"Habitaci\u00f3n Triple - Vista al jard\u00edn","rates_by_peoples":[{"number_people":1},{"amount_rate":500,"number_people":2},{"amount_rate":1000,"number_people":3},{"amount_rate":1500,"number_people":4},{"amount_rate":2000,"number_people":5},{"amount_rate":2500,"number_people":6},{"amount_rate":3000,"number_people":7},{"amount_rate":3000,"number_people":8},{"amount_rate":3000,"number_people":9},{"amount_rate":3000,"number_people":10},{"amount_rate":3000,"number_people":11},{"amount_rate":3000,"number_people":12},{"amount_rate":3000,"number_people":13},{"amount_rate":3000,"number_people":14},{"amount_rate":3000,"number_people":15},{"amount_rate":3000,"number_people":16},{"amount_rate":3000,"number_people":17},{"amount_rate":3000,"number_people":18},{"amount_rate":3000,"number_people":19},{"amount_rate":3000,"number_people":20}],"amount_rooms":5,"size":500,"bedrooms":[{"amount_people":0,"bath":false,"beds":{"0":{"type":0,"amount":1},"3":{"type":3,"amount":2}}},{"amount_people":0,"bath":false,"beds":{"3":{"type":3,"amount":1},"4":{"type":4,"amount":2}}},{"amount_people":0,"bath":false,"beds":[{"type":0,"amount":1}]}]},{"type":45,"features":[{"feature":15},{"feature":17},{"feature":18},{"feature":23},{"feature":24},{"feature":27},{"feature":31},{"feature":32},{"feature":33},{"feature":37},{"feature":39},{"feature":41},{"feature":43},{"feature":45},{"feature":46},{"feature":47},{"feature":48},{"feature":50},{"feature":51},{"feature":54},{"feature":57},{"feature":59},{"feature":64},{"feature":65},{"feature":3},{"feature":4},{"feature":2,"amount":3},{"feature":1,"amount":5}],"increment":false,"max_people":10,"name":"Habitaci\u00f3n Cu\u00e1druple - Vista a la monta\u00f1a","rates_by_peoples":[{"number_people":1},{"number_people":2},{"number_people":3},{"number_people":4},{"number_people":5},{"number_people":6},{"number_people":7},{"number_people":8},{"number_people":9},{"number_people":10}],"amount_rooms":5,"smoking_policy":true,"size":2000,"bedrooms":[{"amount_people":0,"bath":false,"beds":{"0":{"type":0,"amount":1},"3":{"type":3,"amount":2}}},{"amount_people":0,"bath":false,"beds":[{"type":0,"amount":4}]},{"amount_people":0,"bath":false,"beds":{"1":{"type":1,"amount":1},"0":{"type":0,"amount":1},"2":{"type":2,"amount":1}}},{"amount_people":0,"bath":false,"beds":{"3":{"type":3,"amount":2}}},{"amount_people":0,"bath":false,"beds":{"3":{"type":3,"amount":1},"5":{"type":5,"amount":1}}}]},{"type":14,"features":[{"feature":14},{"feature":15},{"feature":16},{"feature":18},{"feature":22},{"feature":24},{"feature":28},{"feature":29},{"feature":33},{"feature":40},{"feature":49},{"feature":50},{"feature":51},{"feature":53},{"feature":54},{"feature":55},{"feature":57},{"feature":59},{"feature":60},{"feature":63},{"feature":3},{"feature":4},{"feature":10},{"feature":9},{"feature":7},{"feature":12},{"feature":2,"amount":4},{"feature":5,"amount":2},{"feature":6,"amount":2},{"feature":1,"amount":3},{"feature":8,"amount":3}],"increment":false,"max_people":20,"name":"Habitaci\u00f3n Villa","rates_by_peoples":[{"number_people":1},{"number_people":2},{"number_people":3},{"number_people":4},{"number_people":5},{"number_people":6},{"number_people":7},{"number_people":8},{"number_people":9},{"number_people":10},{"number_people":11},{"number_people":12},{"number_people":13},{"number_people":14},{"number_people":15},{"number_people":16},{"number_people":17},{"number_people":18},{"number_people":19},{"number_people":20}],"amount_rooms":5,"smoking_policy":true,"size":1500,"bedrooms":[{"amount_people":3,"bath":true,"beds":{"0":{"type":0,"amount":1},"3":{"type":3,"amount":1}}},{"amount_people":5,"bath":true,"beds":{"5":{"type":5,"amount":1},"3":{"type":3,"amount":2}}},{"amount_people":12,"bath":true,"beds":{"1":{"type":1,"amount":1},"3":{"type":3,"amount":1},"4":{"type":4,"amount":1}}}],"livingrooms":[{"amount_people":1,"amount_couch":1},{"amount_people":1,"amount_couch":1},{"amount_people":1,"amount_couch":1}]}]'));
        $tempOwner->setPaymentInfoForm(json_decode('{"account":{"entity":"1111","office":"1111","control":"11","account":"1111111111"},"charging_system":1,"currency":148,"tax_id":"J-18060085-2","same_data_property":true,"swift":"11111114"}'));
        $tempOwner->setTermsAndConditionsInfo(json_decode('{"accepted":true,"client_ip":"127.0.0.1","date":{"date":"2015-08-12 21:55:32","timezone_type":3,"timezone":"UTC"}}'));
        $tempOwner->setGalleryForm(json_decode('{"rooms":[{"idSubGallery":17,"subGallery":"Habitaci\u00f3n Individual - Vista a la piscina","images":[{"name":"Habitaci\u00f3n 1","path":"property\/kbo025\/rooms\/Habitaci\u00f3n Individual - Vista a la piscina\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Habitaci\u00f3n 1_2606703165.png"}]},{"idSubGallery":32,"subGallery":"Habitaci\u00f3n Triple - Vista al jard\u00edn","images":[{"name":"Terraza","path":"property\/kbo025\/rooms\/Habitaci\u00f3n Triple - Vista al jard\u00edn\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Terraza_3210194374.png"}]},{"idSubGallery":45,"subGallery":"Habitaci\u00f3n Cu\u00e1druple - Vista a la monta\u00f1a","images":[{"name":"Ba\u00f1o 2","path":"property\/kbo025\/rooms\/Habitaci\u00f3n Cu\u00e1druple - Vista a la monta\u00f1a\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Ba\u00f1o 2_2372858943.png"}]},{"idSubGallery":14,"subGallery":"Habitaci\u00f3n Villa","images":[{"name":"Piscina","path":"property\/kbo025\/rooms\/Habitaci\u00f3n Villa\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Piscina_1884059738.jpeg"}]}],"otherGalleries":[{"idSubGallery":51,"subGallery":"Zonas comunes","images":[{"name":"Fachada","path":"property\/kbo025\/otherGalleries\/Zonas comunes\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Fachada_3468793835.png"}]},{"idSubGallery":19,"subGallery":"Aparcamiento","images":[{"name":"Detalle del Aparcamiento","path":"property\/kbo025\/otherGalleries\/Aparcamiento\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Detalle del Aparcamiento_3201491902.png"}]},{"idSubGallery":23,"subGallery":"Restaurantes","images":[{"name":"la toscana II","path":"property\/kbo025\/otherGalleries\/Restaurantes\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_la toscana II_2684583120.png"}]},{"idSubGallery":24,"subGallery":"Bares","images":[{"name":"la cuevita","path":"property\/kbo025\/otherGalleries\/Bares\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_la cuevita_3153441787.png"}]},{"idSubGallery":24,"subGallery":"Discos","images":[{"name":"chill out","path":"property\/kbo025\/otherGalleries\/Discos\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_chill out_3227726606.png"}]},{"idSubGallery":34,"subGallery":"Gimnasio","images":[{"name":"Sala de m\u00e1quinas","path":"property\/kbo025\/otherGalleries\/Gimnasio\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Sala de m\u00e1quinas_2387200582.png"}]},{"idSubGallery":92,"subGallery":"Deporte y recreaci\u00f3n","images":[{"name":"Deporte","path":"property\/kbo025\/otherGalleries\/Deporte y recreaci\u00f3n\/navicu_reserva_online_mejor_precio_garantizado_hotel 1__2827742908.png"}]}],"favorites":[{"path":"property\/kbo025\/rooms\/Habitaci\u00f3n Individual - Vista a la piscina\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Habitaci\u00f3n 1_2606703165.png","subGallery":"Habitaci\u00f3n Individual - Vista a la piscina"},{"path":"property\/kbo025\/rooms\/Habitaci\u00f3n Triple - Vista al jard\u00edn\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Terraza_3210194374.png","subGallery":"Habitaci\u00f3n Triple - Vista al jard\u00edn"},{"path":"property\/kbo025\/rooms\/Habitaci\u00f3n Cu\u00e1druple - Vista a la monta\u00f1a\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Ba\u00f1o 2_2372858943.png","subGallery":"Habitaci\u00f3n Cu\u00e1druple - Vista a la monta\u00f1a"},{"path":"property\/kbo025\/rooms\/Habitaci\u00f3n Villa\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Piscina_1884059738.jpeg","subGallery":"Habitaci\u00f3n Villa"},{"path":"property\/kbo025\/otherGalleries\/Zonas comunes\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Fachada_3468793835.png","subGallery":"Zonas comunes"},{"path":"property\/kbo025\/otherGalleries\/Aparcamiento\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_Detalle del Aparcamiento_3201491902.png","subGallery":"Aparcamiento"},{"path":"property\/kbo025\/otherGalleries\/Restaurantes\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_la toscana II_2684583120.png","subGallery":"Restaurantes"},{"path":"property\/kbo025\/otherGalleries\/Bares\/navicu_reserva_online_mejor_precio_garantizado_hotel 1_la cuevita_3153441787.png","subGallery":"Bares"}]}'));
        $tempOwner->setValidations(json_decode('{"property":"OK","services":"OK","rooms":"OK","galleries":"OK","paymentInfo":"OK","termsAndConditions":"OK"}'));

        for ($j = 0; $j < 8; $j++)
            $tempOwner->setProgress($j, 1);

        $manager->persist($user1);
        $manager->persist($tempOwner);

        if ($i < 50)
            $this->creationRecursive($manager, $i+1);
    }

    /**
    * Función que identifica el orden de ejecución de DataFixture
    * @return int
    */
    public function getOrder()
    {
        return 1;
    }
}