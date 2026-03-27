<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\Form\Type;

use PhpCollective\SymfonyDjot\Form\Type\DjotType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DjotTypeTest extends TestCase
{
    public function testGetParent(): void
    {
        $type = new DjotType();

        $this->assertSame(TextareaType::class, $type->getParent());
    }

    public function testGetBlockPrefix(): void
    {
        $type = new DjotType();

        $this->assertSame('djot', $type->getBlockPrefix());
    }
}
