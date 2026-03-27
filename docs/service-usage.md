# Service Usage

## Basic Injection

Inject the converter using the interface:

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;

class ArticleController extends AbstractController
{
    public function __construct(
        private DjotConverterInterface $djot,
    ) {}

    public function show(Article $article): Response
    {
        $html = $this->djot->toHtml($article->getBody());
        $plainText = $this->djot->toText($article->getBody());

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'bodyHtml' => $html,
            'bodyText' => $plainText,
        ]);
    }
}
```

## Available Methods

### `toHtml(string $djot): string`

Converts Djot markup to HTML.

```php
$html = $this->djot->toHtml('*Hello* _world_!');
// <p><strong>Hello</strong> <em>world</em>!</p>
```

### `toText(string $djot): string`

Converts Djot markup to plain text.

```php
$text = $this->djot->toText('*Hello* _world_!');
// Hello world!
```

### `parse(string $djot): Document`

Parses Djot markup into an AST (Abstract Syntax Tree). Useful for advanced manipulation.

```php
use Djot\Node\Document;

$document = $this->djot->parse('# Heading');
// Returns a Document node you can traverse/modify
```

### `getConverter(): DjotConverter`

Access the underlying Djot converter for advanced configuration.

```php
$baseConverter = $this->djot->getConverter();
// Configure extensions, event listeners, etc.
```

## Using Specific Profiles

Inject a specific converter profile using the `#[Autowire]` attribute:

```php
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CommentService
{
    public function __construct(
        #[Autowire(service: 'symfony_djot.converter.user_content')]
        private DjotConverterInterface $safeConverter,

        #[Autowire(service: 'symfony_djot.converter.default')]
        private DjotConverterInterface $defaultConverter,
    ) {}

    public function renderUserComment(string $text): string
    {
        // Safe mode enabled
        return $this->safeConverter->toHtml($text);
    }

    public function renderAdminContent(string $text): string
    {
        // Full features
        return $this->defaultConverter->toHtml($text);
    }
}
```

## Use Cases

### Email Service

```php
class EmailService
{
    public function __construct(
        private DjotConverterInterface $djot,
        private MailerInterface $mailer,
    ) {}

    public function sendNewsletter(string $djotContent, array $recipients): void
    {
        $html = $this->djot->toHtml($djotContent);
        $text = $this->djot->toText($djotContent);

        $email = (new Email())
            ->html($html)
            ->text($text);

        foreach ($recipients as $recipient) {
            $this->mailer->send($email->to($recipient));
        }
    }
}
```

### Search Indexing

```php
class SearchIndexer
{
    public function __construct(
        private DjotConverterInterface $djot,
    ) {}

    public function indexArticle(Article $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $this->djot->toText($article->getBody()),
            'html' => $this->djot->toHtml($article->getBody()),
        ];
    }
}
```

### API Response

```php
#[Route('/api/articles/{id}', methods: ['GET'])]
public function show(Article $article): JsonResponse
{
    return $this->json([
        'id' => $article->getId(),
        'title' => $article->getTitle(),
        'body_raw' => $article->getBody(),
        'body_html' => $this->djot->toHtml($article->getBody()),
        'body_text' => $this->djot->toText($article->getBody()),
    ]);
}
```

## Next Steps

- [Configuration](configuration.md) — set up multiple profiles
- [Safe Mode](safe-mode.md) — protect against XSS
