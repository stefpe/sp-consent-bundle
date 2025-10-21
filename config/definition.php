<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->integerNode('cookie_lifetime')
                ->defaultValue(365 * 24 * 60 * 60) // 1 year in seconds
                ->info('Cookie lifetime in seconds')
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
                        'name' => 'Notwendige Cookies',
                        'description' => 'Diese Cookies sind für das Funktionieren der Website erforderlich und können nicht deaktiviert werden.',
                        'required' => true,
                    ],
                    'analytics' => [
                        'name' => 'Analyse Cookies',
                        'description' => 'Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren.',
                        'required' => false,
                    ],
                    'marketing' => [
                        'name' => 'Marketing Cookies',
                        'description' => 'Diese Cookies werden verwendet, um Ihnen relevante Werbung zu zeigen.',
                        'required' => false,
                    ],
                    'functional' => [
                        'name' => 'Funktionale Cookies',
                        'description' => 'Diese Cookies ermöglichen erweiterte Funktionen und Personalisierung.',
                        'required' => false,
                    ],
                ])
            ->end()
        ->end();
};