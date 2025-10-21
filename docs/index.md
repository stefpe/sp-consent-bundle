SpConsentBundle Documentation
=============================

A Symfony bundle that provides a GDPR-compliant cookie and consent banner component. This bundle helps you manage user cookie preferences in compliance with privacy regulations like GDPR.

## Table of Contents

1. [Installation](#installation)
2. [Basic Usage](#basic-usage)
3. [Using the Live Component](#using-the-live-component)
4. [Configuration Reference](#configuration-reference)
5. [GDPR Compliance and Consent Logging](#gdpr-compliance-and-consent-logging)
6. [Service Reference](#service-reference)
7. [Advanced Usage](#advanced-usage)
8. [Cookie Format](#cookie-format)

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
composer require stefpe/sp-consent-bundle
```

### Step 2: Enable the Bundle

If you're using Symfony Flex (which is recommended), the bundle will be automatically enabled. Otherwise, enable it manually by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php
return [
    // ...
    Stefpe\SpConsentBundle\SpConsentBundle::class => ['all' => true],
];
```

### Step 3: Configuration (Optional)

The bundle comes with sensible defaults, but you can customize it by creating a configuration file:

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    cookie_lifetime: 31536000  # 1 year in seconds
    categories:
        necessary:
            name: 'Notwendige Cookies'
            description: 'Diese Cookies sind für das Funktionieren der Website erforderlich und können nicht deaktiviert werden.'
            required: true
```

See the [Configuration Reference](#configuration-reference) for all available options.

## Basic Usage

### Using the CookieConsentService

The bundle provides a `CookieConsentService` that you can autowire in your controllers:

```php
<?php

namespace App\Controller;

use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsentController extends AbstractController
{
    public function __construct(
        private CookieConsentService $cookieConsentService
    ) {
    }
    
    public function index(Request $request): Response
    {
        // Check if user has given consent
        if (!$this->cookieConsentService->hasConsent($request)) {
            // Show consent banner
        }
        
        // Check if a specific category is allowed
        if ($this->cookieConsentService->isCategoryAllowed($request, 'analytics')) {
            // Load analytics scripts
        }
        
        return $this->render('consent/index.html.twig');
    }
}
```

### Accepting All Cookies

```php
public function acceptAll(Request $request): Response
{
    $cookie = $this->cookieConsentService->acceptAllCookies();
    
    $response = $this->redirectToRoute('homepage');
    $response->headers->setCookie($cookie);
    
    return $response;
}
```

### Rejecting Optional Cookies

```php
public function rejectOptional(Request $request): Response
{
    $cookie = $this->cookieConsentService->rejectOptionalCookies();
    
    $response = $this->redirectToRoute('homepage');
    $response->headers->setCookie($cookie);
    
    return $response;
}
```

### Saving Custom Preferences

```php
public function savePreferences(Request $request): Response
{
    $preferences = [
        'analytics' => true,
        'marketing' => false,
        'functional' => true,
    ];
    
    $cookie = $this->cookieConsentService->saveConsentPreferences($preferences);
    
    $response = $this->redirectToRoute('homepage');
    $response->headers->setCookie($cookie);
    
    return $response;
}
```

## Using the Live Component

The bundle includes a ready-to-use **Symfony UX Live Component** that provides a beautiful, interactive cookie consent banner with zero JavaScript required from your side.

### Quick Start

Simply add the component to your base template (e.g., `base.html.twig`):

```twig
<!DOCTYPE html>
<html>
    <head>
        {# ... your head content ... #}
    </head>
    <body>
        {# Your page content #}
        
        {# Cookie Consent Banner - Add before closing body tag #}
        {{ component('CookieConsentBanner') }}
    </body>
</html>
```

That's it! The component will automatically:
- Show the banner on first visit
- Hide it after user makes a choice
- Remember user preferences
- Provide a settings button to change preferences later

### Features

The Live Component provides:

1. **Simple Banner Mode**
   - "Accept All" button
   - "Only Necessary" button  
   - "Customize Settings" button

2. **Advanced Settings Mode**
   - Toggle each cookie category individually
   - Required categories are disabled (always on)
   - Save custom preferences

3. **Settings Button**
   - Persistent cookie icon button
   - Allows users to reopen settings at any time
   - Appears after consent is given

4. **Real-time Updates**
   - Uses Symfony UX Live Components
   - No page reload needed
   - Instant feedback

### Customization

The component uses **Twig blocks** for maximum flexibility, allowing you to customize specific parts without copying the entire template.

#### Using Twig Blocks

You can override specific blocks by passing them to the component. Here are the available blocks:

**Simple Banner Blocks:**
- `banner_wrapper` - The outer banner container
- `simple_banner` - The entire simple banner
- `simple_banner_title` - The title/heading
- `simple_banner_message` - The description text
- `simple_banner_buttons` - All buttons container
- `button_accept_all` - Accept all button
- `button_reject_optional` - Reject optional button
- `button_customize` - Customize settings button

**Advanced Settings Blocks:**
- `advanced_settings` - The entire advanced view
- `advanced_header` - Header with title and close button
- `advanced_title` - The title
- `advanced_close_button` - Close button
- `categories_list` - The categories container
- `category_item` - Each category row
- `category_header` - Category name/title
- `category_description` - Category description
- `category_toggle` - The toggle switch
- `advanced_footer` - Footer with buttons
- `advanced_buttons` - Buttons container
- `button_save_preferences` - Save button
- `button_accept_all_advanced` - Accept all in advanced view

**Other Blocks:**
- `settings_button` - The persistent cookie icon button
- `settings_button_icon` - Just the icon (🍪)

#### Customization Examples

**Example 1: Change button text**

```twig
{{ component('CookieConsentBanner') }}
    {% block button_accept_all %}
        <button 
            data-action="live#action"
            data-live-action-param="acceptAll"
            class="btn btn-primary"
        >
            I Accept!
        </button>
    {% endblock %}
{% endcomponent %}
```

**Example 2: Customize the banner message**

```twig
{% component 'CookieConsentBanner' %}
    {% block simple_banner_message %}
        <p style="margin: 0; color: #666; font-size: 14px;">
            We use cookies to enhance your browsing experience and analyze our traffic. 
            <a href="/privacy-policy">Learn more</a>
        </p>
    {% endblock %}
{% endcomponent %}
```

**Example 3: Add a custom button**

```twig
{% component 'CookieConsentBanner' %}
    {% block simple_banner_buttons %}
        {{ parent() }}
        <a href="/privacy-policy" class="btn btn-link">Privacy Policy</a>
    {% endblock %}
{% endcomponent %}
```

**Example 4: Completely replace the simple banner**

```twig
{% component 'CookieConsentBanner' %}
    {% block simple_banner %}
        <div class="my-custom-banner">
            <h2>🍪 We Value Your Privacy</h2>
            <p>Choose your cookie preferences:</p>
            {{ parent() }} {# Include original buttons #}
        </div>
    {% endblock %}
{% endcomponent %}
```

**Example 5: Change the settings button icon**

```twig
{{ component('CookieConsentBanner') }}
    {% block settings_button_icon %}⚙️{% endblock %}
{% endcomponent %}
```

#### Full Template Override

If you need complete control, create your own template at:

```
templates/components/CookieConsentBanner.html.twig
```

And extend or completely replace the bundle's template.

#### JavaScript Events

The component fires a custom browser event when consent changes:

```javascript
document.addEventListener('cookieConsent:changed', function(event) {
    const preferences = event.detail.preferences;
    console.log('Cookie preferences changed:', preferences);
    
    // Reload tracking scripts based on new preferences
    if (preferences.analytics) {
        // Load analytics
    }
    if (preferences.marketing) {
        // Load marketing scripts
    }
});
```

### Example: Using with Google Analytics (Consent Mode v2)

This example shows how to integrate with Google Analytics 4 using the modern **Google Consent Mode v2** API. This approach doesn't require page reloads and provides better privacy compliance.

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}My App{% endblock %}</title>
        
        {# Load Google Analytics with Consent Mode #}
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
        <script>
            // Initialize dataLayer
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            
            // Set default consent state (denied by default for GDPR compliance)
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'analytics_storage': 'denied'
            });
            
            // Initialize Google Analytics
            gtag('js', new Date());
            gtag('config', 'G-XXXXXXXXXX');
            
            // Function to update consent based on user preferences
            function applyConsent(analyticsConsent, marketingConsent) {
                gtag('consent', 'update', {
                    'analytics_storage': analyticsConsent ? 'granted' : 'denied',
                    'ad_storage': marketingConsent ? 'granted' : 'denied',
                    'ad_user_data': marketingConsent ? 'granted' : 'denied',
                    'ad_personalization': marketingConsent ? 'granted' : 'denied'
                });
            }
            
            // Apply consent on page load if user already gave consent
            document.addEventListener('DOMContentLoaded', function () {
                {% if app.request.cookies.has('cookie_consent_preferences') %}
                    {% set consent = app.request.cookies.get('cookie_consent_preferences')|json_decode %}
                    applyConsent(
                        {{ consent.analytics ? 'true' : 'false' }},
                        {{ consent.marketing ? 'true' : 'false' }}
                    );
                {% endif %}
            });
            
            // Listen for consent changes (no page reload needed!)
            document.addEventListener('cookieConsent:changed', function(event) {
                const prefs = event.detail.preferences;
                applyConsent(
                    prefs.analytics || false,
                    prefs.marketing || false
                );
            });
        </script>
    </head>
    <body>
        {% block body %}{% endblock %}
        
        {# Cookie Consent Banner #}
        {{ component('CookieConsentBanner') }}
    </body>
</html>
```

**Benefits of this approach:**
- ✅ **No page reload needed** - Consent updates immediately
- ✅ **GDPR compliant** - Consent denied by default
- ✅ **Google Consent Mode v2** - Uses the latest Google standard
- ✅ **Separate analytics & marketing** - Fine-grained control
- ✅ **Privacy-first** - Tracking blocked until explicit consent

### Requirements

The Live Component requires:
- `symfony/ux-live-component` (automatically installed with the bundle)
- `symfony/twig-bundle` (automatically installed with the bundle)

Make sure your application has Symfony UX configured. If not, run:

```bash
composer require symfony/ux-live-component
```

## Configuration Reference

```yaml
sp_consent:
    # Cookie lifetime in seconds
    # Default: 31536000 (1 year)
    cookie_lifetime: 31536000
    
    # Translation settings
    # Enable translations for category names and descriptions
    # Default: false
    use_translations: false
    
    # Translation domain for category translations
    # Default: sp_consent
    translation_domain: sp_consent
    
    # Consent logging settings (for GDPR compliance)
    # Enable logging of consent actions
    # Default: true
    enable_logging: true
    
    # Log level for consent actions
    # Options: debug, info, notice, warning, error
    # Default: info
    log_level: info
    
    # Version of your consent policy (increment when policy changes)
    # Default: 1.0
    consent_version: '1.0'
    
    # Cookie categories configuration
    # You can override defaults or add custom categories
    categories:
        # Each category has a unique key (used in code)
        necessary:
            # Display name (can be literal string or translation key)
            name: 'Notwendige Cookies'
            
            # Description shown to users (can be literal string or translation key)
            description: 'Diese Cookies sind für das Funktionieren der Website erforderlich und können nicht deaktiviert werden.'
            
            # Whether this category is required (cannot be disabled)
            # Default: false
            required: true
        
        analytics:
            name: 'Analyse Cookies'
            description: 'Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren.'
            required: false
        
        marketing:
            name: 'Marketing Cookies'
            description: 'Diese Cookies werden verwendet, um Ihnen relevante Werbung zu zeigen.'
            required: false
        
        functional:
            name: 'Funktionale Cookies'
            description: 'Diese Cookies ermöglichen erweiterte Funktionen und Personalisierung.'
            required: false
        
        # You can add custom categories:
        custom_category:
            name: 'Custom Category Name'
            description: 'Description of what these cookies do'
            required: false
```

### Default Categories

The bundle comes with four predefined categories:

- **necessary** - Essential cookies required for the website to function (always required)
- **analytics** - Cookies for tracking and analyzing user behavior
- **marketing** - Cookies for advertising and marketing purposes
- **functional** - Cookies for enhanced functionality and personalization

### Using Translations

The bundle supports Symfony translations for category names and descriptions, making it easy to provide multilingual cookie consent banners.

#### Enabling Translations

To enable translations, set `use_translations: true` in your configuration:

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    use_translations: true
    categories:
        necessary:
            name: 'cookie.category.necessary.name'
            description: 'cookie.category.necessary.description'
            required: true
```

#### Built-in Translations

The bundle includes translations in the `sp_consent` domain for English and German:

**Supported languages:**
- English (`en`)
- German (`de`)

**Translation keys:**
- `cookie.category.necessary.name` / `cookie.category.necessary.description`
- `cookie.category.analytics.name` / `cookie.category.analytics.description`
- `cookie.category.marketing.name` / `cookie.category.marketing.description`
- `cookie.category.functional.name` / `cookie.category.functional.description`

#### Overriding Translations

You can override the bundle's translations by creating your own translation files:

```yaml
# translations/sp_consent.en.yaml
cookie.category.necessary.name: 'Essential Cookies'
cookie.category.necessary.description: 'Required for the site to work'

cookie.category.analytics.name: 'Analytics & Statistics'
cookie.category.analytics.description: 'Help us improve your experience'
```

#### Custom Translation Domain

If you prefer to use a different translation domain:

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    use_translations: true
    translation_domain: 'my_custom_domain'
    categories:
        necessary:
            name: 'my.cookie.necessary.title'
            description: 'my.cookie.necessary.text'
```

Then create your translations:

```yaml
# translations/my_custom_domain.en.yaml
my.cookie.necessary.title: 'Essential Cookies'
my.cookie.necessary.text: 'These are required'
```

#### Mixed Approach (Literal + Translations)

You can also mix literal strings and translation keys:

```yaml
sp_consent:
    use_translations: true
    categories:
        necessary:
            name: 'cookie.category.necessary.name'  # Will be translated
            description: 'cookie.category.necessary.description'  # Will be translated
        custom:
            name: 'My Custom Category'  # Literal string (if translation key doesn't exist)
            description: 'This is a custom description'  # Literal string
```

**Note:** With `use_translations: false` (default), all strings are treated as literals, regardless of whether they look like translation keys.

#### Complete Example with Translations

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    use_translations: true
    translation_domain: sp_consent
    categories:
        necessary:
            name: 'cookie.category.necessary.name'
            description: 'cookie.category.necessary.description'
            required: true
        analytics:
            name: 'cookie.category.analytics.name'
            description: 'cookie.category.analytics.description'
        marketing:
            name: 'cookie.category.marketing.name'
            description: 'cookie.category.marketing.description'
```

```yaml
# translations/sp_consent.fr.yaml (add French translations)
cookie.category.necessary.name: 'Cookies Nécessaires'
cookie.category.necessary.description: 'Ces cookies sont requis pour le fonctionnement du site.'
cookie.category.analytics.name: 'Cookies Analytiques'
cookie.category.analytics.description: 'Ces cookies nous aident à comprendre comment les visiteurs interagissent avec le site.'
cookie.category.marketing.name: 'Cookies Marketing'
cookie.category.marketing.description: 'Ces cookies sont utilisés pour vous montrer des publicités pertinentes.'
```

### GDPR Compliance and Consent Logging

The bundle includes built-in consent logging to help you comply with GDPR requirements for **proof of consent**. According to GDPR Article 7, you must be able to demonstrate that users have given consent.

#### What Gets Logged

When a user gives, updates, or withdraws consent, the bundle automatically logs:

1. **Timestamp** - When the consent was given (ISO 8601 format)
2. **IP Address** - User's IP address for identification
3. **User Agent** - Browser and device information
4. **Consent Preferences** - Which cookie categories were accepted/rejected
5. **Consent Version** - Version of your consent policy (configurable)
6. **Action Type** - How consent was given:
   - `accept_all` - User clicked "Accept All"
   - `reject_optional` - User clicked "Reject Optional"
   - `custom` - User selected specific categories
7. **Request Context** - Referrer URL and request URI

#### Example Log Entry

```
[2025-10-21 14:32:15] sp_consent.INFO: Cookie consent accept_all: 4 accepted, 0 rejected (version 1.0) {
    "action": "accept_all",
    "preferences": {
        "necessary": true,
        "analytics": true,
        "marketing": true,
        "functional": true,
        "timestamp": 1729516335,
        "version": "1.0"
    },
    "consent_version": "1.0",
    "timestamp": "2025-10-21 14:32:15",
    "ip_address": "192.168.1.1",
    "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)...",
    "referrer": "https://example.com/privacy-policy",
    "request_uri": "/",
    "accepted_categories": ["necessary", "analytics", "marketing", "functional"],
    "rejected_categories": []
}
```

#### Configuring Consent Logging

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    # Enable/disable logging (enabled by default)
    enable_logging: true
    
    # Set log level (debug, info, notice, warning, error)
    log_level: info
    
    # Version your consent policy - increment when you update your privacy policy
    consent_version: '2.0'
```

#### Consent Policy Versioning

When you update your privacy policy or cookie categories, increment the `consent_version`:

```yaml
sp_consent:
    consent_version: '2.0'  # Changed from 1.0
```

This allows you to:
- Track which version of your policy users consented to
- Identify users who need to re-consent to new terms
- Demonstrate compliance for specific policy versions

#### Log Channel and Storage

The bundle logs to the `sp_consent` channel. You can configure Monolog to handle these logs separately:

```yaml
# config/packages/monolog.yaml
monolog:
    channels: ['sp_consent']
    
    handlers:
        # Store consent logs in a dedicated file
        consent:
            type: stream
            path: '%kernel.logs_dir%/consent_%kernel.environment%.log'
            level: info
            channels: ['sp_consent']
            
        # Or send to a centralized logging service for long-term storage
        consent_graylog:
            type: gelf
            publisher:
                hostname: logging.example.com
                port: 12201
            channels: ['sp_consent']
```

#### GDPR Best Practices

1. **Retention Period**: Keep consent logs for at least 3 years (GDPR recommendation)
2. **Log Rotation**: Use Monolog's rotating file handler to manage log file sizes
3. **Access Control**: Restrict access to consent logs to authorized personnel only
4. **Backup**: Regularly backup consent logs to prevent data loss
5. **IP Anonymization**: Consider anonymizing IP addresses for additional privacy:

```yaml
# config/packages/monolog.yaml
services:
    App\Monolog\AnonymizeIpProcessor:
        tags:
            - { name: monolog.processor, channel: sp_consent }
```

#### Disabling Logging

If you have your own consent logging mechanism or don't need logging:

```yaml
sp_consent:
    enable_logging: false
```

**Note**: Even with logging disabled, the timestamp and version are still stored in the cookie for client-side tracking.

#### Viewing Consent Logs

Access logs via:

```bash
# View recent consent logs
tail -f var/log/consent_dev.log

# Search for specific user consents by IP
grep "192.168.1.1" var/log/consent_dev.log

# Filter by action type
grep "accept_all" var/log/consent_dev.log
```

## Service Reference

### CookieConsentService

The main service provided by this bundle.

#### Available Constants

```php
CookieConsentService::COOKIE_NAME = 'cookie_consent_preferences';
```

**Note:** Category names are defined in your bundle configuration and should be referenced as strings (e.g., `'necessary'`, `'analytics'`, `'marketing'`, `'functional'`).

#### Public Methods

##### hasConsent(Request $request): bool

Check if the user has given any consent (i.e., the consent cookie exists).

```php
if ($this->cookieConsentService->hasConsent($request)) {
    // User has made a choice
}
```

##### getConsentPreferences(Request $request): array

Get the user's current consent preferences as an associative array.

```php
$preferences = $this->cookieConsentService->getConsentPreferences($request);
// Returns: ['necessary' => true, 'analytics' => true, 'marketing' => false, ...]
```

##### isCategoryAllowed(Request $request, string $category): bool

Check if a specific category is allowed by the user. Necessary cookies always return `true`.

```php
if ($this->cookieConsentService->isCategoryAllowed($request, 'analytics')) {
    // Load analytics scripts
}
```

##### saveConsentPreferences(array $preferences): Cookie

Save custom consent preferences and return a Cookie object to set in the response.

```php
$preferences = [
    'analytics' => true,
    'marketing' => false,
];
$cookie = $this->cookieConsentService->saveConsentPreferences($preferences);

$response = new Response();
$response->headers->setCookie($cookie);
```

**Note:** The necessary category is automatically set to `true`, and a timestamp is added automatically.

##### acceptAllCookies(): Cookie

Create a cookie that accepts all configured cookie categories.

```php
$cookie = $this->cookieConsentService->acceptAllCookies();
$response->headers->setCookie($cookie);
```

##### rejectOptionalCookies(): Cookie

Create a cookie that only accepts required cookies and rejects all optional ones.

```php
$cookie = $this->cookieConsentService->rejectOptionalCookies();
$response->headers->setCookie($cookie);
```

##### getCookieCategories(): array

Get all available cookie categories with their configuration (name, description, required).

```php
$categories = $this->cookieConsentService->getCookieCategories();
// Returns the configured categories with their metadata
```

This is useful for building a consent form dynamically:

```php
public function consentForm(): Response
{
    $categories = $this->cookieConsentService->getCookieCategories();
    
    return $this->render('consent/form.html.twig', [
        'categories' => $categories,
    ]);
}
```

```twig
{# templates/consent/form.html.twig #}
{% for key, category in categories %}
    <div class="cookie-category">
        <h3>{{ category.name }}</h3>
        <p>{{ category.description }}</p>
        <input type="checkbox" 
               name="consent[{{ key }}]" 
               {% if category.required %}checked disabled{% endif %}>
    </div>
{% endfor %}
```

## Advanced Usage

### Checking Consent in Templates

You can pass the consent status to your templates:

```php
public function index(Request $request): Response
{
    $hasConsent = $this->cookieConsentService->hasConsent($request);
    $preferences = $this->cookieConsentService->getConsentPreferences($request);
    
    return $this->render('page.html.twig', [
        'has_consent' => $hasConsent,
        'consent_preferences' => $preferences,
    ]);
}
```

```twig
{% if not has_consent %}
    {# Show consent banner #}
    <div class="cookie-banner">
        {# ... banner content ... #}
    </div>
{% endif %}

{% if consent_preferences.analytics|default(false) %}
    {# Load analytics scripts #}
    <script src="analytics.js"></script>
{% endif %}
```

### Using in Services

You can also inject the service into your own services:

```php
<?php

namespace App\Service;

use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\RequestStack;

class AnalyticsService
{
    public function __construct(
        private CookieConsentService $cookieConsentService,
        private RequestStack $requestStack
    ) {
    }
    
    public function trackPageView(string $page): void
    {
        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            return;
        }
        
        if (!$this->cookieConsentService->isCategoryAllowed($request, 'analytics')) {
            // Analytics not allowed, skip tracking
            return;
        }
        
        // Track the page view
        // ...
    }
}
```

### Advanced Google Analytics Integration

For more complex scenarios, you can create a helper service to manage analytics:

```php
<?php

namespace App\Service;

use Stefpe\SpConsentBundle\Service\CookieConsentService;
use Symfony\Component\HttpFoundation\RequestStack;

class GoogleAnalyticsHelper
{
    public function __construct(
        private CookieConsentService $cookieConsentService,
        private RequestStack $requestStack,
        private string $measurementId
    ) {
    }
    
    public function getConsentScript(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return '';
        }
        
        $preferences = $this->cookieConsentService->getConsentPreferences($request);
        $analyticsConsent = isset($preferences['analytics']) && $preferences['analytics'];
        $marketingConsent = isset($preferences['marketing']) && $preferences['marketing'];
        
        return sprintf(
            "applyConsent(%s, %s);",
            $analyticsConsent ? 'true' : 'false',
            $marketingConsent ? 'true' : 'false'
        );
    }
    
    public function isEnabled(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return false;
        }
        
        return $this->cookieConsentService->isCategoryAllowed($request, 'analytics');
    }
}
```

Then use it in your templates:

```twig
{# templates/base.html.twig #}
{% set analyticsHelper = app.get('App\\Service\\GoogleAnalyticsHelper') %}

<script async src="https://www.googletagmanager.com/gtag/js?id={{ analyticsHelper.measurementId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    
    gtag('consent', 'default', {
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied'
    });
    
    gtag('js', new Date());
    gtag('config', '{{ analyticsHelper.measurementId }}');
    
    function applyConsent(analytics, marketing) {
        gtag('consent', 'update', {
            'analytics_storage': analytics ? 'granted' : 'denied',
            'ad_storage': marketing ? 'granted' : 'denied',
            'ad_user_data': marketing ? 'granted' : 'denied',
            'ad_personalization': marketing ? 'granted' : 'denied'
        });
    }
    
    // Apply current consent state
    document.addEventListener('DOMContentLoaded', function() {
        {{ analyticsHelper.getConsentScript()|raw }}
    });
    
    // Update on consent changes
    document.addEventListener('cookieConsent:changed', function(event) {
        applyConsent(
            event.detail.preferences.analytics || false,
            event.detail.preferences.marketing || false
        );
    });
</script>
```

### Custom Cookie Categories

You can define custom categories in your configuration:

```yaml
sp_consent:
    categories:
        necessary:
            name: 'Essential'
            description: 'Required for basic functionality'
            required: true
        
        social_media:
            name: 'Social Media'
            description: 'Allows social media sharing and integration'
            required: false
        
        personalization:
            name: 'Personalization'
            description: 'Remembers your preferences and settings'
            required: false
```

Then use them in your code:

```php
if ($this->cookieConsentService->isCategoryAllowed($request, 'social_media')) {
    // Load social media widgets
}
```

### Updating Cookie Lifetime

You can customize the cookie lifetime (in seconds):

```yaml
sp_consent:
    # 6 months
    cookie_lifetime: 15552000
    
    # 2 years
    # cookie_lifetime: 63072000
    
    # 30 days
    # cookie_lifetime: 2592000
```

## Cookie Format

The consent cookie is stored with the name `cookie_consent_preferences` and contains a JSON-encoded object:

```json
{
    "necessary": true,
    "analytics": true,
    "marketing": false,
    "functional": true,
    "timestamp": 1234567890
}
```

### Cookie Attributes

- **Name:** `cookie_consent_preferences` (constant: `CookieConsentService::COOKIE_NAME`)
- **Value:** JSON-encoded preferences object
- **Lifetime:** Configurable (default: 1 year)
- **Path:** `/` (site-wide)
- **Domain:** Auto-detected by browser
- **Secure:** `false` (for maximum compatibility)
- **HttpOnly:** `false` (allows JavaScript access)
- **SameSite:** `Lax`

The cookie is intentionally configured for maximum compatibility with various hosting environments, including shared hosting like Ionos.

## Troubleshooting

### Bundle Not Found

If the bundle is not automatically registered, make sure Symfony Flex is installed and working, or manually register it in `config/bundles.php`.

### Service Not Available

Make sure you've cleared the cache after installing the bundle:

```bash
php bin/console cache:clear
```

### Configuration Not Loading

Verify that your configuration file is in the correct location (`config/packages/sp_consent.yaml`) and uses valid YAML syntax.

Check your configuration with:

```bash
php bin/console debug:config sp_consent
```

### Service Not Autowiring

Verify the service is properly registered:

```bash
php bin/console debug:autowiring CookieConsent
```

You should see `Stefpe\SpConsentBundle\Service\CookieConsentService` in the output.

