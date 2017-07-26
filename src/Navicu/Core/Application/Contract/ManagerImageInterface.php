<?php

namespace Navicu\Core\Application\Contract;

/**
 * Interface Command ManagerImageInterface
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 20/11/2015
 */
interface ManagerImageInterface
{
    /**
     * La siguiente función transforma una imagen dado un path
     * en las dimensiones xs, sm y md
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param string $pathImage
     * @version 23/11/2015
     */
    public function generateImages($pathImage);

    /**
     * La siguiente función se encarga de eliminar una imagen
     * en las distintas carpetas (original, md, sm, xs) dado el path de la imagen
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 09/12/2015
     * @param $pathImage
     * @return mixed
     */
    public function deleteImages($pathImage);

    /**
     * Crear una imagen mediante un filtro en especifico
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $pathImage
     * @param $filter
     * @version 13/01/2016
     */
    public function generateFilter($pathImage, $filter);

    /**
     * Elimina una imagen mediante un filtro en especifico
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param $pathImage
     * @param $filter
     * @return boolean
     * @version 13/01/2015
     */
    public function deleteFilter($pathImage, $filter);
}