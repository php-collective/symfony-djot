# Getting Started

Djot markup language integration for Symfony applications.

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane, the author of CommonMark and Pandoc. It builds on Markdown's foundation while addressing its complexity issues:

- **Cleaner syntax** — more consistent rules, fewer edge cases
- **More features** — footnotes, definition lists, task lists, math, highlights, and more
- **Easier to parse** — simpler specification, faster implementations
- **Better extensibility** — designed for customization

## Features

This bundle provides:

- **Twig integration** — `|djot` filter and `djot()` function
- **Service injection** — `DjotConverterInterface` for use in controllers and services
- **Form type** — `DjotType` for form fields
- **Validation** — `ValidDjot` constraint for input validation
- **Multiple profiles** — different configurations for different contexts (e.g., user content vs. admin content)
- **Safe mode** — XSS protection for untrusted input
- **Caching** — optional caching of rendered output via Symfony cache pools
- **Plain text** — extract plain text for search indexing or previews

## Quick Start

```bash
composer require php-collective/symfony-djot
```

```twig
{# In your templates #}
{{ article.body|djot }}
```

```php
// In your services
public function __construct(
    private DjotConverterInterface $djot,
) {}

public function render(string $content): string
{
    return $this->djot->toHtml($content);
}
```

## Requirements

- PHP 8.2 or higher
- Symfony 6.4, 7.x, or 8.x

## Demo Application

See the [Symfony Djot Demo](https://github.com/php-collective/symfony-djot-demo) for a complete example application.
