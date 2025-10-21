<?php

use Stefpe\SpConsentBundle\Component\CookieConsentBanner;
use Stefpe\SpConsentBundle\EventListener\CookieConsentResponseListener;
use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('sp_consent.cookie_consent_service', CookieConsentService::class)
            ->args([
                '%sp_consent.categories%',
                '%sp_consent.cookie_lifetime%',
            ])
            ->public();
    
    // Alias for autowiring
    $container->services()
        ->alias(CookieConsentService::class, 'sp_consent.cookie_consent_service')
        ->public();
    
    // Live Component
    $container->services()
        ->set('sp_consent.cookie_consent_banner', CookieConsentBanner::class)
            ->args([
                service('sp_consent.cookie_consent_service'),
                service('request_stack'),
            ])
            ->tag('controller.service_arguments')
            ->tag('twig.component', ['key' => 'CookieConsentBanner']);
    
    // Response Listener
    $container->services()
        ->set('sp_consent.response_listener', CookieConsentResponseListener::class)
            ->tag('kernel.event_listener', [
                'event' => 'kernel.response',
                'method' => 'onKernelResponse'
            ]);
};