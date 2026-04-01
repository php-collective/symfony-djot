# Configuration

## Default Configuration

The bundle works out of the box with sensible defaults. Safe mode is enabled by default for security.

## Full Configuration Reference

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    converters:
        default:
            safe_mode: true            # XSS protection (enabled by default)
            significant_newlines: false # Treat line breaks as significant
            soft_break_mode: null       # How to render soft breaks: newline, space, br
            xhtml: false               # Output XHTML-compatible markup

        # Add custom profiles as needed
        trusted:
            safe_mode: false           # Disable for trusted content

    cache:
        enabled: false          # Enable output caching
        pool: cache.app         # Symfony cache pool to use
```

## Converter Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `safe_mode` | bool | `true` | XSS protection - disable only for trusted content |
| `significant_newlines` | bool | `false` | Allow blocks to interrupt paragraphs without blank lines (markdown-like behavior) |
| `soft_break_mode` | string | `null` | How to render soft breaks: `newline`, `space`, or `br` |
| `xhtml` | bool | `false` | Output XHTML-compatible markup (self-closing tags) |

## Converter Profiles

You can define multiple converter profiles for different contexts. Each profile is registered as a separate service.

### Example: Default Safe + Trusted Converter

```yaml
symfony_djot:
    converters:
        # Default is safe (safe_mode: true is the default)
        default: ~

        # For trusted admin/editor content
        trusted:
            safe_mode: false

        # For documentation with extensions
        docs:
            safe_mode: false
            extensions:
                - table_of_contents
                - heading_permalinks
```

### Using Profiles in Twig

```twig
{# Uses 'default' profile (safe mode) #}
{{ comment.text|djot }}

{# Uses 'trusted' profile (no safe mode) #}
{{ article.body|djot('trusted') }}

{# Quick way for trusted content without config #}
{{ article.body|djot_raw }}
```

### Using Profiles in Services

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ContentService
{
    public function __construct(
        // Default converter (safe mode enabled)
        private DjotConverterInterface $djot,

        // Trusted converter (safe mode disabled)
        #[Autowire(service: 'symfony_djot.converter.trusted')]
        private DjotConverterInterface $trusted,
    ) {}

    public function renderComment(string $text): string
    {
        return $this->djot->toHtml($text);
    }

    public function renderArticle(string $text): string
    {
        return $this->trusted->toHtml($text);
    }
}
```

## Service IDs

Each converter profile is registered with a predictable service ID:

| Profile | Service ID |
|---------|------------|
| `default` | `symfony_djot.converter.default` |
| `trusted` | `symfony_djot.converter.trusted` |
| `docs` | `symfony_djot.converter.docs` |

The `default` profile is also aliased to:
- `symfony_djot.converter`
- `PhpCollective\SymfonyDjot\Service\DjotConverterInterface`

## Next Steps

- [Safe Mode](safe-mode.md) - understand XSS protection
- [Caching](caching.md) - improve performance with caching
