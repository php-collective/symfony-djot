# Symfony Djot Bundle

Djot markup language integration for Symfony — Twig filters, services, forms, and validation.

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

## What is Djot?

[Djot](https://djot.net) is a modern light markup language created by John MacFarlane (author of CommonMark/Pandoc). It offers cleaner syntax and more features than Markdown while being easier to parse.

Learn more about Djot syntax at [djot.net](https://djot.net).

