# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of SpConsentBundle
- CookieConsentService for managing cookie consent preferences
- **CookieConsentBanner Live Component** - Beautiful, interactive UI component
- Configurable cookie categories (necessary, analytics, marketing, functional)
- Configurable cookie lifetime
- Support for custom cookie categories
- **Translation support** - Symfony translation integration for category names and descriptions
  - Built-in translations for English and German
  - Optional `use_translations` configuration
  - Configurable translation domain
  - Translation caching for performance
- **GDPR Consent Logging** - Comprehensive logging system for proof of consent
  - Automatic logging of all consent actions (accept_all, reject_optional, custom)
  - Logs timestamp, IP address, user agent, referrer, and request URI
  - Configurable consent policy versioning for tracking policy updates
  - Configurable log levels (debug, info, notice, warning, error)
  - Dedicated `sp_consent` Monolog channel for log separation
  - Optional - can be disabled if custom logging is needed
  - Logs stored with full context for GDPR Article 7 compliance
- GDPR-compliant cookie management
- Response listener for automatic cookie setting
- Comprehensive test suite (25 tests including translation and logging tests)
- Comprehensive documentation with Live Component, translation, and GDPR compliance examples
- JavaScript event system for consent changes (`cookieConsent:changed`)

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

## [1.0.0] - YYYY-MM-DD

### Added
- Initial stable release

[Unreleased]: https://github.com/stefpe/sp-consent-bundle/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/stefpe/sp-consent-bundle/releases/tag/v1.0.0

