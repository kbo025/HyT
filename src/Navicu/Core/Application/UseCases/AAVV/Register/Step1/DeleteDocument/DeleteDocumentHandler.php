<?php


namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\DeleteDocument;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class DeleteDocumentHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        $aavvdocumentrep = $rf->get('AAVVDocument');
        $documentrep = $rf->get('Document');
        $aavvrep = $rf->get('AAVV');

        try {
            $tempdocument = $documentrep->findOneByArray(['fileName' => $command->get('path')]);
            $document = $aavvdocumentrep->findOneByArray(['document' => $tempdocument]);

            if ($document) {

                $file = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/images_original/'.$document->getDocument()->getFilename();


                unlink($file);

                $aavv = $document->getAavv();

                $aavv->removeDocument($document);

                $aavvrep->save($aavv);

                $documentinstance = $documentrep->find($document->getDocument()->getId());

                $aavvdocumentrep->delete($document);

                $documentrep->delete($documentinstance);



                return new ResponseCommandBus(201, 'OK');
            } else {
                return new ResponseCommandBus(404, 'File Not Found');
            }

        } catch(\Exception $e) {
            return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
}