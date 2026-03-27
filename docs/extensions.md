# Extensions

The bundle supports all [djot-php extensions](https://github.com/php-collective/djot-php). Extensions are configured per converter profile.

## Configuration

```yaml
symfony_djot:
    converters:
        default:
            extensions:
                - type: autolink
                - type: smart_quotes
```

## Available Extensions

### autolink

Automatically converts bare URLs and email addresses to clickable links.

```yaml
extensions:
    - type: autolink
      allowed_schemes: ['https', 'http', 'mailto']  # Optional
```

### default_attributes

Adds default attributes to elements by type.

```yaml
extensions:
    - type: default_attributes
      defaults:
          image:
              loading: lazy
              decoding: async
          table:
              class: table table-striped
          link:
              class: styled-link
```

### external_links

Configures behavior for external links (adds `target="_blank"` and security attributes).

```yaml
extensions:
    - type: external_links
      internal_hosts: ['example.com', 'localhost']  # Hosts to treat as internal
      target: '_blank'                               # Target attribute
      rel: 'noopener noreferrer'                     # Rel attribute
      nofollow: false                                # Add nofollow to rel
```

### frontmatter

Parses YAML, TOML, or JSON frontmatter blocks at the start of documents.

```yaml
extensions:
    - type: frontmatter
      default_format: yaml       # Default format when not specified
      render_as_comment: true    # Render as HTML comment
```

### heading_permalinks

Adds anchor links to headings for easy linking.

```yaml
extensions:
    - type: heading_permalinks
      symbol: '#'              # Link symbol
      position: after          # Position: 'before' or 'after'
      class: heading-anchor    # CSS class for the link
      aria_label: 'Permalink'  # Aria label for accessibility
```

### mentions

Converts @username references to profile links.

```yaml
extensions:
    - type: mentions
      user_url_template: 'https://github.com/{username}'
      user_class: mention
```

### semantic_span

Converts span attributes to semantic HTML5 elements.

```yaml
extensions:
    - type: semantic_span
```

Usage in Djot:
```
[Ctrl+C]{kbd}                                    → <kbd>Ctrl+C</kbd>
[API]{dfn="Application Programming Interface"}  → <dfn title="...">API</dfn>
[HTML]{abbr="HyperText Markup Language"}         → <abbr title="...">HTML</abbr>
```

### smart_quotes

Converts straight quotes to typographic (curly) quotes.

```yaml
extensions:
    - type: smart_quotes
      locale: en    # Locale for quote styles
```

### table_of_contents

Generates a table of contents from headings. Use `{toc}` placeholder in your document.

```yaml
extensions:
    - type: table_of_contents
      min_level: 1      # Minimum heading level
      max_level: 6      # Maximum heading level
      toc_class: toc    # CSS class for the TOC container
```

### wikilinks

Supports `[[Page Name]]` wiki-style links.

```yaml
extensions:
    - type: wikilinks
      url_template: '/wiki/{page}'    # URL template ({page} is replaced with slug)
      link_class: wiki-link           # CSS class for wiki links
```

## Using Multiple Converters

Define different converter profiles for different use cases:

```yaml
symfony_djot:
    converters:
        default:
            extensions:
                - type: autolink
                - type: smart_quotes

        documentation:
            extensions:
                - type: heading_permalinks
                - type: table_of_contents

        user_content:
            safe_mode: true
            extensions:
                - type: mentions
                  user_url_template: '/users/{username}'
```

Use in Twig:
```twig
{{ article.body|djot }}
{{ docs.content|djot('documentation') }}
{{ comment.text|djot('user_content') }}
```

Or inject specific converters:
```php
public function __construct(
    #[Autowire(service: 'symfony_djot.converter.documentation')]
    private DjotConverterInterface $docsConverter,
) {}
```
