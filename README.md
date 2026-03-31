# Symfony Djot Bundle

[![CI](https://github.com/php-collective/symfony-djot/actions/workflows/ci.yml/badge.svg)](https://github.com/php-collective/symfony-djot/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/packagist/php-v/php-collective/symfony-djot)](https://packagist.org/packages/php-collective/symfony-djot)
[![License](https://img.shields.io/packagist/l/php-collective/symfony-djot)](LICENSE)

[Djot](https://github.com/php-collective/djot-php) markup language integration for Symfony — Twig filters, services, forms, and validation.

## Installation

```bash
composer require php-collective/symfony-djot
```

## Usage

### Twig Filter

```twig
{# Filter syntax #}
{{ article.body|djot }}

{# Function syntax for inline strings #}
{{ djot('_emphasis_ and *strong*') }}

{# Plain text output #}
{{ article.body|djot_text }}
```

### Service

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;

class ArticleController
{
    public function show(DjotConverterInterface $djot): Response
    {
        $html = $djot->toHtml($article->body);
        $text = $djot->toText($article->body);
    }
}
```

## Configuration

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    converters:
        default:
            safe_mode: false
        user_content:
            safe_mode: true
    cache:
        enabled: false
        pool: cache.app
```

### Multiple Converter Profiles

Use different configurations for different contexts:

```twig
{{ comment.body|djot('user_content') }}
```

```php
public function __construct(
    #[Autowire(service: 'symfony_djot.converter.user_content')]
    private DjotConverterInterface $safeConverter,
) {}
```

### Safe Mode

Enable safe mode when processing untrusted user input for XSS protection:

```yaml
symfony_djot:
    converters:
        user_content:
            safe_mode: true
```

### Extensions

Enable [djot-php extensions](https://github.com/php-collective/djot-php) per converter:

```yaml
symfony_djot:
    converters:
        default:
            extensions:
                - type: autolink
                - type: smart_quotes
                - type: heading_permalinks
                  symbol: '#'
                  position: after
        with_mentions:
            extensions:
                - type: mentions
                  user_url_template: 'https://github.com/{username}'
                - type: table_of_contents
```

Available extensions:
- `autolink` - Auto-convert URLs to clickable links
- `code_group` - Transform code-group divs into tabbed interfaces
- `default_attributes` - Add default attributes to elements by type
- `external_links` - Configure external link behavior (target, rel)
- `frontmatter` - Parse YAML/TOML/JSON frontmatter blocks
- `heading_permalinks` - Add anchor links to headings
- `mentions` - Convert @username to profile links
- `semantic_span` - Convert spans to `<kbd>`, `<dfn>`, `<abbr>` elements
- `smart_quotes` - Convert straight quotes to typographic quotes
- `table_of_contents` - Generate TOC from headings
- `wikilinks` - Support `[[Page Name]]` wiki-style links

See [Extensions documentation](docs/extensions.md) for detailed configuration options.

### Form Type

Use the `DjotType` for form fields that accept Djot markup:

```php
use PhpCollective\SymfonyDjot\Form\Type\DjotType;

$builder->add('body', DjotType::class);
```

### Validation

Validate that a field contains valid Djot markup:

```php
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;

class Article
{
    #[ValidDjot]
    private string $body;
}
```

## Documentation

See the [docs/](docs/) folder for detailed documentation:

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Twig Usage](docs/twig-usage.md)
- [Service Usage](docs/service-usage.md)
- [Forms](docs/forms.md)
- [Validation](docs/validation.md)
- [Safe Mode](docs/safe-mode.md)
- [Extensions](docs/extensions.md)
- [Caching](docs/caching.md)
- [Djot Syntax](docs/djot-syntax.md)

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane (author of CommonMark/Pandoc). It offers cleaner syntax and more features than Markdown while being easier to parse.

Learn more about Djot syntax at [djot.net](https://djot.net).
