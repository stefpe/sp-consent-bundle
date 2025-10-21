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
            ->set('sp_consent.cookie_lifetime', $config['cookie_lifetime'])
            ->set('sp_consent.translation_domain', $config['translation_domain'])
            ->set('sp_consent.use_translations', $config['use_translations'])
            ->set('sp_consent.enable_logging', $config['enable_logging'])
            ->set('sp_consent.log_level', $config['log_level'])
            ->set('sp_consent.consent_version', $config['consent_version']);

        $container->import('../config/services.php');
    }
}
