<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Test fixture for type-safe value object validation
 */
final class Email implements ValueObject
{
    use IsValueObject;

    public function __construct(private readonly string $value)
    {
        // Auto-check will happen via make()
    }

    protected function invariantValidEmailFormat(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
