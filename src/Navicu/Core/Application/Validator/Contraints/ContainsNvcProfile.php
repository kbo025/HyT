<?php
namespace Navicu\Core\Application\Validator\Contraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Navicu\Core\Domain\Adapter\RepositoryFactory;

/**
 * Clase ContainsUser
 *
 * Se define una clase y una serie de funciones necesarios para definir las
 * Validaciones necesarias para el manejo de la entidad NvcProfile.
 *
 * @author Freddy Contreras
 * @author Currently Working: Freddy Contreras
 */
class ContainsNvcProfile
{
    /**
     * Valida al momento de editar un usuario
     * si el username o email, si existen o pertenecen a
     * otro usuario
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Object Room $object
     * @return \Validator
     */
    public static function editNvcProfile($object, ExecutionContextInterface $context)
    {
        if ($object->get('user_id')) {
            $rpUser = RepositoryFactory::get('NvcProfile');
            $userNvc = $rpUser->findExistUsername(
                $object->get('username'),
                $object->get('user_id')
            );

            if (!empty($userNvc))
                $context->buildViolation('exist,' . $object->get('username') . ',string')
                    ->atPath('username')
                    ->addViolation();

            $userNvc = $rpUser->findExistEmail(
                $object->get('email'),
                $object->get('user_id')
            );

            if (!empty($userNvc))
                $context->buildViolation('exist,' . $object->get('email') . ',string')
                    ->atPath('email')
                    ->addViolation();
        }
    }


    /**
     * Función valida al momento de crear un usuario
     * si existe previamente el username o email
     *
     * @author Freddy Contreras <freddycontreras3@gmail.com>
     *
     * @param Object Room $object
     * @return \Validator
     */
    public static function createNvcProfile($object, ExecutionContextInterface $context)
    {
        $rpUser = RepositoryFactory::get('User');

        if ($object->get('username')) {
            $userNvc = $rpUser->findOneBy([
                'username' => $object->get('username')
            ]);

            if (!empty($userNvc))
                $context->buildViolation('exist,' . $object->get('username') . ',string')
                    ->atPath('username')
                    ->addViolation();
        }

        if ($object->get('email')) {
            $userNvc = $rpUser->findOneBy([
                'email' => $object->get('email')
            ]);

            if (!empty($userNvc))
                $context->buildViolation('exist,' . $object->get('email') . ',string')
                    ->atPath('email')
                    ->addViolation();
        }
    }
}
