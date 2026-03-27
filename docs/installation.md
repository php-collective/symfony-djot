# Installation

## Requirements

- PHP 8.2 or higher
- Symfony 6.4 or 7.x

## Install via Composer

```bash
composer require php-collective/symfony-djot
```

## Enable the Bundle

If you're using Symfony Flex, the bundle is automatically enabled. Otherwise, add it to your `config/bundles.php`:

```php
return [
    // ...
    PhpCollective\SymfonyDjot\SymfonyDjotBundle::class => ['all' => true],
];
```

## Verify Installation

Create a simple test in any Twig template:

```twig
{{ '*Hello* _world_!'|djot }}
```

This should render:

```html
<p><strong>Hello</strong> <em>world</em>!</p>
```

## Next Steps

- [Configuration](configuration.md) — customize converter behavior
- [Twig Usage](twig-usage.md) — learn the available filters and functions
- [Djot Syntax](djot-syntax.md) — quick reference for Djot markup
