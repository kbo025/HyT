<?php

namespace Navicu\InfrastructureBundle\Validator\Constraints;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Clase ConstainsPassword
 *
 * Se define un conjunto de validaciones para la entidad User
 *
 * @author Freddy Contreras <freddy.contreras3@gmail.com>
 * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
 * @version 05/08/2015
 */
class ContainsUser
{
    /**
     * La siguiente función se encarga de validar si una contraseña es valida
     *
     * @author Freddy Contreras <freddy.contreras3@gmail.com>
     * @author Currently Working: Freddy Contreras <freddy.contreras3@gmail.com>
     *
     * @param Object Linkage $objLinkage
     * @return \Validator
     */
    public static function isValidPassword($object, ExecutionContextInterface $context)
    {
        $password = $object->getPlainPassword();
        $error = false;

        if(strlen($password) > 5){

            $num=ereg_replace("[^0-9]", "", $password);
            if(!empty($num)){

                $min=ereg_replace("[^a-z]", "", $password);
                if(empty($min))
                    $error = 2;
            } else {
                $error = 2;
            }
        } else {
            $error = 1;
        }

        if ($error) {
            if ($error == 1)
                $errorMessage = "share.message.password_error_length";
            else
                $errorMessage = "share.message.password_error";


            $context->buildViolation($errorMessage)
                ->atPath('plainPassword')
                ->addViolation();
        }
    }
}