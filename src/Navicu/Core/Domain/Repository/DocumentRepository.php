<?php 

namespace Navicu\Core\Domain\Repository;

use Navicu\Core\Domain\Model\Entity\Document;

/**
*   Interfaz de la DocumentRepository
*
*   @author Freddy Contreras <freddycontreras3@gmail.com>
*   @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
*   @version 21-05-2015
*/
interface DocumentRepository
{
    /**
     * Almacena en la BD los datos de un documento
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param  Document
     */
    public function save($document);

    /**
     * La siguiente función se encarga de eliminar
     * un Documento en la BD
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @param Document $document
     * @version 12/01/2016
     */
    public function delete($document);
}