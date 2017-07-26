<?php
namespace Navicu\Core\Application\UseCases\AAVV\Register\Step1\UploadDocument;


use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Model\Entity\Document;
use Navicu\Core\Domain\Model\Entity\AAVVDocument;
use Navicu\Core\Domain\Model\ValueObject\Slug;
use Navicu\Core\Domain\Adapter\CoreSession;

/**
 * La siguiente clase implementa el handler del caso de uso "UploadDocument"
 * Subir un documento de una AAVV
 *
 * 
 *
 * @author Alejandro Conde <adcs2008@gmail.com>
 * @version 02/09/2016
 */
class UploadDocumentHandler implements Handler
{
	/**
     * Instancia del repositoryFactory
     *
     * @var RepositoryFactoryInterface $rf
     */
    //protected $rf;

    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {

        $data = $command->getRequest();
        //die(var_dump($data));
        if ($data['documentType'] == 'rtn') {
            $responseArray = $this->uploadRTNDocument($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(200,'Ok',$responseArray, null);
            else
                return new ResponseCommandBus(404, 'Bad Request');

        } else if ($data['documentType'] == 'rif') {
            $responseArray = $this->uploadRIFDocument($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(200,'Ok',$responseArray, null);
            else
                return new ResponseCommandBus(404, 'Bad Request');
        } else if ($data['documentType'] == 'lease') {
            $responseArray = $this->uploadLeaseDocument($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(201, 'Ok', $responseArray);
            else
                return new ResponseCommandBus(404, 'Bad Request', null);
        } else if ($data['documentType'] == 'logo') {
            $responseArray = $this->uploadLogo($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(201, 'Ok', $responseArray, null);
            else
                return new ResponseCommandBus(404, 'Bad Request');
        } else if ($data['documentType'] == 'image') {
            $responseArray = $this->uploadImage($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(201, 'Ok', $responseArray, null);
            else
                return new ResponseCommandBus(404, 'Bad Request');
        } else if ($data['documentType'] == 'ctr') {
            $responseArray = $this->uploadContract($data, $rf);
            if ($responseArray)
                return new ResponseCommandBus(201, 'Ok', $responseArray, null);
            else
                return new ResponseCommandBus(404, 'Bad Request');
        }

        return new ResponseCommandBus(404, 'Bad Request');
    }

    /**
     * La función carga en el sistema el documento correspondiente al RTN de la AAVV
     *
     * @author Alejandro Conde <adcs2008@gmail.com>
     * @param $data
     * @return Array
     * @version 03/09/2016
     */
    private function uploadRTNDocument($data, $rf)
    {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:application/\w+;base64,#i', '', $data['file']);

            $decodedFile = base64_decode($encoded);

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'documents';



            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $rtn = $aavv->getdocumentByType('RTN');

            if(!is_null($rtn)) {
                $aavvDocument = $rtn;
                $document = $rtn->getDocument();
                try {
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $rtn->getDocument()->getFilename();
                    unlink($file);


                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                }

            } else {
                $document = new Document();
                $aavvDocument = new aavvDocument();
            }


            $document->setFile($decodedFile);
            $document->setName($data['originalName']);
            $document->setFileName($path.'/'.$nameFile);


            $aavvDocument->setType('RTN');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_document', $path, $nameFile);
            
            $aavv_rep->save($aavv);

            
    		

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
        }

        return false;
    }

    private function uploadContract($data, $rf) {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:application/\w+;base64,#i', '', $data['file']);

            $decodedFile = base64_decode($encoded);

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'documents';



            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $rtn = $aavv->getdocumentByType('CTR');

            if(!is_null($rtn)) {
                $aavvDocument = $rtn;
                $document = $rtn->getDocument();
                try {
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $rtn->getDocument()->getFilename();
                    unlink($file);


                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                }

            } else {
                $document = new Document();
                $aavvDocument = new aavvDocument();
            }


            $document->setFile($decodedFile);
            $document->setName($data['originalName']);
            $document->setFileName($path.'/'.$nameFile);


            $aavvDocument->setType('CTR');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_document', $path, $nameFile);

            $aavv_rep->save($aavv);




            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
        }

        return false;
    }

    private function uploadLeaseDocument($data, $rf)
    {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:application/\w+;base64,#i', '', $data['file']);

            $decodedFile = base64_decode($encoded);

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'documents';



            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $lease = $aavv->getdocumentByType('LEASE');

            if(!is_null($lease)) {
                $aavvDocument = $lease;
                $document = $lease->getDocument();
                try {
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $lease->getDocument()->getFilename();
                    unlink($file);


                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                }

            } else {
                $document = new Document();
                $aavvDocument = new aavvDocument();
            }


            $document->setFile($decodedFile);
            $document->setName($data['originalName']);
            $document->setFileName($path.'/'.$nameFile);


            $aavvDocument->setType('LEASE');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_document', $path, $nameFile);
            
            $aavv_rep->save($aavv);

            
    		

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
        }

        return false;
    }

    private function uploadLogo($data, $rf)
    {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:image/\w+;base64,#i', '', $data['file']);

            $decodedFile = base64_decode($encoded);


            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

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
                    return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                }

            } else {
                $document = new Document();
                $aavvDocument = new aavvDocument();
            }


            $document->setFile($decodedFile);
            $document->setName('');
            $document->setFileName($path.'/'.$nameFile);


            $aavvDocument->setType('LOGO');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_image', $path, $nameFile);
            
            $aavv_rep->save($aavv);

            
    		

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();
            return $responseArray;
        }

        return false;
    }

    private function uploadImage($data, $rf)
    {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:image/\w+;base64,#i', '', $data['file']);

            //die(var_dump($encoded));

            $decodedFile = base64_decode($encoded);

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'images';



            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $document = new Document();
            $document->setFile($decodedFile);
            $document->setName('');
            $document->setFileName($path.'/'.$nameFile);

            $aavvDocument = new aavvDocument();
            $aavvDocument->setType('IMAGE');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);

            $document->upload('aavv_image', $path, $nameFile);
            
            $aavv_rep->save($aavv);

            
    		

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['id'] = $aavvDocument->getId();
            return $responseArray;
        }

        return false;
    }

    /**
     * Agregando campo RIF en el registro de la aavv
     *
     * @param $data
     * @param $rf
     * @return array|bool
     */
    private function uploadRIFDocument($data, $rf)
    {
        $aavv_rep = $rf->get('AAVV');
        $aavv = $aavv_rep->findOneByArray(['slug' => $data['slug']]);

        if ($aavv) {

            $encoded = preg_replace('#^data:application/\w+;base64,#i', '', $data['file']);

            $decodedFile = base64_decode($encoded);

            $nameFile =
                'navicu-reserva-'.
                $data['slug'].'-';

            $folder1 = $aavv->getSlug();
            $folder2 = 'documents';

            //Se crea el nombre de la ruta del archivo
            $path = 'aavv/' .
                $folder1 . '/' .
                $folder2 . '/';

            $rif = $aavv->getdocumentByType('RIF');

            if(!is_null($rif)) {
                $aavvDocument = $rif;
                $document = $rif->getDocument();
                try {
                    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/images_original/' . $rif->getDocument()->getFilename();
                    unlink($file);


                } catch(\Exception $e) {
                    return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
                }

            } else {
                $document = new Document();
                $aavvDocument = new aavvDocument();
            }


            $document->setFile($decodedFile);
            $document->setName($data['originalName']);
            $document->setFileName($path.'/'.$nameFile);


            $aavvDocument->setType('RIF');
            $aavvDocument->setAavv($aavv);
            $aavvDocument->setDocument($document);

            $aavv->addDocument($aavvDocument);
            $document->upload('aavv_document', $path, $nameFile);
            $aavv_rep->save($aavv);

            $responseArray = array();
            $responseArray['name'] = $document->getName();
            $responseArray['path'] = $document->getFileName();
            $responseArray['idImage'] = $document->getId();

            return $responseArray;
        }

        return false;
    }
}