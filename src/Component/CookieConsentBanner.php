<?php

namespace Stefpe\SpConsentBundle\Component;

use Stefpe\SpConsentBundle\Enum\ConsentAction;
use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('CookieConsentBanner')]
class CookieConsentBanner
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public bool $showBanner = true;

    #[LiveProp(writable: true)]
    public bool $showAdvanced = false;

    #[LiveProp(writable: true)]
    public array $preferences = [];

    public function __construct(
        private CookieConsentService $cookieConsentService,
        private RequestStack $requestStack
    ) {
    }

    public function mount(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $this->cookieConsentService->hasConsent($request)) {
            $this->showBanner = false;
        }
        
        // Initialize preferences with current settings or defaults
        if ($request) {
            $currentPreferences = $this->cookieConsentService->getConsentPreferences($request);
            if (!empty($currentPreferences)) {
                $this->preferences = $currentPreferences;
            } else {
                // Initialize with all categories as false (except required ones)
                foreach ($this->cookieConsentService->getCookieCategories() as $category => $config) {
                    $this->preferences[$category] = $config['required'] ?? false;
                }
            }
        }
    }

    #[LiveAction]
    public function acceptAll(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $this->cookieConsentService->acceptAllCookies($request);
        $this->showBanner = false;
        
        // Get all categories and set them to true
        $acceptAllPreferences = [];
        foreach ($this->cookieConsentService->getCookieCategories() as $category => $config) {
            $acceptAllPreferences[$category] = true;
        }
        
        // Store cookie in session for the response listener to set it
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->hasSession()) {
            $request->getSession()->set('sp_consent_cookie', $cookie);
        }

        $this->fireCookieConsentChangedEvent($acceptAllPreferences);
    }

    #[LiveAction]
    public function rejectOptional(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $this->cookieConsentService->rejectOptionalCookies($request);
        $this->showBanner = false;
        
        // Get the preferences that were just set (only required = true)
        $rejectOptionalPreferences = [];
        foreach ($this->cookieConsentService->getCookieCategories() as $category => $config) {
            $rejectOptionalPreferences[$category] = $config['required'] ?? false;
        }
        
        // Store cookie in session for the response listener to set it
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->hasSession()) {
            $request->getSession()->set('sp_consent_cookie', $cookie);
        }

        $this->fireCookieConsentChangedEvent($rejectOptionalPreferences);
    }

    #[LiveAction]
    public function showAdvancedSettings(): void
    {
        $this->showAdvanced = true;
    }

    #[LiveAction]
    public function hideAdvancedSettings(): void
    {
        $this->showAdvanced = false;
    }

    #[LiveAction]
    public function reopenSettings(): void
    {
        $this->showBanner = true;
        $this->showAdvanced = true;
    }

    #[LiveAction]
    public function savePreferences(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $this->cookieConsentService->saveConsentPreferences($this->preferences, $request, ConsentAction::CUSTOM);
        $this->showBanner = false;
        
        // Store cookie in session for the response listener to set it
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->hasSession()) {
            $request->getSession()->set('sp_consent_cookie', $cookie);
        }

        $this->fireCookieConsentChangedEvent($this->preferences);
    }

    public function shouldShowBanner(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        return !$this->cookieConsentService->hasConsent($request);
    }

    public function getCookieCategories(): array
    {
        return $this->cookieConsentService->getCookieCategories();
    }

    public function hasConsent(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }

        return $this->cookieConsentService->hasConsent($request);
    }

    private function fireCookieConsentChangedEvent(array $preferences): void
    {
        $this->dispatchBrowserEvent('cookieConsent:changed', ['preferences' => $preferences]);
    }
}

