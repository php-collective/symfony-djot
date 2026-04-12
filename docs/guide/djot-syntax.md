# Djot Syntax Quick Reference

Djot is a light markup language similar to Markdown but with cleaner, more consistent syntax.

For comprehensive syntax documentation, see:

- [Official Djot specification](https://htmlpreview.github.io/?https://github.com/jgm/djot/blob/master/doc/syntax.html)
- [php-collective/djot documentation](https://php-collective.github.io/djot-php/guide/syntax) — includes PHP-specific extensions

## Key Differences from Markdown

If you're coming from Markdown, these are the main syntax changes:

| Feature | Markdown | Djot |
|---------|----------|------|
| Emphasis (italic) | `*text*` or `_text_` | `_text_` |
| Strong (bold) | `**text**` or `__text__` | `*text*` |
| Code fence | ` ```lang ` | ` ``` lang ` (space required) |

## Quick Examples

### Inline Formatting

```djot
_emphasis_ (italic)
*strong* (bold)
_*strong emphasis*_ (bold italic)
`inline code`
~subscript~
^superscript^
{+insert+}
{-delete-}
{=highlight=}
```

### Links and Images

```djot
[Link text](https://example.com)
![Alt text](image.png)
```

### Lists

```djot
- Unordered item
- [ ] Task (unchecked)
- [x] Task (checked)

1. Ordered item
```

### Code Blocks

Note the space between ``` and the language name:

````djot
``` php
$code = 'example';
```
````

### Attributes

```djot
{.warning}
This paragraph has a warning class.

# Heading {#custom-id}
```

### Divs

```djot
::: note
This is a div with class "note".
:::
```

## PHP-Specific Extensions

The [php-collective/djot](https://github.com/php-collective/djot) library includes several extensions beyond standard Djot:

- Fenced comments (`%%%`)
- Abbreviations (`*[ABBR]: definition`)
- Table rowspan/colspan
- Boolean attributes

See the [full syntax documentation](https://php-collective.github.io/djot-php/guide/syntax) for details.
