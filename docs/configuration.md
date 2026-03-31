# Configuration

## Default Configuration

The bundle works out of the box with sensible defaults. No configuration is required for basic usage.

## Full Configuration Reference

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    converters:
        default:
            safe_mode: false           # Enable XSS protection
            significant_newlines: false # Treat line breaks as significant
            soft_break_mode: null       # How to render soft breaks: newline, space, br
            xhtml: false               # Output XHTML-compatible markup

        # Add custom profiles as needed
        user_content:
            safe_mode: true

    cache:
        enabled: false          # Enable output caching
        pool: cache.app         # Symfony cache pool to use
```

## Converter Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `safe_mode` | bool | `false` | Enable XSS protection for untrusted input |
| `significant_newlines` | bool | `false` | Allow blocks to interrupt paragraphs without blank lines (markdown-like behavior) |
| `soft_break_mode` | string | `null` | How to render soft breaks: `newline`, `space`, or `br` |
| `xhtml` | bool | `false` | Output XHTML-compatible markup (self-closing tags) |

## Converter Profiles

You can define multiple converter profiles for different contexts. Each profile is registered as a separate service.

### Example: Trusted vs. Untrusted Content

```yaml
symfony_djot:
    converters:
        # For admin/editor content (trusted)
        default:
            safe_mode: false

        # For user comments/input (untrusted)
        user_content:
            safe_mode: true

        # For email content
        email:
            safe_mode: true
```

### Using Profiles in Twig

```twig
{# Uses 'default' profile #}
{{ article.body|djot }}

{# Uses 'user_content' profile #}
{{ comment.text|djot('user_content') }}
```

### Using Profiles in Services

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CommentService
{
    public function __construct(
        #[Autowire(service: 'symfony_djot.converter.user_content')]
        private DjotConverterInterface $djot,
    ) {}

    public function renderComment(string $text): string
    {
        return $this->djot->toHtml($text);
    }
}
```

## Service IDs

Each converter profile is registered with a predictable service ID:

| Profile | Service ID |
|---------|------------|
| `default` | `symfony_djot.converter.default` |
| `user_content` | `symfony_djot.converter.user_content` |
| `email` | `symfony_djot.converter.email` |

The `default` profile is also aliased to:
- `symfony_djot.converter`
- `PhpCollective\SymfonyDjot\Service\DjotConverterInterface`

## Next Steps

- [Safe Mode](safe-mode.md) — understand XSS protection
- [Caching](caching.md) — improve performance with caching
