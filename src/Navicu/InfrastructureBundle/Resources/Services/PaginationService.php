<?php
/**
 * Implementación de una Clase para el manejo de los Servicios de Paginación.
 */
namespace Navicu\InfrastructureBundle\Resources\Services;

/**
 * Clase PaginationService para ejecutar los servicios relacionados con paginación.
 *
 * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
 * @author Currently Working: Joel D. Requena P.
 * @version 06/06/2015
 */
class PaginationService
{
    /**
     * Esta propiedad es usada para contener el contenedor de servicios
     * 
     * @var \Container_Services
     */
    protected $container;
    
    /**
     * Metodo Constructor de php
     *
     * @param \Container_Services $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Esta función es usada para manejar la paginación de un objeto 
     * 
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     * 
     * @param Object $data
     * @param Integer $numberPage
     * @return Array
     */
    public function pagination(&$data, $numberPage, $elemntPerPage = 10, $elementListNumber = 5)
    {
        $totalPage = count($data);

        if (count($data) > $elemntPerPage) {
            $paginator  = $this->container->get('knp_paginator');
            $pagination = $paginator->paginate(
                $data,
                $numberPage/*page number*/,
                $elemntPerPage/*limit per page*/
            );

            $data = $pagination->getItems();
            $page = $pagination->getPaginationData();
    
            $pageData["pageCount"] = $page["pageCount"];
            $pageData["current"] = $page["current"];
            $pageData["totalCount"] = $page["totalCount"];

            // Paginación por segmento
            if ($totalPage > $elementListNumber) {
                $pageData["min"] = $numberPage;
                $pageData["max"] = $numberPage + $elementListNumber;

                if ($pageData["max"] > $pageData["pageCount"]) {
                    $pageData["min"] = $pageData["pageCount"] - $elementListNumber;
                    $pageData["max"] = $pageData["pageCount"];
                }
            }

            if (isset($page["previous"])) {
                $pageData ["previous"] = $page["previous"];
                if (($numberPage - $elementListNumber) > 0)
                    $pageData ["pre"] = $numberPage - $elementListNumber;
                else
                    $pageData ["pre"] = 1;
            }

            if (isset($page["next"]) && ($pageData["pageCount"] - $elementListNumber) > $numberPage) {
                $pageData ["next"] = $page["next"];
                if (($numberPage + $elementListNumber) < $pageData["pageCount"])
                    $pageData ["post"] = $numberPage + $elementListNumber;
                else
                    $pageData ["post"] = $pageData["pageCount"];
            }

            return $pageData;
        } else {
            return 0;
        }
    }

    /**
     * Esta función es usada para manejar la paginación simple
     * de un objeto
     *
     * @author Joel D. Requena P. <Joel.2005.2@gmail.com>
     * @author Currently Working: Joel D. Requena P. <Joel.2005.2@gmail.com>
     *
     * @param Object $data
     * @param Integer $numberPage
     * @return Array
     */
    public function simplePagination(&$data, $numberPage, $elemntPerPage = 20)
    {

        $paginator  = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $data,
            $numberPage/*page number*/,
            $elemntPerPage/*limit per page*/
        );

        $data = $pagination->getItems();
        $page = $pagination->getPaginationData();

        $pageData ["pageCount"]= $page["pageCount"];
        $pageData ["current"]= $page["current"];
        $pageData ["totalCount"]= $page["totalCount"];

        if (isset($page["previous"])) {
            $pageData ["previous"] = $page["previous"];
        }

        if (isset($page["next"])) {
            $pageData ["next"]= $page["next"];
        }

        return $pageData;

    }
}
