<?php
// src/YourCompany/YourBundle/DependencyInjection/Compiler/FOSUserOverridePass.php

namespace Navicu\InfrastructureBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FOSUserOverridePass implements CompilerPassInterface{

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('fos_user.listener.authentication')->setClass('Navicu\InfrastructureBundle\EventListener\AuthenticationListener');
    }
}