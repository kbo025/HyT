<?php

namespace Navicu\InfrastructureBundle;

use Navicu\InfrastructureBundle\DependencyInjection\Compiler\FOSUserOverridePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Navicu\InfrastructureBundle\DependencyInjection\Compiler\ValidatorRouting;

class NavicuInfrastructureBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FOSUserOverridePass());
        $container->addCompilerPass(new ValidatorRouting());
    }

	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
