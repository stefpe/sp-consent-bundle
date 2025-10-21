<?php

use Stefpe\SpConsentBundle\Component\CookieConsentBanner;
use Stefpe\SpConsentBundle\EventListener\CookieConsentResponseListener;
use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    
    // Enable autowire and autoconfigure for this bundle's services
    $services
        ->defaults()
            ->autowire()
            ->autoconfigure();
    
    $services
        ->set('sp_consent.cookie_consent_service', CookieConsentService::class)
            ->args([
                '%sp_consent.categories%',
                '%sp_consent.cookie_lifetime%',
                service('translator'),
                '%sp_consent.translation_domain%',
                '%sp_consent.use_translations%',
                service('logger')->nullOnInvalid(),
                '%sp_consent.enable_logging%',
                '%sp_consent.log_level%',
                '%sp_consent.consent_version%',
            ])
            ->tag('monolog.logger', ['channel' => 'sp_consent'])
            ->public();
    
    // Alias for autowiring
    $services
        ->alias(CookieConsentService::class, 'sp_consent.cookie_consent_service')
        ->public();
    
    // Auto-register Components, EventListeners, and Twig Extensions via autoconfigure
    $services
        ->load('Stefpe\\SpConsentBundle\\Component\\', __DIR__ . '/../src/Component/');
    
    $services
        ->load('Stefpe\\SpConsentBundle\\EventListener\\', __DIR__ . '/../src/EventListener/');
    
    $services
        ->load('Stefpe\\SpConsentBundle\\Twig\\', __DIR__ . '/../src/Twig/');
};