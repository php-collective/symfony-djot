# Djot Syntax Quick Reference

Djot is a light markup language similar to Markdown but with cleaner, more consistent syntax. This is a quick reference — for the complete specification, see [djot.net](https://djot.net).

## Key Differences from Markdown

| Feature | Markdown | Djot |
|---------|----------|------|
| Emphasis (italic) | `*text*` or `_text_` | `_text_` |
| Strong (bold) | `**text**` or `__text__` | `*text*` |
| Code fence | ` ```lang ` | ` ``` lang ` (space required) |

## Inline Formatting

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

## Links and Images

```djot
[Link text](https://example.com)
[Link with title](https://example.com "Title")

![Alt text](image.png)
![Alt text](image.png "Caption")
```

## Headings

```djot
# Heading 1
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6
```

## Lists

### Unordered

```djot
- Item 1
- Item 2
  - Nested item
  - Another nested
- Item 3
```

### Ordered

```djot
1. First
2. Second
3. Third
```

### Task Lists

```djot
- [ ] Unchecked
- [x] Checked
- [ ] Another task
```

### Definition Lists

```djot
Term 1
: Definition for term 1

Term 2
: Definition for term 2
: Another definition
```

## Code Blocks

````djot
``` php
$converter = new DjotConverter();
echo $converter->convert('Hello *world*!');
```
````

Note the space between ``` and the language name.

## Blockquotes

```djot
> This is a quote.
> It can span multiple lines.
>
> And have multiple paragraphs.
```

## Tables

```djot
| Header 1 | Header 2 | Header 3 |
|----------|:--------:|---------:|
| Left     | Center   | Right    |
| Cell     | Cell     | Cell     |
```

## Horizontal Rule

```djot
---
```

or

```djot
***
```

## Footnotes

```djot
Here is a statement[^1] that needs a citation.

[^1]: This is the footnote content.
```

## Math

Inline math:
```djot
The equation $E = mc^2$ is famous.
```

Display math:
```djot
$$
\int_0^\infty e^{-x^2} dx = \frac{\sqrt{\pi}}{2}
$$
```

## Raw HTML

```djot
`<span class="custom">text</span>`{=html}
```

**Note:** Raw HTML is stripped in safe mode.

## Attributes

Add classes, IDs, or attributes to elements:

```djot
{.warning}
This paragraph has a warning class.

# Heading {#custom-id}

[Link]{target=_blank}(https://example.com)
```

## Spans and Divs

Generic containers:

```djot
[This is a span]{.highlight}

::: note
This is a div with class "note".
It can contain multiple paragraphs.
:::
```

## Smart Typography

Djot automatically converts:

- `"quotes"` → "curly quotes"
- `'apostrophes'` → 'curly apostrophes'
- `--` → en-dash –
- `---` → em-dash —
- `...` → ellipsis …

## Escaping

Use backslash to escape special characters:

```djot
\*not bold\*
\_not italic\_
\`not code\`
```

## More Information

- [Official Djot website](https://djot.net)
- [Djot syntax reference](https://htmlpreview.github.io/?https://github.com/jgm/djot/blob/master/doc/syntax.html)
- [php-collective/djot documentation](https://php-collective.github.io/djot-php/)
