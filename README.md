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
{# Safe by default - XSS protection enabled #}
{{ article.body|djot }}

{# For trusted content only - no XSS protection #}
{{ trustedContent|djot_raw }}

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
        # Default has safe_mode: true (XSS protection enabled)
        default: ~

        # For trusted content (admin, CMS)
        trusted:
            safe_mode: false
    cache:
        enabled: false
        pool: cache.app
```

### Multiple Converter Profiles

Use different configurations for different contexts:

```twig
{# Default is safe #}
{{ comment.body|djot }}

{# Use named converter for trusted content #}
{{ article.body|djot('trusted') }}

{# Or use djot_raw for quick trusted rendering #}
{{ article.body|djot_raw }}
```

```php
public function __construct(
    // Default converter (safe mode enabled)
    private DjotConverterInterface $djot,

    // Trusted converter (safe mode disabled)
    #[Autowire(service: 'symfony_djot.converter.trusted')]
    private DjotConverterInterface $trusted,
) {}
```

### Safe Mode

Safe mode is *enabled by default* for XSS protection. Disable only for trusted content:

```yaml
symfony_djot:
    converters:
        trusted:
            safe_mode: false
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
- `admonition` - Admonition blocks (note, tip, warning, danger, etc.)
- `autolink` - Auto-convert URLs to clickable links
- `code_group` - Transform code-group divs into tabbed interfaces
- `default_attributes` - Add default attributes to elements by type
- `external_links` - Configure external link behavior (target, rel)
- `frontmatter` - Parse YAML/TOML/JSON frontmatter blocks
- `heading_level_shift` - Shift heading levels up/down
- `heading_permalinks` - Add anchor links to headings
- `heading_reference` - Link to headings with `[text](#heading)` syntax
- `inline_footnotes` - Convert spans with class to inline footnotes
- `mentions` - Convert @username to profile links
- `mermaid` - Render Mermaid diagram code blocks
- `semantic_span` - Convert spans to `<kbd>`, `<dfn>`, `<abbr>` elements
- `smart_quotes` - Convert straight quotes to typographic quotes
- `table_of_contents` - Generate TOC from headings
- `tabs` - Tabbed content blocks (CSS or ARIA mode)
- `wikilinks` - Support `[[Page Name]]` wiki-style links

See [Extensions documentation](https://php-collective.github.io/symfony-djot/extensions/) for detailed configuration options.

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

Full documentation: **[php-collective.github.io/symfony-djot](https://php-collective.github.io/symfony-djot/)**

- [Installation](https://php-collective.github.io/symfony-djot/guide/installation)
- [Configuration](https://php-collective.github.io/symfony-djot/guide/configuration)
- [Twig Usage](https://php-collective.github.io/symfony-djot/guide/twig-usage)
- [Service Usage](https://php-collective.github.io/symfony-djot/guide/service-usage)
- [Forms](https://php-collective.github.io/symfony-djot/guide/forms)
- [Validation](https://php-collective.github.io/symfony-djot/guide/validation)
- [Safe Mode](https://php-collective.github.io/symfony-djot/guide/safe-mode)
- [Extensions](https://php-collective.github.io/symfony-djot/extensions/)
- [Caching](https://php-collective.github.io/symfony-djot/guide/caching)
- [Djot Syntax](https://php-collective.github.io/symfony-djot/guide/djot-syntax)

## Demo Application

See the [symfony-djot-demo](https://github.com/php-collective/symfony-djot-demo) for a complete example application.

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane (author of CommonMark/Pandoc). It offers cleaner syntax and more features than Markdown while being easier to parse.

Learn more about Djot syntax at [djot.net](https://djot.net).
