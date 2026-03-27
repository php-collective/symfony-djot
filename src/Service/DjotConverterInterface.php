<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Service;

interface DjotConverterInterface
{
    /**
     * Convert Djot markup to HTML.
     */
    public function toHtml(string $djot): string;

    /**
     * Convert Djot markup to plain text.
     */
    public function toText(string $djot): string;
}
