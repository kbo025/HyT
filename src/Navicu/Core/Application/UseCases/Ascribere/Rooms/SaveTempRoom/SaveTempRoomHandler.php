<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Rooms\SaveTempRoom;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ManagerImageInterface;
use Navicu\Core\Domain\Model\Entity\Room;
use Navicu\Core\Domain\Model\Entity\Bedroom;
use Navicu\Core\Domain\Model\Entity\Livingroom;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\Entity\RoomFeature;
use Navicu\Core\Domain\Model\Entity\RateByPeople;
use Navicu\Core\Domain\Model\Entity\RateByKid;
use Navicu\Core\Domain\Model\ValueObject\Slug;

class SaveTempRoomHandler implements Handler
{
    /**
     * Instancia del ManagerImage
     *
     * @var ManagerImageInterface
     */
    protected $managerImage;

    /**
     * Método Get de $managerImage
     *
     * @param ManagerImageInterface $managerImage
     */
    public function setManagerImage(ManagerImageInterface $managerImage)
    {
        $this->managerImage = $managerImage;
    }

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        //obtengo la data del comando
        $request = $command->getRequest();
        //die(var_dump($request['rates_by_kids']));
        //obtengo los repositorios de TempOwner, Category y Location del repositoryFactory
        $tempowner_repository = $rf->get('TempOwner');
        $feature_type_repository = $rf->get('RoomFeatureType');
        $room_type_rep = $rf->get('RoomType');

        $spa = $feature_type_repository->find(9);
        $dormitorio = $feature_type_repository->find(1);
        $bano = $feature_type_repository->find(2);
        $balcon = $feature_type_repository->find(3);
        $terraza = $feature_type_repository->find(4);
        $sala = $feature_type_repository->find(8);
        $vestidor = $feature_type_repository->find(5);
        $comedor =  $feature_type_repository->find(6);
        $cocina =  $feature_type_repository->find(7);
        $piscina =  $feature_type_repository->find(10);
        $jardin =  $feature_type_repository->find(11);
        $lavadero =  $feature_type_repository->find(12);
        $oldGalleryName = null;
        $newGalleryName = null;
        $oldGalleryId = null;
        $newGalleryId = null;

            //Busco el usuario
            $tempowner = $tempowner_repository->findOneByArray(
                array('slug'=>$request['slug'])
            );
            $features = $feature_type_repository->getAllWithKeys();
            //si existe
            if(!empty($tempowner)) {
                //creo una nueva habitacion
                $room = new Room();
                ///////////////////////////////llenando y validando los datos de la habitacion//////////////////////////////////
                /**tipo y subtipo**/
                if(!empty($request['subtype'])) {
                    $roomType = $room_type_rep->find($request['subtype']);
                    if (isset($roomType)) {
                        $newGalleryId = $request['subtype'];
                        $room->setType($roomType);
                    } else {
                        $errors[] = 'Existe un problema con el tipo de habitación, verifica que esté correcto o comunicate con nosotros';
                    }
                } else {
                    if (!empty($request['type'])) {
                        $roomType = $room_type_rep->find($request['type']);
                        if (isset($roomType)) {
                            $newGalleryId = $request['type'];
                            $room->setType($roomType);
                        } else {
                            $errors[] = 'Existe un problema con el tipo de habitación, verifica que esté correcto o comunicate con nosotros';
                        }
                    } else
                        $errors[] = 'Debes indicarnos el tipo de habitación que deseas agregar';
                }

                /**cantidad de habitacion**/
                if(
                    !empty($request['amount']) &&
                    is_integer($request['amount'])
                ){
                    $room->setAmountRooms($request['amount']);
                } else {
                    $errors[] = 'Existe un problema con la cantidad de habitaciones, verifica que esté correcto o comunicate con nosotros';
                }

                $room->setBaseAvailability(!empty($request['base_availability']) ? $request['base_availability'] : 0);

                /**Tamaño de la habitacion**/
                if (!empty($request['size'])) {
                    if ( is_numeric($request['size']) ) {
                        $room->setSize($request['size']);
                    } else {
                        $errors[] = 'Existe un problema con el tamaño de la habitación, verifica que esté correcto o comunicate con nosotros';
                    }
                }

                /**Politica de fumadores**/
                $room->setSmokingPolicy(!empty($request['smoking_policy']));

                /**Cantidad de personas**/
                if (
                    !empty($request['amount_persons']) &&
                    is_integer($request['amount_persons']) &&
                    $request['amount_persons'] > 0
                )
                    $room->setMaxPeople($request['amount_persons']);
                else {
                    $errors[] = 'Existe un problema con la cantidad maxima de personas permitidas en la habitación, verifica que esté correcto o comunicate con nosotros';
                    $request['amount_persons'] = 0;
                }

                if (
                    !empty($request['minPeople']) &&
                    is_integer($request['minPeople']) &&
                    $request['minPeople'] > 0 &&
                    $request['minPeople'] <= $request['amount_persons']
                )
                    $room->setMinPeople($request['minPeople']);
                else
                    $errors[] = 'Existe un problema con la cantidad minima de personas permitidas en la habitación, verifica que esté correcto o comunicate con nosotros';

                $room->setKidPayAsAdult(!empty($request['kid_pay_as_adult']));

                //incremento por persona
                if (!empty($request['increment'])) {
                    /**Estado de tipo de Variacion**/
                    $room->setIncrement(true);
                    if ( isset($request['type_rate_people'])) {
                        if ( $request['type_rate_people'] == 1 || $request['type_rate_people'] == 2) {
                            $room->setVariationTypePeople($request['type_rate_people']);
                            $room->setSameIncrementAdult(!empty($request['same_increment_adult']));
                            if ( !empty($request['rates_by_people'])) {
                                for ($i=0; $i<$room->getMaxPeople(); $i++) {
                                    if (isset($request['rates_by_people'][$i])) {
                                        $newrate = new RateByPeople($request['rates_by_people'][$i]);
                                        $room->addRatesByPeople($newrate);
                                    } else
                                        $errors[] = 'Existe un problema con los incrementos de adultos asignados, verifica que esté correcto o comunicate con nosotros';
                                }
                            } else
                                $errors[] = 'Debes indicar el aumento para las cantidades de personas que pueden alojarse en la habitación';
                        } else
                            $errors[] = 'Existe un problema con el tipo de incremento de adultos, verifica que esté correcto o comunicate con nosotros';
                    } else
                        $errors[] = 'Debes indicar el tipo de aumento para las cantidades de personas que pueden alojarse en la habitación';
                } else {
                    $room->removeAllRatesByPeople();
                    $room->setSameIncrementAdult(false);
                }

                //Incremento sin niños
                if (!empty($request['increment_kid']) && empty($request['kid_pay_as_adult'])) {
                    $room->setIncrementKid(true);
                    if (isset($request['type_rate_kid'])) {
                            $room->setVariationTypeKids($request['type_rate_kid']);
                            $room->setSameIncrementKid($request['same_increment_kid']);
                            if ( !empty($request['rates_by_kids'])) {
                                for ($i=0; $i<count($request['rates_by_kids']); $i++) {
                                    for($j = 0; $j < count($request['rates_by_kids'][$i]); $j++) {
                                        if (isset($request['rates_by_kids'][$i][$j])) {
                                            $newRateKid = new RateByKid($request['rates_by_kids'][$i][$j]);
                                            $newRateKid->setIndex($i);
                                            $room->addRatesByKid($newRateKid);
                                        } else {
                                            $errors[] = 'Existe un problema con los incrementos para niños asignados, verifica que esté correcto o comunicate con nosotros';
                                        }
                                    }
                                }
                            } else
                                $errors[] = 'Debes indicar el aumento para las cantidades de niños que pueden alojarse en la habitación';
                    } else
                        $errors[] = 'Debes indicar el tipo de aumento para las cantidades de niños que pueden alojarse en la habitación';
                } else {
                    $room->removeAllRatesByKid();
                    $room->setSameIncrementKid(false);
                }

                /**Agregando los servicios**/
                    if (isset($request['services'])) {
                        foreach ($request['services'] as $key => $service_type) {
                            foreach ($service_type as $id => $value) {
                                if($value){
                                    $feature = new RoomFeature();
                                    if(isset($features[$id])){
                                        $feature->setFeature($features[$id]);
                                        $room->addFeature($feature);
                                    }else{
                                        $errors[$key][$id]=array('message'=>'Invalid Service');
                                        $errors[] = 'Existe un problema con el tipo de habitación, verifica que esté correcto o comunicate con nosotros';
                                    }
                                }
                            }
                        }
                    }

                if (!empty($request['balcony'])) {
                    $feature = new RoomFeature();
                    if (isset($balcon)) {
                        $feature->setFeature($balcon);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Balcón", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['terrace'])) {
                    $feature = new RoomFeature();
                    if (isset($terraza)) {
                        $feature->setFeature($terraza);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Terranza", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['pool'])) {
                    $feature = new RoomFeature();
                    if (isset($piscina)) {
                        $feature->setFeature($piscina);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Piscina", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['spa'])) {
                    $feature = new RoomFeature();
                    if (isset($spa)) {
                        $feature->setFeature($spa);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Spa", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['garden'])) {
                    $feature = new RoomFeature();
                    if (isset($jardin)) {
                        $feature->setFeature($jardin);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Jardín", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['kitchen'])) {
                    $feature = new RoomFeature();
                    if(isset($cocina)){
                        $feature->setFeature($cocina);
                        $room->addFeature($feature);
                    }else{
                        $errors[] = 'Existe un problema en el campo "Cocina", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (!empty($request['laundry'])) {
                    $feature = new RoomFeature();
                    if (isset($lavadero)) {
                        $feature->setFeature($lavadero);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Lavandería", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if(
                    !empty($request['amount_baths']) &&
                    is_integer($request['amount_baths']) &&
                    $request['amount_baths']>0
                ) {
                    $feature = new RoomFeature();
                    if(isset($bano)){
                        $feature->setFeature($bano);
                        $feature->setAmount($request['amount_baths']);
                        $room->addFeature($feature);
                    }else{
                        $errors[] = 'Existe un problema en el campo "Baños", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if(
                    !empty($request['amount_dresser']) &&
                    is_integer($request['amount_dresser']) &&
                    $request['amount_dresser']>0
                ) {
                    $feature = new RoomFeature();
                    if(isset($vestidor)){
                        $feature->setFeature($vestidor);
                        $feature->setAmount($request['amount_dresser']);
                        $room->addFeature($feature);
                    }else{
                        $errors[] = 'Existe un problema en el campo "Cantidad de Vestidores", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }
                if(
                    !empty($request['amount_dining_room']) &&
                    is_integer($request['amount_dining_room']) &&
                    $request['amount_dining_room']>0
                ) {
                    $feature = new RoomFeature();
                    if(isset($comedor)){
                        $feature->setFeature($comedor);
                        $feature->setAmount($request['amount_dining_room']);
                        $room->addFeature($feature);
                    } else {
                        $errors[] = 'Existe un problema en el campo "Comedor", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if (
                    !empty($request['amount_bedrooms']) &&
                    is_integer($request['amount_bedrooms']) &&
                    $request['amount_bedrooms'] > 0
                ) {
                    $feature = new RoomFeature();
                    if(isset($dormitorio)){
                        try {
                            $feature->setFeature($dormitorio);
                            $feature->setAmount($request['amount_bedrooms']);
                            $room->addFeature($feature);
                            foreach ( $request['beds_combinations'] as $comb ) {
                                $bedroom = new Bedroom();
                                $bedroom->setBath(
                                    !empty($comb['bathBedroom'])
                                );
                                $bedroom->setAmountPeople(
                                    isset($comb['numPersonBedroom']) ?
                                    $comb['numPersonBedroom'] :
                                    0
                                );
                                foreach ($comb['beds'] as $newbed ){
                                    $bedroom->addBed(
                                        new Bed(isset($newbed['typeBed']) ? $newbed['typeBed'] : 0,$newbed['numTypeBed'])
                                    );
                                }
                                $room->addBedroom($bedroom);
                            }
                        } catch(\Exception $e) {
                            $errors[] = 'Existe un problema con los tipos de cama, verifica que esté correcto o ponte en contacto con nosotros';
                        }
                    } else {
                        $errors[] = 'Existe un problema con las combinaciones de camas, verifica que esté correcto o ponte en contacto con nosotros';
                    }
                } else {
                    $errors[] = 'Existe un problema en el campo "Cantidad de dormitorios", verifica que esté correcto o ponte en contacto con nosotros';
                }

                if(
                    !empty($request['amount_living_room']) &&
                    is_integer($request['amount_living_room']) &&
                    $request['amount_living_room'] > 0
                ) {
                    $feature = new RoomFeature();
                    if (isset($sala)) {
                        $feature->setFeature($sala);
                        $feature->setAmount($request['amount_living_room']);
                        $room->addFeature($feature);
                        foreach ($request['livingrooms'] as $liv) {
                            $living = new LivingRoom();
                            $living->setAmountCouch(
                                $liv['numCouchRoom']
                            );
                            $living->setAmountPeople(
                                $liv['numLivingPerson']
                            );
                            $room->addLivingRoom($living);
                        }
                    } else {
                        $errors['amount_bedrooms'] = array(
                            'message' => 'Invalid Value'
                        );
                        $errors[] = 'Existe un problema en el campo "Balcón", verifica que esté correcto o ponte en contacto con nosotros';
                    }
                }

                if( isset($request['index']) ) {
                    $oldroom = $tempowner->getRoom($request['index']);
                    if ($oldroom) {
                        $oldGalleryId = $oldroom['type'];
                        $oldGalleryName = $oldroom['name'];
                        $tempowner->unsetRoom($request['index']);
                        if (!$tempowner->existsRoom($room)) {
                            $tempowner->addRoom( $room , $request['index'] );
                        } else {
                            $errors[] = 'Existe un problema al acceder al tipo de habitacion, ponte en contacto con nosotros para solucionarlo';
                        }
                        $newGalleryName = $room->generateName();
                    } else {
                        $errors[] = 'Existe un problema al acceder al tipo de habitacion, ponte en contacto con nosotros para solucionarlo';
                    }
                } else {
                    if(!$tempowner->existsRoom($room)) {
                        $tempowner->addRoom( $room );
                    } else {
                        $errors[] = 'Ya existe un tipo de  habitación con estas caracteristicas';
                    }
                }

                if($tempowner->getPropertyForm('amount_room') < $tempowner->getAmountRoomsAdd())
                    $global_errors['rooms'][] ='Cantidad de habitaciones agregadas excedida';
                else
                    if($tempowner->getPropertyForm('amount_room') > $tempowner->getAmountRoomsAdd())
                        $global_errors['rooms'][] ='Cantidad de habitaciones agregadas incompleta';

                if($tempowner->getPropertyForm('basic_quota') < $tempowner->getBasicQuotasRoomsAdd())
                    $global_errors['rooms'][] ='Cuota básica de habitaciones excedida';
                else
                    if($tempowner->getPropertyForm('basic_quota') > $tempowner->getBasicQuotasRoomsAdd())
                        $global_errors['rooms'][] ='Cuota básica de habitaciones incompleta';

                if (empty($errors)) {
                    if (!is_null($newGalleryId)){
                        $tempowner->setGalleryForm(
                            $this->updateGallery(
                                $tempowner->getGalleryForm(),
                                $newGalleryId,
                                $oldGalleryId,
                                $oldGalleryName,
                                $newGalleryName,
                                $request['slug']
                            )
                        );
                    }
                    $validations = $tempowner->getValidations();
                    //$galleries = $validations['galleries'];
                    if(empty($global_errors)) {
                        if ($tempowner->getLastsec()<3) {
                            $tempowner->setLastsec(3);
                        }
                        //marco habitaciones como completo
                        $tempowner->setProgress(2,1);
                        $validations['rooms'] = 'OK';
                    } else {
                        //marco habitaciones como completo
                        $tempowner->setProgress(2,0);
                        $validations['rooms'] = $global_errors['rooms'];
                    }

                    //marco galeria como incompleto
                    $tempowner->setProgress(3,0);
                    //$galleries[] = 'Debes incluir una galeria para '.$room->generateName();
                    //$validations['galleries']['rooms'][] = $global_errors['galleries'];
                    $tempowner->setValidations($validations);

                    //almaceno el usuario temporal
                    /*$tempowner_repository->save( $tempowner );

                    $name = $room->getName();
                    $room->generateName();

                    if ($name != $room->getName()) {
                        $room->changeNameGallery($rf, $name);
                    }*/

                    $response =  new ResponseCommandBus(
                        201,
                        'OK',
                        $tempowner->getRoomsForm()
                    );
                } else {
                    $response = new ResponseCommandBus(400,'Bad request',$errors);
                }
            } else {
                $response = new ResponseCommandBus(401,'Unauthorized');
            }
        return $response;
    }

    /**
     * La siguiente función se encarga de actualizar los valores
     * de las galerías si se sustituye el tipo de habitación
     *
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $dataGallery
     * @param $newGalleryId
     * @param $oldGalleryId
     * @param $oldRoomName
     * @param $newNameRoom
     */
    private function updateGallery($dataGallery, $newGalleryId, $oldGalleryId, $oldNameGallery, $newNameGallery,$slug)
    {
        $slugOldName = Slug::generateSlug($oldNameGallery);
        $slugName = Slug::generateSlug($newNameGallery);

        $oldDirectory = 'property/'.$slug.'/rooms/'.$slugOldName;
        $newDirectory = 'property/'.$slug.'/rooms/'.$slugName;

        if (isset($dataGallery['rooms']))
            foreach ($dataGallery['rooms'] as &$currentRoom) {
                if ($currentRoom['idSubGallery'] == $oldGalleryId) {
                    $currentRoom['idSubGallery'] = $newGalleryId;
                    $currentRoom['subGallery'] = $newNameGallery;
                    if (isset($currentRoom['images']))
                        foreach ($currentRoom['images'] as $key => $currentImage) {
                            $aux = [];
                            $aux['path'] = str_replace($slugOldName, $slugName,$currentImage['path']);
                            $aux['name'] = $currentImage['name'];
                            if(isset($currentImage['favorite']))
                                $aux['favorite'] = $currentImage['favorite'];
                            if(isset($currentImage['progress']))
                                $aux['progress'] = $currentImage['progress'];

                            $currentRoom['images'][$key] = $aux;
                        }
                    break;
                }
            }

        if (isset($dataGallery['favorites'])) {
            $auxFavorites = [];
            foreach ($dataGallery['favorites'] as $key => $currentFavorite) {
                $aux = [];
                $aux['path'] =  str_replace($slugOldName, $slugName,$currentFavorite['path']);
                $aux['subGallery'] = $newNameGallery;

                $auxFavorites[] = $aux;
            }
            $dataGallery['favorites'] = $auxFavorites;
        }

        $this->managerImage->changePath($oldDirectory, $newDirectory);

        return $dataGallery;
    }
}
