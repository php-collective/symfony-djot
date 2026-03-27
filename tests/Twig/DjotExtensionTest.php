<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\Twig;

use InvalidArgumentException;
use PhpCollective\SymfonyDjot\Service\DjotConverter;
use PhpCollective\SymfonyDjot\Twig\DjotExtension;
use PHPUnit\Framework\TestCase;

class DjotExtensionTest extends TestCase
{
    private DjotExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new DjotExtension([
            'default' => new DjotConverter(),
            'safe' => new DjotConverter(safeMode: true),
        ]);
    }

    public function testGetFilters(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(2, $filters);
        $this->assertSame('djot', $filters[0]->getName());
        $this->assertSame('djot_text', $filters[1]->getName());
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertSame('djot', $functions[0]->getName());
        $this->assertSame('djot_text', $functions[1]->getName());
    }

    public function testToHtml(): void
    {
        $html = $this->extension->toHtml('Hello *world*!');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToHtmlWithConverter(): void
    {
        $html = $this->extension->toHtml('Hello *world*!', 'safe');

        $this->assertStringContainsString('<strong>world</strong>', $html);
    }

    public function testToText(): void
    {
        $text = $this->extension->toText('Hello *world*!');

        $this->assertStringContainsString('Hello world!', $text);
    }

    public function testInvalidConverter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Djot converter "invalid" not found');

        $this->extension->toHtml('test', 'invalid');
    }
}
