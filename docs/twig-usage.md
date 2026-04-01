# Twig Usage

## Filters

### `djot` Filter

Converts Djot markup to HTML. Safe mode is enabled by default, protecting against XSS.

```twig
{{ article.body|djot }}
```

With a specific converter profile:

```twig
{{ content|djot('docs') }}
```

The filter is marked as safe for HTML output, so the result is not escaped.

### `djot_raw` Filter

Converts Djot markup to HTML *without* safe mode. Use only for trusted content.

```twig
{# Only use for content you fully control #}
{{ trustedArticle.body|djot_raw }}
```

This filter bypasses XSS protection - dangerous URLs (`javascript:`, `data:`) and raw HTML blocks are preserved. Never use with user-generated content.

### `djot_text` Filter

Converts Djot markup to plain text. Useful for:

- Search indexing
- Meta descriptions
- Email plain text fallbacks
- Previews/excerpts

```twig
<meta name="description" content="{{ article.body|djot_text|slice(0, 160) }}">
```

```twig
{{ article.body|djot_text('docs') }}
```

## Functions

The same functionality is available as Twig functions:

### `djot()` Function

```twig
{{ djot('*Bold* and _italic_') }}
```

With a converter profile:

```twig
{{ djot(content, 'docs') }}
```

### `djot_raw()` Function

```twig
{# Only for trusted content #}
{{ djot_raw(trustedContent) }}
```

### `djot_text()` Function

```twig
{{ djot_text('# Heading\n\nParagraph text') }}
```

## Common Patterns

### Conditional Rendering

```twig
{% if article.body %}
    <div class="content">
        {{ article.body|djot }}
    </div>
{% endif %}
```

### With Default Value

```twig
{{ (article.body ?? '')|djot }}
```

### Excerpt with Fallback

```twig
{% set excerpt = article.excerpt ?? article.body|djot_text|slice(0, 200) ~ '...' %}
<p class="excerpt">{{ excerpt }}</p>
```

### User-Generated Content

The default `|djot` filter is safe for user content:

```twig
{# Safe - XSS protection enabled by default #}
{{ comment.text|djot }}

{# Also safe - explicit converter #}
{{ comment.text|djot('default') }}
```

### Trusted CMS Content

For content from trusted sources (admin, editors):

```twig
{# Quick way - use djot_raw #}
{{ article.body|djot_raw }}

{# Or use a named converter with extensions #}
{{ article.body|djot('docs') }}
```

### Inline Content

For short inline content like titles or labels:

```twig
<h1>{{ article.title|djot }}</h1>
```

Note: This will wrap the content in `<p>` tags. If you need truly inline output, you may want to strip the wrapper:

```twig
<h1>{{ article.title|djot|replace({'<p>': '', '</p>': ''})|trim|raw }}</h1>
```

## Combining with Other Filters

```twig
{# Convert and truncate #}
{{ article.body|djot_text|slice(0, 100) }}...

{# Convert and strip specific tags #}
{{ article.body|djot|striptags('<p><strong><em><a>') }}
```

## Next Steps

- [Service Usage](service-usage.md) - use the converter in PHP code
- [Safe Mode](safe-mode.md) - understand XSS protection
