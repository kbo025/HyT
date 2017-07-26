<?php 

namespace Navicu\Core\Application\Services;

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
class RegisterTempOwner
{
    private $rf;

    /**
    * Constructor del servicio
    *
    * @param RepositoryFactory $rf
    */
    public function __construct($rf)
    { 
        $this->rf= $rf;
    }

    /**
    * La función retorna los datos de una sección del formulario del registro 
    * del establecimiento temporal (TempoOwner)
    * 
    * @param String $slug
    * @param Integer $section
    * @param Integer $lastSec
    * @author Freddy Contreras <freddycontreras3@gmail.com>
    * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
    * @version 24/06/2015
    */
    public function getDataSection($slug, $section = null, &$lastSec)
    {
        $repTempRepository = $this->rf->get('TempOwner')
            ->findOneByArray(array('slug' => $slug));

        $lastSec = $repTempRepository->getLastsec();

        if ( $section == null )
            $section = $lastSec;

        $responseSection = null;

        switch ($section) {
            case 1:
                $responseSection = $repTempRepository->getPropertyForm();
                break;

            case 2:
                $responseSection = $repTempRepository->getServicesForm();
                break;

            case 3: 
                $responseSection = $repTempRepository->getRoomsForm();
                break;

            case 4:
                $responseSection = $repTempRepository->getGalleryForm();
                break;            
            default:
                $responseSection = $fieldSection = null;
                break;
        }

        return $responseSection;
    }
}