<?php

namespace Stefpe\SpConsentBundle\Twig;

use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConsentExtension extends AbstractExtension
{
    public function __construct(
        private readonly CookieConsentService $cookieConsentService,
        private readonly RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('consent_preferences', [$this, 'getConsentPreferences']),
            new TwigFunction('has_consent', [$this, 'hasConsent']),
            new TwigFunction('has_consent_for', [$this, 'hasConsentFor']),
        ];
    }

    /**
     * Get the current consent preferences as an array
     */
    public function getConsentPreferences(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return [];
        }

        return $this->cookieConsentService->getConsentPreferences($request);
    }

    /**
     * Check if user has given any consent
     */
    public function hasConsent(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        return $this->cookieConsentService->hasConsent($request);
    }

    /**
     * Check if user has given consent for a specific category
     */
    public function hasConsentFor(string $category): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        $preferences = $this->cookieConsentService->getConsentPreferences($request);
        
        return $preferences[$category] ?? false;
    }
}

