SpConsentBundle Documentation
=============================

A Symfony bundle that provides a GDPR-compliant cookie and consent banner component. This bundle helps you manage user cookie preferences in compliance with privacy regulations like GDPR.

## Table of Contents

1. [Installation](#installation)
2. [Styling](#styling)
3. [Basic Usage](#basic-usage)
4. [Using the Live Component](#using-the-live-component)
5. [Configuration Reference](#configuration-reference)
6. [GDPR Compliance and Consent Logging](#gdpr-compliance-and-consent-logging)
7. [Service Reference](#service-reference)
8. [Twig Functions](#twig-functions)
9. [Advanced Usage](#advanced-usage)
10. [Cookie Format](#cookie-format)

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

The bundle automatically registers:
- All necessary services
- Twig template paths (`@SpConsentBundle` namespace)
- Event listeners
- Twig components (CookieConsentBanner, CookieSettingsButton)
- Twig functions (consent_preferences(), has_consent(), has_consent_for())

**Styling:** The components use **Tailwind CSS classes by default** for a modern, clean look. If you don't use Tailwind CSS in your project, you can easily override the styling with custom classes or inline styles using nested attributes (see [Advanced Usage](#advanced-usage)).

No additional configuration is required for basic usage.

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

## Styling

The components come with **minimal default classes** (e.g., `cookie-consent-banner`, `cookie-consent-simple`) that you style however you want. This gives you complete flexibility to use Tailwind CSS, custom CSS, Bootstrap, or any other styling approach.

### Default Classes

The bundle provides these default class names for identification and styling:

- `.cookie-consent-banner` - Main banner wrapper
- `.cookie-consent-simple` - Simple banner view
- `.cookie-consent-advanced` - Advanced settings view
- `.cookie-settings-button` - Settings button

**All elements are unstyled by default.** You need to provide styling through one of the methods below.

### Option 1: Using with Tailwind CSS (Recommended)

The easiest way to style the components is with the [SymfonyCasts Tailwind Bundle](https://github.com/SymfonyCasts/tailwind-bundle), which provides delightful Tailwind support for Symfony with AssetMapper (no Node.js required!).

#### Install Tailwind Bundle

```bash
composer require symfonycasts/tailwind-bundle
```

The bundle will automatically:
- Download the standalone Tailwind CSS binary
- Create a `tailwind.config.js` file
- Set up the build process
- Watch for changes in development

#### Build Tailwind CSS

```bash
# Development (watch mode)
php bin/console tailwind:build --watch

# Production
php bin/console tailwind:build --minify
```

#### Apply Tailwind Classes to Components

Pass Tailwind classes via nested attributes:

```twig
{{ component('CookieConsentBanner', {
    'banner:class': 'fixed bottom-0 left-0 right-0 bg-white shadow-lg p-5 z-[9999]',
    'simple_banner:container:class': 'max-w-screen-xl mx-auto flex items-center gap-5 flex-wrap',
    'simple_banner:content:class': 'flex-1 min-w-[300px]',
    'title:class': 'mb-2.5 text-lg font-semibold',
    'message:class': 'text-gray-600 text-sm leading-relaxed',
    'simple_banner:buttons_container:class': 'flex gap-2.5 flex-wrap',
    'button:accept_all:class': 'px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors',
    'button:reject_optional:class': 'px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors',
    'button:customize:class': 'px-6 py-3 bg-transparent text-blue-600 border-2 border-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition-all'
}) }}
```

**Advanced Settings with Tailwind:**

```twig
{{ component('CookieConsentBanner', {
    'banner:class': 'fixed bottom-0 left-0 right-0 bg-white shadow-lg p-5 z-[9999]',
    'advanced_settings:class': 'max-w-3xl mx-auto',
    'advanced_header:class': 'flex justify-between items-center mb-5',
    'advanced_title:class': 'text-xl font-semibold',
    'advanced_close_button:class': 'text-2xl text-gray-600 hover:text-gray-900 cursor-pointer',
    'categories_list:class': 'max-h-96 overflow-y-auto mb-5',
    'category_item:class': 'border border-gray-300 rounded-lg p-4 mb-3 bg-gray-50',
    'category_item:content:class': 'flex justify-between items-start gap-4',
    'category_header:class': 'text-base font-semibold mb-2',
    'category_description:class': 'text-sm text-gray-600',
    'advanced_footer:class': 'flex gap-2.5 justify-end border-t border-gray-300 pt-4',
    'button:save_preferences:class': 'px-8 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700',
    'button:accept_all_advanced:class': 'px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700'
}) }}
```

**Cookie Settings Button with Tailwind:**

```twig
{{ component('CookieSettingsButton', {
    label: 'Cookie Settings',
    class: 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors'
}) }}
```

### Option 2: Using Custom CSS

If you prefer traditional CSS or use a different framework (Bootstrap, Bulma, etc.), you can override the default classes.

#### Create Your Stylesheet

Create `assets/styles/cookie-consent.css`:

```css
/* Base banner styles */
.cookie-consent-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Container */
.cookie-consent-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}

/* Content area */
.cookie-consent-content {
    flex: 1;
    min-width: 300px;
}

.cookie-consent-title {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 700;
    color: white;
}

.cookie-consent-message {
    margin: 0;
    color: rgba(255, 255, 255, 0.95);
    font-size: 15px;
    line-height: 1.6;
}

/* Buttons container */
.cookie-consent-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Button base styles */
.cookie-btn {
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: none;
}

/* Accept button */
.cookie-btn-accept {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.cookie-btn-accept:hover {
    background: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

/* Reject button */
.cookie-btn-reject {
    background: #6b7280;
    color: white;
}

.cookie-btn-reject:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

/* Customize button */
.cookie-btn-customize {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cookie-btn-customize:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

/* Advanced settings */
.cookie-consent-advanced {
    max-width: 800px;
    margin: 0 auto;
}

.cookie-category-item {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.cookie-category-header {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.cookie-category-description {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

/* Responsive design */
@media (max-width: 768px) {
    .cookie-consent-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cookie-consent-buttons {
        flex-direction: column;
    }
    
    .cookie-btn {
        width: 100%;
    }
}
```

#### Import Your Stylesheet

In your `assets/app.js` or main CSS file:

```javascript
import './styles/cookie-consent.css';
```

#### Apply Custom Classes to the Component

```twig
{{ component('CookieConsentBanner', {
    'banner:class': 'cookie-consent-banner',
    'simple_banner:container:class': 'cookie-consent-container',
    'simple_banner:content:class': 'cookie-consent-content',
    'title:class': 'cookie-consent-title',
    'message:class': 'cookie-consent-message',
    'simple_banner:buttons_container:class': 'cookie-consent-buttons',
    'button:accept_all:class': 'cookie-btn cookie-btn-accept',
    'button:reject_optional:class': 'cookie-btn cookie-btn-reject',
    'button:customize:class': 'cookie-btn cookie-btn-customize'
}) }}
```

### Option 3: Using Inline Styles

For simple use cases or quick prototyping, you can use inline styles:

```twig
{{ component('CookieConsentBanner', {
    'banner:style': 'position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(to right, #6366f1, #8b5cf6); padding: 24px; z-index: 9999;',
    'title:style': 'color: white; font-size: 20px; font-weight: bold; margin-bottom: 12px;',
    'message:style': 'color: rgba(255,255,255,0.9); font-size: 15px;',
    'button:accept_all:style': 'padding: 14px 28px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;'
}) }}
```

### Available Customization Points

All these elements can be customized using nested attributes with `:class` or `:style` suffixes:

**Simple Banner View:**
- `banner` - Main wrapper
- `simple_banner` - Simple view container
- `simple_banner:container` - Content container
- `simple_banner:content` - Text content area
- `simple_banner:buttons_container` - Buttons wrapper
- `title` - Banner title
- `message` - Banner message
- `button:accept_all` - Accept all button
- `button:reject_optional` - Reject optional button
- `button:customize` - Customize button

**Advanced Settings View:**
- `advanced_settings` - Advanced view container
- `advanced_settings:container` - Advanced content wrapper
- `advanced_header` - Advanced header
- `advanced_title` - Advanced title
- `advanced_close_button` - Close button
- `categories_list` - Categories list container
- `category_item` - Individual category item
- `category_item:content` - Category content wrapper
- `category_item:info` - Category info section
- `category_header` - Category title
- `category_description` - Category description
- `category_toggle:label` - Toggle switch label
- `advanced_footer` - Advanced footer
- `button:save_preferences` - Save preferences button
- `button:accept_all_advanced` - Accept all button (advanced)

### Using Block Overrides

For more complex customization, you can override entire template blocks:

```twig
{% component 'CookieConsentBanner' %}
    {% block simple_banner_title %}
        <h3 class="custom-title">
            🍪 We Value Your Privacy
        </h3>
    {% endblock %}
    
    {% block simple_banner_message %}
        <p class="custom-message">
            We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
            <a href="{{ path('privacy') }}" class="text-white underline">Learn more</a>
        </p>
    {% endblock %}
    
    {% block button_accept_all %}
        <button data-action="live#action"
                data-live-action-param="acceptAll"
                class="custom-accept-button">
            I Accept 🎉
        </button>
    {% endblock %}
{% endcomponent %}
```

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

### Cookie Settings Button Component

The bundle also provides a separate `CookieSettingsButton` component that you can use anywhere in your application to allow users to reopen the cookie settings. This is useful for adding cookie settings links in footers, navigation menus, or privacy pages.

#### Basic Usage

```twig
{# In your footer, navigation, or anywhere else #}
{{ component('CookieSettingsButton') }}
```

This will render a button with the default label "Cookie-Einstellungen" that triggers the cookie settings dialog when clicked.

#### Customization

You can customize the button label and styling:

```twig
{# Custom label #}
{{ component('CookieSettingsButton', {
    label: 'Manage Cookies'
}) }}

{# With custom CSS classes #}
{{ component('CookieSettingsButton', {
    label: 'Cookie Preferences',
    class: 'btn btn-link text-muted'
}) }}

{# In a footer menu #}
<footer>
    <nav>
        <a href="/privacy">Privacy Policy</a>
        {{ component('CookieSettingsButton', {
            label: 'Cookie Settings',
            class: 'footer-link'
        }) }}
        <a href="/imprint">Imprint</a>
    </nav>
</footer>
```

#### Using Attributes

The component supports the standard Twig Component attributes API:

```twig
{# Add custom attributes #}
{% component 'CookieSettingsButton' with {
    label: 'Cookies',
    class: 'nav-link'
} %}
    {% block attributes %}
        id="cookie-settings-btn"
        data-tracking="footer-cookie-link"
        aria-label="Open cookie settings"
    {% endblock %}
{% endcomponent %}
```

#### How It Works

The button uses JavaScript to trigger the Live Component action:

```javascript
document.querySelector('[data-live-name-value=CookieConsentBanner]').__component.action('reopenSettings')
```

This means:
- ✅ No page reload needed
- ✅ Works from anywhere on the page
- ✅ Opens the advanced settings view directly
- ✅ Fully accessible

#### Example: Footer with Cookie Settings

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>
<body>
    {% block body %}{% endblock %}
    
    <footer style="background: #f8f9fa; padding: 20px; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p>&copy; 2025 Your Company. All rights reserved.</p>
            </div>
            <nav style="display: flex; gap: 20px;">
                <a href="/privacy">Privacy Policy</a>
                <a href="/terms">Terms of Service</a>
                {{ component('CookieSettingsButton', {
                    label: 'Cookie Settings',
                    class: 'text-primary hover:text-primary-dark transition-colors cursor-pointer'
                }) }}
                <a href="/contact">Contact</a>
            </nav>
        </div>
    </footer>
    
    {# Cookie Consent Banner #}
    {{ component('CookieConsentBanner') }}
</body>
</html>
```

**Benefits:**
- ✅ **Reusable** - Use the button anywhere without duplicating code
- ✅ **Consistent** - Same behavior everywhere
- ✅ **Accessible** - Proper button semantics
- ✅ **Customizable** - Easy to style for your design
- ✅ **GDPR Friendly** - Easy access to cookie settings as required by law

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
2. **IP Address** - User's IP address for identification (automatically anonymized using Symfony's `IpUtils::anonymize()`)
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
    "timestamp": "2025-10-21 14:32:15",
    "ip_address": "192.168.1.0",  // Anonymized (last octet removed)
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
5. **IP Anonymization**: IP addresses are automatically anonymized in all consent logs using Symfony's `IpUtils::anonymize()` for enhanced privacy (IPv4: last octet removed, IPv6: last 80 bits removed)

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

# Search for specific user consents by anonymized IP
grep "192.168.1.0" var/log/consent_dev.log  # Note: IPs are anonymized

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

## Twig Functions

The bundle provides convenient Twig functions to access consent information directly in your templates without needing to pass variables from controllers.

### Available Functions

#### `consent_preferences()`

Returns the user's current consent preferences as an associative array.

```twig
{% set consent = consent_preferences() %}

{# Check if analytics is enabled #}
{% if consent.analytics %}
    <script src="analytics.js"></script>
{% endif %}

{# Display all preferences #}
<ul>
    {% for category, value in consent %}
        {% if category != 'timestamp' %}
            <li>{{ category }}: {{ value ? 'Enabled' : 'Disabled' }}</li>
        {% endif %}
    {% endfor %}
</ul>

{# Access specific categories #}
<p>Analytics: {{ consent.analytics ? 'Yes' : 'No' }}</p>
<p>Marketing: {{ consent.marketing ? 'Yes' : 'No' }}</p>
```

#### `has_consent()`

Checks if the user has given any consent (i.e., made a choice).

```twig
{% if has_consent() %}
    <p>Thank you for your cookie preferences!</p>
{% else %}
    <p>Please review our cookie policy.</p>
{% endif %}

{# Conditionally load scripts #}
{% if has_consent() %}
    {% set prefs = consent_preferences() %}
    {% if prefs.analytics %}
        <script src="analytics.js"></script>
    {% endif %}
{% endif %}
```

#### `has_consent_for(category)`

Checks if the user has given consent for a specific category. This is the most convenient way to conditionally load scripts.

```twig
{# Load analytics only if user consented #}
{% if has_consent_for('analytics') %}
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXXXXX');
    </script>
{% endif %}

{# Load marketing pixels #}
{% if has_consent_for('marketing') %}
    <script>
        // Facebook Pixel, Google Ads, etc.
    </script>
{% endif %}

{# Load functional features #}
{% if has_consent_for('functional') %}
    <script src="chat-widget.js"></script>
{% endif %}
```

### Complete Example

Here's a complete example showing how to use the Twig functions:

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}My Website{% endblock %}</title>
    
    {# Always load essential scripts (no consent needed) #}
    <script src="essential.js"></script>
    
    {# Conditionally load analytics #}
    {% if has_consent_for('analytics') %}
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-XXXXXXXXXX');
        </script>
    {% endif %}
    
    {# Conditionally load marketing scripts #}
    {% if has_consent_for('marketing') %}
        <!-- Meta Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'YOUR_PIXEL_ID');
            fbq('track', 'PageView');
        </script>
    {% endif %}
</head>
<body>
    {% block body %}{% endblock %}
    
    {# Display consent status in footer #}
    <footer>
        {% if has_consent() %}
            {% set consent = consent_preferences() %}
            <p>
                Your cookie preferences: 
                Analytics: {{ consent.analytics ? '✓' : '✗' }},
                Marketing: {{ consent.marketing ? '✓' : '✗' }}
                <a href="#" onclick="document.querySelector('[data-controller=live]').dispatchEvent(new Event('live#action', {detail: {action: 'reopenSettings'}}))">Change</a>
            </p>
        {% else %}
            <p>You haven't set your cookie preferences yet.</p>
        {% endif %}
    </footer>
    
    {# Cookie Consent Banner #}
    {{ component('CookieConsentBanner') }}
</body>
</html>
```

### Using with Google Consent Mode v2

Combine the Twig functions with Google Consent Mode for optimal privacy compliance:

```twig
<script>
    // Initialize consent as denied
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    
    gtag('consent', 'default', {
        'ad_storage': 'denied',
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'analytics_storage': 'denied'
    });
    
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXXXX');
    
    // Apply consent if already given
    {% if has_consent() %}
        gtag('consent', 'update', {
            'analytics_storage': {{ has_consent_for('analytics') ? "'granted'" : "'denied'" }},
            'ad_storage': {{ has_consent_for('marketing') ? "'granted'" : "'denied'" }},
            'ad_user_data': {{ has_consent_for('marketing') ? "'granted'" : "'denied'" }},
            'ad_personalization': {{ has_consent_for('marketing') ? "'granted'" : "'denied'" }}
        });
    {% endif %}
    
    // Update consent when user makes a choice
    document.addEventListener('cookieConsent:changed', function(event) {
        const prefs = event.detail.preferences;
        gtag('consent', 'update', {
            'analytics_storage': prefs.analytics ? 'granted' : 'denied',
            'ad_storage': prefs.marketing ? 'granted' : 'denied',
            'ad_user_data': prefs.marketing ? 'granted' : 'denied',
            'ad_personalization': prefs.marketing ? 'granted' : 'denied'
        });
    });
</script>
```

### Benefits of Using Twig Functions

1. **Cleaner Templates** - No need to pass consent data from every controller
2. **Less Code** - Direct access to consent information
3. **Type Safe** - Functions return properly typed data
4. **Consistent** - Same API across all templates
5. **Convenient** - Easy to check specific categories with `has_consent_for()`

### Migration from Cookie Access

If you were previously accessing the cookie directly, you can easily migrate:

**Before:**
```twig
{% if app.request.cookies.has('cookie_consent_preferences') %}
    {% set consent = app.request.cookies.get('cookie_consent_preferences')|json_decode %}
    {% if consent.analytics %}
        {# Load analytics #}
    {% endif %}
{% endif %}
```

**After:**
```twig
{% if has_consent_for('analytics') %}
    {# Load analytics #}
{% endif %}
```

Much cleaner and more readable! 🎉

## Advanced Usage

### Customizing Component Styling

The CookieConsentBanner and CookieSettingsButton components use **Tailwind CSS classes by default**. This provides a clean, modern look out of the box while remaining fully customizable.

#### Using with Tailwind CSS

If your project uses Tailwind CSS, the components will work perfectly without any additional configuration. The default classes include:

- Responsive layout with flexbox
- Modern button styles with hover states
- Proper spacing and typography
- Clean color scheme using Tailwind's default palette

#### Customizing with Tailwind Classes

You can override any element's classes using nested attributes:

```twig
{{ component('CookieConsentBanner', {
    'banner:class': 'fixed bottom-0 inset-x-0 bg-gradient-to-r from-purple-600 to-blue-600 p-8 z-50',
    'button:accept_all:class': 'px-8 py-4 bg-green-500 hover:bg-green-600 text-white rounded-full font-bold shadow-lg',
    'title:class': 'text-2xl font-bold text-white mb-3',
    'message:class': 'text-white text-base'
}) }}
```

#### Using Without Tailwind CSS

If you don't use Tailwind CSS, you can override the default classes with your own CSS classes or use inline styles:

**Option 1: Custom CSS Classes**

```twig
{{ component('CookieConsentBanner', {
    'banner:class': 'my-custom-banner',
    'button:accept_all:class': 'btn btn-primary',
    'button:reject_optional:class': 'btn btn-secondary'
}) }}
```

Then define your styles in CSS:

```css
.my-custom-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 20px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 9999;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.btn-primary {
    background: #0d6efd;
    color: white;
}

.btn-primary:hover {
    background: #0b5ed7;
}
```

**Option 2: Inline Styles**

```twig
{{ component('CookieConsentBanner', {
    'banner:style': 'position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 20px; z-index: 9999;',
    'button:accept_all:style': 'padding: 12px 24px; background: #0d6efd; color: white; border: none; border-radius: 6px; cursor: pointer;'
}) }}
```

#### Available Nested Attribute Keys

You can customize these elements using nested attributes with either `:class` or `:style` suffixes:

- `banner` - The main banner wrapper
- `title` - The banner title
- `message` - The banner message text
- `button:accept_all` - "Accept All" button
- `button:reject_optional` - "Only necessary" button
- `button:customize` - "Customize settings" button
- `button:save_preferences` - "Save preferences" button (advanced view)
- `button:accept_all_advanced` - "Accept All" button (advanced view)
- `advanced_settings` - Advanced settings container
- `advanced_header` - Advanced settings header
- `advanced_footer` - Advanced settings footer
- `categories_list` - Cookie categories list container
- `category_item` - Individual category items

#### Responsive Design

When using Tailwind CSS, you can easily add responsive classes:

```twig
{{ component('CookieConsentBanner', {
    'simple_banner:container:class': 'max-w-screen-xl mx-auto flex flex-col md:flex-row items-center gap-5',
    'simple_banner:buttons_container:class': 'flex flex-col sm:flex-row gap-2.5 w-full md:w-auto'
}) }}
```

### Checking Consent in Templates

You can also pass the consent status to your templates from controllers if needed:

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

