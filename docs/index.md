SpConsentBundle Documentation
=============================

A Symfony bundle that provides a GDPR-compliant cookie and consent banner component. This bundle helps you manage user cookie preferences in compliance with privacy regulations like GDPR.

## Table of Contents

1. [Installation & Quick Start](#installation--quick-start)
2. [Configuration](#configuration)
3. [Google Analytics Integration (Consent Mode v2)](#google-analytics-integration-consent-mode-v2)
4. [Styling](#styling)
5. [GDPR Compliance and Consent Logging](#gdpr-compliance-and-consent-logging)

## Installation & Quick Start

### Step 1: Install the Bundle

```bash
composer require stefpe/sp-consent-bundle
```

### Step 2: Enable the Bundle

If you're using Symfony Flex (recommended), the bundle will be automatically enabled. Otherwise, add it to `config/bundles.php`:

```php
// config/bundles.php
return [
    // ...
    Stefpe\SpConsentBundle\SpConsentBundle::class => ['all' => true],
];
```

### Step 3: Add the Banner to Your Template

Add the cookie consent banner to your base template (e.g., `base.html.twig`):

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

### Step 4: Add a Footer Link/Button (Optional)

Add a cookie settings button anywhere on your site (typically in the footer):

```twig
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

That's it! The banner will automatically:
- Show on first visit
- Hide after user makes a choice
- Remember user preferences
- Provide a settings button to change preferences later

## Configuration

Create a configuration file at `config/packages/sp_consent.yaml`:

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    # Cookie lifetime in seconds (default: 1 year)
    cookie_lifetime: 31536000
    
    # Enable consent logging for GDPR compliance (default: true)
    enable_logging: true
    
    # Log level (debug, info, notice, warning, error)
    log_level: info
    
    # Version your consent policy - increment when you update your privacy policy
    consent_version: '1.0'
    
    # Cookie categories
    categories:
        necessary:
            name: 'Necessary Cookies'
            description: 'These cookies are required for the website to function and cannot be disabled.'
            required: true
        
        analytics:
            name: 'Analytics Cookies'
            description: 'These cookies help us understand how visitors interact with the website.'
            required: false
        
        marketing:
            name: 'Marketing Cookies'
            description: 'These cookies are used to show you relevant advertising.'
            required: false
        
        functional:
            name: 'Functional Cookies'
            description: 'These cookies enable enhanced functionality and personalization.'
            required: false
```

### Default Categories

The bundle comes with four predefined categories:
- **necessary** - Essential cookies (always required)
- **analytics** - Tracking and analytics cookies
- **marketing** - Advertising cookies
- **functional** - Enhanced functionality cookies

## Google Analytics Integration (Consent Mode v2)

This example shows how to integrate with Google Analytics 4 using **Google Consent Mode v2** for GDPR compliance. No page reloads required!

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
                {% set consent = consent_preferences() %}
                {% if consent %}
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

**Benefits:**
- ✅ **No page reload needed** - Consent updates immediately
- ✅ **GDPR compliant** - Consent denied by default
- ✅ **Google Consent Mode v2** - Uses the latest Google standard
- ✅ **Separate analytics & marketing** - Fine-grained control
- ✅ **Privacy-first** - Tracking blocked until explicit consent

## Styling

The components use **Tailwind CSS classes by default** for a modern, clean look. If you don't use Tailwind CSS in your project, you can easily override the styling.

### Option 1: Using with Tailwind CSS (Recommended)

Install the SymfonyCasts Tailwind Bundle:

```bash
composer require symfonycasts/tailwind-bundle
```

Build Tailwind CSS:

```bash
# Development (watch mode)
php bin/console tailwind:build --watch

# Production
php bin/console tailwind:build --minify
```

Customize the banner with Tailwind classes:

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

Cookie Settings Button:

```twig
{{ component('CookieSettingsButton', {
    label: 'Cookie Settings',
    class: 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors'
}) }}
```

### Option 2: Using Custom CSS

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

/* Buttons */
.cookie-consent-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.cookie-btn {
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.cookie-btn-accept {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.cookie-btn-accept:hover {
    background: #059669;
    transform: translateY(-2px);
}

.cookie-btn-reject {
    background: #6b7280;
    color: white;
}

.cookie-btn-reject:hover {
    background: #4b5563;
}

.cookie-btn-customize {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cookie-btn-customize:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
    .cookie-consent-container {
        flex-direction: column;
    }
    
    .cookie-consent-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .cookie-btn {
        width: 100%;
    }
}
```

Import in `assets/app.js`:

```javascript
import './styles/cookie-consent.css';
```

Apply to component:

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

For quick prototyping:

```twig
{{ component('CookieConsentBanner', {
    'banner:style': 'position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(to right, #6366f1, #8b5cf6); padding: 24px; z-index: 9999;',
    'title:style': 'color: white; font-size: 20px; font-weight: bold; margin-bottom: 12px;',
    'message:style': 'color: rgba(255,255,255,0.9); font-size: 15px;',
    'button:accept_all:style': 'padding: 14px 28px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;'
}) }}
```

### Available Customization Points

**Simple Banner:**
- `banner` - Main wrapper
- `simple_banner:container` - Content container
- `simple_banner:content` - Text content area
- `simple_banner:buttons_container` - Buttons wrapper
- `title` - Banner title
- `message` - Banner message
- `button:accept_all` - Accept all button
- `button:reject_optional` - Reject optional button
- `button:customize` - Customize button

**Advanced Settings:**
- `advanced_settings` - Advanced view container
- `advanced_header` - Advanced header
- `advanced_title` - Advanced title
- `advanced_close_button` - Close button
- `categories_list` - Categories list container
- `category_item` - Individual category item
- `category_header` - Category title
- `category_description` - Category description
- `advanced_footer` - Advanced footer
- `button:save_preferences` - Save preferences button
- `button:accept_all_advanced` - Accept all button (advanced)

## GDPR Compliance and Consent Logging

The bundle includes built-in consent logging to help you comply with GDPR requirements for **proof of consent**. According to GDPR Article 7, you must be able to demonstrate that users have given consent.

### What Gets Logged

When a user gives, updates, or withdraws consent, the bundle automatically logs:

1. **Timestamp** - When the consent was given (ISO 8601 format)
2. **IP Address** - Automatically anonymized using Symfony's `IpUtils::anonymize()` (IPv4: last octet removed, IPv6: last 80 bits removed)
3. **User Agent** - Browser and device information
4. **Consent Preferences** - Which cookie categories were accepted/rejected
5. **Consent Version** - Version of your consent policy (configurable)
6. **Action Type** - How consent was given: `accept_all`, `reject_optional`, or `custom`
7. **Request Context** - Referrer URL and request URI

### Example Log Entry

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
    "ip_address": "192.168.1.0",  // Anonymized
    "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)...",
    "referrer": "https://example.com/privacy-policy",
    "request_uri": "/",
    "accepted_categories": ["necessary", "analytics", "marketing", "functional"],
    "rejected_categories": []
}
```

### Configuring Consent Logging

```yaml
# config/packages/sp_consent.yaml
sp_consent:
    # Enable/disable logging (enabled by default)
    enable_logging: true
    
    # Set log level (debug, info, notice, warning, error)
    log_level: info
    
    # Version your consent policy - increment when you update your privacy policy
    consent_version: '1.0'
```

### Consent Policy Versioning

When you update your privacy policy or cookie categories, increment the `consent_version`:

```yaml
sp_consent:
    consent_version: '2.0'  # Changed from 1.0
```

This allows you to:
- Track which version of your policy users consented to
- Identify users who need to re-consent to new terms
- Demonstrate compliance for specific policy versions

### Log Storage Configuration

Configure Monolog to store consent logs separately:

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
        
        # Optional: Rotate logs to manage file sizes
        consent_rotating:
            type: rotating_file
            path: '%kernel.logs_dir%/consent_%kernel.environment%.log'
            level: info
            channels: ['sp_consent']
            max_files: 30  # Keep 30 days of logs
```

### GDPR Best Practices

1. **Retention Period**: Keep consent logs for at least 3 years (GDPR recommendation)
2. **Log Rotation**: Use Monolog's rotating file handler to manage log file sizes
3. **Access Control**: Restrict access to consent logs to authorized personnel only
4. **Backup**: Regularly backup consent logs to prevent data loss
5. **IP Anonymization**: IP addresses are automatically anonymized for enhanced privacy

### Viewing Consent Logs

Access logs via terminal:

```bash
# View recent consent logs
tail -f var/log/consent_dev.log

# Search for specific action types
grep "accept_all" var/log/consent_dev.log

# Filter by date
grep "2025-10-21" var/log/consent_dev.log
```

### Disabling Logging

If you have your own consent logging mechanism:

```yaml
sp_consent:
    enable_logging: false
```

**Note**: Even with logging disabled, the timestamp and version are still stored in the cookie for client-side tracking.

---

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/stefpe/sp-consent-bundle).

## License

This bundle is released under the MIT License. See the [LICENSE](../LICENSE) file for details.
