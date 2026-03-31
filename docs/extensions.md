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

### admonition

Creates styled admonition blocks (callouts) for notes, warnings, tips, etc.

```yaml
extensions:
    - type: admonition
      types: ['note', 'tip', 'warning', 'danger', 'info', 'success']  # Types to recognize
      default_title: true            # Auto-generate title from type
      title_tag: p                   # HTML tag for title
      title_class: admonition-title  # CSS class for title
      container_class: admonition    # CSS class for container
      icons: true                    # Enable default icons (or custom array)
      icon_class: admonition-icon    # CSS class for icon wrapper
```

Usage in Djot:
```
::: note
This is a note.
:::

::: warning Custom Title
This is a warning with a custom title.
:::
```

### autolink

Automatically converts bare URLs and email addresses to clickable links.

```yaml
extensions:
    - type: autolink
      allowed_schemes: ['https', 'http', 'mailto']  # Optional
```

### code_group

Transforms code-group divs into tabbed code block interfaces. Labels are extracted from the language hint using `[Label]` suffix syntax.

```yaml
extensions:
    - type: code_group
      wrapper_class: code-group      # CSS class for container
      panel_class: code-group-panel  # CSS class for panels
      label_class: code-group-label  # CSS class for tab labels
      radio_class: code-group-radio  # CSS class for radio inputs
      id_prefix: codegroup           # Prefix for generated IDs
```

Usage in Djot:

~~~
::: code-group

``` php [PHP]
echo "Hello";
```

``` js [JavaScript]
console.log("Hello");
```

:::
~~~

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

### heading_level_shift

Shifts all heading levels by a specified amount. Useful when embedding content.

```yaml
extensions:
    - type: heading_level_shift
      shift: 1    # Number of levels to shift (1-5)
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

### heading_reference

Creates links to headings using `[text](#heading-id)` syntax.

```yaml
extensions:
    - type: heading_reference
      css_class: heading-ref    # CSS class for reference links
```

### inline_footnotes

Converts spans with a specific class to inline footnotes.

```yaml
extensions:
    - type: inline_footnotes
      css_class: fn    # CSS class that marks inline footnotes
```

Usage in Djot:
```
Some text[^This is an inline footnote.]{.fn}
```

### mentions

Converts @username references to profile links.

```yaml
extensions:
    - type: mentions
      user_url_template: 'https://github.com/{username}'
      user_class: mention
```

### mermaid

Renders code blocks with `mermaid` language as Mermaid diagrams.

```yaml
extensions:
    - type: mermaid
      tag: pre                    # HTML tag (pre or div)
      css_class: mermaid          # CSS class for Mermaid.js detection
      wrap_in_figure: false       # Wrap in figure element
      figure_class: mermaid-figure  # CSS class for figure
```

Usage in Djot:
~~~
``` mermaid
graph TD
    A[Start] --> B[End]
```
~~~

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

### tabs

Creates tabbed content blocks. Supports CSS-only or ARIA modes.

```yaml
extensions:
    - type: tabs
      mode: css               # Output mode: 'css' or 'aria'
      wrapper_class: tabs     # CSS class for container
      tab_class: tabs-panel   # CSS class for panels
      label_class: tabs-label # CSS class for labels
      radio_class: tabs-radio # CSS class for radio inputs (CSS mode)
      id_prefix: tabset       # Prefix for generated IDs
```

Usage in Djot:
```
::: tabs

::: tab First
Content for the first tab.
:::

::: tab Second
Content for the second tab.
:::

:::
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
