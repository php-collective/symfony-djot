<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\Service;

use PhpCollective\SymfonyDjot\Service\DjotConverter;
use PHPUnit\Framework\TestCase;

class DjotConverterTest extends TestCase
{
    public function testToHtml(): void
    {
        $converter = new DjotConverter();

        $html = $converter->toHtml('Hello *world*!');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToHtmlWithEmphasis(): void
    {
        $converter = new DjotConverter();

        $html = $converter->toHtml('Hello _world_!');

        $this->assertStringContainsString('<em>world</em>', $html);
    }

    public function testToText(): void
    {
        $converter = new DjotConverter();

        $text = $converter->toText('Hello *world*!');

        $this->assertStringContainsString('Hello world!', $text);
    }

    public function testSafeMode(): void
    {
        $converter = new DjotConverter(safeMode: true);

        $html = $converter->toHtml('[Click me](javascript:alert("xss"))');

        $this->assertStringNotContainsString('javascript:', $html);
    }
}
