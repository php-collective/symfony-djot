<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Validator\Constraints;

use Djot\DjotConverter;
use Djot\Exception\ParseException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidDjotValidator extends ConstraintValidator
{
    private DjotConverter $converter;

    public function __construct()
    {
        $this->converter = new DjotConverter(
            warnings: true,
            strict: false,
        );
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidDjot) {
            throw new UnexpectedTypeException($constraint, ValidDjot::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $this->converter->convert($value);

            if ($constraint->strict && $this->converter->hasWarnings()) {
                $warnings = $this->converter->getWarnings();
                $firstWarning = $warnings[0] ?? null;
                $errorMessage = $firstWarning?->getMessage() ?? 'Parse warnings detected';

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ error }}', $errorMessage)
                    ->addViolation();
            }
        } catch (ParseException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $e->getMessage())
                ->addViolation();
        }
    }
}
