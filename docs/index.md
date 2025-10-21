SpConsentBundle Documentation
=============================

A Symfony bundle that provides a GDPR-compliant cookie and consent banner component. This bundle helps you manage user cookie preferences in compliance with privacy regulations like GDPR.

## Table of Contents

1. [Installation](#installation)
2. [Basic Usage](#basic-usage)
3. [Using the Live Component](#using-the-live-component)
4. [Configuration Reference](#configuration-reference)
5. [Service Reference](#service-reference)
6. [Advanced Usage](#advanced-usage)
7. [Cookie Format](#cookie-format)

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

### Example: Using with Analytics

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}My App{% endblock %}</title>
        
        {# Only load analytics if user consented #}
        {% if app.request.cookies.has('cookie_consent_preferences') %}
            {% set consent = app.request.cookies.get('cookie_consent_preferences')|json_decode %}
            {% if consent.analytics %}
                <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', 'GA_MEASUREMENT_ID');
                </script>
            {% endif %}
        {% endif %}
    </head>
    <body>
        {% block body %}{% endblock %}
        
        {# Cookie Consent Banner #}
        {{ component('CookieConsentBanner') }}
        
        {# Listen for consent changes and reload if needed #}
        <script>
            document.addEventListener('cookieConsent:changed', function(event) {
                // Reload page to apply new tracking scripts
                if (event.detail.preferences.analytics !== undefined) {
                    window.location.reload();
                }
            });
        </script>
    </body>
</html>
```

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
    
    # Cookie categories configuration
    # You can override defaults or add custom categories
    categories:
        # Each category has a unique key (used in code)
        necessary:
            # Display name for the category
            name: 'Notwendige Cookies'
            
            # Description shown to users
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

