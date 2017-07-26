<?php


namespace Navicu\Core\Application\UseCases\AAVV\Security\DeleteUser;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

class DeleteUserHandler implements Handler
{
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $id = $command->get('id');

        $userrep = $rf->get('User');

        $profilerep = $rf->get('AAVVProfile');

        $user = $userrep->find($id);

        $profile = $user->getAavvProfile();



        if ($profile && !is_null($profile->getProfileOrder()) && $profile->getProfileOrder() != 1) {

            try {
                if($profile->getStatus() == 1){
                    $profile->setStatus(3);
                    $profile->setDeletedAt(new \DateTime('now'));
                    $profile->getUser()->setEnabled(false);
                    $profilerep->save($profile);
                } elseif ($profile->getStatus() == 0) {
                    $userrep->delete($user);
                    $profilerep->delete($profile);
                }


            } catch(\Exception $e) {
                return new ResponseCommandBus(400,$e->getMessage() . $e->getFile() . $e->getLine());
            }

            return new ResponseCommandBus(201,'ok');
        } else {
            return new ResponseCommandBus(404,'El usuario no existe o es el unico usuario');
        }
    }
}