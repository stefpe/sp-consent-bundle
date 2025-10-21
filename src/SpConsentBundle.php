<?php

namespace Stefpe\SpConsentBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SpConsentBundle extends AbstractBundle
{

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Set configuration parameters
        $container->parameters()
            ->set('sp_consent.categories', $config['categories'])
            ->set('sp_consent.cookie_lifetime', $config['cookie_lifetime']);

        $container->import('../config/services.php');
    }
}
