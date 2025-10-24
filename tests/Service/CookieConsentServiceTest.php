<?php

namespace Stefpe\SpConsentBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class CookieConsentServiceTest extends TestCase
{
    private CookieConsentService $service;
    private array $categories;
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->categories = [
            'necessary' => [
                'name' => 'Necessary Cookies',
                'description' => 'Required for basic functionality',
                'required' => true,
            ],
            'analytics' => [
                'name' => 'Analytics Cookies',
                'description' => 'Help us understand user behavior',
                'required' => false,
            ],
            'marketing' => [
                'name' => 'Marketing Cookies',
                'description' => 'Used for advertising',
                'required' => false,
            ],
        ];

        // Create mock translator
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('trans')
            ->willReturnCallback(fn($id) => $id);

        // Create mock logger
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new CookieConsentService(
            cookieCategories: $this->categories,
            cookieLifetime: 3600,
            translator: $this->translator,
            translationDomain: 'sp_consent',
            useTranslations: false,
            logger: $this->logger,
            enableLogging: true,
            logLevel: 'info',
            consentVersion: '1.0'
        );
    }

    public function testHasConsentReturnsFalseWhenNoCookie(): void
    {
        $request = new Request();
        $this->assertFalse($this->service->hasConsent($request));
    }

    public function testHasConsentReturnsTrueWhenCookieExists(): void
    {
        $request = new Request();
        $request->cookies->set(CookieConsentService::COOKIE_NAME, json_encode(['necessary' => true]));
        
        $this->assertTrue($this->service->hasConsent($request));
    }

    public function testGetConsentPreferencesReturnsEmptyArrayWhenNoCookie(): void
    {
        $request = new Request();
        $preferences = $this->service->getConsentPreferences($request);
        
        $this->assertIsArray($preferences);
        $this->assertEmpty($preferences);
    }

    public function testGetConsentPreferencesReturnsCorrectData(): void
    {
        $expectedPreferences = ['necessary' => true, 'analytics' => false];
        $request = new Request();
        $request->cookies->set(
            CookieConsentService::COOKIE_NAME,
            json_encode($expectedPreferences)
        );
        
        $preferences = $this->service->getConsentPreferences($request);
        
        $this->assertEquals($expectedPreferences, $preferences);
    }

    public function testIsCategoryAllowedReturnsTrueForRequiredCategories(): void
    {
        $request = new Request();
        // Even with no cookie, required categories should be allowed
        $this->assertTrue($this->service->isCategoryAllowed($request, 'necessary'));
    }

    public function testIsCategoryAllowedReturnsFalseForOptionalCategoriesWithoutConsent(): void
    {
        $request = new Request();
        $this->assertFalse($this->service->isCategoryAllowed($request, 'analytics'));
    }

    public function testIsCategoryAllowedReturnsTrueWhenCategoryIsAccepted(): void
    {
        $request = new Request();
        $request->cookies->set(
            CookieConsentService::COOKIE_NAME,
            json_encode(['analytics' => true])
        );
        
        $this->assertTrue($this->service->isCategoryAllowed($request, 'analytics'));
    }

    public function testSaveConsentPreferencesEnforcesRequiredCategories(): void
    {
        // Try to save preferences without required category
        $preferences = [
            'analytics' => true,
            'marketing' => false,
        ];
        
        $cookie = $this->service->saveConsentPreferences($preferences);
        
        $this->assertInstanceOf(Cookie::class, $cookie);
        
        // Decode the cookie value
        $cookieValue = json_decode($cookie->getValue(), true);
        
        // Required category should be automatically set to true
        $this->assertTrue($cookieValue['necessary']);
        $this->assertTrue($cookieValue['analytics']);
        $this->assertFalse($cookieValue['marketing']);
        $this->assertArrayHasKey('timestamp', $cookieValue);
    }

    public function testSaveConsentPreferencesCannotDisableRequiredCategories(): void
    {
        // Try to explicitly disable required category
        $preferences = [
            'necessary' => false,  // Try to disable required category
            'analytics' => true,
        ];
        
        $cookie = $this->service->saveConsentPreferences($preferences);
        $cookieValue = json_decode($cookie->getValue(), true);
        
        // Required category should still be true
        $this->assertTrue($cookieValue['necessary']);
    }

    public function testAcceptAllCookiesAcceptsAllCategories(): void
    {
        $cookie = $this->service->acceptAllCookies();
        $cookieValue = json_decode($cookie->getValue(), true);
        
        $this->assertTrue($cookieValue['necessary']);
        $this->assertTrue($cookieValue['analytics']);
        $this->assertTrue($cookieValue['marketing']);
        $this->assertArrayHasKey('timestamp', $cookieValue);
    }

    public function testRejectOptionalCookiesKeepsOnlyRequired(): void
    {
        $cookie = $this->service->rejectOptionalCookies();
        $cookieValue = json_decode($cookie->getValue(), true);
        
        $this->assertTrue($cookieValue['necessary']);
        $this->assertFalse($cookieValue['analytics']);
        $this->assertFalse($cookieValue['marketing']);
        $this->assertArrayHasKey('timestamp', $cookieValue);
    }

    public function testGetCookieCategoriesReturnsConfiguration(): void
    {
        $categories = $this->service->getCookieCategories();
        
        $this->assertEquals($this->categories, $categories);
        $this->assertArrayHasKey('necessary', $categories);
        $this->assertArrayHasKey('analytics', $categories);
        $this->assertTrue($categories['necessary']['required']);
        $this->assertFalse($categories['analytics']['required']);
    }

    public function testCookieHasCorrectAttributes(): void
    {
        $cookie = $this->service->acceptAllCookies();
        
        $this->assertEquals(CookieConsentService::COOKIE_NAME, $cookie->getName());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals('lax', $cookie->getSameSite());
        $this->assertFalse($cookie->isSecure());
        $this->assertFalse($cookie->isHttpOnly());
    }
}

