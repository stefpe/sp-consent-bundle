<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->integerNode('cookie_lifetime')
                ->defaultValue(365 * 24 * 60 * 60) // 1 year in seconds
                ->info('Cookie lifetime in seconds')
            ->end()
            ->scalarNode('translation_domain')
                ->defaultValue('sp_consent')
                ->info('Translation domain for category names and descriptions')
            ->end()
            ->booleanNode('use_translations')
                ->defaultFalse()
                ->info('Whether to translate category names and descriptions')
            ->end()
            ->booleanNode('enable_logging')
                ->defaultTrue()
                ->info('Whether to log consent actions for GDPR compliance')
            ->end()
            ->scalarNode('log_level')
                ->defaultValue('info')
                ->info('Log level for consent actions (debug, info, notice, warning, error)')
            ->end()
            ->scalarNode('consent_version')
                ->defaultValue('1.0')
                ->info('Version of the consent policy (for tracking policy changes)')
            ->end()
            ->arrayNode('categories')
                ->useAttributeAsKey('key')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('description')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('required')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
                ->defaultValue([
                    'necessary' => [
                        'name' => 'cookie.category.necessary.name',
                        'description' => 'cookie.category.necessary.description',
                        'required' => true,
                    ],
                    'analytics' => [
                        'name' => 'cookie.category.analytics.name',
                        'description' => 'cookie.category.analytics.description',
                        'required' => false,
                    ],
                    'marketing' => [
                        'name' => 'cookie.category.marketing.name',
                        'description' => 'cookie.category.marketing.description',
                        'required' => false,
                    ],
                    'functional' => [
                        'name' => 'cookie.category.functional.name',
                        'description' => 'cookie.category.functional.description',
                        'required' => false,
                    ],
                ])
            ->end()
        ->end();
};