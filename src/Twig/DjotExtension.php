<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Twig;

use InvalidArgumentException;
use PhpCollective\SymfonyDjot\Service\DjotConverter;
use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DjotExtension extends AbstractExtension
{
    private ?DjotConverterInterface $rawConverter = null;

    /**
     * @param array<string, \PhpCollective\SymfonyDjot\Service\DjotConverterInterface> $converters
     */
    public function __construct(private array $converters)
    {
    }

    /**
     * @return array<\Twig\TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('djot', $this->toHtml(...), ['is_safe' => ['html']]),
            new TwigFilter('djot_raw', $this->toHtmlRaw(...), ['is_safe' => ['html']]),
            new TwigFilter('djot_text', $this->toText(...)),
        ];
    }

    /**
     * @return array<\Twig\TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('djot', $this->toHtml(...), ['is_safe' => ['html']]),
            new TwigFunction('djot_raw', $this->toHtmlRaw(...), ['is_safe' => ['html']]),
            new TwigFunction('djot_text', $this->toText(...)),
        ];
    }

    public function toHtml(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toHtml($djot);
    }

    /**
     * Convert Djot to HTML without safe mode (for trusted content only).
     */
    public function toHtmlRaw(string $djot): string
    {
        return $this->getRawConverter()->toHtml($djot);
    }

    public function toText(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toText($djot);
    }

    private function getConverter(string $name): DjotConverterInterface
    {
        if (!isset($this->converters[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Djot converter "%s" not found. Available converters: %s',
                $name,
                implode(', ', array_keys($this->converters)),
            ));
        }

        return $this->converters[$name];
    }

    private function getRawConverter(): DjotConverterInterface
    {
        if ($this->rawConverter === null) {
            $this->rawConverter = new DjotConverter(safeMode: false);
        }

        return $this->rawConverter;
    }
}
