<?php
namespace Odl\ShadowBundle;

use Odl\ShadowBundle\DependencyInjection\Compiler\AddValidatorNamespaceAliasPass;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShadowBundle
	extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Add custom validator
        $container->addCompilerPass(
        	new AddValidatorNamespaceAliasPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
	
}
