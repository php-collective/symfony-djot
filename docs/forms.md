# Form Integration

The bundle provides a `DjotType` form type for fields that accept Djot markup.

## Basic Usage

```php
use PhpCollective\SymfonyDjot\Form\Type\DjotType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body', DjotType::class);
    }
}
```

## Options

The `DjotType` extends `TextareaType` and accepts all its options, plus:

### `converter`

Specifies which converter profile to use for this field. This is passed to the template for potential preview functionality.

```php
$builder->add('body', DjotType::class, [
    'converter' => 'user_content',
]);
```

Default: `'default'`

## Template Customization

The form type uses the block prefix `djot`, so you can customize its rendering in your form themes:

```twig
{# templates/form/theme.html.twig #}

{% block djot_widget %}
    <div class="djot-editor">
        {{ block('textarea_widget') }}
        <small class="form-text text-muted">
            Supports <a href="https://djot.net" target="_blank">Djot markup</a>
        </small>
    </div>
{% endblock %}
```

Register the theme in your configuration:

```yaml
# config/packages/twig.yaml
twig:
    form_themes:
        - 'form/theme.html.twig'
```

## Combining with Validation

Use the `ValidDjot` constraint to ensure the input is valid Djot:

```php
use PhpCollective\SymfonyDjot\Form\Type\DjotType;
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('body', DjotType::class, [
            'constraints' => [
                new NotBlank(),
                new ValidDjot(),
            ],
        ]);
    }
}
```

Or on the entity:

```php
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;
use Symfony\Component\Validator\Constraints as Assert;

class Article
{
    #[Assert\NotBlank]
    #[ValidDjot]
    private string $body;
}
```

## Next Steps

- [Validation](validation.md) — validate Djot input
- [Twig Usage](twig-usage.md) — render Djot in templates
