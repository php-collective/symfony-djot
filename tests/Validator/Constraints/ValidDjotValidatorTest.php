<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Tests\Validator\Constraints;

use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjot;
use PhpCollective\SymfonyDjot\Validator\Constraints\ValidDjotValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ValidDjotValidatorTest extends TestCase
{
    private ValidDjotValidator $validator;

    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private ExecutionContextInterface $context;

    /**
     * @var \Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        $this->validator = new ValidDjotValidator();

        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->violationBuilder->method('setParameter')->willReturnSelf();

        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->context->method('buildViolation')->willReturn($this->violationBuilder);

        $this->validator->initialize($this->context);
    }

    public function testValidDjot(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new ValidDjot();
        $this->validator->validate('Hello *world*!', $constraint);
    }

    public function testNullValue(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new ValidDjot();
        $this->validator->validate(null, $constraint);
    }

    public function testEmptyValue(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new ValidDjot();
        $this->validator->validate('', $constraint);
    }

    public function testComplexValidDjot(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $djot = <<<DJOT
# Heading

A paragraph with *strong* and _emphasis_.

- List item 1
- List item 2

> A blockquote
DJOT;

        $constraint = new ValidDjot();
        $this->validator->validate($djot, $constraint);
    }
}
