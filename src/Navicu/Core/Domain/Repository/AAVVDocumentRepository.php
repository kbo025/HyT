<?php
/**
 * Created by PhpStorm.
 * User: developer10
 * Date: 30/09/16
 * Time: 01:44 PM
 */

namespace Navicu\Core\Domain\Repository;
use Navicu\Core\Domain\Model\Entity\AAVVDocument;

interface AAVVDocumentRepository
{
    public function delete($document);

    public function save($document);
}