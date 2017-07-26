<?php
namespace Navicu\Core\Application\UseCases\Ascribere\Images\SortImages;

use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;

class SortImagesHandler implements Handler
{
    private $rf;

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle( Command $command, RepositoryFactoryInterface $rf )
    {
        $this->rf = $rf;

        $rpTempOwner = $this->rf->get('TempOwner');
        $tempOwner = $rpTempOwner->findOneBy(
            array('slug' => $command->get('slug'))
        );

        if ($tempOwner) {

            $data = $command->getRequest()['data'];
            $sortData = $this->getSortData($data);

            $tempOwner->setGalleryForm($sortData);
            $rpTempOwner->save($tempOwner);
            return new ResponseCommandBus(201, 'Ok');

        } else
            return new ResponseCommandBus(403, 'forbidden');
    }

    /**
     * La siguiente función se encarga de crear la estructura
     * de la galería de imagenes del establecimiento temporal
     * según el nuevo ordenamiento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $data
     * @return array
     */
    public function getSortData($data)
    {
        $sortData = array();

        foreach ($data['galleriesSections'] as $currentGallery) {

            $auxSubGallery = array();
            $auxSubGallery['idSubGallery'] = $currentGallery['idSubGallery'];
            $auxSubGallery['subGallery'] = $currentGallery['nameSubGallery'];
            $auxSubGallery['images'] = $currentGallery['loadedFile'];

            if ($currentGallery['nameGallery'] == 'rooms') {

                if (!isset($sortData['rooms']))
                    $sortData['rooms'] = array();
                array_push($sortData['rooms'], $auxSubGallery);

            } else if ($currentGallery['nameGallery'] == 'otherGalleries') {

                if (!isset($sortData['otherGalleries']))
                    $sortData['otherGalleries'] = array();
                array_push($sortData['otherGalleries'], $auxSubGallery);
            }
        }

        foreach ($data['favoritesSection'] as $currentFavorite) {

            if (!isset($sortData['favorites']))
                $sortData['favorites'] = array();

            $auxSubGallery = array();
            $auxSubGallery['path'] = $currentFavorite['path'];
            $auxSubGallery['subGallery'] = $currentFavorite['nameSubGallery'];
            array_push($sortData['favorites'], $auxSubGallery);
        }

        return $sortData;
    }
}