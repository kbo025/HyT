<?php

namespace Navicu\Core\Application\UseCases\Admin\Users\SetRecruitToProperty;

use Navicu\Core\Application\Contract\Command;
use Navicu\Core\Application\Contract\Handler;
use Navicu\Core\Application\Contract\RepositoryFactoryInterface;
use Navicu\Core\Application\Contract\ResponseCommandBus;

/**
 * Clase encargada de asignar el captador al establecimiento registrado
 *
 * Class SetRecruitToPropertyHandler
 * @package Navicu\Core\Application\UseCases\Admin\Users\SetRecruitToProperty
 */
class SetRecruitToPropertyHandler implements Handler
{

    /**
     *  Ejecuta las tareas solicitadas
     *
     * @param \Navicu\Core\Application\Contract\Command $command
     * @param \Navicu\Core\Application\Contract\RepositoryFactoryInterface $rf
     * @return \Navicu\Core\Application\Contract\ResponseCommandBus
     */
    public function handle(Command $command, RepositoryFactoryInterface $rf)
    {
        $request = $command->getRequest();

        // Si es de tipo property
        if ($request['propertyType'] == 1)
            $response = $this->setProperty($request, $rf);
        else
            $response = $this->setTempOwner($request, $rf);

        return new ResponseCommandBus($response['code'], $response['message'], $response['success']);
    }

    /**
     * Asignación de un establecimiento temporal a un comercial
     *
     * @param $request
     * @param $rf
     * @return bool
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 14/02/2017
     */
    private function setTempOwner($request, $rf)
    {
        $rfNvcProfile = $rf->get('NvcProfile');
        $rfTempOwner = $rf->get('TempOwner');

        $nvcProfile = $rfNvcProfile->findOneBy(['id' => $request['nvcProfileId']]);
        $tempOwner = $rfTempOwner->findOneBy(['id' => $request['propertyId']]);

        // Si ya existio un nvcProfile asignado no hacemos nada
        if (!is_null($tempOwner->getRecruit()))
            return ["success" => false, "message" => 'cant continue', "code" => 400];

        if (!$nvcProfile or !$tempOwner)
            return ["success" => false, "message" => 'something is missing', "code" => 400];

        try {
            $tempOwner->setNvcProfile($nvcProfile);
            $nvcProfile->addTempOwner($tempOwner);
            $rfTempOwner->persistObject($nvcProfile);
            $rfNvcProfile->persistObject($tempOwner);
            $rfTempOwner->flushObject();

            return ["success" => true, "message" => 'ok', "code" => 200];
        } catch (\Exception $e){
            return [
                "success" => false,
                "message" => $e->getMessage()."\\n".$e->getFile()."\\n".$e->getLine(),
                "code" => 500
            ];
        }
    }

    /**
     * Asignación de un establecimiento afiliado a un comercial
     *
     * @param $request
     * @param $rf
     * @return bool
     * @author Isabel Nieto <isabelcnd@gmail.com>
     * @version 14/02/2017
     */
    private function setProperty($request, $rf)
    {
        $rfNvcProfile = $rf->get('NvcProfile');
        $rfProperty = $rf->get('Property');

        $nvcProfile = $rfNvcProfile->findOneBy(['id' => $request['nvcProfileId']]);
        $property = $rfProperty->findOneBy(['public_id' => $request['propertyId']]);

        // Si ya existio un nvcProfile asignado no hacemos nada
        if (!is_null($property->getRecruit()))
            return ["success" => false, "message" => 'cant continue', "code" => 400];

        if (!$nvcProfile or !$property)
            return ["success" => false, "message" => 'something is missing', "code" => 400];

        try {
            $property->setRecruit($nvcProfile);
            $nvcProfile->addPropertiesRecruit($property);
            $rfProperty->persistObject($nvcProfile);
            $rfNvcProfile->persistObject($property);
            $rfProperty->flushObject();

            return ["success" => true, "message" => 'ok', "code" => 200];
        } catch (\Exception $e){
            return [
                "success" => false,
                "message" => $e->getMessage()."\\n".$e->getFile()."\\n".$e->getLine(),
                "code" => 400
            ];
        }
    }
}