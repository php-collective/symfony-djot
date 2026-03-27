# Twig Usage

## Filters

### `djot` Filter

Converts Djot markup to HTML.

```twig
{{ article.body|djot }}
```

With a specific converter profile:

```twig
{{ comment.text|djot('user_content') }}
```

The filter is marked as safe for HTML output, so the result is not escaped.

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
{{ article.body|djot_text('user_content') }}
```

## Functions

The same functionality is available as Twig functions:

### `djot()` Function

```twig
{{ djot('*Bold* and _italic_') }}
```

With a converter profile:

```twig
{{ djot(userInput, 'user_content') }}
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

### Safe User Content

Always use a safe profile for user-generated content:

```twig
{# SAFE: Uses safe mode #}
{{ comment.text|djot('user_content') }}

{# UNSAFE: Never do this with user input! #}
{{ comment.text|djot }}
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

- [Service Usage](service-usage.md) — use the converter in PHP code
- [Safe Mode](safe-mode.md) — understand XSS protection
