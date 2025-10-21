<?php

namespace Stefpe\SpConsentBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class CookieConsentService
{
    public const COOKIE_NAME = 'cookie_consent_preferences';

    public function __construct(
        private readonly array $cookieCategories,
        private readonly int $cookieLifetime
    ) {
    }

    public function hasConsent(Request $request): bool
    {
        return $request->cookies->has(self::COOKIE_NAME);
    }

    public function getConsentPreferences(Request $request): array
    {
        $cookieValue = $request->cookies->get(self::COOKIE_NAME);
        
        if (!$cookieValue) {
            return [];
        }

        $preferences = json_decode($cookieValue, true);
        return is_array($preferences) ? $preferences : [];
    }

    public function isCategoryAllowed(Request $request, string $category): bool
    {
        // Check if the category is required (always allowed)
        if (isset($this->cookieCategories[$category]) && ($this->cookieCategories[$category]['required'] ?? false)) {
            return true;
        }

        $preferences = $this->getConsentPreferences($request);
        return isset($preferences[$category]) && $preferences[$category] === true;
    }

    public function saveConsentPreferences(array $preferences): Cookie
    {
        // Ensure all required categories are always enabled
        foreach ($this->cookieCategories as $category => $config) {
            if ($config['required'] ?? false) {
                $preferences[$category] = true;
            }
        }

        // Add timestamp
        $preferences['timestamp'] = time();

        $cookieValue = json_encode($preferences);

        return Cookie::create(
            self::COOKIE_NAME,
            $cookieValue,
            time() + $this->cookieLifetime,
            '/',
            null, // Let browser determine the correct domain
            false, // secure - disabled for maximum compatibility with Ionos hosting
            false, // httpOnly - false so JavaScript can also read it
            false, // raw
            Cookie::SAMESITE_LAX // Most compatible setting
        );
    }

    public function acceptAllCookies(): Cookie
    {
        $preferences = [];
        foreach (array_keys($this->cookieCategories) as $category) {
            $preferences[$category] = true;
        }

        return $this->saveConsentPreferences($preferences);
    }

    public function rejectOptionalCookies(): Cookie
    {
        $preferences = [];
        foreach (array_keys($this->cookieCategories) as $category) {
            $categoryConfig = $this->cookieCategories[$category];
            $preferences[$category] = $categoryConfig['required'] ?? false;
        }

        return $this->saveConsentPreferences($preferences);
    }

    public function getCookieCategories(): array
    {
        return $this->cookieCategories;
    }
}

