SpConsentBundle
===============

A Symfony bundle that provides a GDPR-compliant cookie and consent banner component.

## Features

- 🎨 **Unstyled by Default** - Bring your own styling (Tailwind, Bootstrap, custom CSS)
- 🔧 **Fully Customizable** - Every element can be styled with classes or inline styles
- ⚡ **Live Components** - Interactive banner with Symfony UX Live Components
- 🌍 **Translation Ready** - Multi-language support built-in
- 🍪 **Cookie Categories** - Support for necessary, analytics, marketing, and functional cookies
- 📊 **GDPR Compliant** - Built with privacy regulations in mind
- 🎯 **Easy Integration** - Simple Twig components, no complex setup

## Quick Start

```bash
composer require stefpe/sp-consent-bundle
```

Add the component to your base template:

```twig
{{ component('CookieConsentBanner') }}
```

**Important:** The components come with minimal default classes only. You need to style them yourself using Tailwind CSS, custom CSS, or any CSS framework. See the documentation for styling examples.

Resources
---------

  * [Documentation](docs/index.md)
  * [Contributing](CONTRIBUTING.md)
  * [Report issues](https://github.com/stefpe/sp-consent-bundle/issues) and
    [send Pull Requests](https://github.com/stefpe/sp-consent-bundle/pulls)
    in the [main repository](https://github.com/stefpe/sp-consent-bundle)

License
-------

This bundle is released under the MIT license. See the included [LICENSE](LICENSE) file for more information.
