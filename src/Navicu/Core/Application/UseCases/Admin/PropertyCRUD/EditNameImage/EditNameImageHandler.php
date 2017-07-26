<?php
namespace Navicu\Core\Application\UseCases\Admin\PropertyCRUD\EditNameImage;


use Navicu\Core\Application\Contract\ResponseCommandBus;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Domain\Adapter\CoreSession;
use Navicu\Core\Domain\Model\Entity\LogsUser;

/**
 * La siguiente clase implementa el handler del caso de uso "EditNameImage"
 *
 * Class EditNameImage
 *
 * @author Freddy Contreras <freddycontreras3@gmail.com>
 * @version 12/02/2015
 */
class EditNameImageHandler implements Handler
{
    /**
     * Instancia del repositoryFactory
     *
     * @var RepositoryFactory $rf
     */
    protected $rf;

    /**
     * Ejecuta las tareas solicitadas
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @version 18/11/2015
     * @param Command $command
     * @param RepositoryFactoryInterface $rf
     * @return ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $this->rf = $rf;
        $dataToLog = [];
        $data = $command->getRequest();

        $rpDocument = $this->rf->get('Document');
        $image = $rpDocument->find($data['idImage']);

        if ($image) {
            /* Info para el log al cambiar el nombre de una imagen*/
            $this->saveImageNameToLog($image, $rf, $data['slug'], $data['name']);

            $image->setName($data['name']);
            $rpDocument->save($image);

            return new ResponseCommandBus(200, 'Ok');
        }

        return new ResponseCommandBus(404, 'Bad Request');
    }

    /**
     * Funcion encargada de guardar el nombre de las imagenes al log
     *
     * @param object $image, objeto imagen
     * @param $rf
     * @param string $slug
     * @param string $name, nombre por el cual cambiar
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 21/06/2016
     */
    public function saveImageNameToLog($image, $rf, $slug, $name) {
        $dataToLog['action'] = "update";
        $dataToLog['idResource'] = $image->getId();
        $dataToLog['property'] = $rf->get('Property')->findBySlug($slug);
        $dataToLog['resource'] = "room name";
        $dataToLog['user'] = CoreSession::getUser();
        $dataToLog["description"]['path'] = $image->getFileName();
        $dataToLog["description"]['name'] = $name;

        $logsUser = new LogsUser();
        $logsUser->updateObject($dataToLog);
        $rf->get('LogsUser')->save($logsUser);
    }
}