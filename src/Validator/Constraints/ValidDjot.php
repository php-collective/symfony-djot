<?php

declare(strict_types=1);

namespace PhpCollective\SymfonyDjot\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class ValidDjot extends Constraint
{
    public string $message = 'The value is not valid Djot markup: {{ error }}';

    public bool $strict = false;

    /**
     * @param bool $strict If true, warnings are also treated as errors
     * @param string|null $message Custom error message
     * @param array<string, mixed>|null $options
     * @param array<string>|null $groups
     * @param mixed $payload
     */
    public function __construct(
        bool $strict = false,
        ?string $message = null,
        ?array $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->strict = $strict;
        $this->message = $message ?? $this->message;
    }
}
