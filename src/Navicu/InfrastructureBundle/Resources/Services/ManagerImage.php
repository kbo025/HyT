<?php

namespace Navicu\InfrastructureBundle\Resources\Services;

use Navicu\Core\Application\Contract\ManagerImageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 *	La siguiente clase implementa las funcionalidades
 *  respecto al manejo de imagenes
 *
 *	@author Freddy Contreras <freddycontreras3@gmail.com>
 *	@author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 *	@version 20/11/2015
 */
class ManagerImage implements ManagerImageInterface
{
    /**
     * @var Contenedor de servicios de symfony
     */
    private $container;

    /**
     * @var array Arreglo que contiene un arreglo con las rutas de las carpetas de las imagenes
     */
    private $folderFilter;

    /**
     * Constructor de la clase
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->folderFilter = [
            'images_original' => $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_original/',
            'images_md' => $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_md/',
            'images_sm' => $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_sm/',
            'images_xs' => $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_xs/',
            'images_email' => $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_email/'
        ];
    }

    /**
     * La siguiente función transforma una imagen dado un path
     * en las dimensiones xs, sm y md
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param string $pathImage
     * @version 23/11/2015
     */
    public function generateImages($pathImage)
    {
        $imagemanagerResponse = $this->container->get('liip_imagine.controller');

        $imagemanagerResponse->filterAction(
            new Request(),
            $pathImage, 'images_sm');

        $imagemanagerResponse->filterAction(
            new Request(),
            $pathImage, 'images_xs');

        $imagemanagerResponse->filterAction(
            new Request(),
            $pathImage, 'images_md');
    }

    /**
     * La siguiente función se encarga de eliminar una imagen
     * en las distintas carpetas (original, md, sm, xs) dado el path de la imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 09/12/2015
     * @param $pathImage
     * @return mixed
     */
    public function deleteImages($pathImage)
    {
        foreach($this->folderFilter as $value) {

            $file = $value.$pathImage;
            if (file_exists($file))
                unlink($file);
        }
    }

    /**
     * Crear una imagen mediante un filtro en especifico
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $pathImage
     * @param $filter
     * @version 13/01/2016
     */
    public function generateFilter($pathImage, $filter)
    {
        $imagemanagerResponse = $this->container->get('liip_imagine.controller');

        $imagemanagerResponse->filterAction(
            new Request(),
            $pathImage, $filter);
    }

    /**
     * Elimina una imagen mediante un filtro en especifico
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $pathImage
     * @param $filter
     * @return boolean
     * @version 13/01/2015
     */
    public function deleteFilter($pathImage, $filter)
    {
        $response = false;
        $file = $this->folderFilter[$filter].$pathImage;

        if (file_exists($file)) {
            unlink($file);
            $response = true;
        }

        return $response;
    }

    /**
     * La siguiente función sustituye la ubicación de una imagen
     * en las distintas dimensiones del sistema
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $oldPath
     * @param $newPath
     */
    public function changePath($oldPath, $newPath)
    {
        foreach ($this->folderFilter as $currentFilter) {
            $file = $currentFilter.$oldPath;
            if (file_exists($file))
                rename($file,$currentFilter.$newPath);
        }
    }
}