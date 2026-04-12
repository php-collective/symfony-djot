# Validation

The bundle provides a `ValidDjot` constraint to validate that a string contains valid Djot markup.

## Basic Usage

### As an Attribute

```php
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;

class Article
{
    #[ValidDjot]
    private string $body;
}
```

### In Form Fields

```php
use PhpCollective\SymfonyDjot\Form\Type\DjotType;
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;

$builder->add('body', DjotType::class, [
    'constraints' => [
        new ValidDjot(),
    ],
]);
```

## Options

### `message`

The error message when validation fails.

```php
#[ValidDjot(message: 'Please enter valid Djot markup: {{ error }}')]
private string $body;
```

Default: `'The value is not valid Djot markup: {{ error }}'`

The `{{ error }}` placeholder contains the parse error details.

### `strict`

When enabled, parse warnings are also treated as validation errors.

```php
#[ValidDjot(strict: true)]
private string $body;
```

Default: `false`

## What Gets Validated

The constraint checks:

1. **Syntax errors** — malformed Djot that cannot be parsed
2. **Parse warnings** (strict mode only) — valid but potentially problematic markup

### Valid Input (passes validation)

```djot
# Heading

A paragraph with *strong* and _emphasis_.

- List item 1
- List item 2
```

### Note on Djot Parsing

Djot is designed to be very forgiving — most input will parse without errors. Unlike strict formats like JSON or YAML, Djot will typically produce *some* output even from malformed input.

The validation is most useful for:
- Catching encoding issues
- Detecting truncated input
- Strict mode checking for warnings

## Combining with Other Constraints

```php
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;
use Symfony\Component\Validator\Constraints as Assert;

class Article
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 50000)]
    #[ValidDjot]
    private string $body;
}
```

## Next Steps

- [Forms](forms.md) — use the DjotType form field
- [Safe Mode](safe-mode.md) — protect against XSS
