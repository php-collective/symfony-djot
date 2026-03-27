# Safe Mode

Safe mode provides XSS protection when rendering untrusted content such as user comments, forum posts, or any input from external sources.

## When to Use Safe Mode

| Content Source | Safe Mode |
|---------------|-----------|
| Admin/editor content | Off |
| User comments | **On** |
| Forum posts | **On** |
| External API data | **On** |
| CMS content (trusted editors) | Off |
| User profile descriptions | **On** |
| Email from users | **On** |

**Rule of thumb:** If you don't fully control the content, enable safe mode.

## Configuration

### Global Profile

```yaml
# config/packages/symfony_djot.yaml
symfony_djot:
    converters:
        user_content:
            safe_mode: true
```

### In Twig

```twig
{# SAFE: Uses safe mode profile #}
{{ comment.text|djot('user_content') }}

{# UNSAFE: Don't do this with user content! #}
{{ comment.text|djot }}
```

### In Services

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CommentService
{
    public function __construct(
        #[Autowire(service: 'symfony_djot.converter.user_content')]
        private DjotConverterInterface $djot,
    ) {}
}
```

## What Safe Mode Does

When enabled, safe mode:

1. **Sanitizes URLs** — blocks `javascript:`, `data:`, and other dangerous protocols
2. **Removes raw HTML** — strips any embedded HTML/scripts
3. **Validates links** — ensures URLs are safe

### Example: Dangerous Link

Input:
```djot
[Click me](javascript:alert('XSS'))
```

Without safe mode:
```html
<p><a href="javascript:alert('XSS')">Click me</a></p>
```

With safe mode:
```html
<p><a href="">Click me</a></p>
```

### Example: Raw HTML

Input:
```djot
`<script>alert('XSS')</script>`{=html}
```

Without safe mode: Script is rendered.

With safe mode: Raw HTML blocks are stripped.

## Multiple Profiles Pattern

A common pattern is to have both safe and unsafe profiles:

```yaml
symfony_djot:
    converters:
        # For trusted content (admin, editors)
        default:
            safe_mode: false

        # For user-generated content
        user_content:
            safe_mode: true

        # For email content (extra careful)
        email:
            safe_mode: true
```

```php
class ContentRenderer
{
    public function __construct(
        private DjotConverterInterface $default,

        #[Autowire(service: 'symfony_djot.converter.user_content')]
        private DjotConverterInterface $userContent,
    ) {}

    public function renderArticle(Article $article): string
    {
        // Trusted content from editors
        return $this->default->toHtml($article->getBody());
    }

    public function renderComment(Comment $comment): string
    {
        // User-generated, needs protection
        return $this->userContent->toHtml($comment->getText());
    }
}
```

## Security Recommendations

1. **Default to safe** — when in doubt, use safe mode
2. **Separate profiles** — don't mix trusted and untrusted content
3. **Validate before storing** — safe mode helps, but validate input too
4. **Review trusted content** — even "trusted" content should be reviewed

## More Information

For advanced safe mode options (custom blocked schemes, strict mode), see the [php-collective/djot safe mode documentation](https://php-collective.github.io/djot-php/guide/safe-mode).

## Next Steps

- [Configuration](configuration.md) — set up profiles
- [Service Usage](service-usage.md) — use in PHP code
