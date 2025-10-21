Contributing to SpConsentBundle
================================

First of all, thank you for contributing to this bundle!

Here are some guidelines to help you get started.

## Reporting Issues

If you find a bug or have a feature request, please create an issue on GitHub:

1. Check if the issue already exists
2. Provide a clear title and description
3. Include steps to reproduce (for bugs)
4. Specify your environment (Symfony version, PHP version, etc.)

## Submitting Pull Requests

1. Fork the repository
2. Create a new branch for your feature or bugfix:
   ```bash
   git checkout -b feature/my-new-feature
   ```
3. Make your changes
4. Add tests for your changes
5. Ensure all tests pass:
   ```bash
   vendor/bin/phpunit
   ```
6. Follow the coding standards (PSR-12)
7. Commit your changes with clear commit messages
8. Push to your fork and submit a pull request

## Coding Standards

This project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard and the [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).

You can check your code using PHP CS Fixer:

```bash
vendor/bin/php-cs-fixer fix
```

## Running Tests

Make sure all tests pass before submitting a pull request:

```bash
vendor/bin/phpunit
```

Add tests for any new features or bug fixes you implement.

## Documentation

If you're adding a new feature, please update the documentation in the `docs/` directory.

## License

By contributing to this bundle, you agree that your contributions will be licensed under the MIT License.

