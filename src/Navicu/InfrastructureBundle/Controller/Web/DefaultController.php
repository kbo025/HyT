<?php

namespace Navicu\InfrastructureBundle\Controller\Web;

use Navicu\Core\Application\UseCases\Search\SearchCountryTypeLocation\SearchCountryTypeLocationCommand;
use Navicu\Core\Application\UseCases\Web\GetAirportCoordinates\GetAirportCoordinatesCommand;
use Navicu\Core\Application\UseCases\Web\GetFeaturedProperties\GetFeaturedPropertiesCommand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\UseCases\Prueba\PruebaCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Navicu\Core\Application\UseCases\RegisterTempProperty\RegisterTempPropertyCommand;
use Navicu\Core\Application\UseCases\AutoCompleteDestinationSearch\AutoCompleteDestinationSearchCommand;
use Navicu\Core\Application\UseCases\SuggestionSearch\SuggestionSearchCommand;
use Navicu\Core\Application\UseCases\MoreSuggestionSearch\MoreSuggestionSearchCommand;
use Navicu\Core\Application\UseCases\ResultSearch\ResultSearchCommand;
use Navicu\Core\Application\UseCases\PropertySearchDetails\PropertySearchDetailsCommand;
use Navicu\Core\Application\UseCases\RoomSearchOfProperty\RoomSearchOfPropertyCommand;
use Navicu\Core\Application\UseCases\GetLocationHome\GetLocationHomeCommand;
use Navicu\Core\Application\UseCases\Search\SearchCountry\SearchCountryCommand;
use Navicu\Core\Application\UseCases\Search\SearchCountryLocation\SearchCountryLocationCommand;
use Navicu\Core\Application\UseCases\Reservation\getInfoByRedSocial\getInfoByRedSocialCommand;
use Navicu\Core\Application\UseCases\Reservation\getValidEmail\getValidEmailCommand;
use Navicu\Core\Application\UseCases\EditClientProfile\EditClientProfileCommand;
use Navicu\Core\Application\UseCases\GetClientProfile\GetClientProfileCommand;
use Navicu\Core\Application\UseCases\ClientInfoSession\ClientInfoSessionCommand;
use Navicu\Core\Application\UseCases\Web\getLocationRegister\getLocationRegisterCommand;
use Navicu\Core\Application\UseCases\Web\GetLocationMap\GetLocationMapCommand;
use Navicu\Core\Application\UseCases\Web\getDataSiteMap\getDataSiteMapCommand;
use Navicu\Core\Application\UseCases\Web\ForeignExchange\SetAlphaCurrency\SetAlphaCurrencyCommand;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$command = new GetLocationHomeCommand();
		$response = $this->get('CommandBus')->execute($command);
        return $this->render('NavicuInfrastructureBundle:Web:index-business.html.twig',
			array('locations' => json_encode($response->getData())));

    }

	/**
	 * La siguiente funcionalidad es temporal del home de la página
	 * retorna las localidades de los establecimiento
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @return Response
	 * @version 25/11/2015
	 *
	 */
    public function indexTemporalAction()
    {
	    $command = new GetAirportCoordinatesCommand();
	    $airports = $this->get('CommandBus')->execute($command);
		$command = new GetLocationHomeCommand();
		$response = $this->get('CommandBus')->execute($command);
		$command = new GetLocationMapCommand();
		$destiniesMap = $this->get('CommandBus')->execute($command);
	    $command = new GetFeaturedPropertiesCommand();
	    $command->set('router', $this->get('router'));
	    $featured = $this->get('CommandBus')->execute($command);

		return $this->render('NavicuInfrastructureBundle:Web:index-destiny.html.twig',
			array(
				'locations' => json_encode($response->getData()),
				'destiniesMap' => json_encode($destiniesMap->getData()),
				'featured' => json_encode($featured->getData()),
				'airports' => json_encode($airports->getData())
			));
    }

    public function resumeAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:resume.html.twig');
    }

    public function destinationsAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:destinations.html.twig');
    }

    public function faqAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:faq.html.twig');
    }

    public function about_usAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:about_us.html.twig');
    }

    public function termsAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:termsBooking.html.twig');
    }

    public function termsPreBookingAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:termsPreBooking.html.twig');
    }

    public function tipsAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:tips.html.twig');
    }

    public function concursoAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:concurso-Madres.html.twig');
    }

    /**
    * Esta función retorna a la vista del framework navicu-sass-official
    *
    * @author Helen Mercatudo <hmercatudo@navicu.com>
    * @param Void
    * @version Updated: 17-06-16
    */
    public function navicu_sass_officialAction()
    {
        return $this->render('NavicuInfrastructureBundle:Web:navicu_sass_official.html.twig');
    }


    /**
    * Esta función retorna a la vista homepage, el conjunto de datos a mostrar en la vista
    *
    * @author Freddy Contreras <freddy.contreras3@gmail.com>
    * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
    * @param Void
    * @version Updated: 11-05-15
    */
    public function homeAction()
    {
        return new Response("asdfa");

    }

	/**
     * Esta función retorna un listado de posibles conincidencia entre
     * lugares y hoteles para el autocompletado.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @version Updated: 29-06-15
     */
	public function autoCompleteSearchEnergyAction(Request $request)
    {
		if($request->isXmlHttpRequest()){
			$word = json_decode($request->getContent(),true);
			$command = new AutoCompleteDestinationSearchCommand($word['word']);
            $response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response, 201);
		}
    }

	/**
     * Esta función retorna un listado de posibles conincidencia entre
     * lugares y hoteles para la sugerencia del motor de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @version Updated: 29-06-15
     */
	public function suggestionSearchEnergyAction(Request $request)
    {

		$data = array(
			"word"=>$request->query->get("word")
		);

		if($data["word"]){
			$command = new suggestionSearchCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response) {
				$data["startDate"] = $request->query->get("startDate");
				$data["endDate"] = $request->query->get("endDate");
				$data["adult"] = $request->query->get("adult");
				$data["kid"] = $request->query->get("kid");
				$data["room"] = $request->query->get("room");

				return $this->render(
					'NavicuInfrastructureBundle:Web:suggestion.html.twig',
					array(
						'destinies'=>json_encode($response),
						'data'=>json_encode($data)
					)
				);
			} else {
				return $data;
			}
		}
    }

	/**
     * Esta función retorna un listado de posibles conincidencias entre
     * lugares o hoteles para mas sugerencias del motor de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @version Updated: 29-06-15
     */
	public function apiMoreSuggestionSearchEnergyAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new moreSuggestionSearchCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response) {
				array_pop($response);
				//$dataPag = $this->get('Pagination')->pagination($response, $data["page"]);
				//$response = array("data" =>$response, "page"=>$dataPag);
				return new JsonResponse($response);
			} else {
				return new JsonResponse(null);
			}
		} else {
            return new Response('Not Found',404);
        }
	}

	/**
     * Esta función retorna un listado de resultado del motor de busqueda
     * con los hoteles + destinos que el usuario a escogido.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     */
    public function listSearchAction(Request $request)
    {
		$data = array(
			"idType"=>$request->query->get("idType"),
			"type"=>$request->query->get("type"),
			"startDate"=>$request->query->get("startDate"),
			"endDate"=>$request->query->get("endDate"),
			"adult"=>$request->query->get("adult"),
			"kid"=>$request->query->get("kid"),
			"room"=>$request->query->get("room"),
			"destiny"=>$request->query->get("destiny"),
			"page"=>1
		);

		$command = new resultSearchCommand($data);
		$response = $this->get('CommandBus')->execute($command);

        if ($response) {
                $response["page"] = $this->get('Pagination')->pagination($response["properties"], $data["page"]);
        } else {
        	$response = array();
        }

        $command = new GetLocationHomeCommand();
        $location = $this->get('CommandBus')->execute($command);

        $response["search"] = $data;
        $response["location"] = $location->getData();
        return $this->render('NavicuInfrastructureBundle:Web:listSearch.html.twig',
			array(
				'data'=>json_encode($response)
			)
		);
    }

	/**
     * Esta función retorna un listado de resultado del motor de busqueda
     * con los hoteles + destinos que el usuario a escogido. Petición Api.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     */
	public function apiListSearchAction(Request $request)
    {
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new resultSearchCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response) {
				$response["page"] = $this->get('Pagination')->pagination($response["properties"], $data["page"]);
				$response["search"] = $data;
				return new JsonResponse($response);
			} else {
				return new JsonResponse(null);
			}

		} else {
            return new Response('Not Found',404);
        }
	}

	/**
     * Esta función retorna la información de un establecimiento
     * para mostrarla en la ficha del establecimiento en motor
     * de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     */
    public function propertySearchDetailsAction($slug, Request $request)
    {
		$countryCode = "VE";
		$data = array(
			"slug" => $slug,
			"startDate" => $request->query->get("startDate"),
			"endDate" => $request->query->get("endDate"),
			"page" => 1
		);

		$data["adult"] = is_null($request->query->get('adult')) ? 2 : $request->query->get("adult");
		$data["room"] = is_null($request->query->get('room')) ? 1 : $request->query->get("room");
		$data["kid"] = is_null($request->request->get('kid')) ? 0 : $request->query->get("kid");
		$data["countryCode"] = $countryCode;

		// Si existe un error en el inventario se debe mostrar un error
		$errorInventory = $request->attributes->get('error') ? true : false;

		$command = new PropertySearchDetailsCommand($data);
		$response = $this->get('CommandBus')->execute($command);


		if ($response->getStatusCode() == 200) {
			$dataResponse = $response->getData();

			$dataResponse["search"] = $data;
			return $this->render('NavicuInfrastructureBundle:Web:property.html.twig',
				array(
					'data' => json_encode($dataResponse),
					'errorInventory' => $errorInventory
				)
			);
		}

		return $this->render('TwigBundle:Exception:error404.html.twig');
    }

	/**
     * Esta función retorna una serie de habitaciones con sus servicios
     * para un establecimiento por medio del motor de busqueda.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
     * @param Request $request
     * @version Updated: 29-06-15
     */
	public function apiRoomsSearchOfPropertyAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new roomSearchOfPropertyCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			if ($response) {
				return new JsonResponse($response);
			} else {
				return new JsonResponse("null");
			}
		} else {
            return new Response('Not Found',404);
        }
	}

	/**
     * Esta función es usada para cambiar el manejo del idioma del sistema
     * por medio de una petición.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 *
     * @param Request $request
     * @param String $language
     * @version Updated: 29-06-15
     */
	public function apiLanguageAction(Request $request, $language)
    {
        if ($request->isXmlHttpRequest()) {

            $data["userSession"] = $this->get("SessionService")->setLanguage($language, $request);

			return new JsonResponse(true);
		} else {
            return new Response('Not Found',404);
        }
	}

	/**
	 * La siguiente función se encarga de mostrar los estados de un país
	 * donde se encuentran establecimientos afiliados
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $countryCode
	 * @version 19/01/2016
	 */
	public function searchCountryAction($countryCode, Request $request)
	{
		$command = new SearchCountryCommand($countryCode);

		$response = $this->get('CommandBus')->execute($command);
		$data["startDate"] = $request->query->get("startDate");
		$data["endDate"] = $request->query->get("endDate");
		$data["adult"] = $request->query->get("adult");
		$data["kid"] = $request->query->get("kid");
		$data["room"] = $request->query->get("room");
		$data["word"] = $request->query->get("word");;

		if ($response->getStatusCode() == 200)
			return $this->render(
				'NavicuInfrastructureBundle:Web:suggestionSearchEnergy.html.twig',
				array(
					'destinies'=>json_encode($response->getData()),
					'data' => json_encode($data)
				)
			);
		else
			return $this->render('TwigBundle:Exception:error404.html.twig');

	}

	/**
	 * La siguiente función retorna los resultados de sugerencia dado el pais y una localidad a buscar
	 * si el listado es unico retonar redirige a la ruta 'navicu_search_country_type_slug' sino busca
	 * el listado de sugerencia
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $countryCode
	 * @param $location
	 * @param Request $request
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @version 28/01/2016
	 */
	public function searchCountryLocationAction($countryCode, $location, Request $request)
	{
		$command = new SearchCountryLocationCommand($countryCode, $location);

		$response = $this->get('CommandBus')->execute($command);
		$data["startDate"] = $request->query->get("startDate");
		$data["endDate"] = $request->query->get("endDate");
		$data["adult"] = $request->query->get("adult");
		$data["kid"] = $request->query->get("kid");
		$data["room"] = $request->query->get("room");
		$data['word'] = $request->query->get("word");

		if ($response->getStatusCode() == 200) {
			$data = $response->getData();
			if (isset($data['suggestion'])) {
				return $this->render(
					'NavicuInfrastructureBundle:Web:suggestionSearchEnergy.html.twig',
					array(
						'destinies' => json_encode($response->getData()),
						'data' => json_encode($data)
					)
				);
			} else if (isset($data['redirect'])) {

				return $this->redirectToRoute(
					'navicu_search_country_type_slug',
					[
						'countryCode' => $countryCode,
						'type' => $data['redirect'],
						'slug' => $command->getLocation()
					],
					301
				);
			}

			return new JsonResponse($response->getData());
		}

		else
			return $this->render('TwigBundle:Exception:error404.html.twig');
	}

	/**
	 * La siguiente función se encarga de retornar los establecimientos
	 * por un codigo de pais, un tipo de busqueda (estado/parroquia/etc ó hotel/hostal/etc)
	 * y un slug de la localidad a buscar
	 *
	 * @author Freddy Contreras <freddycontreras3@gmail.com>
	 * @param $countryCode
	 * @param $type
	 * @param $slug
	 * @param Request $request
	 * @return Response
	 */
	public function searchByCountryTypeSlugAction($countryCode,$type, $slug, Request $request)
    {
        $data = array(
            "countryCode" => $countryCode,
            "type" => $type,
            "slug" => $slug,
            "startDate" => $request->query->get("startDate"),
            "endDate" => $request->query->get("endDate"),
            "destiny" => $slug,
            "page" => 1
        );

        $data["adult"] = is_null($request->query->get('adult')) ? 2 : $request->query->get("adult");
        $data["room"] = is_null($request->query->get('room')) ? 1 : $request->query->get("room");
        $data["kid"] = is_null($request->request->get('kid')) ? 0 : $request->query->get("kid");

        $command = new SearchCountryTypeLocationCommand($data);
        $response = $this->get('CommandBus')->execute($command);
        if ($this->get('translator')->trans($type, [], 'location') != $type) {

            $response = $response->getData();
            $response["page"] = $this->get('Pagination')->pagination($response["page"], $data["page"]);
            $response["search"] = $data;

            $command = new GetLocationHomeCommand();
            $location = $this->get('CommandBus')->execute($command);
            $response["location"] = $location->getData();

            return $this->render('NavicuInfrastructureBundle:Web:listSearch.html.twig',
                array(
                    'data' => json_encode($response)
                )
            );

        }

        return $this->render('TwigBundle:Exception:error404.html.twig');
    }

    /**
     * Esta función es usada para devolver la información de un usuario
     * Cliente por medio de su ID de RedSocial.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P.
     *
     * @param Request $request
     */
    public function apiInfoRedSocialAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(),true);

            $command = new getInfoByRedSocialCommand($data);
            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 200) {

                // Manejo de login del usuario con su correo.
                $user = $this->get('SecurityService')->loginDirect(
                    array("userName" => $data["email"]),
                    "navicu_web"
                );

                $user = $this->get('Security.context')->getToken()->getUser();
                $includeSession = new ClientInfoSessionCommand(["user"=>$user]);
                $this->get('CommandBus')->execute($includeSession);
            }
            return new JsonResponse($response->getData(), $response->getStatusCode());
        } else {
            return new Response('Not Found',404);
        }
    }

	/**
     * Esta función es usada para validar si existe un correo electronico
     * en el registro de un usuario cliente registrado.
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 *
     * @param Request $request
     */
	public function apiValidEmailAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);

			$command = new getValidEmailCommand($data);
			$response = $this->get('CommandBus')->execute($command);

			return new JsonResponse($response->getData());
		} else {
            return new Response('Not Found',404);
        }
	}

	/**
	 * Esta funcion editara los valores solicitados por el cliente.
	 *
	 * @author Isabel Nieto <isabelcnd@gmail.com>
	 * @param Request $request
	 * @return JsonResponse|Response
	 */
	public function editClientProfileAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$FosUserId = $this->get('security.context')->getToken()->getUser();
			$data = json_decode($request->getContent(),true);

			$command = new EditClientProfileCommand($FosUserId->getClientProfile(), $data);

			$response = $this->get('CommandBus')->execute($command);
			return new JsonResponse($response->getData(), $response->getStatusCode());

		} else {
			return new JsonResponse('Bad Request',400);
		}
	}

	/**
	 * La siguiente funcionalidad es la vista de registro de cliente
	 *
	 * @author Helen Mercatudo <hmercatudo@navicu.com>
	 * @return Response
	 * @version 8/04/2016
	 *
	 */
	public function registerAction(Request $request)
	{
		if ($user = $this->get('SessionService')->getUserSession())
			return $this->redirect($this->get('router')->generate("navicu_client_home"));

		$command = new getLocationRegisterCommand(["code"=>"VE"]);
		$response = $this->get('CommandBus')->execute($command);
		return $this->render('NavicuInfrastructureBundle:Web:register.html.twig',
			[
				"referer"=>$request->headers->get('referer'),
				"response"=>json_encode($response->getData())
			]);
	}

	/**
	 * La siguiente funcionalidad es para la creación de siteMap.xml
	 *
	 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
	 * @author Currently Working: Joel D. Requena P.
	 * @return Xml
	 */
	public function sitemapAction(Request $request)
	{
		$command = new GetLocationHomeCommand();
		$data["locations"] = $this->get('CommandBus')->execute($command)->getData();
		$data["routing"] = $this->get('router');

		$command = new getDataSiteMapCommand($data);
		$response = $this->get('CommandBus')->execute($command);

		return $this->render('NavicuInfrastructureBundle::site-map.xml.twig',
			[
				'urls'     => $response->getData(),
				'hostname' => "https://www.navicu.com"
			]);
	}

	/**
	* La siguiente funcinalidad renderiza una vista necesaria para mostrar el detalle de los markers
	* del mapa
	*
	* @author Isabel Nieto <isabelcnd@gmail.com>
	* @version 07/07/2016
	* @return view
	*/
	public function markerDetailAction(Request $request)
	{
		return $this->render('NavicuInfrastructureBundle::Web/Partials/markerViewTemplate.html.twig');
	}

	/**
	 * Funcion encargada de buscar una moneda que coincida
	 * con las que se encuentran activas en la base de datos, e incluirla en la session
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @author Isabel Nieto <isabelcnd@gmail.com>
	 * @version 18/06/2016
	 */
	public function SetAlphaCurrencyAction(Request $request)
	{
		if ($request->isXmlHttpRequest()) {
			$data = json_decode($request->getContent(),true);
			$command = new SetAlphaCurrencyCommand($data['data']);
			$response = $this->get('CommandBus')->execute($command);
			return new JsonResponse($response->getData(), $response->getStatusCode());
		} else {
			return new JsonResponse('Bad Request',400);
		}
	}

    /**
    * Funcion temporal para renderizar la vista de mantenimiento
    */
    public function maintenanceViewAction(Request $request) {

        $protocol = "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ? "HTTP/1.1" : "HTTP/1.0";

        $responseView = $this->render('NavicuInfrastructureBundle::Web/maintenanceView.html.twig');
        $responseView->headers->set("Retry-After", "3600");
        $responseView->headers->set($protocol."503 Service Unavailable", true, 503);
        $responseView->headers->set("status", "503");
        return $responseView;
    }

    public function promotionsAction(Request $request)
    {
        return $this->render('NavicuInfrastructureBundle:Web:promotions.html.twig',
        	array('name' => $request));
    }
}
