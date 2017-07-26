<?php

namespace Navicu\InfrastructureBundle\Controller\Admin\Property;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;

use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\GetDataGalleries\GetDataGalleriesCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SetFavoriteImage\SetFavoriteImageCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\UploadImage\UploadImageCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteImage\DeleteImageCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\DeleteFavorite\DeleteFavoriteCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\EditNameImage\EditNameImageCommand;
use Navicu\Core\Application\UseCases\Admin\PropertyCRUD\SortGalleries\SortGalleriesCommand;

class GalleriesController extends Controller
{
    /**
     * La siguiente función se encarga de obtener los datos
     * de la galería de imagen de un establecimiento
     *
     * @author Freddy Contreras
     * @param $slug
     * @version 18/11/2015
     */
    public function getDataGalleriesAction($slug)
    {
        $command = new GetDataGalleriesCommand($slug);
        $response = $this->get('CommandBus')->execute($command);

        return $this->render(
                    'NavicuInfrastructureBundle:Ascribere:affiliateAdmin/editGallery.html.twig',
                        array(
                            'slugTemp' => $slug,
                            'galleries' => $response->getData()));
    }

    /**
     * La siguiente función se encarga de subir una imagen del
     * establecimiento en el sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com >
     * @param string $slug
     * @param Request $request
     * @return Response
     * @version 20/11/2015
     *
     */
    public function uploadImageAction($slug, Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $command = new UploadImageCommand($slug);

            $command->setSlug($slug);

            if ($request->files->get('file'))
                $currentImage = new File($request->files->get('file'));

            $data = json_decode($request->request->get('data'), true);

            //Se carga la imagen en el comando
            if (isset($currentImage) and $currentImage)
                $command->setFile($currentImage);
            if (isset($data['idGallery']))
                $command->setIdGallery($data['idGallery']);
            if (isset($data['typeGallery']))
                $command->setTypeGallery($data['typeGallery']);
            if (isset($data['orderGallery']))
                $command->setOrderGallery($data['orderGallery']);

            $response = $this->get('CommandBus')->execute($command);

            return new JsonResponse($response->getArray(), $response->getStatusCode());
        }

        return new JsonResponse('Bad Request', 400);
    }

    /**
     * Asigna a un establecimiento una imagen a favoritos
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param Request $request
     * @return JsonResponse
     * @version 24/11/2015
     */
    public function addFavoriteAction($slug, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new SetFavoriteImageCommand();

            $command->setSlug($slug);

            if (isset($data['idGallery']))
                $command->setIdGallery($data['idGallery']);

            if (isset($data['typeGallery']))
                $command->setTypeGallery($data['typeGallery']);

            if (isset($data['idImage']))
                $command->setIdImage($data['idImage']);

            if (isset($data['orderGallery']))
                $command->setOrderGallery($data['orderGallery']);


            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse($response->getData(),201);
            else
                return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return New JsonResponse('Bad Request', 400);
    }

    /**
     * La siguiente función se encarga de eliminar una imagen
     * del establecimiento
     *
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param Request $request
     * @return JsonResponse
     * @version 12/01/2016
     */
    public function deleteImageAction($slug, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new DeleteImageCommand();

            $command->setSlug($slug);

            if (isset($data['idGallery']))
                $command->setIdGallery($data['idGallery']);

            if (isset($data['typeGallery']))
                $command->setTypeGallery($data['typeGallery']);

            if (isset($data['idImage']))
                $command->setIdImage($data['idImage']);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse('Ok',201);
            else
                return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return New JsonResponse('Bad Request', 400);
    }

    /**
     * La siguiente función se encarga de recibir la solicitud
     * para eliminar una imagen favorita de una establecimiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param Request $request
     * @return JsonResponse
     * @version 12/01/2016
     */
    public function deleteFavoriteAction($slug, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new DeleteFavoriteCommand();

            $command->setSlug($slug);

            if (isset($data['idFavorite']))
                $command->setIdFavorite($data['idFavorite']);

            if (isset($data['deleteType']))
                $command->setDeleteType($data['deleteType']);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse('Ok',201);
            else
                return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return New JsonResponse('Bad Request', 400);
    }

    /**
     * La función edita el nombre de la imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param Request $request
     * @return JsonResponse
     * @version 12/01/2016
     */
    public function editNameAction($slug, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new EditNameImageCommand();

            if (isset($data['idImage']))
                $command->setIdImage($data['idImage']);

            if (isset($data['name']))
                $command->setName($data['name']);

            $command->setSlug($slug);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse('Ok',201);
            else
                return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return New JsonResponse('Bad Request', 400);
    }

    /**
     * Función recibe los datos del ordamiento de la galería de imagenes
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $slug
     * @param Request $request
     * @return JsonResponse
     * @version 15/01/2015
     */
    public function sortImagesAction($slug, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $data = json_decode($request->getContent(), true);

            $command = new SortGalleriesCommand();

            $command->setSlug($slug);

            if (isset($data['favorites']))
                $command->setFavoritesData($data['favorites']);

            if (isset($data['galleries']))
                $command->setGalleriesData($data['galleries']);

            $response = $this->get('CommandBus')->execute($command);

            if ($response->getStatusCode() == 201)
                return new JsonResponse('Ok',201);
            else
                return new JsonResponse($response->getData(),$response->getStatusCode());
        }

        return New JsonResponse('Bad Request', 400);
    }
}
