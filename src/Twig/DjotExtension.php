<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Twig;

use PhpCollective\SymfonyDjot\Service\DjotConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DjotExtension extends AbstractExtension
{
    /**
     * @param array<string, DjotConverterInterface> $converters
     */
    public function __construct(
        private array $converters,
    ) {
    }

    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('djot', $this->toHtml(...), ['is_safe' => ['html']]),
            new TwigFilter('djot_text', $this->toText(...)),
        ];
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('djot', $this->toHtml(...), ['is_safe' => ['html']]),
            new TwigFunction('djot_text', $this->toText(...)),
        ];
    }

    public function toHtml(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toHtml($djot);
    }

    public function toText(string $djot, string $converter = 'default'): string
    {
        return $this->getConverter($converter)->toText($djot);
    }

    private function getConverter(string $name): DjotConverterInterface
    {
        if (!isset($this->converters[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Djot converter "%s" not found. Available converters: %s',
                $name,
                implode(', ', array_keys($this->converters)),
            ));
        }

        return $this->converters[$name];
    }
}
