<?php

namespace Navicu\InfrastructureBundle\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Navicu\Core\Domain\Repository\TempOwnerRepository;
use Navicu\Core\Domain\Model\Entity\Property;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\Entity\Bed;
use Navicu\Core\Domain\Model\ValueObject\EmailAddress;
use Navicu\Core\Domain\Model\ValueObject\Password;
use Navicu\Core\Domain\Model\Entity\User as UserDomain;
use Navicu\InfrastructureBundle\Entity\User as UserInfrastructure;
use Navicu\InfrastructureBundle\Repositories\UserRepository;

/**
 * TempOwnerRepository Implementa los metodos de la interface del dominio TempOwnerRepositoryInterface
 *
 * @author Gabriel Camacho <kbo025@gmail.com>
 * @author Currently Working: Gabriel Camacho (05-05-15)
 */
class DbTempOwnerRepository extends DbBaseRepository implements TempOwnerRepository
{
	/**
	 * metedo registerUser se encarga de persistir un usuario temporal en la BD
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param TempOwnerEntiry $temp Usuario que se debe persistir
	 */
	public function registerUser($temp)
	{
		$user = new UserInfrastructure();
		$user->setUsername($temp->getUserId()->getUsername());
		$user->setEmail($temp->getUserId()->getEmail());
		$user->setPlainPassword($temp->getUserId()->getPassword());

        $rolerep = $this->getEntityManager()->getRepository('NavicuInfrastructureBundle:Role');
        $role = $rolerep->findByName('ROLE_TEMPOWNER');

		$user->addRole($role);
		$user->setEnabled(true);
		$user->setTempOwner($temp);

		$temp->setUserId($user);
		$temp->setSlug($temp->getUserId()->getUsername());
        $temp->setValidations(array(
            'property' => array('Debes acceder a la sección de Establecimiento e ingresar los datos que te solicitamos'),
            'services' => array('Debes acceder a la sección de Servicios e indicarnos los servicios generales que presta tu establecimiento'),
            'rooms' => array('Debes acceder a la sección de Habitaciones y completar los datos de las clases de habitaciones que ofreces'),
            'galleries' => array('Debes acceder a la sección Galerias e incluir fotografias de las distintas áreas de tu establecimiento'),
            'paymentInfo' => array('Debes acceder a la sección de Datos de Pago e ingresar los datos que te solicitamos'),
            'termsAndConditions' => array('Debes acceder y completar la sección de Terminos y Condiciones'),
        ));
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->persist($temp);
		$this->getEntityManager()->flush();
	}

	/**
	 * verifica si existe un usuario por su username 
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param String $data
	 */
	public function usernameExist($data)
	{
		$query = $this->getEntityManager()->createQuery("SELECT u FROM Navicu\InfrastructureBundle\Entity\User u WHERE u.username = :username");
		$query->setParameters(['username'=>$data]);
		$users1 = $query->getResult();

		$query = $this->getEntityManager()->createQuery("SELECT u FROM Navicu\InfrastructureBundle\Entity\User u WHERE u.username = :username");
		$query->setParameters(['username'=>strtolower($data)]);
		$users2 = $query->getResult();

        return !empty($users1) || !empty($users2);
	}

	/**
	 * verifica si existe un usuarios por su email
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param String $data
	 */
	public function emailExist($data)
	{
		$query = $this->getEntityManager()->createQuery("SELECT u FROM Navicu\InfrastructureBundle\Entity\User u WHERE u.email = :email");
		$query->setParameters(array('email' => $data));
		$users = $query->getResult();

        return !empty($users);
	}

	/**
	 * Busca un usuario por su username
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param  String
	 * @return TempOnwer
	 */
	public function findOneByUsername($username)
	{
		$userrep = $this->getEntityManager()->getRepository('User');
		$user=$this->findOneBy(array('username' => $username));

        if (!empty($user)) {
			$temp = $this->findOneById($user->getId());

			if (!empty($temp)) {
				$userDomain = new UserDomain();
				$userDomain->setUsername($user->getUsername());
				$userDomain->setPassword(new Password($user->getPassword()));
				$userDomain->setEmail(new EmailAddress($user->getEmail()));
				$userDomain->setRol($user->getRoles());
				$temp->setUserId($userDomain);

				return $temp;
			}
		}

        return null;
	}

	/**
	 * Busca TempOwner por sus atributos
	 *
	 * @author Gabriel Camacho <kbo025@gmail.com>
	 * @param  Array
	 * @return TempOwner
	 */
	public function findOneByArray($array)
	{
        return $this->findOneBy($array);
	}

	public function getResumeRoomForm($temp)
	{
		$response = array();
		$roomtype_rep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:RoomType');
        $galleries = $temp->getGalleryForm();
		foreach($temp->getRoomsForm() as $index => $room)
		{
			$newroom = array();
			$roomtype = $roomtype_rep->find($room['type']);
			if ($roomtype->getLvl()==0) {
				$newroom['typeroom'] = $room['name'];
				$newroom['subroom'] = null;
			} else {
				$newroom['subroom'] = null;
				$newroom['typeroom'] = $room['name'];
			}
			$newroom['numRooms'] = $room['amount_rooms'];
			$newroom['numPeople'] = $room['max_people'];
            $newroom['image'] = null;

            if(!empty($galleries['rooms'])) {
                foreach ($galleries['rooms'] as $galleryRoom) {
                    if ($galleryRoom['idSubGallery'] == $room['type']) {
                        if(!empty($galleryRoom['images'])) {
                            $newroom['image'] = $galleryRoom['images'][0]['path'];
                        }
                    }
                }
            }

			$response[$index] = $newroom;
		}
		return $response;
	}

	public function getDataRoom($temp,$index)
	{
		$response = array();
		$roomtype_rep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:RoomType');
		$features = $this
			->getEntityManager()
			->getRepository('NavicuDomain:RoomFeatureType')
			->getAllWithKeys();
		$arrayrooms = $temp->getRoomsForm();
		if (isset($arrayrooms[$index])) {
			$room = $arrayrooms[$index];
			$roomtype = $roomtype_rep->find($room['type']);
			if ($roomtype->getLvl()==0) {
				$response['typeroom'] = $roomtype->getId();
				$response['subroom'] = null;
			} else {
				$response['subroom'] = $roomtype->getId();
				$response['typeroom'] = $roomtype->getParent()->getId();
			}
			if(!empty($room['smoking_policy'])){
				$response['smoking'] = $room['smoking_policy'];
			}

            $response['base_availability'] = !empty($room['base_availability']) ? $room['base_availability'] : 0;

			if(!empty($room['amount_rooms'])){
				$response['numRooms'] = $room['amount_rooms'];
			}

			if(!empty($room['max_people'])){
				$response['numPeople'] = $room['max_people'];
				$response['numKid'] = $room['max_people'] - 1;
			}

			if(!empty($room['min_people']))
				$response['minPeople'] = $room['min_people'];

			if(!empty($room['size'])){
				$response['sizeRoom'] = $room['size'];
			}
			if(!empty($room['max_price_person'])){
				$response['prices']['priceMax'] = $room['max_price_person'];
			}
			if(!empty($room['min_price_person'])){
				$response['prices']['priceMin'] = $room['min_price_person'];
			}
            $response['prices']['ratePeople'] = [];
            if(!empty($room['rates_by_people'])) {
				foreach($room['rates_by_people'] as $rate){
					array_push(
						$response['prices']['ratePeople'],
						array(
							'amountRate'=> !isset($rate['amount_rate']) ? 0 : $rate['amount_rate'],
							'numberPeople'=> $rate['number_people']
						)
					);
				}
			}

			if (!empty($room['rates_by_kids'])) {

				$response['prices']['rateKids'] = array();
                foreach ($room['variation_type_kids'] as $element) {
                    array_push($response['prices']['rateKids'], array());
                }
				foreach($room['rates_by_kids'] as $rate){
					array_push(
						$response['prices']['rateKids'][$rate['index']],
						array(
							'amountRate'=> !isset($rate['amount_rate']) ? 0 : $rate['amount_rate'],
							'numberKid'=> $rate['number_kid']
						)
					);
				}
			}

			$response['prices']['increment'] = $room['increment'];
            $response['prices']['incrementKid'] = !empty($room['increment_kid']);
            $response['prices']['kidPayAsAdult'] = !empty($room['kid_pay_as_adult']);
            $response['prices']['sameIncrementAdult'] = !empty($room['same_increment_adult']);
            $response['prices']['sameIncrementKid'] = !empty($room['same_increment_kid']);

			if(!empty($room['variation_type_people']))
				$response['prices']['variationTypePeople'] = (string)$room['variation_type_people'];

			if (!empty($room['variation_type_kids']))
				$response['prices']['variationTypeKid'] = $room['variation_type_kids'];
			$services = array();
			$response['dresser'] = 0;
			$response['diner'] = 0;
			$response['bath'] = 0;
			$response['numLiving'] = 0;
			
			foreach($room['features'] as $feature){

				if (isset($feature['feature'])) {
					if($features[$feature['feature']]->getType()==1){
						$services[$feature['feature']] = true;
					} else {
						switch($feature['feature']){
							case 1:
								$response['combinationsBeds'] = array();
								if (isset($room['bedrooms'])) {
									foreach($room['bedrooms'] as $bedroom){
										$beds = array();
										foreach($bedroom['beds'] as $bed){
											array_push(
												$beds,
												array(
													'numTypeBed' => $bed['amount'],
													'typeBed' => $bed['type'],
												)
											);
										}
										array_push(
											$response['combinationsBeds'],
											array(
												'bathBedroom' => $bedroom['bath'],
												'numPersonBedroom' => $bedroom['amount_people'],
												'beds' => $beds
											)
										);
									}
								}
								$response['numBedroom'] = $feature['amount'];
								break;
							case 2:
								$response['bath'] = $feature['amount'];
								break;
							case 3:
								$response['balcony'] = true;
								break;
							case 4:
								$response['terrace'] = true;
								break;
							case 5:
								$response['dresser'] = $feature['amount'];
								break;
							case 6:
								$response['diner'] = $feature['amount'];
								break;
							case 7:
								$response['cooking'] = true;
								break;
							case 8:
								$response['livings'] = array();
								if (isset($room['livingrooms'])) {
									foreach($room['livingrooms'] as $livingroom){
										array_push(
											$response['livings'],
											array(
												'numCouchRoom' => $livingroom['amount_couch'],
												'numLivingPerson' => $livingroom['amount_people'],
											)
										);	
									}
								}
								$response['numLiving'] = $feature['amount'];
								break;
							case 9:
								$response['spa'] = true;
								break;
							case 10:
								$response['pool'] = true;
								break;
							case 11:
								$response['garden'] = true;
								break;
							case 12:
								$response['laundry'] = true;
								break;
						}
					}
				}
			}
			$response['featuresRoom'] = array();
			for( $i=0; $i<count($features); $i++){
				$response['featuresRoom'][$i] = (isset($services[$i]) && $services[$i]);
				//si es un servicio
				if ((isset($features[$i]))&&($features[$i]->getType()==1)) {
					if ($features[$i]->getParent()->getId()==1) {
						$response['featuresRoomBeedRoom'][$i] = (isset($services[$i]) && $services[$i]);
					} else {
						$response['featuresRoomBeedRoom'][$i] = false;
					}
					if ($features[$i]->getParent()->getId()==2) {
						$response['featuresRoomBath'][$i] = (isset($services[$i]) && $services[$i]);
					} else {
						$response['featuresRoomBath'][$i] = false;
					}
					if ($features[$i]->getParent()->getId()==13) {
						$response['featuresRoomOthers'][$i] = (isset($services[$i]) && $services[$i]);
					} else {
						$response['featuresRoomOthers'][$i] = false;
					}
				} else {
					$response['featuresRoomBeedRoom'][$i] = false;
					$response['featuresRoomBath'][$i] = false;
					$response['featuresRoomOthers'][$i] = false;
				}
			}
			$response['index'] = $index;
		}
		return $response;
	}

	public function getAllDataRoom($temp)
	{
		$response = array();
		$roomtype_rep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:RoomType');
		$features = $this
			->getEntityManager()
			->getRepository('NavicuDomain:RoomFeatureType')
			->getAllWithKeys();
		foreach($temp->getRoomsForm() as $index => $room)
		{
			$newroom = array();
			$roomtype = $roomtype_rep->find($room['type']);
			if ($roomtype->getLvl()==0) {
				$newroom['typeroom'] = $roomtype->getTitle();
				$newroom['subroom'] = null;
			} else {
				$newroom['subroom'] = $roomtype->getTitle();
				$newroom['typeroom'] = $roomtype->getParent()->getTitle();
			}
			$newroom['name'] = $room['name'];
			if(!empty($room['smoking_policy'])){
				$newroom['smoking'] = $room['smoking_policy'];
			}
			if(!empty($room['amount_rooms'])){
				$newroom['numRooms'] = $room['amount_rooms'];
			}
			if(!empty($room['max_people'])){
				$newroom['numPeople'] = $room['max_people'];
			}
			if(!empty($room['size'])){
				$newroom['sizeRoom'] = $room['size'];
			}
			if(!empty($room['max_price_person'])){
				$newroom['price']['priceMax'] = $room['max_price_person'];
			}
			if(!empty($room['min_price_person'])){
				$newroom['price']['priceMin'] = $room['min_price_person'];
			}
			$newroom['price']['increment'] = $room['increment'];
			$newroom['price']['incrementKid'] = $room['increment_kid'];
			if(!empty($room['rates_by_peoples'])){
				$newroom['price']['ratePeople'] = array();
				foreach($room['rates_by_peoples'] as $rate){
					array_push(
						$newroom['price']['ratePeople'],
						array(
							'amountRate'=> isset($rate['amount_rate']) ? $rate['amount_rate'] : 0,
							'numberPeople'=> $rate['number_people']
						)
					);
				}
			}

			if (!empty($room['rates_by_kids'])) {
				$newroom['price']['rateKid'] = array();
				foreach ($room['rates_by_kids'] as $rate) {
					array_push(
						$newroom['price']['rateKid'],
						[
							'amountRate'=> isset($rate['amount_rate']) ? $rate['amount_rate'] : 0,
							'numberKid'=> $rate['number_kid']
						]
					);
				}
			}

			if(!empty($room['variation_type_people'])){
				$newroom['price']['typeAmount'] = $room['variation_type_people'];
			}

			if (!empty($room['variation_type_kid']))
				$newroom['price']['typeAmountKid'] = $room['variation_type_people'];

			$newroom['featuresRoom'] = array();
			$newroom['dresser'] = 0;
			$newroom['diner'] = 0;
			$newroom['bath'] = 0;
			$newroom['numLiving'] = 0;
			foreach($room['features'] as $feature) {

				if (isset($feature['feature'])) {
					if($features[$feature['feature']]->getType()==1){
						$newroom['featuresRoom'][] = array('name'=>$features[$feature['feature']]->getTitle(),'priority'=>$features[$feature['feature']]->getPriority());
					} else {
						switch($feature['feature']){
							case 1:
								$newroom['combinationsBeds'] = array();
								
								if (isset($room['bedrooms'])) {
									foreach($room['bedrooms'] as $bedroom){
										$beds = array();
										foreach($bedroom['beds'] as $bed){
											$newbed = new Bed($bed['type'],$bed['amount']);
											array_push(
												$beds,
												array(
													'numTypeBed' => $newbed->getAmount(),
													'typeBed' => $newbed->getTypeString()
												)
											);
										}
										array_push(
											$newroom['combinationsBeds'],
											array(
												'bathBedroom' => $bedroom['bath'],
												'numPersonBedroom' => $bedroom['amount_people'],
												'beds' => $beds
											)
										);
									}
								}
								$newroom['numBedroom'] = $feature['amount'];
								break;
							case 2:
								$newroom['bath'] = $feature['amount'];
								break;
							case 3:
								$newroom['balcony'] = true;
								break;
							case 4:
								$newroom['terrace'] = true;
								break;
							case 5:
								$newroom['dresser'] = $feature['amount'];
								break;
							case 6:
								$newroom['diner'] = $feature['amount'];
								break;
							case 7:
								$newroom['cooking'] = true;
								break;
							case 8:
								$newroom['livings'] = array();
								if (isset($room['livingrooms'])) {


									foreach($room['livingrooms'] as $livingroom){
										array_push(
											$newroom['livings'],
											array(
												'numCouchRoom' => $livingroom['amount_couch'],
												'numLivingPerson' => $livingroom['amount_people'],
											)
										);	
									}
								}
								$newroom['numLiving'] = $feature['amount'];
								break;
							case 9:
								$newroom['spa'] = true;
								break;
							case 10:
								$newroom['pool'] = true;
								break;
							case 11:
								$newroom['garden'] = true;
								break;
							case 12:
								$newroom['laundry'] = true;
								break;
						}
					}
				}
			}

			$newroom['index'] = $index;
			$response[] = $newroom;
		}
		return $response;
	}

	public function getDataProperty($temp, $short_schedule = false)
	{
		//Obtener repositorio de category
		$categoryRep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:Category');
		$currencyRep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:CurrencyType');
		$currencyRep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:CurrencyType');
		//Obtener repositorio de location
		$locationRep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:Location');
		//Obtener lista de lenguajes
		$languages = $this
			->getEntityManager()
			->getRepository('NavicuDomain:Language')
			->findAllWithKeys();
		//Obtener repesitorio de contactos
		$contactrep = $this
			->getEntityManager()
			->getRepository('NavicuDomain:ContactPerson');
		//obteniendo la data del formulario de establecimiento
		$form = $temp->getPropertyForm();
		if (!empty($form)) {
			if (isset($form['location'])) {
				$location = $locationRep
					->find($form['location']);
                if ($location->getLvl()==3) {
                    $form['parish'] = $location
                        ->getTitle();
                    if (!empty($location->getCityId())) {
                        $form['parish'] = $form['parish'].'('.$location->getCityId()->getTitle().')';
                    }
                    $form['city'] = $location
                        ->getParent()
                        ->getTitle();
                    $form['state'] = $location
                        ->getParent()
                        ->getParent()
                        ->getTitle();
                    $form['country'] = $location
                        ->getParent()
                        ->getParent()
                        ->getParent()
                        ->getTitle();
                }
                if ($location->getLvl()==2) {
                    $form['parish'] = null;
                    $form['city'] = $location
                        ->getTitle();
                    $form['state'] = $location
                        ->getParent()
                        ->getTitle();
                    $form['country'] = $location
                        ->getParent()
                        ->getParent()
                        ->getTitle();
                }
                if ($location->getLvl()==1) {
                    $form['parish'] = null;
                    $form['city'] = null;
                    $form['state'] = $location
                        ->getTitle();
                    $form['country'] = $location
                        ->getParent()
                        ->getTitle();
                }
                if ($location->getLvl()==0) {
                    $form['parish'] = null;
                    $form['city'] = null;
                    $form['state'] = null;
                    $form['country'] = $location
                        ->getTitle();
                }
			}
            $form['quota_basis'] = isset($form['basic_quota']) ? $form['basic_quota'] : null;
	        if(isset($form['currency'])) {
	            $form['currency_name'] = $currencyRep
	            	->find($form['currency'])
	            	->getTitle();
            }
            if(isset($form['accommodation'])) {
	            $form['accommodation_name'] =  $this
	            	->getEntityManager()
	            	->getRepository('NavicuDomain:Accommodation')
	            	->find($form['accommodation'])
	            	->getTitle();
            }
            if(isset($form['check_in'])) {
	            if (is_array($form['check_in'])) {
					$checkIn = new \DateTime($form['check_in']['date']);
	                $form['check_in'] = $checkIn->format('H:i:s');
	            }
	        }
	        if(isset($form['check_out'])) {
	            if(is_array($form['check_out'])){
					$checkOut = new \DateTime($form['check_out']['date']);
	                $form['check_out'] = $checkOut->format('H:i:s');
	            }
	        }
            foreach ($form['contacts'] as &$contact) {
            	$contact['type'] = $contactrep->getNameType($contact['type']);
            	$contact['required'] = $contactrep->getRequiredType($contact['type']);
            }
            $form['contact'] = $form['contacts'];
            unset($form['contacts']);
            $form['beds']=array(
            	'beds'=> !empty($form['beds']),
                'value_beds_extra'=> !empty($form['beds']) ? $form['beds']['beds_additional_cost'] : null,
                'prior_notice' => $form['beds']['beds_prior_notice'],
            );

			$form['cribs']=array(
                'cribs'=>!empty($form['cribs']),
                'max_cribs'=> !empty($form['cribs']) ? $form['cribs']['cribs_max'] : null,
                'value_cribs'=> !empty($form['cribs']) ? $form['cribs']['cribs_additional_cost'] : null,
                'prior_notice' => $form['cribs']['cribs_prior_notice'],
            );
            $form['mascot']=array(
            	'mascot'=>!empty($form['pets']['pets']),
               	'value_mascot'=> !empty($form['pets']['pets_additional_cost']) ? $form['pets']['pets_additional_cost'] : false
            );
            $form['cash']=array(
                'cash' => !empty($form['cash']['cash']),
				'to'=> !empty($form['cash']['max_cash']) ? $form['cash']['max_cash'] : null
			);
            $form['credit_card'] = array(
				'credit_card' => !empty($form['credit_card']),
                'credit_card_american' => !empty($form['credit_card']['credit_card_american']),
                'credit_card_master' => !empty($form['credit_card']['credit_card_master']),
                'credit_card_visa' => !empty($form['credit_card']['credit_card_visa'])
            );
            $form['amount_comercial_rooms'] = !empty($form['comercial_rooms']) ? $form['comercial_rooms'] : null;
            $form['comercial_rooms'] = !empty($form['comercial_rooms']);
            $form['currency_city_name'] =  !empty($form['city_tax_currency']) ?
	            $currencyRep
	               	->find($form['city_tax_currency'])
	                ->getTitle() :
	            null;
            $form['currency_city'] = !empty($form['city_tax_currency']) ?
            	$form['city_tax_currency'] :
            	null;
            $form['amount'] = !empty($form['city_tax']) ?
               	$form['city_tax'] :
                null;
			$form['max_night'] = !empty($form['city_tax_max_nights']) ? 
                $form['city_tax_max_nights'] :
                null;
			$form['hotel_chain'] = !empty($form['hotel_chain_name']);
            if(!empty($form['city_tax_type'])) {
	            switch ($form['city_tax_type']) {
	            	case 1: 
	                	$form['nightperson'] = 'Persona';
	                    break;
	                case 2:
	                    $form['nightperson'] = 'Noche';
	                    break;
	                case 3:
	                    $form['nightperson'] = 'Persona y Noche';
	                    break;
	                default:
	                   	null;
	            }
	        } else {
	        	$form['nightperson'] = null;
	        }
            $form['tax'] = $form['tax'] ? 1 : 0;
			$tax_rate = $form['tax_rate'];
            $form['tax_rate'] = (empty($tax_rate) ?
                0 :
                ($tax_rate<1 ? $tax_rate*100 : $tax_rate)
            );
            $form['languages']=array();
            foreach( $form['language'] as $lan ){
               	if (!empty($languages[$lan])) {
                    array_push(
                        $form['languages'],
                        $languages[$lan]->getNative()
                    );
               	}
            }
            unset($form['language']);
        }
        return $form;
	}

	public function getSamePropertyPaymentInfo($temp)
	{
		$response = array();
		$property = $temp->getPropertyForm();
		if(isset($property['location'])) {
            $location = $this
                ->getEntityManager()
                ->getRepository('NavicuDomain:Location')
                ->find($property['location']);
            if ($location->getLvl() == 3) {
                $response['parish'] = $location
                    ->getTitle();
                if (!empty($location->getCityId())) {
                    $response['parish'] = $response['parish'] . '(' . $location->getCityId()->getTitle() . ')';
                }
                $response['city'] = $location
                    ->getParent()
                    ->getTitle();
                $response['state'] = $location
                    ->getParent()
                    ->getParent()
                    ->getTitle();
                $response['country'] = $location
                    ->getParent()
                    ->getParent()
                    ->getParent()
                    ->getTitle();
            }
            if ($location->getLvl() == 2) {
                $response['parish'] = null;
                $response['city'] = $location
                    ->getTitle();
                $response['state'] = $location
                    ->getParent()
                    ->getTitle();
                $response['country'] = $location
                    ->getParent()
                    ->getParent()
                    ->getTitle();
            }
            if ($location->getLvl() == 1) {
                $response['parish'] = null;
                $response['city'] = null;
                $response['state'] = $location
                    ->getTitle();
                $response['country'] = $location
                    ->getParent()
                    ->getTitle();
            }
            if ($location->getLvl() == 0) {
                $response['parish'] = null;
                $response['city'] = null;
                $response['state'] = null;
                $response['country'] = $location
                    ->getTitle();
            }
        } else {
            $response['parish'] = null;
            $response['city'] = null;
            $response['state'] = null;
            $response['country'] = null;
        }
		$response['name'] = isset($property['name']) ? $property['name'] : null;
		$response['address'] = isset($property['address']) ? $property['address'] : null;
		return $response;
	}

	public function getPaymentData($temp)
	{
		$response = array();
		$form = $temp->getPaymentInfoForm();
		if (!empty($form)) {

			$response['tax_id'] = isset($form['tax_id']) ? $form['tax_id'] : null;
			$response['swift'] = isset($form['swift']) ? $form['swift'] : null;
			$response['same_data_property'] = $form['same_data_property'];
			if ($form['same_data_property']){
				$response = array_merge(
					$response,
					$this->getSamePropertyPaymentInfo($temp)
				);
			} else {
				if (isset($form['location'])) {
					$location = $this
						->getEntityManager()
						->getRepository('NavicuDomain:Location')
						->find( $form['location']);
                    $response['location'] = $form['location'];
                    if ($location->getLvl()==0) {
                        $response['parish'] = null;
                        $response['city'] = null;
                        $response['state'] = null;
                        $response['country'] = $location->getTitle();
                    } else if ($location->getLvl()==1) {
                        $response['parish'] = null;
                        $response['city'] = null;
                        $response['state'] = $location->getTitle();
                        $response['country'] = $location->getParent()->getTitle();
                    } else if ($location->getLvl()==2) {
                        $response['parish'] = null;
                        $response['city'] = $location->getTitle();
                        $response['state'] = $location->getParent()->getTitle();
                        $response['country'] = $location->getParent()->getParent()->getTitle();
                    } else if ($location->getLvl()==3) {
                        $response['parish'] = $location->getTitle();
                        if (!empty($location->getCityId())) {
                            $response['parish'] = $response['parish'].'('.$location->getCityId()->getTitle().')';
                        }
                        $response['city'] = $location->getParent()->getTitle();
                        $response['state'] = $location->getParent()->getParent()->getTitle();
                        $response['country'] = $location->getParent()->getParent()->getParent()->getTitle();
                    }
				}
                $response['name'] = isset($form['name']) ? $form['name'] : null;
				$response['fiscal_name'] = isset($form['fiscal_name']) ? $form['fiscal_name'] : null;
				$response['address'] = !empty($form['address']) ? $form['address'] : null;
			}
			if (isset($form['charging_system'])) {
				switch($form['charging_system']){
					case 1: $response['charging_system']='Traferencia bancaria'; break;
					//case 2: $response['charging_system']='Tarjeta de crédito'; break;
				}
			} else {
				$form['charging_system'] = null;
			}
			
			if (isset($form['currency_id'])) {
				$currency = $this
					->getEntityManager()
					->getRepository('NavicuDomain:CurrencyType')
					->find( $form['currency_id']);
				$response['currency_payment'] = $currency->getTitle();
				$response['currency_id'] = $currency->getId();
			}
			$response['account_number_part1'] = !empty($form['account']['entity']) ? $form['account']['entity'] : null;
			$response['account_number_part2'] = !empty($form['account']['office']) ? $form['account']['office'] : null;
			$response['account_number_part3'] = !empty($form['account']['control']) ? $form['account']['control'] : null;
			$response['account_number_part4'] = !empty($form['account']['account']) ? $form['account']['account'] : null;

            if (isset($form['rifName'])){
                $response['rifName'] = $form['rifName'];
            }
		}
		return $response;
	}

	public function getServicesData($temp, $as_array = false, $short_schedule = false)
	{
		$types = $this
			->getEntityManager()
			->getRepository('NavicuDomain:ServiceType')
            ->getServicesStructure();

        $data = $temp->getServicesForm();
        $structure = $this->getServicesStructure($types,$data, $as_array, $short_schedule);
        return $structure;
	}

	protected function getServicesStructure( $services, $data, $as_array = false , $short_schedule = false, &$parent = null)
	{
		$res = array();
		$foods = $this
			->getEntityManager()
			->getRepository('NavicuDomain:FoodType');
		$children = false;
		foreach ($services as $clave => $serv) {			
			$child = false;
			$newserv = array();
			$newserv['id'] = $serv['id'];
			$newserv['type'] = $serv['type'];
			$newserv['name'] = $clave;
			$newserv['status'] = false;
			switch ($serv['type']) {
				case 4:
					$newserv['data'] = array(
						'Horario'=>array(
								'Apertura' => null,
								'Cierre' => null,
								'Dias' => null,
								'Full_time' => false
							)
						);
					break;
				case 5: 
					$newserv['data'] = array('costo'=>0);
					break;
				case 8: 
					$newserv['data'] = array('Cantidad'=>null,'costo'=>0);
					break;
				default:
					$newserv['data'] = array();
			}
			if(empty($serv['subservices'])) {
				foreach ($data as $servact) {
					if ($servact['type'] == $serv['id']) {
						$newserv['status'] = true;
						$child = true;
						switch ($serv['type']) {
							case 3:
								foreach ($servact['data'] as $rest) {
									$newrest = array();
									$newrest['Nombre'] = $rest['name'];

                                    $dias = array();
                                    if(!empty($rest['schedule']['days'])) {
                                        $days = decbin($rest['schedule']['days']);
                                        if(strlen($days)<7){
                                            $days = substr('0000000',0,7-strlen($days)).$days;
                                        }
                                        $days = str_split($days);
                                        $i = 0;
                                        foreach ($days as $day) {
                                            if($day=='1') {
                                                switch($i) {
                                                    case 0: $dias['Domingo'] = true; break;
                                                    case 1: $dias['Sabado'] = true; break;
                                                    case 2: $dias['Viernes'] = true; break;
                                                    case 3: $dias['Jueves'] = true; break;
                                                    case 4: $dias['Miercoles'] = true; break;
                                                    case 5: $dias['Martes'] = true; break;
                                                    case 6: $dias['Lunes'] = true; break;
                                                }
                                            }
                                            $i++;
                                        }
                                    }
                                    $newrest['Dias_abirtos'] = $dias;
									/*$newrest['Horario'] =  array(
										'Apertura' => empty($rest['schedule']['opening']) ? null : substr($rest['schedule']['opening'],0,$short_schedule ? 5 : 8),
										'Cierre' => empty($rest['schedule']['closing']) ? null : substr($rest['schedule']['closing'],0,$short_schedule ? 5 : 8),
										'Dias' => !empty($rest['schedule']['days']) ? $rest['schedule']['days'] : null,
										'Full_time' => !empty($rest['schedule']['full_time'])
									);*/
									$newrest['TipoCocina'] = $foods->find($rest['type'])->getTitle();
									$newrest['Desayuno'] =  array(
										'status' =>	(!empty($rest['breakfast_time'])),
										'Apertura' => (!empty($rest['breakfast_time'])) ?
                                            substr($rest['breakfast_time']['opening'],0,$short_schedule ? 5 : 8) :
											null ,
										'Cierre' => (!empty($rest['breakfast_time'])) ?
                                            substr($rest['breakfast_time']['closing'],0,$short_schedule ? 5 : 8) :
											null ,
										'Dias' => (!empty($rest['breakfast_time'])) ?
											$rest['breakfast_time']['days'] :
											null ,
										'Full_time' => (!empty($rest['breakfast_time'])) ?
											$rest['breakfast_time']['full_time'] :
											false ,
									);
									$newrest['Almuerzo'] =  array(
										'status' =>	(!empty($rest['lunch_time'])),
										'Apertura' => (!empty($rest['lunch_time'])) ?
                                            substr($rest['lunch_time']['opening'],0,$short_schedule ? 5 : 8) :
                                            null ,
										'Cierre' => (!empty($rest['lunch_time'])) ?
                                            substr($rest['lunch_time']['closing'],0,$short_schedule ? 5 : 8) :
										 	null ,
										'Dias' => (!empty($rest['lunch_time'])) ?
										    $rest['lunch_time']['days'] :
											null ,
										'Full_time' => (!empty($rest['lunch_time'])) ?
											$rest['lunch_time']['full_time'] :
										 	false ,
									);
									$newrest['Cena'] =  array(
										'status' =>	(!empty($rest['dinner_time'])),
										'Apertura' => (!empty($rest['dinner_time'])) ?
                                            substr($rest['dinner_time']['opening'],0,$short_schedule ? 5 : 8) :
											null ,
										'Cierre' => (!empty($rest['dinner_time'])) ?
                                            substr($rest['dinner_time']['closing'],0,$short_schedule ? 5 : 8) :
										    null ,
										'Dias' => (!empty($rest['dinner_time'])) ?
											$rest['dinner_time']['days'] :
											null ,
										'Full_time' => (!empty($rest['dinner_time'])) ?
											$rest['dinner_time']['full_time'] :
											false ,
									);
									if($rest['buffet_carta']==1)
										$newrest['BuffetoCarta'] = 'Buffet';
									else if($rest['buffet_carta']==2)
										$newrest['BuffetoCarta'] = 'Carta';
									else if($rest['buffet_carta']==3)
										$newrest['BuffetoCarta'] = 'Ambos';
									else 
										$newrest['BuffetoCarta'] = null;
									$newrest['MenuDietetico'] =  $rest['dietary_menu'];
									$newrest['Descripcion'] = isset($rest['description']) ? $rest['description'] : null;
									$newrest['status'] = $rest['status'];
									array_push($newserv['data'],$newrest);
								}
							break;
							case 2: 
								foreach ($servact['data'] as $bar) { 
									$newbar = array();
									$newbar['Comida'] = $bar['food'];
									if(isset($bar['food_type'])){
										$newbar['tipo_comida'] = $foods->find($bar['food_type'])->getTitle();
									}
									$newbar['Horario'] = array(
										'Apertura' => empty($bar['schedule']['opening']) ? null : substr($bar['schedule']['opening'],0,$short_schedule ? 5 : 8),
										'Cierre' => empty($bar['schedule']['closing']) ? null : substr($bar['schedule']['closing'],0,$short_schedule ? 5 : 8),
										'Dias' => $bar['schedule']['days'],
										'Full_time' => $bar['schedule']['full_time']
									);
									$newbar['Edad_Min'] = $bar['min_age'];
									$newbar['Descripcion'] = isset($bar['description']) ? $bar['description'] : null;
									$newbar['Nombre'] = $bar['name'];
									$newbar['status'] = $bar['status'];
									if ( $bar['type'] == 1 ) {
										$newbar['Tipo'] = "Bar";
									}
									if ( $bar['type'] == 2 ) {
										$newbar['Tipo'] = "Discoteca";
									}
									array_push( $newserv['data'], $newbar );
								}
							break;
							case 4:
								$newserv['data']['Horario']['Apertura'] = substr($servact['schedule']["opening"],0,$short_schedule ? 5 : 8);
								$newserv['data']['Horario']['Cierre'] = substr($servact['schedule']["closing"],0,$short_schedule ? 5 : 8);
								$newserv['data']['Horario']['Dias'] = $servact['schedule']["days"];
								$newserv['data']['Horario']['Full_time'] = $servact['schedule']["full_time"];
							break;
							case 5: 
								if ($servact['free']) {
									$newserv['data']['costo'] = 0;
								} else {
									$newserv['data']['costo'] = 1;
								}
							break;
							case 6: 
								foreach ($servact['data'] as $salon) { 
									$newsalon = array();
									if ( $salon['type'] == 1 ) {
										$newsalon['Tipo'] = "Salón";
									}
									if ( $salon['type'] == 2 ) {
										$newsalon['Tipo'] = "Auditorio";
									}
									if ( $salon['type'] == 3 ) {
										$newsalon['Tipo'] = "Teatro";
									}
									$newsalon['LuzNatural'] = !empty($salon['natural_light']);
									$newsalon['Capacidad_Max'] = $salon['capacity'];
									$newsalon['Tamano'] = $salon['size'];
									$newsalon['Cantidad'] = isset($salon['quantity']) ? $salon['quantity'] : null;
									$newsalon['Nombre'] = $salon['name'];
									$newsalon['status'] = $salon['status'];
									array_push($newserv['data'],$newsalon);
								}
							break;
							case 8: 
								$newserv['data']['Cantidad'] = $servact['quantity'];
							break;
						}
					}
				}
			} else {
				$newserv['subservices'] = $this->getServicesStructure( $serv['subservices'], $data, $as_array, $short_schedule, $child);
				$newserv['status'] = $child;
			}
			$children = $children || $child;
			if ($as_array) {
				$res[] = $newserv;
			} else {
				$res[$clave] = $newserv;
			}
		}
		$parent = $children;
		return $res;
	}

	public function getResumeServices($temp)
	{
		$res = array();
		$services = $this
			->getEntityManager()
			->getRepository('NavicuDomain:ServiceType')
			->findAllwithKeys();
		$active_services = $temp->getServicesForm();
		foreach ($active_services as $service) {
			$root = $services[$service['type']];
			while ($root->getParent()!=null ) {
				$root = $root->getParent();
			}
			$res[] = array(
				'name' => $services[$service['type']]->getTitle(),
				'priority' => $services[$service['type']]->getPriority(),
				'root' => $root->getTitle()
			);
		}
		return $res;
	}

	public function getAllData($temp)
	{
		$response = array();
		$response['slug'] = $temp->getSlug();
		$response['progress'] = $temp->getProgress();
		$response['percentage'] = $temp->evaluateProgress();
		$response['property'] = $this->getDataProperty($temp, true);
		$response['services'] = $this->getServicesData($temp, true, true);
		$response['services_priority'] = $this->getResumeServices($temp);
		$response['rooms'] = $this->getAllDataRoom($temp);
		$response['galleries'] = $temp->getGalleryForm();
		return $response;
	}

	/**
	 * Obtener los establecimiento temporales dado un comercial
	 *
	 * @param $commercialId
	 * @return array
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @version 06/04/2016
	 */
	public function findByCommercialId($commercialId)
	{
		return $this->createQueryBuilder('t')
			->where('
                t.nvc_profile = :commercialId
                ')
			->setParameters(array(
					'commercialId' => $commercialId
				)
			)->getQuery()->getResult();
	}

    /**
     * Funcion encargada de realizar la busqueda y/o filtrado del listado de establecimiento temporales
     *
     * @param $param array, array con la solicitud de la busqueda
     *
     * @param string $searchVector, nombre del vector de busqueda
     * @return mixed
     * @version 26/01/2017
     * @author Isabel Nieto <isabelcnd@gmail.com>
     */
    public function tempPropertyByFilter($param, $searchVector)
    {
        $arrayOfMatch[] = "expired_date";
        $separatedWords = $this->separateByType($param['search'], $arrayOfMatch);

        // Para funcionar mediante busqueda por vectores
        $tsQuery = $this->getTsQuery($separatedWords['tsQuery'], $searchVector);
        $tsRank = $this->getTsRank($separatedWords['tsQuery'], $searchVector);

        $additionalCriteria = $this->normalizedWordToAdditionalCriteria($separatedWords['where']);

        return $this
            ->select('
                id,
                slug,
                username,
                name, 
                phones, 
                discount_rate, 
                contact_name, 
                accommodation_title, 
                location,
                nvc_profile_name,
                nvc_profile_id, 
                expired_date,
                progress
            ')
            ->from('admin_temp_property_view')
            ->where($tsQuery, $additionalCriteria)
            ->paginate($param['page'], $param['number_result'])
            ->order($param['order_by'], $param['order_type'], $tsRank)
            ->getResults();
    }
}
