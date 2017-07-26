<?php
namespace Navicu\Core\Application\UseCases\Ascribere\RegisterTempProperty;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\ContactPerson;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Url;
use Navicu\Core\Domain\Model\ValueObject\Phone;
use Navicu\Core\Domain\Model\ValueObject\Coordinate;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
* Clase para ejecutar el caso de uso RegisterTempProperty
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 27/05/2015
*/
class RegisterTempPropertyHandler implements Handler
{

    /**
    *    ejecuta el caso de uso 'Registro temporal de propiedad'
    *
    *    @param RegisterTempPropertyCommand $command Objeto Command contenedor de la soliccitud del usuario
    */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        // Obtengo la data del comando
        $request = $command->getRequest();

        // Obtengo los repositorios de TempOwner, Category y Location del repositoryFactory
        $tempowner_repository = $rf->get('TempOwner');
        $category_repository = $rf->get('Category');
        $currency_repository = $rf->get('CurrencyType');
        $location_repository = $rf->get('Location');
        $accommodation_repository =  $rf->get('Accommodation');
        $languages_repository =  $rf->get('Language');
        $code = 201;
        $errors = [];
        $languages = $languages_repository->findAllWithKeys();


            // Se busca el usuario
            $tempowner = $tempowner_repository->findOneByArray(['slug' => $request['slug']]);

            // Si existe
            if (!empty($tempowner)) {
                $property = new Property();

                // Validacion del Nombre
                if (empty($request['name']))
                    $global_errors['ubicacion'][] = 'Nombre no debe estar vacio';
                else
                    if (!empty(preg_replace('/(\w| |ñ)/i', '', $request['name']))) {
                        $global_errors['ubicacion'][] = 'Nombre no debe contener caracteres especiales';
                        $errors[] = "el campo 'Nombre' no debe contener caracteres especiales, por favor verifique";
                        $code = 400;    
                    } else {
                        $property->setName($request['name']);
                    }

                // Validacion de la direccion
                if (!empty($request['address']))
                    $property->setAddress($request['address']);
                else
                    $global_errors['ubicacion'][] = 'Dirección no debe estar vacia';

                // Validacion de Categoria
                if (!empty($request['star']))
                    $property->setStar($request['star']);
                else
                    $global_errors['ubicacion'][] = 'Categoria no debe estar vacia';

                // Validacion de numero de pisos
                if (!empty($request['number_floor']))
                    $property->setNumberFloor($request['number_floor']);
                else
                    $global_errors['establecimiento'][] = 'Debes indica el N° de pisos que tiene tu establecimiento';

                //Validando y almacenando cadena de hoteles
                if ($request['hotel_chain'] == true) {
                    if (!empty($request['hotel_chain_name'])) {
                        $property->setHotelChainName($request['hotel_chain_name']);
                    } else {
                        $global_errors['establecimiento'][] = 'Debes indicar el nombre de la cadena a la cual pertenece tu establecimiento';
                    }
                }

                // Validacion de cantidad de habitaciones
                if (!empty($request['amount_room']))
                    $property->setAmountRoom($request['amount_room']);
                else
                    $global_errors['establecimiento'][] = 'Debes indicar el N° de habitaciones de tu establecimiento';

                // Validando de que check-in no este vacio
                if (!empty($request['check_in'])) {
                    $newdate = new \DateTime($request['check_in']);

                    if ($newdate) {
                        $property->setCheckIn($newdate);
                    } else {
                        $errors[] = "existe un problema con el campo 'check in' verifica que esté correcto o comunicate con nosotros";
                        $global_errors['establecimiento'][] = 'Debes indicar hora de Check in en tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['establecimiento'][] = 'Debes indicar hora de Check in en tu establecimiento';
                }

                // Validacion de que check-out no este vacio
                if (!empty($request['check_out'])) {
                    $newdate = new \DateTime($request['check_out']);

                    if ($newdate) {
                        $property->setCheckOut($newdate);
                    } else {
                        $errors[]="existe un problema con el campo 'check out' verifica que esté correcto o comunicate con nosotros";
                        $global_errors['establecimiento'][] = 'Debes indicar hora de Check out de tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['establecimiento'][] = 'Debes indicar hora de Check out de tu establecimiento';
                }

                // Validacion de la edad permitida de check-in
                if (!empty($request['check_in_age'])) {
                    $property->setCheckInAge($request['check_in_age']);
                } else {
                    $global_errors['informacion_adicional'][] = 'Debes indicar la edad minima permitida para hacer check in';
                }

                // Validacion de año de apertura del establecimiento
                if (!empty($request['opening_year'])) {
                    $property->setOpeningYear($request['opening_year']);
                } else {
                    $global_errors['establecimiento'][] = 'Debes Indicar el año de apertura de tu establecimiento';
                }

                //si los precios incluyen impuestos o no
                if (isset($request['tax']) and !is_null($request['tax'])) {
                    $property->setTax($request['tax']);
                }

                // Porcentaje de impuesto calculado
                if (isset($request['tax_rate']) and !is_null($request['tax_rate'])) {
                    $property->setTaxRate($request['tax_rate']);
                }

                //si el establecimiento es todo incluido
                $property->setAllIncluded(!empty($request['all_included']));
                $property->setDebit(!empty($request['debit']));

                //año de remodelacion si existe
                if($request['renewal_year']>$request['opening_year'])
                    $property->setRenewalYear($request['renewal_year']);

                //año de remodelacion de areas publicas si existe
                if($request['public_areas_renewal_year']>$request['opening_year'])
                    $property->setPublicAreasRenewalYear($request['public_areas_renewal_year']);

                // Informacion adicional si existe
                if (strlen($request['additional_info']) <= 4000) {
                    $property->setAdditionalInfo($request['additional_info']);
                } else {
                    $errors[]="existe un problema con el campo 'Información Adicional' verifica que esté correcto o comunicate con nosotros";
                    $code = 400;
                }

                if ($request['comercial_rooms']) {
                    if(!empty($request['amount_comercial_rooms'])) {
                        $property->setComercialRooms($request['amount_comercial_rooms']);
                    } else {
                        $global_errors['informacion_adicional'][]='Debes indicar la cantidad de habitaciones comerciales que ofreces';
                    }
                }

                if ($request['design_view_property']) {
                    if(!empty($request['design_view_property'])) {
                        $property->setDesignViewProperty($request['design_view_property']);
                    }
                }

                // Descripcion si existe
                if (!empty($request['description'])) {
                    if (strlen($request['description']) <= 1800) {
                        $property->setDescription($request['description']);
                    } else {
                        $errors[]="existe un problema con el campo 'Descripción' verifica que esté correcto o comunicate con nosotros";
                        $global_errors['descripcion'][]='Debes incluir una descripción de tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['descripcion'][]='Debes incluir una descripción de tu establecimiento';
                }

                // Informacion de camas extra
                $property->setBeds($request['beds']);
                $property->setBedsAdditionalCost($request['beds_additional_cost']);
                $property->setBedsPriorNotice(!empty($request['beds_prior_notice']));

                // Politicas sobre niños
                $property->setChild(!empty($request['child']));
                if ($property->getChild()) {
                    if (!isset($request['age_policy']['adult']))
                        $global_errors['informacion_adicional'][]="Debes indicarnos las edades en las que consideras a un huesped como un adulto";
                    else
                        $property->setAgePolicy(
                            $request['age_policy']['adult'],
                            isset($request['age_policy']['child']) ? $request['age_policy']['child'] : false,
                            isset($request['age_policy']['baby']) ? $request['age_policy']['baby'] : false
                        );
                }
                //$property->setDiscountRate(!empty($request['discount_rate']) ? $request['discount_rate'] : 0.3);

                // Informacion de servicio de cunas en la habitacion
                $property->setCribsAdditionalCost($request['cribs_additional_cost']);
                $property->setCribsMax($request['cribs_max']);
                $property->setCribs($request['cribs']);
                $property->setCribsPriorNotice($request['cribs_prior_notice']);
                if(!empty($request['basic_quota']))
                    $property->setBasicQuota($request['basic_quota']);
                else
                    $global_errors['carga_de_tarifas'][] = 'Debes indicar la cuota basica de habitaciones para tu establecimiento';
                $property->setRateType($request['rate_type']);
                if(empty($request['rate_type']))
                    $global_errors['carga_de_tarifas'][] = 'Debes indicar el tipo de tarifas que deseas manejar en tu establecimiento';

                // Politicas sobre mascotas
                $property->setPets($request['pets']);
                $property->setPetsAdditionalCost($request['pets_additional_cost']);

                // Politicas sobre pago con tdc
                $property->setCreditCard($request['credit_card']);
                $property->setCreditCardAmex($request['credit_card_amex']);
                $property->setCreditCardMc($request['credit_card_mc']);
                $property->setCreditCardVisa($request['credit_card_visa']);

                // Cantidad maxima sobre la cual se calcula impuestos de la ciudad
                $property->setCityTaxMaxNights($request['city_tax_max_nights']);

                // Politicas de pago en efectivo
                $property->setCash($request['cash']);
                if (($property->getCash()) && (!empty($request['max_cash']))) {
                    if (is_numeric($request['max_cash'])) {
                        $property->setMaxCash($request['max_cash']);
                    } else {
                        $errors[]="existe un problema con el campo 'Maximo de efectivo permitido' verifica que esté correcto o comunicate con nosotros";
                        $code = 400;
                    }
                }

                // Tipo de porcentaje cobrado (por )
                $property->setCityTax($request['city_tax']);
                if (!empty($request['city_tax_type'])) {
                    if ( $request['city_tax_type']>0 && $request['city_tax_type']<4 ) {
                        $property->setCityTaxType($request['city_tax_type']);
                    }else{
                        $errors[]="existe un problema con el campo 'Tipo de impuesto de la ciudad' verifica que esté correcto o comunicate con nosotros";
                        $code = 400;
                    }
                }
                
                if (!empty($request['contacts'])) {
                    $contactrep = $rf->get('ContactPerson');
                    $list = $contactrep->getListType();
                    $listadd = [];
                    $minEmailSend = 0;
                    foreach ($request['contacts'] as $key => $contact) {
                        $type_contact = $contactrep->getIdType($contact['type']);
                        $newcontact = new ContactPerson();
                        if (isset($type_contact)) {
                            array_push($listadd,$contact['type']);
                            $newcontact->setType($type_contact);
                        } else {
                            $global_errors['contacts'][$key][] = 'Debes indicar el tipo de contacto';
                        }

                        if (!empty($contact['name'])) {
                            $newcontact->setName($contact['name']);
                        } else {
                            $global_errors['contacts'][$key][] = 'Debes incluir el nombre del contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                        }

                        if(!empty($contact['charge'])) {
                            $newcontact->setCharge($contact['charge']);
                        } else {
                            $global_errors['contacts'][$key][] = 'Debes indicar el cargo que ejerce el contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                        }

                        $newcontact->setEmailReservationReceiver(false);
                        if(!empty($contact['email_reservation_receiver'])) {
                            $newcontact->setEmailReservationReceiver(true);
                            $minEmailSend++;
                        }
                        if(!empty($contact['phone'])) {
                            try {
                                $newcontact->setPhone(
                                    isset($contact['phone']) ?
                                    new Phone($contact['phone']) :
                                    null
                                );
                            } catch( \Exception $e ) {
                                $errors[]="existe un problema con el campo 'Teléfono' del contacto ".$contact['type'].", verifica que esté correcto o comunicate con nosotros";
                                $global_errors['contacts'][$key][] = 'Debes indicar un teléfono para el contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                                $code = 400;
                            }

                        } else {
                            $global_errors['contacts'][$key][] = 'Debes indicar un teléfono para el contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                        }

                        if(!empty($contact['email'])) {
                            try {
                                $newcontact->setEmail(
                                    isset($contact['email']) ?
                                    new EmailAddress($contact['email']) :
                                    null
                                );
                            } catch(\Exception $e) {
                                $errors[]="existe un problema con el campo 'Email' del contacto ".$contact['type'].",verifica que esté correcto o comunicate con nosotros";
                                $global_errors['contacts'][$key][] = 'Debes indicar un email para el contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                                $code = 400;
                            }
                        } else {
                            $global_errors['contacts'][$key][] = 'Debes indicar un email para el contacto'.(isset($type_contact) ? ' de '.$contact['type'] : '');
                        }

                        if(!empty($contact['fax'])) {
                            try {
                                $newcontact->setFax(
                                    isset($contact['fax']) ?
                                    new Phone($contact['fax']) :
                                    null
                                );
                            } catch(\Exception $e) {
                                $errors['contacts'][$key] = 'Invalid fax';
                            }
                        }
                        $property->addContact($newcontact);
                    }
                    if ($minEmailSend<1) {
                        $global_errors['establecimiento'][] = 'Al menos uno de sus contactos debe recibir correos electrónicos por parte de navicu';
                    }
                    foreach ($list as $type) {
                        if ($type['required'] && !in_array($type['name'], $listadd)) {
                            $errors['contacts'][] = 'Debes incluir un contacto de '.$type['name'];
                            $global_errors['contacts'][]='Debes incluir un contacto de '.$type['name'];
                            $code = 201;
                        }
                    }

                } else {
                    $global_errors['contacts'][]='Debes incluir al menos una personas de contacto';
                }

                //llenando objetos valor
                if(!empty($request['url_web'])){
                    if( !strstr($request['url_web'],'http://') && !strstr($request['url_web'],'HTTP://') ) {
                        $request['url_web'] = 'http://'.$request['url_web'];
                    }
                    try{
                        $property->setUrlWeb(new Url($request['url_web']));
                    }catch( \Exception $e){
                        $errors[]="existe un problema con el campo 'Sitio Web', verifica que esté correcto o comunicate con nosotros";
                        $code = 400;
                    }
                }
                if (!empty($request['phones'])) {
                    try {
                        $property->setPhones(new Phone($request['phones']));
                    } catch ( \Exception $e) {
                        $errors[]="existe un problema con el campo 'Teléfono', verifica que esté correcto o comunicate con nosotros";
                        $global_errors['ubicacion'][]='Debes incluir un télefono para tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['ubicacion'][]='Debes incluir un télefono para tu establecimiento';
                }
                if (!empty($request['fax'])) {
                    try {
                        $property->setFax(new Phone($request['fax']));
                    } catch( \Exception $e) {
                        $errors[]="existe un problema con el campo 'Fax', verifica que esté correcto o comunicate con nosotros";
                        $code = 400;
                    }
                }

                if (!empty($request['longitude']) and !empty($request['latitude'])) {
                    try {
                        $property->setCoordinates(new Coordinate($request['longitude'],$request['latitude']));
                    } catch( \Exception $e) {
                        $errors[]="existe un problema con el campo 'Coordenadas', verifica que esté correcto o comunicate con nosotros";
                        $global_errors['ubicacion'][]='Debes incluir las coordenas geográficas de tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['ubicacion'][]='Debes incluir las coordenas geográficas de tu establecimiento';
                }

                //Llenando Relaciones
                if(!empty($request['accommodation'])){
                    //$accommodation = $accommodation_repository->find($request['accommodation']);
                    $accommodation = $accommodation_repository->getById($request['accommodation']);
                    if(isset($accommodation)){
                        $property->setAccommodation($accommodation);
                    }else{
                        $errors[]="existe un problema con el campo 'Tipo de establecimiento', verifica que esté correcto o comunicate con nosotros";
                        $global_errors['ubicacion'][]='Debes indicar el tipo de alojamiento de tu establecimiento';
                        $code = 400;
                    }
                }else{
                    $global_errors['ubicacion'][]='Debes indicar el tipo de alojamiento de tu establecimiento';
                }

                if(!empty($request['currency'])){
                    $currency = $currency_repository->find($request['currency']);
                    if(isset($currency)){
                        $property->setCurrency($currency);
                    }else{
                        $errors[]="existe un problema con el campo 'Moneda', verifica que esté correcto o comunicate con nosotros";
                        $global_errors['requisitos_reserva'][] = 'Debes indicar la moneda usada en tu establecimiento';
                        $code = 400;
                    }
                }else{
                    $global_errors['requisitos_reserva'][] = 'Debes indicar la moneda usada en tu establecimiento';
                }

                if (!empty($request['city_tax_currency'])) {
                    $currency = $currency_repository->find($request['city_tax_currency']);
                    if(isset($currency)){
                        $property->setCityTaxCurrency($currency);
                    }else{
                        $errors[]="existe un problema con el campo 'Moneda de impuesto de la ciudad', verifica que esté correcto o comunicate con nosotros";
                        $code = 400;
                    }
                }

                try{
                    if (!empty($request['languages'])) {
                        if (is_array($request['languages'])) {
                            $aux = [];
                            $addlan = false;
                            foreach ($request['languages'] as $language) {
                                $newlang = $languages_repository->findOneBy(['native' => $language]);
                                if (isset($newlang)) {
                                    $property->addLanguage($newlang);
                                    $addlan = true;
                                } else {
                                    $errors[]='Hubo un error al agregar '.$language.' en los Idiomas hablados, verifica que esté correcto o comunicate con nosotros';
                                    $code = 400;
                                }
                            }
                            if(!$addlan) {
                                $global_errors['establecimiento'][] = 'Debes indicar los idiomas que usan en tu establecimiento';
                            }
                        } else {
                            $errors[]="existe un problema con el campo 'Idiomas Hablados', verifica que esté correcto o comunicate con nosotros";
                            $global_errors['establecimiento'][] = 'Debes indicar los idiomas que usan en tu establecimiento';
                            $code = 400;
                        }
                    } else {
                        $global_errors['establecimiento'][] = 'Debes indicar los idiomas que usan en tu establecimiento';
                    }
                } catch ( \Exception $e) {
                    $errors[]="existe un problema con el campo 'Idiomas Hablados', verifica que esté correcto o comunicate con nosotros";
                    $global_errors['establecimiento'][] = 'Debes indicar los idiomas que usan en tu establecimiento';
                    $code = 400;
                }

                if(!empty($request['city'])){
                    $location = $location_repository->find($request['city']);
                    if(isset($location)){
                        $property->setLocation($location);
                    }else{
                        $errors[]="Existe un problema con los campos 'Pais', 'Estado' y/o 'Ciudad', Verifica que esten correctos o comunicate con nosotros";
                        $global_errors['ubicacion'][]='Debes indicar el pais, estado o provincia y ciudad donde se encuentra ubicado tu establecimiento';
                        $code = 400;
                    }
                } else {
                    $global_errors['ubicacion'][]='Debes indicar el pais, estado o provincia y ciudad donde se encuentra ubicado tu establecimiento';
                }

                $global_rooms_errors = [];
                if($request['amount_room'] < $tempowner->getAmountRoomsAdd()) {
                    $global_rooms_errors[] ='Cantidad de habitaciones agregadas excedida';
                } else {
                    if($request['amount_room'] > $tempowner->getAmountRoomsAdd()) {
                        $global_rooms_errors[] ='Cantidad de habitaciones agregadas incompleta';
                    }
                }
                if($request['basic_quota'] < $tempowner->getBasicQuotasRoomsAdd()) {
                    $global_rooms_errors[] ='>Cuota básica de habitaciones agregadas excedida';
                } else {
                    if($request['basic_quota'] > $tempowner->getBasicQuotasRoomsAdd()) {
                        $global_rooms_errors[] ='Cuota básica de habitaciones agregadas incompleta';
                    }
                }

                $property->generateSlug();

                $validations = $tempowner->getValidations();
                if (empty($global_errors)) {
                    if ($tempowner->getLastsec() < 1) {
                        $tempowner->setLastsec(1);
                    }
                    $tempowner->setProgress(0,1);
                    $validations['property'] = 'OK';
                } else {
                    $tempowner->setProgress(0,0);
                    $validations['property'] = $global_errors;
                }
                
                if(empty($global_rooms_errors)) {
                    if (!empty($request['amount_room'])) {
                        $validations['rooms'] = 'OK';
                        $tempowner->setProgress(2,1);
                    }
                } else {
                    $validations['rooms'] = $global_rooms_errors;
                    $tempowner->setProgress(2,0);
                }
                $tempowner->setValidations($validations);
                $tempowner->setPropertyForm($property);
                $tempowner_repository->save($tempowner);

                return new ResponseCommandBus($code,($code==201 ? 'OK' : 'Bad request'),$errors);
            } else {
                return new ResponseCommandBus(401,'Unauthorized');
            }
    }

    /**
     * La siguiente función se encarga de modificar los nombres de los archivos
     * de las imagenes cuando se modifica el nombre del establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param array $dataGallery
     * @param string $slug
     * string $nameProperty
     * @return boolean
     * @version 03/08/2015
     */
    private function updateGallery(&$dataGallery, $slug, $nameProperty)
    {
        $flag = false;

        $webPath =  $_SERVER['DOCUMENT_ROOT'].'/images/';
        $slugPath = 'property/'.$slug.'/';

        //Se visualiza que las imagenes existan para modificar sus nombres
            if (!empty($dataGallery)) {
                //Se analiza los datos de las archivos de las habitaciones
                if (isset($dataGallery['rooms']) and !empty($dataGallery['rooms'])) {
                    foreach ($dataGallery['rooms'] as &$currentGallery) {
                        if (isset($currentGallery['images'])) {
                            foreach ($currentGallery['images'] as &$currentImage) {

                                $oldPath = $currentImage['path'];

                                $fileExists = true;

                                if (!(file_exists($webPath . 'images_original/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_md/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_sm/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_xs/' . $oldPath)))
                                    $fileExists = false;

                                if ($fileExists) {
                                    $file = $webPath . 'images_original/' . $oldPath;

                                    //Datos de archivo
                                    $name = trim($currentImage['name'], ' ') . '_';
                                    $codeFile = substr(time() + rand(), 0, 14);
                                    $extensionFile = pathinfo($file, PATHINFO_EXTENSION);

                                    $newPath =
                                        $slugPath .
                                        'rooms/' .
                                        $currentGallery['subGallery'] . '/' .
                                        'navicu_reserva_online_mejor_precio_garantizado_' .
                                        $nameProperty.'_'.
                                        $name . '_' .
                                        $codeFile . '.' .
                                        $extensionFile;

                                    $currentImage['path'] = $newPath;
                                    rename($webPath . 'images_original/' . $oldPath, $webPath . 'images_original/' . $newPath);
                                    rename($webPath . 'images_md/' . $oldPath, $webPath . 'images_md/' . $newPath);
                                    rename($webPath . 'images_sm/' . $oldPath, $webPath . 'images_sm/' . $newPath);
                                    rename($webPath . 'images_xs/' . $oldPath, $webPath . 'images_xs/' . $newPath);
                                }
                            }
                            $flag = true;
                        }
                    }
                }

                if (isset($dataGallery['otherGalleries']) and !empty($dataGallery['otherGalleries'])) {
                    //Se analiza los datos del las otras galería de imagenes
                    foreach ($dataGallery['otherGalleries'] as &$currentGallery) {
                        if (isset($currentGallery['images'])) {
                            foreach ($currentGallery['images'] as &$currentImage) {
                                $oldPath = $currentImage['path'];

                                $fileExists = true;

                                if (!(file_exists($webPath . 'images_original/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_md/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_sm/' . $oldPath)))
                                    $fileExists = false;

                                if (!(file_exists($webPath . 'images_xs/' . $oldPath)))
                                    $fileExists = false;

                                if ($fileExists) {
                                    $file = $webPath . 'images_original/' . $oldPath;

                                    //Datos de archivo
                                    $name = trim($currentImage['name'], ' ') . '_';
                                    $codeFile = substr(time() + rand(), 0, 14);
                                    $extensionFile = pathinfo($file, PATHINFO_EXTENSION);

                                    $newPath =
                                        $slugPath .
                                        'otherGalleries/' .
                                        $currentGallery['subGallery'] . '/' .
                                        'navicu_reserva_online_mejor_precio_garantizado_' .
                                        $nameProperty.'_'.
                                        $name.'_'.
                                        $codeFile.'.'.
                                        $extensionFile;

                                    $currentImage['path'] = $newPath;
                                    rename($webPath . 'images_original/' . $oldPath, $webPath . 'images_original/' . $newPath);
                                    rename($webPath . 'images_md/' . $oldPath, $webPath . 'images_md/' . $newPath);
                                    rename($webPath . 'images_sm/' . $oldPath, $webPath . 'images_sm/' . $newPath);
                                    rename($webPath . 'images_xs/' . $oldPath, $webPath . 'images_xs/' . $newPath);
                                }

                                $flag = true;
                            }
                        }
                    }
                }
            }

        return $flag;

    }
}