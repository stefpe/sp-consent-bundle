<?php

namespace Stefpe\SpConsentBundle\Service;

use Psr\Log\LoggerInterface;
use Stefpe\SpConsentBundle\Enum\ConsentAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\Translation\TranslatorInterface;

class CookieConsentService
{
    public const COOKIE_NAME = 'cookie_consent_preferences';

    private ?array $translatedCategories = null;

    public function __construct(
        private readonly array $cookieCategories,
        private readonly int $cookieLifetime,
        private readonly TranslatorInterface $translator,
        private readonly string $translationDomain,
        private readonly bool $useTranslations,
        private readonly ?LoggerInterface $logger = null,
        private readonly bool $enableLogging = true,
        private readonly string $logLevel = 'info',
        private readonly string $consentVersion = '1.0'
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

    public function saveConsentPreferences(array $preferences, ?Request $request = null, ConsentAction $action = ConsentAction::CUSTOM): Cookie
    {
        // Ensure all required categories are always enabled
        foreach ($this->cookieCategories as $category => $config) {
            if ($config['required'] ?? false) {
                $preferences[$category] = true;
            }
        }

        // Add timestamp
        $preferences['timestamp'] = time();
        $preferences['version'] = $this->consentVersion;

        // Log the consent action for GDPR compliance
        $this->logConsentAction($action, $preferences, $request);

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

    public function acceptAllCookies(?Request $request = null): Cookie
    {
        $preferences = [];
        foreach (array_keys($this->cookieCategories) as $category) {
            $preferences[$category] = true;
        }

        return $this->saveConsentPreferences($preferences, $request, ConsentAction::ACCEPT_ALL);
    }

    public function rejectOptionalCookies(?Request $request = null): Cookie
    {
        $preferences = [];
        foreach (array_keys($this->cookieCategories) as $category) {
            $categoryConfig = $this->cookieCategories[$category];
            $preferences[$category] = $categoryConfig['required'] ?? false;
        }

        return $this->saveConsentPreferences($preferences, $request, ConsentAction::REJECT_OPTIONAL);
    }

    public function getCookieCategories(): array
    {
        if (!$this->useTranslations) {
            return $this->cookieCategories;
        }

        // Cache translated categories to avoid re-translating on each call
        if ($this->translatedCategories === null) {
            $this->translatedCategories = [];
            foreach ($this->cookieCategories as $key => $category) {
                $this->translatedCategories[$key] = [
                    'name' => $this->translator->trans($category['name'], [], $this->translationDomain),
                    'description' => $this->translator->trans($category['description'], [], $this->translationDomain),
                    'required' => $category['required'] ?? false,
                ];
            }
        }

        return $this->translatedCategories;
    }

    /**
     * Logs consent action for GDPR compliance.
     * 
     * According to GDPR requirements, consent logs should include:
     * - Timestamp: When the consent was given
     * - IP Address: User identification (anonymized by default)
     * - User Agent: Browser/device information
     * - Consent Preferences: Which categories were accepted/rejected
     * - Consent Version: Version of the consent policy
     * - Action Type: How consent was given (accept_all, reject_optional, custom)
     * 
     * This information serves as proof of consent and demonstrates GDPR compliance.
     */
    private function logConsentAction(ConsentAction $action, array $preferences, ?Request $request): void
    {
        if (!$this->enableLogging || !$this->logger) {
            return;
        }

        $context = [
            'action' => $action->value,
            'preferences' => $preferences,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        // Add request context if available (GDPR proof of consent requirements)
        if ($request) {
            $ipAddress = $request->getClientIp();
            $context['ip_address'] = $this->anonymizeIpAddress($ipAddress);
            $context['user_agent'] = $request->headers->get('User-Agent');
            $context['referrer'] = $request->headers->get('Referer');
            $context['request_uri'] = $request->getRequestUri();
        }

        // Prepare categories summary for easier log reading
        $acceptedCategories = [];
        $rejectedCategories = [];
        foreach ($preferences as $category => $value) {
            if (in_array($category, ['timestamp', 'version'])) {
                continue;
            }
            if ($value === true) {
                $acceptedCategories[] = $category;
            } else {
                $rejectedCategories[] = $category;
            }
        }

        $context['accepted_categories'] = $acceptedCategories;
        $context['rejected_categories'] = $rejectedCategories;

        $message = sprintf(
            'Cookie consent %s: %d accepted, %d rejected (version %s)',
            $action->value,
            count($acceptedCategories),
            count($rejectedCategories),
            $preferences['version'] ?? 'unknown'
        );

        // Log at configured level
        match ($this->logLevel) {
            'debug' => $this->logger->debug($message, $context),
            'notice' => $this->logger->notice($message, $context),
            'warning' => $this->logger->warning($message, $context),
            'error' => $this->logger->error($message, $context),
            default => $this->logger->info($message, $context),
        };
    }

    /**
     * Anonymizes an IP address for GDPR compliance and enhanced privacy.
     * 
     * IP addresses are always anonymized to protect user privacy while still
     * allowing for geographic tracking and fraud prevention.
     * 
     * IPv4: Removes the last octet (e.g., 192.168.1.100 → 192.168.1.0)
     * IPv6: Removes the last 80 bits (e.g., 2001:0db8::1 → 2001:0db8::)
     * 
     * This provides a balance between user identification and privacy.
     */
    private function anonymizeIpAddress(?string $ipAddress): ?string
    {
        if ($ipAddress === null) {
            return null;
        }

        // Check if it's an IPv6 address
        if (str_contains($ipAddress, ':')) {
            // IPv6: Keep first 48 bits (3 groups), remove last 80 bits
            $parts = explode(':', $ipAddress);
            // Keep first 3 groups (48 bits)
            $anonymized = array_slice($parts, 0, 3);
            return implode(':', $anonymized) . '::';
        }

        // IPv4: Remove last octet
        $parts = explode('.', $ipAddress);
        if (count($parts) === 4) {
            $parts[3] = '0';
            return implode('.', $parts);
        }

        return $ipAddress;
    }
}

