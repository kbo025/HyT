<?php
namespace Navicu\Core\Application\UseCases\AAVV\Customize\UploadLogo;

use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\AAVVDocument;
use Navicu\Core\Domain\Model\Entity\Document;

/**
 * Actualización de logo de la AAVV
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
 * @version 21/09/2016
 */
class UploadLogoHandler implements Handler
{
    /**
     * @param Command $command
     * @param RepositoryFactoryInterface|null $rf
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf = null)
    {
        try {

            $aavv = CoreSession::getUser()->getAAVVProfile()->getAAVV();

            $encoded = preg_replace('#^data:image/\w+;base64,#i', '', $command->getRequest()['file']);
            $decodedFile = base64_decode($encoded);

            $rpAavv = $rf->get('AAVV');
            $nameFile =
                'navicu-reserva-'.
                $aavv->getSlug().'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'images';

            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $logo = $aavv->getdocumentByType('LOGO');

            if(!is_null($logo)) {
                $aavvDocument = $logo;
                $document = $logo->getDocument();
                try {
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $logo->getDocument()->getFilename();
                    unlink($file);
                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,'',$e->getMessage() . $e->getFile() . $e->getLine());
                }
            } else {
                $document = new Document();
                $aavvDocument = new AAVVDocument();
            }

            $document->setFile($decodedFile);
            $document->setName($command->getRequest()['nameImage'] ?
                $command->getRequest()['nameImage'] : null
            );
            $document->setFileName($path.'/'.$nameFile);

            $aavvDocument->setType('LOGO');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_image', $path, $nameFile);

            $rpAavv->save($aavv);

            CoreSession::set('urlLogo',$document->getFileName());

            return new ResponseCommandBus(200,'Ok');
        } catch (\Exception $e) {
            return new ResponseCommandBus(500, 'Error', $e->getMessage());
        }
    }
}
