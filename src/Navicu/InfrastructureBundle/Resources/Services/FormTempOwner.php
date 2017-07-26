<?php 

namespace Navicu\InfrastructureBundle\Resources\Services;


use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\Entity\TempOwner;
/**
 *
 * La siguiente clase se encarga de los servicios 
 * que interacturan con la entidad TempOwner
 * 
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 24/06/2015
 * 
 */
class FormTempOwner
{
    private $rf;
    private $security;

    /**
    * Constructor del servicio
    *
    * @param RepositoryFactoryInterface $rf
    */
    public function __construct(RepositoryFactoryInterface $rf, $security)
    {
        $this->rf = $rf;
        $this->security = $security;
    }

    /**
    * La función retorna una array con los datos de la habitación
    * del usuario temporal (TempOwner). Los datos son utilizados para
    * la carga de la galería de imagenes
    *
    * @param Array $data
    * @param Array $services
    * @param Array $slug
    * @param Array $dataImages
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 30/06/2015
    * @return Array
    */
    public function getArrayGalleries($dataRooms, $dataServices, $dataImages, $tempOwner)
    {
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        $response = array(
            'favorites' => array(),
            'rooms' => array(), 
            'otherGalleries' => array(),
            'slug' => $tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress()
        );

        if (isset($dataImages['favorites']))
            $response['favorites'] = $dataImages['favorites'];
        
        foreach ($dataRooms as $currentRoom) {

            $room = $this->rf->get('RoomType')
                    ->findOneByArray(array('id' => $currentRoom['type']));

            $arrayRoom = array();
            $arrayRoom['idSubGallery'] = $currentRoom['type'];
            $arrayRoom['nameRoom'] = $currentRoom['name'];
            $arrayRoom['features'] = array();

            $rpRoomFeatureType = $this->rf->get('RoomFeatureType');
            
            foreach ($currentRoom['features'] as $currentFeature) {
                if (isset($currentFeature['feature'])) {
                    $feature = $rpRoomFeatureType
                        ->findOneByArray(array('id' => $currentFeature['feature']));
                    
                    if ($feature and $feature->getType() == 0) {
                        $auxFeature = array();
                        $auxFeature['nameFeature'] = $feature->getTitle();
                        if (isset($currentFeature['amount']))
                            $auxFeature['amount'] = $currentFeature['amount'];
                        else
                            $auxFeature['amount'] = 0;
                        array_push($arrayRoom['features'], $auxFeature);
                        sort($arrayRoom['features']);
                    }
                }
            }

            $loadedImages = $this->remenberImages($dataImages, 'rooms',$room->getId(),$currentRoom['name']);
            
            if ($loadedImages)
                $arrayRoom['loadedImages'] = $loadedImages;
            else
                $arrayRoom['loadedImages'] = null;

            if (!empty($arrayRoom))
                array_push($response['rooms'], $arrayRoom);
        }

        $this->getCommonZone($response['otherGalleries'],$dataImages);

        foreach ($dataServices as $currentService) {
            $rpServiceType = $this->rf->get('ServiceType');

            $service = $rpServiceType
                        ->findOneByArray(array('id' => $currentService['type']));

            $feature = array();
            $keySubGallery = $this->getSubGallery($service->getTitle(), $feature);

            if ($keySubGallery) {
                
                $flag = false;

                $currentGallery['idSubGallery'] = $service->getId();
                $currentGallery['nameService'] = $service->getTitle();
                $currentGallery['features'] = $feature;


                $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), $keySubGallery);

                if ($loadedImages)
                    $currentGallery['loadedImages'] = $loadedImages;
                else
                    $currentGallery['loadedImages'] = null;

                array_push($response['otherGalleries'],$currentGallery);
            }  else {

                // buscando la galería de imagen según de servicio
                $flagSubGallery = $this->getSubService($response['otherGalleries'], $service, $dataImages);
                
                if (!$flagSubGallery)
                    $this->getSubServiceForName($response['otherGalleries'], $service, $currentService, $dataImages);
            }
        }

        return json_encode($response);
    }

    /**
    * La siguiente Función retorna las galerías referentes los servicios de bienestar como
    * Bares /Discoteras / Salones / Restaurantes
    *
    * @param Array $otherGalleries
    * @param Object ServiceType $service
    * @param Array $currentService
    * @param Array $dataImages
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 20/07/2015
    * @return Void
    */
    private function getSubServiceForName(&$otherGalleries, $service, $currentService,$dataImages) 
    {
        switch ($service->getTitle()) {
            case 'Bar':
                if (isset($currentService['data'])) {

                    $auxBar = array();
                    $auxDisco = array();

                    foreach ($currentService['data'] as $currentBar)  {
                        if ($currentBar['type'] == 1 ) {
                            if (empty($auxBar)) {
                                $auxBar['idSubGallery'] = $service->getId();
                                $auxBar['nameService'] = 'Bares';
                                $auxBar['features'] = array();
                            }

                            array_push($auxBar['features'], $currentBar['name']);
                        } else if ($currentBar['type'] == 2) {

                            if (empty($auxDisco)) {
                                $auxDisco['idSubGallery'] = $service->getId();
                                $auxDisco['nameService'] = 'Discos';
                                $auxDisco['features'] = array();
                            }
                            array_push($auxDisco['features'], $currentBar['name']);
                        }                        
                    }

                    array_push($auxBar['features'],'Barra de bar');
                    if (!empty($auxBar))
                        sort($auxBar['features']);
                    if (!empty($auxDisco))
                        sort($auxDisco['features']);

                    if (!empty($auxBar)) {

                        $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), 'Bares');

                        if ($loadedImages)
                            $auxBar['loadedImages'] = $loadedImages;
                        else
                            $auxBar['loadedImages'] = null;   

                        array_push($otherGalleries, $auxBar);
                    }

                    if (!empty($auxDisco)) {

                        $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), 'Discos');

                        if ($loadedImages)
                            $auxDisco['loadedImages'] = $loadedImages;
                        else
                            $auxDisco['loadedImages'] = null;                    
                        
                        array_push($otherGalleries, $auxDisco);
                    }                    
                }

                break;

            case 'Restaurante':
                if (isset($currentService['data'])) {
                    $auxGallery = array();
                    $auxGallery = array();
                    $auxGallery['idSubGallery'] = $service->getId();   
                    $auxGallery['nameService'] = 'Restaurantes';
                    $auxGallery['features'] = array();

                    $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), 'Restaurantes');

                    if ($loadedImages)
                        $auxGallery['loadedImages'] = $loadedImages;
                    else
                        $auxGallery['loadedImages'] = null;
                    
                    foreach ($currentService['data'] as $currentRestaurant) 
                        array_push($auxGallery['features'], $currentRestaurant['name']);

                    sort($auxGallery['features']);
                    array_push($otherGalleries, $auxGallery);                    
                }
                break;
            case 'Salones':
                if (isset($currentService['data'])) {
                    $auxGallery = array();
                    $auxGallery = array();
                    $auxGallery['idSubGallery'] = $service->getId();   
                    $auxGallery['nameService'] = 'Salones';
                    $auxGallery['features'] = array();

                    $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), 'Salones');

                    if ($loadedImages)
                        $auxGallery['loadedImages'] = $loadedImages;
                    else
                        $auxGallery['loadedImages'] = null;
                    
                    foreach ($currentService['data'] as $currentBanquet) 
                        array_push($auxGallery['features'], $currentBanquet['name']);

                    sort($auxGallery['features']);
                    array_push($otherGalleries, $auxGallery);                    
                }
                break;
        }
    }

    /**
    * La siguiente función retorna la galería de Zonas Comunes del establecimiento
    *
    * @param Array $otherGalleries
    * @param Array $dataImages
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 20/07/2015
    * @return Void
    */
    private function getCommonZone(&$otherGalleries, $dataImages)
    {
        $rpServiceType = $this->rf->get('ServiceType');
        $service = $rpServiceType->findOneByArray(array('title' => 'Zonas comunes'));

        if ($service) {
            
            $auxGallery = array();
            $auxGallery['idSubGallery'] = $service->getId();
            $auxGallery['nameService'] = 'Zonas comunes';
            $auxGallery['features'] = array();

            $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$service->getId(), 'Zonas comunes');
            
            if ($loadedImages)
                $auxGallery['loadedImages'] = $loadedImages;
            else
                $auxGallery['loadedImages'] = null;

            array_push($auxGallery['features'], 'Baños');
            array_push($auxGallery['features'], 'Caminería exterior');
            array_push($auxGallery['features'], 'Detalle entrada');
            array_push($auxGallery['features'], 'Fachada');
            array_push($auxGallery['features'], 'Hall de entrada');
            array_push($auxGallery['features'], 'Parque infantil');
            array_push($auxGallery['features'], 'Pasillos de habitaciones');
            array_push($auxGallery['features'], 'Recepción');
            array_push($auxGallery['features'], 'Vista al exterior');
            array_push($auxGallery['features'], 'Vista al interior');
            array_push($otherGalleries, $auxGallery);
        }
    }

    /**
    * La siguiente Función retorna las Galería si el servicio pertenece a la galería 
    *
    * @param Array $otherGalleries
    * @param Object ServiceType $service
    * @param Array $dataImages
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 22/07/2015
    * @return Boolean
    */
    private function getSubService(&$otherGalleries, $service, $dataImages) 
    {
        
        $flag = false;
        $subGalería = null;

        $rootParent = $service->getRoot();

        if (!is_null($rootParent)) {
            switch ($rootParent->getTitle()) {
                case 'Zonas comunes':
                    foreach ($otherGalleries as &$currentGallery) { 
                        if ($currentGallery['nameService'] == $rootParent->getTitle()) {
                            array_push($currentGallery['features'], $service->getTitle());
                            sort($currentGallery['features']);
                            $flag = true;
                            break;
                        }
                    }

                    break;
                case 'Deporte y recreación':

                    foreach ($otherGalleries as &$currentGallery) {
                        if ($currentGallery['nameService'] == $rootParent->getTitle()) {
                            $auxFlag = false;

                            foreach ($currentGallery['features'] as $currentService) {
                                if ($currentService == $service->getParent()->getTitle()) {

                                   $auxFlag = true;
                                    break;
                                }
                            }
                            if (!$auxFlag) {
                                array_push($currentGallery['features'], $service->getParent()->getTitle());
                                sort($currentGallery['features']);
                            }

                            $flag = true;
                            break;
                        }
                    }

                    if (!$flag) {
                        $auxGallery = array();
                        $auxGallery['idSubGallery'] = $rootParent->getId();
                        $auxGallery['nameService'] = 'Deporte y recreación';
                        $auxGallery['features'] = array();

                        $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$rootParent->getId(), 'Deporte y recreación');

                        if ($loadedImages)
                            $auxGallery['loadedImages'] = $loadedImages;
                        else
                            $auxGallery['loadedImages'] = null;

                        array_push($auxGallery['features'], $service->getParent()->getTitle());
                        sort($auxGallery['features']);
                        array_push($otherGalleries, $auxGallery);

                        $flag = true;                                                
                    }

                    break;

                case 'Spa':
                     foreach ($otherGalleries as &$currentGallery) { 
                        if ($currentGallery['nameService'] == $rootParent->getTitle()) {

                            $auxFlag = false;
                            foreach ($currentGallery['features'] as $currentService) {
                                if ($currentService == $service->getTitle())
                                    $auxFlag = true;
                            }
                            if (!$auxFlag) {
                                array_push($currentGallery['features'], $service->getTitle());
                                sort($currentGallery['features']);
                            }

                            $flag = true;
                            break;
                        }
                    }

                    if (!$flag) {
                        $auxGallery = array();
                        $auxGallery['idSubGallery'] = $rootParent->getId();
                        $auxGallery['nameService'] = 'Spa';
                        $auxGallery['features'] = array();

                        $loadedImages = $this->remenberImages($dataImages, 'otherGalleries',$rootParent->getId(), 'Spa');

                        if ($loadedImages)
                            $auxGallery['loadedImages'] = $loadedImages;
                        else
                            $auxGallery['loadedImages'] = null;

                        array_push($auxGallery['features'], $service->getTitle());
                        sort($auxGallery['features']);
                        array_push($otherGalleries, $auxGallery);

                        $flag = true;                                                
                    }
                    break;

                default:
                    break;
            }
        }
        return  $flag;
    }

    /**
    * La función  retorna el nombre de la Galeria dependiendo el servicio
    * 
    * @param String $title
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @return String
    * @version 27/06/2015
    */
    private function getSubGallery($title, &$feature = array())
    {
        switch ($title) {
            case 'Aparcamiento':
                $response = 'Aparcamiento';
                array_push($feature, 'Aparcamiento');
                array_push($feature, 'Detalle del Aparcamiento');
                break;
            case 'Gimnasio':
                $response = 'Gimnasio';
                array_push($feature, 'Piscina interior');
                array_push($feature, 'Piscina exterior');
                array_push($feature, 'Sala de actividades');
                array_push($feature, 'Sala de máquinas');
                break;
            case 'Recepción':
                $response = 'Recepción';
                array_push($feature, 'Atención al cliente');
                array_push($feature, 'Atención VIP');
                array_push($feature, 'Concierge');
                array_push($feature, 'Guest service');
                array_push($feature, 'Recepción');

                break;
            case 'Guarderia':
                $response = 'Guarderia';
                array_push($feature, 'Kids Club');
                array_push($feature, 'Piscina infantil');

            default:
                $response = false;
                break;
        }

        return $response;
    }

    /**
    * La función recuerda las imagenes anteriormente en la sección de Galería
    *
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @version 30/06/2015
    * @return Array /null
    */
    private function remenberImages($dataImages, $gallery, $idGallery, $subGallery)
    {
        $response = false;
        if (array_key_exists($gallery, $dataImages)) {

            foreach ($dataImages[$gallery] as $currentGallery) {

                if ($currentGallery['idSubGallery'] == $idGallery and 
                    $currentGallery['subGallery'] == $subGallery) {
                    if (isset($currentGallery['images']) and !empty($currentGallery['images'])) 
                        $response = $currentGallery['images'];    
                    break;
                }
            }            
        }
        
        return $response;
    }

    /**
     * La siguiente función se encarga de validar que las imagenes esten correctas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param array $dataRooms
     * @param array $dataServices
     * @param array $dataImages
     * @return array
     * @version 27/08/2015
     */
    public function validateImages($slug, $dataRooms, $dataServices, $dataImages)
    {

        $rpTempOwner = $this->rf->get('TempOwner');

        $tempOwner = $rpTempOwner->findOneByArray(array('slug' => $slug));

        // Se declara el array de errores
        $errors = array(/*'rooms' => array(), 'otherGalleries'=> array()*/);

        // Si no existen favoritos y no tienen
        if (!isset($dataImages['favorites']) or count($dataImages['favorites']) < 8)
            $errors[/*'favorites'*/] = 'Debes Seleccionar 8 imagenes de tus galerias como favoritas';
        /*else
            $errors['favorites'] = false;*/

        if (isset($dataImages['rooms']) and !empty($dataImages['rooms'])) {

            $itRoomsImages = new \ArrayIterator($dataImages['rooms']);

            $itRooms = new \ArrayIterator($dataRooms);
            $itServices = new \ArrayIterator($dataServices);

            foreach ($dataRooms as $currentRoom) {

                $room = $this->rf->get('RoomType')
                    ->findOneByArray(array('id' => $currentRoom['type']));

                $flagError = true;

                foreach ($dataImages['rooms'] as $currentImageRoom) {

                    if ($room->getId() == $currentImageRoom['idSubGallery'] ) {
                        $flagError = false;
                        if (count($currentImageRoom['images']) < 1 or empty($currentImageRoom['images'])){

                            array_push(
                                $errors/*['rooms']*/,
                                'Debes incluir una imagen en la galería '.$currentRoom['name']
                            );
                            break;
                        } else {

                            foreach ($currentImageRoom['images'] as $currentImage) {
                                if (!isset($currentImage['name']) or is_null($currentImage['name'])) {
                                    array_push($errors,
                                        'Debes colocarle un nombre a todas las imagenes de la galería ' .
                                        $currentImageRoom['subGallery']);
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($flagError)
                    array_push($errors,
                        "Debes Colocar una imagen para la galería ".
                        $currentRoom['name']);
            }

        } else {
            //$errors['rooms'] = array();
            array_push($errors/*['rooms']*/, "Debes agregar las galerias de tus habitaciones");
        }

        if (isset($dataImages['otherGalleries']) and !empty($dataImages['otherGalleries'])) {

            $flagError = false;

            foreach ($dataImages['otherGalleries'] as $currentGallery) {
                if ($currentGallery['subGallery'] == 'Zonas comunes') {
                    if (count($currentGallery['images']) < 1) {
                        array_push($errors, 'Debes incluir una imagen en la galería Zonas Comunes');
                    } else {
                        foreach ($currentGallery['images'] as $currentImage) {
                            if (!isset($currentImage['name']) or is_null($currentImage['name'])) {
                                array_push($errors,
                                    'Debes colocarle un nombre a todas las imagenes de la galería '.
                                    $currentGallery['subGallery']);
                                break;
                            }
                        }
                    }
                } else {
                    foreach ($currentGallery['images'] as $currentImage) {
                        if (!isset($currentImage['name']) or is_null($currentImage['name'])) {
                            array_push($errors,
                                'Debes colocarle un nombre a todas las imagenes de la galería '.
                                $currentGallery['subGallery']);
                            break;
                        }
                    }
                }
            }

            //if ($flagError)
            //  array_push($errors/*['otherGalleries']*/, 'Debes incluir una galeria de Zonas Comunes');
        } else {
            array_push($errors/*['otherGalleries']*/, "Debes incluir otras galerias");
        }

        $validations = $tempOwner->getValidations();
        //if (!empty($errors['favorites']) or !empty($errors['rooms']) or !empty($errors['otherGalleries'])) {
        if (!empty($errors)) {
            $validations['galleries'] = $errors;
            $tempOwner->setProgress(3, 0);
        } else {
            $validations['galleries'] = 'OK';
            $tempOwner->setProgress(3, 1);
        }

        $tempOwner->setValidations($validations);
        $rpTempOwner->save($tempOwner);

        return new ResponseCommandBus(201, 'OK'/*,$errors*/);
    }

    /**
    * La función se encarga de actualizar la ultima sección
    * 
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @param string $slug
    * @param integer $lastSec
    * @return void
    * @version 23/07/2015
    */
    public function updateLastSection($slug, $lastSec)
    {
        $rpTempOwner = $this->rf->get('TempOwner');

        $tempOwner = $rpTempOwner
            ->findOneByArray(array('slug' => $slug));

        $tempOwner->setLastSec($lastSec);
        $tempOwner->setProgress(3,1);
        $rpTempOwner->save($tempOwner);
    }

    public function getPropertyFormData($tempOwner)
    {
        //Obtener repositorio de category
        $categoryRep = $this->rf->get('CurrencyType');
        //Obtener un array con todos los tipos de monedas
        $currency = $categoryRep->getAllCurrency();
        //Obtener repositorio de location
        $locationRep = $this->rf->get('Location');
        //Obtener la estructura de paises estados y ciudades
        $locations = $locationRep->getAll();
        //Obtener lista de lenguajes
        $languages = $this
            ->rf
            ->get('Language')
            ->getLanguagesList();
        //Obtener lista de tipos de alojamiento
        $accommodation = $this
            ->rf
            ->get('Accommodation')
            ->getAccommodationList();
        //obteniendo la data del formulario de establecimiento
        $form = $this->rf->get('TempOwner')->getDataProperty( $tempOwner );
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        return array(
            'slug' => $tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'countries' => $locations,
            'currency' => $currency,
            'language' => $languages,
            'accommodation' => $accommodation,
            'form' => $form,
        );
    }

    public function getServicesFormData($tempOwner)
    {
        $services = $this
            ->rf
            ->get('TempOwner')
            ->getServicesData($tempOwner);
        $foods = $this
            ->rf
            ->get('FoodType')
            ->findListAll();
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        //construyendo el objeto para responder la solicitud
        return array(
            'services'=>$services,
            'foods'=>$foods,
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'slug' => $tempOwner->getSlug(),
        );
    }

    public function getRoomFormData($tempOwner,$index = null)
    {
        if (isset($index)) {
            $form = $this->rf->get('TempOwner')->getDataRoom($tempOwner,$index);
            $basic_quota = $tempOwner->getPropertyForm('basic_quota');
            $form['basic_quota'] = !empty($basic_quota) ? $tempOwner->getPropertyForm('basic_quota') : 0;
        } else {
            $form = null;
        }
        $typerooms = $this
            ->rf
            ->get('RoomType')
            ->getRoomsTypesStructure();
        $sections = $this
            ->rf
            ->get('RoomFeatureType')
            ->getSpacesList();
        $services = $this
            ->rf
            ->get('RoomFeatureType')
            ->getServicesList();
        //estos son los servicios por tipo
        $services_bedroom = $this
            ->rf
            ->get('RoomFeatureType')
            ->getServicesList('Habitación');
        $services_bath = $this
            ->rf
            ->get('RoomFeatureType')
            ->getServicesList('Baño');
        $services_others = $this
            ->rf
            ->get('RoomFeatureType')
            ->getServicesList('otro');

        $beds = Bed::getBedsTypes();
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;

        $propertyData = $tempOwner->getPropertyForm();
        $termsAndConditions = $tempOwner->getTermsAndConditionsInfo();
        $acceptsChild = $propertyData['child'] == false ? false: true;
        return array(
            'slug'=>$tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'form'=>$form,
            'data'=>array(
                'TypeRoom' => $typerooms,
                'sections' => $sections,
                'services' => $services,
                'servicesBedroom' => $services_bedroom,
                'servicesBath' => $services_bath,
                'servicesOthers' => $services_others,
                'beds' => $beds,
                'acceptsChild' => $acceptsChild,
                'discountRate' => !isset($termsAndConditions['discount_rate']) ? 0.3 : $termsAndConditions['discount_rate'],
                'rateType' => !isset($propertyData['rate_type']) ? 2 : $propertyData['rate_type'],
                'tax' => !isset($propertyData['tax']) ? true : $propertyData['tax'],
                'taxRate' => !isset($propertyData['tax_rate']) ? 0.12 : $propertyData['tax_rate'] / 100,
                'agePolicy' => !isset($propertyData['agePolicy']) ? null : $propertyData['agePolicy']
            )
        );
    }

    /**
     * esta funcion retorna la estructura de datos de las habitaciones anteriormente registradas
     *
     * @author Gabriel Camacho <kbo025@gmail.com>
     * @version 14-09-2015
     *
     * @param TempOwner $tempowner
     * @param Integer $index
     * @return array
     */
    public function getRoomsFormData($tempOwner)
    {
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        return array(
            'slug'=>$tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'amount_rooms' => $tempOwner->getPropertyForm('amount_room'),
            'amount_rooms_added' => $tempOwner->getAmountRoomsAdd(),
            'rooms'=>$this->rf->get('TempOwner')->getResumeRoomForm($tempOwner)
        );
    }

    public function getPaymentFormData($tempOwner)
    {
        //Obtener repositorio de category
        $categoryRep = $this->rf->get('CurrencyType');
        //Obtener un array con todos los tipos de monedas
        $currency=$categoryRep->getAllCurrency();
        //Obtener repositorio de location
        $locationRep = $this->rf->get('Location');
        //Obtener la estructura de paises estados y ciudades
        $countries = $locationRep->getAll();
        //obtengo la informacion del establecimiento registrado en el paso 1
        $tempownerrep = $this->rf->get('TempOwner');
        $property_info = $tempownerrep->getSamePropertyPaymentInfo($tempOwner);
        //obtengo la informacion de registro de pago si existe
        $form = $tempownerrep->getPaymentData($tempOwner);
        //creo el array con el contenido
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        return array(
            'slug' => $tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'countries' => $countries,
            'currency' => $currency,
            'PropertyInfo' => $property_info,
            'form'=>$form,
        );
    }

    public function getAgreementFormData($tempOwner)
    {
        $form = $tempOwner->getTermsAndConditionsInfo();
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        return array(
            'slug' => $tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'accepted' => isset($form['accepted']) && $form['accepted'],
            'discount_rate' => isset($form['discount_rate']) ? $form['discount_rate'] : 0.3,
            'credit_days' => isset($form['credit_days']) ? $form['credit_days'] : 30,
        );
    }

    public function getStatusFormData($tempOwner)
    {
        $progress = $tempOwner->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        return array(
            'slug' => $tempOwner->getSlug(),
            'progress' => $progress,
            'percentage' => $tempOwner->evaluateProgress(),
            'validations' => $tempOwner->validate() ? 'OK' : $tempOwner->getValidations()
        );
    }

    public function haveAccess($temp)
    {
        $access = false;
        $sessionUser = $this->security->getToken()->getUser();
        if(is_string($temp)) {
            $temp = $this->rf->get('TempOwner')->findOneByArray(array('slug' => $temp));
        }
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // si el usuario temporal solicitado existe
            if(isset($temp) and ($temp instanceof TempOwner)) {

                //Si es un usuario admin
                if ($this->security->isGranted('ROLE_ADMIN') OR
                    $this->security->isGranted('ROLE_SALES_EXEC') OR
                    $this->security->isGranted('ROLE_TELEMARKETING'))
                    $access = true;
                else if ($this->security->isGranted('ROLE_TEMPOWNER'))
                    $access = ($temp->getUserId() == $sessionUser);
                else
                    $access = false;
            }
        }
        return $access;
    }

    public function isAdmin()
    {
        return $this->security->isGranted('ROLE_ADMIN_FIREWALL');
    }

    public function getAllData($temp)
    {
        $response = array();
        $tempownerrep = $this->rf->get('TempOwner');
        $progress = $temp->getProgress();
        if($this->isAdmin())
            $progress[6]=0;
        $response['slug'] = $temp->getSlug();
        $response['progress'] = $progress;
        $response['percentage'] = $temp->evaluateProgress();
        $response['property'] = $tempownerrep->getDataProperty($temp, true);
        $response['services'] = $tempownerrep->getServicesData($temp, true, true);
        $response['services_priority'] = $tempownerrep->getResumeServices($temp);
        $response['rooms'] = $tempownerrep->getAllDataRoom($temp);
        $response['galleries'] = $temp->getGalleryForm();
        return $response;
    }

    public function getTermAndConditionPdfData(TempOwner $temp)
    {
        $data = array();
        $property = $temp->getPropertyForm();
        $terms = $temp->getTermsAndConditionsInfo();
        $data['discount_rate'] = isset($terms['discount_rate']) ? $terms['discount_rate'] : null;
        $data['amount_rooms'] = isset($property['amount_rooms']) ? $property['amount_rooms'] : null;
        $data['address'] = isset($property['address']) ? $property['address'] : null;
        $data['fax'] = isset($property['fax']) ? $property['fax'] : null;
        $data['email'] = isset($property['email']) ? $property['email'] : null;
        $data['user_slug'] = $temp->getSlug();
        $data['hotel_slug'] = isset($property['slug']) ? $property['slug'] : null;
        return $data;
    }
}
