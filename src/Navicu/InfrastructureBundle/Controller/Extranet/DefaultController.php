<?php
namespace Navicu\InfrastructureBundle\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\CreateTempOwner\CreateTempOwnerCommand;
use Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand;
use Navicu\Core\Application\UseCases\RegisterTempServices\RegisterTempServicesCommand;
use Navicu\Core\Application\UseCases\Ascribere\Images\RegisterTempImage\RegisterTempImageCommand;
use Navicu\Core\Application\UseCases\RegisterTempFavoritesImages\RegisterTempFavoritesImagesCommand;
use Navicu\Core\Application\UseCases\DeleteTempFavoriteImage\DeleteTempFavoriteImageCommand;
use Navicu\Core\Application\UseCases\DeleteTempImage\DeleteTempImageCommand;
use Navicu\Core\Application\UseCases\EditTempImage\EditTempImageCommand;
use Navicu\Core\Application\UseCases\SaveTempRoom\SaveTempRoomCommand;
use Navicu\Core\Application\UseCases\SelectTempRoom\SelectTempRoomCommand;
use Navicu\Core\Application\UseCases\DeleteTempRoom\DeleteTempRoomCommand;
use Navicu\Core\Application\UseCases\AdvanceSection\AdvanceSectionCommand;
use Navicu\Core\Application\UseCases\AcceptTermsAndConditions\AcceptTermsAndConditionsCommand;
use Navicu\Core\Application\UseCases\AddPaymentInfo\AddPaymentInfoCommand;
use Navicu\Core\Application\UseCases\SortImages\SortImagesCommand;
use Navicu\Core\Application\UseCases\Extranet\GetReservationsForProperty\GetReservationsForPropertyCommand;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Navicu\Core\Application\UseCases\Admin\RegisterOwnerUser\RegisterOwnerUserCommand;
use Navicu\Core\Domain\Model\Entity\Bed;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Zend\Serializer\Adapter\Json;
use Navicu\Core\Application\UseCases\PropertyInventory\IncludeIntoSession\IncludeIntoSessionCommand;
use Navicu\Core\Application\UseCases\Extranet\FindRoomWithoutAvailability\FindRoomWithoutAvailabilityCommand;
use Navicu\Core\Application\UseCases\Admin\GetReservationDetails\GetReservationDetailsCommand;
use Navicu\Core\Application\UseCases\Extranet\NotifyTheUnavailabilityInProperties\NotifyTheUnavailabilityInPropertiesCommand;

class DefaultController extends Controller
{
    /**
     * La siguiente función muestra la vista del index del inventario
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 09/10/2015
     * @return Response
     */
    public function homeAction($slug)
    {
        if ($slug) {
            $data["slug"] = $slug;
        } else {
            $data["properties"] = $this->get("SessionService")->userOwnerSession()->getProperties();
        }
        $command = new IncludeIntoSessionCommand($data);
        $this->get('CommandBus')->execute($command);

        $data["slug"] = $slug;
        $command = new NotifyTheUnavailabilityInPropertiesCommand($data);
        $dailyAvailability = $this->get('CommandBus')->execute($command);
        return $this->render(
            "NavicuInfrastructureBundle:Extranet:newIndex.html.twig",
            array("data" => json_encode($dailyAvailability->getData()),
                "slug" => $dailyAvailability->getData()['slug'])
            );
   }

    /**
    * La siguiente función muestra la vista del nuevo index del inventario
    * Ademas que ubicará los locales que tenga a cargo el admin/hotelero para avisar que existen
    * un conjunto de hoteles que estan proximos a vencer las tarifas y necesita cargar
    * mas.
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @author Isabel Nieto <isabelcndgmail.com>
    * @param Request $request
    * @version 10/05/2016
    * @return JsonResponse
     */
    public function newHomeAction($slug) {
        if ($slug) {
            $data["slug"] = $slug;

            $command = new NotifyTheUnavailabilityInPropertiesCommand($data);

            $response = $this->get('CommandBus')->execute($command);
            return $this->render("NavicuInfrastructureBundle:Extranet:newIndex.html.twig",
                array("data" => json_encode($response->getData()),
                    "slug" => $response->getData()['slug'])
            );
        }
   }

    /**
     *   esta envia al cliente la plantilla de la seccion que tiene pendiente o de la que 
     *   solicite
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function registerAction($slug, $level)
    {

        $tempownerrep = $this->getDoctrine()->getRepository('NavicuDomain:TempOwner');
        $tempOwner = $tempownerrep->findOneBy( array('slug'=>$slug) );
        $serviceFormTempOwner = $this->get('FormTempOwner');

        //validando el acceso a las funciones del formulario
        if (!$serviceFormTempOwner->haveAccess($tempOwner)) 
            return new Response('Unauthorized',401);
            //return $this->render('TwigBundle:Exception:error401.html.twig');

        //Se busca cual es la sección por donde se encuentra el usuario
        $progress = $tempOwner->getProgress();
        if (is_null($level)) {
            $seccion = $tempOwner->getLastsec();
            if ($progress[0] == 0) {
                $level = 'welcome';
            } else {
                if (($progress[6]==1) && (!$serviceFormTempOwner->isAdmin())) {
                    $level = 'preview';
                } else {
                    if ($seccion == 0)
                        $level = 'property';
                    else if ($seccion == 1)
                        $level = 'services';
                    else if ($seccion == 2)
                        $level = 'rooms';
                    else if ($seccion == 3)
                        $level = 'gallery';
                    else if ($seccion == 4)
                        $level = 'payment';
                    else if ($seccion == 5)
                        $level = 'agreement';
                    else if ($seccion == 6)
                        $level = 'preview';
                    else if ($seccion == 7)
                        $level = 'status';
                    else
                        $level = 'property';
                }
            }
        } else {
            if (($progress[6]==1) && (!$serviceFormTempOwner->isAdmin())) {
                $level = 'preview';
            }
        }
            
        switch($level)
        {
            case 'welcome':
                return $this->redirect($this->generateUrl('navicu_welcome'));

            case 'property':
                $response = $serviceFormTempOwner->getPropertyFormData($tempOwner);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegister.html.twig',
                    array("data"=>json_encode($response), "slugTemp" => $slug)
                );

            case 'services':
                $response = $serviceFormTempOwner->getServicesFormData($tempOwner);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegister2.html.twig',
                    array("data"=>json_encode($response), "slugTemp" => $slug)
                );

            case 'rooms':
                $form = $tempOwner->getRoomsForm();
                /*si no existen habitaciones le envio el formulario de registro de habitaciones
                si si existen le envio a la vista de habitaciones agregadas*/
                if (empty($form)) {
                    $response = $serviceFormTempOwner->getRoomFormData($tempOwner);
                    return $this->render(
                        'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterRoom.html.twig',
                        array("rooms"=>json_encode($response), "slugTemp" => $slug, 'basicQuota' => $tempOwner->getPropertyForm('basic_quota'))
                    );
                } else {
                    $response = $serviceFormTempOwner->getRoomsFormData($tempOwner);
                    return $this->render(
                        'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterRoomView.html.twig',
                        array('rooms'=>json_encode($response), "slugTemp" => $slug, 'basicQuota' => $tempOwner->getPropertyForm('basic_quota'))
                    );
                }

            case 'gallery':
                $serviceTempOwner = $this->get('RegisterTempOwner');
                $lastSec = null;
                $dataProperty = $serviceTempOwner->getDataSection($slug,1, $lastSec);

                // Si no existe el slug no debe ingresar imagenes
                if (!isset($dataProperty['slug']) and is_string($dataProperty['slug'])) {

                    $progress = $tempOwner->getProgress();
                    $percentage = $tempOwner->evaluateProgress();

                    return $this->render(
                        'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterPhotos.html.twig', 
                        array(
                            'slugTemp' => $slug,
                            'galleries' => json_encode(array(
                                'progress' => $progress,
                                'percentage' => $percentage,
                                'isValid' => false))));
                } else {
                    $dataRooms = $serviceTempOwner->getDataSection($slug, 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($slug, 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($slug,4,$lastSec);

                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $response = $serviceFormTempOwner->getArrayGalleries($dataRooms, $dataServices,$dataImages,$tempOwner);    
                    return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterPhotos.html.twig', 
                        array(
                            'slugTemp' => $slug,
                            'galleries' => $response));
                }

            case 'payment':
                $response = $serviceFormTempOwner->getPaymentFormData($tempOwner);
                //retornar respuesta
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterPayment.html.twig',
                    array('response'=>json_encode($response), "slugTemp" => $slug)
                );

            case 'agreement':
                $response = $serviceFormTempOwner->getAgreementFormData($tempOwner);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterAgreement.html.twig',
                    array(
                        'response'=>json_encode($response),
                        "slugTemp" => $slug
                    )
                );

            case 6:
            case 'preview':
                $data = $serviceFormTempOwner->getAllData($tempOwner);
                //$data = $tempownerrep->getAllData($tempOwner);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterPreview.html.twig',
                        array(
                        'response'=>json_encode($data),
                        "slugTemp" => $slug
                    )
                );

            case 'status':
                $response = $serviceFormTempOwner->getStatusFormData($tempOwner);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterStatus.html.twig',
                    array (
                        "response" => json_encode($response),
                        "slugTemp" => $slug
                    )
                ) ;

            default: return new Response('Unauthorized',401);
        }
    }

    /**
     *   Registro del hotelero como usuario temporal
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function registerOwnerAction(Request $request)
    {
        $command = new CreateTempOwnerCommand();

        $translation = $this->get('translator')->trans('share.action.sign_up');
        
        $form = $this->createFormBuilder($command)
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('password', 'password', array('invalid_message' => 'Passwords do not match'))
            ->add('save', 'submit', array('label' => $translation))
            ->getForm();


        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $response = $this->get('CommandBus')->execute($command);
            $slug = $response->getData()['slug'];
            
            $username = $command->getUserName();
            $password = $command->getPassword();
            $email = $command->getEmail();

            //Se inicia sesión del usuario
            if($response->getStatusCode() == 200){

                $logger = $this->get('logger');
                $log = [];
                $log['username'] = $username;
                $log['email'] = $email;
                $log['tempOwner'] = $username;
                $logger->info('temp_owner.new:'.json_encode($log));

                if ($this->login($username, $password)) {
                    return $this->redirect($this->generateUrl('navicu_welcome'));
                }
            }else{
                $form->addError(new FormError($response->getMessage()));
           }
        }

        return $this->render(
            'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterOwner.html.twig',
            array('form' => $form->createView(), 'form2' => $form->createView())
        );
    }

    /**
     * La siguiente función verifica que un usuario se encuentre registrado y 
     * inicia la sesión del usario automatica
     * 
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param string $username
     * @param string $password
     * @return boolean
     * @version 21/07/2015
     */
    private function login($username, $password)
    {
        $user_manager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

        //Se busca al usuario por su username
        $user = $user_manager->loadUserByUsername($username);

        $encoder = $factory->getEncoder($user);

        //Se verifica si password corresponde
        //Si el password corresponde se inicia la sesión
        if ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) {
            $token = new UsernamePasswordToken($user, null, "extranet_owner", $user->getRoles());
            $this->get("security.context")->setToken($token); 
            return true;
        }

        return false;
    }

    /**
     * Registro del formulario del establecimiento
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function registerPropertyAction( Request $request )
    {
        //si la solicitud es ajax
        if ($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                $serviceFormTempOwner = $this->get('FormTempOwner');
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    //obtengo y decodifico el JSON
                    $data = json_decode($request->getContent(), true);
                    $data['form']['slug'] = $data['slug'];
                    //creo un comando para crear la tempproperty
                    $command = new RegisterTempPropertyCommand($data['form']);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    //valido el comando
                    $validator = $this->get('validator');
                    $errors = $validator->validate($command);
                    //si hay errores
                    if (count($errors) > 0) {
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] = $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this->get('CommandBus')->execute($command);
                        //se envia la respuesta
                        return new JsonResponse($response->getArray(), $response->getStatusCode());
                    }
                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     *   Almacena los servicios seleccionados por el usuario en el formulario de servicios
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function registerTempServicesAction( Request $request )
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if ($request->isXmlHttpRequest()) {
            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    //creo el comando
                    $command = new RegisterTempServicesCommand($data);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    //obtengo el servicio validator
                    $validator = $this->get('validator');
                    //valido el comando
                    $errors = $validator->validate($command);
                    //si tiene errores
                    if (count($errors) > 0) {
                        //envio un 400 con los errores encontrados
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] = $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this
                            ->get('CommandBus')
                            ->execute($command);
                        //se envia la respuesta
                        return new JsonResponse(
                            $response->getArray(),
                            $response->getStatusCode()
                        );
                    }
                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     *   inserta o actualiza una habitacion en el espacio temporal
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function saveTempRoomAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if( $request->isXmlHttpRequest() ) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    $command = new SaveTempRoomCommand($data);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());

                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $serviceFormTempOwner = $this->get('FormTempOwner');

                    //se ejecuta el comando
                    $response = $this->get('CommandBus')->execute($command);
                    $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);
                    $response2 = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);
                    //se envia la respuesta
                    return new JsonResponse($response->getArray(), $response->getStatusCode());

                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     *   muestra el formulario de habitación
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function saveTempRoomFormAction($slug, $index)
    {
            //Obtener repositorio de TempOwner
            $serviceFormTempOwner = $this->get('FormTempOwner');
            $tempownerrep = $this->getDoctrine()->getRepository('NavicuDomain:TempOwner');
            //busco el usuario
            $tempOwner = $tempownerrep->findOneBy(array('slug'=>$slug));
            //si el usuario existe
            if ($serviceFormTempOwner->haveAccess($tempOwner)) {
                $response = $serviceFormTempOwner->getRoomFormData($tempOwner,$index);
                return $this->render(
                    'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterRoom.html.twig',
                    array("rooms"=>json_encode($response), 'slugTemp' => $slug, 'basicQuota' => $tempOwner->getPropertyForm('basic_quota'))
                );
            } else {
                return new Response('Unauthorized',401);
            }
    }

    /**
     *   envia toda la información de una habitacion en el espacio temporal
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function selectTempRoomAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if($request->isXmlHttpRequest()){

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    $command = new SelectTempRoomCommand($data);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    $validator = $this->get('validator');
                    $errors = $validator->validate($command);
                    //si hay errores
                    if (count($errors) > 0) {
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] =  $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this->get('CommandBus')->execute($command);
                        //se envia la respuesta
                        return new JsonResponse($response->getArray(), $response->getStatusCode());
                    }
                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        }else{
            return new Response('Not Found',404);
        }
    }

    /**
     *   elimina una habitacion guardada en el espacio temporal
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function deleteTempRoomAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if($request->isXmlHttpRequest()){
            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    $command = new DeleteTempRoomCommand($data);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    $validator = $this->get('validator');
                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $errors = $validator->validate($command);
                    //si hay errores
                    if (count($errors) > 0) {
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] =  $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this->get('CommandBus')->execute($command);
                        $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                        $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                        $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);
                        $response = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);

                        //se envia la respuesta
                        return new JsonResponse($response->getArray(), $response->getStatusCode());
                    }
                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);

        }else{
            return new Response('Not Found',401);
        }
    }

    /**
     *   Finaliza el proceso de registro
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function advanceSectionAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    $command = new AdvanceSectionCommand($data);
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    $validator = $this->get('validator');
                    $errors = $validator->validate($command);
                    //si hay errores
                    if (count($errors) > 0) {
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] = $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this->get('CommandBus')->execute($command);
                        //se envia la respuesta
                        return new JsonResponse($response->getArray(), $response->getStatusCode());
                    }
                } else {
                    return new JsonResponse('Unauthorized', 401);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     *   registro de la informacion de pago del establecimiento en el espacio temporal
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function registerPaymentInfoAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if ($request->isXmlHttpRequest()) {
            //obtengo y decodifico el JSON 
            $data = json_decode($request->getContent(),true);
            if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                $data['form']['slug'] = $data['slug'];
                $command = new AddPaymentInfoCommand($data['form']);
                $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                $validator = $this->get('validator');
                $errors = $validator->validate($command);
                //si hay errores
                if (count($errors) > 0) {
                    $arrayErrors = array();
                    foreach ($errors as $error)
                        $arrayErrors[] = $error->getMessage();
                    $response = array(
                        'meta'=>array(
                            'code'=>400,
                            'message'=>'Bad request'
                        ),
                        'data'=> $arrayErrors
                    );
                    return new JsonResponse($response,400);
                } else {
                    //se ejecuta el comando
                    $response = $this->get('CommandBus')->execute($command);
                    //se envia la respuesta
                    return new JsonResponse($response->getArray(),$response->getStatusCode());
                }
            } else
                return new JsonResponse('Unauthorized', 401);

        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     *   accion para aceptar los terminos y condiciones
     *
     *   @author Gabriel Camacho <kbo025@gmail.com>
     *   @version 06/08/2015
     *   @param Request $request
     */
    public function acceptTermsConditionsAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if ($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //obtengo y decodifico el JSON
                $data = json_decode($request->getContent(), true);
                if ($serviceFormTempOwner->haveAccess($data['slug'])) {
                    $command = new AcceptTermsAndConditionsCommand($data);
                    $command->setClientIp($request->getClientIp());
                    $command->setIsAdmin($serviceFormTempOwner->isAdmin());
                    $validator = $this->get('validator');
                    $errors = $validator->validate($command);
                    //si hay errores
                    if (count($errors) > 0) {
                        $arrayErrors = array();
                        foreach ($errors as $error)
                            $arrayErrors[] = $error->getMessage();
                        $response = array(
                            'meta' => array(
                                'code' => 400,
                                'message' => 'Bad request'
                            ),
                            'data' => $arrayErrors
                        );
                        return new JsonResponse($response, 400);
                    } else {
                        //se ejecuta el comando
                        $response = $this->get('CommandBus')->execute($command);
                        //se envia la respuesta
                        return new JsonResponse($response->getArray(), $response->getStatusCode());
                    }
                } else {
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);
                }
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La función se encarga de recibir una imagen de la registro temporal
     * La petición se realiza mediante Ajax y POST
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 25/06/2015
     */
    public function loadImagesAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //Se realiza la petición ajax
        if($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {
                //Obtiene la información adicional de la imagen
                $data = json_decode($request->request->get('data'), true);

                //Obtiene la archivo/imagen enviada
                if ($request->files->get('file'))
                    $currentImage = new File($request->files->get('file'));

                $command = new RegisterTempImageCommand();

                //Se carga la imagen en el comando
                if (isset($currentImage) and $currentImage)
                    $command->setImage($currentImage);

                if (isset($data['name']))
                    $command->setName($data['name']);

                if (isset($data['slug']))
                    $command->setSlug($data['slug']);

                if (isset($data['idSubGallery']))
                    $command->setIdSubGallery($data['idSubGallery']);

                if (isset($data['gallery']))
                    $command->setGallery($data['gallery']);

                if (isset($data['subGallery']))
                    $command->setSubGallery($data['subGallery']);

                if (!$serviceFormTempOwner->haveAccess($data['slug']))
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

                //Ejecutando el caso de uso
                $response = $this->get('CommandBus')->execute($command);

                // Aplicando los flitros de las imagenes en las distintas resoluciones
                if ($response->getStatusCode() == 201) {

                    $imagemanagerResponse = $this->container->get('liip_imagine.controller');

                    $imagemanagerResponse->filterAction(
                        $this->getRequest(),
                        $command->getPathImage(), 'images_sm');

                    $imagemanagerResponse->filterAction(
                        $this->getRequest(),
                        $command->getPathImage(), 'images_xs');

                    $imagemanagerResponse->filterAction(
                        $this->getRequest(),
                        $command->getPathImage(), 'images_md');
                }

                // si se ejecuta el comando correctamente se aplican las validaciones de la seccion de galeria
                $serviceTempOwner = $this->get('RegisterTempOwner');
                $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);

                $serviceFormTempOwner = $this->get('FormTempOwner');
                $validations = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);

                return new JsonResponse($response->getArray(), $response->getStatusCode());
                }
            return new JsonResponse('Unauthorized', 401);

        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La función se encarga de eliminar una imagen del registro temporal
     * mediante una petición via Ajax
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 30/06/2015
     */
    public function deleteImageAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //Se realiza la petición ajax
        if($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {


                //Obtiene la información de la imagen a eliminar
                $data = json_decode($request->request->get('data'), true);

                //Se crea y asignan los valores a los propiedades de la clase
                $command = new DeleteTempImageCommand();

                if (isset($data['slug']))
                    $command->setSlug($data['slug']);
                if (isset($data['path']))
                    $command->setPath($data['path']);
                if (isset($data['gallery']))
                    $command->setGallery($data['gallery']);
                if (isset($data['subGallery']))
                    $command->setSubGallery($data['subGallery']);
                if (isset($data['idSubGallery']))
                    $command->setIdSubGallery($data['idSubGallery']);

                $validator = $this->container->get('validator');
                $validatedData = $validator->validate($command);

                if (count($validatedData) > 0) {
                    $respose = array();
                    $response['meta'] = array(
                        'code' => 400,
                        'message' => 'Bad Request');

                    $arrayErrors = array();

                    foreach ($validatedData as $error) {
                        $arrayErrors[$error->getPropertyPath()] = array(
                            'message' => $error->getMessage(),
                            'value' => $error->getInvalidValue(),
                        );
                    }

                    $response['data'] = $arrayErrors;

                    return new JsonResponse($response, 400);

                }

                //Valida el acceso
                if (!$serviceFormTempOwner->haveAccess($data['slug']))
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

                $response = $this->get('CommandBus')->execute($command);
                if ($response->getStatusCode() == 201) {
                    // si se ejecuta el comando correctamente se aplican las validaciones de la seccion de galeria
                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);

                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $validations = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);
                }
                return new JsonResponse($response->getArray(), $response->getStatusCode());
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La función editar los datos de una imagen del registro temporal
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 01/07/2015
     */
    public function editImageAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //Se realiza la petición ajax
        if($request->isXmlHttpRequest()) {
            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //Obtiene la información de la imagen a eliminar
                $data = json_decode($request->request->get('data'), true);

                //Se crea y asignan los valores a los propiedades de la clase
                $command = new EditTempImageCommand();

                if ($request->files->get('file'))
                    $command->setImage(new File($request->files->get('file')));
                if (isset($data['slug']))
                    $command->setSlug($data['slug']);
                if (isset($data['path']))
                    $command->setPath($data['path']);
                if (isset($data['gallery']))
                    $command->setGallery($data['gallery']);
                if (isset($data['subGallery']))
                    $command->setSubGallery($data['subGallery']);
                if (isset($data['name']))
                    $command->setName($data['name']);
                if (isset($data['idSubGallery']))
                    $command->setIdSubGallery($data['idSubGallery']);

                $validator = $this->container->get('validator');
                $validatedData = $validator->validate($command);

                if (count($validatedData) > 0) {
                    $respose = array();
                    $response['meta'] = array(
                        'code' => 400,
                        'message' => 'Bad Request');

                    $arrayErrors = array();

                    foreach ($validatedData as $error) {
                        $arrayErrors[$error->getPropertyPath()] = array(
                            'message' => $error->getMessage(),
                            'value' => $error->getInvalidValue(),
                        );
                    }

                    $response['data'] = $arrayErrors;

                    return new JsonResponse($response, 400);
                }

                //Valida el acceso
                if (!$serviceFormTempOwner->haveAccess($data['slug']))
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

                $response = $this->get('CommandBus')->execute($command);
                if ($response->getStatusCode() == 201) {
                    // si se ejecuta el comando correctamente se aplican las validaciones de la seccion de galeria
                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);

                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $validations = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);
                }
                return new JsonResponse($response->getArray(), $response->getStatusCode());
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La función registra los datos de las imagenes favoritas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 07/07/2015
     */
    public function registerFavoritesImagesAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //Se realiza la petición ajax
        if ($request->isXmlHttpRequest()) {
            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //Obtiene la información de la imagen a eliminar
                $data = json_decode($request->request->get('data'), true);

                //Se crea y asignan los valores a los propiedades de la clase
                $command = new RegisterTempFavoritesImagesCommand();

                if (isset($data['images']))
                    $command->setFavoritesImages($data['images']);

                if (isset($data['slug']))
                    $command->setSlug($data['slug']);

                $validator = $this->container->get('validator');
                $validatedData = $validator->validate($command);

                if (count($validatedData) > 0) {
                    $respose = $serviceFormTempOwner = $this->get('FormTempOwner');
                    array();
                    $response['meta'] = array(
                        'code' => 400,
                        'message' => 'Bad Request');

                    $arrayErrors = array();

                    foreach ($validatedData as $error) {
                        $arrayErrors[$error->getPropertyPath()] = array(
                            'message' => $error->getMessage(),
                            'value' => $error->getInvalidValue(),
                        );
                    }

                    $response['data'] = $arrayErrors;

                    return new JsonResponse($response, 400);
                }

                //Valida el acceso
                if (!$serviceFormTempOwner->haveAccess($data['slug']))
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

                $response = $this->get('CommandBus')->execute($command);
                if ($response->getStatusCode() == 201) {
                    // si se ejecuta el comando correctamente se aplican las validaciones de la seccion de galeria
                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);

                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $validations = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);
                }
                return new JsonResponse($response->getArray(), $response->getStatusCode());
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La función se encarga de revisar la solicitud vía ajax para eliminar
     * una imagen de la galería de imagenes de los favoritos
     *
     * @author Freddy Contreras <freddycontreras3.gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 08/07/2015
     */
    public function deleteFavoriteImageAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        if($request->isXmlHttpRequest()) {

            if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {

                //Obtiene la información de la imagen a eliminar
                $data = json_decode($request->request->get('data'), true);

                //Se crea y asignan los valores a los propiedades de la clase
                $command = new DeleteTempFavoriteImageCommand();

                if (isset($data['slug']))
                    $command->setSlug($data['slug']);

                if (isset($data['path']))
                    $command->setPath($data['path']);

                if (isset($data['subGallery']))
                    $command->setSubGallery($data['subGallery']);

                $validator = $this->container->get('validator');
                $validatedData = $validator->validate($command);

                if (count($validatedData) > 0) {
                    $respose = array();
                    $response['meta'] = array(
                        'code' => 400,
                        'message' => 'Bad Request');

                    $arrayErrors = array();

                    foreach ($validatedData as $error) {
                        $arrayErrors[$error->getPropertyPath()] = array(
                            'message' => $error->getMessage(),
                            'value' => $error->getInvalidValue(),
                        );
                    }

                    $response['data'] = $arrayErrors;
                    return new JsonResponse($response, 400);
                }

                //Valida el acceso
                if (!$serviceFormTempOwner->haveAccess($data['slug']))
                    return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

                $response = $this->get('CommandBus')->execute($command);
                if ($response->getStatusCode() == 201) {
                    // si se ejecuta el comando correctamente se aplican las validaciones de la seccion de galeria
                    $serviceTempOwner = $this->get('RegisterTempOwner');
                    $dataRooms = $serviceTempOwner->getDataSection($data['slug'], 3, $lastSec);
                    $dataServices = $serviceTempOwner->getDataSection($data['slug'], 2, $lastSec);
                    $dataImages = $serviceTempOwner->getDataSection($data['slug'], 4, $lastSec);

                    $serviceFormTempOwner = $this->get('FormTempOwner');
                    $validations = $serviceFormTempOwner->validateImages($data['slug'], $dataRooms, $dataServices, $dataImages);
                }
                return new JsonResponse($response->getArray(), $response->getStatusCode());
            } else
                return new JsonResponse('Unauthorized', 401);
        } else {
            return new Response('Not Found',404);
        }
    }

    /**
     * La siguiente función se encarga de asignar el orden de las imagenes
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Request $request
     * @return JsonResponse
     * @version 21/10/2015
     */
    public function sortImagesAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');

        if($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $auxData = array();
            $auxData['galleriesSections'] = $data['galleriesSections'];
            $auxData['favoritesSection'] = $data['favoritesSection'];

            $command = new SortImagesCommand(
                $data['slug'],
                $auxData
            );

            //Validel acceso
            if (!$serviceFormTempOwner->haveAccess($data['slug']))
                return new JsonResponse(NULL, Response::HTTP_FORBIDDEN);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse('Ok', 201);
             else
                return new JsonResponse('Unauthorized', $response->getStatus());

        } else
            return new JsonResponse('Not Found',404);

    }

    public function welcomeViewAction()
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        $slugTemp  = $this->get('security.context')->getToken()->getUser()->getUserName();
        if($serviceFormTempOwner->haveAccess($slugTemp)) {
            return $this->render(
                'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterWelcome.html.twig',
                array('slugTemp' => $slugTemp)
            );
        } else {
            return new Response('Unauthorized',401);
            //return $this->render('TwigBundle:Exception:error401.html.twig');
        }
    }

    public function endAction()
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        $slugTemp  = $this->get('security.context')->getToken()->getUser()->getUserName();
        if($serviceFormTempOwner->haveAccess($slugTemp)) {
            return $this->render(
                'NavicuInfrastructureBundle:Ascribere/PropertyForm:formRegisterEnd.html.twig',
                array('slugTemp' => $slugTemp)
            );
        } else {
            return new Response('Unauthorized',401);
            //return $this->render('TwigBundle:Exception:error401.html.twig');
        }
    }

    /**
     * La siguiente función es utilizada cuando se quiere mostrar 
     * la vista de "Actualiza contraseña correctamente"
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param void
     * @version 28/07/2015
     */
    public function passwordUpdateSuccessAction()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Ascribere/PropertyForm:updatePasswordSuccess.html.twig'
        );
    }

    /**
     * Consulta un location y devuelve un JSON con sus hijos en formato {"id":00, "name" : "xxx"}
     *
     * @author Gabriel Camacho <freddycontreras3@gmail.com>
     * @param void
     * @version 19-08-2015
     */
    public function getLocationAction(Request $request)
    {
        $serviceFormTempOwner = $this->get('FormTempOwner');
        //si la solicitud es ajax
        if($request->isXmlHttpRequest()){
            //obtengo y decodifico el JSON
            //if ($this->get('SessionService')->isLoggedIn('ROLE_TEMPOWNER')) {
                $data = json_decode($request->getContent(), true);
                //if ($serviceFormTempOwner->haveAccess($data['slug'])) {

                    $rep = $this->getDoctrine()->getRepository('NavicuDomain:Location');
                    $locations = $rep->getAll(isset($data['location']) ? $data['location'] : null);
                    $response = array(
                        'meta' => array(
                            'code' => 200,
                            'message' => 'OK'
                        ),
                        'data' => $locations
                    );
                    return new JsonResponse($response);
                //} else
                    //return new JsonResponse('Unauthorized', 401);
            //} else
                //return new JsonResponse('Unauthorized', 401);
        } else
            return new Response('Not Found',404);
    }

    /**
     * @param $slug
     * @return Response
     */
    /*public function termsAndConditionsPdfAction($slug)
    {
        $tempownerrep = $this->getDoctrine()->getRepository('NavicuDomain:TempOwner');
        $tempOwner = $tempownerrep->findOneBy( array('slug'=>$slug) );
        $serviceFormTempOwner = $this->get('FormTempOwner');

        //validando el acceso a las funciones del formulario
        if (($tempOwner==null) || !$serviceFormTempOwner->haveAccess($tempOwner))
            return new Response('Unauthorized',401);

        $data = $serviceFormTempOwner->getTermAndConditionPdfData($tempOwner);
        $pdf = $this->get('pdfCreator')->generateTermsAndConditions($data,false);

        return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));
    }*/

    /**
     * @param $slug
     * @return Response
     */
    public function termsAndConditionsPdfAction($slug)
    {
        /*set_time_limit(0);
        $url['fonts'] =  $_SERVER['DOCUMENT_ROOT'].'/fonts/font-navicu/font-lato/';
        $pdf = $this->get('pdfCreator')->generatePdfFromHtml(
            $this->render(
                    'NavicuInfrastructureBundle:Ascribere:PropertyForm/TermsAndConditionsPdf.html.twig',
                    $url
                )
                ->getContent()
        );
        return new Response($pdf, 200, array('Content-Type' => 'application/pdf'));*/
        $filename = 'termsAndConditionsAscribere.pdf';
        $path = $this->get('kernel')->getRootDir(). "/../web/files/";
        $content = file_get_contents($path.$filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

        $response->setContent($content);
        return $response;
    }



    /**
     * Esta función es usada para redireccionar a la vista de tips
     * 
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @author Currently Working: Helen Mercatudo
     *
     */
    public function tipsViewAction()
    {
        return $this->render(
            'NavicuInfrastructureBundle:Extranet\Tips:tips.html.twig'
        );
    }

    /**
     * Esta función es usada para redireccionar a la vista de reservas
     * 
     * @author Helen Mercatudo <hmercatudo@navicu.com>
     * @author Currently Working: Helen Mercatudo
     *
     */
    public function reservationsAction($slug,Request $request)
    {
        $rpReservation = $this->getDoctrine()->getRepository('NavicuDomain:Reservation');

        $data = $request->isXmlHttpRequest() ?
            json_decode($request->getContent(), true) :
            ['slug'=>$slug];

        $pagination = $request->isXmlHttpRequest() ?
            $data['page'] :
            1;

        $command = new GetReservationsForPropertyCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        $dataResponse = $response->getData();

        $dataPag = $this->get('Pagination')->pagination(
            $dataResponse, $pagination
        );

        return $request->isXmlHttpRequest() ?
            new JsonResponse(
                [
                    'reservations' => $dataResponse,
                    'page' => $dataPag,
                ],
                $response->getStatusCode()
            ) :
            $this->render(
                'NavicuInfrastructureBundle:Extranet\Reservations:reservations.html.twig',
                [
                    'data' => json_encode([
                        'reservations' => $dataResponse,
                        'states' => $rpReservation->getStatesList(),
                        'page' => $dataPag,
                        'hasReservation' => count($dataResponse)==0 ? 0 : 1,
                        'slug' => $slug
                    ])
                ]
            );
    }

	/**
     * Esta función es usada para devolver la información necesario
     * para el manejo de detalles de la reserva.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 *
     * @param Request $request
     */
    public function detailsReservationsAction(Request $request)
    {
        
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);
            $data["userSession"] = $this->get("SessionService")->getUserSession();
            $command = new GetReservationDetailsCommand($data);
            $command->setOwner(true);
            $response = $this->get('CommandBus')->execute($command);
            return new JsonResponse($response->getData(),$response->getStatusCode());

        } else {
            return new JsonResponse('Bad Request', 400);
        }
    }
}
