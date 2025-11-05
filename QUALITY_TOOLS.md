# Quality Tools Configuration

This document describes the quality tools configured for the Transmissor project and how to use them.

## Available Tools

### 1. PHP CS Fixer

**Purpose**: Automatically fix code style issues according to PSR-12 and custom rules.

**Configuration**: `.php-cs-fixer.php`

**Usage**:
```bash
# Check code style issues (dry-run)
composer format-dry

# Fix code style issues
composer format
```

**Manual usage**:
```bash
# Check only
vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code
vendor/bin/php-cs-fixer fix
```

**GitHub Actions**: Runs automatically on push to main branches and PRs. On main branches, it will auto-commit fixes.

### 2. PHPStan

**Purpose**: Static analysis to find bugs without running code.

**Configuration**: `phpstan.neon`

**Level**: 5 (configurable from 0-9, where 9 is strictest)

**Usage**:
```bash
# Run PHPStan analysis
composer phpstan

# Or manually
vendor/bin/phpstan analyse
```

**Features**:
- Laravel-aware analysis via Larastan
- Detects type errors, undefined variables, unreachable code
- Custom rules for Laravel patterns

**GitHub Actions**: Runs on all PHP file changes

### 3. Psalm

**Purpose**: Advanced static analysis with type checking.

**Configuration**: `psalm.xml`

**Error Level**: 7 (less strict, suitable for legacy code)

**Usage**:
```bash
# Run Psalm analysis
composer psalm

# Or manually
vendor/bin/psalm
```

**Features**:
- Laravel plugin included
- Type inference and validation
- Dead code detection

**GitHub Actions**: Runs on all PHP file changes

### 4. PHPUnit

**Purpose**: Unit and integration testing.

**Configuration**: `phpunit.xml`

**Usage**:
```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage
```

**Manual usage**:
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Unit/NotificationServiceTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html build/coverage
```

**GitHub Actions**: Runs on all pushes and PRs against multiple PHP and Laravel versions

## Running All Quality Checks

Run all quality checks at once:

```bash
composer check
```

This will run:
1. PHP CS Fixer (dry-run)
2. PHPStan analysis
3. Psalm analysis
4. PHPUnit tests

## Composer Scripts Summary

| Script | Command | Description |
|--------|---------|-------------|
| `test` | `vendor/bin/phpunit` | Run PHPUnit tests |
| `test-coverage` | `vendor/bin/phpunit --coverage-html build/coverage` | Run tests with HTML coverage report |
| `format` | `vendor/bin/php-cs-fixer fix` | Fix code style issues |
| `format-dry` | `vendor/bin/php-cs-fixer fix --dry-run --diff` | Check code style without fixing |
| `phpstan` | `vendor/bin/phpstan analyse` | Run PHPStan static analysis |
| `psalm` | `vendor/bin/psalm` | Run Psalm static analysis |
| `analyse` | Runs both `phpstan` and `psalm` | Run all static analysis tools |
| `check` | Runs all quality checks | Run format-dry, analyse, and test |

## GitHub Actions Workflows

### 1. Tests Workflow (`.github/workflows/run-tests.yml`)

- **Trigger**: Push/PR to master, main, stable, develop
- **Matrix**: PHP 7.4, 8.0, 8.1, 8.2 × Laravel 8, 9, 10
- **Runs**: PHPUnit test suite

### 2. PHPStan Workflow (`.github/workflows/phpstan.yml`)

- **Trigger**: Push/PR to main branches (PHP files only)
- **PHP Version**: 8.2
- **Runs**: PHPStan static analysis

### 3. Psalm Workflow (`.github/workflows/psalm.yml`)

- **Trigger**: Push/PR to main branches (PHP files only)
- **PHP Version**: 8.2
- **Runs**: Psalm static analysis

### 4. PHP CS Fixer Workflow (`.github/workflows/php-cs-fixer.yml`)

- **Trigger**: Push/PR to main branches (PHP files only)
- **PHP Version**: 8.2
- **Behavior**:
  - On PRs: Checks code style (fails if issues found)
  - On main branches: Fixes and auto-commits style issues

## Best Practices

### Before Committing

Always run quality checks before committing:

```bash
composer check
```

### During Development

1. **Run tests frequently**: `composer test`
2. **Fix style issues**: `composer format`
3. **Check static analysis**: `composer analyse`

### Fixing Static Analysis Issues

#### PHPStan Issues

If PHPStan reports false positives, you can:
1. Fix the actual issue (preferred)
2. Add proper PHPDoc type hints
3. Add ignore rules to `phpstan.neon`

Example:
```neon
parameters:
    ignoreErrors:
        - '#Call to an undefined method App\\Models\\User::customMethod\(\)#'
```

#### Psalm Issues

For Psalm false positives:
1. Add proper type hints
2. Use `@psalm-suppress` annotations
3. Configure baseline: `vendor/bin/psalm --set-baseline=psalm-baseline.xml`

### Adjusting Tool Strictness

#### PHPStan
Edit `phpstan.neon` and change the level (0-9):
```neon
parameters:
    level: 6  # Increase for stricter checks
```

#### Psalm
Edit `psalm.xml` and change errorLevel (1-8):
```xml
<psalm errorLevel="6">  <!-- Lower number = stricter -->
```

## Continuous Integration

All tools run automatically on:
- Every push to main branches
- Every pull request
- Changes to relevant configuration files

PRs must pass all checks before merging.

## Troubleshooting

### Cache Issues

Clear tool caches if you encounter strange behavior:

```bash
# Clear all caches
rm -rf .php-cs-fixer.cache .phpstan.cache .psalm-cache .phpunit.cache

# Or clear individually
vendor/bin/php-cs-fixer clear-cache
vendor/bin/phpstan clear-result-cache
vendor/bin/psalm --clear-cache
```

### Dependencies

Ensure all dev dependencies are installed:

```bash
composer install --dev
```

### Memory Issues

If PHPStan or Psalm run out of memory:

```bash
php -d memory_limit=2G vendor/bin/phpstan analyse
php -d memory_limit=2G vendor/bin/psalm
```

## Updating Tools

Keep quality tools up to date:

```bash
composer update --dev friendsofphp/php-cs-fixer
composer update --dev phpstan/phpstan nunomaduro/larastan
composer update --dev vimeo/psalm
composer update --dev phpunit/phpunit
```

## Resources

- [PHP CS Fixer Documentation](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Larastan Documentation](https://github.com/nunomaduro/larastan)
- [Psalm Documentation](https://psalm.dev/docs/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
