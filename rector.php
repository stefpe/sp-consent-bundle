<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        // Aktiviere dies, falls du Unit-Tests in deinem Bundle hast:
        __DIR__ . '/tests',
    ])

    // Die modernen, fluenten Methoden von Rector (Rector 1.x)
    ->withSets([
            // Aktualisiert die PHP-Syntax auf PHP 8.4
        LevelSetList::UP_TO_PHP_84,

            // Symfony Code Quality (z.B. neuere EventSubscriber-Logiken, neuere DI-Patterns)
        SymfonySetList::SYMFONY_CODE_QUALITY,

            // Symfony-Sets durchgehen. 
            // WICHTIG: rector-symfony hat meist noch kein hartes SYMFONY_80 Set, 
            // da Symfony 8 einfach Symfony 7.4 ohne Deprecations ist. 
            // Daher ziehen wir das Bundle auf den höchsten 7.x-Stand hoch.
        SymfonySetList::SYMFONY_64,
        SymfonySetList::SYMFONY_71,
    ])

    // Für Bundles extrem wichtig: Symfony-Attribute (wie #[AsCommand], #[AsEventListener]) nutzen
    ->withAttributesSets(symfony: true)

    // Falls du das Symfony/Doctrine-Plugin auch im Bundle hast
    // ->withAttributesSets(symfony: true, doctrine: true)

    ->withSkip([
        // Der TreeBuilder (Configuration.php) wird oft von Rector falsch formatiert 
        // (z. B. durch strikte Typisierung auf den RootNode).
        // Für Bundles ist es Best Practice, diese Datei beim automatischen Refactoring auszuschließen:
        __DIR__ . '/src/DependencyInjection/Configuration.php',
    ]);