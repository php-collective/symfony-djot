# Safe Mode

Safe mode is *enabled by default* to provide XSS protection. This protects against malicious content in user comments, forum posts, or any input from external sources.

## Default Behavior

The `|djot` filter uses safe mode by default. For trusted content (admin/CMS), use `|djot_raw` or a named converter with `safe_mode: false`.

```twig
{# Safe by default - use for any content #}
{{ content|djot }}

{# Explicit raw - only for trusted content you control #}
{{ article.body|djot_raw }}
```

## When to Disable Safe Mode

| Content Source | Filter to Use |
|---------------|---------------|
| User comments | `|djot` (default) |
| Forum posts | `|djot` (default) |
| External API data | `|djot` (default) |
| User profile descriptions | `|djot` (default) |
| Admin/editor content | `|djot_raw` or named converter |
| CMS content (trusted editors) | `|djot_raw` or named converter |

**Rule of thumb:** Only use `|djot_raw` when you fully control and trust the content or sanitized the possible HTML content using e.g. HTMLPurifier.

## What Safe Mode Does

When enabled (the default), safe mode:

1. **Sanitizes URLs** - blocks `javascript:`, `data:`, and other dangerous protocols
2. **Removes raw HTML** - strips any embedded HTML/scripts
3. **Validates links** - ensures URLs are safe

### Example: Dangerous Link

Input:
```djot
[Click me](javascript:alert('XSS'))
```

With `|djot` (safe mode, default):
```html
<p><a href="">Click me</a></p>
```

With `|djot_raw` (no safe mode):
```html
<p><a href="javascript:alert('XSS')">Click me</a></p>
```

### Example: Raw HTML

Input:
```djot
`<script>alert('XSS')</script>`{=html}
```

With `|djot` (safe mode): Raw HTML blocks are stripped.

With `|djot_raw` (no safe mode): Script is rendered.

## Using Named Converters

For more control, define named converter profiles:

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    converters:
        # Default is already safe (safe_mode: true)
        default: ~

        # For trusted CMS content
        trusted:
            safe_mode: false

        # With extensions for documentation
        docs:
            safe_mode: false
            extensions:
                - table_of_contents
                - heading_permalinks
```

```twig
{# Uses default safe converter #}
{{ comment.text|djot }}

{# Uses trusted converter #}
{{ article.body|djot('trusted') }}

{# Uses docs converter with extensions #}
{{ documentation.content|djot('docs') }}
```

### In Services

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ContentRenderer
{
    public function __construct(
        // Default converter (safe mode)
        private DjotConverterInterface $default,

        // Trusted converter (no safe mode)
        #[Autowire(service: 'symfony_djot.converter.trusted')]
        private DjotConverterInterface $trusted,
    ) {}

    public function renderComment(Comment $comment): string
    {
        // User-generated content - use safe default
        return $this->default->toHtml($comment->getText());
    }

    public function renderArticle(Article $article): string
    {
        // Trusted content from editors
        return $this->trusted->toHtml($article->getBody());
    }
}
```

## Security Recommendations

1. **Use the default** - `|djot` is safe by default, use it everywhere
2. **Explicit trust** - only use `|djot_raw` for content you control
3. **Validate before storing** - safe mode helps at render time, but validate input too
4. **Review trusted content** - even "trusted" content should be reviewed

## More Information

For advanced safe mode options (custom blocked schemes, strict mode), see the [php-collective/djot safe mode documentation](https://php-collective.github.io/djot-php/guide/safe-mode).

## Next Steps

- [Configuration](configuration.md) - set up converter profiles
- [Service Usage](service-usage.md) - use in PHP code
