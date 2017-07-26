<?php

namespace Navicu\Core\Application\UseCases\Ascribere\RegisterTempServices;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\TempOwner;
use Navicu\Core\Domain\Model\Entity\ServiceType;
use Navicu\Core\Domain\Model\Entity\Restaurant;
use Navicu\Core\Domain\Model\Entity\Bar;
use Navicu\Core\Domain\Model\Entity\Salon;
use Navicu\Core\Domain\Model\Entity\PropertyService;
use Navicu\Core\Domain\Model\ValueObject\Schedule;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\ArrayCollection;

/**
* Clase para ejecutar el caso de uso RegisterTempServices
* @author Gabriel Camacho <kbo025@gmail.com>
* @author Currently Working: Gabriel Camacho
* @version 08/06/2015
*/

class RegisterTempServicesHandler implements Handler
{

	/**
	*	Contenedor de PropertyService que se agregaran a tempowner
	*	@var ArrayCollection
	*/
	protected $services;

	/**
	*	conjunto de errores encontrados durante el proceso de ejecucion del comando
	*	@var array
	*/
	protected $errors;

	/**
	*	array con todos los objetos ServicesType almacenados
	* 	@var array
	*/
	protected $array_services;

	/**
	*	array con todos los tipos de comidas almacenados
	* 	@var array
	*/
	protected $array_foods;

	/**
	*	Codigo de respuesta
	* 	@var array
	*/
	protected $code;

	/**
	*	Array de errores del negocio, usado para incluir a las validaciones finales
	* 	@var array
	*/
	protected $global_errors;

	public function __construct()
	{
		$this->services = new ArrayCollection();
		$this->errors = array();
		$this->global_errors = array();
		$this->code = 201;
	}

    /**
     *    ejecuta el caso de uso 'Registro temporal de servicios'
     *
     * @param Command $command Objeto Command contenedor de la soliccitud del usuario
     * @param RepositoryFactoryInterface  $rf
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
	public function handle(Command $command, RepositoryFactoryInterface $rf = null)
	{
        //obtengo la data del comando
        $request = $command->getRequest();

        //obtengo el repositorio de tempowner
		$tempowner_repository = $rf->get('TempOwner');

		//obtengo la lista de tipos de servicios del repositorio
		$this->array_services = $rf
			->get('ServiceType')
			->findAllWithKeys();

		$this->array_foods = $rf
			->get('FoodType')
			->findAllWithKeys();

        //Busco el usuario
        $tempowner = $tempowner_repository->findOneByArray(
            array('slug'=>$request['slug'])
        );
		//si existe
		if(!empty($tempowner)){
			if(is_array($request['services'])){
				$this->addServices($request['services']);
			} else {
				return new ResponseCommandBus(500,'services must be a array');
			}

			//agregando los servicios validos a tempowner
			foreach ($this->services as $service) {
				$tempowner->getServices()->add($service);
			}
			$validations = $tempowner->getValidations();
			if (!empty($this->global_errors)) {
                $validations['services'] = $this->global_errors;
                $tempowner->setProgress(1,0);
			} else {
				$validations['services'] = 'OK';
				//si el usuario estaba en una seccion anterior y terminó la actual se actualiza su estado
				if ($tempowner->getLastsec()<2) {
					$tempowner->setLastsec(2);
				}

    			//marco el formulario de registro de servicios como completado
				$tempowner->setProgress(1,1);
			}

            $tempowner->setServicesForm(null);
			$tempowner->setValidations($validations);
			$tempowner_repository->save($tempowner);

			$response = new ResponseCommandBus($this->code,($this->code==201 ? 'OK' : 'Bad request'),$this->errors);

        } else {
            $response = new ResponseCommandBus(404,'Not Found');
        }
        return $response;
	}

	/**
	 *	funcion recursiva que va recorriendo la estructura de servicios y almacenando los resultados
	 *	en las estructuras services y errors de la clase
	 *	@param $services Array
	 */
	protected function addServices($services)
	{
		$errors = array();
		$global_errors = array();
		foreach ($services as $name => $service) {
			//array de errores
			$errors[$name] = array();
			//si el tipo de servico esta definido
			if (isset( $this->array_services[$service['id']])) {
				//y el servicio esta marcado como true
				//if ($service['status']) {
					//reviso cual es el tipo de servicio
					switch ($service['type']) {
						case 0:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
						break;
						case 2:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							foreach ($service['data'] as $databar)
							{
								$bar = new Bar();
								$bar->setName($databar['Nombre']);
								$bar->setFood(!empty($databar['Comida']));
								if($bar->getFood()) {
									$foodtype = null;
									foreach($this->array_foods as $food) {
										if (empty($foodtype)) {
											if(!empty($databar['tipo_comida'])) {
												$foodtype = ($databar['tipo_comida'] == $food->getTitle()) ? $food : null;
											}
										}
									}
									$bar->setFoodType($foodtype);
								}
								try {
									$bar->setSchedule(
										new Schedule(
											new \DateTime($databar['Horario']['Apertura']),
											new \DateTime($databar['Horario']['Cierre']),
											isset($databar['Horario']['Dias']) ? $databar['Horario']['Dias'] : null,
											isset($databar['Horario']['Full_time']) ? $databar['Horario']['Full_time'] : null
										)
									);
								} catch( \Exception $e ) {
									$this->code = 400;
                                    $global_errors[] = 'Existe un problema con el horario del bar "'.$databar['Nombre'].'", verifica que esté correcto';
								}
								$bar->setMinAge( $databar['Edad_Min'] );
								$bar->setDescription($databar['Descripcion']);
								$bar->setStatus( $databar['status'] );
								if($databar['Tipo']=="Bar"){
									$bar->setType(1);
								} else if($databar['Tipo']=="Discoteca") {
									$bar->setType(2);
								} else {
                                    $global_errors[] = 'Existe un problema con el bar "'.$databar['Nombre'].'", debes escoger que tipo de local es';
                                }
                                $propserv->addBar($bar);
							}
							break;
						case 3:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							foreach ($service['data'] as $datarest) {
								$restaurant = new Restaurant();
								$restaurant->setName($datarest['Nombre']);
								$foodtype = null;
								foreach($this->array_foods as $food) {
									if (empty($foodtype)) {
										$foodtype = ($datarest['TipoCocina'] == $food->getTitle()) ? $food : null;
									}
								}
								$restaurant->setType($foodtype);
								$restaurant->setStatus($datarest['status']);
                                $dias = [false,false,false,false,false,false,false];
                                foreach($datarest['Dias_abirtos'] as $dia => $value) {
                                    if ($value) {
                                        switch(strtolower($dia)){
                                            case 'lunes': $dias[0] = true;  break;
                                            case 'martes': $dias[1] = true; break;
                                            case 'miercoles': $dias[2] = true; break;
                                            case 'jueves': $dias[3] = true; break;
                                            case 'viernes': $dias[4] = true; break;
                                            case 'sabado': $dias[5] = true; break;
                                            case 'domingo': $dias[6] = true; break;
                                        }
                                    }
                                }
								try {
									$restaurant->setSchedule( new Schedule(null, null, $dias, true)) ;
								} catch( \Exception $e ) {
									$this->code = 400;
                                    $errors[] = 'Existe un problema con el horario del Local "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
                                    $global_errors[] = 'Existe un problema con el horario del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto';
								}
								if(!empty($datarest['Desayuno']['status'])){
									try {
										$restaurant->setBreakfastTime(
											new Schedule(
												new \DateTime($datarest['Desayuno']['Apertura']),
												new \DateTime($datarest['Desayuno']['Cierre']),
												isset($datarest['Desayuno']['Dias']) ? $datarest['Desayuno']['Dias'] : null,
												isset($datarest['Desayuno']['Full_time']) ? $datarest['Desayuno']['Full_time'] : null
											)
										);
									} catch( \Exception $e ) {
										$this->code = 400;
                                        $errors[] = 'Existe un problema con el horario del desayuno del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
                                        $global_errors[] = 'Existe un problema con el horario del desayuno del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
									}
								}
								if (!empty($datarest['Almuerzo']['status'])) {
									try {
										$restaurant->setLunchTime(
											new Schedule(
												new \DateTime($datarest['Almuerzo']['Apertura']),
												new \DateTime($datarest['Almuerzo']['Cierre']),
												isset($datarest['Almuerzo']['Dias']) ? $datarest['Almuerzo']['Dias'] : null,
												isset($datarest['Almuerzo']['Full_time']) ? $datarest['Almuerzo']['Full_time'] : null
											)
										);
									} catch( \Exception $e ) {
										$this->code = 400;
                                        $errors[] = 'Existe un problema con el horario del almuerzo del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
                                        $global_errors[] = 'Existe un problema con el horario del almuerzo del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
									}
								}
								if(!empty($datarest['Cena']['status'])){
									try {
										$restaurant->setDinnerTime(
											new Schedule(
												new \DateTime($datarest['Cena']['Apertura']),
												new \DateTime($datarest['Cena']['Cierre']),
												isset($datarest['Cena']['Dias']) ? $datarest['Cena']['Dias'] : null,
												isset($datarest['Cena']['Full_time']) ? $datarest['Cena']['Full_time'] : null
											)
										);
									} catch( \Exception $e ) {
										$this->code = 400;
                                        $errors[] = 'Existe un problema con el horario de cena del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
                                        $global_errors[] = 'Existe un problema con el horario de cena del restaurant "'.$datarest['Nombre'].'", verifica que esté correcto o comunicate con nosotros';
									}
								}
								$restaurant->setDietaryMenu(!empty($datarest['MenuDietetico']));
								if($datarest['BuffetoCarta']=='Buffet'){
									$restaurant->setBuffetCarta(1);
								} else if ($datarest['BuffetoCarta']=='Carta') {
									$restaurant->setBuffetCarta(2);
								} else if ($datarest['BuffetoCarta']=='Ambos') {
			            			$restaurant->setBuffetCarta(3);
            					} else {
                                    $this->code = 400;
                                    $errors[] = 'Existe un problema con el restaurant "'.$datarest['Nombre'].'", debe especificar tipo de menú (buffet, carta o ambos)';
                                    $global_errors[] = 'Existe un problema con el restaurant "'.$datarest['Nombre'].'", debe especificar tipo de menú (buffet, carta o ambos)';
                                }
                                $restaurant->setDescription($datarest['Descripcion']);
								$propserv->addRestaurant($restaurant);
							}
							break;
						case 4:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							try {
								$propserv->setSchedule(
									new Schedule(
										new \DateTime($service['data']['Horario']['Apertura']),
										new \DateTime($service['data']['Horario']['Cierre']),
										isset($service['data']['Horario']['Dias']) ? $service['data']['Horario']['Dias'] : null,
										isset($service['data']['Horario']['Full_time']) ? $service['data']['Horario']['Full_time'] : null
									)
								);
							} catch (\Exception $e) {
								$this->code = 400;
                                $errors[] = 'Existe un problema con el horario, verifica e intenta de nuevo';
                                $global_errors[] = 'Existe un problema con el servicio '.$name.', verifica que sea correcto' ;
							}
							break;
						case 5:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							$propserv->setFree(!$service['data']['costo']);
							break;
						case 6:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							foreach($service['data'] as $index => $datasalon)
							{
								$salon = new Salon();
								$salon->setName(
									isset($datasalon['Nombre']) ?
									$datasalon['Nombre'] :
									null
								);
								$salon->setSize($datasalon['Tamano']);
								$salon->setDescription(isset($datasalon['Descripcion']) ? $datasalon['Descripcion'] : null);
								$salon->setQuantity(isset($datasalon['Cantidad']) ? $datasalon['Cantidad'] : null);
								$salon->setCapacity(isset($datasalon['Capacidad_Max']) ? $datasalon['Capacidad_Max'] : null);

								if ($datasalon['Tipo'] == "Salón") {
									$salon->setType(1);
								} else if ($datasalon['Tipo'] == "Auditorio") {
									$salon->setType(2);
								} else if ($datasalon['Tipo'] == "Teatro") {
									$salon->setType(3);
								} else {
                                    $global_errors[] = 'Existe un problema con el Salón '.($index+1).', verifica que sea correcto';
                                }

								if (isset($datasalon['Nombre'])) {
									$salon->setName($datasalon['Nombre']);
								} else {
									if ($salon->getType()==1) {
										$salon->setName("Salon ".($index+1)." (".$datasalon['Capacidad_Max']." Personas)");
									}
									if ($salon->getType()==2) {
										$salon->setName("Auditorio ".($index+1)." (".$datasalon['Capacidad_Max']." Personas)");
									}
									if ($salon->getType()==3) {
										$salon->setName("Teatro ".($index+1)." (".$datasalon['Capacidad_Max']." Personas)");
									}
								}

								$salon->setStatus($datasalon['status']);
								$salon->setNaturalLight(!empty($datasalon['LuzNatural']));
								$propserv->addSalon($salon);
							}
							break;
						case 8:
							$propserv = new PropertyService();
							$propserv->setType($this->array_services[$service['id']]);
							$propserv->setQuantity($service['data']['Cantidad']);
							$propserv->setFree(!$service['data']['costo']);
							break;
						case 9:
						case 7:
						case 1:
							$this->addServices($service['subservices']);
						break;
					}
					if($service['status']) {
						if($service['type'] != 9 && $service['type'] != 7 && $service['type'] != 1) {
							if($propserv->validate()) {
								$this->services->add($propserv);
							} else {
								$this->code = 400;
                                $global_errors[] = 'Existe un problema con el servicio "'.$service['name'].'", por favor verifica';
							}
						}
					} else {
						if ( $this->array_services[$service['id']]->getRequired()) {
							array_push(
								$global_errors,
								$name.' es un servicio requerido'
							);
						}
					}
				//}
			} else {
				$this->code = 400;
                $errors[] = 'Existe un problema con el servicio "'.$service['name'].'", verifica e intenta de nuevo';
			}
		}
		foreach ($errors as $error) {
			if (!empty($error)) {
				$this->errors[] = $error;
			}
		}
        $this->global_errors = array_merge($this->global_errors,$global_errors);
	}
}
