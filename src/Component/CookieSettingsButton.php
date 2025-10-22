<?php

namespace Stefpe\SpConsentBundle\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'CookieSettingsButton',
    template: '@SpConsentBundle/components/CookieSettingsButton.html.twig'
)]
class CookieSettingsButton
{
    public string $label = 'Cookie-Einstellungen';
    public string $class = '';
}

