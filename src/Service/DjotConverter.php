<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Service;

use Djot\DjotConverter as BaseDjotConverter;
use Djot\Extension\ExtensionInterface;
use Djot\Node\Document;
use Djot\Renderer\PlainTextRenderer;
use Psr\Cache\CacheItemPoolInterface;

class DjotConverter implements DjotConverterInterface
{
    private BaseDjotConverter $converter;

    private PlainTextRenderer $textRenderer;

    /**
     * @param array<ExtensionInterface> $extensions
     */
    public function __construct(
        bool $safeMode = false,
        private ?CacheItemPoolInterface $cache = null,
        array $extensions = [],
    ) {
        $this->converter = new BaseDjotConverter(
            safeMode: $safeMode,
        );
        $this->textRenderer = new PlainTextRenderer();

        foreach ($extensions as $extension) {
            $this->converter->addExtension($extension);
        }
    }

    public function toHtml(string $djot): string
    {
        if ($this->cache !== null) {
            $cacheKey = 'symfony_djot_html_' . hash('xxh3', $djot);
            $item = $this->cache->getItem($cacheKey);

            if ($item->isHit()) {
                /** @var string $cached */
                $cached = $item->get();

                return $cached;
            }

            $html = $this->converter->convert($djot);
            $item->set($html);
            $this->cache->save($item);

            return $html;
        }

        return $this->converter->convert($djot);
    }

    public function toText(string $djot): string
    {
        $document = $this->converter->parse($djot);

        return $this->textRenderer->render($document);
    }

    public function parse(string $djot): Document
    {
        return $this->converter->parse($djot);
    }

    public function getConverter(): BaseDjotConverter
    {
        return $this->converter;
    }
}
